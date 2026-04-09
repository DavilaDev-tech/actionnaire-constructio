<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Facture;
use App\Services\ActiviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // ── Formulaire de création ──
    public function create()
    {
        // On ne récupère que les factures qui ne sont pas encore totalement payées
        $factures = Facture::where('statut', '!=', 'payee')
                           ->with('vente.client')
                           ->get();

        return view('paiements.create', compact('factures'));
    }

    // ── Enregistrement d'un paiement ──
   
   public function store(Request $request)
{
    // 1. Validation stricte : la date DOIT être aujourd'hui
    $request->validate([
        'facture_id'    => 'required|exists:factures,id',
        'montant'       => 'required|numeric|min:1',
        'mode_paiement' => 'required|in:especes,mobile_money,virement,cheque',
        'date_paiement' => 'required|date|after_or_equal:today|before_or_equal:today', 
        'reference'     => 'nullable|string|max:100',
        'note'          => 'nullable|string|max:500', 
    ]);

    $facture = Facture::findOrFail($request->facture_id);

    // 2. Sécurité : On vérifie une dernière fois que le montant ne dépasse pas le reste
    if ($request->montant > $facture->resteAPayer()) {
        return back()->with('error', "Le montant saisi dépasse le reste à payer.");
    }

    try {
        DB::transaction(function () use ($request, $facture) {
            // 3. Création du paiement avec la note auto reçue du JS
            $paiement = Paiement::create([
                'facture_id'    => $request->facture_id,
                'montant'       => $request->montant,
                'mode_paiement' => $request->mode_paiement,
                'date_paiement' => $request->date_paiement,
                'reference'     => $request->reference,
                'note'          => $request->note, // Enregistre la phrase générée par le JS
                'created_by'    => auth()->id(),
            ]);

            // 4. Mise à jour automatique du statut de la facture
            $facture->refresh();
            $totalPaye = $facture->montantPaye();

            if ($totalPaye >= $facture->montant) {
                $facture->update(['statut' => 'payee']);
            } elseif ($totalPaye > 0) {
                $facture->update(['statut' => 'partiellement_payee']);
            }
        });

        return redirect()->route('paiements.index')->with('success', 'Paiement encaissé avec succès.');

    } catch (\Exception $e) {
        return back()->with('error', "Une erreur est survenue : " . $e->getMessage());
    }
}
    // ── Détail d'un paiement ──
    public function show(Paiement $paiement)
    {
        $paiement->load(['facture.vente.client', 'createdBy']);

        ActiviteService::enregistrer(
            'consultation',
            'Paiements',
            "Consultation du paiement #{$paiement->id} — Facture : {$paiement->facture->numero}",
            'Paiement',
            $paiement->id
        );

        return view('paiements.show', compact('paiement'));
    }

    // ── Suppression d'un paiement ──
    public function destroy(Paiement $paiement)
    {
        $facture    = $paiement->facture;
        $montant    = $paiement->montant;
        $paiementId = $paiement->id;

        DB::transaction(function () use ($paiement, $facture) {
            $paiement->delete();

            // Après suppression, on recalcule le nouveau statut de la facture
            $totalRestantDejaPaye = $facture->paiements()->sum('montant');

            if ($totalRestantDejaPaye <= 0) {
                $facture->update(['statut' => 'non_payee']);
            } else {
                $facture->update(['statut' => 'partiellement_payee']);
            }
        });

        // Log de suppression
        ActiviteService::enregistrer(
            'suppression',
            'Paiements',
            "Suppression du paiement #{$paiementId} de " . number_format($montant, 0, ',', ' ') . " F",
            'Paiement',
            $paiementId
        );

        return redirect()->route('paiements.index')
                         ->with('success', 'Paiement supprimé. Le statut de la facture a été réajusté.');
    }

    // ── Rapport financier ──
    public function rapport()
    {
        $totalEncaisse    = Paiement::sum('montant');
        // On calcule le total dû en additionnant le reste à payer de chaque facture non soldée
        $totalDu          = Facture::where('statut', '!=', 'payee')->get()->sum(function($f) {
            return $f->resteAPayer();
        });

        $facturesPayees   = Facture::where('statut', 'payee')->count();
        $facturesImpayees = Facture::where('statut', '!=', 'payee')->count();

        $parMode = Paiement::selectRaw('mode_paiement, SUM(montant) as total, COUNT(*) as nombre')
                           ->groupBy('mode_paiement')
                           ->get();

        $paiementsMois = Paiement::whereMonth('date_paiement', now()->month)
                                 ->whereYear('date_paiement', now()->year)
                                 ->sum('montant');

        $derniersPaiements = Paiement::with(['facture.vente.client', 'createdBy'])
                                     ->latest()
                                     ->limit(10)
                                     ->get();

        return view('paiements.rapport', compact(
            'totalEncaisse', 'totalDu', 'facturesPayees', 'facturesImpayees', 
            'parMode', 'paiementsMois', 'derniersPaiements'
        ));
    }
}