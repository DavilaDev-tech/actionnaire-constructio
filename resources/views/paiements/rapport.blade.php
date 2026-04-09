@extends('layouts.app')
@section('title', 'Rapport Financier')
@section('page-title', 'Rapport Financier')

@push('styles')
<style>
    .hector-stat {
        background: white;
        border-radius: 12px;
        border: 1px solid #F3F4F6;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .hector-stat:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transform: translateY(-2px);
    }
    .hector-stat-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center;
        justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }
    .hector-stat-value {
        font-size: 1.4rem; font-weight: 700;
        line-height: 1; color: #111827;
    }
    .hector-stat-label {
        font-size: 0.78rem; color: #9CA3AF;
        margin-top: 4px; font-weight: 500;
    }

    .hector-table thead th {
        background: #F9FAFB !important;
        color: #6B7280 !important;
        font-size: 0.72rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        padding: 12px 16px !important;
        border-bottom: 1px solid #F3F4F6 !important;
        border-top: none !important;
    }
    .hector-table tbody td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #F9FAFB !important;
        border-top: none !important;
        color: #374151 !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;
    }
    .hector-table tbody tr:hover td { background: #FFFBF5 !important; }
    .hector-table tbody tr:last-child td { border-bottom: none !important; }

    /* Barre de progression mode paiement */
    .mode-progress-bar {
        height: 6px;
        border-radius: 3px;
        background: #F3F4F6;
        overflow: hidden;
        margin-top: 6px;
    }
    .mode-progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 1s ease;
    }

    /* Cards statut factures */
    .facture-stat-card {
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        border: 1px solid #F3F4F6;
        transition: all 0.2s;
    }
    .facture-stat-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    .mode-badge {
        border-radius: 20px; padding: 3px 10px;
        font-size: 0.72rem; font-weight: 600; display: inline-block;
    }
    .mode-especes      { background:#F0FDF4;color:#10B981;border:1px solid #A7F3D0; }
    .mode-mobile_money { background:#EFF6FF;color:#3B82F6;border:1px solid #BFDBFE; }
    .mode-virement     { background:#F5F3FF;color:#8B5CF6;border:1px solid #DDD6FE; }
    .mode-cheque       { background:#FFFBEB;color:#D97706;border:1px solid #FDE68A; }
</style>
@endpush

@section('content')

{{-- ── En-tête ── --}}
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827;font-size:1.3rem">
            Rapport Financier
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            Vue d'ensemble des finances
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span style="background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
                     border-radius:20px;padding:5px 14px;font-size:0.82rem;font-weight:600">
            <i class="bi bi-calendar3 me-1"></i>
            {{ now()->isoFormat('MMMM YYYY') }}
        </span>
        <a href="{{ route('paiements.index') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

{{-- ── KPIs ── --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#F0FDF4">
                <i class="bi bi-cash-stack" style="color:#10B981"></i>
            </div>
            <div class="flex-grow-1">
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($totalEncaisse, 0, ',', ' ') }}
                    <span style="font-size:0.72rem;color:#9CA3AF;font-weight:400"> F</span>
                </div>
                <div class="hector-stat-label">Total encaissé</div>
            </div>
            <div style="font-size:0.72rem;color:#10B981;font-weight:600;
                         background:#F0FDF4;padding:3px 8px;border-radius:20px;
                         border:1px solid #A7F3D0;white-space:nowrap">
                ↑ Encaissé
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FEF2F2">
                <i class="bi bi-hourglass-split" style="color:#EF4444"></i>
            </div>
            <div class="flex-grow-1">
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($totalDu, 0, ',', ' ') }}
                    <span style="font-size:0.72rem;color:#9CA3AF;font-weight:400"> F</span>
                </div>
                <div class="hector-stat-label">Montant dû</div>
            </div>
            <div style="font-size:0.72rem;color:#EF4444;font-weight:600;
                         background:#FEF2F2;padding:3px 8px;border-radius:20px;
                         border:1px solid #FECACA;white-space:nowrap">
                À recouvrer
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-calendar-check" style="color:#F97316"></i>
            </div>
            <div class="flex-grow-1">
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($paiementsMois, 0, ',', ' ') }}
                    <span style="font-size:0.72rem;color:#9CA3AF;font-weight:400"> F</span>
                </div>
                <div class="hector-stat-label">Encaissé ce mois</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#EFF6FF">
                <i class="bi bi-receipt" style="color:#3B82F6"></i>
            </div>
            <div class="flex-grow-1">
                <div class="hector-stat-value">
                    {{ $facturesPayees }}
                    <span style="font-size:0.9rem;color:#9CA3AF;font-weight:400">
                        / {{ $facturesPayees + $facturesImpayees }}
                    </span>
                </div>
                <div class="hector-stat-label">Factures soldées</div>
            </div>
            @php
                $totalFactures = $facturesPayees + $facturesImpayees;
                $pctFactures   = $totalFactures > 0
                    ? round(($facturesPayees / $totalFactures) * 100)
                    : 0;
            @endphp
            <div style="font-size:0.72rem;color:#3B82F6;font-weight:700;
                         background:#EFF6FF;padding:3px 8px;border-radius:20px;
                         border:1px solid #BFDBFE;white-space:nowrap">
                {{ $pctFactures }}%
            </div>
        </div>
    </div>
</div>

{{-- ── Modes + Statut factures ── --}}
<div class="row g-3 mb-4">

    {{-- Modes de paiement --}}
    <div class="col-md-5">
        <div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                                  box-shadow:0 1px 4px rgba(0,0,0,0.06)">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                    <i class="bi bi-pie-chart me-2" style="color:#F97316"></i>
                    Répartition par mode
                </span>
            </div>
            <div class="px-4 py-3">
                @php
                    $modeConfig = [
                        'especes'      => ['libelle'=>'💵 Espèces',      'color'=>'#10B981','bg'=>'#F0FDF4'],
                        'mobile_money' => ['libelle'=>'📱 Mobile Money', 'color'=>'#3B82F6','bg'=>'#EFF6FF'],
                        'virement'     => ['libelle'=>'🏦 Virement',     'color'=>'#8B5CF6','bg'=>'#F5F3FF'],
                        'cheque'       => ['libelle'=>'📄 Chèque',       'color'=>'#D97706','bg'=>'#FFFBEB'],
                    ];
                @endphp
                @forelse($parMode as $mode)
                @php
                    $cfg = $modeConfig[$mode->mode_paiement] ?? ['libelle'=>$mode->mode_paiement,'color'=>'#6B7280','bg'=>'#F9FAFB'];
                    $pct = $totalEncaisse > 0
                        ? round(($mode->total / $totalEncaisse) * 100, 1)
                        : 0;
                @endphp
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:8px;height:8px;border-radius:50%;
                                        background:{{ $cfg['color'] }}"></div>
                            <span style="font-size:0.875rem;font-weight:500;color:#374151">
                                {{ $cfg['libelle'] }}
                            </span>
                        </div>
                        <div class="text-end">
                            <div style="font-size:0.82rem;font-weight:600;color:#111827">
                                {{ number_format($mode->total, 0, ',', ' ') }} F
                            </div>
                            <div style="font-size:0.72rem;color:#9CA3AF">
                                {{ $mode->nombre }} paiement{{ $mode->nombre > 1 ? 's' : '' }}
                                · {{ $pct }}%
                            </div>
                        </div>
                    </div>
                    <div class="mode-progress-bar">
                        <div class="mode-progress-fill"
                             style="width:{{ $pct }}%;
                                    background:{{ $cfg['color'] }}">
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="bi bi-inbox"
                       style="font-size:2rem;color:#D1D5DB;display:block;margin-bottom:8px"></i>
                    <div style="font-size:0.82rem;color:#9CA3AF">
                        Aucun paiement enregistré
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Statut factures --}}
    <div class="col-md-7">
        <div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                                  box-shadow:0 1px 4px rgba(0,0,0,0.06);height:100%">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                    <i class="bi bi-receipt me-2" style="color:#F97316"></i>
                    Statut des factures
                </span>
            </div>
            <div class="px-4 py-3 d-flex align-items-center h-100">
                <div class="row w-100 g-3">

                    {{-- Payées --}}
                    <div class="col-md-6">
                        <div class="facture-stat-card" style="background:#F0FDF4">
                            <div style="width:56px;height:56px;background:white;border-radius:12px;
                                        display:flex;align-items:center;justify-content:center;
                                        margin:0 auto 12px;box-shadow:0 2px 8px rgba(16,185,129,0.15)">
                                <i class="bi bi-check-circle-fill"
                                   style="color:#10B981;font-size:1.5rem"></i>
                            </div>
                            <div style="font-size:2rem;font-weight:800;color:#10B981;line-height:1">
                                {{ $facturesPayees }}
                            </div>
                            <div style="font-size:0.82rem;color:#6B7280;margin-top:4px">
                                Factures payées
                            </div>
                            <div style="font-size:0.72rem;color:#10B981;font-weight:600;
                                         margin-top:8px;background:white;border-radius:20px;
                                         padding:2px 10px;display:inline-block">
                                ✓ Soldées
                            </div>
                        </div>
                    </div>

                    {{-- Impayées --}}
                    <div class="col-md-6">
                        <div class="facture-stat-card" style="background:#FEF2F2">
                            <div style="width:56px;height:56px;background:white;border-radius:12px;
                                        display:flex;align-items:center;justify-content:center;
                                        margin:0 auto 12px;box-shadow:0 2px 8px rgba(239,68,68,0.15)">
                                <i class="bi bi-x-circle-fill"
                                   style="color:#EF4444;font-size:1.5rem"></i>
                            </div>
                            <div style="font-size:2rem;font-weight:800;color:#EF4444;line-height:1">
                                {{ $facturesImpayees }}
                            </div>
                            <div style="font-size:0.82rem;color:#6B7280;margin-top:4px">
                                Factures impayées
                            </div>
                            <div style="font-size:0.72rem;color:#EF4444;font-weight:600;
                                         margin-top:8px;background:white;border-radius:20px;
                                         padding:2px 10px;display:inline-block">
                                {{ number_format($totalDu, 0, ',', ' ') }} F dus
                            </div>
                        </div>
                    </div>

                    {{-- Barre de progression globale --}}
                    <div class="col-12">
                        <div style="background:#F3F4F6;border-radius:8px;padding:16px">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.82rem;color:#374151;font-weight:500">
                                    Taux de recouvrement
                                </span>
                                <span style="font-size:0.82rem;font-weight:700;color:#10B981">
                                    {{ $pctFactures }}%
                                </span>
                            </div>
                            <div style="height:8px;background:#E5E7EB;border-radius:4px;overflow:hidden">
                                <div style="height:100%;width:{{ $pctFactures }}%;
                                            background:linear-gradient(90deg,#10B981,#059669);
                                            border-radius:4px;transition:width 1s ease">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span style="font-size:0.72rem;color:#9CA3AF">
                                    {{ $facturesPayees }} factures soldées
                                </span>
                                <span style="font-size:0.72rem;color:#9CA3AF">
                                    {{ $facturesImpayees }} restantes
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Derniers paiements ── --}}
<div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                          box-shadow:0 1px 4px rgba(0,0,0,0.06)">

    <div class="d-flex justify-content-between align-items-center px-4 py-3"
         style="border-bottom:1px solid #F9FAFB">
        <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
            <i class="bi bi-clock-history me-2" style="color:#F97316"></i>
            Derniers paiements
        </span>
        <a href="{{ route('paiements.index') }}"
           style="font-size:0.82rem;color:#F97316;font-weight:600;text-decoration:none">
            Voir tout →
        </a>
    </div>

    <div class="table-responsive">
        <table class="table hector-table mb-0">
            <thead>
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
                @php
                    $initiale = strtoupper(substr($paiement->facture->vente->client->nom, 0, 1));
                    $colors   = ['#F97316','#3B82F6','#10B981','#8B5CF6','#EF4444','#F59E0B'];
                    $color    = $colors[ord($initiale) % count($colors)];
                    $modesCfg = [
                        'especes'      => ['libelle'=>'💵 Espèces',      'class'=>'mode-especes'],
                        'mobile_money' => ['libelle'=>'📱 Mobile Money', 'class'=>'mode-mobile_money'],
                        'virement'     => ['libelle'=>'🏦 Virement',     'class'=>'mode-virement'],
                        'cheque'       => ['libelle'=>'📄 Chèque',       'class'=>'mode-cheque'],
                    ];
                    $modeCfg = $modesCfg[$paiement->mode_paiement]
                        ?? ['libelle'=>$paiement->mode_paiement,'class'=>'mode-especes'];
                @endphp
                <tr>
                    <td>
                        <span class="fw-semibold"
                              style="color:#111827;font-family:monospace;font-size:0.82rem">
                            {{ $paiement->facture->numero }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;border-radius:50%;
                                        background:{{ $color }}20;color:{{ $color }};
                                        display:flex;align-items:center;justify-content:center;
                                        font-weight:700;font-size:0.72rem;flex-shrink:0">
                                {{ $initiale }}
                            </div>
                            <span style="color:#111827;font-weight:500">
                                {{ $paiement->facture->vente->client->nom }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold" style="color:#10B981">
                            +{{ number_format($paiement->montant, 0, ',', ' ') }}
                            <span style="color:#9CA3AF;font-weight:400;font-size:0.78rem"> F</span>
                        </span>
                    </td>
                    <td>
                        <span class="mode-badge {{ $modeCfg['class'] }}">
                            {{ $modeCfg['libelle'] }}
                        </span>
                    </td>
                    <td style="color:#9CA3AF;font-size:0.82rem">
                        {{ $paiement->date_paiement->format('d/m/Y') }}
                    </td>
                    <td>
                        @if($paiement->reference)
                        <span style="background:#F3F4F6;color:#374151;border-radius:6px;
                                     padding:3px 8px;font-size:0.75rem;font-family:monospace">
                            {{ $paiement->reference }}
                        </span>
                        @else
                        <span style="color:#D1D5DB">—</span>
                        @endif
                    </td>
                    <td style="color:#9CA3AF;font-size:0.82rem">
                        {{ $paiement->createdBy->prenom ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-inbox"
                           style="font-size:2.5rem;display:block;
                                  margin-bottom:12px;color:#D1D5DB"></i>
                        <div style="font-size:0.875rem;color:#9CA3AF">
                            Aucun paiement enregistré
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection