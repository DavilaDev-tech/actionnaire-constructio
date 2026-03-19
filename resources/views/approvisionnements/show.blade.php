@extends('layouts.app')
@section('title', 'Détail Approvisionnement')
@section('page-title', 'Détail Approvisionnement')

@section('content')
<div class="row">
    <!-- Infos appro -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-down-circle me-2"></i>
                    {{ $approvisionnement->numero }}
                </h5>
                <span class="badge bg-{{ $approvisionnement->couleur_statut }} fs-6">
                    {{ ucfirst($approvisionnement->statut) }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Fournisseur</td>
                        <td class="fw-semibold">
                            {{ $approvisionnement->fournisseur->nom }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Responsable</td>
                        <td>{{ $approvisionnement->user->nom_complet }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Date</td>
                        <td>{{ $approvisionnement->date_appro->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Montant total</td>
                        <td class="fw-bold text-danger fs-5">
                            {{ number_format($approvisionnement->montant_total, 0, ',', ' ') }} F CFA
                        </td>
                    </tr>
                    @if($approvisionnement->note)
                    <tr>
                        <td class="text-muted">Note</td>
                        <td>{{ $approvisionnement->note }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Changer statut -->
        @if(!$approvisionnement->isRecu() && !$approvisionnement->isAnnule())
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">Changer le statut</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('approvisionnements.statut', $approvisionnement) }}"
                      method="POST">
                    @csrf @method('PATCH')
                    <select name="statut" class="form-select mb-3">
                        <option value="en_attente"
                            {{ $approvisionnement->statut=='en_attente'?'selected':'' }}>
                            ⏳ En attente
                        </option>
                        <option value="recu">✅ Marquer comme reçu</option>
                        <option value="annule">❌ Annuler</option>
                    </select>
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Marquer comme <strong>reçu</strong> ajoutera
                        automatiquement les quantités au stock !
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check me-1"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($approvisionnement->isRecu())
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            Cet approvisionnement a été <strong>reçu</strong>.
            Le stock a été mis à jour automatiquement.
        </div>
        @endif
    </div>

    <!-- Détail produits -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>
                    Produits commandés
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvisionnement->details as $detail)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $detail->produit->nom }}</div>
                                <small class="text-muted">
                                    Stock actuel :
                                    <strong>{{ $detail->produit->quantite_stock }}</strong>
                                    {{ $detail->produit->unite }}
                                </small>
                            </td>
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
                            <td colspan="3" class="text-end fw-bold">TOTAL DÉPENSE</td>
                            <td class="text-end fw-bold fs-5">
                                {{ number_format($approvisionnement->montant_total, 0, ',', ' ') }} F CFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <a href="{{ route('approvisionnements.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>
@endsection