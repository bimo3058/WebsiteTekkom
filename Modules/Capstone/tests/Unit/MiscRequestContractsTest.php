<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Admin\Monitoring\DestroyGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Monitoring\FlagMemberRequest;
use Modules\Capstone\Http\Requests\Admin\Monitoring\MessageGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Monitoring\PeerReviewsQueryRequest;
use Modules\Capstone\Http\Requests\Admin\Monitoring\ProgressQueryRequest;
use Modules\Capstone\Http\Requests\Admin\User\IndexUsersRequest;
use Modules\Capstone\Http\Requests\Auth\ExchangeTokenRequest;
use Modules\Capstone\Http\Requests\Auth\SetActiveRoleRequest;
use Modules\Capstone\Http\Requests\File\UploadFileRequest;
use Modules\Capstone\Http\Requests\Grade\GradeConsistencyQueryRequest;
use Modules\Capstone\Http\Requests\Grade\UpdateConsistencyCheckRequest;
use Modules\Capstone\Http\Requests\Registration\RegisterPeriodRequest;
use Modules\Capstone\Http\Requests\Report\ExportRequest;
use Modules\Capstone\Http\Requests\Report\SummaryRequest;
use Tests\TestCase;

/** Pure unit test: misc request contracts mirror the former inline rules. No database needed. */
class MiscRequestContractsTest extends TestCase
{
    public function test_report_summary_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => 'required|exists:capstone_periods,id'],
            (new SummaryRequest)->rules()
        );
    }

    public function test_report_export_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'period_id' => 'required|exists:capstone_periods,id',
                'group_id' => 'nullable|integer',
                'student_search' => 'nullable|string',
                'mode' => 'nullable|in:summary,detail',
            ],
            (new ExportRequest)->rules()
        );
    }

    public function test_upload_file_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['file' => ['required', 'file', 'max:10240']],
            (new UploadFileRequest)->rules()
        );
    }

    public function test_register_period_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => 'required|exists:capstone_periods,id'],
            (new RegisterPeriodRequest)->rules()
        );
    }

    public function test_exchange_token_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['ott' => 'required|string|size:64'],
            (new ExchangeTokenRequest)->rules()
        );
    }

    public function test_set_active_role_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['role' => ['required', 'string', 'in:admin,dosen,mahasiswa']],
            (new SetActiveRoleRequest)->rules()
        );
    }

    public function test_grade_consistency_query_rules_match_inline_contract(): void
    {
        // Shared by index + generate (byte-identical inline rules).
        $this->assertSame(
            ['period_id' => 'required|exists:capstone_periods,id'],
            (new GradeConsistencyQueryRequest)->rules()
        );
    }

    public function test_update_consistency_check_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'status' => 'sometimes|string|in:UNCHECKED,CONSISTENT,INCONSISTENT',
                'notes' => 'nullable|string',
            ],
            (new UpdateConsistencyCheckRequest)->rules()
        );
    }

    public function test_index_users_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'role' => 'nullable|in:admin,dosen,mahasiswa',
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,suspended',
                'sort_by' => 'nullable|in:name,email,created_at',
                'sort_order' => 'nullable|in:asc,desc',
                'per_page' => 'nullable|integer|min:1|max:100',
            ],
            (new IndexUsersRequest)->rules()
        );
    }

    public function test_monitoring_query_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => 'nullable|integer|exists:capstone_periods,id'],
            (new ProgressQueryRequest)->rules()
        );
        $this->assertSame(
            ['period_id' => 'nullable|integer|exists:capstone_periods,id'],
            (new PeerReviewsQueryRequest)->rules()
        );
    }

    public function test_message_group_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer',
                'message' => 'required|string|max:1000',
            ],
            (new MessageGroupRequest)->rules()
        );
    }

    public function test_flag_member_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['reason' => 'required|string|max:1000'],
            (new FlagMemberRequest)->rules()
        );
    }

    public function test_destroy_group_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['reason' => 'required|string|min:10|max:1000'],
            (new DestroyGroupRequest)->rules()
        );
    }
}
