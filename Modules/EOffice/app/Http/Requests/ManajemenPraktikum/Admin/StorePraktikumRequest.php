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
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50'],
            'matkul_id' => [
                'required',
                'integer',
                'exists:eo_matkul_praktikum,id',
                \Illuminate\Validation\Rule::unique('eo_praktikum', 'matkul_id')->where(function ($query) {
                    return $query->where('tahun_ajaran', $this->tahun_ajaran)
                        ->where('semester', $this->semester ? ucfirst(strtolower($this->semester)) : null)
                        ->whereNull('deleted_at');
                }),
            ],
            'deskripsi' => ['nullable', 'string'],
            // dosen_ids & koor_id mengacu ke users.id yang bertipe bigint
            'dosen_ids' => ['required', 'array', 'min:1', 'max:3'],
            'dosen_ids.*' => ['required', 'integer', 'exists:users,id'],
            'koor_id' => ['nullable', 'integer', 'exists:users,id'],
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            // Form kirim "Ganjil"/"Genap" (kapital), terima keduanya
            'semester' => ['required', 'in:Ganjil,Genap,ganjil,genap'],
            'status' => ['required', 'in:aktif,nonaktif'],
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

    public function messages(): array
    {
        return [
            'matkul_id.unique' => 'Praktikum untuk Mata Kuliah ini sudah ada pada Tahun Ajaran dan Semester tersebut. Tidak boleh ada praktikum ganda.',
        ];
    }
}