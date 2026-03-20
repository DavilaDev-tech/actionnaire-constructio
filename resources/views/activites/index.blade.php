@extends('layouts.app')
@section('title', 'Journal d\'activités')
@section('page-title', 'Journal d\'activités')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Journal d'activités</h4>
        <p class="text-muted mb-0">Historique de toutes les actions</p>
    </div>
    <form method="POST" action="{{ route('activites.vider') }}"
          onsubmit="return confirm('Supprimer les entrées de plus de 30 jours ?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">
            <i class="bi bi-trash me-1"></i> Nettoyer (> 30 jours)
        </button>
    </form>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-activity fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    <div class="small opacity-75">Total actions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-calendar-check fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['aujourd_hui'] }}</div>
                    <div class="small opacity-75">Aujourd'hui</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-arrow-in-right fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['connexions'] }}</div>
                    <div class="small opacity-75">Connexions aujourd'hui</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-pencil fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['modifications'] }}</div>
                    <div class="small opacity-75">Modifications aujourd'hui</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('activites.index') }}"
              class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Toutes</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}"
                            {{ request('action') == $action ? 'selected' : '' }}>
                        {{ ucfirst($action) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Module</label>
                <select name="module" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($modules as $module)
                    <option value="{{ $module }}"
                            {{ request('module') == $module ? 'selected' : '' }}>
                        {{ $module }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Utilisateur</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}"
                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->nom_complet }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date début</label>
                <input type="date" name="date_debut"
                       class="form-control form-control-sm"
                       value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date fin</label>
                <input type="date" name="date_fin"
                       class="form-control form-control-sm"
                       value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-1"></i> Filtrer
                </button>
                <a href="{{ route('activites.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Timeline --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Date & Heure</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activites as $activite)
                <tr>
                    <td class="text-muted small">
                        {{ $activite->created_at->format('d/m/Y H:i:s') }}
                        <br>
                        <span style="font-size:0.72rem">
                            {{ $activite->created_at->diffForHumans() }}
                        </span>
                    </td>
                    <td>
                        @if($activite->user)
                        <div class="fw-semibold small">
                            {{ $activite->user->nom_complet }}
                        </div>
                        <span class="badge bg-secondary"
                              style="font-size:0.65rem">
                            {{ ucfirst($activite->user->role) }}
                        </span>
                        @else
                        <span class="text-muted small">Système</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $activite->couleur_action }}">
                            <i class="bi {{ $activite->icone_action }} me-1"></i>
                            {{ ucfirst($activite->action) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $activite->module }}
                        </span>
                    </td>
                    <td class="small">{{ $activite->description }}</td>
                    <td class="text-muted small">
                        {{ $activite->ip ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard fs-1 d-block mb-2 opacity-50"></i>
                        Aucune activité enregistrée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($activites->hasPages())
    <div class="card-footer">
        {{ $activites->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection