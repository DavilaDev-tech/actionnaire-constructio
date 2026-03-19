@extends('layouts.app')
@section('title', 'Factures')
@section('page-title', 'Gestion des Factures')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">🧾 Factures</h4>
        <p class="text-muted mb-0">Toutes les factures générées</p>
    </div>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalFactures }}</div>
                    <div class="small opacity-75">Total factures</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $payees }}</div>
                    <div class="small opacity-75">Payées</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-hourglass fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $nonPayees }}</div>
                    <div class="small opacity-75">Non payées</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash-stack fs-1 opacity-75"></i>
                <div>
                    <div class="fs-4 fw-bold">
                        {{ number_format($montantDu, 0, ',', ' ') }}
                    </div>
                    <div class="small opacity-75">Montant dû (F)</div>
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
                    <th>N° Facture</th>
                    <th>N° Vente</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factures as $facture)
                <tr>
                    <td class="fw-bold">{{ $facture->numero }}</td>
                    <td>{{ $facture->vente->numero_vente }}</td>
                    <td>{{ $facture->vente->client->nom }}</td>
                    <td>{{ $facture->created_at->format('d/m/Y') }}</td>
                    <td class="fw-semibold">
                        {{ number_format($facture->montant, 0, ',', ' ') }} F
                    </td>
                    <td>
                        <span class="badge bg-{{
                            $facture->statut=='payee'    ? 'success' :
                            ($facture->statut=='annulee' ? 'danger'  : 'warning')
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('factures.show', $facture) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('factures.telecharger', $facture) }}"
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i>
                            </a>
                            @if($facture->statut == 'non_payee')
                            <form action="{{ route('factures.marquer-payee', $facture) }}"
                                  method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary"
                                        title="Marquer payée">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                        Aucune facture trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($factures->hasPages())
    <div class="card-footer">{{ $factures->links() }}</div>
    @endif
</div>
@endsection