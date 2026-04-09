@extends('layouts.app')
@section('title', 'Nouvelle Vente')
@section('page-title', 'Nouvelle Vente')

@push('styles')
<style>
    .ligne-produit {
        background: #F9FAFB;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 10px;
        border: 1px solid #F3F4F6;
        transition: all 0.2s;
    }
    .ligne-produit:hover {
        border-color: #FED7AA;
        background: #FFFBF5;
    }
    .is-invalid-stock {
        border-color: #EF4444 !important;
        background-color: #FEF2F2;
    }
    .tva-bloc {
        background: #F9FAFB;
        border-radius: 10px;
        border: 1px solid #F3F4F6;
        padding: 20px;
        margin-top: 16px;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #F3F4F6;
        font-size: 0.875rem;
    }
    .total-row:last-child { border-bottom: none; }
    .total-row.ttc {
        padding-top: 12px;
        margin-top: 4px;
        border-top: 2px solid #F3F4F6;
    }
    .form-section {
        background: white;
        border-radius: 12px;
        border: 1px solid #F3F4F6;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .form-section-header {
        padding: 14px 20px;
        border-bottom: 1px solid #F9FAFB;
        font-weight: 600;
        font-size: 0.9rem;
        color: #111827;
        background: #FAFAFA;
    }
    .form-section-body { padding: 20px; }
</style>
@endpush

@php
    $produitsJson = $produits->map(fn($p) => [
        'id'             => $p->id,
        'nom'            => $p->nom,
        'prix_vente'     => $p->prix_vente,
        'quantite_stock' => $p->quantite_stock,
        'unite'          => $p->unite,
    ]);
@endphp

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">

        {{-- En-tête --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold" style="color:#111827">Nouvelle vente</h4>
                <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
                    Remplissez les informations de la vente
                </p>
            </div>
            <span style="background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
                         border-radius:20px;padding:5px 14px;font-size:0.85rem;font-weight:700">
                {{ $numero }}
            </span>
        </div>

        <form action="{{ route('ventes.store') }}" method="POST" id="form-vente">
            @csrf

            {{-- ── Section 1 : Informations générales ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="bi bi-info-circle me-2" style="color:#F97316"></i>
                    Informations générales
                </div>
                <div class="form-section-body">
                    <div class="row g-3">

                        {{-- Client --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.875rem">
                                Client <span class="text-danger">*</span>
                            </label>
                            <select name="client_id" id="client_id"
                                    class="form-select @error('client_id') is-invalid @enderror"
                                    required>
                                <option value="">— Sélectionner un client —</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        data-exonere="{{ $client->exonere_tva ? '1' : '0' }}"
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }}
                                    @if($client->exonere_tva)
                                        (Exonéré TVA)
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Badge statut TVA client --}}
                            <div id="badge-tva-client" style="display:none;margin-top:6px">
                                <span id="texte-tva-client"
                                      style="font-size:0.75rem;font-weight:600;
                                             padding:2px 10px;border-radius:20px">
                                </span>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:0.875rem">
                                <i class="bi bi-calendar3 me-1" style="color:#F97316"></i>
                                Date de vente
                            </label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ date('d/m/Y') }}"
                                   readonly
                                   style="background:#F9FAFB;cursor:not-allowed;color:#6B7280">
                            <input type="hidden" name="date_vente" value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Note --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:0.875rem">
                                Note
                            </label>
                            <input type="text" name="note" class="form-control"
                                   value="{{ old('note') }}"
                                   placeholder="Remarque optionnelle">
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Section 2 : Produits ── --}}
            <div class="form-section">
                <div class="form-section-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-box-seam me-2" style="color:#F97316"></i>
                        Produits vendus
                    </span>
                    <button type="button" id="btn-ajouter-ligne"
                            style="background:linear-gradient(135deg,#F97316,#EA580C);
                                   color:white;border:none;border-radius:8px;
                                   padding:6px 14px;font-size:0.82rem;font-weight:600;
                                   cursor:pointer">
                        <i class="bi bi-plus me-1"></i> Ajouter un produit
                    </button>
                </div>
                <div class="form-section-body">
                    <div id="lignes-container">
                        <div class="ligne-produit row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold"
                                       style="color:#6B7280">Produit</label>
                                <select name="produits[0][produit_id]"
                                        class="form-select select-produit" required>
                                    <option value="">— Choisir —</option>
                                    @foreach($produits as $p)
                                    <option value="{{ $p->id }}"
                                            data-prix="{{ $p->prix_vente }}"
                                            data-stock="{{ $p->quantite_stock }}">
                                        {{ $p->nom }}
                                        (Stock: {{ $p->quantite_stock }} {{ $p->unite }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold"
                                       style="color:#6B7280">Prix unitaire</label>
                                <div class="input-group">
                                    <input type="number"
                                           name="produits[0][prix_unitaire]"
                                           class="form-control input-prix"
                                           readonly step="0.01" required
                                           style="background:#F9FAFB">
                                    <span class="input-group-text"
                                          style="background:#F9FAFB;
                                                 border-color:#E5E7EB;
                                                 font-size:0.75rem;color:#9CA3AF">F</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold"
                                       style="color:#6B7280">Quantité</label>
                                <input type="number"
                                       name="produits[0][quantite]"
                                       class="form-control input-quantite"
                                       min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold"
                                       style="color:#6B7280">Sous-total</label>
                                <input type="text"
                                       class="form-control input-sous-total"
                                       readonly placeholder="0"
                                       style="background:#F9FAFB;
                                              font-weight:600;color:#111827">
                            </div>
                            <div class="col-md-1">
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm btn-supprimer-ligne"
                                        style="border-radius:8px;width:36px;height:36px;
                                               display:flex;align-items:center;justify-content:center">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 3 : TVA & Total ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="bi bi-calculator me-2" style="color:#F97316"></i>
                    Récapitulatif & TVA
                </div>
                <div class="form-section-body">
                    <div class="row g-3">

                        {{-- Switch TVA --}}
                        <div class="col-md-6">

                            {{-- Alerte exonération --}}
                            <div id="alerte-exonere"
                                 style="display:none;background:#FFFBEB;border:1px solid #FDE68A;
                                        border-radius:8px;padding:12px 16px;margin-bottom:12px">
                                <i class="bi bi-exclamation-triangle me-2"
                                   style="color:#D97706"></i>
                                <span style="font-size:0.875rem;color:#92400E;font-weight:500">
                                    Ce client est <strong>exonéré de TVA</strong>.
                                    La TVA ne peut pas être appliquée.
                                </span>
                            </div>

                            <div style="background:#F9FAFB;border-radius:10px;
                                        padding:16px;border:1px solid #F3F4F6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold" style="color:#111827;font-size:0.9rem">
                                            Appliquer la TVA
                                        </div>
                                        <div style="font-size:0.78rem;color:#9CA3AF;margin-top:2px">
                                            Taux fixe : <strong style="color:#F97316">19,25%</strong>
                                            (Cameroun)
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox"
                                               id="tva_applicable" name="tva_applicable"
                                               value="1" role="switch"
                                               style="width:44px;height:22px;cursor:pointer">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Récapitulatif montants --}}
                        <div class="col-md-6">
                            <div style="background:#F9FAFB;border-radius:10px;
                                        padding:16px;border:1px solid #F3F4F6">

                                <div class="total-row">
                                    <span style="color:#6B7280">Sous-total HT</span>
                                    <span class="fw-semibold" style="color:#111827"
                                          id="display-ht">0 F</span>
                                </div>

                                <div class="total-row" id="ligne-tva"
                                     style="display:none">
                                    <span style="color:#F97316">
                                        TVA (19,25%)
                                    </span>
                                    <span class="fw-semibold" style="color:#F97316"
                                          id="display-tva">+ 0 F</span>
                                </div>

                                <div class="total-row" id="ligne-exonere"
                                     style="display:none">
                                    <span style="color:#9CA3AF">TVA</span>
                                    <span style="color:#9CA3AF;font-size:0.82rem">
                                        Exonéré
                                    </span>
                                </div>

                                <div class="total-row ttc">
                                    <span class="fw-bold" style="color:#111827;font-size:1rem">
                                        TOTAL TTC
                                    </span>
                                    <span class="fw-bold"
                                          style="color:#F97316;font-size:1.2rem"
                                          id="display-ttc">
                                        0 F CFA
                                    </span>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Boutons ── --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-lg" id="btn-submit"
                        style="background:linear-gradient(135deg,#F97316,#EA580C);
                               color:white;border:none;border-radius:10px;
                               padding:12px 28px;font-weight:600;
                               box-shadow:0 2px 8px rgba(249,115,22,0.3)">
                    <i class="bi bi-check-lg me-2"></i> Enregistrer la vente
                </button>
                <a href="{{ route('ventes.index') }}"
                   class="btn btn-lg btn-outline-secondary"
                   style="border-radius:10px;padding:12px 24px">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const TAUX_TVA    = 19.25;
    const produitsData = {!! json_encode($produitsJson) !!};
    let ligneIndex     = 1;

    // ── Éléments DOM ──
    const checkTVA      = document.getElementById('tva_applicable');
    const alerteExonere = document.getElementById('alerte-exonere');
    const ligneTVA      = document.getElementById('ligne-tva');
    const ligneExonere  = document.getElementById('ligne-exonere');
    const displayHT     = document.getElementById('display-ht');
    const displayTVA    = document.getElementById('display-tva');
    const displayTTC    = document.getElementById('display-ttc');
    const selectClient  = document.getElementById('client_id');
    const badgeTVA      = document.getElementById('badge-tva-client');
    const texteBadge    = document.getElementById('texte-tva-client');

    // ── Gestion client exonéré ──
    selectClient.addEventListener('change', function() {
        const opt     = this.options[this.selectedIndex];
        const exonere = opt.dataset.exonere === '1';

        if (this.value === '') {
            badgeTVA.style.display = 'none';
            alerteExonere.style.display = 'none';
            checkTVA.disabled = false;
            return;
        }

        badgeTVA.style.display = 'block';

       if (exonere) {
    badgeTVA.style.display      = 'block';
    texteBadge.textContent      = '⚪ Client exonéré de TVA';
    texteBadge.style.background = '#F3F4F6';
    texteBadge.style.color      = '#6B7280';
    texteBadge.style.border     = '1px solid #E5E7EB';
    alerteExonere.style.display = 'block';
    checkTVA.checked            = false;
    checkTVA.disabled           = true;
    ligneTVA.style.display      = 'none';
    ligneExonere.style.display  = 'flex';
} else {
    // Client normal → on n'affiche RIEN
    badgeTVA.style.display      = 'none';
    alerteExonere.style.display = 'none';
    checkTVA.disabled           = false;
    ligneExonere.style.display  = 'none';
}

        calculerTotaux();
    });

    // ── Gestion switch TVA ──
    checkTVA.addEventListener('change', function() {
        ligneTVA.style.display = this.checked ? 'flex' : 'none';
        calculerTotaux();
    });

    // ── Calcul totaux ──
    function calculerTotaux() {
        let montantHT  = 0;
        let erreurStock = false;

        document.querySelectorAll('.ligne-produit').forEach(function(ligne) {
            const select   = ligne.querySelector('.select-produit');
            const opt      = select.options[select.selectedIndex];
            const stockMax = opt ? parseFloat(opt.dataset.stock || 0) : 0;
            const prix     = parseFloat(ligne.querySelector('.input-prix').value) || 0;
            const qteInput = ligne.querySelector('.input-quantite');
            const qte      = parseFloat(qteInput.value) || 0;

            // Vérification stock
            if (qte > stockMax && select.value !== '') {
                qteInput.classList.add('is-invalid-stock');
                erreurStock = true;
            } else {
                qteInput.classList.remove('is-invalid-stock');
            }

            const st = prix * qte;
            ligne.querySelector('.input-sous-total').value =
                st > 0 ? st.toLocaleString('fr-FR') + ' F' : '';

            montantHT += st;
        });

        // Calcul TVA
        const tvaActive  = checkTVA.checked && !checkTVA.disabled;
        const montantTVA = tvaActive
            ? Math.round(montantHT * (TAUX_TVA / 100))
            : 0;
        const montantTTC = montantHT + montantTVA;

        // Mise à jour affichage
        displayHT.textContent  = montantHT.toLocaleString('fr-FR') + ' F';
        displayTVA.textContent = '+ ' + montantTVA.toLocaleString('fr-FR') + ' F';
        displayTTC.textContent = montantTTC.toLocaleString('fr-FR') + ' F CFA';

        // Couleur TTC selon TVA
        displayTTC.style.color = tvaActive ? '#F97316' : '#10B981';

        // Bouton submit
        const btnSubmit = document.getElementById('btn-submit');
        if (erreurStock) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML =
                '<i class="bi bi-exclamation-triangle me-2"></i> Stock insuffisant';
            btnSubmit.style.background = '#EF4444';
        } else {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML =
                '<i class="bi bi-check-lg me-2"></i> Enregistrer la vente';
            btnSubmit.style.background =
                'linear-gradient(135deg,#F97316,#EA580C)';
        }
    }

    // ── Générer options produits ──
    function genererOptions() {
        let html = '<option value="">— Choisir —</option>';
        produitsData.forEach(function(p) {
            html += `<option value="${p.id}"
                             data-prix="${p.prix_vente}"
                             data-stock="${p.quantite_stock}">
                        ${p.nom} (Stock: ${p.quantite_stock} ${p.unite})
                    </option>`;
        });
        return html;
    }

    // ── Nouvelle ligne produit ──
    function nouvelleLigne(index) {
        return `
        <div class="ligne-produit row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold" style="color:#6B7280">
                    Produit
                </label>
                <select name="produits[${index}][produit_id]"
                        class="form-select select-produit" required>
                    ${genererOptions()}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold" style="color:#6B7280">
                    Prix unitaire
                </label>
                <div class="input-group">
                    <input type="number" name="produits[${index}][prix_unitaire]"
                           class="form-control input-prix"
                           readonly step="0.01" required
                           style="background:#F9FAFB">
                    <span class="input-group-text"
                          style="background:#F9FAFB;border-color:#E5E7EB;
                                 font-size:0.75rem;color:#9CA3AF">F</span>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold" style="color:#6B7280">
                    Quantité
                </label>
                <input type="number" name="produits[${index}][quantite]"
                       class="form-control input-quantite" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold" style="color:#6B7280">
                    Sous-total
                </label>
                <input type="text" class="form-control input-sous-total"
                       readonly placeholder="0"
                       style="background:#F9FAFB;font-weight:600;color:#111827">
            </div>
            <div class="col-md-1">
                <button type="button"
                        class="btn btn-outline-danger btn-sm btn-supprimer-ligne"
                        style="border-radius:8px;width:36px;height:36px;
                               display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    }

    // ── Attacher événements ──
    function attacherEvenements() {
        document.querySelectorAll('.select-produit').forEach(function(select) {
            select.onchange = function() {
                const opt  = this.options[this.selectedIndex];
                const ligne = this.closest('.ligne-produit');
                ligne.querySelector('.input-prix').value =
                    opt.dataset.prix || '';
                calculerTotaux();
            };
        });

        document.querySelectorAll('.input-prix, .input-quantite').forEach(function(input) {
            input.oninput = calculerTotaux;
        });

        document.querySelectorAll('.btn-supprimer-ligne').forEach(function(btn) {
            btn.onclick = function() {
                if (document.querySelectorAll('.ligne-produit').length > 1) {
                    this.closest('.ligne-produit').remove();
                    calculerTotaux();
                } else {
                    alert('Vous devez avoir au moins un produit !');
                }
            };
        });
    }

    // ── Ajouter ligne ──
    document.getElementById('btn-ajouter-ligne').addEventListener('click', function() {
        document.getElementById('lignes-container')
                .insertAdjacentHTML('beforeend', nouvelleLigne(ligneIndex++));
        attacherEvenements();
    });

    attacherEvenements();
    calculerTotaux();

});
</script>
@endpush