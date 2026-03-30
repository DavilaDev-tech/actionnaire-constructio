<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ActiviteService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // ── Liste ──
   public function index(Request $request)
{
    $search = $request->get('search', '');

    $query = Client::withCount('ventes')->latest();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
        });
    }

    $clients      = $query->paginate(10)->withQueryString();
    $totalClients = Client::count();
    $particuliers = Client::where('type', 'particulier')->count();
    $entreprises  = Client::where('type', 'entreprise')->count();

    if ($request->ajax()) {
        return response()->json([
            'html' => view('clients.partials.tableau',
                compact('clients'))->render(),
            'pagination' => view('partials.pagination',
                compact('clients'))->render(),
            'total' => $clients->total(),
        ]);
    }

    return view('clients.index', compact(
        'clients', 'totalClients',
        'particuliers', 'entreprises', 'search'
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

        $client = Client::create($request->only(
            'nom', 'telephone', 'email', 'adresse', 'type'
        ));

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'creation',
            'Clients',
            "Création du client {$client->nom} — " .
            ucfirst($client->type) .
            ($client->telephone ? " — Tél : {$client->telephone}" : ''),
            'Client',
            $client->id
        );

        return redirect()->route('clients.index')
                         ->with('success', 'Client créé avec succès !');
    }

    // ── Détail ──
    public function show(Client $client)
    {
        $client->load(['ventes' => function($q) {
            $q->latest()->take(5);
        }]);

        // Enregistrer la consultation
        ActiviteService::enregistrer(
            'consultation',
            'Clients',
            "Consultation de la fiche client {$client->nom}",
            'Client',
            $client->id
        );

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

        // Sauvegarder les données avant modification
        $avant = [
            'nom'       => $client->nom,
            'telephone' => $client->telephone,
            'email'     => $client->email,
            'adresse'   => $client->adresse,
            'type'      => $client->type,
        ];

        $client->update($request->only(
            'nom', 'telephone', 'email', 'adresse', 'type'
        ));

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'modification',
            'Clients',
            "Modification du client {$client->nom}",
            'Client',
            $client->id,
            $avant,
            [
                'nom'       => $client->nom,
                'telephone' => $client->telephone,
                'email'     => $client->email,
                'adresse'   => $client->adresse,
                'type'      => $client->type,
            ]
        );

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

        $nomClient = $client->nom;
        $clientId  = $client->id;

        $client->delete();

        // Enregistrer l'activité
        ActiviteService::enregistrer(
            'suppression',
            'Clients',
            "Suppression du client {$nomClient}",
            'Client',
            $clientId
        );

        return redirect()->route('clients.index')
                         ->with('success', 'Client supprimé avec succès !');
    }
}