<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class ReopenPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['period_id' => 'required|exists:capstone_periods,id'];
    }
}
