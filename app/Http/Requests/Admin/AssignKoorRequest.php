<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignKoorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pengguna_id' => ['required', 'uuid', 'exists:pengguna,id'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'pengguna_id.required' => 'ID Pengguna wajib diisi.',
            'pengguna_id.exists'   => 'Pengguna yang dipilih tidak ditemukan.',
            'pengguna_id.uuid'     => 'Format ID Pengguna tidak valid.',
        ];
    }
}
