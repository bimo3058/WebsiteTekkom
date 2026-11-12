<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SimulateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dry-run of execute.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'period_id' => ['required', 'exists:capstone_periods,id'],
        ];
    }
}
