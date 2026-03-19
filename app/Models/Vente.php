<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'numero_vente',
        'date_vente',
        'montant_total',
        'statut',
        'note',
    ];

    protected $casts = [
        'date_vente' => 'date',
    ];

    // ── Relations ──
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(VenteDetail::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    public function livraison()
    {
        return $this->hasOne(Livraison::class);
    }

    // ── Helpers statut ──
    public function isAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function isLivree(): bool
    {
        return $this->statut === 'livree';
    }

    // ── Générateur numéro de vente ──
    public static function genererNumero(): string
    {
            $derniere = self::latest('id')->first();
    $numero   = $derniere
        ? intval(substr($derniere->numero_vente, -4)) + 1
        : 1;
    return 'VTE-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}




