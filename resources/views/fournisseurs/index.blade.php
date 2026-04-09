@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('page-title', 'Gestion des Fournisseurs')

@section('content')
<style>
    /* Design System Global */
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
    
    .avatar-vendor { width: 40px; height: 40px; border-radius: 50%; background-color: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem; }
    
    .badge-soft-success { background-color: #dcfce7; color: #15803d; border: none; }
    
    .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #e2e8f0; color: #64748b; background: white; text-decoration: none; transition: all 0.2s; }
    .action-btn:hover { background-color: #f8fafc; color: #334155; border-color: #cbd5e1; }
    .btn-view:hover { color: #0ea5e9; border-color: #0ea5e9; }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-1">Fournisseurs</h2>
        <p class="text-muted mb-0">Gérez votre réseau de fournisseurs et partenaires</p>
    </div>
    <div>
        <a href="{{ route('fournisseurs.create') }}" class="btn btn-orange shadow-sm px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i> Nouveau fournisseur
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card kpi-card">
            <div class="card-body d-flex align-items-center">
                <div class="kpi-icon bg-light text-primary me-3">
                    <i class="bi bi-truck fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="fw-bold mb-0">{{ $totalFournisseurs }}</h3>
                        <span class="badge rounded-pill bg-success text-white small">Actifs</span>
                    </div>
                    <p class="text-muted small mb-0">Total fournisseurs</p>
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
                <input type="text" id="vendorSearch" class="form-control search-input" placeholder="Rechercher un fournisseur...">
            </div>
            <div class="text-muted small"><b id="vendorCount">{{ $fournisseurs->count() }}</b> résultats</div>
        </div>
        
        <table class="table table-hover table-custom mb-0" id="vendorTable">
            <thead>
                <tr>
                    <th>FOURNISSEUR</th>
                    <th>CONTACT</th>
                    <th>TÉLÉPHONE / EMAIL</th>
                    <th class="text-center">PRODUITS</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseurs as $fournisseur)
                <tr class="vendor-row">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-vendor me-3">
                                {{ strtoupper(substr($fournisseur->nom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark vendor-name">{{ $fournisseur->nom }}</div>
                                <div class="text-muted small">ID #{{ $fournisseur->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-dark fw-medium">{{ $fournisseur->contact_personne ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-telephone me-1 text-muted"></i> {{ $fournisseur->telephone ?? '—' }}</div>
                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $fournisseur->email ?? '—' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-soft-success px-3 py-2 rounded-pill">
                            {{ $fournisseur->produits_count }} produits
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="action-btn btn-view" title="Voir détails">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="action-btn" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                        Aucun fournisseur enregistré.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fournisseurs->hasPages())
    <div id="paginationArea" class="card-footer bg-white border-0 py-3">
        {{ $fournisseurs->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('vendorSearch');
        const rows = document.querySelectorAll('.vendor-row');
        const vendorCount = document.getElementById('vendorCount');
        const paginationArea = document.getElementById('paginationArea');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.querySelector('.vendor-name').textContent.toLowerCase();
                if (name.includes(query)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            vendorCount.textContent = visibleCount;
            if(paginationArea) paginationArea.style.display = query.length > 0 ? "none" : "block";
        });
    });
</script>
@endsection