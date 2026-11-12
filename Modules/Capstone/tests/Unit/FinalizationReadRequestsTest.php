<?php

namespace Modules\Capstone\Tests\Unit;

use Modules\Capstone\Http\Requests\Admin\Finalization\AutoFixRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\DashboardRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ExportRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\SimulateRequest;
use Tests\TestCase;

/** Pure unit test: read-path request contracts mirror the former inline rules. No database needed. */
class FinalizationReadRequestsTest extends TestCase
{
    public function test_dashboard_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'tab' => ['nullable', 'in:ready,final,post,others'],
                'sub_tab' => ['nullable', 'in:no_group,no_title,not_ready'],
                'search' => ['nullable', 'string', 'max:255'],
                'supervisor_status' => ['nullable', 'in:all,missing_sv1,missing_sv2,complete'],
                'member_count' => ['nullable', 'in:all,under_min,in_range,over_max'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
                'page' => ['nullable', 'integer', 'min:1'],
            ],
            (new DashboardRequest)->rules()
        );
    }

    public function test_simulate_rules_match_inline_contract(): void
    {
        $this->assertSame(
            ['period_id' => ['required', 'exists:capstone_periods,id']],
            (new SimulateRequest)->rules()
        );
    }

    public function test_export_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'period_id' => ['required', 'exists:capstone_periods,id'],
                'format' => ['required', 'in:excel,pdf'],
            ],
            (new ExportRequest)->rules()
        );
    }

    public function test_auto_fix_rules_match_inline_contract(): void
    {
        $this->assertSame(
            [
                'mode' => ['nullable', 'in:safe,aggressive'],
                'group_id' => ['nullable', 'exists:capstone_groups,id'],
                'period_id' => ['nullable', 'exists:capstone_periods,id'],
            ],
            (new AutoFixRequest)->rules()
        );
    }
}
