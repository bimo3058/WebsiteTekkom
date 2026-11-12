<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new peer review indicator template (bank soal).
 *
 * Rules copied verbatim from PeerReviewIndicatorTemplateController::store;
 * this class carries shape only, no authorization or side effects.
 *
 * Note: the controller is currently unrouted (no entries in routes/*.php),
 * so these endpoints are unreachable until routes are registered.
 */
class StoreIndicatorTemplateRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:capstone_peer_review_indicator_templates',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
        ];
    }
}
