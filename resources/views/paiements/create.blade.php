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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($factures->isEmpty())
                <div class="alert alert-info">
                    Toutes les factures sont payées ! 
                    <a href="{{ route('factures.index') }}" class="alert-link">Voir les factures</a>
                </div>
                @else
                <form action="{{ route('paiements.store') }}" method="POST" id="form-paiement">
                    @csrf
                    <div class="row g-3">

                        {{-- 1. Sélection de la facture --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Facture <span class="text-danger">*</span></label>
                            <select name="facture_id" class="form-select" id="select-facture" required>
                                <option value="">-- Sélectionner une facture --</option>
                                @foreach($factures as $facture)
                                    <option value="{{ $facture->id }}"
                                            data-montant="{{ $facture->montant }}"
                                            data-restant="{{ $facture->resteAPayer() }}"
                                            data-client="{{ $facture->vente->client->nom }}"
                                            {{ old('facture_id') == $facture->id ? 'selected' : '' }}>
                                        {{ $facture->numero }} — {{ $facture->vente->client->nom }} — (Reste : {{ number_format($facture->resteAPayer(), 0, ',', ' ') }} F)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Bloc Info Bleu (Mis à jour avec TVA) --}}
                        <div class="col-12" id="info-facture" style="display:none">
                            <div class="alert alert-info border-0 shadow-sm">
                                <div class="row text-center">
                                    <div class="col-md-3 border-end">
                                        <small class="text-muted d-block small fw-bold">CLIENT</small>
                                        <strong id="info-client">—</strong>
                                    </div>
                                    <div class="col-md-3 border-end">
                                        <small class="text-muted d-block small fw-bold">TOTAL (HT)</small>
                                        <strong id="info-montant-ht">—</strong>
                                    </div>
                                    <div class="col-md-3 border-end">
                                        <small class="text-muted d-block small fw-bold">TVA (19.25%)</small>
                                        <strong id="info-tva" class="text-warning">—</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block small fw-bold">RESTE (TTC)</small>
                                        <strong id="info-restant" class="text-danger">—</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Montant à encaisser --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montant à encaisser (F CFA) <span class="text-danger">*</span></label>
                            <input type="number" name="montant" class="form-control" id="input-montant" value="{{ old('montant') }}" required>
                        </div>

                        {{-- 4. Date de paiement --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date de paiement</label>
                            <input type="date" name="date_paiement" class="form-control bg-light" value="{{ date('Y-m-d') }}" readonly>
                        </div>

                        {{-- 5. Mode de règlement --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mode de règlement <span class="text-danger">*</span></label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="especes">💵 Espèces</option>
                                <option value="mobile_money">📱 Mobile Money</option>
                                <option value="virement">🏦 Virement bancaire</option>
                                <option value="cheque">📄 Chèque</option>
                            </select>
                        </div>

                        {{-- 6. Référence --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference" class="form-control" placeholder="Ex: ID Transaction">
                        </div>

                        {{-- 7. Note / Observation --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Note / Observation (Auto)</label>
                            <textarea name="note" id="input-note" class="form-control bg-light" rows="2" readonly style="font-style: italic;"></textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer l'encaissement
                        </button>
                        <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
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
document.addEventListener('DOMContentLoaded', function() {
    const selectFacture = document.getElementById('select-facture');
    const inputMontant  = document.getElementById('input-montant');
    const inputNote     = document.getElementById('input-note');
    const blocBleu      = document.getElementById('info-facture');

    function mettreAJourLaNote() {
        const option = selectFacture.options[selectFacture.selectedIndex];
        
        if (!option || option.value === "") {
            if(blocBleu) blocBleu.style.display = 'none';
            inputNote.value = "";
            return;
        }

        if(blocBleu) blocBleu.style.display = 'block';

        // Récupération des données HT et Reste (qui est TTC maintenant via le modèle)
        const ht = parseFloat(option.getAttribute('data-montant')) || 0;
        const resteInitialTTC = parseFloat(option.getAttribute('data-restant')) || 0;
        const montantSaisi = parseFloat(inputMontant.value) || 0;
        
        // Calcul manuel de la TVA pour l'affichage JS
        const taux = 19.25;
        const tva = (ht * taux) / 100;

        // Mise à jour des éléments du bloc bleu
        const elClient = document.getElementById('info-client');
        const elHT     = document.getElementById('info-montant-ht');
        const elTVA    = document.getElementById('info-tva');
        const elReste  = document.getElementById('info-restant');

        if(elClient) elClient.innerText = option.getAttribute('data-client');
        if(elHT)     elHT.innerText     = ht.toLocaleString() + " F";
        if(elTVA)    elTVA.innerText    = tva.toLocaleString() + " F";
        if(elReste)  elReste.innerText  = resteInitialTTC.toLocaleString() + " F";

        // Logique de la Note
        let message = "";
        if (montantSaisi <= 0) {
            message = "En attente de saisie...";
        } else if (montantSaisi >= resteInitialTTC) {
            message = "Paiement intégral de " + montantSaisi.toLocaleString() + " F (TTC). La facture sera SOLDÉE.";
        } else {
            let reliquat = resteInitialTTC - montantSaisi;
            message = "Paiement partiel de " + montantSaisi.toLocaleString() + " F. Reste à payer (TTC) : " + reliquat.toLocaleString() + " F.";
        }
        
        inputNote.value = message;
    }

    selectFacture.addEventListener('change', function() {
        const optionChoisie = this.options[this.selectedIndex];
        if (optionChoisie && optionChoisie.value !== "") {
            // On remplit avec le reste TTC par défaut
            inputMontant.value = optionChoisie.getAttribute('data-restant');
        }
        mettreAJourLaNote();
    });

    inputMontant.addEventListener('input', mettreAJourLaNote);
    mettreAJourLaNote();
});
</script>
@endpush