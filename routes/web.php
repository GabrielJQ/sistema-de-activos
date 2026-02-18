<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\UnitTechnicianController;
/* |-------------------------------------------------------------------------- | Rutas públicas |-------------------------------------------------------------------------- */
Route::get('/assets/qr-tag/{tag}', [AssetAssignmentController::class , 'qrTagView'])->name('assets.qrTagView');
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

/* |-------------------------------------------------------------------------- | Rutas protegidas con autenticación Breeze |-------------------------------------------------------------------------- */
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard y perfil (todos los roles)
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

    Route::middleware([RoleMiddleware::class . ':super_admin'])->group(function () {

            // Usuarios
            Route::get('users', [UserController::class , 'index'])->name('users.index');
            Route::get('users/create', [UserController::class , 'create'])->name('users.create');
            Route::post('users', [UserController::class , 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class , 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class , 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class , 'destroy'])->name('users.destroy');
            Route::get('/users/status', [UserController::class , 'status'])->name('users.status');

            // Proveedores
            Route::get('suppliers', [SupplierController::class , 'index'])->name('suppliers.index');
            Route::get('suppliers/create', [SupplierController::class , 'create'])->name('suppliers.create');
            Route::post('suppliers', [SupplierController::class , 'store'])->name('suppliers.store');
            Route::get('suppliers/{supplier}/edit', [SupplierController::class , 'edit'])->name('suppliers.edit');
            Route::put('suppliers/{supplier}', [SupplierController::class , 'update'])->name('suppliers.update');
            Route::delete('suppliers/{supplier}', [SupplierController::class , 'destroy'])->name('suppliers.destroy');

            // Departamentos
            Route::get('departments', [DepartmentController::class , 'index'])->name('departments.index');
            Route::get('departments/create', [DepartmentController::class , 'create'])->name('departments.create');
            Route::post('departments', [DepartmentController::class , 'store'])->name('departments.store');
            Route::get('departments/{department}/edit', [DepartmentController::class , 'edit'])->name('departments.edit');
            Route::put('departments/{department}', [DepartmentController::class , 'update'])->name('departments.update');
            Route::delete('departments/{department}', [DepartmentController::class , 'destroy'])->name('departments.destroy');
            Route::get('/departments/import', [DepartmentController::class , 'showImport'])->name('departments.import');
            Route::post('/departments/import', [DepartmentController::class , 'import'])->name('departments.import.process');
            Route::get('/departments/template/download', [\App\Http\Controllers\DepartmentController::class , 'downloadTemplate'])
                ->name('departments.template.download');
            Route::get('/departments/import/instructions', [DepartmentController::class , 'downloadInstructionsPDF'])
                ->name('departments.instructions.pdf');


            // Empleados
            Route::get('employees', [EmployeeController::class , 'index'])->name('employees.index');
            Route::get('employees/create', [EmployeeController::class , 'create'])->name('employees.create');
            Route::post('employees', [EmployeeController::class , 'store'])->name('employees.store');
            Route::get('employees/{employee}/edit', [EmployeeController::class , 'edit'])->name('employees.edit');
            Route::put('employees/{employee}', [EmployeeController::class , 'update'])->name('employees.update');
            Route::delete('employees/{employee}', [EmployeeController::class , 'destroy'])->name('employees.destroy');

            Route::get('employees/import', [EmployeeController::class , 'showImport'])->name('employees.showImport');
            Route::post('employees/import', [EmployeeController::class , 'import'])->name('employees.import');
            Route::get('employees/export', [EmployeeController::class , 'exportForm'])->name('employees.exportForm');
            Route::post('employees/export', [EmployeeController::class , 'export'])->name('employees.export');
            Route::get('employees/template/download', [EmployeeController::class , 'downloadTemplate'])->name('employees.template.download');
            Route::get('employees/instructions/pdf', [EmployeeController::class , 'downloadInstructionsPDF'])->name('employees.instructions.pdf');

            // Tipos de equipos
            Route::get('device-types', [DeviceTypeController::class , 'index'])->name('device_types.index');
            Route::get('device-types/create', [DeviceTypeController::class , 'create'])->name('device_types.create');
            Route::post('device-types', [DeviceTypeController::class , 'store'])->name('device_types.store');
            Route::get('device-types/{deviceType}/edit', [DeviceTypeController::class , 'edit'])->name('device_types.edit');
            Route::put('device-types/{deviceType}', [DeviceTypeController::class , 'update'])->name('device_types.update');
            Route::delete('device-types/{deviceType}', [DeviceTypeController::class , 'destroy'])->name('device_types.destroy');

            // Activos
            Route::get('/assets', [AssetController::class , 'index'])->name('assets.index');
            Route::get('/assets/create', [AssetController::class , 'create'])->name('assets.create');
            Route::post('/assets', [AssetController::class , 'store'])->name('assets.store');
            Route::get('/assets/{asset}/edit', [AssetController::class , 'edit'])->name('assets.edit');
            Route::put('/assets/{asset}', [AssetController::class , 'update'])->name('assets.update');
            Route::delete('/assets/{asset}', [AssetController::class , 'destroy'])->name('assets.destroy');

            // Importación Status Check
            Route::get('assets/import/active', [AssetController::class , 'checkActiveImport'])->name('assets.import.checkActive');

            Route::get('assets/import', [AssetController::class , 'showImport'])->name('assets.showImport');
            Route::post('assets/import', [AssetController::class , 'import'])->name('assets.import');
            Route::get('assets/template/download', [AssetController::class , 'downloadTemplate'])->name('assets.template.download');
            Route::get('assets/export', [AssetController::class , 'exportForm'])->name('assets.exportForm');
            Route::post('assets/export', [AssetController::class , 'export'])->name('assets.export');
            Route::get('assets/instructions/pdf', [AssetController::class , 'downloadInstructionsPDF'])->name('assets.instructions.pdf');
            Route::get('/assets/{asset}/report', [AssetController::class , 'report'])->name('assets.report');
            Route::post('/assets/{asset}/report', [AssetController::class , 'submitReport'])->name('assets.submitReport');
            Route::get('/assets/group/{tag}', [AssetController::class , 'showByTag'])
                ->name('assets.group');
            Route::post('/assets/tag/{tag}/bulk-update', [AssetController::class , 'bulkUpdateByTag'])
                ->name('assets.bulkUpdateByTag');

            // AssetAssignments
            Route::get('asset-assignments', [AssetAssignmentController::class , 'index'])->name('asset_assignments.index');
            Route::get('asset-assignments/create', [AssetAssignmentController::class , 'create'])->name('asset_assignments.create');
            Route::post('asset-assignments', [AssetAssignmentController::class , 'store'])->name('asset_assignments.store');
            Route::get('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'show'])->name('asset_assignments.show');
            Route::get('asset-assignments/{assetAssignment}/edit', [AssetAssignmentController::class , 'edit'])->name('asset_assignments.edit');
            Route::put('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'update'])->name('asset_assignments.update');
            Route::delete('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'destroy'])->name('asset_assignments.destroy');

            Route::post('asset-assignments/bulk-return', [AssetAssignmentController::class , 'bulkReturn'])->name('asset_assignments.bulkReturn');
            Route::post('asset-assignments/{id}/return', [AssetAssignmentController::class , 'returnAsset'])->name('asset_assignments.return');
            Route::get('asset_assignments/generate_receipt', [AssetAssignmentController::class , 'generateReceipt'])->name('asset_assignments.generateReceipt');
            Route::get('asset-assignments/receipt/prepare', [AssetAssignmentController::class , 'prepareReceipt'])
                ->name('asset_assignments.prepareReceipt');

            Route::post('asset-assignments/receipt/preview', [AssetAssignmentController::class , 'previewReceipt'])
                ->name('asset_assignments.previewReceipt');

            Route::post('asset-assignments/bulk-download', [AssetAssignmentController::class , 'bulkDownload'])
                ->name('asset_assignments.bulkDownload');
            Route::post('/asset-assignments/bulk-qr-download', [AssetAssignmentController::class , 'bulkQrDownload'])->name('assets.bulkQrDownload');

            Route::get(
                'asset-assignments/baja/pdf',
            [AssetAssignmentController::class , 'generateBajaPdf']
            )->name('asset_assignments.generateBajaPdf');

            // PREPARE BAJA (desde asignaciones -> prepare)
            Route::get('asset_assignments/prepare_baja', [AssetAssignmentController::class , 'prepareBaja'])
                ->name('asset_assignments.prepareBaja');

            // PREVIEW / GENERAR PDF LIBERACIÓN (abre PDF en otra pestaña)
            Route::post('asset_assignments/preview_baja_pdf', [AssetAssignmentController::class , 'previewBajaPdf'])
                ->name('asset_assignments.previewBajaPdf');

            // CONFIRMAR BAJA (ya aplica cambios en BD)
            Route::post('asset_assignments/confirm_baja', [AssetAssignmentController::class , 'confirmBaja'])
                ->name('asset_assignments.confirmBaja');

            // Historial
            Route::get('history', [HistoryController::class , 'index'])->name('history.index');
            Route::get('history/employee/{employee}', [HistoryController::class , 'showEmployee'])->name('history.showEmployee');
            Route::get('history/asset/{asset}', [HistoryController::class , 'showAsset'])->name('history.showAsset');
            Route::get('history/employee/{employee}/report', [HistoryController::class , 'generateEmployeeReport'])->name('history.employeeReport');

            // Unit Technicians (Moved here for Admin access)
            Route::get('tecnicos', [UnitTechnicianController::class , 'index'])
                ->name('unit-technicians.index');

            Route::post('tecnicos', [UnitTechnicianController::class , 'store'])
                ->name('unit-technicians.store');

            Route::get(
                'unit-technicians/employees/{unit}',
            [UnitTechnicianController::class , 'employeesByUnit']
            )->name('unit-technicians.employees');



        }
        );

        // =========================
        // ADMIN: acceso completo
        // =========================
        Route::middleware([RoleMiddleware::class . ':admin,super_admin'])->group(function () {

            // Usuarios
            Route::get('users', [UserController::class , 'index'])->name('users.index');
            Route::get('users/create', [UserController::class , 'create'])->name('users.create');
            Route::post('users', [UserController::class , 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class , 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class , 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class , 'destroy'])->name('users.destroy');
            Route::get('/users/status', [UserController::class , 'status'])->name('users.status');

            // Proveedores
            Route::get('suppliers', [SupplierController::class , 'index'])->name('suppliers.index');
            Route::get('suppliers/create', [SupplierController::class , 'create'])->name('suppliers.create');
            Route::post('suppliers', [SupplierController::class , 'store'])->name('suppliers.store');
            Route::get('suppliers/{supplier}/edit', [SupplierController::class , 'edit'])->name('suppliers.edit');
            Route::put('suppliers/{supplier}', [SupplierController::class , 'update'])->name('suppliers.update');
            Route::delete('suppliers/{supplier}', [SupplierController::class , 'destroy'])->name('suppliers.destroy');

            // Departamentos
            Route::get('departments', [DepartmentController::class , 'index'])->name('departments.index');
            Route::get('departments/create', [DepartmentController::class , 'create'])->name('departments.create');
            Route::post('departments', [DepartmentController::class , 'store'])->name('departments.store');
            Route::get('departments/{department}/edit', [DepartmentController::class , 'edit'])->name('departments.edit');
            Route::put('departments/{department}', [DepartmentController::class , 'update'])->name('departments.update');
            Route::delete('departments/{department}', [DepartmentController::class , 'destroy'])->name('departments.destroy');

            // Empleados
            Route::get('employees', [EmployeeController::class , 'index'])->name('employees.index');
            Route::get('employees/create', [EmployeeController::class , 'create'])->name('employees.create');
            Route::post('employees', [EmployeeController::class , 'store'])->name('employees.store');
            Route::get('employees/{employee}/edit', [EmployeeController::class , 'edit'])->name('employees.edit');
            Route::put('employees/{employee}', [EmployeeController::class , 'update'])->name('employees.update');
            Route::delete('employees/{employee}', [EmployeeController::class , 'destroy'])->name('employees.destroy');

            Route::get('employees/import', [EmployeeController::class , 'showImport'])->name('employees.showImport');
            Route::post('employees/import', [EmployeeController::class , 'import'])->name('employees.import');
            Route::get('employees/export', [EmployeeController::class , 'exportForm'])->name('employees.exportForm');
            Route::post('employees/export', [EmployeeController::class , 'export'])->name('employees.export');
            Route::get('employees/template/download', [EmployeeController::class , 'downloadTemplate'])->name('employees.template.download');
            Route::get('employees/instructions/pdf', [EmployeeController::class , 'downloadInstructionsPDF'])->name('employees.instructions.pdf');

            // Tipos de equipos
            Route::get('device-types', [DeviceTypeController::class , 'index'])->name('device_types.index');
            Route::get('device-types/create', [DeviceTypeController::class , 'create'])->name('device_types.create');
            Route::post('device-types', [DeviceTypeController::class , 'store'])->name('device_types.store');
            Route::get('device-types/{deviceType}/edit', [DeviceTypeController::class , 'edit'])->name('device_types.edit');
            Route::put('device-types/{deviceType}', [DeviceTypeController::class , 'update'])->name('device_types.update');
            Route::delete('device-types/{deviceType}', [DeviceTypeController::class , 'destroy'])->name('device_types.destroy');

            // Activos
            Route::get('/assets', [AssetController::class , 'index'])->name('assets.index');
            Route::get('/assets/create', [AssetController::class , 'create'])->name('assets.create');
            Route::post('/assets', [AssetController::class , 'store'])->name('assets.store');
            Route::get('/assets/{asset}/edit', [AssetController::class , 'edit'])->name('assets.edit');
            Route::put('/assets/{asset}', [AssetController::class , 'update'])->name('assets.update');
            Route::delete('/assets/{asset}', [AssetController::class , 'destroy'])->name('assets.destroy');

            Route::get('assets/import', [AssetController::class , 'showImport'])->name('assets.showImport');
            Route::post('assets/import', [AssetController::class , 'import'])->name('assets.import');
            // Import Progress Routes
            Route::get('assets/import/progress/{task}', [App\Http\Controllers\ImportProgressController::class , 'show'])->name('assets.import.progress');
            Route::get('assets/import/status/{task}', [App\Http\Controllers\ImportProgressController::class , 'status'])->name('assets.import.status');
            Route::post('assets/import/cancel/{task}', [App\Http\Controllers\ImportProgressController::class , 'cancel'])->name('assets.import.cancel');
            Route::get('assets/template/download', [AssetController::class , 'downloadTemplate'])->name('assets.template.download');
            Route::get('assets/export', [AssetController::class , 'exportForm'])->name('assets.exportForm');
            Route::post('assets/export', [AssetController::class , 'export'])->name('assets.export');
            Route::get('assets/instructions/pdf', [AssetController::class , 'downloadInstructionsPDF'])->name('assets.instructions.pdf');
            Route::get('/assets/{asset}/report', [AssetController::class , 'report'])->name('assets.report');
            Route::post('/assets/{asset}/report', [AssetController::class , 'submitReport'])->name('assets.submitReport');
            Route::get('/assets/group/{tag}', [AssetController::class , 'showByTag'])
                ->name('assets.group');
            Route::post('/assets/tag/{tag}/bulk-update', [AssetController::class , 'bulkUpdateByTag'])
                ->name('assets.bulkUpdateByTag');

            // PREPARE BAJA (desde asignaciones -> prepare)
            Route::get('asset_assignments/prepare_baja', [AssetAssignmentController::class , 'prepareBaja'])
                ->name('asset_assignments.prepareBaja');

            // PREVIEW / GENERAR PDF LIBERACIÓN (abre PDF en otra pestaña)
            Route::post('asset_assignments/preview_baja_pdf', [AssetAssignmentController::class , 'previewBajaPdf'])
                ->name('asset_assignments.previewBajaPdf');

            // CONFIRMAR BAJA (ya aplica cambios en BD)
            Route::post('asset_assignments/confirm_baja', [AssetAssignmentController::class , 'confirmBaja'])
                ->name('asset_assignments.confirmBaja');

            // AssetAssignments
            Route::get('asset-assignments', [AssetAssignmentController::class , 'index'])->name('asset_assignments.index');
            Route::get('asset-assignments/create', [AssetAssignmentController::class , 'create'])->name('asset_assignments.create');
            Route::post('asset-assignments', [AssetAssignmentController::class , 'store'])->name('asset_assignments.store');
            Route::get('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'show'])->name('asset_assignments.show');
            Route::get('asset-assignments/{assetAssignment}/edit', [AssetAssignmentController::class , 'edit'])->name('asset_assignments.edit');
            Route::put('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'update'])->name('asset_assignments.update');
            Route::delete('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'destroy'])->name('asset_assignments.destroy');

            Route::post('asset-assignments/bulk-return', [AssetAssignmentController::class , 'bulkReturn'])->name('asset_assignments.bulkReturn');
            Route::post('asset-assignments/{id}/return', [AssetAssignmentController::class , 'returnAsset'])->name('asset_assignments.return');
            Route::get('asset_assignments/generate_receipt', [AssetAssignmentController::class , 'generateReceipt'])->name('asset_assignments.generateReceipt');
            Route::get('asset-assignments/receipt/prepare', [AssetAssignmentController::class , 'prepareReceipt'])
                ->name('asset_assignments.prepareReceipt');

            Route::post('asset-assignments/receipt/preview', [AssetAssignmentController::class , 'previewReceipt'])
                ->name('asset_assignments.previewReceipt');

            Route::post('asset-assignments/bulk-download', [AssetAssignmentController::class , 'bulkDownload'])
                ->name('asset_assignments.bulkDownload');
            Route::post('/asset-assignments/bulk-qr-download', [AssetAssignmentController::class , 'bulkQrDownload'])->name('assets.bulkQrDownload');

            Route::get(
                'asset-assignments/baja/pdf',
            [AssetAssignmentController::class , 'generateBajaPdf']
            )->name('asset_assignments.generateBajaPdf');

            // Historial
            Route::get('history', [HistoryController::class , 'index'])->name('history.index');
            Route::get('history/employee/{employee}', [HistoryController::class , 'showEmployee'])->name('history.showEmployee');
            Route::get('history/asset/{asset}', [HistoryController::class , 'showAsset'])->name('history.showAsset');
            Route::get('history/employee/{employee}/report', [HistoryController::class , 'generateEmployeeReport'])->name('history.employeeReport');

            Route::get('tecnicos', [UnitTechnicianController::class , 'index'])
                ->name('unit-technicians.index');

            Route::post('tecnicos', [UnitTechnicianController::class , 'store'])
                ->name('unit-technicians.store');

            Route::get(
                'unit-technicians/employees/{unit}',
            [UnitTechnicianController::class , 'employeesByUnit']
            )->name('unit-technicians.employees');


        }
        );

        // =========================
        // EDITOR: acceso parcial
        // =========================
        Route::middleware([RoleMiddleware::class . ':collaborator,admin,super_admin'])->group(function () {
            // Activos
            Route::get('/assets', [AssetController::class , 'index'])->name('assets.index');
            Route::get('/assets/{asset}/edit', [AssetController::class , 'edit'])->name('assets.edit');
            Route::put('/assets/{asset}', [AssetController::class , 'update'])->name('assets.update');

            // AssetAssignments
            Route::get('asset-assignments', [AssetAssignmentController::class , 'index'])->name('asset_assignments.index');
            Route::get('asset-assignments/create', [AssetAssignmentController::class , 'create'])->name('asset_assignments.create');
            Route::post('asset-assignments', [AssetAssignmentController::class , 'store'])->name('asset_assignments.store');
            Route::get('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'show'])->name('asset_assignments.show');
            Route::get('asset-assignments/{assetAssignment}/edit', [AssetAssignmentController::class , 'edit'])->name('asset_assignments.edit');
            Route::put('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'update'])->name('asset_assignments.update');
            Route::post('asset-assignments/bulk-download', [AssetAssignmentController::class , 'bulkDownload'])
                ->name('asset_assignments.bulkDownload');
            Route::post('/asset-assignments/bulk-qr-download', [AssetAssignmentController::class , 'bulkQrDownload'])->name('assets.bulkQrDownload');
            Route::post('/assets/tag/{tag}/bulk-update', [AssetController::class , 'bulkUpdateByTag'])
                ->name('assets.bulkUpdateByTag');
            // PREPARE BAJA (desde asignaciones -> prepare)
            Route::get('asset_assignments/prepare_baja', [AssetAssignmentController::class , 'prepareBaja'])
                ->name('asset_assignments.prepareBaja');

            // PREVIEW / GENERAR PDF LIBERACIÓN (abre PDF en otra pestaña)
            Route::post('asset_assignments/preview_baja_pdf', [AssetAssignmentController::class , 'previewBajaPdf'])
                ->name('asset_assignments.previewBajaPdf');

            // CONFIRMAR BAJA (ya aplica cambios en BD)
            Route::post('asset_assignments/confirm_baja', [AssetAssignmentController::class , 'confirmBaja'])
                ->name('asset_assignments.confirmBaja');


            // Departamentos
            Route::get('departments', [DepartmentController::class , 'index'])->name('departments.index');
            Route::get('departments/{department}/edit', [DepartmentController::class , 'edit'])->name('departments.edit');
            Route::put('departments/{department}', [DepartmentController::class , 'update'])->name('departments.update');

            // Empleados
            Route::get('employees', [EmployeeController::class , 'index'])->name('employees.index');
            Route::get('employees/create', [EmployeeController::class , 'create'])->name('employees.create');
            Route::post('employees', [EmployeeController::class , 'store'])->name('employees.store');
            Route::get('employees/{employee}/edit', [EmployeeController::class , 'edit'])->name('employees.edit');
            Route::put('employees/{employee}', [EmployeeController::class , 'update'])->name('employees.update');
            Route::delete('employees/{employee}', [EmployeeController::class , 'destroy'])->name('employees.destroy');

            Route::get('employees/import', [EmployeeController::class , 'showImport'])->name('employees.showImport');
            Route::post('employees/import', [EmployeeController::class , 'import'])->name('employees.import');
            Route::get('employees/export', [EmployeeController::class , 'exportForm'])->name('employees.exportForm');
            Route::post('employees/export', [EmployeeController::class , 'export'])->name('employees.export');
            Route::get('employees/template/download', [EmployeeController::class , 'downloadTemplate'])->name('employees.template.download');
            Route::get('employees/instructions/pdf', [EmployeeController::class , 'downloadInstructionsPDF'])->name('employees.instructions.pdf');

            Route::get('suppliers', [SupplierController::class , 'index'])->name('suppliers.index');
        }
        );


        // =========================
        // VISITOR: solo lectura limitada
        // =========================
        Route::middleware([RoleMiddleware::class . ':visitor,collaborator,admin,super_admin'])->group(function () {
            Route::get('asset-assignments', [AssetAssignmentController::class , 'index'])->name('asset_assignments.index');
            Route::get('asset-assignments/{assetAssignment}', [AssetAssignmentController::class , 'show'])->name('asset_assignments.show');
            Route::get('/assets', [AssetController::class , 'index'])->name('assets.index');
            Route::get('assets/export', [AssetController::class , 'exportForm'])->name('assets.exportForm');
            Route::post('assets/export', [AssetController::class , 'export'])->name('assets.export');
        }
        );

        // Historial
        Route::get('history', [HistoryController::class , 'index'])->name('history.index');
        Route::get('history/employee/{employee}', [HistoryController::class , 'showEmployee'])->name('history.showEmployee');
        Route::get('history/asset/{asset}', [HistoryController::class , 'showAsset'])->name('history.showAsset');
        Route::get('history/employee/{employee}/report', [HistoryController::class , 'generateEmployeeReport'])->name('history.employeeReport');


    });

require __DIR__ . '/auth.php';