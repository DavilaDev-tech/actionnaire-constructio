@extends('layouts.app')
@section('title', 'Sauvegardes')
@section('page-title', 'Sauvegardes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Sauvegardes</h4>
        <p class="text-muted mb-0">Gérez les sauvegardes de la base de données</p>
    </div>
    <form method="POST" action="{{ route('backups.lancer') }}">
        @csrf
        <button class="btn btn-primary">
            <i class="bi bi-cloud-download me-1"></i>
            Lancer une sauvegarde maintenant
        </button>
    </form>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-archive fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ count($backups) }}</div>
                    <div class="small opacity-75">Sauvegardes disponibles</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-clock-history fs-1 opacity-75"></i>
                <div>
                    <div class="fs-5 fw-bold">
                        {{ $backups[0]['date'] ?? '—' }}
                    </div>
                    <div class="small opacity-75">Dernière sauvegarde</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-calendar-check fs-1 opacity-75"></i>
                <div>
                    <div class="fs-5 fw-bold">02:00</div>
                    <div class="small opacity-75">Sauvegarde auto quotidienne</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-list-ul text-primary me-2"></i>
            Liste des sauvegardes
        </h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom du fichier</th>
                    <th>Taille</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr>
                    <td>
                        <i class="bi bi-file-zip text-warning me-2"></i>
                        {{ $backup['nom'] }}
                    </td>
                    <td>{{ $backup['taille'] }}</td>
                    <td>{{ $backup['date'] }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('backups.telecharger', ['path' => $backup['path']]) }}"
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download me-1"></i> Télécharger
                            </a>
                            <form method="POST"
                                  action="{{ route('backups.supprimer') }}"
                                  onsubmit="return confirm('Supprimer cette sauvegarde ?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="path"
                                       value="{{ $backup['path'] }}">
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-archive fs-1 d-block mb-2 opacity-50"></i>
                        <p>Aucune sauvegarde disponible.</p>
                        <p class="small">
                            Cliquez sur "Lancer une sauvegarde" pour créer la première.
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection