@extends('layouts.app')
@section('title', 'Rapport Financier')
@section('page-title', 'Rapport Financier')

@push('styles')
<style>
    .rapport-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .rapport-card:hover { transform: translateY(-3px); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Rapport Financier</h4>
        <p class="text-muted mb-0">Vue d'ensemble des finances</p>
    </div>
    <span class="badge bg-primary fs-6">
        {{ now()->isoFormat('MMMM YYYY') }}
    </span>
</div>

<!-- KPIs principaux -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card rapport-card shadow-sm bg-success text-white">
            <div class="card-body text-center py-4">
                <i class="bi bi-cash-stack fs-1 mb-2 opacity-75"></i>
                <div class="fs-4 fw-bold">
                    {{ number_format($totalEncaisse, 0, ',', ' ') }} F
                </div>
                <div class="small opacity-75 mt-1">Total encaissé</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card rapport-card shadow-sm bg-danger text-white">
            <div class="card-body text-center py-4">
                <i class="bi bi-hourglass fs-1 mb-2 opacity-75"></i>
                <div class="fs-4 fw-bold">
                    {{ number_format($totalDu, 0, ',', ' ') }} F
                </div>
                <div class="small opacity-75 mt-1">Montant dû</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card rapport-card shadow-sm bg-primary text-white">
            <div class="card-body text-center py-4">
                <i class="bi bi-calendar-check fs-1 mb-2 opacity-75"></i>
                <div class="fs-4 fw-bold">
                    {{ number_format($paiementsMois, 0, ',', ' ') }} F
                </div>
                <div class="small opacity-75 mt-1">Encaissé ce mois</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card rapport-card shadow-sm bg-warning text-dark">
            <div class="card-body text-center py-4">
                <i class="bi bi-receipt fs-1 mb-2 opacity-75"></i>
                <div class="fs-4 fw-bold">
                    {{ $facturesPayees }} / {{ $facturesPayees + $facturesImpayees }}
                </div>
                <div class="small opacity-75 mt-1">Factures payées</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    <!-- Répartition par mode de paiement -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-pie-chart text-primary me-2"></i>
                    Répartition par mode de paiement
                </h6>
            </div>
            <div class="card-body">
                @forelse($parMode as $mode)
                @php
                    $couleurs = [
                        'especes'      => 'success',
                        'mobile_money' => 'primary',
                        'virement'     => 'info',
                        'cheque'       => 'warning',
                    ];
                    $libelles = [
                        'especes'      => '💵 Espèces',
                        'mobile_money' => '📱 Mobile Money',
                        'virement'     => '🏦 Virement',
                        'cheque'       => '📄 Chèque',
                    ];
                    $pct = $totalEncaisse > 0
                        ? round(($mode->total / $totalEncaisse) * 100, 1)
                        : 0;
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold">
                            {{ $libelles[$mode->mode_paiement] ?? $mode->mode_paiement }}
                        </span>
                        <span class="text-muted small">
                            {{ number_format($mode->total, 0, ',', ' ') }} F
                            ({{ $pct }}%)
                        </span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $couleurs[$mode->mode_paiement] ?? 'secondary' }}"
                             style="width: {{ $pct }}%">
                        </div>
                    </div>
                    <small class="text-muted">{{ $mode->nombre }} paiement(s)</small>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Aucun paiement enregistré.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Statut factures -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-receipt text-warning me-2"></i>
                    Statut des factures
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="row text-center w-100">
                    <div class="col-md-6 mb-3">
                        <div class="p-4 bg-success bg-opacity-10 rounded-3">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <div class="fs-2 fw-bold text-success mt-2">
                                {{ $facturesPayees }}
                            </div>
                            <div class="text-muted">Factures payées</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-4 bg-danger bg-opacity-10 rounded-3">
                            <i class="bi bi-x-circle text-danger fs-1"></i>
                            <div class="fs-2 fw-bold text-danger mt-2">
                                {{ $facturesImpayees }}
                            </div>
                            <div class="text-muted">Factures impayées</div>
                            <div class="small text-danger fw-semibold mt-1">
                                {{ number_format($totalDu, 0, ',', ' ') }} F dus
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Derniers paiements -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-3 d-flex
                justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-clock-history text-primary me-2"></i>
            Derniers paiements
        </h6>
        <a href="{{ route('paiements.index') }}"
           class="btn btn-sm btn-outline-primary">
            Voir tout
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Facture</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Enregistré par</th>
                </tr>
            </thead>
            <tbody>
                @forelse($derniersPaiements as $paiement)
                <tr>
                    <td class="fw-semibold">{{ $paiement->facture->numero }}</td>
                    <td>{{ $paiement->facture->vente->client->nom }}</td>
                    <td class="fw-bold text-success">
                        {{ number_format($paiement->montant, 0, ',', ' ') }} F
                    </td>
                    <td>
                        <span class="badge bg-{{ $paiement->couleur_mode }}">
                            {{ $paiement->libelle_mode }}
                        </span>
                    </td>
                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                    <td>{{ $paiement->reference ?? '—' }}</td>
                    <td>{{ $paiement->createdBy->nom_complet }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Aucun paiement enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection