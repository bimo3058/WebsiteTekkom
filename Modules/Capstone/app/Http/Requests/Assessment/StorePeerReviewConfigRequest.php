<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a peer review configuration save for a period.
 *
 * Rules copied verbatim from PeriodPeerReviewConfigController::store;
 * this class carries shape only. The caller discards the validated
 * payload (validation side effect only) and reads $request->template_ids
 * directly.
 */
class StorePeerReviewConfigRequest extends FormRequest
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
            'template_ids' => 'present|array',
            'template_ids.*' => 'integer|distinct|exists:capstone_peer_review_indicator_templates,id',
        ];
    }
}
