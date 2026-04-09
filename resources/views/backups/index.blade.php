@extends('layouts.app')

@section('title', 'Sauvegardes')

@section('content')

<style>
    /* Hector Style - Backup Extension */
    .bg-soft-primary { background-color: #eff6ff; }
    .bg-soft-success { background-color: #f0fdf4; }
    .bg-soft-info { background-color: #f0f9ff; }
    
    .table thead th { 
        background-color: #f8fafc; 
        font-size: 11px; 
        letter-spacing: 0.5px;
    }

    .table td { 
        padding: 1rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .breadcrumb-item, .breadcrumb-item a { 
        font-size: 12px; 
        color: #64748b; 
    }

    .btn-light:hover {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Fix Pagination SVG scale if necessary */
    .pagination svg { width: 18px; height: 18px; }
</style>

<div class="container-fluid px-4">
    {{-- Header Style Hector --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h4 class="fw-bold text-dark mb-0">Gestion des Sauvegardes</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Backups</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{ route('backups.lancer') }}">
            @csrf
            <button class="btn btn-dark btn-sm rounded-3 shadow-sm px-3">
                <i class="bi bi-cloud-plus me-1"></i>
                Lancer une sauvegarde
            </button>
        </form>
    </div>

    {{-- Stats Grill --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Total Archivé</p>
                            <h4 class="mb-0 fw-bolder text-dark">{{ count($backups) }}</h4>
                        </div>
                        <div class="bg-soft-primary p-3 rounded-3">
                            <i class="bi bi-archive fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Dernière Opération</p>
                            <h5 class="mb-0 fw-bolder text-dark">{{ $backups[0]['date'] ?? 'Aucune' }}</h5>
                        </div>
                        <div class="bg-soft-success p-3 rounded-3">
                            <i class="bi bi-clock-history fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Auto-Sauvegarde</p>
                            <h4 class="mb-0 fw-bolder text-dark">02:00 <small class="text-muted fw-normal fs-6">AM</small></h4>
                        </div>
                        <div class="bg-soft-info p-3 rounded-3">
                            <i class="bi bi-calendar-check fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup List Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-list-nested me-2 text-primary"></i>
                Historique des fichiers
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 border-0 py-3 small text-uppercase text-muted fw-bold">Nom du fichier</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold text-center">Poids</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold text-center">Généré le</th>
                        <th class="border-0 py-3 small text-uppercase text-muted fw-bold text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($backups as $backup)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-zip fs-4 text-warning me-3"></i>
                                <div>
                                    <span class="text-dark fw-bold small d-block mb-0">{{ $backup['nom'] }}</span>
                                    <span class="text-muted" style="font-size: 11px;">Format: .zip (SQL + Media)</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark fw-medium border rounded-pill px-3">
                                {{ $backup['taille'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted small fw-bold">{{ $backup['date'] }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('backups.telecharger', ['path' => $backup['path']]) }}"
                                   class="btn btn-sm btn-light border text-success rounded-3 px-3"
                                   title="Télécharger">
                                    <i class="bi bi-download me-1"></i>
                                </a>
                                <form method="POST" action="{{ route('backups.supprimer') }}"
                                      onsubmit="return confirm('Supprimer définitivement ce fichier ?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                    <button class="btn btn-sm btn-light border text-danger rounded-3">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="opacity-50">
                                <i class="bi bi-archive fs-1 d-block mb-3 text-muted"></i>
                                <h6 class="text-dark fw-bold">Coffre-fort vide</h6>
                                <p class="small text-muted mb-0">Aucune sauvegarde n'a été trouvée sur le serveur.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection