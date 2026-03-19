@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">👥 Utilisateurs</h4>
        <p class="text-muted mb-0">Gérez les comptes et les rôles</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvel utilisateur
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->nom_complet }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->telephone ?? '—' }}</td>
                    <td>
                        @php
                            $colors = [
                                'admin'       => 'danger',
                                'vendeur'     => 'primary',
                                'magasinier'  => 'success',
                                'comptable'   => 'warning',
                            ];
                        @endphp
                        <span class="badge bg-{{ $colors[$user->role] }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('users.edit', $user) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- Toggle actif -->
                            <form action="{{ route('users.toggle-actif', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-{{ $user->actif ? 'warning' : 'success' }}"
                                        title="{{ $user->actif ? 'Désactiver' : 'Activer' }}">
                                    <i class="bi bi-{{ $user->actif ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            <!-- Supprimer -->
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection