<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an assessment configuration copy from another period.
 *
 * Copied from PeriodAssessmentConfigController::copy with one mechanical
 * adaptation: the inline rule concatenated the controller's $periodId
 * argument ('not_in:'.$periodId), which a rules() method cannot see, so
 * the id resolves from the {period} route parameter instead — the same
 * value, since the route supplies that argument positionally.
 */
class CopyAssessmentConfigRequest extends FormRequest
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
            'source_period_id' => ['required', 'integer', 'exists:capstone_periods,id', 'not_in:'.$this->route('period')],
        ];
    }
}
