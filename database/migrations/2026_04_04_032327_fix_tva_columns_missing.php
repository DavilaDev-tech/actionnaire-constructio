<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Ventes : ajouter colonnes TVA si elles n'existent pas ──
        Schema::table('ventes', function (Blueprint $table) {
            if (!Schema::hasColumn('ventes', 'tva_applicable')) {
                $table->boolean('tva_applicable')->default(false)->after('note');
            }
            if (!Schema::hasColumn('ventes', 'taux_tva')) {
                $table->decimal('taux_tva', 5, 2)->default(19.25)->after('tva_applicable');
            }
            if (!Schema::hasColumn('ventes', 'montant_ht')) {
                $table->decimal('montant_ht', 15, 2)->default(0)->after('taux_tva');
            }
            if (!Schema::hasColumn('ventes', 'montant_tva')) {
                $table->decimal('montant_tva', 15, 2)->default(0)->after('montant_ht');
            }
        });

        // ── Factures : ajouter colonnes TVA si elles n'existent pas ──
        Schema::table('factures', function (Blueprint $table) {
            if (!Schema::hasColumn('factures', 'tva_applicable')) {
                $table->boolean('tva_applicable')->default(false)->after('statut');
            }
            if (!Schema::hasColumn('factures', 'montant_ht')) {
                $table->decimal('montant_ht', 15, 2)->default(0)->after('tva_applicable');
            }
            if (!Schema::hasColumn('factures', 'montant_tva')) {
                $table->decimal('montant_tva', 15, 2)->default(0)->after('montant_ht');
            }
        });

        // ── Clients : ajouter colonnes TVA si elles n'existent pas ──
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'exonere_tva')) {
                $table->boolean('exonere_tva')->default(false)->after('type');
            }
            if (!Schema::hasColumn('clients', 'numero_exoneration')) {
                $table->string('numero_exoneration', 100)->nullable()->after('exonere_tva');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('ventes', 'tva_applicable')  ? 'tva_applicable'  : null,
                Schema::hasColumn('ventes', 'taux_tva')        ? 'taux_tva'        : null,
                Schema::hasColumn('ventes', 'montant_ht')      ? 'montant_ht'      : null,
                Schema::hasColumn('ventes', 'montant_tva')     ? 'montant_tva'     : null,
            ]));
        });
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('factures', 'tva_applicable') ? 'tva_applicable' : null,
                Schema::hasColumn('factures', 'montant_ht')     ? 'montant_ht'     : null,
                Schema::hasColumn('factures', 'montant_tva')    ? 'montant_tva'    : null,
            ]));
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('clients', 'exonere_tva')          ? 'exonere_tva'          : null,
                Schema::hasColumn('clients', 'numero_exoneration')   ? 'numero_exoneration'   : null,
            ]));
        });
    }
};