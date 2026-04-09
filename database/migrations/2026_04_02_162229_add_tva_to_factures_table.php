<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
               $table->decimal('taux_tva', 5, 2)->default(env('TVA_DEFAULT_RATE', 19.25)); // Pourcentage
               $table->decimal('montant_tva', 15, 2)->default(0);  // Valeur en FCFA
               $table->decimal('total_ttc', 15, 2)->default(0);    // Net à payer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            //
        });
    }
};
