<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\User;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $query = Activite::with('user')->latest();

        // Filtres
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $activites = $query->paginate(20);
        $users     = User::orderBy('nom')->get();
        $modules   = Activite::distinct()->pluck('module');
        $actions   = ['creation', 'modification', 'suppression',
                      'connexion', 'deconnexion', 'consultation'];

        // Stats
        $stats = [
            'total'         => Activite::count(),
            'aujourd_hui'   => Activite::whereDate(
                                   'created_at', today()
                               )->count(),
            'connexions'    => Activite::where('action', 'connexion')
                                       ->whereDate('created_at', today())
                                       ->count(),
            'modifications' => Activite::where('action', 'modification')
                                       ->whereDate('created_at', today())
                                       ->count(),
        ];

        return view('activites.index', compact(
            'activites', 'users', 'modules',
            'actions', 'stats'
        ));
    }

    // ── Vider le journal ──
    public function vider()
    {
        Activite::where('created_at', '<',
            now()->subDays(30)
        )->delete();

        return back()->with('success',
            'Journal nettoyé (entrées de plus de 30 jours supprimées) !');
    }
}