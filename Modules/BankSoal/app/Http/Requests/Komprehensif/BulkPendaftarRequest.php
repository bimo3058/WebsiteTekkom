<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi aksi bulk (approve / reject) pada daftar peserta.
 * Digunakan bersama oleh bulkApprove() dan bulkReject().
 */
class BulkPendaftarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:bs_pendaftar_ujians,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Pilih minimal satu peserta.',
            'ids.min'      => 'Pilih minimal satu peserta.',
        ];
    }
}
