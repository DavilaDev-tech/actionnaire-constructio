@extends('layouts.app')
@section('title', 'Livraisons')
@section('page-title', 'Gestion des Livraisons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Livraisons</h4>
        <p class="text-muted mb-0">Suivez toutes les livraisons</p>
    </div>
    <a href="{{ route('livraisons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle livraison
    </a>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-truck fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalLivraisons }}</div>
                    <div class="small opacity-75">Total livraisons</div>
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
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-arrow-right-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $enCours }}</div>
                    <div class="small opacity-75">En cours</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $livrees }}</div>
                    <div class="small opacity-75">Livrées</div>
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
                    <th>#</th>
                    <th>N° Vente</th>
                    <th>Client</th>
                    <th>Adresse</th>
                    <th>Date livraison</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($livraisons as $livraison)
                <tr>
                    <td>{{ $livraison->id }}</td>
                    <td class="fw-semibold">
                        {{ $livraison->vente->numero_vente }}
                    </td>
                    <td>{{ $livraison->client->nom }}</td>
                    <td class="text-muted small">
                        {{ Str::limit($livraison->adresse_livraison, 30) }}
                    </td>
                    <td>
                        {{ $livraison->date_livraison
                            ? $livraison->date_livraison->format('d/m/Y')
                            : '—' }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $livraison->couleur_statut }}">
                            {{ ucfirst(str_replace('_', ' ', $livraison->statut)) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('livraisons.show', $livraison) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$livraison->isLivree())
                            <a href="{{ route('livraisons.edit', $livraison) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Changement rapide de statut -->
                            <form action="{{ route('livraisons.statut', $livraison) }}"
                                  method="POST">
                                @csrf @method('PATCH')
                                <select name="statut"
                                        class="form-select form-select-sm"
                                        onchange="this.form.submit()">
                                    <option value="en_attente"
                                        {{ $livraison->statut=='en_attente' ? 'selected':'' }}>
                                        En attente
                                    </option>
                                    <option value="en_cours"
                                        {{ $livraison->statut=='en_cours' ? 'selected':'' }}>
                                        En cours
                                    </option>
                                    <option value="livree">
                                        Livrée ✓
                                    </option>
                                </select>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-truck fs-1 d-block mb-2"></i>
                        Aucune livraison trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($livraisons->hasPages())
    <div class="card-footer">{{ $livraisons->links() }}</div>
    @endif
</div>
@endsection