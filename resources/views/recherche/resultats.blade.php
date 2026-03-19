@extends('layouts.app')
@section('title', 'Résultats de recherche')
@section('page-title', 'Résultats de recherche')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1">
            🔍 Résultats pour
            <span class="text-primary">"{{ $query }}"</span>
        </h4>
        <p class="text-muted mb-0">
            {{ $totalResultats }} résultat(s) trouvé(s)
        </p>
    </div>
</div>

@if($totalResultats === 0)
<div class="card shadow-sm border-0">
    <div class="card-body text-center py-5">
        <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
        <h5 class="text-muted">Aucun résultat trouvé</h5>
        <p class="text-muted">
            Aucun résultat pour <strong>"{{ $query }}"</strong>.
            Essayez avec d'autres mots-clés.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house me-1"></i> Retour au dashboard
        </a>
    </div>
</div>
@endif

<div class="row g-4">

    {{-- ── Clients ── --}}
    @if($clients->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-people text-primary me-2"></i>
                    Clients
                    <span class="badge bg-primary ms-1">{{ $clients->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td class="fw-semibold">{{ $client->nom }}</td>
                            <td>
                                <span class="badge bg-{{ $client->type == 'entreprise' ? 'primary' : 'success' }}">
                                    {{ ucfirst($client->type) }}
                                </span>
                            </td>
                            <td>{{ $client->telephone ?? '—' }}</td>
                            <td>{{ $client->email ?? '—' }}</td>
                            <td>
                                <a href="{{ route('clients.show', $client) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Produits ── --}}
    @if($produits->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-box-seam text-success me-2"></i>
                    Produits
                    <span class="badge bg-success ms-1">{{ $produits->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix vente</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produits as $produit)
                        <tr>
                            <td class="fw-semibold">{{ $produit->nom }}</td>
                            <td>{{ $produit->categorie->nom }}</td>
                            <td>
                                {{ number_format($produit->prix_vente, 0, ',', ' ') }} F
                            </td>
                            <td>
                                <span class="badge bg-{{
                                    $produit->quantite_stock == 0 ? 'danger' :
                                    ($produit->quantite_stock <= $produit->seuil_alerte
                                        ? 'warning' : 'success')
                                }}">
                                    {{ $produit->quantite_stock }} {{ $produit->unite }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('produits.show', $produit) }}"
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Ventes ── --}}
    @if($ventes->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-cart3 text-warning me-2"></i>
                    Ventes
                    <span class="badge bg-warning text-dark ms-1">
                        {{ $ventes->count() }}
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Vente</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventes as $vente)
                        @php
                            $colors = [
                                'en_attente' => 'warning',
                                'confirmee'  => 'primary',
                                'livree'     => 'success',
                                'annulee'    => 'danger',
                            ];
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $vente->numero_vente }}</td>
                            <td>{{ $vente->client->nom }}</td>
                            <td>
                                {{ number_format($vente->montant_total, 0, ',', ' ') }} F
                            </td>
                            <td>
                                <span class="badge bg-{{ $colors[$vente->statut] }}">
                                    {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('ventes.show', $vente) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Factures ── --}}
    @if($factures->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-receipt text-info me-2"></i>
                    Factures
                    <span class="badge bg-info ms-1">{{ $factures->count() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factures as $facture)
                        <tr>
                            <td class="fw-semibold">{{ $facture->numero }}</td>
                            <td>{{ $facture->vente->client->nom }}</td>
                            <td>
                                {{ number_format($facture->montant, 0, ',', ' ') }} F
                            </td>
                            <td>
                                <span class="badge bg-{{
                                    $facture->statut == 'payee' ? 'success' :
                                    ($facture->statut == 'annulee' ? 'danger' : 'warning')
                                }}">
                                    {{ ucfirst($facture->statut) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('factures.show', $facture) }}"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Fournisseurs ── --}}
    @if($fournisseurs->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-truck text-secondary me-2"></i>
                    Fournisseurs
                    <span class="badge bg-secondary ms-1">
                        {{ $fournisseurs->count() }}
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fournisseurs as $fournisseur)
                        <tr>
                            <td class="fw-semibold">{{ $fournisseur->nom }}</td>
                            <td>{{ $fournisseur->telephone ?? '—' }}</td>
                            <td>{{ $fournisseur->email ?? '—' }}</td>
                            <td>
                                <a href="{{ route('fournisseurs.show', $fournisseur) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Livraisons ── --}}
    @if($livraisons->isNotEmpty())
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-geo-alt text-danger me-2"></i>
                    Livraisons
                    <span class="badge bg-danger ms-1">
                        {{ $livraisons->count() }}
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vente</th>
                            <th>Client</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livraisons as $livraison)
                        <tr>
                            <td class="fw-semibold">
                                {{ $livraison->vente->numero_vente }}
                            </td>
                            <td>{{ $livraison->client->nom }}</td>
                            <td>
                                <span class="badge bg-{{ $livraison->couleur_statut }}">
                                    {{ ucfirst(str_replace('_', ' ', $livraison->statut)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('livraisons.show', $livraison) }}"
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection