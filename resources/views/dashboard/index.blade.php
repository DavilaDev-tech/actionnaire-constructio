@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@push('styles')
<style>
    .stat-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
    }
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    .stat-icon {
        width: 55px; height: 55px;
        border-radius: 12px;
        display: flex; align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: transform 0.3s;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .chart-card, .table-card { border: none; border-radius: 12px; }

    /* Filtres période */
    .btn-periode {
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 500;
        padding: 6px 14px;
        border: 1.5px solid #e5e7eb;
        color: #6b7280;
        background: white;
        transition: all 0.2s;
    }
    .btn-periode:hover, .btn-periode.active {
        background: #1a3c5e;
        color: white;
        border-color: #1a3c5e;
    }

    /* Tendances */
    .tendance-up   { color: #198754; font-size: 0.78rem; font-weight: 600; }
    .tendance-down { color: #dc3545; font-size: 0.78rem; font-weight: 600; }
    .tendance-neu  { color: #6c757d; font-size: 0.78rem; }
</style>
@endpush

@section('content')

{{-- ══ Bienvenue ══ --}}
<div class="alert border-0 shadow-sm mb-4"
     style="background:linear-gradient(135deg,#1a3c5e,#2d6a9f);
            color:white;border-radius:12px">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:52px;height:52px;background:rgba(255,255,255,0.15);
                        border-radius:12px;display:flex;align-items:center;
                        justify-content:center;font-size:1.5rem">
                👋
            </div>
            <div>
                <h5 class="mb-0">Bienvenue, {{ auth()->user()->nom_complet }} !</h5>
                <small class="opacity-75">
                    {{ ucfirst(auth()->user()->role) }} —
                    {{ now()->isoFormat('dddd D MMMM YYYY') }}
                </small>
            </div>
        </div>
        <!-- Période active -->
        <div class="badge fs-6 px-3 py-2"
             style="background:rgba(232,160,32,0.25);
                    color:#e8a020;border-radius:8px">
            <i class="bi bi-calendar3 me-1"></i>
            {{ $libellePeriode }}
        </div>
    </div>
</div>

{{-- ══ Filtres Période ══ --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius:12px">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('dashboard') }}"
              id="form-periode"
              class="d-flex flex-wrap align-items-center gap-2">

            <span class="text-muted small fw-semibold me-1">
                <i class="bi bi-funnel me-1"></i>Période :
            </span>

            @foreach([
                'aujourd_hui' => "Aujourd'hui",
                'semaine'     => 'Cette semaine',
                'mois'        => 'Ce mois',
                'annee'       => 'Cette année',
            ] as $val => $label)
            <button type="submit"
                    name="periode" value="{{ $val }}"
                    class="btn-periode {{ $periode === $val ? 'active' : '' }}">
                {{ $label }}
            </button>
            @endforeach

            <!-- Séparateur -->
            <div class="vr mx-1"></div>

            <!-- Période personnalisée -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <input type="date"
                       name="date_debut"
                       class="form-control form-control-sm"
                       style="width:140px;border-radius:8px;font-size:0.82rem"
                       value="{{ $periode === 'personnalise' ? $dateDebut->format('Y-m-d') : '' }}"
                       placeholder="Date début">
                <span class="text-muted small">→</span>
                <input type="date"
                       name="date_fin"
                       class="form-control form-control-sm"
                       style="width:140px;border-radius:8px;font-size:0.82rem"
                       value="{{ $periode === 'personnalise' ? $dateFin->format('Y-m-d') : '' }}"
                       placeholder="Date fin">
                <button type="submit"
                        name="periode" value="personnalise"
                        class="btn-periode {{ $periode === 'personnalise' ? 'active' : '' }}">
                    <i class="bi bi-search me-1"></i>Appliquer
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ══ Cartes statistiques ══ --}}
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e3f2fd">
                    <i class="bi bi-cart3 text-primary"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Ventes</div>
                    <div class="fs-3 fw-bold text-primary">
                        <span class="compteur"
                              data-target="{{ $stats['total_ventes'] }}">0</span>
                    </div>
                    @if($tendances['ventes'] !== null)
                    <div class="{{ $tendances['ventes'] >= 0 ? 'tendance-up' : 'tendance-down' }}">
                        <i class="bi bi-arrow-{{ $tendances['ventes'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($tendances['ventes']) }}% vs période préc.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f5e9">
                    <i class="bi bi-cash-stack text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">Chiffre d'affaires</div>
                    <div class="fs-5 fw-bold text-success">
                        <span class="compteur-money"
                              data-target="{{ $stats['chiffre_affaires'] }}">0</span> F
                    </div>
                    @if($tendances['ca'] !== null)
                    <div class="{{ $tendances['ca'] >= 0 ? 'tendance-up' : 'tendance-down' }}">
                        <i class="bi bi-arrow-{{ $tendances['ca'] >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($tendances['ca']) }}% vs période préc.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff3e0">
                    <i class="bi bi-receipt text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">Factures impayées</div>
                    <div class="fs-5 fw-bold text-warning">
                        <span class="compteur-money"
                              data-target="{{ $stats['factures_impayees'] }}">0</span> F
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fce4ec">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small">Stock bas</div>
                    <div class="fs-3 fw-bold text-danger">
                        <span class="compteur"
                              data-target="{{ $stats['stock_bas'] }}">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f3e5f5">
                    <i class="bi bi-people" style="color:#6a1b9a"></i>
                </div>
                <div>
                    <div class="text-muted small">Clients</div>
                    <div class="fs-3 fw-bold" style="color:#6a1b9a">
                        <span class="compteur"
                              data-target="{{ $stats['total_clients'] }}">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e0f2f1">
                    <i class="bi bi-box-seam" style="color:#00695c"></i>
                </div>
                <div>
                    <div class="text-muted small">Produits</div>
                    <div class="fs-3 fw-bold" style="color:#00695c">
                        <span class="compteur"
                              data-target="{{ $stats['total_produits'] }}">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8eaf6">
                    <i class="bi bi-truck" style="color:#3949ab"></i>
                </div>
                <div>
                    <div class="text-muted small">Fournisseurs</div>
                    <div class="fs-3 fw-bold" style="color:#3949ab">
                        <span class="compteur"
                              data-target="{{ $stats['total_fournisseurs'] }}">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e1f5fe">
                    <i class="bi bi-geo-alt text-info"></i>
                </div>
                <div>
                    <div class="text-muted small">Livraisons en cours</div>
                    <div class="fs-3 fw-bold text-info">
                        <span class="compteur"
                              data-target="{{ $stats['livraisons_encours'] }}">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══ Graphiques ══ --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-graph-up text-primary me-2"></i>
                    Évolution des ventes (6 derniers mois)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartVentesMois" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-pie-chart text-warning me-2"></i>
                    Ventes par statut
                    <small class="text-muted fw-normal">({{ $libellePeriode }})</small>
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartStatuts" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card chart-card shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-bar-chart text-success me-2"></i>
                    CA par catégorie
                    <small class="text-muted fw-normal">({{ $libellePeriode }})</small>
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartCategories" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card table-card shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-trophy text-warning me-2"></i>
                    Top 5 Produits
                    <small class="text-muted fw-normal">({{ $libellePeriode }})</small>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">CA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProduits as $i => $produit)
                        <tr>
                            <td>
                                @if($i==0) 🥇
                                @elseif($i==1) 🥈
                                @elseif($i==2) 🥉
                                @else {{ $i+1 }}
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $produit->nom }}</td>
                            <td class="text-center">{{ $produit->total_vendu }}</td>
                            <td class="text-end text-success">
                                {{ number_format($produit->chiffre_affaires, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Aucune vente sur cette période.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ══ Tableaux récapitulatifs ══ --}}
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white border-0 pt-3 d-flex
                        justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Dernières ventes
                    <small class="text-muted fw-normal">({{ $libellePeriode }})</small>
                </h6>
                <a href="{{ route('ventes.index') }}"
                   class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Vente</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dernieresVentes as $vente)
                        @php
                            $colors = [
                                'en_attente' => 'warning',
                                'confirmee'  => 'primary',
                                'livree'     => 'success',
                                'annulee'    => 'danger',
                            ];
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $vente->numero_vente }}</td>
                            <td>{{ $vente->client->nom }}</td>
                            <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                            <td>
                                <span class="badge bg-{{ $colors[$vente->statut] }}">
                                    {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Aucune vente sur cette période.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white border-0 pt-3 d-flex
                        justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Alertes Stock
                </h6>
                <a href="{{ route('produits.index') }}"
                   class="btn btn-sm btn-outline-danger">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Seuil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produitsStockBas as $produit)
                        <tr>
                            <td class="fw-semibold">
                                {{ Str::limit($produit->nom, 20) }}
                                <br>
                                <small class="text-muted">
                                    {{ $produit->categorie->nom }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{
                                    $produit->quantite_stock == 0
                                        ? 'danger' : 'warning'
                                }} rounded-pill">
                                    {{ $produit->quantite_stock }}
                                </span>
                            </td>
                            <td class="text-center text-muted">
                                {{ $produit->seuil_alerte }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Tous les stocks sont OK !
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-people text-success me-2"></i>
                    Top 5 Clients
                    <small class="text-muted fw-normal">({{ $libellePeriode }})</small>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Client</th>
                            <th class="text-end">Total achats</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topClients as $i => $client)
                        <tr>
                            <td class="fw-bold text-muted">{{ $i+1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $client->nom }}</div>
                                <small class="text-muted">
                                    {{ ucfirst($client->type) }}
                                </small>
                            </td>
                            <td class="text-end fw-semibold text-success">
                                {{ number_format($client->ventes_sum_montant_total ?? 0, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                Aucun client.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white border-0 pt-3 d-flex
                        justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-truck text-info me-2"></i>
                    Livraisons en cours
                </h6>
                <a href="{{ route('livraisons.index') }}"
                   class="btn btn-sm btn-outline-info">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vente</th>
                            <th>Client</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livraisonsEnCours as $livraison)
                        <tr>
                            <td class="fw-semibold">
                                {{ $livraison->vente->numero_vente }}
                            </td>
                            <td>{{ $livraison->client->nom }}</td>
                            <td>
                                <span class="badge bg-{{ $livraison->couleur_statut }}">
                                    {{ ucfirst(str_replace('_',' ',$livraison->statut)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Aucune livraison en cours.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.addEventListener('load', function () {
    const script    = document.createElement('script');
    script.src      = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
    script.onload   = dessinerGraphiques;
    document.head.appendChild(script);
});

function dessinerGraphiques() {
    const ventesParMois   = @json($ventesParMois);
    const ventesParStatut = @json($ventesParStatut);
    const caParCategorie  = @json($caParCategorie);
    const moisNoms = ['','Jan','Fév','Mar','Avr','Mai','Jun',
                      'Jul','Aoû','Sep','Oct','Nov','Déc'];

    // ── Graphique 1 : Ventes par mois ──
    const c1 = document.getElementById('chartVentesMois');
    if (c1) {
        new Chart(c1, {
            type: 'bar',
            data: {
                labels: ventesParMois.map(v => moisNoms[v.mois] + ' ' + v.annee),
                datasets: [
                    {
                        label: 'CA (F CFA)',
                        data: ventesParMois.map(v => v.total),
                        backgroundColor: 'rgba(26,60,94,0.85)',
                        borderColor: '#1a3c5e',
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Nb ventes',
                        data: ventesParMois.map(v => v.nombre),
                        type: 'line',
                        borderColor: '#e8a020',
                        backgroundColor: 'rgba(232,160,32,0.15)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 5,
                        pointBackgroundColor: '#e8a020',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top' } },
                scales: {
                    y:  { position: 'left',  ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' } },
                    y1: { position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });
    }

    // ── Graphique 2 : Statuts ──
    const c2 = document.getElementById('chartStatuts');
    if (c2) {
        if (ventesParStatut.length === 0) {
            c2.parentElement.innerHTML = `<div class="text-center text-muted py-5">
                <i class="bi bi-pie-chart fs-1 d-block mb-2 opacity-50"></i>
                <p>Aucune vente sur cette période.</p></div>`;
        } else {
            const colors = { en_attente:'#ffc107', confirmee:'#0d6efd',
                             livree:'#198754', annulee:'#dc3545' };
            new Chart(c2, {
                type: 'doughnut',
                data: {
                    labels: ventesParStatut.map(v =>
                        v.statut.replace('_',' ').replace(/^\w/,c=>c.toUpperCase())),
                    datasets: [{
                        data: ventesParStatut.map(v => v.total),
                        backgroundColor: ventesParStatut.map(v => colors[v.statut] ?? '#6c757d'),
                        borderWidth: 2, borderColor: '#fff',
                    }]
                },
                options: { responsive: true, cutout: '65%',
                           plugins: { legend: { position: 'bottom' } } }
            });
        }
    }

    // ── Graphique 3 : CA catégories ──
    const c3 = document.getElementById('chartCategories');
    if (c3) {
        if (caParCategorie.length === 0) {
            c3.parentElement.innerHTML = `<div class="text-center text-muted py-5">
                <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-50"></i>
                <p>Aucune donnée sur cette période.</p></div>`;
        } else {
            const palette = ['#1a3c5e','#e8a020','#2e7d32','#6a1b9a',
                             '#00695c','#c62828','#1565c0','#f57f17'];
            new Chart(c3, {
                type: 'bar',
                data: {
                    labels: caParCategorie.map(c => c.nom),
                    datasets: [{
                        data: caParCategorie.map(c => c.total),
                        backgroundColor: caParCategorie.map((_,i) => palette[i % palette.length]),
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' } } }
                }
            });
        }
    }
}

// ── Compteurs animés ──
document.addEventListener('DOMContentLoaded', function () {
    function animer(el, fin, duree, money) {
        if (fin === 0) { el.textContent = '0'; return; }
        let start = null;
        const step = ts => {
            if (!start) start = ts;
            const p = Math.min((ts - start) / duree, 1);
            const e = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.floor(e * fin).toLocaleString('fr-FR');
            if (p < 1) requestAnimationFrame(step);
            else el.textContent = fin.toLocaleString('fr-FR');
        };
        requestAnimationFrame(step);
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.anime) {
                entry.target.dataset.anime = 'true';
                const target = parseFloat(entry.target.dataset.target) || 0;
                const money  = entry.target.classList.contains('compteur-money');
                animer(entry.target, target, money ? 2000 : 1500, money);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.compteur, .compteur-money')
            .forEach(el => observer.observe(el));
});
</script>
@endpush