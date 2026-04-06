<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $user         = $this->user();
        $isSuperadmin = $user->hasRole('superadmin');
        $isAdmin      = $user->hasAnyRole(['admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan']);

        return [
            'name' => ($isSuperadmin || $isAdmin)
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'],

            'personal_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::notIn([$user->email]),
            ],

            // Kode negara dari dropdown (+62, +1, +44, dst)
            'phone_code' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^\+[0-9]+$/',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\-\s]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'personal_email.not_in' => 'Email pribadi tidak boleh sama dengan email resmi SSO Anda.',
            'whatsapp.regex'        => 'Format nomor WhatsApp tidak valid. Masukkan angka saja.',
            'phone_code.regex'      => 'Format kode negara tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'personal_email' => 'email pribadi',
            'whatsapp'       => 'nomor WhatsApp',
            'phone_code'     => 'kode negara',
        ];
    }
}