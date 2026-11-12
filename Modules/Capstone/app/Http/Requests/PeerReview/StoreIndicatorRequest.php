<?php

namespace Modules\Capstone\Http\Requests\PeerReview;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new peer review indicator.
 *
 * Rules copied verbatim from PeerReviewController::storeIndicator;
 * this class carries shape only, no authorization or side effects.
 */
class StoreIndicatorRequest extends FormRequest
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
            'period_id' => 'required|exists:capstone_periods,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
        ];
    }
}
