@extends('layouts.app')
@section('title', 'Modifier utilisateur')
@section('page-title', 'Modifier Utilisateur')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Modifier : {{ $user->nom_complet }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   value="{{ old('nom', $user->nom) }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom"
                                   class="form-control @error('prenom') is-invalid @enderror"
                                   value="{{ old('prenom', $user->prenom) }}" required>
                            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   value="{{ old('telephone', $user->telephone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin"      {{ old('role',$user->role)=='admin'      ? 'selected':'' }}>Administrateur</option>
                                <option value="vendeur"    {{ old('role',$user->role)=='vendeur'    ? 'selected':'' }}>Vendeur</option>
                                <option value="magasinier" {{ old('role',$user->role)=='magasinier' ? 'selected':'' }}>Magasinier</option>
                                <option value="comptable"  {{ old('role',$user->role)=='comptable'  ? 'selected':'' }}>Comptable</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="actif" id="actif"
                                       {{ $user->actif ? 'checked' : '' }}>
                                <label class="form-check-label" for="actif">Compte actif</label>
                            </div>
                        </div>

                        <!-- Changer mot de passe (optionnel) -->
                        <div class="col-12">
                            <hr>
                            <p class="text-muted small mb-2">
                                Laisser vide pour ne pas changer le mot de passe
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmer mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg me-1"></i> Mettre à jour
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection