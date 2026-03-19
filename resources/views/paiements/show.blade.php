@extends('layouts.app')
@section('title', 'Détail Paiement')
@section('page-title', 'Détail Paiement')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-cash-coin me-2"></i>
                    Paiement #{{ $paiement->id }}
                </h5>
                <span class="badge bg-{{ $paiement->couleur_mode }} fs-6">
                    {{ $paiement->libelle_mode }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <div class="text-muted small mb-1">Montant payé</div>
                                <div class="fs-2 fw-bold text-success">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} F CFA
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <div class="text-muted small mb-1">
                                    Restant sur la facture
                                </div>
                                <div class="fs-2 fw-bold
                                    {{ $paiement->facture->montant_restant > 0
                                        ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($paiement->facture->montant_restant, 0, ',', ' ') }} F CFA
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Facture</td>
                                <td class="fw-semibold">
                                    {{ $paiement->facture->numero }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Client</td>
                                <td>
                                    {{ $paiement->facture->vente->client->nom }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date paiement</td>
                                <td>
                                    {{ $paiement->date_paiement->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Mode</td>
                                <td>
                                    <span class="badge bg-{{ $paiement->couleur_mode }}">
                                        {{ $paiement->libelle_mode }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Référence</td>
                                <td>{{ $paiement->reference ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Note</td>
                                <td>{{ $paiement->note ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Enregistré par</td>
                                <td>{{ $paiement->createdBy->nom_complet }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Enregistré le</td>
                                <td>
                                    {{ $paiement->created_at->format('d/m/Y à H:i') }}
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection