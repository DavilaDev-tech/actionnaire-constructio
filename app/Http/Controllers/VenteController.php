<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Produit;
use App\Services\ActiviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    // ── Liste des ventes ──
   public function index(Request $request)
{
    $search = $request->get('search', '');

    $query = Vente::with(['client', 'user'])->latest();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('numero_vente', 'like', "%{$search}%")
              ->orWhere('statut', 'like', "%{$search}%")
              ->orWhereHas('client', function($q) use ($search) {
                  $q->where('nom', 'like', "%{$search}%");
              });
        });
    }

    $ventes = $query->paginate(10)->withQueryString();

    $totalVentes    = Vente::count();
    $chiffreAffaire = Vente::where('statut', '!=', 'annulee')->sum('montant_total');
    $enAttente      = Vente::where('statut', 'en_attente')->count();
    $annulees       = Vente::where('statut', 'annulee')->count();

    if ($request->ajax()) {
        return response()->json([
            'html' => view('ventes.partials.tableau',
                compact('ventes'))->render(),
            'pagination' => view('partials.pagination',
                compact('ventes'))->render(),
            'total' => $ventes->total(),
        ]);
    }

    return view('ventes.index', compact(
        'ventes', 'totalVentes',
        'chiffreAffaire', 'enAttente', 'annulees',
        'search'
    ));
}
    // ── Formulaire création ──
    public function create()
    {
        $clients  = Client::orderBy('nom')->get();
        $produits = Produit::where('quantite_stock', '>', 0)
                           ->orderBy('nom')
                           ->get();
        $numero   = Vente::genererNumero();

        return view('ventes.create', compact('clients', 'produits', 'numero'));
    }

    // ── Enregistrement ──
 public function store(Request $request)
{
    $request->validate([
        'client_id'                => 'required|exists:clients,id',
        'date_vente'               => 'required|date',
        'note'                     => 'nullable|string',
        'tva_applicable'           => 'nullable|boolean',
        'produits'                 => 'required|array|min:1',
        'produits.*.produit_id'    => 'required|exists:produits,id',
        'produits.*.quantite'      => 'required|integer|min:1',
        'produits.*.prix_unitaire' => 'required|numeric|min:0',
    ]);

    try {
        $vente = DB::transaction(function () use ($request) {

            // ── ÉTAPE 1 : Vérification globale du stock ──
            foreach ($request->produits as $ligne) {
                $produit = Produit::findOrFail($ligne['produit_id']);

                if ($produit->quantite_stock < $ligne['quantite']) {
                    throw new \Exception(
                        "Stock insuffisant pour \"{$produit->nom}\" ! " .
                        "Stock disponible : {$produit->quantite_stock} {$produit->unite}. " .
                        "Quantité demandée : {$ligne['quantite']} {$produit->unite}."
                    );
                }
            }

            // ── ÉTAPE 2 : TVA ──
            $client        = Client::findOrFail($request->client_id);
            $tvaApplicable = !$client->exonere_tva
                             && $request->boolean('tva_applicable');

            // ── ÉTAPE 3 : Création de la vente ──
            $vente = Vente::create([
                'client_id'      => $request->client_id,
                'user_id'        => auth()->id(),
                'numero_vente'   => Vente::genererNumero(),
                'date_vente'     => $request->date_vente ?? now(),
                'montant_total'  => 0,
                'montant_ht'     => 0,
                'montant_tva'    => 0,
                'tva_applicable' => $tvaApplicable,
                'taux_tva'       => Vente::TAUX_TVA,
                'statut'         => 'en_attente',
                'note'           => $request->note,
            ]);

            $montantHT = 0;

            // ── ÉTAPE 4 : Lignes de vente + déduction stock ──
            foreach ($request->produits as $ligne) {
                $produit   = Produit::find($ligne['produit_id']);
                $vraiPrix  = $produit->prix_vente;
                $sousTotal = $ligne['quantite'] * $vraiPrix;

                VenteDetail::create([
                    'vente_id'      => $vente->id,
                    'produit_id'    => $produit->id,
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $vraiPrix,
                    'sous_total'    => $sousTotal,
                ]);

                // Déduction réelle du stock
                $produit->decrement('quantite_stock', $ligne['quantite']);

                $montantHT += $sousTotal;
            }

            // ── ÉTAPE 5 : Calcul TVA ──
            $montantTVA = $tvaApplicable
                ? round($montantHT * (Vente::TAUX_TVA / 100), 2)
                : 0;
            $montantTTC = $montantHT + $montantTVA;

            // ── ÉTAPE 6 : Mise à jour montants vente ──
            $vente->update([
                'montant_ht'    => $montantHT,
                'montant_tva'   => $montantTVA,
                'montant_total' => $montantTTC,
            ]);

            // ── ÉTAPE 7 : Création facture ──
            Facture::create([
                'vente_id'       => $vente->id,
                'numero'         => Facture::genererNumero(),
                'montant'        => $montantTTC,
                'montant_ht'     => $montantHT,
                'montant_tva'    => $montantTVA,
                'tva_applicable' => $tvaApplicable,
                'statut'         => 'non_payee',
            ]);

            return $vente;
        });

        // ── ÉTAPE 8 : Log activité + redirection ──
        $vente->load('client');
        ActiviteService::enregistrer(
            'creation',
            'Ventes',
            "Vente {$vente->numero_vente} pour {$vente->client->nom} — " .
            ($vente->tva_applicable ? "TVA incluse — " : "Sans TVA — ") .
            "TTC : " . number_format($vente->montant_total, 0, ',', ' ') . " F",
            'Vente',
            $vente->id
        );

        return redirect()->route('ventes.index')
                         ->with('success', 'Vente créée avec succès ! Facture générée.');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
    // ── Détail d'une vente ──
    public function show(Vente $vente)
    {
        $vente->load(['client', 'user', 'details.produit', 'facture']);

        // Enregistrer la consultation
        ActiviteService::enregistrer(
            'consultation',
            'Ventes',
            "Consultation de la vente {$vente->numero_vente}",
            'Vente',
            $vente->id
        );

        return view('ventes.show', compact('vente'));
    }

    // ── Changer le statut ──
    public function changerStatut(Request $request, Vente $vente)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,confirmee,livree,annulee',
        ]);

        $ancienStatut = $vente->statut;

        // Si on annule, on remet le stock
        if ($request->statut === 'annulee' && !$vente->isAnnulee()) {
            foreach ($vente->details as $detail) {
                $detail->produit->increment('quantite_stock', $detail->quantite);
            }
            // Annuler la facture aussi
            if ($vente->facture) {
                $vente->facture->update(['statut' => 'annulee']);
            }
        }

        $vente->update(['statut' => $request->statut]);

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Ventes',
            "Changement statut vente {$vente->numero_vente} : " .
            ucfirst($ancienStatut) . " → " . ucfirst($request->statut),
            'Vente',
            $vente->id
        );

        return back()->with('success', 'Statut mis à jour avec succès !');
    }

    // ── Suppression ──
    public function destroy(Vente $vente)
    {
        if (!$vente->isAnnulee()) {
            return back()->with('error',
                'Vous devez d\'abord annuler la vente avant de la supprimer !');
        }

        $numeroVente = $vente->numero_vente;
        $clientNom   = $vente->client->nom;

        $vente->delete();

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'suppression',
            'Ventes',
            "Suppression de la vente {$numeroVente} du client {$clientNom}",
            'Vente',
            null
        );

        return redirect()->route('ventes.index')
                         ->with('success', 'Vente supprimée avec succès !');
    }
}