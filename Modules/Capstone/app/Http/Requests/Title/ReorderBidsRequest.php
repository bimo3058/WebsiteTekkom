<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a bid priority reorder.
 *
 * Rules copied verbatim from BidController::reorder; this class
 * carries shape only, no authorization or side effects.
 */
class ReorderBidsRequest extends FormRequest
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
            'bids' => 'required|array|min:1|max:3',
            'bids.*.id' => 'required|integer|distinct',
            'bids.*.priority' => 'required|integer|distinct|min:1|max:3',
        ];
    }
}
