<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expediente' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'apellido_pat' => 'nullable|string|max:65',
            'apellido_mat' => 'nullable|string|max:65',
            'curp' => 'nullable|string|max:18',
            'department_id' => 'nullable|exists:departments,id',
            'puesto' => 'nullable|string|max:65',
            'tipo' => 'required|in:' . implode(',', config('assets.employee_types')),
            'email' => 'nullable|email|max:65',
            'telefono' => 'nullable|string|max:65',
            'extension' => 'nullable|string|max:65',
            'status' => 'required|in:Activo,Inactivo',
        ];
    }
}
