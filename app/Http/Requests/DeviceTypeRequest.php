<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceTypeRequest extends FormRequest
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
            'equipo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // 2MB max
        ];
    }

    public function attributes(): array
    {
        return [
            'equipo' => 'nombre del tipo de dispositivo',
            'descripcion' => 'descripción',
            'image' => 'imagen',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres (o kilobytes).',
            'image' => 'El archivo seleccionado para :attribute debe ser una imagen válida.',
        ];
    }
}
