<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\Facture;
use App\Models\Fournisseur;
use App\Models\Livraison;
use Illuminate\Http\Request;

class RechercheController extends Controller
{
    // ── Recherche complète (page résultats) ──
    public function index(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return redirect()->back()
                             ->with('error', 'Tapez au moins 2 caractères.');
        }

        // ── Clients ──
        $clients = Client::where('nom', 'like', "%{$query}%")
                         ->orWhere('telephone', 'like', "%{$query}%")
                         ->orWhere('email', 'like', "%{$query}%")
                         ->limit(5)->get();


     // ── Produits ──
$produits = Produit::where('nom', 'like', "%{$query}%")
                   ->with('categorie')
                   ->limit(5)->get();

        // ── Ventes ──
        $ventes = Vente::where('numero_vente', 'like', "%{$query}%")
                       ->orWhereHas('client', function($q) use ($query) {
                           $q->where('nom', 'like', "%{$query}%");
                       })
                       ->with('client')
                       ->limit(5)->get();

        // ── Factures ──
        $factures = Facture::where('numero', 'like', "%{$query}%")
                           ->with('vente.client')
                           ->limit(5)->get();

        // ── Fournisseurs ──
        $fournisseurs = Fournisseur::where('nom', 'like', "%{$query}%")
                                   ->orWhere('telephone', 'like', "%{$query}%")
                                   ->limit(5)->get();

        // ── Livraisons ──
        $livraisons = Livraison::whereHas('client', function($q) use ($query) {
                                    $q->where('nom', 'like', "%{$query}%");
                                })
                               ->orWhereHas('vente', function($q) use ($query) {
                                    $q->where('numero_vente', 'like', "%{$query}%");
                               })
                               ->with(['client', 'vente'])
                               ->limit(5)->get();

        $totalResultats = $clients->count()
                        + $produits->count()
                        + $ventes->count()
                        + $factures->count()
                        + $fournisseurs->count()
                        + $livraisons->count();

        return view('recherche.resultats', compact(
            'query', 'clients', 'produits',
            'ventes', 'factures', 'fournisseurs',
            'livraisons', 'totalResultats'
        ));
    }

    // ── Suggestions live (AJAX) ──
    public function suggestions(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $resultats = [];

        // ── Clients ──
        $clients = Client::where('nom', 'like', "%{$query}%")
                         ->limit(3)->get();
        foreach ($clients as $c) {
            $resultats[] = [
                'type'  => 'client',
                'icone' => 'bi-person',
                'label' => $c->nom,
                'info'  => $c->telephone ?? $c->email ?? 'Client',
                'url'   => route('clients.show', $c->id),
            ];
        }


// ── Produits ──
$produits = Produit::where('nom', 'like', "%{$query}%")
                   ->limit(3)->get();

        // ── Ventes ──
        $ventes = Vente::where('numero_vente', 'like', "%{$query}%")
                       ->orWhereHas('client', function($q) use ($query) {
                           $q->where('nom', 'like', "%{$query}%");
                       })
                       ->with('client')
                       ->limit(3)->get();
        foreach ($ventes as $v) {
            $resultats[] = [
                'type'  => 'vente',
                'icone' => 'bi-cart3',
                'label' => $v->numero_vente,
                'info'  => $v->client->nom
                           . ' — '
                           . number_format($v->montant_total, 0, ',', ' ')
                           . ' F',
                'url'   => route('ventes.show', $v->id),
            ];
        }

        // ── Factures ──
        $factures = Facture::where('numero', 'like', "%{$query}%")
                           ->with('vente.client')
                           ->limit(3)->get();
        foreach ($factures as $f) {
            $resultats[] = [
                'type'  => 'facture',
                'icone' => 'bi-receipt',
                'label' => $f->numero,
                'info'  => $f->vente->client->nom
                           . ' — '
                           . number_format($f->montant, 0, ',', ' ')
                           . ' F',
                'url'   => route('factures.show', $f->id),
            ];
        }

        // ── Fournisseurs ──
        $fournisseurs = Fournisseur::where('nom', 'like', "%{$query}%")
                                   ->orWhere('telephone', 'like', "%{$query}%")
                                   ->limit(2)->get();
        foreach ($fournisseurs as $f) {
            $resultats[] = [
                'type'  => 'fournisseur',
                'icone' => 'bi-truck',
                'label' => $f->nom,
                'info'  => $f->telephone ?? $f->email ?? 'Fournisseur',
                'url'   => route('fournisseurs.show', $f->id),
            ];
        }

        return response()->json($resultats);
    }
}