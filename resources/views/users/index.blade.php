@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<style>
    /* Design System Global */
    .btn-orange { background-color: #FF6B00; border-color: #FF6B00; color: white; border-radius: 8px; }
    .btn-orange:hover { background-color: #e66000; border-color: #e66000; color: white; }
    
    .kpi-card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    
    .search-container { position: relative; width: 300px; }
    .search-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); color: #6c757d; pointer-events: none; z-index: 5; }
    .search-input { border-radius: 10px; border: 1px solid #e2e8f0; background-color: #f8fafc; padding-left: 40px !important; height: 40px; }
    
    .table-custom thead { background-color: #1e293b; color: white; }
    .table-custom th { font-weight: 500; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 15px; border: none; }
    .table-custom td { vertical-align: middle; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; }
    
    /* Avatars Utilisateurs */
    .avatar-user { width: 40px; height: 40px; border-radius: 10px; background-color: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    
    /* Badges de Rôles Spécifiques */
    .badge-role-admin { background-color: #fee2e2; color: #991b1b; }
    .badge-role-vendeur { background-color: #e0e7ff; color: #4338ca; }
    .badge-role-magasinier { background-color: #dcfce7; color: #15803d; }
    .badge-role-comptable { background-color: #fef9c3; color: #854d0e; }

    .status-indicator { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

    .action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #e2e8f0; color: #64748b; background: white; text-decoration: none; transition: all 0.2s; }
    .action-btn:hover { background-color: #f8fafc; border-color: #cbd5e1; }
</style>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold mb-1">Utilisateurs</h2>
        <p class="text-muted mb-0">Contrôlez les accès et les permissions de votre équipe</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-orange shadow-sm px-4 py-2">
            <i class="bi bi-person-plus me-1"></i> Nouvel utilisateur
        </a>
    </div>
</div>

<div class="card kpi-card overflow-hidden">
    <div class="card-body p-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="userSearch" class="form-control search-input" placeholder="Chercher un nom ou email...">
            </div>
            <div class="text-muted small"><b id="userCount">{{ $users->count() }}</b> comptes enregistrés</div>
        </div>
        
        <table class="table table-hover table-custom mb-0" id="userTable">
            <thead>
                <tr>
                    <th>UTILISATEUR</th>
                    <th>CONTACT</th>
                    <th>RÔLE</th>
                    <th>STATUT</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="user-row">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-user me-3 shadow-sm text-uppercase">
                                {{ substr($user->nom_complet, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark user-name">{{ $user->nom_complet }}</div>
                                <div class="text-muted small">ID #{{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-medium text-dark"><i class="bi bi-envelope me-1 text-muted"></i> {{ $user->email }}</div>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $user->telephone ?? '—' }}</div>
                    </td>
                    <td>
                        @php
                            $roleClass = 'badge-role-' . strtolower($user->role);
                        @endphp
                        <span class="badge {{ $roleClass }} px-2 py-1">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->actif)
                            <span class="text-success small fw-medium">
                                <span class="status-indicator bg-success"></span> En ligne
                            </span>
                        @else
                            <span class="text-muted small fw-medium">
                                <span class="status-indicator bg-secondary"></span> Inactif
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('users.edit', $user) }}" class="action-btn" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('users.toggle-actif', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="action-btn {{ $user->actif ? 'text-warning' : 'text-success' }}" 
                                        title="{{ $user->actif ? 'Suspendre' : 'Réactiver' }}">
                                    <i class="bi bi-{{ $user->actif ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>

                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn text-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Aucun utilisateur trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div id="paginationArea" class="card-footer bg-white border-0 py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('userSearch');
        const rows = document.querySelectorAll('.user-row');
        const userCount = document.getElementById('userCount');
        const pagination = document.getElementById('paginationArea');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            let count = 0;

            rows.forEach(row => {
                const name = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.innerText.toLowerCase(); // Recherche aussi dans l'email
                if (name.includes(query) || email.includes(query)) {
                    row.style.display = "";
                    count++;
                } else {
                    row.style.display = "none";
                }
            });

            userCount.textContent = count;
            if(pagination) pagination.style.display = query.length > 0 ? "none" : "block";
        });
    });
</script>
@endsection