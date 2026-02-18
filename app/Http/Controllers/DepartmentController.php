<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Address;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\StructureImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    // Mostrar lista de departamentos
    public function index(Request $request)
    {
        // Carga normal de departamentos
        $departments = Department::with(['address', 'unit.region'])
            ->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where('areanom', 'like', "%{$search}%")
                ->orWhereHas('unit', fn($u) => $u->where('uninom', 'like', "%{$search}%"))
                ->orWhereHas('address', fn($a) => $a->where('calle', 'like', "%{$search}%")
            ->orWhere('colonia', 'like', "%{$search}%")
            ->orWhere('cp', 'like', "%{$search}%"));
        })
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
            ->orderBy('areanom')
            ->paginate(6)
            ->withQueryString();

        //Cargar estructura organizacional
        $organizacion = \App\Models\Region::with([
            'units.departments' => fn($d) => $d->orderBy('areanom')
        ])
            ->orderBy('regnom')
            ->get();

        $estructura = $organizacion->map(function ($region) {
            return [
            'region' => strtolower($region->regnom),
            'units' => $region->units->map(function ($unit) {
                    return [
                    'name' => strtolower($unit->uninom),
                    'label' => $unit->uninom,
                    'departments' => $unit->departments->map(function ($dept) {
                            return [
                            'name' => strtolower($dept->areanom),
                            'label' => $dept->areanom
                            ];
                        }
                        )
                        ];
                    }
                    )
                    ];
                });

        return view('departments.index', [
            'departments' => $departments,
            'organizacion' => $organizacion,
            'estructura' => $estructura, // ← ADD
        ]);

    }

    // Mostrar formulario para crear un nuevo departamento
    public function create()
    {
        $units = Unit::all(); // Para elegir la unidad
        $addresses = Address::orderBy('municipio')->get();

        return view('departments.create', compact('units', 'addresses'));
    }

    // Almacenar un nuevo departamento
    public function store(DepartmentRequest $request)
    {
        // La validación ya se hizo automáticamente en DepartmentRequest

        if (!$request->address_id) {
            $address = Address::create($request->only(['calle', 'colonia', 'cp', 'municipio', 'ciudad', 'estado']));
            $address_id = $address->id;
        }
        else {
            $address_id = $request->address_id;
        }

        Department::create([
            'areacve' => $request->areacve,
            'areanom' => $request->areanom,
            'tipo' => $request->tipo,
            'unit_id' => $request->unit_id,
            'address_id' => $address_id,
        ]);

        return redirect()->route('departments.index')->with('success', 'Departamento creado correctamente.');
    }

    public function show(Department $department)
    {
        $department->load(['address', 'unit', 'employees', 'assets']);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $department->load(['address', 'unit']);
        $units = Unit::all();
        return view('departments.edit', compact('department', 'units'));
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        // La validación ya se hizo automáticamente en DepartmentRequest

        $address = null;
        if ($request->filled(['calle', 'colonia', 'cp'])) {
            $address = Address::updateOrCreate(
            ['id' => $department->address_id],
            [
                'calle' => $request->calle,
                'colonia' => $request->colonia,
                'cp' => $request->cp,
                'municipio' => 'OAXACA DE JUAREZ',
                'ciudad' => 'OAXACA DE JUAREZ',
                'estado' => 'OAXACA',
            ]
            );
        }

        $department->update([
            'areacve' => $request->areacve,
            'areanom' => $request->areanom,
            'tipo' => $request->tipo,
            'unit_id' => $request->unit_id,
            'address_id' => $address ? $address->id : $department->address_id,
        ]);

        return redirect()->route('departments.index')
            ->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }

    public function showImport()
    {
        return view('departments.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new StructureImport(), $request->file('file'));
        }
        catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }

        return redirect()->route('departments.index')
            ->with('success', 'Estructura organizacional importada correctamente.');
    }

    public function downloadTemplate()
    {

        $headers = [[
                'regcve',
                'regnom',
                'unicve',
                'uninom',
                'areacve',
                'areanom',
                'tipo',
                'calle',
                'colonia',
                'cp',
                'municipio',
                'ciudad',
                'estado'
            ]];

        $data = $headers;

        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $rows;

            public function __construct($rows)
            {
                $this->rows = $rows;
            }

            public function array(): array
            {
                return $this->rows;
            }
        };

        $fileName = "plantilla_estructura_organizacional_" . now()->format('Y-m-d_H-i-s') . ".xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download($export, $fileName);
    }


    public function downloadInstructionsPDF()
    {
        $pdf = \PDF::loadView('departments.instructions_pdf');
        return $pdf->stream('Instrucciones_Importacion_Estructura_Organizacional.pdf');
    }


}
