<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Livraison;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Période sélectionnée ──
        $periode = $request->get('periode', 'mois');
        $dateDebut = null;
        $dateFin   = Carbon::now()->endOfDay();

        switch ($periode) {
            case 'aujourd_hui':
                $dateDebut = Carbon::today();
                $dateFin   = Carbon::now()->endOfDay();
                break;
            case 'semaine':
                $dateDebut = Carbon::now()->startOfWeek();
                $dateFin   = Carbon::now()->endOfWeek();
                break;
            case 'mois':
                $dateDebut = Carbon::now()->startOfMonth();
                $dateFin   = Carbon::now()->endOfMonth();
                break;
            case 'annee':
                $dateDebut = Carbon::now()->startOfYear();
                $dateFin   = Carbon::now()->endOfYear();
                break;
            case 'personnalise':
                $dateDebut = $request->get('date_debut')
                    ? Carbon::parse($request->get('date_debut'))->startOfDay()
                    : Carbon::now()->startOfMonth();
                $dateFin = $request->get('date_fin')
                    ? Carbon::parse($request->get('date_fin'))->endOfDay()
                    : Carbon::now()->endOfDay();
                break;
            default:
                $dateDebut = Carbon::now()->startOfMonth();
        }

        // ── Libellé période ──
        $libellePeriode = match($periode) {
            'aujourd_hui'  => 'Aujourd\'hui',
            'semaine'      => 'Cette semaine',
            'mois'         => 'Ce mois',
            'annee'        => 'Cette année',
            'personnalise' => 'Du ' . $dateDebut->format('d/m/Y') .
                              ' au ' . $dateFin->format('d/m/Y'),
            default        => 'Ce mois',
        };

        // ── Query de base filtrée par période ──
        $ventesQuery = Vente::whereBetween('date_vente', [$dateDebut, $dateFin])
                            ->where('statut', '!=', 'annulee');

        // ── Statistiques filtrées ──
        $stats = [
            'total_ventes'       => (clone $ventesQuery)->count(),
            'chiffre_affaires'   => (clone $ventesQuery)->sum('montant_total'),
            'factures_impayees'  => Facture::where('statut', 'non_payee')
                                           ->sum('montant'),
            'stock_bas'          => Produit::whereColumn(
                                        'quantite_stock', '<=', 'seuil_alerte'
                                    )->count(),
            'total_clients'      => Client::count(),
            'total_produits'     => Produit::count(),
            'total_fournisseurs' => Fournisseur::count(),
            'livraisons_encours' => Livraison::where('statut', 'en_cours')->count(),
        ];

        // ── Comparaison avec période précédente ──
        $duree      = $dateDebut->diffInDays($dateFin) + 1;
        $debutPrec  = $dateDebut->copy()->subDays($duree);
        $finPrec    = $dateDebut->copy()->subDay()->endOfDay();

        $ventesPrec = Vente::whereBetween('date_vente', [$debutPrec, $finPrec])
                           ->where('statut', '!=', 'annulee');

        $statsPrec = [
            'total_ventes'     => (clone $ventesPrec)->count(),
            'chiffre_affaires' => (clone $ventesPrec)->sum('montant_total'),
        ];

        // Calcul tendances (%)
        $tendances = [
            'ventes' => $statsPrec['total_ventes'] > 0
                ? round((($stats['total_ventes'] - $statsPrec['total_ventes'])
                    / $statsPrec['total_ventes']) * 100, 1)
                : null,
            'ca' => $statsPrec['chiffre_affaires'] > 0
                ? round((($stats['chiffre_affaires'] - $statsPrec['chiffre_affaires'])
                    / $statsPrec['chiffre_affaires']) * 100, 1)
                : null,
        ];

        // ── Ventes par mois (graphique) ──
        $ventesParMoisRaw = Vente::select(
                DB::raw('MONTH(date_vente) as mois'),
                DB::raw('YEAR(date_vente) as annee'),
                DB::raw('SUM(montant_total) as total'),
                DB::raw('COUNT(*) as nombre')
            )
            ->where('statut', '!=', 'annulee')
            ->where('date_vente', '>=', now()->subMonths(6))
            ->groupBy('annee', 'mois')
            ->orderBy('annee')->orderBy('mois')
            ->get();

        $ventesParMois = $ventesParMoisRaw->map(fn($v) => [
            'mois'   => (int) $v->mois,
            'annee'  => (int) $v->annee,
            'total'  => (float) $v->total,
            'nombre' => (int) $v->nombre,
        ])->values();

        // ── Ventes par statut ──
        $ventesParStatut = Vente::select('statut', DB::raw('COUNT(*) as total'))
            ->whereBetween('date_vente', [$dateDebut, $dateFin])
            ->groupBy('statut')
            ->get()
            ->map(fn($v) => [
                'statut' => $v->statut,
                'total'  => (int) $v->total,
            ])->values();

        // ── CA par catégorie ──
        $caParCategorie = DB::table('vente_details')
            ->join('produits', 'vente_details.produit_id', '=', 'produits.id')
            ->join('categories', 'produits.categorie_id', '=', 'categories.id')
            ->join('ventes', 'vente_details.vente_id', '=', 'ventes.id')
            ->select(
                'categories.nom',
                DB::raw('SUM(vente_details.sous_total) as total')
            )
            ->whereBetween('ventes.date_vente', [$dateDebut, $dateFin])
            ->where('ventes.statut', '!=', 'annulee')
            ->groupBy('categories.id', 'categories.nom')
            ->orderByDesc('total')
            ->get()
            ->map(fn($c) => [
                'nom'   => $c->nom,
                'total' => (float) $c->total,
            ])->values();

        // ── Top 5 produits ──
        $topProduits = DB::table('vente_details')
            ->join('produits', 'vente_details.produit_id', '=', 'produits.id')
            ->join('ventes', 'vente_details.vente_id', '=', 'ventes.id')
            ->select(
                'produits.nom',
                DB::raw('SUM(vente_details.quantite) as total_vendu'),
                DB::raw('SUM(vente_details.sous_total) as chiffre_affaires')
            )
            ->whereBetween('ventes.date_vente', [$dateDebut, $dateFin])
            ->where('ventes.statut', '!=', 'annulee')
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_vendu')
            ->limit(5)->get();

        // ── Top 5 clients ──
        $topClients = Client::withSum(
                ['ventes' => fn($q) => $q->where('statut', '!=', 'annulee')
                                         ->whereBetween('date_vente',
                                             [$dateDebut, $dateFin])],
                'montant_total'
            )
            ->orderByDesc('ventes_sum_montant_total')
            ->limit(5)->get();

        // ── Produits stock bas ──
        $produitsStockBas = Produit::with('categorie')
            ->whereColumn('quantite_stock', '<=', 'seuil_alerte')
            ->orderBy('quantite_stock')
            ->limit(5)->get();

        // ── Dernières ventes ──
        $dernieresVentes = Vente::with(['client', 'user'])
            ->whereBetween('date_vente', [$dateDebut, $dateFin])
            ->latest()->limit(5)->get();

        // ── Livraisons en cours ──
        $livraisonsEnCours = Livraison::with(['client', 'vente'])
            ->where('statut', '!=', 'livree')
            ->latest()->limit(5)->get();

        return view('dashboard.index', compact(
            'stats', 'tendances', 'periode', 'libellePeriode',
            'dateDebut', 'dateFin',
            'ventesParMois', 'ventesParStatut', 'caParCategorie',
            'topProduits', 'topClients', 'produitsStockBas',
            'dernieresVentes', 'livraisonsEnCours'
        ));
    }
}