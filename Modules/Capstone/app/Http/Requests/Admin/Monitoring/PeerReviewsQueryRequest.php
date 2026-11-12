<?php

namespace Modules\Capstone\Http\Requests\Admin\Monitoring;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the optional monitoring period filter (peer-review feed).
 *
 * Rules copied verbatim from BladeMonitoringController::peerReviews;
 * this class carries shape only. The caller discards the validated
 * payload (validation side effect only) and reads $request->filled/integer
 * directly.
 */
class PeerReviewsQueryRequest extends FormRequest
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
        return ['period_id' => 'nullable|integer|exists:capstone_periods,id'];
    }
}
