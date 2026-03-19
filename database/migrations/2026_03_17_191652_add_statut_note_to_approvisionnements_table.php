<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approvisionnements', function (Blueprint $table) {
            $table->enum('statut', [
                'en_attente',
                'recu',
                'annule'
            ])->default('en_attente')->after('montant_total');
            $table->text('note')->nullable()->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('approvisionnements', function (Blueprint $table) {
            $table->dropColumn(['statut', 'note']);
        });
    }
};