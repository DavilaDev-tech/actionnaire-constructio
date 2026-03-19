@extends('layouts.app')
@section('title', 'Produits')
@section('page-title', 'Produits & Stock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Produits & Stock</h4>
        <p class="text-muted mb-0">Gérez votre catalogue et votre stock</p>
    </div>
<div class="d-flex gap-2">
    <a href="{{ route('export.produits') }}"
       class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
    </a>
    <a href="{{ route('produits.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau produit
    </a>
</div>
</div>



<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-seam fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalProduits }}</div>
                    <div class="small opacity-75">Total produits</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stockBas }}</div>
                    <div class="small opacity-75">Stock bas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-x-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stockEpuise }}</div>
                    <div class="small opacity-75">Stock épuisé</div>
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
                    <th>Image</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix achat</th>
                    <th>Prix vente</th>
                    <th class="text-center">Stock</th>
                    <th>Unité</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $produit)
                <tr>
                    <td>{{ $produit->id }}</td>
                    <td>
                        @if($produit->image)
                            <img src="{{ asset('storage/' . $produit->image) }}"
                                 width="45" height="45"
                                 class="rounded object-fit-cover">
                        @else
                            <div class="bg-light rounded d-flex align-items-center
                                        justify-content-center"
                                 style="width:45px;height:45px">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $produit->nom }}</div>
                        <small class="text-muted">{{ $produit->fournisseur->nom }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $produit->categorie->nom }}
                        </span>
                    </td>
                    <td>{{ number_format($produit->prix_achat, 0, ',', ' ') }} F</td>
                    <td>{{ number_format($produit->prix_vente, 0, ',', ' ') }} F</td>
                    <td class="text-center">
                        <span class="fw-bold
                            {{ $produit->isStockEpuise() ? 'text-danger' :
                               ($produit->isStockBas()   ? 'text-warning' : 'text-success') }}">
                            {{ $produit->quantite_stock }}
                        </span>
                        <small class="text-muted d-block">/ {{ $produit->seuil_alerte }} min</small>
                    </td>
                    <td>{{ $produit->unite }}</td>
                    <td>
                        @php
                            $statutColor = [
                                'disponible' => 'success',
                                'bas'        => 'warning',
                                'épuisé'     => 'danger',
                            ];
                        @endphp
                        <span class="badge bg-{{ $statutColor[$produit->statut_stock] }}">
                            {{ ucfirst($produit->statut_stock) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('produits.show', $produit) }}"
                               class="btn btn-sm btn-outline-info" title="Détail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('produits.edit', $produit) }}"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('produits.destroy', $produit) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce produit ?')">
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
                    <td colspan="10" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Aucun produit trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produits->hasPages())
    <div class="card-footer">
        {{ $produits->links() }}
    </div>
    @endif
</div>
@endsection