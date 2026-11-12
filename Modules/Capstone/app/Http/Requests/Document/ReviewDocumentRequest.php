<?php

namespace Modules\Capstone\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewDocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * Shared by phase-document and expo-document review: both accept the
     * 3-status review where APPROVED locks and REVISE/REJECTED let the
     * student re-upload.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['APPROVED', 'REVISE', 'REJECTED'])],
            'feedback' => ['nullable', 'string'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
