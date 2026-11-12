<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class RollbackFinalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:capstone_periods,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'integer|exists:capstone_groups,id',
            'reason' => 'required|string|max:1000',
        ];
    }
}
