@extends('layouts.app')
@section('title', 'Produits')
@section('page-title', 'Produits & Stock')

@section('content')
<style>
    /* Design System */
    .btn-orange { background-color: #FF6B00; border-color: #FF6B00; color: white; border-radius: 8px; }
    .btn-orange:hover { background-color: #e66000; border-color: #e66000; color: white; }
    
    .kpi-card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .kpi-icon { width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    
    .search-container { position: relative; width: 300px; }
    .search-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); color: #6c757d; pointer-events: none; z-index: 5; }
    .search-input { border-radius: 10px; border: 1px solid #e2e8f0; background-color: #f8fafc; padding-left: 40px !important; height: 40px; }
    
    .table-custom thead { background-color: #1e293b; color: white; }
    .table-custom th { font-weight: 500; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 15px; border: none; }
    .table-custom td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    
    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: none; }
    .badge-soft-warning { background-color: #fef9c3; color: #854d0e; border: none; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; border: none; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; border: none; }

    .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #e2e8f0; color: #64748b; background: white; text-decoration: none; transition: all 0.2s; }
    .action-btn:hover { background-color: #f8fafc; color: #334155; border-color: #cbd5e1; }
    .btn-view:hover { color: #0ea5e9; border-color: #0ea5e9; }
    
    .product-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background-color: #f8fafc; border: 1px solid #f1f5f9; }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-1">Produits & Stock</h2>
        <p class="text-muted mb-0">Gestion de votre catalogue et suivi des niveaux de stock</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('export.produits') }}" class="btn btn-light border shadow-sm px-3">
            <i class="bi bi-file-earmark-excel me-1 text-success"></i> Exporter
        </a>
        <a href="{{ route('produits.create') }}" class="btn btn-orange shadow-sm px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i> Nouveau produit
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body d-flex align-items-center">
                <div class="kpi-icon bg-light text-primary me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="fw-bold mb-0">{{ $totalProduits }}</h3>
                    <p class="text-muted small mb-0">Total produits</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body d-flex align-items-center">
                <div class="kpi-icon bg-light text-warning me-3">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="fw-bold mb-0 text-warning">{{ $stockBas }}</h3>
                    <p class="text-muted small mb-0">Stock bas</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body d-flex align-items-center">
                <div class="kpi-icon bg-light text-danger me-3">
                    <i class="bi bi-x-circle fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h3 class="fw-bold mb-0 text-danger">{{ $stockEpuise }}</h3>
                    <p class="text-muted small mb-0">Stock épuisé</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card kpi-card overflow-hidden">
    <div class="card-body p-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="productSearch" class="form-control search-input" placeholder="Rechercher un produit...">
            </div>
            <div class="text-muted small"><b id="countDisplay">{{ $produits->count() }}</b> résultats</div>
        </div>
        
        <table class="table table-hover table-custom mb-0" id="productTable">
            <thead>
                <tr>
                    <th>PRODUIT</th>
                    <th>CATÉGORIE</th>
                    <th>PRIX VENTE</th>
                    <th class="text-center">STOCK</th>
                    <th>UNITÉ</th>
                    <th>STATUT</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $produit)
                <tr class="product-row">
                    <td>
                        <div class="d-flex align-items-center">
                            @if($produit->image)
                                <img src="{{ asset('storage/' . $produit->image) }}" class="product-img me-3">
                            @else
                                <div class="product-img me-3 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold text-dark product-name">{{ $produit->nom }}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">{{ $produit->fournisseur->nom ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-soft-secondary">{{ $produit->categorie->nom }}</span></td>
                    <td class="fw-bold text-dark">{{ number_format($produit->prix_vente, 0, ',', ' ') }} F</td>
                    <td class="text-center">
                        <div class="fw-bold {{ $produit->isStockEpuise() ? 'text-danger' : ($produit->isStockBas() ? 'text-warning' : 'text-success') }}">
                            {{ $produit->quantite_stock }}
                        </div>
                        <div class="text-muted small" style="font-size: 0.65rem;">Min: {{ $produit->seuil_alerte }}</div>
                    </td>
                    <td class="text-muted small">{{ $produit->unite }}</td>
                    <td>
                        @php
                            $colors = ['disponible' => 'success', 'bas' => 'warning', 'épuisé' => 'danger'];
                            $c = $colors[$produit->statut_stock] ?? 'secondary';
                        @endphp
                        <span class="badge badge-soft-{{ $c }} small text-uppercase">
                            {{ $produit->statut_stock }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('produits.show', $produit) }}" class="action-btn btn-view" title="Détails">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('produits.edit', $produit) }}" class="action-btn" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5 text-muted">Aucun produit trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produits->hasPages())
    <div id="paginationBlock" class="card-footer bg-white border-0 py-3">
        {{ $produits->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('productSearch');
        const rows = document.querySelectorAll('.product-row');
        const countDisplay = document.getElementById('countDisplay');
        const pagination = document.getElementById('paginationBlock');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let count = 0;

            rows.forEach(row => {
                const text = row.querySelector('.product-name').textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = "";
                    count++;
                } else {
                    row.style.display = "none";
                }
            });

            countDisplay.textContent = count;
            if(pagination) pagination.style.display = query.length > 0 ? "none" : "block";
        });
    });
</script>
@endsection