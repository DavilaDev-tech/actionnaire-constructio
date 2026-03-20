<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActiviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // ── Afficher la page de connexion ──
    public function create(): View
    {
        return view('auth.login');
    }

    // ── Traiter la connexion ──
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Enregistrer l'activité de connexion
        ActiviteService::enregistrer(
            'connexion',
            'Authentification',
            "Connexion de " . auth()->user()->nom_complet .
            " — Rôle : " . ucfirst(auth()->user()->role)
        );

        return redirect()->intended(route('dashboard', absolute: false));
    }

    // ── Traiter la déconnexion ──
    public function destroy(Request $request): RedirectResponse
    {
        // Enregistrer l'activité AVANT la déconnexion
        if (auth()->check()) {
            ActiviteService::enregistrer(
                'deconnexion',
                'Authentification',
                "Déconnexion de " . auth()->user()->nom_complet .
                " — Rôle : " . ucfirst(auth()->user()->role)
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}