<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SwitchRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'exists:role,nama'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Role wajib diisi.',
            'role.exists'   => 'Role tidak ditemukan di sistem.',
        ];
    }
}
