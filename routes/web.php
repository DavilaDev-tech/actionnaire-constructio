<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\ApprovisionnementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // ── Recherche ──
    Route::get('/recherche', [RechercheController::class, 'index'])
         ->name('recherche');
    Route::get('recherche/suggestions',
               [RechercheController::class, 'suggestions'])
         ->name('recherche.suggestions');

    // ── Dashboard ──
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // ── Catalogue (admin + magasinier) ──
    Route::middleware(['role:admin,magasinier'])->group(function () {
        Route::resource('categories', CategorieController::class)
             ->except(['show'])
             ->parameters(['categories' => 'categorie']);
        Route::resource('produits', ProduitController::class);
        Route::post('produits/{produit}/ajuster-stock',
                    [ProduitController::class, 'ajusterStock'])
             ->name('produits.ajuster-stock');
    });

    // ── Clients (admin + vendeur) ──
    Route::middleware(['role:admin,vendeur'])->group(function () {
        Route::resource('clients', ClientController::class);
    });

    // ── Fournisseurs (admin + magasinier) ──
    Route::middleware(['role:admin,magasinier'])->group(function () {
        Route::resource('fournisseurs', FournisseurController::class);
    });

    // ── Ventes (admin + vendeur) ──
    Route::middleware(['role:admin,vendeur'])->group(function () {
        Route::resource('ventes', VenteController::class)
             ->except(['edit', 'update']);
        Route::patch('ventes/{vente}/statut',
                     [VenteController::class, 'changerStatut'])
             ->name('ventes.statut');
    });

    // ── Factures (admin + vendeur + comptable) ──
    Route::middleware(['role:admin,vendeur,comptable'])->group(function () {
        Route::resource('factures', FactureController::class)
             ->only(['index', 'show']);
        Route::get('factures/{facture}/telecharger',
                   [FactureController::class, 'telecharger'])
             ->name('factures.telecharger');
        Route::patch('factures/{facture}/marquer-payee',
                     [FactureController::class, 'marquerPayee'])
             ->name('factures.marquer-payee');
    });

    // ── Paiements (admin + comptable) ──
    Route::middleware(['role:admin,comptable'])->group(function () {
        Route::resource('paiements', PaiementController::class)
             ->except(['edit', 'update']);
        Route::get('paiements-rapport',
                   [PaiementController::class, 'rapport'])
             ->name('paiements.rapport');
    });

    // ── Livraisons (admin + magasinier) ──
    Route::middleware(['role:admin,magasinier'])->group(function () {

        // Routes spéciales AVANT le resource
        Route::get('livraisons-carte',
                   [LivraisonController::class, 'carte'])
             ->name('livraisons.carte');
        Route::get('livraisons/geocoder',
                   [LivraisonController::class, 'geocoder'])
             ->name('livraisons.geocoder');
        Route::post('livraisons/{livraison}/coordonnees',
                    [LivraisonController::class, 'sauvegarderCoordonnees'])
             ->name('livraisons.coordonnees');

        // Resource APRÈS
        Route::resource('livraisons', LivraisonController::class)
             ->except(['destroy']);
        Route::patch('livraisons/{livraison}/statut',
                     [LivraisonController::class, 'changerStatut'])
             ->name('livraisons.statut');
    });

    // ── Approvisionnements (admin + magasinier) ──
    Route::middleware(['role:admin,magasinier'])->group(function () {
        Route::resource('approvisionnements', ApprovisionnementController::class)
             ->except(['edit', 'update']);
        Route::patch('approvisionnements/{approvisionnement}/statut',
                     [ApprovisionnementController::class, 'changerStatut'])
             ->name('approvisionnements.statut');
    });

    // ── Utilisateurs (admin seulement) ──
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-actif',
                     [UserController::class, 'toggleActif'])
             ->name('users.toggle-actif');
    });

    // ── Backups (admin seulement) ──
    Route::middleware(['role:admin'])->group(function () {
        Route::get('backups', [BackupController::class, 'index'])
             ->name('backups.index');
        Route::post('backups/lancer', [BackupController::class, 'lancer'])
             ->name('backups.lancer');
        Route::get('backups/telecharger', [BackupController::class, 'telecharger'])
             ->name('backups.telecharger');
        Route::delete('backups/supprimer', [BackupController::class, 'supprimer'])
             ->name('backups.supprimer');
    });

    // ── Journal activités (admin seulement) ──
    Route::middleware(['role:admin'])->group(function () {
        Route::get('activites', [ActiviteController::class, 'index'])
             ->name('activites.index');
        Route::delete('activites/vider', [ActiviteController::class, 'vider'])
             ->name('activites.vider');
    });

    // ── Exports Excel ──
    Route::middleware(['role:admin,comptable'])->group(function () {
        Route::get('export/ventes',
                   [ExportController::class, 'ventes'])
             ->name('export.ventes');
        Route::get('export/paiements',
                   [ExportController::class, 'paiements'])
             ->name('export.paiements');
    });

    Route::middleware(['role:admin,magasinier'])->group(function () {
        Route::get('export/produits',
                   [ExportController::class, 'produits'])
             ->name('export.produits');
    });

    Route::middleware(['role:admin,vendeur'])->group(function () {
        Route::get('export/clients',
                   [ExportController::class, 'clients'])
             ->name('export.clients');
    });

    // ── Notifications ──
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
             ->name('index');
        Route::get('/non-lues', [NotificationController::class, 'nonLues'])
             ->name('non-lues');
        Route::post('/{id}/lire', [NotificationController::class, 'lire'])
             ->name('lire');
        Route::post('/tout-lire', [NotificationController::class, 'toutLire'])
             ->name('tout-lire');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])
             ->name('destroy');
    });

});

require __DIR__ . '/auth.php';