<?php

namespace App\Http\Requests\Admin;

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
            'kode'         => ['nullable', 'string', 'max:50', 'unique:praktikum,kode'],
            'deskripsi'    => ['nullable', 'string'],
            'dosen_id'     => ['nullable', 'uuid', 'exists:pengguna,id'],
            'koor_id'      => ['nullable', 'uuid', 'exists:pengguna,id'],
            'tahun_ajaran' => ['required', 'integer', 'min:2000', 'max:2100'],
            'semester'     => ['required', 'in:ganjil,genap'],
            'status'       => ['nullable', 'in:aktif,nonaktif'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama praktikum wajib diisi.',
            'kode.unique'   => 'Kode praktikum sudah digunakan.',
            'dosen_id.exists' => 'Dosen yang dipilih tidak valid.',
            'koor_id.exists'  => 'Koordinator yang dipilih tidak valid.',
            'semester.in'   => 'Semester harus ganjil atau genap.',
        ];
    }
}
