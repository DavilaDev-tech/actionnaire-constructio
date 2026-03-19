<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    // ── Liste des catégories ──
    public function index()
    {
        $categories = Categorie::withCount('produits')
                               ->latest()
                               ->paginate(10);
        return view('categories.index', compact('categories'));
    }

    // ── Formulaire création ──
    public function create()
    {
        return view('categories.create');
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom',
            'description' => 'nullable|string|max:500',
        ]);

        Categorie::create($request->only('nom', 'description'));

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie créée avec succès !');
    }

    // ── Formulaire modification ──
    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    // ── Mise à jour ──
    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom,' . $categorie->id,
            'description' => 'nullable|string|max:500',
        ]);

        $categorie->update($request->only('nom', 'description'));

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie modifiée avec succès !');
    }

    // ── Suppression ──
    public function destroy(Categorie $categorie)
    {
        // Vérifier si la catégorie a des produits
        if ($categorie->produits()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer cette catégorie car elle contient ' .
                $categorie->produits()->count() . ' produit(s) !');
        }

        $categorie->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Catégorie supprimée avec succès !');
    }
}