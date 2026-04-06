<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Department;
use App\Models\DeviceType;
use App\Models\Supplier;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterDepartment = $request->department_id;
        $filterDeviceType = $request->device_type_id;
        $filterSupplier = $request->supplier_id;

        // Caché de datos estáticos por 60 minutos
        $departments = \Cache::remember('dashboard_departments', 3600, fn() => Department::select('id', 'areanom')->get());
        $deviceTypes = \Cache::remember('dashboard_device_types', 3600, fn() => DeviceType::select('id', 'equipo')->get());
        $suppliers = \Cache::remember('dashboard_suppliers', 3600, fn() => Supplier::select('id', 'prvnombre')->get());

        $isSuperAdmin = hasRole(['super_admin']);
        $userId = auth()->id();
        $unitId = auth()->user()->unit_id;
        $regionId = auth()->user()->region_id;

        // Generar una llave de caché basada en filtros y rol
        $cacheKey = "dashboard_stats_" . md5(serialize([
            $filterDepartment,
            $filterDeviceType,
            $filterSupplier,
            $isSuperAdmin,
            $unitId,
            $regionId
        ]));

        $data = \Cache::remember($cacheKey, 300, function () use ($filterDepartment, $filterDeviceType, $filterSupplier, $isSuperAdmin, $unitId, $regionId) {
            $assetTypes = [
                'Equipo All In One',
                'Equipo Escritorio',
                'Escritorio Avanzada',
                'Laptop de Avanzada',
                'Laptop de Intermedia'
            ];

            // Consulta base optimizada
            $baseQuery = Asset::query();

            if (!$isSuperAdmin) {
                $baseQuery->whereHas('department.unit', function ($q) use ($unitId, $regionId) {
                            $q->where('units.id', $unitId)
                                ->where('units.region_id', $regionId);
                        }
                        );
                    }

                    // Estadísticas globales (KPIs)
                    $totalAssetsGlobal = (clone $baseQuery)->count();

                    // Filtros de equipos de cómputo específicos
                    $compQuery = (clone $baseQuery)->whereHas('deviceType', fn($q) => $q->whereIn('equipo', $assetTypes));

                    if ($filterDepartment)
                        $compQuery->where('department_id', $filterDepartment);
                    if ($filterDeviceType)
                        $compQuery->where('device_type_id', $filterDeviceType);
                    if ($filterSupplier)
                        $compQuery->where('supplier_id', $filterSupplier);

                    $totalAssets = (clone $compQuery)->count();
                    $assignedAssets = (clone $compQuery)->where('estado', 'OPERACION')->count();

                    // Lógica de disponibles con AB
                    $abSupplierName = 'ALIMENTACION PARA EL BIENESTAR';
                    $availableAssetsRaw = (clone $compQuery)->where('estado', 'RESGUARDADO')->count();

                    $abAllInOneAvailable = (clone $compQuery)
                        ->where('estado', 'RESGUARDADO')
                        ->whereHas('deviceType', fn($q) => $q->where('equipo', 'Equipo All In One'))
                        ->whereHas('supplier', fn($q) => $q->whereRaw('UPPER(TRIM(prvnombre)) = ?', [mb_strtoupper(trim($abSupplierName))]))
                        ->count();

                    $availableAssets = max(0, $availableAssetsRaw - $abAllInOneAvailable);

                    // Activos por tipo (Combinado en una sola consulta)
                    $assetsByType = (clone $compQuery)
                        ->join('device_types', 'assets.device_type_id', '=', 'device_types.id')
                        ->select('device_types.equipo', DB::raw('COUNT(*) as total'))
                        ->groupBy('device_types.equipo')
                        ->pluck('total', 'device_types.equipo')
                        ->toArray();

                    // Activos por estado
                    $assetsByStatus = (clone $compQuery)
                        ->select('estado', DB::raw('COUNT(*) as total'))
                        ->groupBy('estado')
                        ->pluck('total', 'estado')
                        ->toArray();

                    // Top Departamentos (Solo si no es super admin)
                    $topDepartments = collect();
                    if (!$isSuperAdmin) {
                        /**
         * OPTIMIZACIÓN DE RENDIMIENTO (Top Departamentos):
         * En lugar de procesar objetos de Eloquent masivamente en PHP,
         * usamos SQL nativo ('join', 'select', 'groupBy', 'COUNT') para
         * calcular los 3 departamentos con más activos. Esto evita el problema
         * N+1 y los desbordamientos de memoria en el servidor.
         */
                        $topDepartments = (clone $compQuery)
                            ->join('departments', 'assets.department_id', '=', 'departments.id')
                            ->select('departments.id', 'departments.areanom as name', DB::raw('COUNT(assets.id) as count'))
                            ->groupBy('departments.id', 'departments.areanom')
                            ->orderByDesc('count')
                            ->limit(3)
                            ->get()
                            ->map(function ($d) use ($assetTypes) {
                    $mostCommon = Asset::where('department_id', $d->id)
                        ->join('device_types', 'assets.device_type_id', '=', 'device_types.id')
                        ->whereIn('device_types.equipo', $assetTypes)
                        ->select('device_types.equipo', DB::raw('COUNT(*) as total'))
                        ->groupBy('device_types.equipo')
                        ->orderByDesc('total')
                        ->first();

                    return [
                    'name' => $d->name,
                    'count' => $d->count,
                    'mostCommonType' => $mostCommon ? $mostCommon->equipo : 'Sin datos',
                    ];
                }
                );
            }

            // Activos por región (Solo Super Admin)
            $assetsByRegion = [];
            if ($isSuperAdmin) {
                $assetsByRegion = Asset::join('departments', 'assets.department_id', '=', 'departments.id')
                    ->join('units', 'departments.unit_id', '=', 'units.id')
                    ->join('regions', 'units.region_id', '=', 'regions.id')
                    ->whereHas('deviceType', fn($q) => $q->whereIn('equipo', $assetTypes))
                    ->select('regions.regnom', DB::raw('COUNT(*) as total'))
                    ->groupBy('regions.regnom')
                    ->orderByDesc('total')
                    ->limit(3)
                    ->pluck('total', 'regions.regnom')
                    ->toArray();
            }

            /**
             * OPTIMIZACIÓN DE RENDIMIENTO (Top Empleados):
             * Reemplaza la iteración de la tabla `Employees` completa.
             * Cruza las tablas directamente desde `AssetAssignment` transaccional.
             * 
             * NOTA POSTGRESQL: Se usa DB::raw('true') en lugar de booleano puro 'true' o 1
             * para evitar el "Operator does not exist: boolean = integer" en Postgres.
             */
            $topEmployees = \App\Models\AssetAssignment::where('asset_assignments.is_current', DB::raw('true'))
                ->join('assets', 'asset_assignments.asset_id', '=', 'assets.id')
                ->join('device_types', 'assets.device_type_id', '=', 'device_types.id')
                ->join('employees', 'asset_assignments.employee_id', '=', 'employees.id')
                ->whereIn('device_types.equipo', $assetTypes)
                ->when(!$isSuperAdmin, function ($q) use ($unitId, $regionId) {
                $q->join('departments as emp_dept', 'employees.department_id', '=', 'emp_dept.id')
                    ->join('units as emp_unit', 'emp_dept.unit_id', '=', 'emp_unit.id')
                    ->where('emp_unit.id', $unitId)
                    ->where('emp_unit.region_id', $regionId);
            }
            )
                ->select(
                DB::raw("TRIM(CONCAT(COALESCE(employees.nombre,''), ' ', COALESCE(employees.apellido_pat,''), ' ', COALESCE(employees.apellido_mat,''))) as full_name"),
                DB::raw('COUNT(asset_assignments.id) as current_assets_count')
            )
                ->groupBy('employees.id', 'employees.nombre', 'employees.apellido_pat', 'employees.apellido_mat')
                ->orderByDesc('current_assets_count')
                ->take(3)
                ->pluck('current_assets_count', 'full_name')
                ->toArray();

            /**
             * OPTIMIZACIÓN DE RENDIMIENTO (Top Proveedores):
             * Usar la tabla de `suppliers` sumada directamente con COUNT(assets).
             * FIX DE LÓGICA DE NEGOCIO: No restrigimos proveedores por tipo ("Equipo de cómputo")
             * para asegurar que empresas con productos anexos (Ej. "SYNNEX" con "No Breaks") 
             * aparezcan correctamente en la estadística global.
             */
            $supplierQuery = (clone $baseQuery);
            if ($filterDepartment)
                $supplierQuery->where('department_id', $filterDepartment);
            if ($filterDeviceType)
                $supplierQuery->where('device_type_id', $filterDeviceType);
            if ($filterSupplier)
                $supplierQuery->where('supplier_id', $filterSupplier);

            $topSuppliers = (clone $supplierQuery)
                ->join('suppliers', 'assets.supplier_id', '=', 'suppliers.id')
                ->whereRaw('UPPER(TRIM(suppliers.prvnombre)) <> ?', [mb_strtoupper($abSupplierName)])
                ->select('suppliers.id', 'suppliers.prvnombre as name', DB::raw('COUNT(assets.id) as count'))
                ->groupBy('suppliers.id', 'suppliers.prvnombre')
                ->orderByDesc('count')
                ->limit(4)
                ->get()
                ->map(function ($s) {
                return [
                'name' => $s->name,
                'count' => $s->count,
                ];
            }
            );

            return [
            'totalAssetsGlobal' => $totalAssetsGlobal,
            'totalAssets' => $totalAssets,
            'assignedAssets' => $assignedAssets,
            'availableAssets' => $availableAssets,
            'assetsByType' => $assetsByType,
            'assetsByStatus' => $assetsByStatus,
            'topDepartments' => $topDepartments,
            'topEmployees' => $topEmployees,
            'topSuppliers' => $topSuppliers,
            'assetsByRegion' => $assetsByRegion,
            'assetTypes' => $assetTypes,
            'abSupplierName' => $abSupplierName,
            ];
        });

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', array_merge($data, [
            'departments' => $departments,
            'deviceTypes' => $deviceTypes,
            'suppliers' => $suppliers
        ]));
    }
}
