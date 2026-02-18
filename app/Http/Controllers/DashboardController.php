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
                });
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
                $topDepartments = Department::where('unit_id', $unitId)
                    ->withCount([
                        'assets' => function ($q) use ($assetTypes) {
                            $q->whereHas('deviceType', fn($dt) => $dt->whereIn('equipo', $assetTypes));
                        }
                    ])
                    ->orderByDesc('assets_count')
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
                            'name' => $d->areanom,
                            'count' => $d->assets_count,
                            'mostCommonType' => $mostCommon ? $mostCommon->equipo : 'Sin datos',
                        ];
                    });
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

            // Top Empleados
            $topEmployees = Employee::withCount([
                'assetAssignments as current_assets_count' => function ($q) use ($assetTypes) {
                    $q->where('is_current', DB::raw('true'))
                        ->whereHas('asset.deviceType', fn($qt) => $qt->whereIn('equipo', $assetTypes));
                }
            ])
                ->orderByDesc('current_assets_count')
                ->take(3)
                ->get()
                ->pluck('current_assets_count', 'fullName')
                ->toArray();

            // Top Proveedores
            $topSuppliers = Supplier::whereRaw('UPPER(TRIM(prvnombre)) <> ?', [mb_strtoupper($abSupplierName)])
                ->select('suppliers.id', 'suppliers.prvnombre')
                ->withCount([
                    'assets' => function ($q) use ($assetTypes, $isSuperAdmin, $unitId) {
                        $q->whereHas('deviceType', fn($dt) => $dt->whereIn('equipo', $assetTypes));
                        if (!$isSuperAdmin)
                            $q->whereHas('department', fn($d) => $d->where('unit_id', $unitId));
                    }
                ])
                ->orderByDesc('assets_count')
                ->limit(4)
                ->get()
                ->map(function ($s) use ($assetTypes) {
                    return [
                        'name' => $s->prvnombre,
                        'count' => $s->assets_count,
                    ];
                });

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
