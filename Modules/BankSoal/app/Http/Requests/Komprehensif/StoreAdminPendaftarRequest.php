<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi form tambah peserta manual oleh admin.
 *
 * Duplikat NIM cek di sini (unique scoped periode + whereNull deleted_at)
 * agar error langsung masuk bag 'pendaftar', plus guard atomik di controller + DB index.
 */
class StoreAdminPendaftarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'periode_ujian_id'      => ['required', 'exists:bs_periode_ujians,id'],
            'nim'                   => [
                'required', 'string', 'max:50',
                Rule::unique('bs_pendaftar_ujians', 'nim')->where(function ($query) {
                    return $query->where('periode_ujian_id', $this->input('periode_ujian_id'))
                                 ->whereNull('deleted_at');
                }),
            ],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'semester_aktif'        => ['required', 'integer', 'min:1', 'max:20'],
            'target_wisuda'         => ['nullable', 'string', 'max:100'],
            'dosen_pembimbing_1_id' => ['nullable', 'exists:users,id'],
            'dosen_pembimbing_2_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
