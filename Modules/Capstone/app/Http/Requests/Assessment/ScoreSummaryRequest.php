<?php

namespace Modules\Capstone\Http\Requests\Assessment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin summary of aggregated scores.
 *
 * EXPO stays accepted here: the summary serves old split-table rows
 * read-only for audit (writes are rejected since ADR 0012).
 */
class ScoreSummaryRequest extends FormRequest
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
            'period_id' => ['required', 'exists:capstone_periods,id'],
            'type' => ['required', 'string', Rule::in(['SEMPRO', 'SIDANG_TA', 'EXPO', 'BIMBINGAN'])],
        ];
    }
}
