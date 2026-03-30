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
            'produits'                 => 'required|array|min:1',
            'produits.*.produit_id'    => 'required|exists:produits,id',
            'produits.*.quantite'      => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $vente = null;

        DB::transaction(function () use ($request, &$vente) {

            // 1. Créer la vente
            $vente = Vente::create([
                'client_id'     => $request->client_id,
                'user_id'       => auth()->id(),
                'numero_vente'  => Vente::genererNumero(),
                'date_vente'    => $request->date_vente,
                'montant_total' => 0,
                'statut'        => 'en_attente',
                'note'          => $request->note,
            ]);

            $montantTotal = 0;

            // 2. Créer les lignes de vente
            foreach ($request->produits as $ligne) {
                $sousTotal = $ligne['quantite'] * $ligne['prix_unitaire'];

                VenteDetail::create([
                    'vente_id'      => $vente->id,
                    'produit_id'    => $ligne['produit_id'],
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total'    => $sousTotal,
                ]);

                // 3. Déduire le stock
                $produit = Produit::find($ligne['produit_id']);
                $produit->decrement('quantite_stock', $ligne['quantite']);

                $montantTotal += $sousTotal;
            }

            // 4. Mettre à jour le montant total
            $vente->update(['montant_total' => $montantTotal]);

            // 5. Créer la facture automatiquement
            Facture::create([
                'vente_id' => $vente->id,
                'numero'   => Facture::genererNumero(),
                'montant'  => $montantTotal,
                'statut'   => 'non_payee',
            ]);
        });

        // 6. Enregistrer l'activité
        $vente->load('client');
        ActiviteService::enregistrer(
            'creation',
            'Ventes',
            "Création de la vente {$vente->numero_vente} pour {$vente->client->nom} — " .
            number_format($vente->montant_total, 0, ',', ' ') . " F CFA",
            'Vente',
            $vente->id
        );

        return redirect()->route('ventes.index')
                         ->with('success',
                             'Vente créée avec succès ! Facture générée automatiquement.');
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