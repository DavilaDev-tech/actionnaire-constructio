@extends('layouts.app')
@section('title', 'Détail Fournisseur')
@section('page-title', 'Détail Fournisseur')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-truck me-2"></i>{{ $fournisseur->nom }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Contact</td>
                        <td>{{ $fournisseur->contact_personne ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Téléphone</td>
                        <td>{{ $fournisseur->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $fournisseur->email ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Adresse</td>
                        <td>{{ $fournisseur->adresse ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nb produits</td>
                        <td>
                            <span class="badge bg-success">
                                {{ $fournisseur->nombre_produits }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nb appros</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $fournisseur->nombre_appros }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Produits liés -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>Produits fournis
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Prix vente</th>
                            <th class="text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fournisseur->produits as $produit)
                        <tr>
                            <td class="fw-semibold">{{ $produit->nom }}</td>
                            <td>{{ $produit->categorie->nom }}</td>
                            <td>
                                {{ number_format($produit->prix_vente, 0, ',', ' ') }} F
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{
                                    $produit->isStockEpuise() ? 'danger' :
                                    ($produit->isStockBas()   ? 'warning' : 'success')
                                }}">
                                    {{ $produit->quantite_stock }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Aucun produit associé.
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
    <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i> Modifier
    </a>
    <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection