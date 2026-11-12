<?php

namespace Modules\Capstone\Http\Requests\Grade;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWeightsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Update grade weight configuration.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pdc1_weights' => ['nullable', 'array'],
            'pdc1_weights.SEMPRO' => ['nullable', 'numeric', 'min:0'],
            'pdc1_weights.BIMBINGAN_SEMPRO' => ['nullable', 'numeric', 'min:0'],
            'pdc2_weights' => ['nullable', 'array'],
            'pdc2_weights.NILAI_DOSEN' => ['nullable', 'numeric', 'min:0'],
            'pdc2_weights.MILESTONE' => ['nullable', 'numeric', 'min:0'],
            'pdc2_weights.EXPO' => ['nullable', 'numeric', 'min:0'],
            'pdc2_weights.PEER_REVIEW' => ['nullable', 'numeric', 'min:0'],
            'ta_weights' => ['nullable', 'array'],
            'ta_weights.BIMBINGAN_TA' => ['nullable', 'numeric', 'min:0'],
            'ta_weights.SIDANG_TA' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
