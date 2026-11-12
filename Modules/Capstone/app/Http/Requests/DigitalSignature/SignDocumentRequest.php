<?php

namespace Modules\Capstone\Http\Requests\DigitalSignature;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate a document signature request.
 *
 * Rules copied verbatim from DigitalSignatureController::sign; this
 * class carries shape only. The caller discards the validated payload
 * (validation side effect only) and reads $request->document_reference
 * etc. directly.
 */
class SignDocumentRequest extends FormRequest
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
            'document_reference' => 'required|string|max:255',
            'document_type' => 'required|string|in:SURAT_TUGAS,BERITA_ACARA,SURAT_KEPUTUSAN,LAINNYA',
            'signature_data' => 'required|string', // base64 signature image
        ];
    }
}
