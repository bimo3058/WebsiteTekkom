<?php

namespace Modules\Capstone\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\WorkflowPhases;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $requirements = $this->phaseRequirements();

        return [
            'phase' => ['required', 'string', Rule::in(WorkflowPhases::UI_PHASES)],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'document_type' => $requirements !== []
                ? ['required', 'string', Rule::in($requirements)]
                : ['nullable', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Requirement names for the requested phase in the caller's period.
     * Membership and workflow guards stay in the service; this only shapes
     * the document_type rule. Fail-open to nullable when the membership
     * cannot be resolved (the service returns the 400 then).
     *
     * @return array<int, string>
     */
    private function phaseRequirements(): array
    {
        try {
            $phase = $this->input('phase');
            if (! is_string($phase) || $phase === '') {
                return [];
            }
            $member = GroupMember::where('student_id', CapstoneActor::student($this->user())->id)->first();
            if (! $member || ! $member->group) {
                return [];
            }

            return PhaseDocumentRequirement::where('period_id', $member->group->period_id)
                ->where('phase', $phase)
                ->pluck('name')
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }
}
