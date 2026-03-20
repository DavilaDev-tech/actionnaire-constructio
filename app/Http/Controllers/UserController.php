<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActiviteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // ── Liste des utilisateurs ──
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    // ── Formulaire de création ──
    public function create()
    {
        return view('users.create');
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role'      => 'required|in:admin,vendeur,magasinier,comptable',
            'telephone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $request->telephone,
        ]);

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'creation',
            'Utilisateurs',
            "Création de l'utilisateur {$user->nom_complet} — " .
            "Rôle : " . ucfirst($user->role) .
            " — Email : {$user->email}",
            'User',
            $user->id
        );

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur créé avec succès !');
    }

    // ── Affichage d'un utilisateur ──
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // ── Formulaire de modification ──
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // ── Mise à jour ──
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,vendeur,magasinier,comptable',
            'telephone' => 'nullable|string|max:20',
        ]);

        // Sauvegarder les données avant modification
        $avant = [
            'nom'       => $user->nom,
            'prenom'    => $user->prenom,
            'email'     => $user->email,
            'role'      => $user->role,
            'telephone' => $user->telephone,
            'actif'     => $user->actif,
        ];

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'role'      => $request->role,
            'telephone' => $request->telephone,
            'actif'     => $request->has('actif'),
        ]);

        // Changer le mot de passe si renseigné
        $mdpChange = false;
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
            $mdpChange = true;
        }

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Utilisateurs',
            "Modification de l'utilisateur {$user->nom_complet}" .
            ($mdpChange ? " — Mot de passe modifié" : "") .
            ($avant['role'] !== $user->role
                ? " — Rôle : " . ucfirst($avant['role']) .
                  " → " . ucfirst($user->role)
                : ""),
            'User',
            $user->id,
            $avant,
            [
                'nom'       => $user->nom,
                'prenom'    => $user->prenom,
                'email'     => $user->email,
                'role'      => $user->role,
                'telephone' => $user->telephone,
                'actif'     => $user->actif,
            ]
        );

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur modifié avec succès !');
    }

    // ── Suppression ──
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error',
                'Vous ne pouvez pas supprimer votre propre compte !');
        }

        $nomUser = $user->nom_complet;
        $userId  = $user->id;
        $role    = $user->role;

        $user->delete();

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'suppression',
            'Utilisateurs',
            "Suppression de l'utilisateur {$nomUser} — Rôle : " . ucfirst($role),
            'User',
            $userId
        );

        return redirect()->route('users.index')
                         ->with('success', 'Utilisateur supprimé avec succès !');
    }

    // ── Activer / Désactiver ──
    public function toggleActif(User $user)
    {
        $user->update(['actif' => !$user->actif]);
        $msg = $user->actif ? 'activé' : 'désactivé';

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Utilisateurs',
            "Compte de {$user->nom_complet} " . $msg,
            'User',
            $user->id
        );

        return back()->with('success', "Utilisateur {$msg} avec succès !");
    }
}