@extends('layouts.app')
@section('title', 'Modifier Catégorie')
@section('page-title', 'Modifier Catégorie')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>Modifier : {{ $categorie->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.update', $categorie) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nom de la catégorie <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="nom"
                               class="form-control @error('nom') is-invalid @enderror"
                               value="{{ old('nom', $categorie->nom) }}"
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3">{{ old('description', $categorie->description) }}</textarea>
                    </div>

                    <!-- Info produits liés -->
                    @if($categorie->nombre_produits > 0)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Cette catégorie contient
                        <strong>{{ $categorie->nombre_produits }} produit(s)</strong>.
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection