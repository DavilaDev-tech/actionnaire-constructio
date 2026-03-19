<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livraison extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'client_id',
        'adresse_livraison',
        'latitude',
        'longitude',
        'date_livraison',
        'statut',
        'note',
    ];

    protected $casts = [
        'date_livraison' => 'date',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    // ── Relations ──
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // ── Helpers statut ──
    public function isLivree(): bool
    {
        return $this->statut === 'livree';
    }

    public function isEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    // ── A des coordonnées GPS ──
    public function hasCoordonnees(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    // ── Couleur statut ──
    public function getCouleurStatutAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'en_cours'   => 'primary',
            'livree'     => 'success',
            default      => 'secondary',
        };
    }

    // ── Icône statut pour la carte ──
    public function getIconeStatutAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => '🟡',
            'en_cours'   => '🔵',
            'livree'     => '🟢',
            default      => '⚪',
        };
    }
}