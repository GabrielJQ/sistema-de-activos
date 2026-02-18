<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StyledAssetsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize 
{
    protected $data;
    protected $headings;

    public function __construct(array $data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return $this->data;
    }
    // Devuelve los encabezados
    public function headings(): array
    {
        return $this->headings;
    }
    // Aplica estilos a la hoja de cálculo
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'], 
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '305496'],
                ],
            ],
        ];
    }
}
