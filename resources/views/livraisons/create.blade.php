@extends('layouts.app')
@section('title', 'Nouvelle Livraison')
@section('page-title', 'Nouvelle Livraison')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-truck me-2"></i>Créer une livraison
                </h5>
            </div>
            <div class="card-body">

                @if($ventes->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Aucune vente confirmée disponible pour une livraison.
                    <a href="{{ route('ventes.index') }}" class="alert-link">
                        Voir les ventes
                    </a>
                </div>
                @else
                <form action="{{ route('livraisons.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <!-- Vente -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Vente associée <span class="text-danger">*</span>
                            </label>
                            <select name="vente_id"
                                    class="form-select @error('vente_id') is-invalid @enderror"
                                    id="select-vente" required>
                                <option value="">-- Sélectionner une vente --</option>
                                @foreach($ventes as $vente)
                                    <option value="{{ $vente->id }}"
                                            data-client="{{ $vente->client->nom }}"
                                            data-adresse="{{ $vente->client->adresse }}"
                                            {{ old('vente_id')==$vente->id ? 'selected':'' }}>
                                        {{ $vente->numero_vente }} —
                                        {{ $vente->client->nom }} —
                                        {{ number_format($vente->montant_total, 0, ',', ' ') }} F CFA
                                    </option>
                                @endforeach
                            </select>
                            @error('vente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info client auto -->
                        <div class="col-12" id="info-client" style="display:none">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-person me-2"></i>
                                Client : <strong id="nom-client"></strong>
                            </div>
                        </div>

                        <!-- Adresse livraison -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Adresse de livraison <span class="text-danger">*</span>
                            </label>
                            <textarea name="adresse_livraison"
                                      class="form-control @error('adresse_livraison') is-invalid @enderror"
                                      rows="2"
                                      id="adresse-livraison"
                                      placeholder="Adresse complète de livraison..."
                                      required>{{ old('adresse_livraison') }}</textarea>
                            @error('adresse_livraison')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date livraison -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Date de livraison prévue
                            </label>
                            <input type="date" name="date_livraison"
                                   class="form-control"
                                   value="{{ old('date_livraison') }}"
                                   min="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Note -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Note</label>
                            <input type="text" name="note"
                                   class="form-control"
                                   value="{{ old('note') }}"
                                   placeholder="Instructions particulières...">
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('livraisons.index') }}"
                           class="btn btn-outline-secondary">
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
// Remplir automatiquement l'adresse du client
document.getElementById('select-vente').addEventListener('change', function() {
    const opt     = this.options[this.selectedIndex];
    const client  = opt.dataset.client  || '';
    const adresse = opt.dataset.adresse || '';

    if (client) {
        document.getElementById('nom-client').textContent     = client;
        document.getElementById('info-client').style.display  = 'block';
        document.getElementById('adresse-livraison').value    = adresse;
    } else {
        document.getElementById('info-client').style.display  = 'none';
        document.getElementById('adresse-livraison').value    = '';
    }
});
</script>
@endpush