<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new bid on a lecturer title.
 *
 * Rules copied verbatim from BidController::store; this class
 * carries shape only, no authorization or side effects.
 */
class StoreBidRequest extends FormRequest
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
            'title_id' => 'required|integer|exists:capstone_titles,id',
            'priority' => 'sometimes|integer|min:1|max:3',
        ];
    }
}
