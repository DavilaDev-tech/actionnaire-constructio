@extends('layouts.app')
@section('title', 'Nouveau Produit')
@section('page-title', 'Nouveau Produit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Créer un produit
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produits.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">

                        <!-- Nom -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Nom du produit <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}"
                                   placeholder="Ex: Ciment Portland 50kg"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unité -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Unité <span class="text-danger">*</span>
                            </label>
                            <select name="unite" class="form-select" required>
                                <option value="sac"    {{ old('unite')=='sac'    ?'selected':'' }}>Sac</option>
                                <option value="barre"  {{ old('unite')=='barre'  ?'selected':'' }}>Barre</option>
                                <option value="mètre"  {{ old('unite')=='mètre'  ?'selected':'' }}>Mètre</option>
                                <option value="kg"     {{ old('unite')=='kg'     ?'selected':'' }}>Kg</option>
                                <option value="litre"  {{ old('unite')=='litre'  ?'selected':'' }}>Litre</option>
                                <option value="unité"  {{ old('unite')=='unité'  ?'selected':'' }}>Unité</option>
                                <option value="rouleau"{{ old('unite')=='rouleau'?'selected':'' }}>Rouleau</option>
                                <option value="tonne"  {{ old('unite')=='tonne'  ?'selected':'' }}>Tonne</option>
                            </select>
                        </div>

                        <!-- Catégorie -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Catégorie <span class="text-danger">*</span>
                            </label>
                            <select name="categorie_id"
                                    class="form-select @error('categorie_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Choisir --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                            {{ old('categorie_id')==$cat->id ? 'selected':'' }}>
                                        {{ $cat->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorie_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fournisseur -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Fournisseur <span class="text-danger">*</span>
                            </label>
                            <select name="fournisseur_id"
                                    class="form-select @error('fournisseur_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Choisir --</option>
                                @foreach($fournisseurs as $four)
                                    <option value="{{ $four->id }}"
                                            {{ old('fournisseur_id')==$four->id ? 'selected':'' }}>
                                        {{ $four->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fournisseur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prix achat -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Prix d'achat (F CFA) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="prix_achat" step="0.01"
                                   class="form-control @error('prix_achat') is-invalid @enderror"
                                   value="{{ old('prix_achat') }}" min="0" required>
                            @error('prix_achat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prix vente -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Prix de vente (F CFA) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="prix_vente" step="0.01"
                                   class="form-control @error('prix_vente') is-invalid @enderror"
                                   value="{{ old('prix_vente') }}" min="0" required>
                            @error('prix_vente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantité stock -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                Stock initial <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantite_stock"
                                   class="form-control"
                                   value="{{ old('quantite_stock', 0) }}" min="0" required>
                        </div>

                        <!-- Seuil alerte -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                Seuil alerte <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="seuil_alerte"
                                   class="form-control"
                                   value="{{ old('seuil_alerte', 5) }}" min="0" required>
                        </div>

                        <!-- Description -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control"
                                      rows="3"
                                      placeholder="Description optionnelle...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Image -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Image</label>
                            <input type="file" name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('produits.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection