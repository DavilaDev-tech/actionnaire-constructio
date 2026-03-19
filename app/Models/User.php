<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'telephone',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'actif'             => 'boolean',
        ];
    }

    // ── Accesseur : nom complet ──
    public function getNomCompletAttribute(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }

    // ── Helpers rôles ──
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendeur(): bool
    {
        return $this->role === 'vendeur';
    }

    public function isMagasinier(): bool
    {
        return $this->role === 'magasinier';
    }

    public function isComptable(): bool
    {
        return $this->role === 'comptable';
    }

    // ── Relations ──
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'created_by');
    }

    public function approvisionnements()
    {
        return $this->hasMany(Approvisionnement::class);
    }
}