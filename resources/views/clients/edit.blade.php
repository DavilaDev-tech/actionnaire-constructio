@extends('layouts.app')
@section('title', 'Modifier Client')
@section('page-title', 'Modifier Client')

@push('styles')
<style>
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

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- En-tête --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold" style="color:#111827">
                    Modifier le client
                </h4>
                <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
                    {{ $client->nom }}
                </p>
            </div>
            @if($client->exonere_tva)
            <span style="background:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;
                         border-radius:20px;padding:5px 14px;font-size:0.82rem;font-weight:600">
                ⚪ Exonéré TVA
            </span>
            @else
            <span style="background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
                         border-radius:20px;padding:5px 14px;font-size:0.82rem;font-weight:600">
                🟠 Assujetti TVA
            </span>
            @endif
        </div>

        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf @method('PUT')

            {{-- ── Section 1 : Informations générales ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="bi bi-person me-2" style="color:#F97316"></i>
                    Informations générales
                </div>
                <div class="form-section-body">
                    <div class="row g-3">

                        {{-- Nom --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold"
                                   style="font-size:0.875rem">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $client->nom) }}"
                                   required>
                            @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"
                                   style="font-size:0.875rem">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-select" required>
                                <option value="particulier"
                                    {{ old('type', $client->type) == 'particulier'
                                        ? 'selected' : '' }}>
                                    Particulier
                                </option>
                                <option value="entreprise"
                                    {{ old('type', $client->type) == 'entreprise'
                                        ? 'selected' : '' }}>
                                    Entreprise
                                </option>
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
                                             font-weight:600;color:#374151;
                                             font-size:0.875rem">
                                    +237
                                </span>
                                <input type="text" name="telephone"
                                       class="form-control @error('telephone') is-invalid @enderror"
                                       value="{{ old('telephone', $client->telephone) }}"
                                       placeholder="6XXXXXXXX"
                                       maxlength="9"
                                       pattern="6[0-9]{8}"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small style="color:#9CA3AF;font-size:0.75rem">
                                <i class="bi bi-info-circle me-1"></i>
                                Doit commencer par <strong>6</strong> et faire 9 chiffres
                            </small>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"
                                   style="font-size:0.875rem">
                                Email
                            </label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $client->email) }}"
                                   placeholder="email@exemple.com">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Adresse --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold"
                                   style="font-size:0.875rem">
                                Adresse
                            </label>
                            <textarea name="adresse" class="form-control" rows="2"
                                      placeholder="Quartier, ville...">{{ old('adresse', $client->adresse) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ── Section 2 : Statut TVA ── --}}
            <div class="form-section">
                <div class="form-section-header">
                    <i class="bi bi-percent me-2" style="color:#F97316"></i>
                    Statut TVA
                </div>
                <div class="form-section-body">

                    {{-- Switch exonération --}}
                    <div style="background:#F9FAFB;border-radius:10px;
                                padding:16px;border:1px solid #F3F4F6">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold"
                                     style="color:#111827;font-size:0.9rem">
                                    Client exonéré de TVA
                                </div>
                                <div style="font-size:0.78rem;color:#9CA3AF;margin-top:2px">
                                    Si activé, la TVA ne sera jamais appliquée
                                    sur les ventes de ce client
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox"
                                       id="exonere_tva" name="exonere_tva"
                                       value="1" role="switch"
                                       style="width:44px;height:22px;cursor:pointer"
                                       {{ old('exonere_tva', $client->exonere_tva)
                                           ? 'checked' : '' }}
                                       onchange="toggleExoneration()">
                            </div>
                        </div>
                    </div>

                    {{-- Champ numéro exonération --}}
                    @php
                        $showExo = old('exonere_tva', $client->exonere_tva);
                    @endphp

                    <div id="champ-exoneration"
                         style="display:{{ $showExo ? 'block' : 'none' }};
                                margin-top:14px">
                        <label class="form-label fw-semibold"
                               style="font-size:0.875rem">
                            Numéro d'exonération
                        </label>
                        <input type="text" name="numero_exoneration"
                               class="form-control"
                               value="{{ old('numero_exoneration',
                                            $client->numero_exoneration) }}"
                               placeholder="Ex : EXO-2024-00123">
                        <small style="color:#9CA3AF;font-size:0.75rem">
                            <i class="bi bi-info-circle me-1"></i>
                            Numéro officiel d'exonération TVA du client
                        </small>
                    </div>

                    {{-- Info TVA normale --}}
                    <div id="info-tva-normale"
                         style="display:{{ $showExo ? 'none' : 'block' }};
                                margin-top:14px">
                        <div style="background:#FFF7ED;border:1px solid #FED7AA;
                                    border-radius:8px;padding:12px 16px">
                            <i class="bi bi-info-circle me-2"
                               style="color:#F97316"></i>
                            <span style="font-size:0.82rem;color:#92400E">
                                Ce client sera soumis à la TVA de
                                <strong>19,25%</strong> sur les ventes
                                si la TVA est activée lors de la création
                                de la vente.
                            </span>
                        </div>
                    </div>

                    {{-- Historique ventes avec TVA --}}
                    @if($client->ventes()->where('tva_applicable', true)->count() > 0)
                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;
                                border-radius:8px;padding:12px 16px;margin-top:12px">
                        <i class="bi bi-info-circle me-2"
                           style="color:#3B82F6"></i>
                        <span style="font-size:0.82rem;color:#1E40AF">
                            Ce client a
                            <strong>
                                {{ $client->ventes()
                                    ->where('tva_applicable', true)
                                    ->count() }}
                            </strong>
                            vente(s) avec TVA appliquée.
                            Modifier le statut TVA n'affectera pas
                            les ventes existantes.
                        </span>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ── Boutons ── --}}
            <div class="d-flex gap-2">
                <button type="submit"
                        style="background:linear-gradient(135deg,#F97316,#EA580C);
                               color:white;border:none;border-radius:10px;
                               padding:12px 28px;font-weight:600;font-size:0.9rem;
                               box-shadow:0 2px 8px rgba(249,115,22,0.3);
                               cursor:pointer">
                    <i class="bi bi-check-lg me-2"></i> Mettre à jour
                </button>
                <a href="{{ route('clients.index') }}"
                   class="btn btn-outline-secondary"
                   style="border-radius:10px;padding:12px 24px">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleExoneration() {
    const check       = document.getElementById('exonere_tva');
    const champExo    = document.getElementById('champ-exoneration');
    const infoNormale = document.getElementById('info-tva-normale');

    if (check.checked) {
        champExo.style.display    = 'block';
        infoNormale.style.display = 'none';
    } else {
        champExo.style.display    = 'none';
        infoNormale.style.display = 'block';
    }
}
</script>
@endpush

@endsection