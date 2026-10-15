<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Http\Controllers\SupervisorEvaluationController;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\SchedulingService;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class ExaminerDeadlineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.examiner_deadline_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('examiner_deadline_test');

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
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('code')->nullable();
            $t->string('status')->default('KELOMPOK_FINAL');
            $t->timestamp('nilai_dosen_deadline')->nullable();
            $t->timestamp('milestone_deadline')->nullable();
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
            $t->timestamp('evaluation_deadline')->nullable();
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
        Schema::create('capstone_notifications', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->string('type');
            $t->string('title');
            $t->text('message');
            $t->string('related_type')->nullable();
            $t->unsignedBigInteger('related_id')->nullable();
            $t->boolean('is_read')->default(false);
            $t->timestamps();
        });
        Schema::create('capstone_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('action');
            $t->string('target_type')->nullable();
            $t->unsignedBigInteger('target_id')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('examiner_deadline_test');
        parent::tearDown();
    }

    public function test_seminar_schedule_creation_auto_sets_deadline(): void
    {
        $group = Group::create(['period_id' => 1, 'code' => 'GRP-DL', 'status' => 'READY_FOR_SEMPRO']);

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'date' => '2026-09-01',
            'status' => 'SCHEDULED',
        ]);

        $this->assertSame('2026-09-03 00:00:00', (string) $schedule->evaluation_deadline);
    }

    public function test_seminar_deadline_follows_date_change_but_respects_explicit_value(): void
    {
        $group = Group::create(['period_id' => 1, 'code' => 'GRP-DL', 'status' => 'READY_FOR_SEMPRO']);

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'date' => '2026-09-01',
            'status' => 'SCHEDULED',
        ]);

        // Admin approval edits the date → deadline follows (supervisor parity).
        $schedule->update(['date' => '2026-10-10']);

        $this->assertSame('2026-10-12 00:00:00', (string) $schedule->fresh()->evaluation_deadline);

        // An explicitly set deadline is never overridden by a date edit.
        $schedule->update(['date' => '2026-11-11', 'evaluation_deadline' => '2030-01-01 00:00:00']);

        $this->assertSame('2030-01-01 00:00:00', (string) $schedule->fresh()->evaluation_deadline);
    }

    public function test_migration_backfills_legacy_null_deadlines(): void
    {
        $legacyId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => 1, 'type' => 'SEMPRO', 'date' => '2026-05-01',
            'status' => 'COMPLETED', 'evaluation_deadline' => null,
        ]);
        $keptId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => 1, 'type' => 'EXPO', 'date' => '2026-06-01',
            'status' => 'SCHEDULED', 'evaluation_deadline' => '2030-12-31 00:00:00',
        ]);

        $migration = require base_path('Modules/Capstone/database/migrations/2026_09_24_020000_add_evaluation_deadline_to_seminar_schedules.php');
        $migration->up();

        $this->assertSame(
            '2026-05-03 00:00:00',
            date('Y-m-d H:i:s', strtotime(DB::table('capstone_seminar_schedules')->where('id', $legacyId)->value('evaluation_deadline')))
        );
        $this->assertSame(
            '2030-12-31 00:00:00',
            date('Y-m-d H:i:s', strtotime(DB::table('capstone_seminar_schedules')->where('id', $keptId)->value('evaluation_deadline')))
        );
    }

    public function test_late_seminar_submit_saves_scores_and_notifies(): void
    {
        $f = $this->seedLateFixture();

        $out = (new SchedulingService(new GroupStateMachine))->submitSeminarEvaluation(
            $f['eval1'], ['scores' => ['1_7' => 80], 'notes' => []], 80.0, 'PASS', $f['userId']
        );

        $this->assertFalse($out['updated']);
        $this->assertSame('SUBMITTED', SeminarEvaluation::find($f['eval1'])->status);

        $notification = DB::table('capstone_notifications')->first();

        $this->assertNotNull($notification);
        $this->assertSame('EVALUATION_DEADLINE_PASSED', $notification->type);
        $this->assertSame($f['userId'], (int) $notification->user_id);
        $this->assertSame('SeminarSchedule', $notification->related_type);
        $this->assertStringContainsString('after the deadline', $notification->message);
    }

    public function test_on_time_submit_sends_no_notification(): void
    {
        $f = $this->seedLateFixture(deadline: date('Y-m-d H:i:s', strtotime('+7 days')));

        (new SchedulingService(new GroupStateMachine))->submitSeminarEvaluation(
            $f['eval1'], ['scores' => ['1_7' => 80], 'notes' => []], 80.0, 'PASS', $f['userId']
        );

        $this->assertSame('SUBMITTED', SeminarEvaluation::find($f['eval1'])->status);
        $this->assertSame(0, DB::table('capstone_notifications')->count());
    }

    public function test_context_deadline_payload_flags_passed_deadline(): void
    {
        $controller = new SeminarDashboardController;

        $past = $this->invoke($controller, 'resolveScheduleDeadline', [
            new SeminarSchedule(['date' => '2020-01-10', 'evaluation_deadline' => '2020-01-12 00:00:00']),
        ]);

        $this->assertSame('2020-01-12 00:00:00', $past['evaluation_deadline']);
        $this->assertTrue($past['deadline_passed']);

        // Legacy row without a stored deadline falls back to date + 2 days.
        $fallback = $this->invoke($controller, 'resolveScheduleDeadline', [
            new SeminarSchedule(['date' => date('Y-m-d', strtotime('+5 days')), 'evaluation_deadline' => null]),
        ]);

        $this->assertSame(date('Y-m-d', strtotime('+7 days')).' 00:00:00', $fallback['evaluation_deadline']);
        $this->assertFalse($fallback['deadline_passed']);

        $dateless = $this->invoke($controller, 'resolveScheduleDeadline', [
            new SeminarSchedule(['date' => null, 'evaluation_deadline' => null]),
        ]);

        $this->assertNull($dateless['evaluation_deadline']);
        $this->assertFalse($dateless['deadline_passed']);
    }

    public function test_supervisor_soft_deadline_persists_and_seeds_from_expo(): void
    {
        $controller = new SupervisorEvaluationController;
        $group = Group::create(['period_id' => 1, 'code' => 'GRP-SOFT', 'status' => 'PDC2_ACTIVE']);

        DB::table('capstone_seminar_schedules')->insert([
            'group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-08-01', 'status' => 'COMPLETED',
        ]);

        $first = $this->invoke($controller, 'resolveSoftDeadline', [$group, 'NILAI_DOSEN']);
        $second = $this->invoke($controller, 'resolveSoftDeadline', [Group::find($group->id), 'NILAI_DOSEN']);

        $this->assertSame('2026-08-03 00:00:00', $first);
        $this->assertSame($first, $second);
        $this->assertSame($first, date('Y-m-d H:i:s', strtotime(Group::find($group->id)->nilai_dosen_deadline)));
    }

    public function test_supervisor_soft_deadline_falls_back_to_seven_days_without_expo(): void
    {
        $controller = new SupervisorEvaluationController;
        $group = Group::create(['period_id' => 1, 'code' => 'GRP-SOFT2', 'status' => 'PDC2_ACTIVE']);

        $deadline = $this->invoke($controller, 'resolveSoftDeadline', [$group, 'NILAI_DOSEN']);

        $this->assertSame(date('Y-m-d', strtotime('+7 days')), date('Y-m-d', strtotime($deadline)));
        $this->assertNotNull(Group::find($group->id)->nilai_dosen_deadline);
    }

    public function test_stale_schedule_resubmit_skips_transition_and_saves(): void
    {
        // Group moved on to PDC2_ACTIVE while its sempro schedule stayed
        // SCHEDULED (real S1T26-22 shape). Editing must save, complete the
        // schedule, and leave the group status untouched — never throw.
        $userId = DB::table('users')->insertGetId(['name' => 'Examiner']);
        $lecturer1 = DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => 'Examiner 1']);
        $lecturer2 = DB::table('lecturers')->insertGetId(['user_id' => null, 'name' => 'Examiner 2']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-STALE', 'status' => 'PDC2_ACTIVE']);

        $scheduleId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'date' => '2020-01-10',
            'examiner_1_id' => $lecturer1,
            'examiner_2_id' => $lecturer2,
            'status' => 'SCHEDULED',
            'evaluation_deadline' => '2020-01-12 00:00:00',
        ]);

        $eval1 = DB::table('capstone_seminar_evaluations')->insertGetId([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer1, 'status' => 'SUBMITTED',
            'rubric_json' => json_encode(['scores' => ['5_87' => 90], 'notes' => []]), 'score' => 90,
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer2, 'status' => 'SUBMITTED',
            'rubric_json' => json_encode(['scores' => ['5_87' => 86], 'notes' => []]), 'score' => 86,
        ]);

        $out = (new SchedulingService(new GroupStateMachine))->submitSeminarEvaluation(
            $eval1, ['scores' => ['5_87' => 91], 'notes' => []], 91.0, 'PASS', $userId
        );

        $this->assertTrue($out['updated']);
        $this->assertSame(91.0, (float) SeminarEvaluation::find($eval1)->score);
        $this->assertSame('COMPLETED', SeminarSchedule::find($scheduleId)->status);
        $this->assertSame('PDC2_ACTIVE', Group::find($group->id)->status);
    }

    public function test_transition_guard_skips_disallowed_but_allows_legal_move(): void
    {
        $service = new SchedulingService(new GroupStateMachine);

        // Disallowed: stale PDC2_ACTIVE → SEMPRO_DONE must not throw.
        $stale = Group::create(['period_id' => 1, 'code' => 'GRP-G1', 'status' => 'PDC2_ACTIVE']);
        $this->invoke($service, 'transitionGroupIfAllowed', [$stale, 'SEMPRO_DONE', 'SEMPRO_PASS', 1]);
        $this->assertSame('PDC2_ACTIVE', Group::find($stale->id)->status);

        // Allowed: the normal READY_FOR_SEMPRO → SEMPRO_DONE still fires.
        $live = Group::create(['period_id' => 1, 'code' => 'GRP-G2', 'status' => 'READY_FOR_SEMPRO']);
        $this->invoke($service, 'transitionGroupIfAllowed', [$live, 'SEMPRO_DONE', 'SEMPRO_PASS', 1]);
        $this->assertSame('SEMPRO_DONE', Group::find($live->id)->status);
    }

    public function test_current_member_submit_replaces_rubric_and_archives_previous(): void
    {
        // Membership changed since the exam: submitting scores keyed to the
        // current members overwrites the old rubric, so the previous record
        // must be archived in the audit log while the group stays put.
        $userId = DB::table('users')->insertGetId(['name' => 'Examiner']);
        $lecturer1 = DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => 'Examiner 1']);
        $lecturer2 = DB::table('lecturers')->insertGetId(['user_id' => null, 'name' => 'Examiner 2']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-REPLACE', 'status' => 'PDC2_ACTIVE']);

        $scheduleId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'date' => '2020-01-10',
            'examiner_1_id' => $lecturer1,
            'examiner_2_id' => $lecturer2,
            'status' => 'SCHEDULED',
            'evaluation_deadline' => '2020-01-12 00:00:00',
        ]);

        $oldRubric = ['scores' => ['5_87' => 90], 'notes' => []];
        $eval1 = DB::table('capstone_seminar_evaluations')->insertGetId([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer1, 'status' => 'SUBMITTED',
            'rubric_json' => json_encode($oldRubric), 'score' => 90,
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer2, 'status' => 'SUBMITTED',
            'rubric_json' => json_encode(['scores' => ['5_87' => 86], 'notes' => []]), 'score' => 86,
        ]);

        // Scores keyed to the current members (102-104), not the examined 87.
        $newRubric = ['scores' => ['5_102' => 88, '5_103' => 84, '5_104' => 90], 'notes' => []];

        (new SchedulingService(new GroupStateMachine))->submitSeminarEvaluation(
            $eval1, $newRubric, 87.33, 'PASS', $userId
        );

        $saved = SeminarEvaluation::find($eval1);

        $this->assertSame($newRubric, $saved->rubric_json);
        $this->assertSame('COMPLETED', SeminarSchedule::find($scheduleId)->status);
        $this->assertSame('PDC2_ACTIVE', Group::find($group->id)->status);

        $audit = DB::table('capstone_audit_logs')
            ->where('action', 'SEMPRO_PASS')
            ->where('target_id', $scheduleId)
            ->first();

        $this->assertNotNull($audit);
        $payload = json_decode($audit->payload, true);

        $this->assertSame($oldRubric, $payload['previous_record']['rubric_json']);
        $this->assertSame(90.0, (float) $payload['previous_record']['score']);
    }

    /**
     * @return array{userId: int, eval1: int}
     */
    private function seedLateFixture(?string $deadline = null): array
    {
        $userId = DB::table('users')->insertGetId(['name' => 'Examiner']);
        $lecturer1 = DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => 'Examiner 1']);
        $lecturer2 = DB::table('lecturers')->insertGetId(['user_id' => null, 'name' => 'Examiner 2']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-LATE', 'status' => 'READY_FOR_SEMPRO']);

        // Query-builder insert bypasses the model hook, simulating a legacy row.
        $scheduleId = DB::table('capstone_seminar_schedules')->insertGetId([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'date' => '2020-01-10',
            'examiner_1_id' => $lecturer1,
            'examiner_2_id' => $lecturer2,
            'status' => 'SCHEDULED',
            'evaluation_deadline' => $deadline ?? '2020-01-12 00:00:00',
        ]);

        $eval1 = DB::table('capstone_seminar_evaluations')->insertGetId([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer1, 'status' => 'PENDING',
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            'schedule_id' => $scheduleId, 'examiner_id' => $lecturer2, 'status' => 'PENDING',
        ]);

        return ['userId' => $userId, 'eval1' => $eval1];
    }

    private function invoke(object $target, string $method, array $args): mixed
    {
        $ref = new \ReflectionMethod($target, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($target, $args);
    }
}
