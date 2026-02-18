<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
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
        // Obtener el ID del departamento si estamos editando
        $departmentId = $this->route('department') ? $this->route('department')->id : null;

        return [
            'areacve' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'areacve')->ignore($departmentId),
            ],
            'areanom' => 'required|string|max:255',
            'tipo' => 'required|in:Oficina,Almacen,Otro',
            'unit_id' => 'required|exists:units,id',
            // Dirección
            'address_id' => 'nullable|exists:addresses,id',
            'calle' => 'nullable|string|max:255',
            'colonia' => 'nullable|string|max:255',
            'cp' => 'nullable|string|max:10',
        ];
    }

    public function attributes(): array
    {
        return [
            'areacve' => 'clave del departamento',
            'areanom' => 'nombre del departamento',
            'tipo' => 'tipo de departamento',
            'unit_id' => 'unidad administrativa',
            'address_id' => 'dirección seleccionada',
            'calle' => 'calle',
            'colonia' => 'colonia',
            'cp' => 'código postal',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres.',
            'unique' => 'La :attribute ya ha sido registrada anteriormente.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            'in' => 'El :attribute seleccionado no es válido.',
        ];
    }
}
