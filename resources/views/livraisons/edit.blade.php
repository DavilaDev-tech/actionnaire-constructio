@extends('layouts.app')
@section('title', 'Modifier Livraison')
@section('page-title', 'Modifier Livraison')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Modifier Livraison #{{ $livraison->id }}
                </h5>
            </div>
            <div class="card-body">

                <!-- Info non modifiable -->
                <div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Vente :</strong>
                            {{ $livraison->vente->numero_vente }}
                        </div>
                        <div class="col-md-6">
                            <strong>Client :</strong>
                            {{ $livraison->client->nom }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('livraisons.update', $livraison) }}"
                      method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Adresse de livraison <span class="text-danger">*</span>
                            </label>
                            <textarea name="adresse_livraison"
                                      class="form-control @error('adresse_livraison') is-invalid @enderror"
                                      rows="2" required>{{ old('adresse_livraison', $livraison->adresse_livraison) }}</textarea>
                            @error('adresse_livraison')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Date de livraison prévue
                            </label>
                            <input type="date" name="date_livraison"
                                   class="form-control"
                                   value="{{ old('date_livraison',
                                       $livraison->date_livraison?->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Note</label>
                            <input type="text" name="note" class="form-control"
                                   value="{{ old('note', $livraison->note) }}"
                                   placeholder="Instructions particulières...">
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('livraisons.show', $livraison) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection