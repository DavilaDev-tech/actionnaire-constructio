@extends('layouts.app')
@section('title', 'Détail Livraison')
@section('page-title', 'Détail Livraison')

@section('content')
<div class="row">
    <!-- Infos livraison -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-truck me-2"></i>Livraison #{{ $livraison->id }}
                </h5>
                <span class="badge bg-{{ $livraison->couleur_statut }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $livraison->statut)) }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Vente</td>
                        <td class="fw-semibold">
                            {{ $livraison->vente->numero_vente }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Client</td>
                        <td class="fw-semibold">{{ $livraison->client->nom }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Téléphone</td>
                        <td>{{ $livraison->client->telephone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Adresse</td>
                        <td>{{ $livraison->adresse_livraison }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date prévue</td>
                        <td>
                            {{ $livraison->date_livraison
                                ? $livraison->date_livraison->format('d/m/Y')
                                : '—' }}
                        </td>
                    </tr>
                    @if($livraison->note)
                    <tr>
                        <td class="text-muted">Note</td>
                        <td>{{ $livraison->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Changer statut -->
        @if(!$livraison->isLivree())
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">Changer le statut</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('livraisons.statut', $livraison) }}"
                      method="POST">
                    @csrf @method('PATCH')
                    <select name="statut" class="form-select mb-3">
                        <option value="en_attente"
                            {{ $livraison->statut=='en_attente' ? 'selected':'' }}>
                            ⏳ En attente
                        </option>
                        <option value="en_cours"
                            {{ $livraison->statut=='en_cours' ? 'selected':'' }}>
                            🚚 En cours
                        </option>
                        <option value="livree">
                            ✅ Livrée
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check me-1"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Produits de la vente -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    Produits à livrer
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($livraison->vente->details as $detail)
                        <tr>
                            <td class="fw-semibold">{{ $detail->produit->nom }}</td>
                            <td class="text-center">
                                {{ $detail->quantite }} {{ $detail->produit->unite }}
                            </td>
                            <td class="text-end">
                                {{ number_format($detail->sous_total, 0, ',', ' ') }} F
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">TOTAL</td>
                            <td class="text-end fw-bold">
                                {{ number_format($livraison->vente->montant_total, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Suivi visuel -->
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-geo-alt me-2"></i>Suivi de livraison
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Étape 1 -->
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center
                                    justify-content-center mx-auto mb-1
                                    {{ in_array($livraison->statut, ['en_attente','en_cours','livree'])
                                        ? 'bg-primary text-white' : 'bg-light' }}"
                             style="width:45px;height:45px">
                            <i class="bi bi-clock"></i>
                        </div>
                        <small>En attente</small>
                    </div>

                    <!-- Ligne -->
                    <div class="flex-grow-1 mx-2"
                         style="height:3px;background:{{
                            in_array($livraison->statut, ['en_cours','livree'])
                                ? '#0d6efd' : '#dee2e6'
                         }}"></div>

                    <!-- Étape 2 -->
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center
                                    justify-content-center mx-auto mb-1
                                    {{ in_array($livraison->statut, ['en_cours','livree'])
                                        ? 'bg-primary text-white' : 'bg-light' }}"
                             style="width:45px;height:45px">
                            <i class="bi bi-truck"></i>
                        </div>
                        <small>En cours</small>
                    </div>

                    <!-- Ligne -->
                    <div class="flex-grow-1 mx-2"
                         style="height:3px;background:{{
                            $livraison->statut == 'livree' ? '#198754' : '#dee2e6'
                         }}"></div>

                    <!-- Étape 3 -->
                    <div class="text-center">
                        <div class="rounded-circle d-flex align-items-center
                                    justify-content-center mx-auto mb-1
                                    {{ $livraison->statut == 'livree'
                                        ? 'bg-success text-white' : 'bg-light' }}"
                             style="width:45px;height:45px">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <small>Livrée</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    @if(!$livraison->isLivree())
    <a href="{{ route('livraisons.edit', $livraison) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i> Modifier
    </a>
    @endif
    <a href="{{ route('livraisons.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection