<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Admin\TitleManagement\UpdateTitleTextRequest;
use Modules\Capstone\Http\Requests\Period\StorePeriodRequest;
use Modules\Capstone\Http\Requests\Period\UpdatePeriodRequest;
use Modules\Capstone\Http\Requests\Title\RecommendBidRequest;
use Modules\Capstone\Http\Requests\Title\RejectTitleProposalRequest;
use Modules\Capstone\Http\Requests\Title\ReorderBidsRequest;
use Modules\Capstone\Http\Requests\Title\StoreBidRequest;
use Modules\Capstone\Http\Requests\Title\StoreStudentProposalRequest;
use Modules\Capstone\Http\Requests\Title\StoreTitleRequest;
use Modules\Capstone\Http\Requests\Title\UpdateStudentProposalRequest;
use Modules\Capstone\Http\Requests\Title\UpdateTitleRequest;
use Tests\TestCase;

/** Pure unit test: title/bid/period request contracts mirror the former inline rules. No database needed. */
class TitleRequestContractsTest extends TestCase
{
    public function test_store_title_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'problem_statement' => 'required|string',
                'scope' => 'required|string',
                'specializations' => 'required|array|min:1',
                'specializations.*' => 'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
                'quota' => 'required|integer|min:1',
            ],
            (new StoreTitleRequest)->rules()
        );
    }

    public function test_update_title_rules_match_inline_contract(): void
    {
        $rules = (new UpdateTitleRequest)->rules();

        $this->assertSame('sometimes|string|max:255', $rules['title']);
        $this->assertSame('sometimes|in:open,closed', $rules['status']);
        // Dosen titles stay cross-period: period_id can never be set via update.
        $this->assertSame('prohibited', $rules['period_id']);
    }

    public function test_store_bid_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'title_id' => 'required|integer|exists:capstone_titles,id',
                'priority' => 'sometimes|integer|min:1|max:3',
            ],
            (new StoreBidRequest)->rules()
        );
    }

    public function test_reorder_bids_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'bids' => 'required|array|min:1|max:3',
                'bids.*.id' => 'required|integer|distinct',
                'bids.*.priority' => 'required|integer|distinct|min:1|max:3',
            ],
            (new ReorderBidsRequest)->rules()
        );
    }

    public function test_recommend_bid_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['recommendation' => 'required|in:ACCEPT,REJECT,CANCEL'],
            (new RecommendBidRequest)->rules()
        );
    }

    public function test_reject_title_proposal_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['rejection_reason' => 'required|string|max:1000'],
            (new RejectTitleProposalRequest)->rules()
        );
    }

    public function test_store_student_proposal_rules_match_inline_contract(): void
    {
        $rules = (new StoreStudentProposalRequest)->rules();

        // Store half of the $editing split: title_id prohibited on create.
        $this->assertSame('prohibited', $rules['title_id']);
        $this->assertSame('required|string|max:255', $rules['title']);
        $this->assertSame('required|integer|exists:lecturers,id', $rules['proposed_supervisor_id']);
    }

    public function test_update_student_proposal_rules_match_inline_contract(): void
    {
        $rules = (new UpdateStudentProposalRequest)->rules();

        // Update half of the $editing split: title_id required on edit.
        $this->assertSame('required|integer|exists:capstone_titles,id', $rules['title_id']);
        $this->assertSame('required|string|max:255', $rules['title']);
    }

    public function test_store_period_rules_match_inline_contract(): void
    {
        $rules = (new StorePeriodRequest)->rules();

        $this->assertSame('required|string|max:255', $rules['name']);
        $this->assertSame('required|date|after:start_date', $rules['end_date']);
        $this->assertSame('nullable|integer|min:1|max:10', $rules['min_group_size']);
        $this->assertSame('nullable|integer|min:1|max:50', $rules['max_supervise_load']);
    }

    public function test_update_period_rules_match_inline_contract(): void
    {
        $rules = (new UpdatePeriodRequest)->rules();

        $this->assertSame('sometimes|string|max:255', $rules['name']);
        $this->assertSame('sometimes|date|after:start_date', $rules['end_date']);
        $this->assertSame('boolean', $rules['is_active']);
    }

    public function test_update_title_text_rules_match_inline_contract(): void
    {
        $rules = (new UpdateTitleTextRequest)->rules();

        $this->assertSame('sometimes|required|string|max:255', $rules['title']);
        $this->assertSame('sometimes|nullable|array|min:1', $rules['specializations']);
        // Governance columns: a text edit must never change these.
        foreach (['status', 'quota', 'lecturer_id', 'period_id', 'title_source', 'proposed_by_group_id', 'proposed_supervisor_id', 'supervisor_approval_status', 'approved_by_admin', 'is_reserved', 'pre_assigned_group_id', 'rejection_reason'] as $column) {
            $this->assertSame('prohibited', $rules[$column]);
        }
    }
}
