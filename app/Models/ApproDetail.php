<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApproDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'approvisionnement_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    // ── Relations ──
    public function approvisionnement()
    {
        return $this->belongsTo(Approvisionnement::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}