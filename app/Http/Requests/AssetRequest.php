<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
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
        $asset = $this->route('asset');
        $assetId = $asset ? $asset->id : 'NULL';

        $rules = [
            'tag' => 'required|string|max:255',
            'serie' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'device_type_id' => 'required|exists:device_types,id',
            'estado' => 'required|in:' . implode(',', config('assets.statuses')),
            'propiedad' => 'required|in:' . implode(',', config('assets.properties')),
            'ip_address' => 'nullable|ipv4|unique:asset_network_interfaces,ip_address,' . $assetId . ',asset_id',
        ];

        if ($this->isMethod('post')) {
            $rules['modo_registro'] = 'nullable|string|in:ALTA,REEMPLAZO';
        } else {
            $rules['tag'] = 'nullable|string|max:255';
            $rules['serie'] = 'nullable|string|max:255';
            $rules['department_id'] = 'nullable|exists:departments,id';
            $rules['activo'] = 'nullable|string|max:35';
        }

        return $rules;
    }
}
