@extends('layouts.app')
@section('title', 'Détail Client')
@section('page-title', 'Détail Client')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person me-2"></i>{{ $client->nom }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Type</td>
                        <td>
                            <span class="badge bg-{{ $client->type=='entreprise' ? 'info':'success' }}">
                                {{ ucfirst($client->type) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Téléphone</td>
                        <td>{{ $client->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $client->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Adresse</td>
                        <td>{{ $client->adresse ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total achats</td>
                        <td class="fw-bold text-success">
                            {{ number_format($client->total_achats, 0, ',', ' ') }} F CFA
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Dernières ventes -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-cart3 me-2"></i>
                    Dernières ventes
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Vente</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->ventes as $vente)
                        <tr>
                            <td>{{ $vente->numero_vente }}</td>
                            <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                            <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                            <td>
                                @php
                                    $colors = [
                                        'en_attente' => 'warning',
                                        'confirmee'  => 'primary',
                                        'livree'     => 'success',
                                        'annulee'    => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $colors[$vente->statut] }}">
                                    {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Aucune vente pour ce client.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i> Modifier
    </a>
    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection