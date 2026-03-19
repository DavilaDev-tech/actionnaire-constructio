<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    // ── Liste ──
    public function index()
    {
        $fournisseurs      = Fournisseur::withCount('produits')
                                        ->latest()
                                        ->paginate(10);
        $totalFournisseurs = Fournisseur::count();

        return view('fournisseurs.index', compact(
            'fournisseurs', 'totalFournisseurs'
        ));
    }

    // ── Formulaire création ──
    public function create()
    {
        return view('fournisseurs.create');
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'nom'              => 'required|string|max:200',
            'telephone'        => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:fournisseurs,email',
            'adresse'          => 'nullable|string|max:500',
            'contact_personne' => 'nullable|string|max:200',
        ]);

        Fournisseur::create($request->only(
            'nom', 'telephone', 'email', 'adresse', 'contact_personne'
        ));

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur créé avec succès !');
    }

    // ── Détail ──
    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load(['produits', 'approvisionnements' => function($q) {
            $q->latest()->take(5);
        }]);

        return view('fournisseurs.show', compact('fournisseur'));
    }

    // ── Formulaire modification ──
    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    // ── Mise à jour ──
    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom'              => 'required|string|max:200',
            'telephone'        => 'nullable|string|max:20',
            'email'            => 'nullable|email|unique:fournisseurs,email,' . $fournisseur->id,
            'adresse'          => 'nullable|string|max:500',
            'contact_personne' => 'nullable|string|max:200',
        ]);

        $fournisseur->update($request->only(
            'nom', 'telephone', 'email', 'adresse', 'contact_personne'
        ));

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur modifié avec succès !');
    }

    // ── Suppression ──
    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->produits()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer ce fournisseur car il a ' .
                $fournisseur->produits()->count() . ' produit(s) associé(s) !');
        }

        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur supprimé avec succès !');
    }
}
