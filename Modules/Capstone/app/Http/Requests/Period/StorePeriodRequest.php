<?php

namespace Modules\Capstone\Http\Requests\Period;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new period.
 *
 * Rules copied verbatim from PeriodController::store (including the
 * section comments); this class carries shape only, no authorization
 * or side effects.
 */
class StorePeriodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            // Bidding config
            'bidding_start' => 'nullable|date',
            'bidding_end' => 'nullable|date|after_or_equal:bidding_start',
            // Phase dates
            'pdc1_start' => 'nullable|date',
            'pdc1_end' => 'nullable|date|after_or_equal:pdc1_start',
            'pdc2_start' => 'nullable|date',
            'pdc2_end' => 'nullable|date|after_or_equal:pdc2_start',
            'expo_date' => 'nullable|date',
            'ta_start' => 'nullable|date',
            'ta_end' => 'nullable|date|after_or_equal:ta_start',
            // Group config
            'min_group_size' => 'nullable|integer|min:1|max:10',
            'max_group_size' => 'nullable|integer|min:1|max:10',
            'max_supervise_load' => 'nullable|integer|min:1|max:50',
        ];
    }
}
