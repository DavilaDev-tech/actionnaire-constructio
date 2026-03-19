@extends('layouts.app')
@section('title', 'Nouveau Fournisseur')
@section('page-title', 'Nouveau Fournisseur')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Créer un fournisseur
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('fournisseurs.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom') }}"
                                   placeholder="Nom de l'entreprise fournisseur"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Personne contact</label>
                            <input type="text" name="contact_personne"
                                   class="form-control"
                                   value="{{ old('contact_personne') }}"
                                   placeholder="Nom du responsable">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone"
                                   class="form-control"
                                   value="{{ old('telephone') }}"
                                   placeholder="Ex: 699000000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="email@fournisseur.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"
                                      placeholder="Adresse complète...">{{ old('adresse') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer
                        </button>
                        <a href="{{ route('fournisseurs.index') }}"
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