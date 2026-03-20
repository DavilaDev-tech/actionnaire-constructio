<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use App\Services\ActiviteService;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    // ── Liste des paiements ──
    public function index()
    {
        $paiements = Paiement::with(['facture.vente.client', 'createdBy'])
                             ->latest()
                             ->paginate(10);

        $totalPaiements = Paiement::count();
        $totalEncaisse  = Paiement::sum('montant');
        $parEspeces     = Paiement::where('mode_paiement', 'especes')->sum('montant');
        $parMobileMoney = Paiement::where('mode_paiement', 'mobile_money')->sum('montant');

        return view('paiements.index', compact(
            'paiements', 'totalPaiements',
            'totalEncaisse', 'parEspeces', 'parMobileMoney'
        ));
    }

    // ── Formulaire création ──
    public function create()
    {
        $factures = Facture::where('statut', 'non_payee')
                           ->with('vente.client')
                           ->get();

        return view('paiements.create', compact('factures'));
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'facture_id'    => 'required|exists:factures,id',
            'montant'       => 'required|numeric|min:1',
            'mode_paiement' => 'required|in:especes,mobile_money,virement,cheque',
            'date_paiement' => 'required|date',
            'reference'     => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:500',
        ]);

        $facture = Facture::findOrFail($request->facture_id);

        // Vérifier que le montant ne dépasse pas le restant
        $montantRestant = $facture->montant_restant;
        if ($request->montant > $montantRestant) {
            return back()
                ->withInput()
                ->with('error',
                    'Le montant saisi (' .
                    number_format($request->montant, 0, ',', ' ') .
                    ' F) dépasse le montant restant (' .
                    number_format($montantRestant, 0, ',', ' ') .
                    ' F) !');
        }

        // Créer le paiement
        $paiement = Paiement::create([
            'facture_id'    => $request->facture_id,
            'montant'       => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'date_paiement' => $request->date_paiement,
            'reference'     => $request->reference,
            'note'          => $request->note,
            'created_by'    => auth()->id(),
        ]);

        // Vérifier si la facture est totalement payée
        $facture->refresh();
        if ($facture->isPayeeCompletement()) {
            $facture->update(['statut' => 'payee']);
        }

        // Modes de paiement lisibles
        $modes = [
            'especes'      => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'virement'     => 'Virement',
            'cheque'       => 'Chèque',
        ];

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'creation',
            'Paiements',
            "Paiement de " .
            number_format($paiement->montant, 0, ',', ' ') .
            " F — Facture : {$facture->numero} — " .
            "Client : {$facture->vente->client->nom} — " .
            "Mode : " . ($modes[$paiement->mode_paiement] ?? $paiement->mode_paiement),
            'Paiement',
            $paiement->id
        );

        return redirect()->route('paiements.index')
                         ->with('success', 'Paiement enregistré avec succès !');
    }

    // ── Détail paiement ──
    public function show(Paiement $paiement)
    {
        $paiement->load(['facture.vente.client', 'createdBy']);

        // Enregistrer la consultation
        ActiviteService::enregistrer(
            'consultation',
            'Paiements',
            "Consultation du paiement #{$paiement->id} — " .
            "Facture : {$paiement->facture->numero}",
            'Paiement',
            $paiement->id
        );

        return view('paiements.show', compact('paiement'));
    }

    // ── Suppression ──
    public function destroy(Paiement $paiement)
    {
        $facture    = $paiement->facture;
        $montant    = $paiement->montant;
        $paiementId = $paiement->id;
        $factureNum = $facture->numero;

        $paiement->delete();

        if ($facture->statut === 'payee') {
            $facture->update(['statut' => 'non_payee']);
        }

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'suppression',
            'Paiements',
            "Suppression du paiement #{$paiementId} de " .
            number_format($montant, 0, ',', ' ') .
            " F — Facture : {$factureNum}",
            'Paiement',
            $paiementId
        );

        return redirect()->route('paiements.index')
                         ->with('success', 'Paiement supprimé avec succès !');
    }

    // ── Rapport financier ──
    public function rapport()
    {
        $totalEncaisse    = Paiement::sum('montant');
        $totalDu          = Facture::where('statut', 'non_payee')->sum('montant');
        $facturesPayees   = Facture::where('statut', 'payee')->count();
        $facturesImpayees = Facture::where('statut', 'non_payee')->count();

        $parMode = Paiement::selectRaw(
                       'mode_paiement, SUM(montant) as total, COUNT(*) as nombre'
                   )
                   ->groupBy('mode_paiement')
                   ->get();

        $paiementsMois = Paiement::whereMonth('date_paiement', now()->month)
                                 ->whereYear('date_paiement', now()->year)
                                 ->sum('montant');

        $derniersPaiements = Paiement::with(['facture.vente.client', 'createdBy'])
                                     ->latest()
                                     ->limit(10)
                                     ->get();

        // Enregistrer la consultation du rapport
        ActiviteService::enregistrer(
            'consultation',
            'Paiements',
            "Consultation du rapport financier"
        );

        return view('paiements.rapport', compact(
            'totalEncaisse', 'totalDu',
            'facturesPayees', 'facturesImpayees',
            'parMode', 'paiementsMois',
            'derniersPaiements'
        ));
    }
}