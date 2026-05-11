<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

class SaveAnswerRequest extends FormRequest
{
    /**
     * Otorisasi: hanya user yang sudah login (middleware auth sudah menjamin ini).
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'jawaban_id'    => ['required', 'integer', 'exists:bs_kompre_jawaban,id'],
            'opsi_terpilih' => ['required', 'integer', 'exists:bs_jawaban,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'jawaban_id.required'    => 'ID jawaban wajib disertakan.',
            'jawaban_id.exists'      => 'Data jawaban tidak ditemukan.',
            'opsi_terpilih.required' => 'Pilihan opsi wajib disertakan.',
            'opsi_terpilih.exists'   => 'Opsi jawaban tidak ditemukan.',
        ];
    }
}
