<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $jawaban_id
 * @property bool $is_ragu
 */
class ToggleRaguRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'jawaban_id' => ['required', 'integer', 'exists:bs_kompre_jawaban,id'],
            'is_ragu' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jawaban_id.required' => 'ID jawaban wajib disertakan.',
            'jawaban_id.exists' => 'Data jawaban tidak ditemukan.',
            'is_ragu.required' => 'Status ragu-ragu wajib disertakan.',
            'is_ragu.boolean' => 'Nilai ragu-ragu harus berupa true atau false.',
        ];
    }
}
