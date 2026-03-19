<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    // ── Liste des sauvegardes ──
   public function index()
{
    $backups = [];
    $disk    = Storage::disk('local');

    // Chercher dans tous les sous-dossiers possibles
    $appName  = config('app.name');
    $chemins  = [
        'private/' . $appName,
        'private/' . str_replace(' ', '-', $appName),
        'Laravel/' . $appName,
        $appName,
    ];

    $path = null;
    foreach ($chemins as $chemin) {
        if ($disk->exists($chemin)) {
            $path = $chemin;
            break;
        }
    }

    if ($path) {
        $files = $disk->files($path);
        foreach ($files as $file) {
            if (str_ends_with($file, '.zip')) {
                $backups[] = [
                    'nom'    => basename($file),
                    'taille' => $this->formaterTaille($disk->size($file)),
                    'date'   => date('d/m/Y H:i', $disk->lastModified($file)),
                    'path'   => $file,
                ];
            }
        }
        rsort($backups);
    }

    return view('backups.index', compact('backups'));
}
    // ── Lancer une sauvegarde manuellement ──
  
 public function lancer()
{
    try {
        // Lancer via un processus séparé comme le terminal
        $phpPath    = 'C:/xampp/php/php.exe';
        $artisan    = base_path('artisan');
        $command    = "{$phpPath} {$artisan} backup:run --only-db";

        $output     = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        $outputStr = implode("\n", $output);

        if (str_contains($outputStr, 'Backup completed')) {
            return back()->with('success',
                '✅ Sauvegarde effectuée avec succès !');
        }

        return back()->with('error',
            '❌ Erreur lors de la sauvegarde. Vérifiez les logs.');

    } catch (\Exception $e) {
        return back()->with('error',
            '❌ Erreur : ' . $e->getMessage());
    }
}
    // ── Télécharger une sauvegarde ──
    public function telecharger(Request $request)
    {
        $path = $request->get('path');
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            return back()->with('error', 'Fichier introuvable !');
        }

        return response()->download(
            storage_path('app/' . $path),
            basename($path)
        );
    }

    // ── Supprimer une sauvegarde ──
    public function supprimer(Request $request)
    {
        $path = $request->get('path');
        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            $disk->delete($path);
            return back()->with('success', 'Sauvegarde supprimée !');
        }

        return back()->with('error', 'Fichier introuvable !');
    }

    // ── Formater la taille ──
    private function formaterTaille(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}