@extends('layouts.app')

@section('title', 'Journal d\'activités')

@section('content')


<style>
    /* Hector Style System */
    :root {
        --hector-primary: #2563eb;
        --hector-border: #f1f5f9;
    }
    .bg-soft-primary { background: #eff6ff; color: #2563eb; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; }
    
    /* Stats light backgrounds */
    .bg-light-primary { background-color: #eef2ff !important; }
    .bg-light-success { background-color: #f0fdf4 !important; }
    .bg-light-info { background-color: #f0f9ff !important; }
    .bg-light-warning { background-color: #fffbeb !important; }
    
    /* Custom badge dots */
    .badge-dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    
    .table thead th { background-color: #f8fafc; font-size: 11px; }
    .table td { padding: 0.75rem 0.5rem; border-color: var(--hector-border); }
    
    .breadcrumb-item, .breadcrumb-item a { font-size: 12px; color: #64748b; }
    
    .form-select-sm, .form-control-sm { border-radius: 8px; border-color: #e2e8f0; }
    
    /* Pagination Style Hector */
    .pagination { margin-bottom: 0; gap: 5px; }
    .page-link { border: none; border-radius: 8px !important; color: #64748b; font-size: 13px; }
    .page-item.active .page-link { background-color: #0f172a; }
</style>

<div class="container-fluid px-4">
    {{-- Header Hector Style --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-0">Journal d'activités</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Audit Log</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{ route('activites.vider') }}" 
              onsubmit="return confirm('Supprimer les entrées de plus de 30 jours ?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm rounded-3">
                <i class="bi bi-trash3 me-1"></i> Purger l'historique (> 30j)
            </button>
        </form>
    </div>

    {{-- Stats Grill --}}
    <div class="row g-3 mb-4">
        @php
            $statCards = [
                ['Total', $stats['total'], 'bi-database', 'primary'],
                ['Aujourd\'hui', $stats['aujourd_hui'], 'bi-calendar-event', 'success'],
                ['Connexions', $stats['connexions'], 'bi-shield-check', 'info'],
                ['Modifications', $stats['modifications'], 'bi-pencil-square', 'warning']
            ];
        @endphp

        @foreach($statCards as $stat)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">{{ $stat[0] }}</p>
                            <h4 class="mb-0 fw-bolder">{{ number_format($stat[1], 0, ',', ' ') }}</h4>
                        </div>
                        <div class="bg-light-{{ $stat[3] }} p-2 rounded-3">
                            <i class="bi {{ $stat[2] }} fs-4 text-{{ $stat[3] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('activites.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Action</label>
                    <select name="action" class="form-select form-select-sm border-light">
                        <option value="">Toutes</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Module</label>
                    <select name="module" class="form-select form-select-sm border-light">
                        <option value="">Tous les modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Utilisateur</label>
                    <select name="user_id" class="form-select form-select-sm border-light">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Période</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="date_debut" class="form-control border-light" value="{{ request('date_debut') }}">
                        <span class="input-group-text bg-light border-light">au</span>
                        <input type="date" name="date_fin" class="form-control border-light" value="{{ request('date_fin') }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-3">
                        Filtrer
                    </button>
                    <a href="{{ route('activites.index') }}" class="btn btn-light btn-sm border rounded-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3 border-0 py-3 small text-uppercase text-muted fw-bold">Date & Heure</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold">Acteur</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold">Opération</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold">Cible</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold">Détails</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold text-end pe-3">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($activites as $activite)
                    <tr>
                        <td class="ps-3">
                            <span class="d-block fw-bold text-dark small">{{ $activite->created_at->format('d/m/Y') }}</span>
                            <span class="text-muted" style="font-size: 11px;">{{ $activite->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($activite->user)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text bg-soft-primary rounded-circle me-2">
                                        {{ strtoupper(substr($activite->user->nom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold small mb-0">{{ $activite->user->nom_complet }}</div>
                                        <div class="text-muted" style="font-size: 10px;">{{ strtoupper($activite->user->role) }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-light text-muted fw-normal">Système</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-dot bg-{{ $activite->couleur_action }}"></span>
                            <span class="small fw-bold text-dark">{{ strtoupper($activite->action) }}</span>
                        </td>
                        <td>
                            <span class="text-muted small"><i class="bi bi-tag-fill me-1"></i>{{ $activite->module }}</span>
                        </td>
                        <td>
                            <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;" title="{{ $activite->description }}">
                                {{ $activite->description }}
                            </p>
                        </td>
                        <td class="text-end pe-3">
                            <span class="font-monospace text-muted small">{{ $activite->ip ?? '127.0.0.1' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Aucune donnée trouvée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activites->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            {{ $activites->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Hector Style System */
    :root {
        --hector-primary: #2563eb;
        --hector-border: #f1f5f9;
    }
    .bg-soft-primary { background: #eff6ff; color: #2563eb; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; }
    
    /* Stats light backgrounds */
    .bg-light-primary { background-color: #eef2ff !important; }
    .bg-light-success { background-color: #f0fdf4 !important; }
    .bg-light-info { background-color: #f0f9ff !important; }
    .bg-light-warning { background-color: #fffbeb !important; }
    
    /* Custom badge dots */
    .badge-dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    
    .table thead th { background-color: #f8fafc; font-size: 11px; }
    .table td { padding: 0.75rem 0.5rem; border-color: var(--hector-border); }
    
    .breadcrumb-item, .breadcrumb-item a { font-size: 12px; color: #64748b; }
    
    .form-select-sm, .form-control-sm { border-radius: 8px; border-color: #e2e8f0; }
    
    /* Pagination Style Hector */
    .pagination { margin-bottom: 0; gap: 5px; }
    .page-link { border: none; border-radius: 8px !important; color: #64748b; font-size: 13px; }
    .page-item.active .page-link { background-color: #0f172a; }
</style>

@endsection