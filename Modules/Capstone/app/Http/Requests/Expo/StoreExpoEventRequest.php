<?php

namespace Modules\Capstone\Http\Requests\Expo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new expo event.
 *
 * Rules copied verbatim from ExpoEventController::store; this class
 * carries shape only. prepareForValidation() replicates the controller's
 * normalizePeriodInput() verbatim (empty present period_id means
 * cross-period NULL): FormRequest validation runs before the controller
 * body, so without this the raw 0/'' from stale clients would fail the
 * `exists` rule. The controller keeps its own normalize call, which is
 * now a harmless no-op.
 */
class StoreExpoEventRequest extends FormRequest
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
            'period_id' => 'nullable|exists:capstone_periods,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'eoffice_ruangan_id' => 'required|integer|exists:eo_mr_ruangans,id',
            'capacity' => 'required|integer|min:1|max:200',
            'is_published' => 'boolean',
        ];
    }
}
