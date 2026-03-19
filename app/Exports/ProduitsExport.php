<?php

namespace App\Exports;

use App\Models\Produit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProduitsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function collection()
    {
        return Produit::with('categorie')
                      ->orderBy('nom')
                      ->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Catégorie',
            'Prix achat (F)',
            'Prix vente (F)',
            'Stock actuel',
            'Seuil alerte',
            'Unité',
            'Statut stock',
        ];
    }

    public function map($produit): array
    {
        return [
            $produit->nom,
            $produit->categorie->nom,
            number_format($produit->prix_achat, 0, ',', ' '),
            number_format($produit->prix_vente, 0, ',', ' '),
            $produit->quantite_stock,
            $produit->seuil_alerte,
            $produit->unite,
            $produit->quantite_stock == 0
                ? 'Épuisé'
                : ($produit->quantite_stock <= $produit->seuil_alerte
                    ? 'Stock bas'
                    : 'OK'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0d6efd'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Produits';
    }
}