@extends('layouts.app')
@section('title', 'Paiements')
@section('page-title', 'Gestion des Paiements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"> Paiements</h4>
            <p class="text-muted mb-0">Gérez les paiements des factures</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('paiements.rapport') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Rapport
            </a>
    
                <a href="{{ route('export.paiements') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('paiements.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Nouveau paiement
                </a>
            
        </div>
    </div>



    <!-- Statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-receipt fs-1 opacity-75"></i>
                    <div>
                        <div class="fs-3 fw-bold">{{ $totalPaiements }}</div>
                        <div class="small opacity-75">Total paiements</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-cash-stack fs-1 opacity-75"></i>
                    <div>
                        <div class="fs-5 fw-bold">
                            {{ number_format($totalEncaisse, 0, ',', ' ') }} F
                        </div>
                        <div class="small opacity-75">Total encaissé</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-cash fs-1 opacity-75"></i>
                    <div>
                        <div class="fs-5 fw-bold">
                            {{ number_format($parEspeces, 0, ',', ' ') }} F
                        </div>
                        <div class="small opacity-75">En espèces</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body d-flex align-items-center gap-3">
                    <i class="bi bi-phone fs-1 opacity-75"></i>
                    <div>
                        <div class="fs-5 fw-bold">
                            {{ number_format($parMobileMoney, 0, ',', ' ') }} F
                        </div>
                        <div class="small opacity-75">Mobile Money</div>
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
                        <th>Facture</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Enregistré par</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paiements as $paiement)
                        <tr>
                            <td>{{ $paiement->id }}</td>
                            <td class="fw-semibold">
                                {{ $paiement->facture->numero }}
                            </td>
                            <td>{{ $paiement->facture->vente->client->nom }}</td>
                            <td class="fw-bold text-success">
                                {{ number_format($paiement->montant, 0, ',', ' ') }} F
                            </td>
                            <td>
                                <span class="badge bg-{{ $paiement->couleur_mode }}">
                                    {{ $paiement->libelle_mode }}
                                </span>
                            </td>
                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td>{{ $paiement->reference ?? '—' }}</td>
                            <td>{{ $paiement->createdBy->nom_complet }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('paiements.show', $paiement) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('paiements.destroy', $paiement) }}" method="POST"
                                        onsubmit="return confirm('Supprimer ce paiement ?')">
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
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-cash-coin fs-1 d-block mb-2"></i>
                                Aucun paiement enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($paiements->hasPages())
            <div class="card-footer">{{ $paiements->links() }}</div>
        @endif
    </div>
@endsection