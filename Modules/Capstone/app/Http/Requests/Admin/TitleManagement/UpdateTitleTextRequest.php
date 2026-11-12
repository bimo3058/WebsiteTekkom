<?php

namespace Modules\Capstone\Http\Requests\Admin\TitleManagement;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate an admin text-only title edit.
 *
 * Rules copied verbatim from TitleManagementController::update; this
 * class carries shape only. The governance `prohibited` columns stay
 * exactly as listed: a text edit must never change them.
 */
class UpdateTitleTextRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'problem_statement' => 'sometimes|nullable|string',
            'scope' => 'sometimes|nullable|string',
            'specializations' => 'sometimes|nullable|array|min:1',
            'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            // Governance columns: admin text edit must never change these.
            'status' => 'prohibited',
            'quota' => 'prohibited',
            'lecturer_id' => 'prohibited',
            'period_id' => 'prohibited',
            'title_source' => 'prohibited',
            'proposed_by_group_id' => 'prohibited',
            'proposed_supervisor_id' => 'prohibited',
            'supervisor_approval_status' => 'prohibited',
            'approved_by_admin' => 'prohibited',
            'is_reserved' => 'prohibited',
            'pre_assigned_group_id' => 'prohibited',
            'rejection_reason' => 'prohibited',
        ];
    }
}
