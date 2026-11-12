<?php

namespace Modules\Capstone\Http\Requests\Admin\Finalization;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Group-centric dashboard filters.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'in:ready,final,post,others'],
            'sub_tab' => ['nullable', 'in:no_group,no_title,not_ready'],
            'search' => ['nullable', 'string', 'max:255'],
            'supervisor_status' => ['nullable', 'in:all,missing_sv1,missing_sv2,complete'],
            'member_count' => ['nullable', 'in:all,under_min,in_range,over_max'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
