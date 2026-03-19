<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained()->onDelete('restrict');
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->text('adresse_livraison');
            $table->date('date_livraison')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'livree'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
