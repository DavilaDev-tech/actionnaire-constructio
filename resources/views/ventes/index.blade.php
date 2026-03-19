@extends('layouts.app')
@section('title', 'Ventes')
@section('page-title', 'Gestion des Ventes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">🛒 Ventes</h4>
        <p class="text-muted mb-0">Gérez toutes les ventes</p>
    </div>
    <div class="d-flex gap-2">
    <a href="{{ route('export.ventes') }}"
       class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
    </a>
    <a href="{{ route('ventes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nouvelle vente
    </a>
</div>
</div>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cart3 fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $totalVentes }}</div>
                    <div class="small opacity-75">Total ventes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-cash fs-1 opacity-75"></i>
                <div>
                    <div class="fs-4 fw-bold">
                        {{ number_format($chiffreAffaire, 0, ',', ' ') }}
                    </div>
                    <div class="small opacity-75">Chiffre d'affaires (F)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-hourglass fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $enAttente }}</div>
                    <div class="small opacity-75">En attente</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-x-circle fs-1 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $annulees }}</div>
                    <div class="small opacity-75">Annulées</div>
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
                    <th>N° Vente</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Facture</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventes as $vente)
                @php
                    $statutColors = [
                        'en_attente' => 'warning',
                        'confirmee'  => 'primary',
                        'livree'     => 'success',
                        'annulee'    => 'danger',
                    ];
                @endphp
                <tr>
                    <td class="fw-bold">{{ $vente->numero_vente }}</td>
                    <td>{{ $vente->client->nom }}</td>
                    <td>{{ $vente->user->nom }}</td>
                    <td>{{ $vente->date_vente->format('d/m/Y') }}</td>
                    <td class="fw-semibold">
                        {{ number_format($vente->montant_total, 0, ',', ' ') }} F
                    </td>
                    <td>
                        <span class="badge bg-{{ $statutColors[$vente->statut] }}">
                            {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                        </span>
                    </td>
                    <td>
                        @if($vente->facture)
                            <span class="badge bg-{{
                                $vente->facture->statut == 'payee'    ? 'success' :
                                ($vente->facture->statut == 'annulee' ? 'danger'  : 'warning')
                            }}">
                                {{ ucfirst(str_replace('_', ' ', $vente->facture->statut)) }}
                            </span>
                        @else
                            <span class="badge bg-secondary">Aucune</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('ventes.show', $vente) }}"
                               class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!$vente->isAnnulee() && !$vente->isLivree())
                            <form action="{{ route('ventes.statut', $vente) }}"
                                  method="POST">
                                @csrf @method('PATCH')
                                <select name="statut"
                                        class="form-select form-select-sm"
                                        onchange="this.form.submit()">
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
                                    <option value="annulee">Annuler</option>
                                </select>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                        Aucune vente trouvée.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ventes->hasPages())
    <div class="card-footer">{{ $ventes->links() }}</div>
    @endif
</div>
@endsection