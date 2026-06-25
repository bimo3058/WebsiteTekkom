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
            'nama' => ['sometimes', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50'],
            'matkul_id' => [
                'required',
                'integer',
                'exists:eo_matkul_praktikum,id',
                \Illuminate\Validation\Rule::unique('eo_praktikum', 'matkul_id')->where(function ($query) {
                    return $query->where('tahun_ajaran', $this->tahun_ajaran ?? \Modules\EOffice\Models\Praktikum::find($this->route('praktikum'))?->tahun_ajaran)
                        ->where('semester', $this->semester ? ucfirst(strtolower($this->semester)) : \Modules\EOffice\Models\Praktikum::find($this->route('praktikum'))?->semester)
                        ->whereNull('deleted_at');
                })->ignore($this->route('praktikum')),
            ],
            'deskripsi' => ['nullable', 'string'],
            'dosen_ids' => ['required', 'array', 'min:1', 'max:3'],
            'dosen_ids.*' => ['required', 'integer', 'exists:users,id'],
            'koor_id' => ['nullable', 'integer', 'exists:users,id'],
            'tahun_ajaran' => ['sometimes', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['sometimes', 'in:Ganjil,Genap,ganjil,genap'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ];
    }

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
