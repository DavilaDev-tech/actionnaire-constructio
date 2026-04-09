@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Gestion des Clients')

@push('styles')
<style>
    .hector-stat {
        background: white;
        border-radius: 12px;
        border: 1px solid #F3F4F6;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .hector-stat:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transform: translateY(-2px);
    }
    .hector-stat-icon {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center;
        justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .hector-stat-value {
        font-size: 1.6rem; font-weight: 700;
        line-height: 1; color: #111827;
    }
    .hector-stat-label {
        font-size: 0.78rem; color: #9CA3AF;
        margin-top: 3px; font-weight: 500;
    }
    .hector-table thead th {
        background: #F9FAFB !important;
        color: #6B7280 !important;
        font-size: 0.72rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        padding: 12px 16px !important;
        border-bottom: 1px solid #F3F4F6 !important;
        border-top: none !important;
    }
    .hector-table tbody td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #F9FAFB !important;
        border-top: none !important;
        color: #374151 !important;
        font-size: 0.875rem !important;
        vertical-align: middle !important;
    }
    .hector-table tbody tr:hover td { background: #FFFBF5 !important; }
    .hector-table tbody tr:last-child td { border-bottom: none !important; }
    .btn-action {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #E5E7EB; background: white; color: #6B7280;
        transition: all 0.15s; font-size: 0.85rem; text-decoration: none;
        cursor: pointer;
    }
    .btn-action:hover.view { background:#EFF6FF;border-color:#93C5FD;color:#3B82F6; }
    .btn-action:hover.edit { background:#FFF7ED;border-color:#FED7AA;color:#F97316; }
    .btn-action:hover.del  { background:#FEF2F2;border-color:#FECACA;color:#EF4444; }
    .badge-particulier {
        background:#EFF6FF;color:#3B82F6;border:1px solid #BFDBFE;
        border-radius:20px;padding:3px 10px;font-size:0.72rem;font-weight:600;
    }
    .badge-entreprise {
        background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
        border-radius:20px;padding:3px 10px;font-size:0.72rem;font-weight:600;
    }
    .search-wrapper { position: relative; }
    .search-wrapper i {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); color: #9CA3AF; font-size: 0.85rem;
    }
    .search-input {
        border: 1px solid #E5E7EB; border-radius: 8px;
        padding: 8px 14px 8px 38px; font-size: 0.875rem;
        color: #374151; background: #F9FAFB; transition: all 0.2s; width: 280px;
    }
    .search-input:focus {
        outline: none; border-color: #F97316; background: white;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    /* ── Modal style ── */
    .modal-content {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
    }
    .modal-header {
        border-bottom: 1px solid #F3F4F6 !important;
        padding: 20px 24px !important;
        border-radius: 16px 16px 0 0 !important;
    }
    .modal-body { padding: 24px !important; }
    .modal-footer {
        border-top: 1px solid #F3F4F6 !important;
        padding: 16px 24px !important;
        border-radius: 0 0 16px 16px !important;
    }
    .modal-title {
        font-weight: 700 !important;
        color: #111827 !important;
        font-size: 1rem !important;
    }
    .form-section-modal {
        background: #F9FAFB;
        border-radius: 10px;
        border: 1px solid #F3F4F6;
        padding: 16px;
        margin-bottom: 16px;
    }
    .form-section-modal-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: #F97316;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 12px;
    }
    /* Erreurs inline */
    .field-error {
        color: #EF4444;
        font-size: 0.75rem;
        margin-top: 4px;
        display: none;
    }
    .field-error.show { display: block; }
    .input-invalid { border-color: #EF4444 !important; }
    .input-valid   { border-color: #10B981 !important; }
</style>
@endpush

@section('content')

{{-- ── En-tête ── --}}
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827;font-size:1.3rem">Clients</h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            {{ $totalClients }} client{{ $totalClients > 1 ? 's' : '' }}
            enregistré{{ $totalClients > 1 ? 's' : '' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @if(Route::has('export.clients'))
        <a href="{{ route('export.clients') }}"
           class="btn btn-sm"
           style="background:white;border:1px solid #E5E7EB;color:#374151;
                  border-radius:8px;font-size:0.85rem;padding:8px 16px;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <i class="bi bi-file-earmark-excel me-1" style="color:#10B981"></i>
            Exporter
        </a>
        @endif

        {{-- Bouton ouvre la modal création --}}
        <button type="button"
                onclick="ouvrirModalCreation()"
                style="background:linear-gradient(135deg,#F97316,#EA580C);
                       color:white;border:none;border-radius:8px;
                       font-size:0.85rem;padding:8px 16px;
                       box-shadow:0 2px 8px rgba(249,115,22,0.3);cursor:pointer">
            <i class="bi bi-plus me-1"></i> Nouveau client
        </button>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-people-fill" style="color:#F97316"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $totalClients }}</div>
                <div class="hector-stat-label">Total clients</div>
            </div>
            <div class="ms-auto">
                <span style="font-size:0.75rem;color:#10B981;font-weight:600;
                             background:#ECFDF5;padding:3px 8px;border-radius:20px">
                    ● Actifs
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#EFF6FF">
                <i class="bi bi-person-fill" style="color:#3B82F6"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $particuliers }}</div>
                <div class="hector-stat-label">Particuliers</div>
            </div>
            <div class="ms-auto">
                <div style="font-size:0.72rem;color:#9CA3AF">
                    {{ $totalClients > 0 ? round(($particuliers/$totalClients)*100) : 0 }}%
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#F0FDF4">
                <i class="bi bi-building-fill" style="color:#10B981"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $entreprises }}</div>
                <div class="hector-stat-label">Entreprises</div>
            </div>
            <div class="ms-auto">
                <div style="font-size:0.72rem;color:#9CA3AF">
                    {{ $totalClients > 0 ? round(($entreprises/$totalClients)*100) : 0 }}%
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Tableau ── --}}
<div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                          box-shadow:0 1px 4px rgba(0,0,0,0.06)">
    <div class="d-flex justify-content-between align-items-center px-4 py-3"
         style="border-bottom:1px solid #F9FAFB">
        <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" class="search-input" id="recherche-clients"
                   placeholder="Rechercher un client..."
                   value="{{ $search ?? '' }}">
        </div>
        <div class="d-flex align-items-center gap-3">
            <span style="font-size:0.8rem;color:#9CA3AF">
                <span id="compteur-clients" class="fw-semibold"
                      style="color:#111827">{{ $clients->total() }}</span>
                résultat{{ $clients->total() > 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    <div id="tableau-clients" class="table-responsive">
        @include('clients.partials.tableau')
    </div>

    <div id="pagination-clients" style="border-top:1px solid #F9FAFB;padding:12px 20px">
        @include('partials.pagination', compact('clients'))
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL CRÉATION / MODIFICATION CLIENT
════════════════════════════════════════ --}}
<div class="modal fade" id="modalClient" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:#FFF7ED;border-radius:8px;
                                display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-person-plus" style="color:#F97316"></i>
                    </div>
                    <h5 class="modal-title" id="modal-titre">Nouveau client</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <form id="form-client" novalidate>
                    @csrf
                    <input type="hidden" id="client-id" value="">
                    <input type="hidden" id="client-method" value="POST">

                    {{-- Section 1 : Infos générales --}}
                    <div class="form-section-modal">
                        <div class="form-section-modal-title">
                            <i class="bi bi-person me-1"></i> Informations générales
                        </div>
                        <div class="row g-3">

                            {{-- Nom --}}
                            <div class="col-md-8">
                                <label class="form-label fw-semibold"
                                       style="font-size:0.875rem">
                                    Nom <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="client-nom" name="nom"
                                       class="form-control"
                                       placeholder="Nom du client ou entreprise"
                                       required>
                                <div class="field-error" id="err-nom">
                                    Le nom est obligatoire
                                </div>
                            </div>

                            {{-- Type --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold"
                                       style="font-size:0.875rem">
                                    Type <span class="text-danger">*</span>
                                </label>
                                <select id="client-type" name="type"
                                        class="form-select" required>
                                    <option value="particulier">Particulier</option>
                                    <option value="entreprise">Entreprise</option>
                                </select>
                            </div>

                            {{-- Téléphone --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       style="font-size:0.875rem">
                                    Téléphone
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"
                                          style="background:#F9FAFB;border-color:#E5E7EB;
                                                 font-weight:600;color:#374151">
                                        +237
                                    </span>
                                    <input type="text" id="client-telephone"
                                           name="telephone" class="form-control"
                                           placeholder="6XXXXXXXX"
                                           maxlength="9"
                                           oninput="validerTelephone(this)">
                                </div>
                                <div class="field-error" id="err-telephone">
                                    Le numéro doit commencer par 6 et avoir 9 chiffres
                                </div>
                                <small style="color:#9CA3AF;font-size:0.72rem">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Doit commencer par <strong>6</strong>
                                    et faire exactement <strong>9 chiffres</strong>
                                </small>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"
                                       style="font-size:0.875rem">
                                    Email
                                </label>
                                <input type="email" id="client-email"
                                       name="email" class="form-control"
                                       placeholder="email@exemple.com"
                                       oninput="validerEmail(this)">
                                <div class="field-error" id="err-email">
                                    Adresse email invalide
                                </div>
                            </div>

                            {{-- Adresse --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold"
                                       style="font-size:0.875rem">
                                    Adresse
                                </label>
                                <textarea id="client-adresse" name="adresse"
                                          class="form-control" rows="2"
                                          placeholder="Quartier, ville..."></textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Section 2 : TVA --}}
                    <div class="form-section-modal">
                        <div class="form-section-modal-title">
                            <i class="bi bi-percent me-1"></i> Statut TVA
                        </div>

                        <div style="background:white;border-radius:8px;padding:14px;
                                    border:1px solid #F3F4F6">
                            <div class="d-flex align-items-center
                                        justify-content-between">
                                <div>
                                    <div class="fw-semibold"
                                         style="color:#111827;font-size:0.875rem">
                                        Client exonéré de TVA
                                    </div>
                                    <div style="font-size:0.75rem;color:#9CA3AF;
                                                margin-top:2px">
                                        Si activé, la TVA ne s'applique jamais
                                        sur ses ventes
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="client-exonere-tva"
                                           name="exonere_tva"
                                           value="1" role="switch"
                                           style="width:44px;height:22px;cursor:pointer"
                                           onchange="toggleExonerationModal()">
                                </div>
                            </div>
                        </div>

                        {{-- Numéro exonération --}}
                        <div id="champ-exo-modal" style="display:none;margin-top:12px">
                            <label class="form-label fw-semibold"
                                   style="font-size:0.875rem">
                                Numéro d'exonération
                            </label>
                            <input type="text" id="client-numero-exo"
                                   name="numero_exoneration"
                                   class="form-control"
                                   placeholder="Ex : EXO-2024-00123">
                            <small style="color:#9CA3AF;font-size:0.72rem">
                                Numéro officiel d'exonération TVA
                            </small>
                        </div>

                        {{-- Info TVA normale --}}
                        <div id="info-tva-modal" style="margin-top:12px">
                            <div style="background:#FFF7ED;border:1px solid #FED7AA;
                                        border-radius:8px;padding:10px 14px;
                                        font-size:0.82rem;color:#92400E">
                                <i class="bi bi-info-circle me-2"
                                   style="color:#F97316"></i>
                                Ce client sera soumis à la TVA de
                                <strong>19,25%</strong> si activée lors
                                de la vente.
                            </div>
                        </div>

                    </div>

                    {{-- Erreur générale --}}
                    <div id="modal-erreur"
                         style="display:none;background:#FEF2F2;border:1px solid #FECACA;
                                border-radius:8px;padding:12px 16px;
                                font-size:0.875rem;color:#991B1B">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <span id="modal-erreur-texte"></span>
                    </div>

                </form>
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                        style="border-radius:8px">
                    Annuler
                </button>
                <button type="button" id="btn-sauvegarder"
                        onclick="sauvegarderClient()"
                        style="background:linear-gradient(135deg,#F97316,#EA580C);
                               color:white;border:none;border-radius:8px;
                               padding:10px 24px;font-weight:600;
                               cursor:pointer;min-width:140px">
                    <span id="btn-texte">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </span>
                    <span id="btn-loader" style="display:none">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Enregistrement...
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal suppression --}}
<div class="modal fade" id="modalSupprimer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div style="width:60px;height:60px;background:#FEF2F2;border-radius:50%;
                            display:flex;align-items:center;justify-content:center;
                            margin:0 auto 16px">
                    <i class="bi bi-trash" style="color:#EF4444;font-size:1.5rem"></i>
                </div>
                <h5 class="fw-bold" style="color:#111827">Supprimer le client ?</h5>
                <p style="color:#6B7280;font-size:0.875rem" id="texte-suppression">
                    Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal" style="border-radius:8px">
                    Annuler
                </button>
                <form id="form-suppression" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="background:#EF4444;color:white;border:none;
                                   border-radius:8px;padding:8px 24px;
                                   font-weight:600;cursor:pointer">
                        <i class="bi bi-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Recherche ──
    const input = document.getElementById('recherche-clients');
    if (input) {
        input.addEventListener('input', function() {
            const q    = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#tableau-clients tbody tr');
            let visible = 0;
            rows.forEach(function(row) {
                const match = row.textContent.toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('compteur-clients').textContent = visible;
        });
    }

});

// ════════════════════════
// VALIDATION TÉLÉPHONE
// ════════════════════════
function validerTelephone(input) {
    // Bloquer tout sauf les chiffres
    input.value = input.value.replace(/[^0-9]/g, '');

    // Bloquer si le premier chiffre n'est pas 6
    if (input.value.length > 0 && input.value[0] !== '6') {
        input.value = '';
    }

    return /^6[0-9]{8}$/.test(input.value);
}

// ════════════════════════
// VALIDATION EMAIL
// ════════════════════════
function validerEmail(input) {
    const val = input.value.trim();
    const err = document.getElementById('err-email');

    if (val.length === 0) {
        input.classList.remove('input-invalid', 'input-valid');
        err.classList.remove('show');
        return true;
    }

    const valide = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);

    if (valide) {
        input.classList.remove('input-invalid');
        input.classList.add('input-valid');
        err.classList.remove('show');
    } else {
        input.classList.remove('input-valid');
        input.classList.add('input-invalid');
        err.classList.add('show');
    }

    return valide;
}

// ════════════════════════
// TOGGLE EXONÉRATION TVA
// ════════════════════════
function toggleExonerationModal() {
    const check    = document.getElementById('client-exonere-tva');
    const champExo = document.getElementById('champ-exo-modal');
    const infoTVA  = document.getElementById('info-tva-modal');

    champExo.style.display = check.checked ? 'block' : 'none';
    infoTVA.style.display  = check.checked ? 'none'  : 'block';
}

// ════════════════════════
// OUVRIR MODAL CRÉATION
// ════════════════════════
function ouvrirModalCreation() {
    // Réinitialiser le formulaire
    document.getElementById('form-client').reset();
    document.getElementById('client-id').value     = '';
    document.getElementById('client-method').value = 'POST';
    document.getElementById('modal-titre').textContent = 'Nouveau client';
    document.getElementById('btn-texte').innerHTML =
        '<i class="bi bi-check-lg me-1"></i> Enregistrer';

    // Réinitialiser les erreurs
    document.querySelectorAll('.field-error').forEach(e => e.classList.remove('show'));
    document.querySelectorAll('.input-invalid, .input-valid').forEach(e => {
        e.classList.remove('input-invalid', 'input-valid');
    });
    document.getElementById('modal-erreur').style.display = 'none';
    document.getElementById('champ-exo-modal').style.display = 'none';
    document.getElementById('info-tva-modal').style.display  = 'block';

    new bootstrap.Modal(document.getElementById('modalClient')).show();
}

// ════════════════════════
// OUVRIR MODAL MODIFICATION
// ════════════════════════
function ouvrirModalModification(id, nom, type, telephone, email, adresse, exonereTva, numeroExo) {
    document.getElementById('client-id').value        = id;
    document.getElementById('client-method').value    = 'PUT';
    document.getElementById('modal-titre').textContent = 'Modifier : ' + nom;
    document.getElementById('btn-texte').innerHTML    =
        '<i class="bi bi-check-lg me-1"></i> Mettre à jour';

    // Remplir les champs
    document.getElementById('client-nom').value       = nom;
    document.getElementById('client-type').value      = type;
    document.getElementById('client-telephone').value = telephone || '';
    document.getElementById('client-email').value     = email || '';
    document.getElementById('client-adresse').value   = adresse || '';

    // TVA
    const checkTVA = document.getElementById('client-exonere-tva');
    checkTVA.checked = exonereTva == 1;
    document.getElementById('client-numero-exo').value = numeroExo || '';
    document.getElementById('champ-exo-modal').style.display =
        exonereTva == 1 ? 'block' : 'none';
    document.getElementById('info-tva-modal').style.display =
        exonereTva == 1 ? 'none' : 'block';

    // Réinitialiser erreurs
    document.querySelectorAll('.field-error').forEach(e => e.classList.remove('show'));
    document.querySelectorAll('.input-invalid, .input-valid').forEach(e => {
        e.classList.remove('input-invalid', 'input-valid');
    });
    document.getElementById('modal-erreur').style.display = 'none';

    new bootstrap.Modal(document.getElementById('modalClient')).show();
}

// ════════════════════════
// SAUVEGARDER CLIENT (AJAX)
// ════════════════════════
function sauvegarderClient() {
    const id       = document.getElementById('client-id').value;
    const methode  = document.getElementById('client-method').value;
    const nom      = document.getElementById('client-nom').value.trim();
    const type     = document.getElementById('client-type').value;
    const tel      = document.getElementById('client-telephone').value.trim();
    const email    = document.getElementById('client-email').value.trim();
    const adresse  = document.getElementById('client-adresse').value.trim();
    const exo      = document.getElementById('client-exonere-tva').checked;
    const numExo   = document.getElementById('client-numero-exo').value.trim();

    // ── Validation ──
    let valide = true;

    // Nom obligatoire
    const errNom = document.getElementById('err-nom');
    if (!nom) {
        errNom.classList.add('show');
        document.getElementById('client-nom').classList.add('input-invalid');
        valide = false;
    } else {
        errNom.classList.remove('show');
        document.getElementById('client-nom').classList.remove('input-invalid');
    }

    // Téléphone si renseigné
    if (tel && !validerTelephone(document.getElementById('client-telephone'))) {
        valide = false;
    }

    // Email si renseigné
    if (email && !validerEmail(document.getElementById('client-email'))) {
        valide = false;
    }

    if (!valide) return;

    // ── Afficher loader ──
    document.getElementById('btn-texte').style.display  = 'none';
    document.getElementById('btn-loader').style.display = 'inline';
    document.getElementById('btn-sauvegarder').disabled = true;
    document.getElementById('modal-erreur').style.display = 'none';

    // ── URL et méthode ──
    const url = id
        ? `/clients/${id}`
        : `/clients`;

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    if (methode === 'PUT') formData.append('_method', 'PUT');
    formData.append('nom',      nom);
    formData.append('type',     type);
    formData.append('telephone', tel);
    formData.append('email',    email);
    formData.append('adresse',  adresse);
    if (exo) {
        formData.append('exonere_tva', '1');
        formData.append('numero_exoneration', numExo);
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) {
        if (response.ok || response.redirected) {
            // Succès → recharger la page
            window.location.reload();
        } else {
            return response.json().then(function(data) {
                throw new Error(data.message || 'Erreur lors de l\'enregistrement');
            });
        }
    })
    .catch(function(err) {
        document.getElementById('modal-erreur-texte').textContent = err.message;
        document.getElementById('modal-erreur').style.display = 'block';
    })
    .finally(function() {
        document.getElementById('btn-texte').style.display  = 'inline';
        document.getElementById('btn-loader').style.display = 'none';
        document.getElementById('btn-sauvegarder').disabled = false;
    });
}

// ════════════════════════
// OUVRIR MODAL SUPPRESSION
// ════════════════════════
function ouvrirModalSuppression(id, nom) {
    document.getElementById('texte-suppression').textContent =
        `Voulez-vous vraiment supprimer le client "${nom}" ? Cette action est irréversible.`;
    document.getElementById('form-suppression').action = `/clients/${id}`;
    new bootstrap.Modal(document.getElementById('modalSupprimer')).show();
}
</script>
@endpush