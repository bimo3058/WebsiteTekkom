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
            'deskripsi'    => ['nullable', 'string'],
            'dosen_id'     => ['nullable', 'uuid', 'exists:users,id'],
            'koor_id'      => ['nullable', 'uuid', 'exists:users,id'],
            'tahun_ajaran' => ['required', 'integer', 'min:2000'],
            'semester'     => ['required', 'in:ganjil,genap'],
            'status'       => ['sometimes', 'in:aktif,nonaktif'],
        ];
    }
}
