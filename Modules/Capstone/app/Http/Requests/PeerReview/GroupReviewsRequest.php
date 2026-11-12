<?php

namespace Modules\Capstone\Http\Requests\PeerReview;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the supervised-group scope for peer review results.
 *
 * Rules copied verbatim from PeerReviewController::groupReviews; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->group_id directly.
 */
class GroupReviewsRequest extends FormRequest
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
            'group_id' => 'required|exists:capstone_groups,id',
        ];
    }
}
