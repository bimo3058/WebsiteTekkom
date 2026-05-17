<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form pendaftaran ujian komprehensif oleh mahasiswa.
 */
class StorePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Middleware 'role:mahasiswa' sudah menjaga akses rute ini,
        // tapi kita tetap eksplisit di sini sebagai defense in depth.
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nim'                   => ['required', 'string'],
            'nama'                  => ['required', 'string'],
            'kontak_wa'             => ['required', 'string', 'max:20'],
            'semester'              => ['required', 'integer', 'min:7'],
            'target_wisuda'         => ['required', 'string'],
            'dosen_pembimbing_1_id' => ['required', 'exists:users,id'],
            'dosen_pembimbing_2_id' => ['nullable', 'exists:users,id', 'different:dosen_pembimbing_1_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'dosen_pembimbing_2_id.different' => 'Dosen Pembimbing 2 tidak boleh sama dengan Dosen Pembimbing 1',
            'semester.min'                    => 'Mahasiswa minimal semester 7',
        ];
    }
}
