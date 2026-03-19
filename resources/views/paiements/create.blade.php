@extends('layouts.app')
@section('title', 'Nouveau Paiement')
@section('page-title', 'Nouveau Paiement')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-cash-coin me-2"></i>Enregistrer un paiement
                </h5>
            </div>
            <div class="card-body">

                @if($factures->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Toutes les factures sont payées !
                    <a href="{{ route('factures.index') }}" class="alert-link">
                        Voir les factures
                    </a>
                </div>
                @else
                <form action="{{ route('paiements.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <!-- Facture -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Facture <span class="text-danger">*</span>
                            </label>
                            <select name="facture_id"
                                    class="form-select @error('facture_id') is-invalid @enderror"
                                    id="select-facture" required>
                                <option value="">-- Sélectionner une facture --</option>
                                @foreach($factures as $facture)
                                    <option value="{{ $facture->id }}"
                                            data-montant="{{ $facture->montant }}"
                                            data-restant="{{ $facture->montant_restant }}"
                                            data-client="{{ $facture->vente->client->nom }}"
                                            {{ old('facture_id')==$facture->id ? 'selected':'' }}>
                                        {{ $facture->numero }} —
                                        {{ $facture->vente->client->nom }} —
                                        {{ number_format($facture->montant_restant, 0, ',', ' ') }} F restant
                                    </option>
                                @endforeach
                            </select>
                            @error('facture_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info facture -->
                        <div class="col-12" id="info-facture" style="display:none">
                            <div class="alert alert-info mb-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Client</small>
                                        <strong id="info-client">—</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">
                                            Montant total
                                        </small>
                                        <strong id="info-montant">—</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">
                                            Restant à payer
                                        </small>
                                        <strong id="info-restant" class="text-danger">
                                            —
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Montant -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Montant (F CFA) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="montant"
                                   class="form-control @error('montant') is-invalid @enderror"
                                   id="input-montant"
                                   value="{{ old('montant') }}"
                                   min="1" step="0.01" required>
                            @error('montant')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date paiement -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Date de paiement <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_paiement"
                                   class="form-control"
                                   value="{{ old('date_paiement', date('Y-m-d')) }}"
                                   required>
                        </div>

                        <!-- Mode paiement -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Mode de paiement <span class="text-danger">*</span>
                            </label>
                            <select name="mode_paiement"
                                    class="form-select @error('mode_paiement') is-invalid @enderror"
                                    required>
                                <option value="">-- Choisir --</option>
                                <option value="especes"
                                    {{ old('mode_paiement')=='especes' ? 'selected':'' }}>
                                    💵 Espèces
                                </option>
                                <option value="mobile_money"
                                    {{ old('mode_paiement')=='mobile_money' ? 'selected':'' }}>
                                    📱 Mobile Money
                                </option>
                                <option value="virement"
                                    {{ old('mode_paiement')=='virement' ? 'selected':'' }}>
                                    🏦 Virement bancaire
                                </option>
                                <option value="cheque"
                                    {{ old('mode_paiement')=='cheque' ? 'selected':'' }}>
                                    📄 Chèque
                                </option>
                            </select>
                            @error('mode_paiement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Référence -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Référence / N° reçu
                            </label>
                            <input type="text" name="reference"
                                   class="form-control"
                                   value="{{ old('reference') }}"
                                   placeholder="Ex: MTN-2024-001">
                        </div>

                        <!-- Note -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note</label>
                            <textarea name="note" class="form-control" rows="2"
                                      placeholder="Remarque optionnelle...">{{ old('note') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('paiements.index') }}"
                           class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('select-facture').addEventListener('change', function() {
    const opt     = this.options[this.selectedIndex];
    const montant = opt.dataset.montant  || 0;
    const restant = opt.dataset.restant  || 0;
    const client  = opt.dataset.client   || '';

    if (client) {
        document.getElementById('info-facture').style.display = 'block';
        document.getElementById('info-client').textContent    = client;
        document.getElementById('info-montant').textContent   =
            parseFloat(montant).toLocaleString('fr-FR') + ' F CFA';
        document.getElementById('info-restant').textContent   =
            parseFloat(restant).toLocaleString('fr-FR') + ' F CFA';
        document.getElementById('input-montant').value        = restant;
        document.getElementById('input-montant').max          = restant;
    } else {
        document.getElementById('info-facture').style.display = 'none';
        document.getElementById('input-montant').value        = '';
    }
});
</script>
@endpush