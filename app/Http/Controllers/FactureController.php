<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    // ── Liste des factures ──
    public function index()
    {
        $factures   = Facture::with(['vente.client'])
                             ->latest()
                             ->paginate(10);

        $totalFactures = Facture::count();
        $nonPayees     = Facture::where('statut', 'non_payee')->count();
        $payees        = Facture::where('statut', 'payee')->count();
        $montantDu     = Facture::where('statut', 'non_payee')->sum('montant');

        return view('factures.index', compact(
            'factures', 'totalFactures',
            'nonPayees', 'payees', 'montantDu'
        ));
    }

    // ── Détail facture ──
    public function show(Facture $facture)
    {
        $facture->load(['vente.client', 'vente.details.produit', 'vente.user']);
        return view('factures.show', compact('facture'));
    }

    // ── Télécharger PDF ──
    public function telecharger(Facture $facture)
    {
        $facture->load(['vente.client', 'vente.details.produit', 'vente.user']);

        $pdf = Pdf::loadView('factures.pdf', compact('facture'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Facture-' . $facture->numero . '.pdf');
    }

    // ── Marquer comme payée ──
    public function marquerPayee(Facture $facture)
    {
        $facture->update(['statut' => 'payee']);

        return back()->with('success', 'Facture marquée comme payée !');
    }
}
