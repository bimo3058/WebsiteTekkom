<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new lecturer title.
 *
 * Rules copied verbatim from TitleController::store; this class
 * carries shape only, no authorization or side effects.
 */
class StoreTitleRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'scope' => 'required|string',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'quota' => 'required|integer|min:1',
        ];
    }
}
