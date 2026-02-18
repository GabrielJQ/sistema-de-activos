<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\TemporaryAssignment;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Requests\AssetAssignmentRequest;
use App\Services\AssignmentService;

class AssetAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index()
    {
        $employees = Employee::with(['department', 'currentAssets.deviceType'])
            ->whereHas('assetAssignments', fn($q) => $q->where('is_current', \Illuminate\Support\Facades\DB::raw('true')))
            ->orderBy('nombre')
            ->get();

        return view('asset_assignments.index', compact('employees'));
    }

    public function create(Request $request)
    {
        $employees = Employee::where('status', 'Activo')
            ->orderBy('nombre')
            ->get();

        $assets = Asset::where('estado', 'RESGUARDADO')
            ->with('deviceType')
            ->orderBy('tag')
            ->get();

        $departments = Department::orderBy('areanom')->get();
        $selectedEmployeeId = $request->query('employee_id');

        return view('asset_assignments.create', compact('employees', 'assets', 'departments', 'selectedEmployeeId'));
    }

    public function store(AssetAssignmentRequest $request)
    {
        try {
            $this->assignmentService->assignAssets(
                $request->asset_ids,
                $request->employee_id,
                $request->assigned_at,
                $request->observations,
                $request->assignment_type,
                $request->temporary_holder
            );
            return redirect()->route('asset_assignments.index')->with('success', 'Activos asignados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(AssetAssignment $assetAssignment)
    {
        $employees = Employee::where('status', 'Activo')
            ->orderBy('nombre')
            ->get();

        return view('asset_assignments.edit', compact('assetAssignment', 'employees'));
    }

    public function update(AssetAssignmentRequest $request, AssetAssignment $assetAssignment)
    {
        try {
            $this->assignmentService->assignAssets(
                [$assetAssignment->asset_id],
                $request->employee_id,
                $request->assigned_at,
                $request->observations,
                $request->assignment_type,
                $request->temporary_holder
            );

            return redirect()->route('asset_assignments.index')
                ->with('success', $request->employee_id ? 'Activo reasignado correctamente.' : 'Activo dado de baja y asignado a Informática.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($employeeId)
    {
        $employee = Employee::with(['assetAssignments.asset.deviceType', 'department'])
            ->findOrFail($employeeId);

        // Obtener solo las asignaciones vigentes
        $allAssignments = $employee->assetAssignments->where('is_current', true);

        // Agrupar por tag
        $groupedAssignments = $allAssignments->groupBy(function ($assignment) {
            return optional($assignment->asset)->tag ?: 'Sin tag';
        })->sortKeys();

        return view('asset_assignments.show', compact('employee', 'groupedAssignments'));
    }

    public function returnAsset($id)
    {
        try {
            $assignment = AssetAssignment::findOrFail($id);
            if ($assignment instanceof AssetAssignment) {
                $this->assignmentService->returnAsset($assignment);
            }
            return redirect()->back()->with('success', 'Activo devuelto y asignado a Informática.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function bulkReturn(Request $request)
    {
        $request->validate(['assignments' => 'required|array|min:1']);

        try {
            $ids = [];
            foreach ($request->assignments as $group) {
                $ids = array_merge($ids, explode(',', $group));
            }

            $assignments = AssetAssignment::whereIn('id', $ids)->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))->get();

            foreach ($assignments as $assignment) {
                $this->assignmentService->returnAsset($assignment);
            }

            return redirect()->back()->with('success', 'Activos devueltos y reasignados correctamente al técnico de informática.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function generateBajaPdf(Request $request)
    {
        $ids = explode(',', $request->query('assignments'));

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'asset.supplier',
            'employee.department'
        ])
            ->whereIn('id', $ids)
            ->get();

        if ($assignments->isEmpty()) {
            abort(404, 'No hay activos para generar la baja.');
        }

        $employee = $assignments->first()->employee;
        // Agrupar por TAG
        $groupedAssignments = $assignments->groupBy(fn($a) => $a->asset->tag ?? 'Sin tag');

        // Detectar equipo principal
        $mainComputer = $this->getMainComputer($assignments);

        $pdf = PDF::loadView('asset_assignments.baja_pdf', [
            'employee' => $employee,
            'assignments' => $assignments,
            'groupedAssignments' => $groupedAssignments,
            'mainComputer' => $mainComputer,
            'fecha' => now()->format('d/m/Y'),
        ]);


        return $pdf->stream(
            'baja_activos_' . $employee->full_name . '.pdf'
        );
    }

    /** Generar PDF de asignaciones de un empleado */
    public function generateReceipt(Request $request)
    {
        $assignmentIds = $request->query('assignments');

        if (!$assignmentIds) {
            return back()->with('error', 'No se seleccionaron resguardos.');
        }

        if (is_string($assignmentIds)) {
            $assignmentIds = [$assignmentIds];
        }

        $ids = [];
        foreach ($assignmentIds as $group) {
            $ids = array_merge($ids, explode(',', $group));
        }

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'temporaryAssignment',
            'employee.department',
            'asset.supplier'
        ])
            ->whereIn('id', $ids)
            ->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))
            ->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'No se encontraron asignaciones válidas.');
        }

        $groupedByTag = $assignments->groupBy(fn($a) => $a->asset->tag ?? 'Sin tag');

        $pdfFiles = [];

        foreach ($groupedByTag as $tag => $group) {
            $employee = $group->first()->employee;
            $mainComputer = $this->getMainComputer($group);

            $supplierName = $mainComputer?->asset?->supplier?->prvnombre ?? '';
            $view = $this->isAlimentacionSupplier($supplierName)
                ? 'asset_assignments.generateReceiptSpecial'
                : 'asset_assignments.generateReceipt';


            $pdf = PDF::loadView($view, [
                'employee' => $employee,
                'groupedAssignments' => collect([$tag => $group]),
                'mainComputer' => $mainComputer,
            ]);

            $fileName = 'resguardo_' . $tag . '_' . $employee->full_name . '.pdf';
            $pdfFiles[] = ['pdf' => $pdf, 'name' => $fileName];
        }

        if (count($pdfFiles) === 1) {
            return $pdfFiles[0]['pdf']->stream($pdfFiles[0]['name']);
        }

        $zip = new \ZipArchive();
        $zipFileName = 'resguardos_seleccionados.zip';
        $zipPath = storage_path($zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFromString($file['name'], $file['pdf']->output());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function prepareReceipt(Request $request)
    {
        $assignmentIds = $request->query('assignments');
        if (!$assignmentIds) {
            return back()->with('error', 'No se seleccionaron resguardos.');
        }

        if (is_array($assignmentIds)) {
            $ids = [];
            foreach ($assignmentIds as $g)
                $ids = array_merge($ids, explode(',', $g));
        } else {
            $ids = explode(',', $assignmentIds);
        }

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'temporaryAssignment',
            'employee.department',
            'asset.supplier'
        ])
            ->whereIn('id', $ids)
            ->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))
            ->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'No se encontraron asignaciones válidas.');
        }

        $employee = $assignments->first()->employee;
        $groupedByTag = $assignments->groupBy(fn($a) => $a->asset->tag ?? 'Sin tag');

        // Hostname sugerido por TAG
        $hostnamesByTag = [];
        foreach ($groupedByTag as $tag => $group) {
            $mainComputer = $this->getMainComputer($group);

            $savedHostname = $mainComputer?->asset?->hostname ?? null;

            $fallback = 'OAX-' . strtoupper(explode('@', $employee->email)[0]);

            $hostnamesByTag[$tag] = $savedHostname ?: $fallback;
        }

        $defaults = [
            'folio' => '',
            'unidad_adscripcion' => $employee->unidad_operativa ?? '',
            'jefe_autoriza_nombre' => '',
            'jefe_autoriza_cargo' => '',
            'piso' => 'PB',
            'centro_trabajo' => 'ALIMENTACION PARA EL BIENESTAR, S.A. DE C.V. REGIONAL OAXACA',
        ];

        return view('asset_assignments.receipt_prepare', compact(
            'employee',
            'groupedByTag',
            'ids',
            'defaults',
            'hostnamesByTag'
        ));
    }


    public function previewReceipt(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'folio' => 'nullable|string|max:50',
            'unidad_adscripcion' => 'nullable|string|max:255',
            'jefe_autoriza_nombre' => 'nullable|string|max:255',
            'jefe_autoriza_cargo' => 'nullable|string|max:255',
            'piso' => 'nullable|string|max:50',
            'centro_trabajo' => 'nullable|string|max:255',
            'hostname' => 'nullable|array',
            'hostname.*' => 'nullable|string|max:80',
        ]);

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'temporaryAssignment',
            'employee.department',
            'asset.supplier'
        ])
            ->whereIn('id', $request->ids)
            ->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))
            ->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'No se encontraron asignaciones válidas.');
        }

        $groupedByTag = $assignments->groupBy(fn($a) => $a->asset->tag ?? 'Sin tag');

        $pdfFiles = [];

        foreach ($groupedByTag as $tag => $group) {
            $employee = $group->first()->employee;
            $mainComputer = $this->getMainComputer($group);
            $hostnameForTag = data_get($request->hostname, $tag);

            $supplierName = $mainComputer?->asset?->supplier?->prvnombre ?? '';
            $view = $this->isAlimentacionSupplier($supplierName)
                ? 'asset_assignments.generateReceiptSpecial'
                : 'asset_assignments.generateReceipt';

            $extraData = [
                'folio' => $request->folio,
                'unidad_adscripcion' => $request->unidad_adscripcion,
                'jefe_autoriza_nombre' => $request->jefe_autoriza_nombre,
                'jefe_autoriza_cargo' => $request->jefe_autoriza_cargo,
                'piso' => $request->piso,
                'centro_trabajo' => $request->centro_trabajo,
                'hostname' => $hostnameForTag,
            ];

            $pdf = PDF::loadView($view, [
                'employee' => $employee,
                'groupedAssignments' => collect([$tag => $group]),
                'mainComputer' => $mainComputer,
                'extraData' => $extraData, // 👈 clave
            ]);

            $fileName = 'resguardo_' . $tag . '_' . $employee->full_name . '.pdf';
            $pdfFiles[] = ['pdf' => $pdf, 'name' => $fileName];
        }

        if (count($pdfFiles) === 1) {
            return $pdfFiles[0]['pdf']->stream($pdfFiles[0]['name']);
        }

        $zip = new \ZipArchive();
        $zipFileName = 'resguardos_preparados.zip';
        $zipPath = storage_path($zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFromString($file['name'], $file['pdf']->output());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function prepareBaja(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $idsRaw = $request->query('ids', '');
        $motivo = $request->query('motivo', '');

        $ids = collect(explode(',', $idsRaw))
            ->map(fn($x) => trim($x))
            ->filter()
            ->unique()
            ->values();

        abort_if(!$employeeId || $ids->isEmpty(), 404);

        $employee = Employee::with(['department.unit.technician.employee'])->findOrFail($employeeId);

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'asset.supplier',
            'temporaryAssignment',
            'employee.department.unit',
        ])
            ->whereIn('id', $ids)
            ->where('employee_id', $employeeId)
            ->get();

        // Agrupar por TAG
        $groupedByTag = $assignments
            ->groupBy(fn($a) => optional($a->asset)->tag ?: 'Sin tag')
            ->sortKeys();

        // Tipos principales
        $mainDeviceTypes = [
            'Equipo Escritorio',
            'Escritorio Avanzada',
            'Laptop Avanzada',
            'Laptop Intermedia',
            'Equipo All In One'
        ];

        // Hostname por TAG
        $userKey = strtoupper(explode('@', $employee->email)[0] ?? 'USUARIO');
        $hostnamesByTag = [];
        foreach ($groupedByTag as $tag => $items) {
            $hostnamesByTag[$tag] = 'OAX-' . $userKey;
        }

        $defaults = [
            'folio' => 'OAX-' . Carbon::now()->format('Y') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'piso' => 'PB',
            'centro_trabajo' => 'ALIMENTACIÓN PARA EL BIENESTAR, S.A. DE C.V. REGIONAL OAXACA',
            'jefe_autoriza_nombre' => '',
        ];

        return view('asset_assignments.prepare_baja', compact(
            'employee',
            'ids',
            'motivo',
            'groupedByTag',
            'hostnamesByTag',
            'defaults',
            'mainDeviceTypes'
        ));
    }

    public function previewBajaPdf(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'integer'],
            'ids' => ['required', 'array'],
            'motivo' => ['required', 'string'],
            'folio' => ['nullable', 'string'],
            'piso' => ['nullable', 'string'],
            'centro_trabajo' => ['nullable', 'string'],
            'unidad_adscripcion' => ['required', 'string'],
            'jefe_autoriza_nombre' => ['required', 'string'],
            'jefe_autoriza_cargo' => ['nullable', 'string'],
            'hostname' => ['array'],
        ]);

        $employee = Employee::with(['department.unit.technician.employee'])
            ->findOrFail($request->employee_id);

        $ids = collect($request->ids)->map(fn($x) => (int) $x)->filter()->values();

        $assignments = AssetAssignment::with([
            'asset.deviceType',
            'asset.supplier',
            'temporaryAssignment',
            'employee.department.unit',
        ])
            ->whereIn('id', $ids)
            ->where('employee_id', $employee->id)
            ->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'No se encontraron asignaciones válidas.');
        }

        // Agrupar por TAG
        $groupedByTag = $assignments
            ->groupBy(fn($a) => optional($a->asset)->tag ?: 'Sin tag')
            ->sortKeys();

        $pdfFiles = [];

        foreach ($groupedByTag as $tag => $group) {

            // Equipo principal por TAG
            $mainComputer = $this->getMainComputer($group);

            //Hostname por TAG desde el form
            $hostnameForTag = data_get($request->hostname, $tag);

            $extraData = [
                'folio' => $request->folio,
                'piso' => $request->piso,
                'centro_trabajo' => $request->centro_trabajo,
                'unidad_adscripcion' => $request->unidad_adscripcion,
                'jefe_autoriza_nombre' => $request->jefe_autoriza_nombre,
                'jefe_autoriza_cargo' => $request->jefe_autoriza_cargo,
                'hostname' => $hostnameForTag,
                'motivo' => $request->motivo,
            ];

            $pdf = PDF::loadView('asset_assignments.liberacion', [
                'employee' => $employee,
                'groupedAssignments' => collect([$tag => $group]),
                'assignments' => $group,       // por si lo usas en accesorios
                'mainComputer' => $mainComputer,
                'extraData' => $extraData,
            ])->setPaper('letter');

            $fileName = 'LIBERACION_' . $tag . '_' . $employee->full_name . '.pdf';
            $pdfFiles[] = ['pdf' => $pdf, 'name' => $fileName];
        }

        // Si solo es 1 TAG, lo mostramos normal
        if (count($pdfFiles) === 1) {
            return $pdfFiles[0]['pdf']->stream($pdfFiles[0]['name']);
        }

        // Si son varios TAG => ZIP
        $zip = new \ZipArchive();
        $friendlyZipName = 'LIBERACIONES_' . Str::slug($employee->full_name) . '.zip';

        // Usar directorio temporal del sistema para evitar problemas de permisos/renombrado
        // tempnam crea un archivo, ZipArchive lo sobrescribe.
        $tempDir = sys_get_temp_dir();
        $tempFile = tempnam($tempDir, 'zip_baja_');
        // Renombrar para asegurar extension .zip si fuera necesario, o simplemente usar esa ruta
        // ZipArchive prefiere ruta con extensión .zip a veces, pero no es estricto.
        // Lo importante es que sea escribible.
        $zipPath = $tempFile . '.zip';
        if (file_exists($tempFile)) {
            @rename($tempFile, $zipPath);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFromString($file['name'], $file['pdf']->output());
            }
            $zip->close();
        }

        return response()->download($zipPath, $friendlyZipName)->deleteFileAfterSend(true);
    }

    public function confirmBaja(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:asset_assignments,id'],
            'motivo' => ['required', 'string'],
        ]);

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $this->assignmentService->confirmBaja($request->ids, $employee, $request->motivo);
            return redirect()->route('asset_assignments.index')->with('success', 'Baja aplicada: activos devueltos a Informática y marcados como disponibles (RESGUARDADO).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /** Generar PDF masivo filtrando por empleado o departamento */
    public function bulkDownload(Request $request)
    {
        // Quitar límites de tiempo y subir memoria
        set_time_limit(0);                 // Sin límite de tiempo de ejecución
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');     // Sin límite de memoria

        $request->validate([
            'filter_type' => 'required|in:all,department,employee',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $filterType = $request->filter_type;

        $assignmentsQuery = AssetAssignment::with([
            'asset.deviceType',
            'employee.department',
            'asset.supplier'
        ])->where('is_current', \Illuminate\Support\Facades\DB::raw('true'));

        if ($filterType === 'department' && $request->department_id) {
            $assignmentsQuery->whereHas(
                'employee',
                fn($q) =>
                $q->where('department_id', $request->department_id)
            );
        }

        if ($filterType === 'employee' && $request->employee_id) {
            $assignmentsQuery->where('employee_id', $request->employee_id);
        }

        $assignments = $assignmentsQuery->get();

        if ($assignments->isEmpty()) {
            return back()->with('error', 'No se encontraron asignaciones para los filtros seleccionados.');
        }

        // Crear ZIP
        $zip = new \ZipArchive();
        $zipFileName = 'resguardos_masivos.zip';
        $zipPath = storage_path($zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            // Agrupar por empleado
            $groupedByEmployee = $assignments->groupBy('employee_id');

            foreach ($groupedByEmployee as $employeeId => $employeeAssignments) {
                $employee = $employeeAssignments->first()->employee;
                $department = $employee->department->areanom ?? 'Sin Departamento';
                $folderPath = $department . '/' . $employee->full_name;

                // Agrupar por tag dentro de cada empleado
                $groupedByTag = $employeeAssignments->groupBy(fn($a) => $a->asset->tag ?? 'Sin tag');

                foreach ($groupedByTag as $tag => $group) {
                    $mainComputer = $this->getMainComputer($group);
                    $supplierName = $mainComputer?->asset?->supplier?->prvnombre ?? '';
                    if ($this->isAlimentacionSupplier($supplierName)) {
                        abort(403, 'Para activos de ALIMENTACIÓN PARA EL BIENESTAR no se genera PDF de baja.');
                    }
                    $view = $this->isAlimentacionSupplier($supplierName)
                        ? 'asset_assignments.generateReceiptSpecial'
                        : 'asset_assignments.generateReceipt';

                    $pdf = PDF::loadView($view, [
                        'employee' => $employee,
                        'groupedAssignments' => collect([$tag => $group]),
                        'mainComputer' => $mainComputer,
                    ]);

                    $fileName = $folderPath . '/resguardo_' . $tag . '.pdf';
                    $zip->addFromString($fileName, $pdf->output());
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function getMainComputer($assignments)
    {
        $mainDeviceTypes = [
            'Equipo Escritorio',
            'Escritorio Avanzada',
            'Laptop Avanzada',
            'Laptop Intermedia',
            'Equipo All In One'
        ];

        return $assignments->first(function ($assignment) use ($mainDeviceTypes) {
            return in_array($assignment->asset->deviceType->equipo, $mainDeviceTypes);
        }) ?? $assignments->first();
    }
    private function normalizeSupplierName(?string $name): string
    {
        $name = $name ?? '';
        $name = trim($name);

        $name = Str::upper(Str::ascii($name));
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    private function isAlimentacionSupplier(?string $supplierName): bool
    {
        $s = $this->normalizeSupplierName($supplierName);

        $needles = [
            'ALIMENTACION PARA EL BIENESTAR',
            'ALIMENTACION P EL BIENESTAR',
            'ALIMENTACION PARA BIENESTAR',
            'DICONSA',
        ];

        foreach ($needles as $n) {
            if (Str::contains($s, $n))
                return true;
        }

        return false;
    }

    public function bulkQrDownload(Request $request)
    {
        $request->validate([
            'filter_type' => 'required|in:all,department,employee',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $assetsQuery = Asset::with(['deviceType', 'department', 'currentAssignment.employee'])
            ->whereHas('currentAssignment', fn($q) => $q->where('is_current', \Illuminate\Support\Facades\DB::raw('true')));

        if ($request->filter_type === 'department' && $request->department_id) {
            $assetsQuery->where('department_id', $request->department_id);
        }

        if ($request->filter_type === 'employee' && $request->employee_id) {
            $assetsQuery->whereHas(
                'currentAssignment',
                fn($q) =>
                $q->where('employee_id', $request->employee_id)
            );
        }

        $assets = $assetsQuery->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'No se encontraron activos para generar los códigos QR.');
        }

        // Agrupar por empleado y por tag para el primer QR
        $groupedAssets = $assets->groupBy(fn($a) => $a->currentAssignment->employee_id);

        return view('asset_assignments.qr_generate', compact('groupedAssets'));
    }

    public function qrTagView($tag)
    {
        $assignments = AssetAssignment::with(['asset.deviceType', 'employee.department'])
            ->whereHas('asset', fn($q) => $q->where('tag', $tag))
            ->where('is_current', \Illuminate\Support\Facades\DB::raw('true'))
            ->get();

        if ($assignments->isEmpty()) {
            abort(404, 'No se encontraron activos para este tag.');
        }

        $employee = $assignments->first()->employee;
        $assets = $assignments->pluck('asset');

        return view('asset_assignments.qr_tag_view', compact('employee', 'assets', 'tag'));
    }

}
