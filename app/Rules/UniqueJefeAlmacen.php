<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Employee;

class UniqueJefeAlmacen implements Rule
{
    protected $departmentId;
    protected $employeeId; // para permitir edición

    public function __construct($departmentId, $employeeId = null)
    {
        $this->departmentId = $departmentId;
        $this->employeeId = $employeeId;
    }

    public function passes($attribute, $value)
    {
        return !Employee::where('puesto', 'JEFE DE ALMACEN')
            ->where('department_id', $this->departmentId)
            ->when($this->employeeId, fn($q) => $q->where('id', '!=', $this->employeeId))
            ->exists();
    }

    public function message()
    {
        return 'Ya existe un Jefe de Almacén registrado en este departamento.';
    }
}
