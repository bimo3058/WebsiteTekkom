<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Controllers\BladePeriodController;
use Modules\Capstone\Http\Requests\Assessment\CopyPeerReviewConfigRequest;
use Modules\Capstone\Http\Requests\Assessment\StorePeerReviewConfigRequest;
use Modules\Capstone\Http\Requests\BursaIde\RequestJoinRequest;
use Modules\Capstone\Http\Requests\Expo\RejectExpoRequest;
use Modules\Capstone\Http\Requests\Expo\SubmitExpoScoresRequest;
use Modules\Capstone\Http\Requests\Expo\UploadExpoDocumentRequest;
use Modules\Capstone\Http\Requests\IndividualTa\UploadTaDocumentRequest;
use Modules\Capstone\Http\Requests\PeerReview\GroupReviewsRequest;
use Modules\Capstone\Http\Requests\PeerReview\IndicatorsRequest;
use Modules\Capstone\Http\Requests\PeerReview\StoreIndicatorRequest;
use Modules\Capstone\Http\Requests\PeerReview\StorePeerReviewsRequest;
use Modules\Capstone\Http\Requests\PeerReview\UpdateIndicatorRequest;
use Modules\Capstone\Http\Requests\Period\SavePeriodRequest;
use Modules\Capstone\Http\Requests\Sempro\ApproveSemproRequest;
use Modules\Capstone\Http\Requests\Sempro\EvaluateSemproRequest;
use Modules\Capstone\Http\Requests\Sempro\RejectSemproRequest;
use Modules\Capstone\Http\Requests\Sempro\ScheduleSemproRequest;
use Modules\Capstone\Http\Requests\Sempro\UpdateSemproScheduleRequest;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\AdminScheduleSummaryRequest;
use Modules\Capstone\Http\Requests\TaDefense\ApproveTaDefenseRequest;
use Modules\Capstone\Http\Requests\TaDefense\EvaluateTaDefenseRequest;
use Modules\Capstone\Http\Requests\TaDefense\RejectTaDefenseRequest;
use Modules\Capstone\Http\Requests\TaDefense\ScheduleTaDefenseRequest;
use Modules\Capstone\Http\Requests\TaSubmission\ReviewTaSubmissionRequest;
use Modules\Capstone\Http\Requests\TaSubmission\TaSubmissionFileRequest;
use Tests\TestCase;

/** Pure unit test: heavy-controller request contracts mirror the former inline rules. No database needed. */
class HeavyRequestContractsTest extends TestCase
{
    public function test_update_sempro_schedule_rules_match_inline_contract(): void
    {
        $rules = (new UpdateSemproScheduleRequest)->rules();

        $this->assertSame('required|date', $rules['date']);
        $this->assertSame('required|integer|exists:eo_mr_ruangans,id', $rules['eoffice_ruangan_id']);
        $this->assertSame('required|exists:lecturers,id|different:examiner_1_id', $rules['examiner_2_id']);
    }

    public function test_schedule_sempro_rules_match_inline_contract(): void
    {
        $rules = (new ScheduleSemproRequest)->rules();

        $this->assertSame('required|exists:capstone_groups,id', $rules['group_id']);
        $this->assertSame('required|integer|exists:eo_mr_ruangans,id', $rules['eoffice_ruangan_id']);
    }

    public function test_evaluate_sempro_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'rubric_json' => 'required|array',
                'score' => 'required|numeric|min:0|max:100',
                'result' => 'required|in:PASS,FAIL',
            ],
            (new EvaluateSemproRequest)->rules()
        );
    }

    public function test_approve_sempro_rules_match_inline_contract(): void
    {
        $rules = (new ApproveSemproRequest)->rules();

        $this->assertSame('nullable|integer|exists:eo_mr_ruangans,id', $rules['eoffice_ruangan_id']);
        $this->assertSame('nullable|string|max:255', $rules['room']);
        $this->assertSame('required|exists:lecturers,id', $rules['examiner_1_id']);
    }

    public function test_reject_sempro_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectSemproRequest)->rules()
        );
    }

    public function test_schedule_ta_defense_rules_match_inline_contract(): void
    {
        $rules = (new ScheduleTaDefenseRequest)->rules();

        $this->assertSame('required|exists:students,id', $rules['student_id']);
        $this->assertSame('nullable|string', $rules['room']);
    }

    public function test_evaluate_ta_defense_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'rubric_json' => 'required|array',
                'score' => 'required|numeric|min:0|max:100',
                'result' => 'required|in:PASS,FAIL',
            ],
            (new EvaluateTaDefenseRequest)->rules()
        );
    }

    public function test_approve_ta_defense_rules_match_inline_contract(): void
    {
        $rules = (new ApproveTaDefenseRequest)->rules();

        $this->assertSame('required|date', $rules['date']);
        $this->assertSame('nullable|string', $rules['room']);
    }

    public function test_reject_ta_defense_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectTaDefenseRequest)->rules()
        );
    }

    public function test_ta_submission_file_rules_match_inline_contract(): void
    {
        // Shared by upload + revise (byte-identical inline rules).
        $this->assertSame(
            ['file_path' => 'required|string'],
            (new TaSubmissionFileRequest)->rules()
        );
    }

    public function test_review_ta_submission_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'result' => 'required|in:APPROVE,REVISE',
                'feedback' => 'nullable|string',
            ],
            (new ReviewTaSubmissionRequest)->rules()
        );
    }

    public function test_submit_expo_scores_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'scores' => 'required|array|min:1',
                'scores.*.period_component_id' => 'required|integer|distinct',
                'scores.*.score' => 'required|numeric|min:1|max:100',
                'scores.*.notes' => 'nullable|string|max:5000',
            ],
            (new SubmitExpoScoresRequest)->rules()
        );
    }

    public function test_upload_expo_document_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240'],
            (new UploadExpoDocumentRequest)->rules()
        );
    }

    public function test_reject_expo_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectExpoRequest)->rules()
        );
    }

    public function test_store_peer_reviews_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'reviews' => 'required|array|min:1',
                'reviews.*.reviewee_id' => 'required|integer',
                'reviews.*.indicator_id' => 'nullable|integer',
                'reviews.*.period_indicator_id' => 'nullable|integer',
                'reviews.*.score' => 'required|numeric|min:0|max:100',
                'reviews.*.comment' => 'nullable|string|max:5000',
            ],
            (new StorePeerReviewsRequest)->rules()
        );
    }

    public function test_group_reviews_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['group_id' => 'required|exists:capstone_groups,id'],
            (new GroupReviewsRequest)->rules()
        );
    }

    public function test_indicators_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => 'required|exists:capstone_periods,id'],
            (new IndicatorsRequest)->rules()
        );
    }

    public function test_store_indicator_rules_match_inline_contract(): void
    {
        $rules = (new StoreIndicatorRequest)->rules();

        $this->assertSame('required|exists:capstone_periods,id', $rules['period_id']);
        $this->assertSame('required|string|max:255', $rules['name']);
        $this->assertSame('required|numeric|min:0|max:100', $rules['weight']);
    }

    public function test_update_indicator_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'weight' => 'sometimes|numeric|min:0|max:100',
                'sort_order' => 'nullable|integer',
            ],
            (new UpdateIndicatorRequest)->rules()
        );
    }

    public function test_request_join_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['message' => 'nullable|string|max:500'],
            (new RequestJoinRequest)->rules()
        );
    }

    public function test_upload_ta_document_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'document_type' => 'required|string|max:255',
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            ],
            (new UploadTaDocumentRequest)->rules()
        );
    }

    public function test_save_period_rules_match_inline_contract(): void
    {
        $rules = (new SavePeriodRequest)->rules();

        $this->assertSame('required|string|max:255', $rules['name']);
        $this->assertSame('required|integer|min:1|max:10|gte:min_group_size', $rules['max_group_size']);
        // Programmatic loops replicate the saver's construction exactly.
        foreach (BladePeriodController::TYPES as $type) {
            $this->assertSame('present|array', $rules['assessments.'.$type]);
            $this->assertSame('integer|distinct|exists:capstone_assessment_component_templates,id', $rules['assessments.'.$type.'.*']);
        }
        $this->assertSame('nullable|date|after_or_equal:pdc1_start', $rules['pdc1_end']);
        $this->assertSame('nullable|date', $rules['expo_date']);
        $this->assertSame('integer|distinct|exists:capstone_peer_review_indicator_templates,id', $rules['peer_ids.*']);
    }

    public function test_admin_schedule_summary_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['schedule_source' => 'nullable|in:schedule,seminar,ta'],
            (new AdminScheduleSummaryRequest)->rules()
        );
    }

    public function test_store_peer_review_config_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'template_ids' => 'present|array',
                'template_ids.*' => 'integer|distinct|exists:capstone_peer_review_indicator_templates,id',
            ],
            (new StorePeerReviewConfigRequest)->rules()
        );
    }

    public function test_copy_peer_review_config_rules_match_inline_contract(): void
    {
        $rules = (new CopyPeerReviewConfigRequest)->rules();

        $this->assertSame('required', $rules['source_period_id'][0]);
        $this->assertSame('integer', $rules['source_period_id'][1]);
        $this->assertSame('exists:capstone_periods,id', $rules['source_period_id'][2]);
        $this->assertStringStartsWith('not_in:', $rules['source_period_id'][3]);
    }
}
