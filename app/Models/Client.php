<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'adresse',
        'type',
        'exonere_tva',
        'numero_exoneration',
    ];

    protected $casts = [
        'exonere_tva' => 'boolean',
    ];

    // ── Relations ──
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function livraisons()
    {
        return $this->hasMany(Livraison::class);
    }

    // ── Accesseurs ──
    public function getNombreVentesAttribute(): int
    {
        return $this->ventes()->count();
    }

    public function getTotalAchatsAttribute(): float
    {
        return $this->ventes()->sum('montant_total');
    }

    public function getLibelleTvaAttribute(): string
    {
        return $this->exonere_tva ? '⚪ Exonéré TVA' : '🟠 Assujetti TVA';
    }

    public function getTvaApplicableAttribute(): bool
    {
        return !$this->exonere_tva;
    }
}