<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a lecturer title update.
 *
 * Rules copied verbatim from TitleController::update; this class
 * carries shape only, no authorization or side effects. The caller
 * keeps its defensive unset($validated['period_id']) line: `prohibited`
 * already guarantees the key is absent, so the line is a no-op kept
 * for exactness.
 */
class UpdateTitleRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'problem_statement' => 'sometimes|string',
            'scope' => 'sometimes|string',
            'specializations' => 'sometimes|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'quota' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:open,closed',
            // Dosen titles stay cross-period: period_id can never be set via update.
            'period_id' => 'prohibited',
        ];
    }
}
