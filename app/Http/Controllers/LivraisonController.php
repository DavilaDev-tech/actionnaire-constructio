<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Vente;
use App\Models\Client;
use App\Mail\LivraisonEnCoursMail;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LivraisonController extends Controller
{
    // ── Changer le statut ──
    public function changerStatut(Request $request, Livraison $livraison)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,livree',
        ]);

        $ancienStatut = $livraison->statut;
        $livraison->update(['statut' => $request->statut]);

        // ── Notifier le client quand livraison passe en cours ──
        if ($request->statut === 'en_cours' && $ancienStatut !== 'en_cours') {
            $livraison->load(['vente.details.produit', 'client']);
            $this->notifierClientEnCours($livraison);
        }

        // ── Notifier le client quand livraison est livrée ──
        if ($request->statut === 'livree' && $ancienStatut !== 'livree') {
            $livraison->load(['vente.details.produit', 'client']);
            $this->notifierClientLivree($livraison);
            $livraison->vente->update(['statut' => 'livree']);
        }

        return back()->with('success', 'Statut de livraison mis à jour !');
    }

    // ── Notifier client : livraison en cours ──
    private function notifierClientEnCours(Livraison $livraison): void
    {
        $client = $livraison->client;

        // ── Email ──
        if ($client->email) {
            try {
                Mail::to($client->email)
                    ->send(new LivraisonEnCoursMail($livraison));
                Log::info("Email envoyé à {$client->email} pour livraison #{$livraison->id}");
            } catch (\Exception $e) {
                Log::error("Erreur email livraison #{$livraison->id} : " . $e->getMessage());
            }
        }

        // ── SMS ──
        if ($client->telephone) {
            try {
                $sms     = new SmsService();
                $message = "Bonjour {$client->nom}, votre commande "
                         . $livraison->vente->numero_vente
                         . " est en cours de livraison a l'adresse : "
                         . $livraison->adresse_livraison
                         . ". Actionnaire Construction.";
                $sms->envoyer($client->telephone, $message);
                Log::info("SMS envoyé à {$client->telephone} pour livraison #{$livraison->id}");
            } catch (\Exception $e) {
                Log::error("Erreur SMS livraison #{$livraison->id} : " . $e->getMessage());
            }
        }
    }

    // ── Notifier client : livraison livrée ──
    private function notifierClientLivree(Livraison $livraison): void
    {
        $client = $livraison->client;

        // ── SMS livraison terminée ──
        if ($client->telephone) {
            try {
                $sms     = new SmsService();
                $message = "Bonjour {$client->nom}, votre commande "
                         . $livraison->vente->numero_vente
                         . " a ete livree avec succes. "
                         . "Merci pour votre confiance ! "
                         . "Actionnaire Construction.";
                $sms->envoyer($client->telephone, $message);
                Log::info("SMS livraison terminée envoyé à {$client->telephone}");
            } catch (\Exception $e) {
                Log::error("Erreur SMS livraison terminée : " . $e->getMessage());
            }
        }
    }

    // ── Liste des livraisons ──
    public function index()
    {
        $livraisons = Livraison::with(['vente', 'client'])
                               ->latest()
                               ->paginate(10);

        $totalLivraisons = Livraison::count();
        $enAttente       = Livraison::where('statut', 'en_attente')->count();
        $enCours         = Livraison::where('statut', 'en_cours')->count();
        $livrees         = Livraison::where('statut', 'livree')->count();

        return view('livraisons.index', compact(
            'livraisons', 'totalLivraisons',
            'enAttente', 'enCours', 'livrees'
        ));
    }

    // ── Carte des livraisons ──
    public function carte()
    {
        $livraisons = Livraison::with(['vente', 'client'])
                               ->whereNotNull('latitude')
                               ->whereNotNull('longitude')
                               ->get();

        $livraisonsSansCoord = Livraison::with(['vente', 'client'])
                                        ->whereNull('latitude')
                                        ->orWhereNull('longitude')
                                        ->get();

        $stats = [
            'total'      => Livraison::count(),
            'en_attente' => Livraison::where('statut', 'en_attente')->count(),
            'en_cours'   => Livraison::where('statut', 'en_cours')->count(),
            'livrees'    => Livraison::where('statut', 'livree')->count(),
        ];

        return view('livraisons.carte', compact(
            'livraisons', 'livraisonsSansCoord', 'stats'
        ));
    }

    // ── Géocoder une adresse ──
    public function geocoder(Request $request)
    {
        $adresse = $request->get('adresse');

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Actionnaire-Construction/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q'              => $adresse . ', Cameroun',
                'format'         => 'json',
                'limit'          => 1,
                'addressdetails' => 1,
            ]);

            $data = $response->json();

            if (!empty($data)) {
                return response()->json([
                    'success'   => true,
                    'latitude'  => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                    'display'   => $data[0]['display_name'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Adresse introuvable',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ]);
        }
    }

    // ── Sauvegarder coordonnées ──
    public function sauvegarderCoordonnees(Request $request, Livraison $livraison)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $livraison->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Formulaire création ──
    public function create()
    {
        $ventes = Vente::where('statut', 'confirmee')
                       ->whereDoesntHave('livraison')
                       ->with('client')
                       ->get();

        return view('livraisons.create', compact('ventes'));
    }

    // ── Enregistrement ──
    public function store(Request $request)
    {
        $request->validate([
            'vente_id'          => 'required|exists:ventes,id',
            'adresse_livraison' => 'required|string|max:500',
            'date_livraison'    => 'nullable|date|after_or_equal:today',
            'note'              => 'nullable|string|max:500',
        ]);

        $vente = Vente::findOrFail($request->vente_id);

        if ($vente->livraison) {
            return back()->with('error',
                'Une livraison existe déjà pour cette vente !');
        }

        Livraison::create([
            'vente_id'          => $request->vente_id,
            'client_id'         => $vente->client_id,
            'adresse_livraison' => $request->adresse_livraison,
            'date_livraison'    => $request->date_livraison,
            'statut'            => 'en_attente',
            'note'              => $request->note,
        ]);

        return redirect()->route('livraisons.index')
                         ->with('success', 'Livraison créée avec succès !');
    }

    // ── Détail ──
    public function show(Livraison $livraison)
    {
        $livraison->load(['vente.details.produit', 'client']);
        return view('livraisons.show', compact('livraison'));
    }

    // ── Formulaire modification ──
    public function edit(Livraison $livraison)
    {
        return view('livraisons.edit', compact('livraison'));
    }

    // ── Mise à jour ──
    public function update(Request $request, Livraison $livraison)
    {
        $request->validate([
            'adresse_livraison' => 'required|string|max:500',
            'date_livraison'    => 'nullable|date',
            'note'              => 'nullable|string|max:500',
        ]);

        $livraison->update($request->only(
            'adresse_livraison', 'date_livraison', 'note'
        ));

        return redirect()->route('livraisons.show', $livraison)
                         ->with('success', 'Livraison modifiée avec succès !');
    }
}