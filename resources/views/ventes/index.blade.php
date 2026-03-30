@extends('layouts.app')
@section('title', 'Ventes')
@section('page-title', 'Gestion des Ventes')

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
    }
    .btn-action:hover.view { background:#EFF6FF;border-color:#93C5FD;color:#3B82F6; }
    .btn-action:hover.edit { background:#FFF7ED;border-color:#FED7AA;color:#F97316; }
    .btn-action:hover.del  { background:#FEF2F2;border-color:#FECACA;color:#EF4444; }

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

    /* Statut badges */
    .statut-badge {
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.72rem;
        font-weight: 600;
        display: inline-block;
    }
    .statut-en_attente {
        background: #FFFBEB;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    .statut-confirmee {
        background: #EFF6FF;
        color: #3B82F6;
        border: 1px solid #BFDBFE;
    }
    .statut-livree {
        background: #F0FDF4;
        color: #10B981;
        border: 1px solid #A7F3D0;
    }
    .statut-annulee {
        background: #FEF2F2;
        color: #EF4444;
        border: 1px solid #FECACA;
    }

    /* Select statut inline */
    .select-statut {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 0.78rem;
        color: #374151;
        background: #F9FAFB;
        cursor: pointer;
        transition: all 0.2s;
    }
    .select-statut:focus {
        outline: none;
        border-color: #F97316;
        box-shadow: 0 0 0 2px rgba(249,115,22,0.1);
    }
</style>
@endpush

@section('content')

{{-- ── En-tête ── --}}
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827;font-size:1.3rem">
            Ventes
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            {{ $totalVentes }} vente{{ $totalVentes > 1 ? 's' : '' }} enregistrée{{ $totalVentes > 1 ? 's' : '' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @if(Route::has('export.ventes'))
        <a href="{{ route('export.ventes') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <i class="bi bi-file-earmark-excel me-1" style="color:#10B981"></i>
            Exporter
        </a>
        @endif
        <a href="{{ route('ventes.create') }}"
           class="btn btn-sm"
           style="background:linear-gradient(135deg,#F97316,#EA580C);
                  color:white;border:none;border-radius:8px;
                  font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 2px 8px rgba(249,115,22,0.3)">
            <i class="bi bi-plus me-1"></i> Nouvelle vente
        </a>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-cart3-fill" style="color:#F97316"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $totalVentes }}</div>
                <div class="hector-stat-label">Total ventes</div>
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
                    {{ number_format($chiffreAffaire, 0, ',', ' ') }}
                    <span style="font-size:0.75rem;color:#9CA3AF;font-weight:500"> F</span>
                </div>
                <div class="hector-stat-label">Chiffre d'affaires</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFFBEB">
                <i class="bi bi-hourglass-split" style="color:#D97706"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $enAttente }}</div>
                <div class="hector-stat-label">En attente</div>
            </div>
            <div class="ms-auto">
                <span style="font-size:0.72rem;color:#D97706;font-weight:600;
                             background:#FFFBEB;padding:3px 8px;border-radius:20px;
                             border:1px solid #FDE68A">
                    En cours
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FEF2F2">
                <i class="bi bi-x-circle-fill" style="color:#EF4444"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $annulees }}</div>
                <div class="hector-stat-label">Annulées</div>
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
                   id="recherche-ventes"
                   placeholder="Rechercher une vente, un client..."
                   value="{{ $search ?? '' }}">
        </div>
        <div class="d-flex align-items-center gap-3">
            <span style="font-size:0.8rem;color:#9CA3AF">
                <span id="compteur-ventes" class="fw-semibold"
                      style="color:#111827">{{ $ventes->total() }}</span>
                résultat{{ $ventes->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div id="tableau-ventes" class="table-responsive">
        @include('ventes.partials.tableau')
    </div>

    {{-- Pagination --}}
    <div id="pagination-ventes"
         style="border-top:1px solid #F9FAFB;padding:12px 20px">
        @include('partials.pagination', compact('ventes'))
    </div>

</div>

@endsection