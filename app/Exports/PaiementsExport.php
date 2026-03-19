<?php

namespace App\Exports;

use App\Models\Paiement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PaiementsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function collection()
    {
        return Paiement::with(['facture.vente.client', 'createdBy'])
                       ->orderByDesc('date_paiement')
                       ->get();
    }

    public function headings(): array
    {
        return [
            'N° Facture',
            'Client',
            'Montant (F CFA)',
            'Mode paiement',
            'Date',
            'Référence',
            'Enregistré par',
        ];
    }

    public function map($paiement): array
    {
        $modes = [
            'especes'      => 'Espèces',
            'mobile_money' => 'Mobile Money',
            'virement'     => 'Virement',
            'cheque'       => 'Chèque',
        ];

        return [
            $paiement->facture->numero,
            $paiement->facture->vente->client->nom,
            number_format($paiement->montant, 0, ',', ' '),
            $modes[$paiement->mode_paiement] ?? $paiement->mode_paiement,
            $paiement->date_paiement->format('d/m/Y'),
            $paiement->reference ?? '—',
            $paiement->createdBy->nom_complet,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6f42c1'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Paiements';
    }
}