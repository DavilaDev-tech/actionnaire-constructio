<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // ── Liste toutes les notifications ──
    public function index()
    {
        $notifications = auth()->user()
                               ->notifications()
                               ->paginate(15);

        // Marquer toutes comme lues
        auth()->user()->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    // ── Marquer une comme lue et rediriger ──
    public function lire(string $id)
    {
        $notification = auth()->user()
                              ->notifications()
                              ->findOrFail($id);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        return redirect($url);
    }

    // ── Marquer toutes comme lues ──
    public function toutLire()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été lues !');
    }

    // ── Supprimer une notification ──
    public function destroy(string $id)
    {
        auth()->user()
              ->notifications()
              ->findOrFail($id)
              ->delete();

        return back()->with('success', 'Notification supprimée !');
    }

    // ── API : nombre non lues (pour la cloche) ──
    public function nonLues()
    {
        return response()->json([
            'count'         => auth()->user()->unreadNotifications->count(),
            'notifications' => auth()->user()
                                     ->unreadNotifications()
                                     ->latest()
                                     ->limit(5)
                                     ->get()
                                     ->map(fn($n) => [
                                         'id'      => $n->id,
                                         'titre'   => $n->data['titre'],
                                         'message' => $n->data['message'],
                                         'url'     => $n->data['url'] ?? '#',
                                         'date'    => $n->created_at
                                                        ->diffForHumans(),
                                     ]),
        ]);
    }
}