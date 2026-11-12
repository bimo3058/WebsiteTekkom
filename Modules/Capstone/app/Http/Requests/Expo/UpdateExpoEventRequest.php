<?php

namespace Modules\Capstone\Http\Requests\Expo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an expo event update.
 *
 * Rules copied verbatim from ExpoEventController::update; this class
 * carries shape only. prepareForValidation() replicates the controller's
 * normalizePeriodInput() verbatim (same reason as the store twin):
 * FormRequest validation runs before the controller body. The
 * controller keeps its own normalize call as a harmless no-op.
 */
class UpdateExpoEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize the period selector before validation: an empty value
     * means cross-period (global, NULL). Stale clients may send '' or 0.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('period_id') && empty($this->input('period_id'))) {
            $this->merge(['period_id' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'period_id' => 'sometimes|nullable|exists:capstone_periods,id',
            'name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date_format:Y-m-d',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'eoffice_ruangan_id' => 'sometimes|integer|exists:eo_mr_ruangans,id',
            'capacity' => 'sometimes|integer|min:1|max:200',
        ];
    }
}
