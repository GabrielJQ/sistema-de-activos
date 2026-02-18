<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    // Columnas seleccionadas dinámicamente
    protected array $columns;

    // Filtro dinámico opcional
    protected ?string $filterColumn;
    protected ?string $filterValue;

    // Columnas extra con valor fijo (ej: región, unidad, etc.)
    protected array $extraColumns;

    public function __construct(array $columns = [], ?string $filterColumn = null, ?string $filterValue = null, array $extraColumns = [])
    {
        $this->columns = $columns;
        $this->filterColumn = $filterColumn;
        $this->filterValue = $filterValue;
        $this->extraColumns = $extraColumns;
    }

    public function collection()
    {
        // Cargar relaciones necesarias para evitar N+1 queries
        $query = Employee::with('department');

        // Aplicar filtro dinámico según la columna seleccionada
        if ($this->filterColumn && $this->filterValue) {
            if ($this->filterColumn === 'department_id') {
                $query->whereHas('department', function ($q) {
                    $q->where('areanom', 'LIKE', "%{$this->filterValue}%");
                });
            } else {
                $query->where($this->filterColumn, 'LIKE', "%{$this->filterValue}%");
            }
        }

        return $query->get();
    }

    public function map($employee): array
    {
        // Construye la fila respetando el orden de columnas seleccionado
        $row = [];

        foreach ($this->columns as $col) {
            switch ($col) {
                case 'expediente': $row[] = $employee->expediente; break;
                case 'nombre': $row[] = $employee->nombre; break;
                case 'apellido_pat': $row[] = $employee->apellido_pat; break;
                case 'apellido_mat': $row[] = $employee->apellido_mat; break;
                case 'full_name': $row[] = $employee->full_name; break;
                case 'department_id': $row[] = $employee->department->areanom ?? '—'; break;
                case 'puesto': $row[] = $employee->puesto; break;
                case 'tipo': $row[] = $employee->tipo; break;
                case 'email': $row[] = $employee->email; break;
                case 'telefono': $row[] = $employee->telefono; break;
                case 'extension': $row[] = $employee->extension; break;
                case 'status': $row[] = $employee->status; break;
                default: $row[] = ''; break;
            }
        }

        // Agrega columnas fijas adicionales al final de cada fila
        foreach ($this->extraColumns as $extra) {
            $row[] = $extra['value'] ?? '';
        }

        return $row;
    }

    public function headings(): array
    {
        // Genera encabezados alineados exactamente con las columnas exportadas
        $heads = [];

        foreach ($this->columns as $col) {
            switch ($col) {
                case 'expediente': $heads[] = 'Expediente'; break;
                case 'nombre': $heads[] = 'Nombre'; break;
                case 'apellido_pat': $heads[] = 'Apellido Paterno'; break;
                case 'apellido_mat': $heads[] = 'Apellido Materno'; break;
                case 'full_name': $heads[] = 'Nombre Completo'; break;
                case 'department_id': $heads[] = 'Departamento'; break;
                case 'puesto': $heads[] = 'Puesto'; break;
                case 'tipo': $heads[] = 'Tipo'; break;
                case 'email': $heads[] = 'Correo'; break;
                case 'telefono': $heads[] = 'Teléfono'; break;
                case 'extension': $heads[] = 'Extensión'; break;
                case 'status': $heads[] = 'Estado'; break;
                default: $heads[] = ucfirst(str_replace('_', ' ', $col)); break;
            }
        }

        // Encabezados de columnas extra
        foreach ($this->extraColumns as $extra) {
            $heads[] = $extra['name'] ?? 'Columna Extra';
        }

        return $heads;
    }
}
