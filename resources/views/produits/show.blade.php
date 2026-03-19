@extends('layouts.app')
@section('title', 'Détail Produit')
@section('page-title', 'Détail Produit')

@section('content')
<div class="row">
    <!-- Infos produit -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>{{ $produit->nom }}
                </h5>
                <span class="badge bg-{{ ['disponible'=>'success','bas'=>'warning','épuisé'=>'danger'][$produit->statut_stock] }} fs-6">
                    {{ ucfirst($produit->statut_stock) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Catégorie</small>
                        <div class="fw-semibold">{{ $produit->categorie->nom }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Fournisseur</small>
                        <div class="fw-semibold">{{ $produit->fournisseur->nom }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Prix d'achat</small>
                        <div class="fw-semibold text-danger">
                            {{ number_format($produit->prix_achat, 0, ',', ' ') }} F CFA
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Prix de vente</small>
                        <div class="fw-semibold text-success">
                            {{ number_format($produit->prix_vente, 0, ',', ' ') }} F CFA
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Marge bénéficiaire</small>
                        <div class="fw-semibold text-primary">
                            {{ number_format($produit->prix_vente - $produit->prix_achat, 0, ',', ' ') }} F CFA
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Description</small>
                        <div>{{ $produit->description ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock + Ajustement -->
    <div class="col-md-4">
        <!-- Carte stock -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
                <div class="fs-1 fw-bold
                    {{ $produit->isStockEpuise() ? 'text-danger' :
                       ($produit->isStockBas()   ? 'text-warning' : 'text-success') }}">
                    {{ $produit->quantite_stock }}
                </div>
                <div class="text-muted">{{ $produit->unite }}(s) en stock</div>
                <hr>
                <small class="text-muted">
                    Seuil d'alerte : <strong>{{ $produit->seuil_alerte }}</strong>
                </small>
            </div>
        </div>

        <!-- Ajustement stock -->
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-sliders me-2"></i>Ajuster le stock
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('produits.ajuster-stock', $produit) }}"
                      method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Quantité</label>
                        <input type="number" name="quantite"
                               class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opération</label>
                        <select name="operation" class="form-select">
                            <option value="ajouter">➕ Ajouter au stock</option>
                            <option value="retirer">➖ Retirer du stock</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check me-1"></i> Appliquer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('produits.edit', $produit) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i> Modifier
    </a>
    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection