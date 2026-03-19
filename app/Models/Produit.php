<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_id',
        'fournisseur_id',
        'nom',
        'description',
        'prix_achat',
        'prix_vente',
        'quantite_stock',
        'seuil_alerte',
        'unite',
        'image',
    ];

    // ── Relations ──
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function venteDetails()
    {
        return $this->hasMany(VenteDetail::class);
    }

    public function approDetails()
    {
        return $this->hasMany(ApproDetail::class);
    }

    // ── Helpers stock ──
    public function isStockBas(): bool
    {
        return $this->quantite_stock <= $this->seuil_alerte;
    }

    public function isStockEpuise(): bool
    {
        return $this->quantite_stock <= 0;
    }

    // ── Accesseur statut stock ──
    public function getStatutStockAttribute(): string
    {
        if ($this->isStockEpuise()) return 'épuisé';
        if ($this->isStockBas())    return 'bas';
        return 'disponible';
    }
}