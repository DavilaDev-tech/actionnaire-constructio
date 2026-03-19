<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClientsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function collection()
    {
        return Client::withCount('ventes')
                     ->withSum('ventes', 'montant_total')
                     ->orderBy('nom')
                     ->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Type',
            'Téléphone',
            'Email',
            'Adresse',
            'Nb ventes',
            'Total achats (F CFA)',
        ];
    }

    public function map($client): array
    {
        return [
            $client->nom,
            ucfirst($client->type),
            $client->telephone ?? '—',
            $client->email     ?? '—',
            $client->adresse   ?? '—',
            $client->ventes_count,
            number_format($client->ventes_sum_montant_total ?? 0, 0, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '198754'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Clients';
    }
}