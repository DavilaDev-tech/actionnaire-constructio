<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // On inclut 'annulee' pour correspondre à tes données existantes (Ligne ID 5)
        // et on ajoute 'partiellement_payee' pour tes nouveaux besoins.
        DB::statement("ALTER TABLE factures MODIFY COLUMN statut ENUM('non_payee', 'partiellement_payee', 'payee', 'annulee') DEFAULT 'non_payee'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retour à l'état initial si nécessaire
        DB::statement("ALTER TABLE factures MODIFY COLUMN statut ENUM('non_payee', 'payee', 'annulee') DEFAULT 'non_payee'");
    }
};