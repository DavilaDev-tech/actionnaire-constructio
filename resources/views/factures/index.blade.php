@extends('layouts.app')
@section('title', 'Factures')
@section('page-title', 'Gestion des Factures')

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
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .hector-stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
        color: #111827;
    }
    .hector-stat-label {
        font-size: 0.78rem;
        color: #9CA3AF;
        margin-top: 3px;
        font-weight: 500;
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
    .hector-table tbody tr:hover td {
        background: #FFFBF5 !important;
    }
    .hector-table tbody tr:last-child td {
        border-bottom: none !important;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E5E7EB;
        background: white;
        color: #6B7280;
        transition: all 0.15s;
        font-size: 0.85rem;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-action:hover.view     { background:#EFF6FF;border-color:#93C5FD;color:#3B82F6; }
    .btn-action:hover.download { background:#F0FDF4;border-color:#86EFAC;color:#10B981; }
    .btn-action:hover.check    { background:#FFF7ED;border-color:#FED7AA;color:#F97316; }

    .search-wrapper { position: relative; }
    .search-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 0.85rem;
    }
    .search-input {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 8px 14px 8px 38px;
        font-size: 0.875rem;
        color: #374151;
        background: #F9FAFB;
        transition: all 0.2s;
        width: 280px;
    }
    .search-input:focus {
        outline: none;
        border-color: #F97316;
        background: white;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    .statut-badge {
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.72rem;
        font-weight: 600;
        display: inline-block;
    }
    .statut-payee {
        background: #F0FDF4;
        color: #10B981;
        border: 1px solid #A7F3D0;
    }
    .statut-non_payee {
        background: #FFFBEB;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    .statut-annulee {
        background: #FEF2F2;
        color: #EF4444;
        border: 1px solid #FECACA;
    }

    /* Barre de progression paiement */
    .progress-mini {
        height: 4px;
        border-radius: 2px;
        background: #F3F4F6;
        margin-top: 5px;
        overflow: hidden;
    }
    .progress-mini-bar {
        height: 100%;
        border-radius: 2px;
        background: linear-gradient(90deg, #F97316, #EA580C);
        transition: width 0.5s ease;
    }
</style>
@endpush

@section('content')

{{-- ── En-tête ── --}}
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827;font-size:1.3rem">
            Factures
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            {{ $totalFactures }} facture{{ $totalFactures > 1 ? 's' : '' }} générée{{ $totalFactures > 1 ? 's' : '' }}
        </p>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-receipt-cutoff" style="color:#F97316"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $totalFactures }}</div>
                <div class="hector-stat-label">Total factures</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#F0FDF4">
                <i class="bi bi-check-circle-fill" style="color:#10B981"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $payees }}</div>
                <div class="hector-stat-label">Payées</div>
            </div>
            <div class="ms-auto">
                <span style="font-size:0.72rem;color:#10B981;font-weight:600;
                             background:#F0FDF4;padding:3px 8px;border-radius:20px;
                             border:1px solid #A7F3D0">
                    ✓ Soldées
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFFBEB">
                <i class="bi bi-hourglass-split" style="color:#D97706"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $nonPayees }}</div>
                <div class="hector-stat-label">Non payées</div>
            </div>
            <div class="ms-auto">
                <span style="font-size:0.72rem;color:#D97706;font-weight:600;
                             background:#FFFBEB;padding:3px 8px;border-radius:20px;
                             border:1px solid #FDE68A">
                    En attente
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FEF2F2">
                <i class="bi bi-cash-stack" style="color:#EF4444"></i>
            </div>
            <div>
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($montantDu, 0, ',', ' ') }}
                    <span style="font-size:0.75rem;color:#9CA3AF;font-weight:500"> F</span>
                </div>
                <div class="hector-stat-label">Montant dû</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tableau ── --}}
<div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                          box-shadow:0 1px 4px rgba(0,0,0,0.06)">

    {{-- Toolbar --}}
    <div class="d-flex justify-content-between align-items-center px-4 py-3"
         style="border-bottom:1px solid #F9FAFB">
        <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text"
                   class="search-input"
                   id="recherche-factures"
                   placeholder="Rechercher une facture..."
                   value="">
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Filtre statut --}}
            <select id="filtre-statut"
                    style="border:1px solid #E5E7EB;border-radius:8px;
                           padding:7px 12px;font-size:0.82rem;
                           color:#374151;background:#F9FAFB;cursor:pointer">
                <option value="">Tous les statuts</option>
                <option value="non_payee">Non payées</option>
                <option value="payee">Payées</option>
                <option value="annulee">Annulées</option>
            </select>
            <span style="font-size:0.8rem;color:#9CA3AF">
                <span class="fw-semibold" style="color:#111827">
                    {{ $factures->total() }}
                </span> résultat{{ $factures->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>
@include('factures.partials.tableau')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Recherche en temps réel ──
    const input  = document.getElementById('recherche-factures');
    const filtre = document.getElementById('filtre-statut');
    const tbody  = document.querySelector('.hector-table tbody');

    function filtrer() {
        const q       = input.value.toLowerCase().trim();
        const statut  = filtre.value;
        const rows    = tbody.querySelectorAll('tr');
        let   visible = 0;

        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            const matchQ      = q === '' || text.includes(q);
            const matchStatut = statut === '' || row.innerHTML.includes('statut-' + statut);
            if (matchQ && matchStatut) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (input)  input.addEventListener('input', filtrer);
    if (filtre) filtre.addEventListener('change', filtrer);

});
</script>
@endpush

@endsection