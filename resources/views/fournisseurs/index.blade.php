@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('page-title', 'Gestion des Fournisseurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Fournisseurs</h4>
        <p class="text-muted mb-0">Gérez vos fournisseurs de matériaux</p>
    </div>
    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau fournisseur
    </a>
</div>

<!-- Statistique -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-truck fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalFournisseurs }}</div>
                    <div class="small opacity-75">Total fournisseurs</div>
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
                    <th>Nom</th>
                    <th>Contact</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th class="text-center">Produits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseurs as $fournisseur)
                <tr>
                    <td>{{ $fournisseur->id }}</td>
                    <td class="fw-semibold">{{ $fournisseur->nom }}</td>
                    <td>{{ $fournisseur->contact_personne ?? '—' }}</td>
                    <td>{{ $fournisseur->telephone ?? '—' }}</td>
                    <td>{{ $fournisseur->email ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge bg-success rounded-pill">
                            {{ $fournisseur->produits_count }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('fournisseurs.show', $fournisseur) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('fournisseurs.edit', $fournisseur) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('fournisseurs.destroy', $fournisseur) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce fournisseur ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-truck fs-1 d-block mb-2"></i>
                        Aucun fournisseur trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fournisseurs->hasPages())
    <div class="card-footer">{{ $fournisseurs->links() }}</div>
    @endif
</div>
@endsection