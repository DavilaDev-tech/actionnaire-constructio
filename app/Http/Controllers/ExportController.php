<?php

namespace App\Http\Controllers;

use App\Exports\VentesExport;
use App\Exports\ClientsExport;
use App\Exports\ProduitsExport;
use App\Exports\PaiementsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    // ── Export Ventes ──
    public function ventes(Request $request)
    {
        $dateDebut = $request->get('date_debut')
            ? Carbon::parse($request->get('date_debut'))->startOfDay()
            : null;
        $dateFin = $request->get('date_fin')
            ? Carbon::parse($request->get('date_fin'))->endOfDay()
            : null;

        $nom = 'ventes_' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(
            new VentesExport($dateDebut, $dateFin),
            $nom
        );
    }

    // ── Export Clients ──
    public function clients()
    {
        $nom = 'clients_' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new ClientsExport(), $nom);
    }

    // ── Export Produits ──
    public function produits()
    {
        $nom = 'produits_stock_' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new ProduitsExport(), $nom);
    }

    // ── Export Paiements ──
    public function paiements()
    {
        $nom = 'paiements_' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new PaiementsExport(), $nom);
    }
}