<?php

namespace Modules\EOffice\Http\Requests\ManajemenPraktikum\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePraktikumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'         => ['sometimes', 'string', 'max:255'],
            'kode'         => ['nullable', 'string', 'max:50'],
            'deskripsi'    => ['nullable', 'string'],
            'dosen_ids'    => ['required', 'array', 'min:1', 'max:3'],
            'dosen_ids.*'  => ['required', 'integer', 'exists:users,id'],
            'koor_id'      => ['nullable', 'integer', 'exists:users,id'],
            'tahun_ajaran' => ['sometimes', 'integer', 'min:2000'],
            'semester'     => ['sometimes', 'in:Ganjil,Genap,ganjil,genap'],
            'status'       => ['sometimes', 'in:aktif,nonaktif'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('semester')) {
            $this->merge(['semester' => ucfirst(strtolower($this->semester))]);
        }
    }
}
