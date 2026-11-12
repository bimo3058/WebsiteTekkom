<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class SetSupervisorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'group_id' => 'required|exists:capstone_groups,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
            'notes' => 'nullable|string|max:1000',
            'mark_final' => 'nullable|boolean',
        ];
    }
}
