<?php

namespace Modules\Capstone\Http\Requests\Period;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Capstone\Http\Controllers\BladePeriodController;

/**
 * Validate a Blade period save (store + update share one saver).
 *
 * Replicates BladePeriodController::save()'s programmatic $rules
 * construction verbatim: the loops over the controller's TYPES constant
 * and the phase/reminder date fields run here instead. The rule set is
 * static (no request-input dependence), so moving it into rules() is
 * behavior-identical. store/update/save signatures take this class.
 */
class SavePeriodRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'required|boolean',
            'min_group_size' => 'required|integer|min:1|max:10',
            'max_group_size' => 'required|integer|min:1|max:10|gte:min_group_size',
            'max_supervisor_load' => 'required|integer|min:1|max:50',
            'assessments' => 'required|array:'.implode(',', BladePeriodController::TYPES),
            'peer_ids' => 'present|array',
            'peer_ids.*' => 'integer|distinct|exists:capstone_peer_review_indicator_templates,id',
        ];
        foreach (BladePeriodController::TYPES as $type) {
            $rules['assessments.'.$type] = 'present|array';
            $rules['assessments.'.$type.'.*'] = 'integer|distinct|exists:capstone_assessment_component_templates,id';
        }
        foreach (['bidding', 'pdc1', 'pdc2', 'ta'] as $phase) {
            $rules[$phase.'_start'] = 'nullable|date';
            $rules[$phase.'_end'] = 'nullable|date|after_or_equal:'.$phase.'_start';
        }
        foreach (['expo_date', 'bidding_reminder_at', 'pdc1_reminder_at', 'pdc2_reminder_at', 'expo_reminder_at', 'ta_reminder_at'] as $field) {
            $rules[$field] = 'nullable|date';
        }

        return $rules;
    }
}
