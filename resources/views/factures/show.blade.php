@extends('layouts.app')
@section('title', 'Détail Facture')
@section('page-title', 'Détail Facture')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Facture {{ $facture->numero }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('factures.telecharger', $facture) }}"
                       class="btn btn-success btn-sm">
                        <i class="bi bi-download me-1"></i> Télécharger PDF
                    </a>
                    @if($facture->statut == 'non_payee')
                    <form action="{{ route('factures.marquer-payee', $facture) }}"
                          method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-primary btn-sm">
                            <i class="bi bi-check me-1"></i> Marquer payée
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- En-tête facture -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4 class="text-primary fw-bold">Actionnaire Construction</h4>
                        <p class="text-muted mb-0">Vente de Matériaux de Construction</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>FACTURE</h5>
                        <p class="mb-1"><strong>N° :</strong> {{ $facture->numero }}</p>
                        <p class="mb-1">
                            <strong>Date :</strong>
                            {{ $facture->created_at->format('d/m/Y') }}
                        </p>
                        <span class="badge bg-{{
                            $facture->statut=='payee'    ? 'success' :
                            ($facture->statut=='annulee' ? 'danger'  : 'warning')
                        }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $facture->statut)) }}
                        </span>
                    </div>
                </div>

                <hr>

                <!-- Client -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">FACTURÉ À :</h6>
                        <p class="mb-1 fw-bold">{{ $facture->vente->client->nom }}</p>
                        <p class="mb-1">{{ $facture->vente->client->telephone }}</p>
                        <p class="mb-0">{{ $facture->vente->client->adresse }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted">VENTE :</h6>
                        <p class="mb-1">
                            <strong>N° :</strong> {{ $facture->vente->numero_vente }}
                        </p>
                        <p class="mb-1">
                            <strong>Date :</strong>
                            {{ $facture->vente->date_vente->format('d/m/Y') }}
                        </p>
                        <p class="mb-0">
                            <strong>Vendeur :</strong>
                            {{ $facture->vente->user->nom_complet }}
                        </p>
                    </div>
                </div>

                <!-- Tableau produits -->
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->vente->details as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $detail->produit->nom }}</td>
                            <td class="text-center">
                                {{ $detail->quantite }} {{ $detail->produit->unite }}
                            </td>
                            <td class="text-end">
                                {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($detail->sous_total, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <td colspan="4" class="text-end fw-bold fs-5">TOTAL</td>
                            <td class="text-end fw-bold fs-5">
                                {{ number_format($facture->montant, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                    </tfoot>
                </table>

                @if($facture->vente->note)
                <div class="alert alert-light border mt-3">
                    <strong>Note :</strong> {{ $facture->vente->note }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<a href="{{ route('factures.index') }}" class="btn btn-outline-secondary mt-3">
    <i class="bi bi-arrow-left me-1"></i> Retour
</a>
@endsection