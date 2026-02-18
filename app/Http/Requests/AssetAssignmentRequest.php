<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetAssignmentRequest extends FormRequest
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
            'employee_id' => 'nullable|exists:employees,id',
            'assignment_type' => 'required|in:' . implode(',', config('assets.assignment_types')),
            'temporary_holder' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'assigned_at' => 'required|date|before_or_equal:today',
        ];

        if ($this->isMethod('post')) {
            $rules['asset_ids'] = 'required|array';
            $rules['asset_ids.*'] = 'exists:assets,id';
        }

        return $rules;
    }
}
