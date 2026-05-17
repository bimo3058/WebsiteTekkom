<?php

namespace Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePraktikumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'         => ['required', 'string', 'max:255'],
            'kode'         => ['nullable', 'string', 'max:50'],
            'matkul_id'    => ['nullable', 'integer', 'exists:eo_matkul_praktikum,id'],
            'deskripsi'    => ['nullable', 'string'],
            // dosen_id & koor_id mengacu ke users.id yang bertipe bigint
            'dosen_id'     => ['nullable', 'integer', 'exists:users,id'],
            'koor_id'      => ['nullable', 'integer', 'exists:users,id'],
            'tahun_ajaran' => ['required', 'integer', 'min:2000'],
            // Form kirim "Ganjil"/"Genap" (kapital), terima keduanya
            'semester'     => ['required', 'in:Ganjil,Genap,ganjil,genap'],
            'status'       => ['sometimes', 'in:aktif,nonaktif'],
        ];
    }

    /**
     * Normalize semester ke Title Case sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('semester')) {
            $this->merge(['semester' => ucfirst(strtolower($this->semester))]);
        }
    }
}