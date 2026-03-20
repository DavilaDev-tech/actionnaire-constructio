<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Services\ActiviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    // ── Liste des produits ──
    public function index()
    {
        $produits = Produit::with(['categorie', 'fournisseur'])
                           ->latest()
                           ->paginate(10);

        $totalProduits = Produit::count();
        $stockBas      = Produit::whereColumn('quantite_stock', '<=', 'seuil_alerte')->count();
        $stockEpuise   = Produit::where('quantite_stock', 0)->count();

        return view('produits.index', compact(
            'produits', 'totalProduits', 'stockBas', 'stockEpuise'
        ));
    }

    // ── Formulaire création ──
    public function create()
    {
        $categories   = Categorie::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('produits.create', compact('categories', 'fournisseurs'));
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:200',
            'categorie_id'   => 'required|exists:categories,id',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'description'    => 'nullable|string',
            'prix_achat'     => 'required|numeric|min:0',
            'prix_vente'     => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte'   => 'required|integer|min:0',
            'unite'          => 'required|string|max:50',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                                     ->store('produits', 'public');
        }

        $produit = Produit::create($data);

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'creation',
            'Produits',
            "Création du produit {$produit->nom} — " .
            "Prix vente : " . number_format($produit->prix_vente, 0, ',', ' ') .
            " F — Stock : {$produit->quantite_stock} {$produit->unite}",
            'Produit',
            $produit->id
        );

        return redirect()->route('produits.index')
                         ->with('success', 'Produit créé avec succès !');
    }

    // ── Détail d'un produit ──
    public function show(Produit $produit)
    {
        $produit->load(['categorie', 'fournisseur']);

        // Enregistrer la consultation
        ActiviteService::enregistrer(
            'consultation',
            'Produits',
            "Consultation du produit {$produit->nom}",
            'Produit',
            $produit->id
        );

        return view('produits.show', compact('produit'));
    }

    // ── Formulaire modification ──
    public function edit(Produit $produit)
    {
        $categories   = Categorie::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('produits.edit', compact('produit', 'categories', 'fournisseurs'));
    }

    // ── Mise à jour ──
    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom'            => 'required|string|max:200',
            'categorie_id'   => 'required|exists:categories,id',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'description'    => 'nullable|string',
            'prix_achat'     => 'required|numeric|min:0',
            'prix_vente'     => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte'   => 'required|integer|min:0',
            'unite'          => 'required|string|max:50',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Sauvegarder les données avant modification
        $avant = [
            'nom'            => $produit->nom,
            'prix_achat'     => $produit->prix_achat,
            'prix_vente'     => $produit->prix_vente,
            'quantite_stock' => $produit->quantite_stock,
            'seuil_alerte'   => $produit->seuil_alerte,
        ];

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')
                                     ->store('produits', 'public');
        }

        $produit->update($data);

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Produits',
            "Modification du produit {$produit->nom}",
            'Produit',
            $produit->id,
            $avant,
            [
                'nom'            => $produit->nom,
                'prix_achat'     => $produit->prix_achat,
                'prix_vente'     => $produit->prix_vente,
                'quantite_stock' => $produit->quantite_stock,
                'seuil_alerte'   => $produit->seuil_alerte,
            ]
        );

        return redirect()->route('produits.index')
                         ->with('success', 'Produit modifié avec succès !');
    }

    // ── Suppression ──
    public function destroy(Produit $produit)
    {
        $nomProduit = $produit->nom;
        $produitId  = $produit->id;

        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'suppression',
            'Produits',
            "Suppression du produit {$nomProduit}",
            'Produit',
            $produitId
        );

        return redirect()->route('produits.index')
                         ->with('success', 'Produit supprimé avec succès !');
    }

    // ── Ajustement stock manuel ──
    public function ajusterStock(Request $request, Produit $produit)
    {
        $request->validate([
            'quantite'  => 'required|integer',
            'operation' => 'required|in:ajouter,retirer',
        ]);

        $ancienStock = $produit->quantite_stock;

        if ($request->operation === 'ajouter') {
            $produit->increment('quantite_stock', $request->quantite);
            $operation = 'ajout';
        } else {
            if ($produit->quantite_stock < $request->quantite) {
                return back()->with('error', 'Stock insuffisant !');
            }
            $produit->decrement('quantite_stock', $request->quantite);
            $operation = 'retrait';
        }

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Produits',
            "Ajustement stock {$produit->nom} : {$operation} de {$request->quantite} " .
            "{$produit->unite} — Stock : {$ancienStock} → {$produit->quantite_stock}",
            'Produit',
            $produit->id
        );

        return back()->with('success', 'Stock ajusté avec succès !');
    }
}