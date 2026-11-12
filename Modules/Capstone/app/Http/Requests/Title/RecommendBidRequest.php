<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a lecturer recommendation on a bid.
 *
 * Rules copied verbatim from BidController::recommend; this class
 * carries shape only. The caller discards the validated payload
 * (validation side effect only).
 */
class RecommendBidRequest extends FormRequest
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
            'recommendation' => 'required|in:ACCEPT,REJECT,CANCEL',
        ];
    }
}
