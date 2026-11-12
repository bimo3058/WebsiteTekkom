<?php

namespace Modules\Capstone\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a one-time-token exchange.
 *
 * Rules copied verbatim from AuthBridgeController::exchange; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->ott directly.
 */
class ExchangeTokenRequest extends FormRequest
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
            'ott' => 'required|string|size:64',
        ];
    }
}
