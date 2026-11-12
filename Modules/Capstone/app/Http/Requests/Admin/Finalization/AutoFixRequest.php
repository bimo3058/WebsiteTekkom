<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AutoFixRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Auto-fix group readiness.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mode' => ['nullable', 'in:safe,aggressive'],
            'group_id' => ['nullable', 'exists:capstone_groups,id'],
            'period_id' => ['nullable', 'exists:capstone_periods,id'],
        ];
    }
}
