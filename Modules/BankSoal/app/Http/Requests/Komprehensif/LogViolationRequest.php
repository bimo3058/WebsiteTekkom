<?php

namespace Modules\BankSoal\Http\Requests\Komprehensif;

use Illuminate\Foundation\Http\FormRequest;

class LogViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'event_type'  => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_type.required' => 'Tipe kejadian wajib disertakan.',
            'event_type.max'      => 'Tipe kejadian maksimal 100 karakter.',
            'description.max'     => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}
