@extends('layouts.app')
@section('title', 'Paiements')
@section('page-title', 'Gestion des Paiements')

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
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center;
        justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .hector-stat-value {
        font-size: 1.6rem; font-weight: 700;
        line-height: 1; color: #111827;
    }
    .hector-stat-label {
        font-size: 0.78rem; color: #9CA3AF;
        margin-top: 3px; font-weight: 500;
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
    .btn-action {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #E5E7EB; background: white; color: #6B7280;
        transition: all 0.15s; font-size: 0.85rem;
        text-decoration: none; cursor: pointer;
    }
    .btn-action:hover.view { background:#EFF6FF;border-color:#93C5FD;color:#3B82F6; }
    .btn-action:hover.del  { background:#FEF2F2;border-color:#FECACA;color:#EF4444; }
    .search-wrapper { position: relative; }
    .search-wrapper i {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: #9CA3AF; font-size: 0.85rem;
    }
    .search-input {
        border: 1px solid #E5E7EB; border-radius: 8px;
        padding: 8px 14px 8px 38px; font-size: 0.875rem;
        color: #374151; background: #F9FAFB; transition: all 0.2s; width: 260px;
    }
    .search-input:focus {
        outline: none; border-color: #F97316; background: white;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }
    .mode-badge {
        border-radius: 20px; padding: 3px 10px;
        font-size: 0.72rem; font-weight: 600; display: inline-block;
    }

/* Nouveaux styles pour les statuts de facture */
    .statut-badge {
        border-radius: 6px; padding: 4px 10px;
        font-size: 0.7rem; font-weight: 700; 
        text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px;
    }
    .statut-payee { background: #DCFCE7; color: #15803d; }
    .statut-partiel { background: #FEF9C3; color: #854d0e; }
    .statut-non_payee { background: #FEE2E2; color: #991b1b; }
    

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
            Paiements
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            {{ $totalPaiements }} paiement{{ $totalPaiements > 1 ? 's' : '' }} enregistré{{ $totalPaiements > 1 ? 's' : '' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('paiements.rapport') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <i class="bi bi-bar-chart me-1" style="color:#8B5CF6"></i>
            Rapport
        </a>
        <a href="{{ route('export.paiements') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <i class="bi bi-file-earmark-excel me-1" style="color:#10B981"></i>
            Exporter
        </a>
        <a href="{{ route('paiements.create') }}"
           class="btn btn-sm"
           style="background:linear-gradient(135deg,#F97316,#EA580C);
                  color:white;border:none;border-radius:8px;
                  font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 2px 8px rgba(249,115,22,0.3)">
            <i class="bi bi-plus me-1"></i> Nouveau paiement
        </a>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-receipt" style="color:#F97316"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $totalPaiements }}</div>
                <div class="hector-stat-label">Total paiements</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#F0FDF4">
                <i class="bi bi-cash-stack" style="color:#10B981"></i>
            </div>
            <div>
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($totalEncaisse, 0, ',', ' ') }}
                    <span style="font-size:0.75rem;color:#9CA3AF;font-weight:500"> F</span>
                </div>
                <div class="hector-stat-label">Total encaissé</div>
            </div>
            <div class="ms-auto">
                <span style="font-size:0.72rem;color:#10B981;font-weight:600;
                             background:#F0FDF4;padding:3px 8px;border-radius:20px;
                             border:1px solid #A7F3D0">
                    ↑ Encaissé
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#EFF6FF">
                <i class="bi bi-cash" style="color:#3B82F6"></i>
            </div>
            <div>
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($parEspeces, 0, ',', ' ') }}
                    <span style="font-size:0.75rem;color:#9CA3AF;font-weight:500"> F</span>
                </div>
                <div class="hector-stat-label">En espèces</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFFBEB">
                <i class="bi bi-phone" style="color:#D97706"></i>
            </div>
            <div>
                <div class="hector-stat-value" style="font-size:1.1rem">
                    {{ number_format($parMobileMoney, 0, ',', ' ') }}
                    <span style="font-size:0.75rem;color:#9CA3AF;font-weight:500"> F</span>
                </div>
                <div class="hector-stat-label">Mobile Money</div>
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
            <input type="text" class="search-input"
                   id="recherche-paiements"
                   placeholder="Rechercher un paiement...">
        </div>
        <div class="d-flex align-items-center gap-3">
            <select id="filtre-mode"
                    style="border:1px solid #E5E7EB;border-radius:8px;
                           padding:7px 12px;font-size:0.82rem;
                           color:#374151;background:#F9FAFB;cursor:pointer">
                <option value="">Tous les modes</option>
                <option value="especes">Espèces</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="virement">Virement</option>
                <option value="cheque">Chèque</option>
            </select>
            <span style="font-size:0.8rem;color:#9CA3AF">
                <span class="fw-semibold" style="color:#111827">
                    {{ $paiements->total() }}
                </span>
                résultat{{ $paiements->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div id="tableau-paiements" class="table-responsive">
        @include('paiements.partials.tableau')
    </div>

    {{-- Pagination --}}
    @if($paiements->hasPages())
    <div style="border-top:1px solid #F9FAFB;padding:12px 20px">
        {{ $paiements->links() }}
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input  = document.getElementById('recherche-paiements');
    const filtre = document.getElementById('filtre-mode');
    const tbody  = document.querySelector('#tableau-paiements tbody');

    function filtrer() {
        if (!tbody) return;
        const q    = input.value.toLowerCase().trim();
        const mode = filtre.value;
        tbody.querySelectorAll('tr').forEach(function(row) {
            const text      = row.textContent.toLowerCase();
            const matchQ    = q === '' || text.includes(q);
            const matchMode = mode === '' ||
                              row.innerHTML.includes('mode-' + mode);
            row.style.display = (matchQ && matchMode) ? '' : 'none';
        });
    }

    if (input)  input.addEventListener('input', filtrer);
    if (filtre) filtre.addEventListener('change', filtrer);
});
</script>
@endpush

@endsection