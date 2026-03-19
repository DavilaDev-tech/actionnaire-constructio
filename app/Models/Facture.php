<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'numero',
        'montant',
        'statut',
        'fichier_pdf',
    ];

    // ── Relations ──
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // ── Générateur numéro facture ──
    public static function genererNumero(): string
    {
        $derniere = self::latest()->first();
        $numero   = $derniere ? intval(substr($derniere->numero, -4)) + 1 : 1;
        return 'FAC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
