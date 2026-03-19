<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    // ── Liste des ventes ──
    public function index()
    {
        $ventes = Vente::with(['client', 'user'])
                       ->latest()
                       ->paginate(10);

        $totalVentes    = Vente::count();
        $chiffreAffaire = Vente::where('statut', '!=', 'annulee')
                               ->sum('montant_total');
        $enAttente      = Vente::where('statut', 'en_attente')->count();
        $annulees       = Vente::where('statut', 'annulee')->count();

        return view('ventes.index', compact(
            'ventes', 'totalVentes',
            'chiffreAffaire', 'enAttente', 'annulees'
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
            'client_id'              => 'required|exists:clients,id',
            'date_vente'             => 'required|date',
            'note'                   => 'nullable|string',
            'produits'               => 'required|array|min:1',
            'produits.*.produit_id'  => 'required|exists:produits,id',
            'produits.*.quantite'    => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Créer la vente
            $vente = Vente::create([
                'client_id'    => $request->client_id,
                'user_id'      => auth()->id(),
                'numero_vente' => Vente::genererNumero(),
                'date_vente'   => $request->date_vente,
                'montant_total'=> 0,
                'statut'       => 'en_attente',
                'note'         => $request->note,
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

        return redirect()->route('ventes.index')
                         ->with('success', 'Vente créée avec succès ! Facture générée automatiquement.');
    }

    // ── Détail d'une vente ──
    public function show(Vente $vente)
    {
        $vente->load(['client', 'user', 'details.produit', 'facture']);
        return view('ventes.show', compact('vente'));
    }

    // ── Changer le statut ──
    public function changerStatut(Request $request, Vente $vente)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,confirmee,livree,annulee',
        ]);

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

        return back()->with('success', 'Statut mis à jour avec succès !');
    }

    // ── Suppression ──
    public function destroy(Vente $vente)
    {
        if (!$vente->isAnnulee()) {
            return back()->with('error',
                'Vous devez d\'abord annuler la vente avant de la supprimer !');
        }

        $vente->delete();

        return redirect()->route('ventes.index')
                         ->with('success', 'Vente supprimée avec succès !');
    }
}