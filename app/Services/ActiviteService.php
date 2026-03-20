<?php

namespace App\Services;

use App\Models\Activite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActiviteService
{
    public static function enregistrer(
        string $action,
        string $module,
        string $description,
        ?string $modele    = null,
        ?int    $modeleId  = null,
        ?array  $avant     = null,
        ?array  $apres     = null
    ): void {
        try {
            Activite::create([
                'user_id'       => Auth::id(),
                'action'        => $action,
                'module'        => $module,
                'description'   => $description,
                'modele'        => $modele,
                'modele_id'     => $modeleId,
                'donnees_avant' => $avant,
                'donnees_apres' => $apres,
                'ip'            => Request::ip(),
                'navigateur'    => substr(
                    Request::header('User-Agent') ?? '', 0, 200
                ),
            ]);
        } catch (\Exception $e) {
            // Ne pas bloquer l'application si le log échoue
        }
    }
}