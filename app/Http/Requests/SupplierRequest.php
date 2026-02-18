<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        $rules = [
            'prvnombre' => 'required|string|max:255',
            'contrato' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'enlace' => 'nullable|string|max:255',
            'prvstatus' => 'boolean',
            'logo' => 'nullable|image|max:2048', // 2MB max
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'prvnombre' => 'nombre del proveedor',
            'contrato' => 'contrato',
            'telefono' => 'teléfono',
            'enlace' => 'enlace web',
            'prvstatus' => 'estatus',
            'logo' => 'logotipo',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres (o kilobytes).',
            'image' => 'El archivo seleccionado para :attribute debe ser una imagen válida.',
        ];
    }
}
