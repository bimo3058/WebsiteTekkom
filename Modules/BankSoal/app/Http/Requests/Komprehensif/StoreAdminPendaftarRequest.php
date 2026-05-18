<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form tambah peserta manual oleh admin.
 *
 * Catatan: pengecekan duplikat NIM dan eksistensi mahasiswa di sistem
 * dilakukan di controller karena membutuhkan named error bag 'pendaftar'.
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
            'nim'                   => ['required', 'string', 'max:50'],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'semester_aktif'        => ['required', 'integer', 'min:1', 'max:20'],
            'target_wisuda'         => ['nullable', 'string', 'max:100'],
            'dosen_pembimbing_1_id' => ['nullable', 'exists:users,id'],
            'dosen_pembimbing_2_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
