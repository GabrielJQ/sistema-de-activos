<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = $user ? $user->id : 'NULL';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => ($this->isMethod('post') ? 'required' : 'nullable') . '|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', config('assets.user_roles')),
            'region_id' => 'nullable|exists:regions,id',
            'unit_id' => 'nullable|exists:units,id',
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Las credenciales proporcionadas no son válidas.',
            'password.min' => 'Las credenciales proporcionadas no son válidas.',
        ];
    }
}
