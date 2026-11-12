<?php

namespace Modules\Capstone\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a document type update.
 *
 * Rules copied verbatim from DocumentTypeController::update;
 * this class carries shape only, no authorization or side effects.
 */
class UpdateDocumentTypeRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'phase' => 'nullable|string|in:PDC1,SEMPRO,PDC2,TA,EXPO,SIDANG',
            'is_active' => 'boolean',
        ];
    }
}
