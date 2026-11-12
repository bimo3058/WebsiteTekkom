<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a peer review indicator template update.
 *
 * Rules copied verbatim from PeerReviewIndicatorTemplateController::update;
 * this class carries shape only, no authorization or side effects.
 * The unique-ignore id resolves from the apiResource route parameter.
 *
 * Note: the controller is currently unrouted (no entries in routes/*.php),
 * so these endpoints are unreachable until routes are registered.
 */
class UpdateIndicatorTemplateRequest extends FormRequest
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
            'code' => 'sometimes|string|max:50|unique:capstone_peer_review_indicator_templates,code,'.$this->route('peer_review_indicator_template'),
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'sometimes|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
