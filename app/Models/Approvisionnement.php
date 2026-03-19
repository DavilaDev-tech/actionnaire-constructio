<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approvisionnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'fournisseur_id',
        'user_id',
        'numero',
        'date_appro',
        'montant_total',
        'statut',
        'note',
    ];

    protected $casts = [
        'date_appro' => 'date',
    ];

    // ── Relations ──
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ApproDetail::class);
    }

    // ── Helpers statut ──
    public function isRecu(): bool
    {
        return $this->statut === 'recu';
    }

    public function isAnnule(): bool
    {
        return $this->statut === 'annule';
    }

    // ── Couleur statut ──
    public function getCouleurStatutAttribute(): string
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'recu'       => 'success',
            'annule'     => 'danger',
            default      => 'secondary',
        };
    }

    // ── Générateur numéro ──
    public static function genererNumero(): string
    {
        $dernier = self::latest()->first();
        $numero  = $dernier
            ? intval(substr($dernier->numero, -4)) + 1
            : 1;
        return 'APP-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}