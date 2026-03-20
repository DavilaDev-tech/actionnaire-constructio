<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'modele',
        'modele_id',
        'donnees_avant',
        'donnees_apres',
        'ip',
        'navigateur',
    ];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
    ];

    // ── Relations ──
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Couleur action ──
    public function getCouleurActionAttribute(): string
    {
        return match($this->action) {
            'creation'     => 'success',
            'modification' => 'warning',
            'suppression'  => 'danger',
            'connexion'    => 'primary',
            'deconnexion'  => 'secondary',
            'consultation' => 'info',
            default        => 'secondary',
        };
    }

    // ── Icône action ──
    public function getIconeActionAttribute(): string
    {
        return match($this->action) {
            'creation'     => 'bi-plus-circle',
            'modification' => 'bi-pencil',
            'suppression'  => 'bi-trash',
            'connexion'    => 'bi-box-arrow-in-right',
            'deconnexion'  => 'bi-box-arrow-left',
            'consultation' => 'bi-eye',
            default        => 'bi-activity',
        };
    }
}