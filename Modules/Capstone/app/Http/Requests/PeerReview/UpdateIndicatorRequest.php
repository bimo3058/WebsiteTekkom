<?php

namespace Modules\Capstone\Http\Requests\PeerReview;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a peer review indicator update.
 *
 * Rules copied verbatim from PeerReviewController::updateIndicator;
 * this class carries shape only, no authorization or side effects.
 */
class UpdateIndicatorRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'sometimes|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
        ];
    }
}
