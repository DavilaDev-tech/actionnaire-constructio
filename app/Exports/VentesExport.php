<?php

namespace App\Exports;

use App\Models\Vente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VentesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected $dateDebut;
    protected $dateFin;

    public function __construct($dateDebut = null, $dateFin = null)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin   = $dateFin;
    }

    public function collection()
    {
        $query = Vente::with(['client', 'user', 'details'])
                      ->orderByDesc('date_vente');

        if ($this->dateDebut && $this->dateFin) {
            $query->whereBetween('date_vente',
                [$this->dateDebut, $this->dateFin]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'N° Vente',
            'Date',
            'Client',
            'Téléphone client',
            'Nb articles',
            'Montant total (F CFA)',
            'Statut',
            'Vendeur',
        ];
    }

    public function map($vente): array
    {
        return [
            $vente->numero_vente,
            $vente->date_vente->format('d/m/Y'),
            $vente->client->nom,
            $vente->client->telephone ?? '—',
            $vente->details->count(),
            number_format($vente->montant_total, 0, ',', ' '),
            ucfirst(str_replace('_', ' ', $vente->statut)),
            $vente->user->nom_complet,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1a3c5e'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Ventes';
    }
}