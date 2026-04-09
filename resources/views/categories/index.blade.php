@extends('layouts.app')
@section('title', 'Catégories')
@section('page-title', 'Gestion des Catégories')

@section('content')
<style>
    /* Styles personnalisés pour correspondre à la capture d'écran */
    .btn-orange { background-color: #FF6B00; border-color: #FF6B00; color: white; border-radius: 8px; }
    .btn-orange:hover { background-color: #e66000; border-color: #e66000; color: white; }
    
    .kpi-card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .kpi-icon { width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    
    .search-container { position: relative; width: 300px; }
    .search-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); color: #6c757d; pointer-events: none; z-index: 5; }
    .search-input { border-radius: 10px; border: 1px solid #e2e8f0; background-color: #f8fafc; padding-left: 40px !important; height: 40px; }
    
    .table-custom thead { background-color: #1e293b; color: white; }
    .table-custom th { font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em; padding: 15px; border: none; }
    .table-custom td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f5f9; }
    
    .avatar-sm { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    .badge-soft-primary { background-color: #e0e7ff; color: #4338ca; }
    
    .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #e2e8f0; color: #64748b; background: white; }
    .action-btn:hover { background-color: #f8fafc; }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-1">Catégories</h2>
        <p class="text-muted mb-0">{{ $categories->total() }} catégories enregistrées</p>
    </div>
    <div>
        <a href="{{ route('categories.create') }}" class="btn btn-orange shadow-sm px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i> Nouvelle catégorie
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body d-flex align-items-center">
                <div class="kpi-icon bg-light text-warning me-3">
                    <i class="bi bi-tags fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="fw-bold mb-0">{{ $categories->total() }}</h3>
                        <span class="badge rounded-pill bg-success text-white small">Actifs</span>
                    </div>
                    <p class="text-muted small mb-0">Total catégories</p>
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
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Rechercher une catégorie...">
            </div>
            <div class="text-muted small"><b id="resultCount">{{ $categories->count() }}</b> résultats affichés</div>
        </div>
        
        <table class="table table-hover table-custom mb-0" id="categoriesTable">
            <thead>
                <tr>
                    <th>CATÉGORIE</th>
                    <th>DESCRIPTION</th>
                    <th class="text-center">PRODUITS</th>
                    <th>DATE CRÉATION</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $categorie)
                <tr class="category-row">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3 bg-light text-primary">
                                {{ strtoupper(substr($categorie->nom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark category-name">{{ $categorie->nom }}</div>
                                <div class="text-muted small">ID #{{ $categorie->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">
                        {{ Str::limit($categorie->description ?? 'Aucune description', 40) }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill">
                            {{ $categorie->produits_count }}
                        </span>
                    </td>
                    <td>{{ $categorie->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('categories.edit', $categorie) }}" class="action-btn" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $categorie) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="noResults">
                    <td colspan="5" class="text-center py-5 text-muted">
                        Aucune catégorie trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div id="paginationArea" class="card-footer bg-white border-0 py-3">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('.category-row');
        const resultCount = document.getElementById('resultCount');
        const paginationArea = document.getElementById('paginationArea');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const name = row.querySelector('.category-name').textContent.toLowerCase();
                if (name.includes(filter)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // Mise à jour du compteur
            resultCount.textContent = visibleCount;

            // Masquer la pagination si on recherche (car le filtrage est local)
            if (filter.length > 0) {
                if(paginationArea) paginationArea.style.display = "none";
            } else {
                if(paginationArea) paginationArea.style.display = "block";
            }
        });
    });
</script>
@endsection