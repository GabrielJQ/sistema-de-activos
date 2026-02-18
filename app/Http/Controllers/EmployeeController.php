<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Imports\EmployeesImport;
use App\Exports\EmployeesExport;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{
    /** Mostrar lista de empleados */
    public function index()
    {
        $activeEmployees = Employee::with('department')->where('status', 'Activo')->get();
        $inactiveEmployees = Employee::with('department')->where('status', 'Inactivo')->get();

        return view('employees.index', compact('activeEmployees', 'inactiveEmployees'));
    }

    /** Formulario para crear empleado */
    public function create()
    {
        $departments = Department::orderBy('areanom')->get();
        return view('employees.create', compact('departments'));
    }

    public function store(EmployeeRequest $request)
    {
        Employee::create($request->validated());
        return redirect()->route('employees.index')->with('success', 'Empleado creado correctamente.');
    }

    /** Formulario para editar empleado */
    public function edit(Employee $employee)
    {
        $departments = Department::orderBy('areanom')->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());
        return redirect()->route('employees.index')->with('success', 'Empleado actualizado correctamente.');
    }

    /** Eliminar empleado */
    public function destroy(Employee $employee)
    {
        // Verificar si el empleado tiene activos actualmente asignados
        $hasActiveAssets = $employee->assetAssignments()
            ->where('is_current', 'true')
            ->exists();

        if ($hasActiveAssets) {
            return redirect()->route('employees.index')
                ->with('error', 'No se puede eliminar al empleado porque tiene activos asignados. Reasigna o devuelve los activos antes de eliminarlo.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    protected function normalizeTipo($tipo)
    {
        $tipo = trim(strtoupper($tipo));

        return match ($tipo) {
            'SINDICALIZADO' => 'Sindicalizado',
            'CONFIANZA' => 'Confianza',
            'EVENTUAL' => 'Eventual',
            'HONORARIOS' => 'Honorarios',
            'OTRO' => 'Otro',
            default => 'Otro',
        };
    }

    /** Mostrar vista de importación */
    public function showImport()
    {
        return view('employees.import');
    }

    /** Importar empleados desde Excel/CSV */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        // Validar que el archivo tenga datos
        $hasData = false;

        if ($extension === 'csv') {
            $rows = array_map('str_getcsv', file($file));
            $dataRows = array_slice($rows, 1);
            $hasData = count(array_filter($dataRows, fn($row) => !empty(array_filter($row)))) > 0;
        } else {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            $dataRows = array_slice($rows, 1);
            $hasData = count(array_filter($dataRows, fn($row) => !empty(array_filter($row)))) > 0;
        }

        if (!$hasData) {
            return redirect()->back()->with('error', 'El archivo está vacío. Por favor agrega datos antes de importar.');
        }

        $filename = $file->getClientOriginalName();
        $path = $file->store('imports');

        $task = \App\Models\ImportTask::create([
            'user_id' => auth()->id(),
            'filename' => $filename,
            'status' => 'pending',
        ]);

        Excel::queueImport(new EmployeesImport($task->id), $path);

        return redirect()->route('assets.import.progress', $task->id)
            ->with('success', 'Importación de empleados iniciada en segundo plano.');
    }

    /** Exportar empleados (CSV, XLSX o PDF) */
    public function export(Request $request)
    {
        $request->validate([
            'columns' => 'nullable|array',
            'format' => 'required|in:csv,xlsx,pdf',
            'filter_column' => 'nullable|string',
            'filter_value' => 'nullable|string',
            'extra_columns' => 'nullable|array',
            'extra_columns.*.name' => 'required_with:extra_columns|string|max:50',
            'extra_columns.*.value' => 'nullable|string|max:255',
        ]);

        $columns = $request->input('columns', []);
        $format = $request->input('format');
        $filterColumn = $request->input('filter_column');
        $filterValue = $request->input('filter_value');
        $extraColumns = $request->input('extra_columns', []);

        if (empty($columns)) {
            return redirect()->back()->with('error', 'Debes seleccionar al menos una columna para exportar.');
        }

        $query = Employee::with('department');

        if ($filterColumn && $filterValue) {
            if ($filterColumn === 'department_id') {
                $query->whereHas('department', fn($q) => $q->where('areanom', 'LIKE', "%{$filterValue}%"));
            } else {
                $query->where($filterColumn, 'LIKE', "%{$filterValue}%");
            }
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No hay empleados que coincidan con el filtro.');
        }

        if ($format === 'pdf') {
            $data = $employees->map(function ($employee) use ($columns, $extraColumns) {
                /** @var \App\Models\Employee $employee */
                return $this->formatRow($employee, $columns, $extraColumns);
            });
            $pdf = Pdf::loadView('employees.pdf', ['data' => $data]);
            $fileName = 'empleados_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            return $pdf->stream($fileName);
        }

        return Excel::download(
            new EmployeesExport($columns, $filterColumn, $filterValue, $extraColumns),
            'empleados_' . now()->format('Y-m-d_H-i-s') . '.' . $format
        );
    }

    /** Formulario de exportación */
    public function exportForm()
    {
        $columns = [
            'expediente' => 'Expediente',
            'nombre' => 'Nombre',
            'apellido_pat' => 'Apellido Paterno',
            'apellido_mat' => 'Apellido Materno',
            'department_id' => 'Departamento',
            'puesto' => 'Puesto',
            'tipo' => 'Tipo',
            'email' => 'Email',
            'telefono' => 'Teléfono',
            'extension' => 'Extensión',
            'status' => 'Estado',
        ];

        $departments = Department::orderBy('areanom')->pluck('areanom', 'id');
        $tipos = Employee::select('tipo')->distinct()->pluck('tipo');
        $employees = Employee::select('id', 'nombre', 'apellido_pat', 'apellido_mat')
            ->get()
            ->mapWithKeys(fn($e) => [$e->id => $e->nombre . ' ' . $e->apellido_pat . ' ' . $e->apellido_mat]);

        return view('employees.export', compact('columns', 'departments', 'tipos', 'employees'));
    }

    /** Descargar plantilla de Excel */
    public function downloadTemplate()
    {
        $headers = [
            ['nombre', 'apellido_pat', 'apellido_mat', 'unidad', 'departamento', 'puesto', 'tipo', 'email', 'telefono', 'extension', 'status', 'curp', 'expediente']
        ];

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "plantilla_empleados_{$timestamp}.xlsx";

        $export = new class ($headers) implements FromArray {
            protected $data;
            public function __construct($data)
            {
                $this->data = $data; }
            public function array(): array
            {
                return $this->data; }
        };

        return Excel::download($export, $filename);
    }

    /** Formatear fila para PDF */
    protected function formatRow(Employee $employee, array $columns, array $extraColumns): array
    {
        $row = [];

        foreach ($columns as $col) {
            $row[$col] = match ($col) {
                'expediente' => $employee->expediente,
                'nombre' => $employee->nombre,
                'apellido_pat' => $employee->apellido_pat,
                'apellido_mat' => $employee->apellido_mat,
                'full_name' => $employee->full_name,
                'department_id' => $employee->department->areanom ?? '—',
                'puesto' => $employee->puesto,
                'tipo' => $employee->tipo,
                'email' => $employee->email,
                'telefono' => $employee->telefono,
                'extension' => $employee->extension,
                'status' => $employee->status,
                default => '',
            };
        }

        foreach ($extraColumns as $extra) {
            $row[$extra['name']] = $extra['value'] ?? '';
        }

        return $row;
    }

    /** Instrucciones PDF */
    public function downloadInstructionsPDF()
    {
        $pdf = Pdf::loadView('employees.instructions_pdf');
        return $pdf->stream('Instrucciones_Llenado_Empleados.pdf');
    }
}
