<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'facture_id',
        'montant',
        'mode_paiement',
        'date_paiement',
        'reference',
        'note',
        'created_by',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant'       => 'decimal:2',
    ];

    // ── Relations ──
    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accesseur libellé mode paiement ──
    public function getLibelleModeAttribute(): string
    {
        return match($this->mode_paiement) {
            'especes'      => '💵 Espèces',
            'mobile_money' => '📱 Mobile Money',
            'virement'     => '🏦 Virement',
            'cheque'       => '📄 Chèque',
            default        => $this->mode_paiement,
        };
    }

    // ── Couleur mode paiement ──
    public function getCouleurModeAttribute(): string
    {
        return match($this->mode_paiement) {
            'especes'      => 'success',
            'mobile_money' => 'primary',
            'virement'     => 'info',
            'cheque'       => 'warning',
            default        => 'secondary',
        };
    }
}