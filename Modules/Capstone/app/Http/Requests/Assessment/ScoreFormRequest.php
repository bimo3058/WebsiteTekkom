<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Assessment form (components + existing scores) for a group.
 *
 * EXPO is deliberately not accepted: the engine grades EXPO only from
 * member self-evaluations, so split-table EXPO writes would be silently
 * ignored (see ADR 0012). Use the Expo self-evaluation flow instead.
 */
class ScoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'exists:capstone_groups,id'],
            'type' => ['required', 'string', Rule::in(['SEMPRO', 'SIDANG_TA', 'BIMBINGAN'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.in' => 'EXPO scores are recorded through member self-evaluation; submit them via the Expo self-evaluation flow instead.',
        ];
    }
}
