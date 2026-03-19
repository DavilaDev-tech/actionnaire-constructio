@extends('layouts.app')
@section('title', 'Modifier Client')
@section('page-title', 'Modifier Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil me-2"></i>Modifier : {{ $client->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $client->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select">
                                <option value="particulier"
                                    {{ old('type',$client->type)=='particulier' ? 'selected':'' }}>
                                    Particulier
                                </option>
                                <option value="entreprise"
                                    {{ old('type',$client->type)=='entreprise' ? 'selected':'' }}>
                                    Entreprise
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   value="{{ old('telephone', $client->telephone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $client->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <textarea name="adresse" class="form-control"
                                      rows="2">{{ old('adresse', $client->adresse) }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('clients.index') }}"
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