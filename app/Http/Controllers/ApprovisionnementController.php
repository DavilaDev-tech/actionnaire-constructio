<?php

namespace App\Http\Controllers;

use App\Models\Approvisionnement;
use App\Models\ApproDetail;
use App\Models\Fournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovisionnementController extends Controller
{
    // ── Liste ──
    public function index()
    {
        $appros = Approvisionnement::with(['fournisseur', 'user'])
            ->latest()
            ->paginate(10);

        $totalAppros = Approvisionnement::count();
        $totalDepenses = Approvisionnement::where('statut', '=', 'recu')
            ->sum('montant_total');
        $enAttente = Approvisionnement::where('statut', '=', 'en_attente')->count();
        $recus = Approvisionnement::where('statut', '=', 'recu')->count();

        return view('approvisionnements.index', compact(
            'appros',
            'totalAppros',
            'totalDepenses',
            'enAttente',
            'recus'
        ));
    }

    // ── Formulaire création ──
    public function create()
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        $produits = Produit::with('categorie')->orderBy('nom')->get();
        $numero = Approvisionnement::genererNumero();

        return view('approvisionnements.create', compact(
            'fournisseurs',
            'produits',
            'numero'
        ));
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_appro' => 'required|date',
            'note' => 'nullable|string',
            'produits' => 'required|array|min:1',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Créer l'approvisionnement
            $appro = Approvisionnement::create([
                'fournisseur_id' => $request->fournisseur_id,
                'user_id' => auth()->id(),
                'numero' => Approvisionnement::genererNumero(),
                'date_appro' => $request->date_appro,
                'montant_total' => 0,
                'statut' => 'en_attente',
                'note' => $request->note,
            ]);

            $montantTotal = 0;

            // 2. Créer les lignes
            foreach ($request->produits as $ligne) {
                $sousTotal = $ligne['quantite'] * $ligne['prix_unitaire'];

                ApproDetail::create([
                    'approvisionnement_id' => $appro->id,
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total' => $sousTotal,
                ]);

                $montantTotal += $sousTotal;
            }

            // 3. Mettre à jour le montant total
            $appro->update(['montant_total' => $montantTotal]);

            // NB: Le stock sera mis à jour uniquement
            // quand le statut passe à "recu"
        });

        return redirect()->route('approvisionnements.index')
            ->with('success', 'Approvisionnement créé avec succès !');
    }

    // ── Détail ──
    public function show(Approvisionnement $approvisionnement)
    {
        $approvisionnement->load([
            'fournisseur',
            'user',
            'details.produit'
        ]);
        return view(
            'approvisionnements.show',
            compact('approvisionnement')
        );
    }

    // ── Changer statut ──
    public function changerStatut(Request $request, Approvisionnement $approvisionnement)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,recu,annule',
        ]);

        // Si on marque comme reçu → on ajoute au stock
        if ($request->statut === 'recu' && !$approvisionnement->isRecu()) {
            foreach ($approvisionnement->details as $detail) {
                $detail->produit->increment(
                    'quantite_stock',
                    $detail->quantite
                );
            }
        }

        // Si on annule un appro déjà reçu → on retire du stock
        if ($request->statut === 'annule' && $approvisionnement->isRecu()) {
            foreach ($approvisionnement->details as $detail) {
                $detail->produit->decrement(
                    'quantite_stock',
                    $detail->quantite
                );
            }
        }

        $approvisionnement->update(['statut' => $request->statut]);

        return back()->with('success', 'Statut mis à jour avec succès !');
    }

    // ── Suppression ──
    public function destroy(Approvisionnement $approvisionnement)
    {
        if ($approvisionnement->isRecu()) {
            return back()->with(
                'error',
                'Impossible de supprimer un approvisionnement déjà reçu !'
            );
        }

        $approvisionnement->delete();

        return redirect()->route('approvisionnements.index')
            ->with('success', 'Approvisionnement supprimé !');
    }
}
