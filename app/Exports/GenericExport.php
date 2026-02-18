<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Devuelve los datos a exportar
    public function array(): array
    {
        return $this->data;
    }

    // Devuelve los encabezados
    public function headings(): array
    {
        // Tomamos los encabezados del primer registro
        return array_keys($this->data[0] ?? []);
    }
}
