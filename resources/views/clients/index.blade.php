@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Gestion des Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">👥 Clients</h4>
        <p class="text-muted mb-0">Gérez votre portefeuille clients</p>
    </div>
  <div class="d-flex gap-2">
    <a href="{{ route('export.clients') }}"
       class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
    </a>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouveau client
    </a>
</div>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-people fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalClients }}</div>
                    <div class="small opacity-75">Total clients</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-person fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $particuliers }}</div>
                    <div class="small opacity-75">Particuliers</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-building fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $entreprises }}</div>
                    <div class="small opacity-75">Entreprises</div>
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
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Adresse</th>
                    <th class="text-center">Ventes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td class="fw-semibold">{{ $client->nom }}</td>
                    <td>
                        <span class="badge bg-{{ $client->type == 'entreprise' ? 'info' : 'success' }}">
                            {{ ucfirst($client->type) }}
                        </span>
                    </td>
                    <td>{{ $client->telephone ?? '—' }}</td>
                    <td>{{ $client->email ?? '—' }}</td>
                    <td class="text-muted small">
                        {{ Str::limit($client->adresse, 30) ?? '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill">
                            {{ $client->ventes_count }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('clients.show', $client) }}"
                               class="btn btn-sm btn-outline-info" title="Détail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('clients.destroy', $client) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                        Aucun client trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="card-footer">{{ $clients->links() }}</div>
    @endif
</div>
@endsection