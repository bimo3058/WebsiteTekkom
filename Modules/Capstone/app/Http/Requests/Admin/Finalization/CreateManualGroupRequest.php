<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Foundation\Http\FormRequest;

class CreateManualGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
            'period_id' => 'required|exists:capstone_periods,id',
            'option' => 'required|in:no_title,assign_title,add_title',
            'title_id' => 'required_if:option,assign_title|nullable|exists:capstone_titles,id',
            'new_title.title' => 'required_if:option,add_title|nullable|string|max:255',
            'new_title.description' => 'nullable|string',
            'new_title.specializations' => 'nullable|array',
            'new_title.lecturer_id' => 'required_if:option,add_title|nullable|exists:lecturers,id',
        ];
    }
}
