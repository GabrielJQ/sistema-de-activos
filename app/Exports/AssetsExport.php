<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromCollection, WithHeadings, WithMapping
{
    // Columnas seleccionadas dinámicamente por el usuario
    protected array $columns;

    // Filtro dinámico opcional
    protected ?string $filterColumn;
    protected ?string $filterValue;

    public function __construct(array $columns = [], ?string $filterColumn = null, ?string $filterValue = null)
    {
        $this->columns = $columns;
        $this->filterColumn = $filterColumn;
        $this->filterValue = $filterValue;
    }

    public function collection()
    {
        // Cargar relaciones necesarias para evitar N+1 queries
        $query = Asset::with(['deviceType', 'department', 'currentHolder.employee']);

        // Aplicar filtro dinámico según la columna seleccionada
        if ($this->filterColumn && $this->filterValue) {
            if ($this->filterColumn === 'department') {
                $query->whereHas('department', fn($q) => $q->where('areanom', 'like', "%{$this->filterValue}%"));
            } elseif ($this->filterColumn === 'resguardo') {
                $query->whereHas('currentHolder.employee', fn($q) => $q->where('full_name', 'like', "%{$this->filterValue}%"));
            } else {
                $query->where($this->filterColumn, 'like', "%{$this->filterValue}%");
            }
        }

        $assets = $query->get();

        // Evita generar archivos vacíos
        if ($assets->isEmpty()) {
            throw new \Exception("No hay activos para exportar.");
        }

        return $assets;
    }

    public function map($asset): array
    {
        // Construye cada fila respetando el orden de columnas seleccionado
        $row = [];

        foreach ($this->columns as $col) {
            switch ($col) {
                case 'tag': $row[] = $asset->tag; break;
                case 'device_type_equipo': $row[] = $asset->deviceType->equipo ?? '—'; break;
                case 'marca': $row[] = $asset->marca; break;
                case 'modelo': $row[] = $asset->modelo; break;
                case 'serie': $row[] = $asset->serie; break;
                case 'estado': $row[] = $asset->estado; break;
                case 'propiedad': $row[] = $asset->propiedad ?? '—'; break;
                case 'resguardo': $row[] = $asset->currentHolder?->employee->full_name ?? 'Informática'; break;
                case 'department': $row[] = $asset->department->areanom ?? '—'; break;
                default: $row[] = ''; break;
            }
        }

        return $row;
    }

    public function headings(): array
    {
        // Genera encabezados alineados exactamente con las columnas exportadas
        $heads = [];

        foreach ($this->columns as $col) {
            switch ($col) {
                case 'tag': $heads[] = 'TAG'; break;
                case 'device_type_equipo': $heads[] = 'Equipo'; break;
                case 'marca': $heads[] = 'Marca'; break;
                case 'modelo': $heads[] = 'Modelo'; break;
                case 'serie': $heads[] = 'Serie'; break;
                case 'estado': $heads[] = 'Estado'; break;
                case 'propiedad': $heads[] = 'Propiedad'; break;
                case 'resguardo': $heads[] = 'Resguardo'; break;
                case 'department': $heads[] = 'Departamento'; break;
                default: $heads[] = ucfirst(str_replace('_', ' ', $col)); break;
            }
        }

        return $heads;
    }
}
