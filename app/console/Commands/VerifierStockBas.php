<?php

namespace App\Console\Commands;

use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockBasNotification;
use Illuminate\Console\Command;

class VerifierStockBas extends Command
{
    protected $signature   = 'stock:verifier';
    protected $description = 'Vérifie les produits en stock bas et notifie les admins';

    public function handle(): void
    {
        $produits = Produit::whereColumn('quantite_stock', '<=', 'seuil_alerte')
                           ->orderBy('quantite_stock')
                           ->get();

        if ($produits->isEmpty()) {
            $this->info('✅ Tous les stocks sont OK.');
            return;
        }

        // Notifier tous les admins et magasiniers
        $utilisateurs = User::whereIn('role', ['admin', 'magasinier'])
                            ->where('actif', true)
                            ->get();

        foreach ($utilisateurs as $user) {
            $user->notify(new StockBasNotification($produits));
        }

        $this->info("🔔 {$produits->count()} produit(s) en stock bas. " .
                    "{$utilisateurs->count()} utilisateur(s) notifié(s).");
    }
}