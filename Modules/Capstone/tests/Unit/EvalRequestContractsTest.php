<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Admin\PeriodRegistration\RejectPeriodRegistrationRequest;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\AttachStakeholderRequest;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\StoreStakeholderRequest;
use Modules\Capstone\Http\Requests\Admin\Stakeholder\UpdateStakeholderRequest;
use Modules\Capstone\Http\Requests\Admin\TaRegistration\RejectTaRegistrationRequest;
use Modules\Capstone\Http\Requests\DigitalSignature\SignDocumentRequest;
use Modules\Capstone\Http\Requests\Evaluation\StoreEvaluationRequest;
use Modules\Capstone\Http\Requests\Expo\StoreExpoEventRequest;
use Modules\Capstone\Http\Requests\Expo\UpdateExpoEventRequest;
use Tests\TestCase;

/** Pure unit test: eval/registration request contracts mirror the former inline rules. No database needed. */
class EvalRequestContractsTest extends TestCase
{
    public function test_store_evaluation_rules_match_inline_contract(): void
    {
        $rules = (new StoreEvaluationRequest)->rules();

        $this->assertSame('required|exists:capstone_groups,id', $rules['group_id']);
        $this->assertSame('required|exists:students,id', $rules['student_id']);
        // Rule::in object asserted via string form (precedent: GradeRequestContractsTest).
        $this->assertSame('required', (string) $rules['type'][0]);
        $this->assertStringContainsString('bimbingan', (string) $rules['type'][1]);
        $this->assertStringContainsString('proposal', (string) $rules['type'][1]);
        $this->assertStringContainsString('skripsi', (string) $rules['type'][1]);
        $this->assertSame('required|numeric|min:0|max:100', $rules['score']);
    }

    public function test_store_expo_event_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'period_id' => 'nullable|exists:capstone_periods,id',
                'name' => 'required|string|max:255',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'eoffice_ruangan_id' => 'required|integer|exists:eo_mr_ruangans,id',
                'capacity' => 'required|integer|min:1|max:200',
                'is_published' => 'boolean',
            ],
            (new StoreExpoEventRequest)->rules()
        );
    }

    public function test_update_expo_event_rules_match_inline_contract(): void
    {
        $rules = (new UpdateExpoEventRequest)->rules();

        $this->assertSame('sometimes|nullable|exists:capstone_periods,id', $rules['period_id']);
        $this->assertSame('sometimes|date_format:H:i', $rules['start_time']);
        $this->assertSame('sometimes|integer|exists:eo_mr_ruangans,id', $rules['eoffice_ruangan_id']);
    }

    public function test_reject_ta_registration_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectTaRegistrationRequest)->rules()
        );
    }

    public function test_reject_period_registration_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectPeriodRegistrationRequest)->rules()
        );
    }

    public function test_store_stakeholder_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'name' => 'required|string|max:255',
                'organization' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'type' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
            ],
            (new StoreStakeholderRequest)->rules()
        );
    }

    public function test_update_stakeholder_rules_match_inline_contract(): void
    {
        $rules = (new UpdateStakeholderRequest)->rules();

        $this->assertSame('sometimes|required|string|max:255', $rules['name']);
        $this->assertSame('nullable|email|max:255', $rules['email']);
    }

    public function test_attach_stakeholder_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'stakeholder_id' => 'required|exists:stakeholders,id',
                'role' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
            ],
            (new AttachStakeholderRequest)->rules()
        );
    }

    public function test_sign_document_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'document_reference' => 'required|string|max:255',
                'document_type' => 'required|string|in:SURAT_TUGAS,BERITA_ACARA,SURAT_KEPUTUSAN,LAINNYA',
                'signature_data' => 'required|string',
            ],
            (new SignDocumentRequest)->rules()
        );
    }
}
