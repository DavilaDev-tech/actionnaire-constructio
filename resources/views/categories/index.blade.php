@extends('layouts.app')
@section('title', 'Catégories')
@section('page-title', 'Gestion des Catégories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Catégories</h4>
        <p class="text-muted mb-0">Gérez les catégories de produits</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle catégorie
    </a>
</div>

<!-- Statistique rapide -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-tags fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $categories->total() }}</div>
                    <div class="small opacity-75">Total catégories</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th class="text-center">Nb Produits</th>
                    <th>Créée le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $categorie)
                <tr>
                    <td>{{ $categorie->id }}</td>
                    <td class="fw-semibold">{{ $categorie->nom }}</td>
                    <td class="text-muted">
                        {{ $categorie->description ?? '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $categorie->produits_count > 0 ? 'success' : 'secondary' }} rounded-pill">
                            {{ $categorie->produits_count }}
                        </span>
                    </td>
                    <td>{{ $categorie->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('categories.edit', $categorie) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $categorie) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer cette catégorie ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Aucune catégorie trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="card-footer">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection