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

    public function attributes(): array
    {
        return [
            'department_id' => 'departamento',
            'nombre' => 'nombre',
            'apellido_pat' => 'apellido paterno',
            'apellido_mat' => 'apellido materno',
            'curp' => 'CURP',
            'puesto' => 'puesto',
            'tipo' => 'tipo de empleado',
            'email' => 'correo electrónico',
            'telefono' => 'teléfono',
            'extension' => 'extensión',
            'status' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'in' => 'El :attribute seleccionado no es válido.',
            'exists' => 'El :attribute seleccionado no existe.',
            'unique' => 'El :attribute ya ha sido registrado.',
        ];
    }
}
