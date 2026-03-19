@extends('layouts.app')
@section('title', 'Approvisionnements')
@section('page-title', 'Gestion des Approvisionnements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Approvisionnements</h4>
        <p class="text-muted mb-0">Gérez les achats auprès des fournisseurs</p>
    </div>
    <a href="{{ route('approvisionnements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvel approvisionnement
    </a>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-arrow-down-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalAppros }}</div>
                    <div class="small opacity-75">Total appros</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack fs-1 opacity-75"></i>
                <div>
                    <div class="fs-5 fw-bold">
                        {{ number_format($totalDepenses, 0, ',', ' ') }} F
                    </div>
                    <div class="small opacity-75">Total dépenses</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-hourglass fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $enAttente }}</div>
                    <div class="small opacity-75">En attente</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $recus }}</div>
                    <div class="small opacity-75">Reçus</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>N° Appro</th>
                    <th>Fournisseur</th>
                    <th>Responsable</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appros as $appro)
                <tr>
                    <td class="fw-bold">{{ $appro->numero }}</td>
                    <td>{{ $appro->fournisseur->nom }}</td>
                    <td>{{ $appro->user->nom_complet }}</td>
                    <td>{{ $appro->date_appro->format('d/m/Y') }}</td>
                    <td class="fw-semibold">
                        {{ number_format($appro->montant_total, 0, ',', ' ') }} F
                    </td>
                    <td>
                        <span class="badge bg-{{ $appro->couleur_statut }}">
                            {{ ucfirst($appro->statut) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('approvisionnements.show', $appro) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$appro->isRecu() && !$appro->isAnnule())
                            <form action="{{ route('approvisionnements.statut', $appro) }}"
                                  method="POST">
                                @csrf @method('PATCH')
                                <select name="statut"
                                        class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                    <option value="en_attente"
                                        {{ $appro->statut=='en_attente'?'selected':'' }}>
                                        En attente
                                    </option>
                                    <option value="recu">✅ Marquer reçu</option>
                                    <option value="annule">❌ Annuler</option>
                                </select>
                            </form>
                            <form action="{{ route('approvisionnements.destroy', $appro) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer cet approvisionnement ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Aucun approvisionnement trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($appros->hasPages())
    <div class="card-footer">{{ $appros->links() }}</div>
    @endif
</div>
@endsection