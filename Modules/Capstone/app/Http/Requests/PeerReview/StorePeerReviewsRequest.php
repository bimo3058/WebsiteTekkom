<?php

namespace Modules\Capstone\Http\Requests\PeerReview;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a peer review submission batch.
 *
 * Rules copied verbatim from PeerReviewController::store; this class
 * carries shape only, no authorization or side effects.
 */
class StorePeerReviewsRequest extends FormRequest
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
            'reviews' => 'required|array|min:1',
            'reviews.*.reviewee_id' => 'required|integer',
            'reviews.*.indicator_id' => 'nullable|integer',
            'reviews.*.period_indicator_id' => 'nullable|integer',
            'reviews.*.score' => 'required|numeric|min:0|max:100',
            'reviews.*.comment' => 'nullable|string|max:5000',
        ];
    }
}
