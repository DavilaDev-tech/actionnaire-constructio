<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'numero',
        'montant',      // Ce montant est considéré comme le Hors Taxe (HT)
        'taux_tva',     // Le pourcentage (ex: 19.25)
        'montant_tva',  // La valeur calculée de la taxe
        'total_ttc',    // Le net à payer (HT + TVA)
        'statut',
        'fichier_pdf',
    ];

    /**
     * Logique automatique de calcul à la création et à la mise à jour
     */
    protected static function booted()
    {
        // Avant de créer une facture en base de données
        static::creating(function ($facture) {
            $facture->calculerTaxes();
        });

        // Avant de mettre à jour une facture (si on change le montant HT)
        static::updating(function ($facture) {
            $facture->calculerTaxes();
        });
    }

    /**
     * Fonction interne pour centraliser le calcul de la TVA
     */
    public function calculerTaxes()
    {
        // Taux par défaut (19.25% pour le Cameroun par exemple)
        // On peut le rendre configurable plus tard via config('app.tva_rate')
        $this->taux_tva = 19.25; 
        
        // Calcul du montant de la TVA
        $this->montant_tva = ($this->montant * $this->taux_tva) / 100;
        
        // Calcul du Total TTC (Net à payer)
        $this->total_ttc = $this->montant + $this->montant_tva;
    }

    // ── Relations ──

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // ── Méthodes de Calcul ──

    /**
     * Calcule le total déjà encaissé pour cette facture
     */
    public function montantPaye()
    {
        return $this->paiements()->sum('montant');
    }

    /**
     * Calcule le reste à payer basé sur le TOTAL TTC
     */
    public function resteAPayer()
    {
        // Important : On soustrait les paiements du total_ttc et non plus du montant HT
        return max(0, $this->total_ttc - $this->montantPaye());
    }

    // ── Générateur numéro facture ──

    public static function genererNumero(): string
    {
        $derniere = self::latest()->first();
        $numero   = $derniere ? intval(substr($derniere->numero, -4)) + 1 : 1;
        return 'FAC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}