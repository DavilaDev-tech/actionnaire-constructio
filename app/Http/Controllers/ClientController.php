<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // ── Liste ──
    public function index()
    {
        $clients       = Client::withCount('ventes')
                               ->latest()
                               ->paginate(10);
        $totalClients  = Client::count();
        $particuliers  = Client::where('type', 'particulier')->count();
        $entreprises   = Client::where('type', 'entreprise')->count();

        return view('clients.index', compact(
            'clients', 'totalClients', 'particuliers', 'entreprises'
        ));
    }

    // ── Formulaire création ──
    public function create()
    {
        return view('clients.create');
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:200',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|unique:clients,email',
            'adresse'   => 'nullable|string|max:500',
            'type'      => 'required|in:particulier,entreprise',
        ]);

        Client::create($request->only(
            'nom', 'telephone', 'email', 'adresse', 'type'
        ));

        return redirect()->route('clients.index')
                         ->with('success', 'Client créé avec succès !');
    }

    // ── Détail ──
    public function show(Client $client)
    {
        $client->load(['ventes' => function($q) {
            $q->latest()->take(5);
        }]);

        return view('clients.show', compact('client'));
    }

    // ── Formulaire modification ──
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // ── Mise à jour ──
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom'       => 'required|string|max:200',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|unique:clients,email,' . $client->id,
            'adresse'   => 'nullable|string|max:500',
            'type'      => 'required|in:particulier,entreprise',
        ]);

        $client->update($request->only(
            'nom', 'telephone', 'email', 'adresse', 'type'
        ));

        return redirect()->route('clients.index')
                         ->with('success', 'Client modifié avec succès !');
    }

    // ── Suppression ──
    public function destroy(Client $client)
    {
        if ($client->ventes()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer ce client car il a ' .
                $client->ventes()->count() . ' vente(s) associée(s) !');
        }

        $client->delete();

        return redirect()->route('clients.index')
                         ->with('success', 'Client supprimé avec succès !');
    }
}