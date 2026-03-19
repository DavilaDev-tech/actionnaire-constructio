@extends('layouts.app')
@section('title', 'Modifier Produit')
@section('page-title', 'Modifier Produit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>Modifier : {{ $produit->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produits.update', $produit) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $produit->nom) }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unité <span class="text-danger">*</span></label>
                            <select name="unite" class="form-select" required>
                                @foreach(['sac','barre','mètre','kg','litre','unité','rouleau','tonne'] as $u)
                                    <option value="{{ $u }}"
                                            {{ old('unite',$produit->unite)==$u ? 'selected':'' }}>
                                        {{ ucfirst($u) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                            <select name="categorie_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            {{ old('categorie_id',$produit->categorie_id)==$cat->id ? 'selected':'' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur <span class="text-danger">*</span></label>
                            <select name="fournisseur_id" class="form-select" required>
                                @foreach($fournisseurs as $four)
                                    <option value="{{ $four->id }}"
                                            {{ old('fournisseur_id',$produit->fournisseur_id)==$four->id ? 'selected':'' }}>
                                        {{ $four->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prix d'achat <span class="text-danger">*</span></label>
                            <input type="number" name="prix_achat" step="0.01"
                                   class="form-control"
                                   value="{{ old('prix_achat', $produit->prix_achat) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prix de vente <span class="text-danger">*</span></label>
                            <input type="number" name="prix_vente" step="0.01"
                                   class="form-control"
                                   value="{{ old('prix_vente', $produit->prix_vente) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Stock</label>
                            <input type="number" name="quantite_stock"
                                   class="form-control"
                                   value="{{ old('quantite_stock', $produit->quantite_stock) }}" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Seuil alerte</label>
                            <input type="number" name="seuil_alerte"
                                   class="form-control"
                                   value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $produit->description) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Image</label>
                            @if($produit->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $produit->image) }}"
                                         height="60" class="rounded">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Laisser vide pour garder l'image actuelle</small>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection