<?php

namespace App\Notifications;

use App\Models\Produit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StockBasNotification extends Notification
{
    use Queueable;

    protected Collection $produits;

    public function __construct(Collection $produits)
    {
        $this->produits = $produits;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titre'    => 'Alerte stock bas',
            'message'  => $this->produits->count() . ' produit(s) ont un stock bas ou épuisé.',
            'produits' => $this->produits->map(fn($p) => [
                'id'             => $p->id,
                'nom'            => $p->nom,
                'quantite_stock' => $p->quantite_stock,
                'seuil_alerte'   => $p->seuil_alerte,
                'statut'         => $p->quantite_stock == 0 ? 'epuise' : 'bas',
            ])->toArray(),
            'url' => route('produits.index'),
        ];
    }
}