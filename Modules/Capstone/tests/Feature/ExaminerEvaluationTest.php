<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\SchedulingService;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class ExaminerEvaluationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.examiner_eval_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('examiner_eval_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->timestamps();
            $t->softDeletes();
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
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('code')->nullable();
            $t->string('status')->default('KELOMPOK_FINAL');
            $t->unsignedBigInteger('supervisor_1_id')->nullable();
            $t->unsignedBigInteger('supervisor_2_id')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->boolean('is_leader')->default(false);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_assessment_components', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('type');
            $t->string('code')->nullable();
            $t->string('name')->nullable();
            $t->text('description')->nullable();
            $t->decimal('weight', 5, 2)->default(0);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('capstone_assessment_component_templates', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->decimal('weight', 5, 2);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('capstone_period_assessment_components', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('template_id');
            $t->string('type');
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('capstone_assessment_scores', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('component_id')->nullable();
            $t->unsignedBigInteger('evaluator_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->text('notes')->nullable();
            $t->string('evaluation_type')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('type');
            $t->date('date')->nullable();
            $t->time('start_time')->nullable();
            $t->time('end_time')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_seminar_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->json('rubric_json')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->string('result', 4)->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('action');
            $t->string('target_type');
            $t->unsignedBigInteger('target_id');
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('examiner_eval_test');
        parent::tearDown();
    }

    private function seedSemproFixture(): array
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'TA 2026']);
        $userId = DB::table('users')->insertGetId(['name' => 'Examiner']);
        $lecturer1 = DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => 'Examiner 1']);
        $lecturer2 = DB::table('lecturers')->insertGetId(['user_id' => null, 'name' => 'Examiner 2']);

        $group = Group::create([
            'period_id' => $periodId,
            'code' => 'GRP-EXAM',
            'status' => 'READY_FOR_SEMPRO',
        ]);

        $scheduleId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'examiner_1_id' => $lecturer1,
            'examiner_2_id' => $lecturer2,
            'status' => 'SCHEDULED',
        ]);

        $eval1 = DB::table('capstone_seminar_evaluations')->insertGetId([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer1, 'status' => 'PENDING',
        ]);
        $eval2 = DB::table('capstone_seminar_evaluations')->insertGetId([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer2, 'status' => 'PENDING',
        ]);

        $templateId = DB::table('capstone_assessment_component_templates')->insertGetId([
            'code' => 'CPMK-1', 'name' => 'Template One', 'description' => 'Desc', 'weight' => 100,
        ]);
        $periodComponentId = DB::table('capstone_period_assessment_components')->insertGetId([
            'period_id' => $periodId, 'template_id' => $templateId, 'type' => 'SEMPRO', 'sort_order' => 1,
        ]);

        return compact('group', 'scheduleId', 'eval1', 'eval2', 'lecturer1', 'periodComponentId');
    }

    private function service(): SchedulingService
    {
        return new SchedulingService(new GroupStateMachine);
    }

    public function test_submit_persists_result_and_allows_pre_completion_update(): void
    {
        $f = $this->seedSemproFixture();

        $out = $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => ['1_7' => 80], 'notes' => []], 80.0, 'PASS', 1);

        $this->assertFalse($out['updated']);
        $this->assertFalse($out['all_submitted']);
        $this->assertSame('PASS', SeminarEvaluation::find($f['eval1'])->result);
        $this->assertSame('SCHEDULED', DB::table('capstone_seminar_schedules')->where('id', $f['scheduleId'])->value('status'));

        $out = $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => ['1_7' => 60], 'notes' => []], 60.0, 'FAIL', 1);

        $this->assertTrue($out['updated']);
        $this->assertSame(60.0, (float) SeminarEvaluation::find($f['eval1'])->score);
        $this->assertSame('FAIL', SeminarEvaluation::find($f['eval1'])->result);
    }

    public function test_completion_transitions_group_and_locks_result(): void
    {
        $f = $this->seedSemproFixture();

        $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => []], 80.0, 'PASS', 1);
        $out = $this->service()->submitSeminarEvaluation($f['eval2'], ['scores' => []], 70.0, 'FAIL', 1);

        $this->assertTrue($out['all_submitted']);
        $this->assertSame('COMPLETED', DB::table('capstone_seminar_schedules')->where('id', $f['scheduleId'])->value('status'));
        $this->assertSame('PDC1_ACTIVE', Group::find($f['group']->id)->status);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Result is locked');
        $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => []], 80.0, 'FAIL', 1);
    }

    public function test_score_edit_after_completion_is_allowed_and_idempotent(): void
    {
        $f = $this->seedSemproFixture();

        $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => []], 80.0, 'FAIL', 1);
        $this->service()->submitSeminarEvaluation($f['eval2'], ['scores' => []], 70.0, 'FAIL', 1);
        $auditCount = DB::table('capstone_audit_logs')->count();

        $out = $this->service()->submitSeminarEvaluation($f['eval1'], ['scores' => ['1_7' => 85]], 85.0, 'FAIL', 1);

        $this->assertTrue($out['updated']);
        $this->assertSame(85.0, (float) SeminarEvaluation::find($f['eval1'])->score);
        $this->assertSame('COMPLETED', DB::table('capstone_seminar_schedules')->where('id', $f['scheduleId'])->value('status'));
        $this->assertSame($auditCount, DB::table('capstone_audit_logs')->count());
    }

    public function test_context_resolves_period_components_and_hydrates_rubric_scores(): void
    {
        $f = $this->seedSemproFixture();

        DB::table('capstone_seminar_evaluations')->where('id', $f['eval1'])->update([
            'status' => 'SUBMITTED',
            'result' => 'PASS',
            'rubric_json' => json_encode(['scores' => [$f['periodComponentId'].'_7' => 88], 'notes' => [$f['periodComponentId'].'_7' => 'Good']]),
        ]);

        $controller = new SeminarDashboardController;
        $components = $this->invoke($controller, 'resolveExaminerComponents', [Group::find($f['group']->id)->period_id, 'SEMPRO']);

        $this->assertCount(1, $components);
        $this->assertSame('CPMK-1', $components[0]['code']);
        $this->assertSame('Template One', $components[0]['name']);
        $this->assertSame(100.0, $components[0]['weight']);

        $scores = $this->invoke($controller, 'resolveExaminerScores', [SeminarEvaluation::find($f['eval1']), $f['lecturer1'], $f['group']->id, 'SEMPRO']);

        $this->assertSame(88, $scores[$f['periodComponentId'].'_7']['score']);
        $this->assertSame('Good', $scores[$f['periodComponentId'].'_7']['notes']);
    }

    public function test_context_falls_back_to_legacy_components(): void
    {
        $f = $this->seedSemproFixture();

        DB::table('capstone_assessment_components')->insert([
            'period_id' => Group::find($f['group']->id)->period_id,
            'type' => 'SEMPRO',
            'code' => 'LEG-1',
            'name' => 'Legacy One',
            'weight' => 50,
            'sort_order' => 1,
        ]);
        Schema::dropIfExists('capstone_period_assessment_components');

        $controller = new SeminarDashboardController;
        $components = $this->invoke($controller, 'resolveExaminerComponents', [Group::find($f['group']->id)->period_id, 'SEMPRO']);

        $this->assertCount(1, $components);
        $this->assertSame('LEG-1', $components[0]['code']);
    }

    private function invoke(object $target, string $method, array $args): mixed
    {
        $ref = new \ReflectionMethod($target, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($target, $args);
    }
}
