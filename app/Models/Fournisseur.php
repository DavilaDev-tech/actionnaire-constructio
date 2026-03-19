<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'adresse',
        'contact_personne',
    ];

    // ── Relations ──
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function approvisionnements()
    {
        return $this->hasMany(Approvisionnement::class);
    }

    // ── Accesseurs ──
    public function getNombreProduitsAttribute(): int
    {
        return $this->produits()->count();
    }

    public function getNombreApprosAttribute(): int
    {
        return $this->approvisionnements()->count();
    }
}