<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\FinalizationController;
use Modules\Capstone\Http\Requests\Admin\Finalization\AddToExistingGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\AssignTitleRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\BatchSetSupervisorRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\CancelKelompokFinalRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\CreateManualGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ExecuteFinalizationRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\FinalizePeriodFlagRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ForceReadyRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\PromoteToReadyRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ReopenPeriodRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\RollbackFinalizationRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\SetSupervisorRequest;
use Tests\TestCase;

/**
 * Characterization: finalization write endpoints, pinned byte-for-byte
 * before extraction to FinalizationService + Form Requests.
 * Isolated real SQL: never migrate the application's configured database.
 */
class FinalizationWritesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.finalization_writes_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('finalization_writes_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->string('student_number')->nullable();
            $t->timestamps();
        });
        Schema::create('lecturers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_finalized')->default(false);
            $t->integer('min_group_size')->default(3);
            $t->integer('max_group_size')->default(4);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('title_id')->nullable();
            $t->string('code')->nullable();
            $t->string('status')->default('FORMING');
            $t->unsignedBigInteger('supervisor_1_id')->nullable();
            $t->unsignedBigInteger('supervisor_2_id')->nullable();
            $t->string('group_mode')->default('GROUP');
            $t->boolean('has_existing_group')->default(false);
            $t->timestamp('finalized_at')->nullable();
            $t->unsignedBigInteger('finalized_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->boolean('is_leader')->default(false);
            $t->unsignedBigInteger('period_id')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_titles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('lecturer_id')->nullable();
            $t->string('title');
            $t->text('description')->nullable();
            $t->integer('quota')->default(1);
            $t->string('title_source')->nullable();
            $t->string('supervisor_approval_status')->nullable();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_bids', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('title_id')->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_supervisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('supervisor_id');
            $t->string('role')->nullable();
            $t->unsignedBigInteger('assigned_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('action');
            $t->string('target_type')->nullable();
            $t->unsignedBigInteger('target_id')->nullable();
            $t->text('payload')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('finalization_writes_test');
        parent::tearDown();
    }

    private function admin(): User
    {
        $id = DB::table('users')->insertGetId(['name' => 'Admin']);

        return User::find($id);
    }

    private function controller(): FinalizationController
    {
        return app(FinalizationController::class);
    }

    /**
     * Build a FormRequest exactly like the framework does on a real call:
     * container + user + explicit validateResolved() pass.
     */
    private function callAs(User $user, string $requestClass, string $method, array $params = [])
    {
        /** @var Request $request */
        $request = $requestClass::create('/x', 'POST', $params);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $this->controller()->$method($request);
    }

    /** @return array{period:int, group:int, students:array<int>} */
    private function seedReadyGroup(string $status = 'READY_FOR_FINALIZATION'): array
    {
        DB::table('lecturers')->insert([['id' => 11, 'name' => 'Dosen 1'], ['id' => 12, 'name' => 'Dosen 2']]);
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true, 'is_finalized' => false]);
        $titleId = DB::table('capstone_titles')->insertGetId(['title' => 'Judul A', 'quota' => 2, 'title_source' => 'LECTURER']);
        $groupId = DB::table('capstone_groups')->insertGetId([
            'period_id' => $periodId, 'title_id' => $titleId, 'code' => 'G1',
            'status' => $status, 'supervisor_1_id' => 11, 'supervisor_2_id' => 12,
        ]);
        $studentIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $userId = DB::table('users')->insertGetId(['name' => "M{$i}"]);
            $studentIds[] = DB::table('students')->insertGetId(['user_id' => $userId, 'name' => "M{$i}"]);
        }
        foreach ($studentIds as $index => $studentId) {
            DB::table('capstone_group_members')->insert(['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => $index === 0, 'period_id' => $periodId]);
        }

        return ['period' => $periodId, 'group' => $groupId, 'students' => $studentIds];
    }

    public function test_execute_finalizes_ready_groups_and_period(): void
    {
        $seed = $this->seedReadyGroup();

        $response = $this->callAs($this->admin(), ExecuteFinalizationRequest::class, 'execute', [
            'period_id' => $seed['period'], 'confirmation' => 'yes',
        ]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame(1, $response->getData(true)['data']['finalized_count']);
        $this->assertSame('KELOMPOK_FINAL', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertTrue((bool) DB::table('capstone_periods')->where('id', $seed['period'])->value('is_finalized'));
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'FINALIZATION_EXECUTE', 'target_id' => $seed['group']]);
    }

    public function test_execute_rejects_missing_confirmation(): void
    {
        $seed = $this->seedReadyGroup();

        $this->expectException(ValidationException::class);
        $this->callAs($this->admin(), ExecuteFinalizationRequest::class, 'execute', ['period_id' => $seed['period']]);
    }

    public function test_execute_reports_no_ready_groups(): void
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true, 'is_finalized' => false]);

        $response = $this->callAs($this->admin(), ExecuteFinalizationRequest::class, 'execute', [
            'period_id' => $periodId, 'confirmation' => 'yes',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('No groups ready for finalization.', $response->getData(true)['message']);
    }

    public function test_execute_reports_ineligible_groups_as_bad_request(): void
    {
        $seed = $this->seedReadyGroup();
        DB::table('capstone_group_members')->where('group_id', $seed['group'])->limit(2)->delete();

        $response = $this->callAs($this->admin(), ExecuteFinalizationRequest::class, 'execute', [
            'period_id' => $seed['period'], 'confirmation' => 'yes',
        ]);

        // Was an unhandled 500 before extraction; normalized to the same 400
        // envelope every sibling guard uses, with the message preserved.
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringStartsWith('No eligible groups could be finalized.', $response->getData(true)['message']);
        $this->assertFalse((bool) DB::table('capstone_periods')->where('id', $seed['period'])->value('is_finalized'));
    }

    public function test_rollback_reverts_final_and_unfinalizes_period(): void
    {
        $seed = $this->seedReadyGroup('KELOMPOK_FINAL');
        DB::table('capstone_periods')->where('id', $seed['period'])->update(['is_finalized' => true]);

        $response = $this->callAs($this->admin(), RollbackFinalizationRequest::class, 'rollback', [
            'period_id' => $seed['period'], 'reason' => 'salah data',
        ]);

        $this->assertSame(1, $response->getData(true)['data']['rolled_back_count']);
        $this->assertSame('READY_FOR_FINALIZATION', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertFalse((bool) DB::table('capstone_periods')->where('id', $seed['period'])->value('is_finalized'));
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'FINALIZATION_ROLLBACK', 'target_id' => $seed['group']]);
    }

    public function test_cancel_kelompok_final_returns_group_payload(): void
    {
        $seed = $this->seedReadyGroup('KELOMPOK_FINAL');

        $response = $this->callAs($this->admin(), CancelKelompokFinalRequest::class, 'cancelKelompokFinal', [
            'period_id' => $seed['period'], 'group_id' => $seed['group'],
        ]);

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertSame('KELOMPOK_FINAL', $data['data']['old_status']);
        $this->assertSame('READY_FOR_FINALIZATION', $data['data']['new_status']);
        $this->assertSame('READY_FOR_FINALIZATION', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
    }

    public function test_reopen_reverts_pdc1_and_reports_deeper_states(): void
    {
        $seed = $this->seedReadyGroup('PDC1_ACTIVE');
        $deepId = DB::table('capstone_groups')->insertGetId(['period_id' => $seed['period'], 'code' => 'G2', 'status' => 'SEMPRO_DONE']);
        DB::table('capstone_periods')->where('id', $seed['period'])->update(['is_finalized' => true]);

        $response = $this->callAs($this->admin(), ReopenPeriodRequest::class, 'reopen', ['period_id' => $seed['period']]);

        $data = $response->getData(true)['data'];
        $this->assertContains($seed['group'], $data['reverted_group_ids']);
        $this->assertSame('KELOMPOK_FINAL', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertSame([['group_id' => $deepId, 'code' => 'G2', 'status' => 'SEMPRO_DONE']], $data['left_post_final']);
        $this->assertFalse((bool) DB::table('capstone_periods')->where('id', $seed['period'])->value('is_finalized'));
    }

    public function test_force_ready_promotes_approved_title_group(): void
    {
        $seed = $this->seedReadyGroup('TITLE_APPROVED');

        $response = $this->callAs($this->admin(), ForceReadyRequest::class, 'forceReady', ['group_id' => $seed['group']]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame('READY_FOR_FINALIZATION', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'FINALIZATION_FORCE_READY', 'target_id' => $seed['group']]);
    }

    public function test_assign_title_moves_forming_group_to_title_approved(): void
    {
        $seed = $this->seedReadyGroup('FORMING');
        DB::table('capstone_groups')->where('id', $seed['group'])->update(['title_id' => null]);
        $titleId = DB::table('capstone_titles')->insertGetId(['title' => 'Judul B', 'quota' => 1, 'title_source' => 'LECTURER']);

        $response = $this->callAs($this->admin(), AssignTitleRequest::class, 'assignTitle', [
            'group_id' => $seed['group'], 'title_id' => $titleId,
        ]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame($titleId, (int) DB::table('capstone_groups')->where('id', $seed['group'])->value('title_id'));
        $this->assertSame('TITLE_APPROVED', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
    }

    public function test_promote_to_ready_requires_title_first(): void
    {
        $seed = $this->seedReadyGroup('TITLE_APPROVED');
        DB::table('capstone_groups')->where('id', $seed['group'])->update(['title_id' => null]);

        $response = $this->callAs($this->admin(), PromoteToReadyRequest::class, 'promoteToReady', ['group_id' => $seed['group']]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('TITLE_APPROVED', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
    }

    public function test_set_supervisor_with_mark_final_transitions_group(): void
    {
        $seed = $this->seedReadyGroup();

        $response = $this->callAs($this->admin(), SetSupervisorRequest::class, 'setSupervisor', [
            'group_id' => $seed['group'], 'supervisor_1_id' => 11, 'supervisor_2_id' => 12, 'mark_final' => true,
        ]);

        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertSame('Kelompok berhasil ditandai sebagai Kelompok Final.', $data['message']);
        $this->assertSame('KELOMPOK_FINAL', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'FINALIZATION_MARK_KELOMPOK_FINAL', 'target_id' => $seed['group']]);
    }

    public function test_batch_set_supervisor_reports_per_group_results(): void
    {
        $seed = $this->seedReadyGroup();
        $otherId = DB::table('capstone_groups')->insertGetId(['period_id' => $seed['period'], 'code' => 'G9', 'status' => 'FORMING']);

        $response = $this->callAs($this->admin(), BatchSetSupervisorRequest::class, 'batchSetSupervisor', [
            'group_ids' => [$seed['group'], $otherId], 'supervisor_1_id' => 11, 'supervisor_2_id' => 12,
        ]);

        $data = $response->getData(true)['data'];
        $this->assertSame(1, $data['success_count']);
        $this->assertSame(1, $data['failed_count']);
        $this->assertSame($seed['group'], $data['results']['success'][0]['group_id']);
        $this->assertSame($otherId, $data['results']['failed'][0]['group_id']);
    }

    public function test_finalize_period_flag_with_pdc1_activation(): void
    {
        $seed = $this->seedReadyGroup('KELOMPOK_FINAL');

        $response = $this->callAs($this->admin(), FinalizePeriodFlagRequest::class, 'finalizePeriodFlag', [
            'period_id' => $seed['period'], 'confirmation' => 'yes', 'activate_pdc1' => true,
        ]);

        $data = $response->getData(true)['data'];
        $this->assertTrue((bool) DB::table('capstone_periods')->where('id', $seed['period'])->value('is_finalized'));
        $this->assertSame('PDC1_ACTIVE', DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertSame([$seed['group']], $data['activated_pdc1']);
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'PERIOD_MANUAL_FINALIZED']);
    }

    public function test_create_manual_group_without_title(): void
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true, 'is_finalized' => false]);
        $userId = DB::table('users')->insertGetId(['name' => 'Solo']);
        $studentId = DB::table('students')->insertGetId(['user_id' => $userId, 'name' => 'Solo']);

        $response = $this->callAs($this->admin(), CreateManualGroupRequest::class, 'createManualGroup', [
            'period_id' => $periodId, 'student_ids' => [$studentId], 'option' => 'no_title',
        ]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame('FORMING_SOLO', DB::table('capstone_groups')->where('period_id', $periodId)->value('status'));
        $this->assertTrue(DB::table('capstone_group_members')->where('student_id', $studentId)->where('is_leader', true)->exists());
    }

    public function test_add_to_existing_group_rejects_over_max_size(): void
    {
        $seed = $this->seedReadyGroup('FORMING');
        $extraIds = [];
        for ($i = 4; $i <= 6; $i++) {
            $userId = DB::table('users')->insertGetId(['name' => "X{$i}"]);
            $extraIds[] = DB::table('students')->insertGetId(['user_id' => $userId, 'name' => "X{$i}"]);
        }

        $response = $this->callAs($this->admin(), AddToExistingGroupRequest::class, 'addToExistingGroup', [
            'group_id' => $seed['group'], 'student_ids' => $extraIds,
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(3, DB::table('capstone_group_members')->where('group_id', $seed['group'])->count());
    }
}
