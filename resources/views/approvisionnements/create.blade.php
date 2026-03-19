@extends('layouts.app')
@section('title', 'Nouvel Approvisionnement')
@section('page-title', 'Nouvel Approvisionnement')

@push('styles')
<style>
    .ligne-produit {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
    }
    #total-general { font-size: 1.5rem; font-weight: bold; color: #dc3545; }
</style>
@endpush

@php
    $produitsJson = $produits->map(function($p) {
        return [
            'id'         => $p->id,
            'nom'        => $p->nom,
            'categorie'  => $p->categorie->nom,
            'prix_achat' => $p->prix_achat,
        ];
    });
@endphp

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex
                        justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-arrow-down-circle me-2"></i>
                    Créer un approvisionnement
                </h5>
                <span class="badge bg-light text-dark fs-6">{{ $numero }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('approvisionnements.store') }}"
                      method="POST" id="form-appro">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">
                                Fournisseur <span class="text-danger">*</span>
                            </label>
                            <select name="fournisseur_id"
                                    class="form-select @error('fournisseur_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($fournisseurs as $f)
                                    <option value="{{ $f->id }}"
                                            {{ old('fournisseur_id')==$f->id ? 'selected':'' }}>
                                        {{ $f->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fournisseur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_appro"
                                   class="form-control"
                                   value="{{ old('date_appro', date('Y-m-d')) }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Note</label>
                            <input type="text" name="note"
                                   class="form-control"
                                   value="{{ old('note') }}"
                                   placeholder="Remarque optionnelle...">
                        </div>
                    </div>

                    <!-- Lignes produits -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-list-ul me-2"></i>Produits commandés
                        </h6>
                        <button type="button" class="btn btn-success btn-sm"
                                id="btn-ajouter-ligne">
                            <i class="bi bi-plus me-1"></i> Ajouter un produit
                        </button>
                    </div>

                    <div id="lignes-container">
                        <div class="ligne-produit row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small">Produit</label>
                                <select name="produits[0][produit_id]"
                                        class="form-select select-produit" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach($produits as $p)
                                        <option value="{{ $p->id }}"
                                                data-prix="{{ $p->prix_achat }}">
                                            {{ $p->nom }} ({{ $p->categorie->nom }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Prix unitaire (F)</label>
                                <input type="number"
                                       name="produits[0][prix_unitaire]"
                                       class="form-control input-prix"
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Quantité</label>
                                <input type="number"
                                       name="produits[0][quantite]"
                                       class="form-control input-quantite"
                                       min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Sous-total</label>
                                <input type="text"
                                       class="form-control input-sous-total"
                                       readonly placeholder="0">
                            </div>
                            <div class="col-md-1">
                                <button type="button"
                                        class="btn btn-outline-danger btn-supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-end mt-3">
                        <div class="text-end">
                            <span class="text-muted me-3">TOTAL DÉPENSE :</span>
                            <span id="total-general">0 F CFA</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('approvisionnements.index') }}"
                           class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const produitsData = {!! json_encode($produitsJson) !!};
    let ligneIndex = 1;

    // ── Générer les options ──
    function genererOptions() {
        let html = '<option value="">-- Choisir --</option>';
        produitsData.forEach(function(p) {
            html += '<option value="' + p.id + '"' +
                    ' data-prix="' + p.prix_achat + '">' +
                    p.nom + ' (' + p.categorie + ')' +
                    '</option>';
        });
        return html;
    }

    // ── Template nouvelle ligne ──
    function nouvelleLigne(index) {
        return '<div class="ligne-produit row g-2 align-items-end">' +
            '<div class="col-md-5">' +
                '<label class="form-label small">Produit</label>' +
                '<select name="produits[' + index + '][produit_id]"' +
                        ' class="form-select select-produit" required>' +
                    genererOptions() +
                '</select>' +
            '</div>' +
            '<div class="col-md-2">' +
                '<label class="form-label small">Prix unitaire (F)</label>' +
                '<input type="number"' +
                       ' name="produits[' + index + '][prix_unitaire]"' +
                       ' class="form-control input-prix"' +
                       ' step="0.01" min="0" required>' +
            '</div>' +
            '<div class="col-md-2">' +
                '<label class="form-label small">Quantité</label>' +
                '<input type="number"' +
                       ' name="produits[' + index + '][quantite]"' +
                       ' class="form-control input-quantite"' +
                       ' min="1" required>' +
            '</div>' +
            '<div class="col-md-2">' +
                '<label class="form-label small">Sous-total</label>' +
                '<input type="text"' +
                       ' class="form-control input-sous-total"' +
                       ' readonly placeholder="0">' +
            '</div>' +
            '<div class="col-md-1">' +
                '<button type="button"' +
                        ' class="btn btn-outline-danger btn-supprimer">' +
                    '<i class="bi bi-trash"></i>' +
                '</button>' +
            '</div>' +
        '</div>';
    }

    // ── Calcul totaux ──
    function calculerTotaux() {
        let total = 0;
        document.querySelectorAll('.ligne-produit').forEach(function(ligne) {
            const prix = parseFloat(
                ligne.querySelector('.input-prix').value) || 0;
            const qte  = parseFloat(
                ligne.querySelector('.input-quantite').value) || 0;
            const st   = prix * qte;
            ligne.querySelector('.input-sous-total').value =
                st > 0 ? st.toLocaleString('fr-FR') + ' F' : '';
            total += st;
        });
        document.getElementById('total-general').textContent =
            total.toLocaleString('fr-FR') + ' F CFA';
    }

    // ── Attacher les événements ──
    function attacherEvenements() {
        document.querySelectorAll('.select-produit').forEach(function(select) {
            select.onchange = function() {
                const opt   = this.options[this.selectedIndex];
                const ligne = this.closest('.ligne-produit');
                ligne.querySelector('.input-prix').value =
                    opt.dataset.prix || '';
                calculerTotaux();
            };
        });

        document.querySelectorAll('.input-prix, .input-quantite')
                .forEach(function(input) {
            input.oninput = calculerTotaux;
        });

        document.querySelectorAll('.btn-supprimer')
                .forEach(function(btn) {
            btn.onclick = function() {
                const lignes = document.querySelectorAll('.ligne-produit');
                if (lignes.length > 1) {
                    this.closest('.ligne-produit').remove();
                    calculerTotaux();
                } else {
                    alert('Vous devez avoir au moins un produit !');
                }
            };
        });
    }

    // ── Bouton ajouter ──
    document.getElementById('btn-ajouter-ligne')
            .addEventListener('click', function() {
        document.getElementById('lignes-container')
                .insertAdjacentHTML('beforeend', nouvelleLigne(ligneIndex++));
        attacherEvenements();
    });

    // Attacher sur la ligne par défaut
    attacherEvenements();

}); // fin DOMContentLoaded
</script>
@endpush