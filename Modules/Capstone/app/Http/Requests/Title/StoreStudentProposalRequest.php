<?php

namespace Modules\Capstone\Http\Requests\Title;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a new student title proposal.
 *
 * This is the store half of StudentProposalController::save(): the
 * inline rule made title_id `prohibited` when not editing. Split into
 * two classes because rules() cannot see the controller's $editing
 * argument; update carries the `required` half.
 */
class StoreStudentProposalRequest extends FormRequest
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
            'title_id' => 'prohibited',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'scope' => 'required|string',
            'specializations' => 'sometimes|array',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'proposed_supervisor_id' => 'required|integer|exists:lecturers,id',
        ];
    }
}
