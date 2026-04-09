<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vente extends Model
{
    use HasFactory;

    // ── Taux TVA Cameroun (fixe) ──
    const TAUX_TVA = 19.25;

    protected $fillable = [
        'client_id',
        'user_id',
        'numero_vente',
        'date_vente',
        'montant_total',  // TTC
        'montant_ht',
        'montant_tva',
        'tva_applicable',
        'taux_tva',
        'statut',
        'note',
    ];

    protected $casts = [
        'date_vente'     => 'date',
        'tva_applicable' => 'boolean',
        'montant_total'  => 'decimal:2',
        'montant_ht'     => 'decimal:2',
        'montant_tva'    => 'decimal:2',
        'taux_tva'       => 'decimal:2',
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

    // ── Calcul TVA ──
    public static function calculerTVA(float $montantHT): array
    {
        $tva = round($montantHT * (self::TAUX_TVA / 100), 2);
        return [
            'montant_ht'  => $montantHT,
            'montant_tva' => $tva,
            'montant_ttc' => round($montantHT + $tva, 2),
            'taux_tva'    => self::TAUX_TVA,
        ];
    }

    // ── Accesseurs TVA ──
    public function getMontantTtcAttribute(): float
    {
        return (float) $this->montant_total;
    }

    public function getLibelleTvaAttribute(): string
    {
        return $this->tva_applicable
            ? 'TVA ' . self::TAUX_TVA . '%'
            : 'Sans TVA';
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