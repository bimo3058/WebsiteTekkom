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
            // Nama hanya divalidasi jika boleh diedit — kalau tidak, tetap lolos tapi diabaikan di controller
            'name' => ($isSuperadmin || $isAdmin)
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'],   // tetap terima tapi controller akan abaikan

            // Email pribadi (opsional, bukan email login)
            'personal_email' => [
                'nullable',
                'email',
                'max:255',
                // Pastikan tidak sama dengan email SSO
                Rule::notIn([$user->email]),
            ],

            // WhatsApp opsional
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
        ];
    }

    public function attributes(): array
    {
        return [
            'personal_email' => 'email pribadi',
            'whatsapp'       => 'nomor WhatsApp',
        ];
    }
}