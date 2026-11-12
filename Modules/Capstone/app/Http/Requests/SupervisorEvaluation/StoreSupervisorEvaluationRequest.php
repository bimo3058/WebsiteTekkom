<?php

namespace Modules\Capstone\Http\Requests\SupervisorEvaluation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StoreSupervisorEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Schema-aware validation: accepts both component field names so they
     * survive validation whichever schema the period uses. The service
     * resolves which field applies and rejects items missing both.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $componentTable = Schema::hasTable('capstone_period_assessment_components')
            ? 'capstone_period_assessment_components'
            : 'capstone_assessment_components';

        return [
            'group_id' => ['required', 'exists:capstone_groups,id'],
            'evaluation_type' => ['required', Rule::in(['BIMBINGAN_SEMPRO', 'NILAI_DOSEN', 'MILESTONE', 'BIMBINGAN_TA'])],
            'scores' => ['required', 'array'],
            'scores.*.student_id' => ['required', 'exists:students,id'],
            'scores.*.score' => ['required', 'numeric', 'min:1', 'max:100'],
            'scores.*.notes' => ['nullable', 'string'],
            // Include both component ID fields so they survive validation
            'scores.*.period_component_id' => ['nullable', 'integer', "exists:{$componentTable},id"],
            'scores.*.component_id' => ['nullable', 'integer', "exists:{$componentTable},id"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'group_id.required' => 'Group is required.',
            'group_id.exists' => 'Selected group does not exist.',
            'evaluation_type.required' => 'Evaluation type is required.',
            'evaluation_type.in' => 'Invalid evaluation type.',
            'scores.required' => 'Scores are required.',
        ];
    }
}
