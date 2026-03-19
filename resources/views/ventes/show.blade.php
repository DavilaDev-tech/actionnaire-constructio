@extends('layouts.app')
@section('title', 'Détail Vente')
@section('page-title', 'Détail Vente')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Infos vente -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-cart3 me-2"></i>{{ $vente->numero_vente }}
                </h5>
                @php
                    $statutColors = [
                        'en_attente' => 'warning',
                        'confirmee'  => 'primary',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                    ];
                @endphp
                <span class="badge bg-{{ $statutColors[$vente->statut] }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <small class="text-muted">Client</small>
                        <div class="fw-semibold">{{ $vente->client->nom }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Vendeur</small>
                        <div class="fw-semibold">{{ $vente->user->nom_complet }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Date</small>
                        <div class="fw-semibold">
                            {{ $vente->date_vente->format('d/m/Y') }}
                        </div>
                    </div>
                    @if($vente->note)
                    <div class="col-12">
                        <small class="text-muted">Note</small>
                        <div>{{ $vente->note }}</div>
                    </div>
                    @endif
                </div>

                <!-- Lignes produits -->
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vente->details as $detail)
                        <tr>
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
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL</td>
                            <td class="text-end fw-bold fs-5">
                                {{ number_format($vente->montant_total, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="col-md-4">
        <!-- Changer statut -->
        @if(!$vente->isAnnulee() && !$vente->isLivree())
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header">
                <h6 class="mb-0">Changer le statut</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('ventes.statut', $vente) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="statut" class="form-select mb-3">
                        <option value="en_attente"
                            {{ $vente->statut=='en_attente' ? 'selected':'' }}>
                            En attente
                        </option>
                        <option value="confirmee"
                            {{ $vente->statut=='confirmee' ? 'selected':'' }}>
                            Confirmée
                        </option>
                        <option value="livree"
                            {{ $vente->statut=='livree' ? 'selected':'' }}>
                            Livrée
                        </option>
                        <option value="annulee">Annulée</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check me-1"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Facture -->
        @if($vente->facture)
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>Facture
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-1">
                    <strong>N°</strong> {{ $vente->facture->numero }}
                </p>
                <p class="mb-1">
                    <strong>Montant :</strong>
                    {{ number_format($vente->facture->montant, 0, ',', ' ') }} F CFA
                </p>
                <p class="mb-3">
                    <strong>Statut :</strong>
                    <span class="badge bg-{{
                        $vente->facture->statut=='payee'   ? 'success' :
                        ($vente->facture->statut=='annulee'? 'danger'  : 'warning')
                    }}">
                        {{ ucfirst(str_replace('_', ' ', $vente->facture->statut)) }}
                    </span>
                </p>
                <a href="{{ route('factures.telecharger', $vente->facture) }}"
                   class="btn btn-success w-100 mb-2">
                    <i class="bi bi-download me-1"></i> Télécharger PDF
                </a>
                @if($vente->facture->statut == 'non_payee')
                <form action="{{ route('factures.marquer-payee', $vente->facture) }}"
                      method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-success w-100">
                        <i class="bi bi-check-circle me-1"></i> Marquer payée
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary mt-2">
    <i class="bi bi-arrow-left me-1"></i> Retour
</a>
@endsection