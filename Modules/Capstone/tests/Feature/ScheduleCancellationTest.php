<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\CalendarController;
use Modules\Capstone\Http\Controllers\ExpoController;
use Modules\Capstone\Http\Controllers\LocationController;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Http\Controllers\SemproController;
use Modules\Capstone\Http\Controllers\TaDefenseController;
use Modules\Capstone\Http\Controllers\TaDefenseScheduleController;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Services\ExpoEligibilityService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\SchedulingService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class ScheduleCancellationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.schedule_cancel_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('schedule_cancel_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('lecturers', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->unsignedBigInteger('title_id')->nullable();
            $t->string('code')->nullable();
            $t->string('status')->default('KELOMPOK_FINAL');
            $t->timestamps();
        });
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_supervisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('supervisor_id');
            $t->timestamps();
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('type');
            $t->date('date')->nullable();
            $t->time('start_time')->nullable();
            $t->time('end_time')->nullable();
            $t->string('room')->nullable();
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
        Schema::create('capstone_ta_defense_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id')->nullable();
            $t->unsignedBigInteger('student_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->date('date')->nullable();
            $t->string('room')->nullable();
            $t->time('start_time')->nullable();
            $t->time('end_time')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->timestamp('evaluation_deadline')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_schedule_student', function (Blueprint $t) {
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('student_id');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_examiners', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id')->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_ta_submissions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('student_id');
            $t->string('status')->default('TA_DRAFT');
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
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->boolean('is_active')->default(false);
            $t->boolean('is_finalized')->default(false);
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_titles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('lecturer_id')->nullable();
            $t->string('title')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_locations', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('lokasi')->nullable();
            $t->integer('lantai')->nullable();
            $t->integer('kapasitas')->default(0);
            $t->json('fasilitas')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
        Schema::create('eo_mr_peminjamans', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('ruangan_id')->nullable();
            $t->string('status')->nullable();
            $t->date('tanggal_pinjam')->nullable();
            $t->timestamps();
        });
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->unsignedBigInteger('model_id');
            $t->string('model_type');
        });
    }

    protected function tearDown(): void
    {
        DB::purge('schedule_cancel_test');
        parent::tearDown();
    }

    public function test_sempro_cancel_notifies_students_and_examiners_and_audits(): void
    {
        $admin = $this->makeUser('Admin');
        $studentUser = $this->makeUser('Student');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student']);
        $examinerA = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam A')->id, 'name' => 'Exam A']);
        $examinerB = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam B')->id, 'name' => 'Exam B']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-CX', 'status' => 'READY_FOR_SEMPRO']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01',
            'examiner_1_id' => $examinerA, 'examiner_2_id' => $examinerB, 'status' => 'SCHEDULED',
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            ['schedule_id' => $schedule->id, 'examiner_id' => $examinerA, 'status' => 'PENDING'],
            ['schedule_id' => $schedule->id, 'examiner_id' => $examinerB, 'status' => 'PENDING'],
        ]);

        $this->actingAs($admin);
        $response = (new SemproController(new GroupStateMachine, $this->scheduling()))->cancel($this->requestAs($admin), $schedule->id);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('CANCELLED', $schedule->fresh()->status);

        $this->assertDatabaseHas('capstone_audit_logs', [
            'action' => 'SEMPRO_CANCELLED', 'target_type' => 'SeminarSchedule', 'target_id' => $schedule->id,
        ]);
        $this->assertDatabaseHas('capstone_notifications', [
            'user_id' => $studentUser->id, 'type' => 'SEMPRO_CANCELLED', 'related_type' => 'SeminarSchedule', 'related_id' => $schedule->id,
        ]);
        $this->assertSame(2, DB::table('capstone_notifications')->where('type', 'SEMPRO_CANCELLED')->where('related_id', $schedule->id)->whereNot('user_id', $studentUser->id)->count());
    }

    public function test_expo_cancel_reverts_group_and_notifies_both_roles(): void
    {
        $admin = $this->makeUser('Admin');
        $studentUser = $this->makeUser('Student');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student']);
        $examinerA = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam A')->id, 'name' => 'Exam A']);
        $examinerB = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam B')->id, 'name' => 'Exam B']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-EX', 'status' => 'EXPO_REGISTERED']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-10-01',
            'examiner_1_id' => $examinerA, 'examiner_2_id' => $examinerB, 'status' => 'SCHEDULED',
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            ['schedule_id' => $schedule->id, 'examiner_id' => $examinerA, 'status' => 'PENDING'],
            ['schedule_id' => $schedule->id, 'examiner_id' => $examinerB, 'status' => 'PENDING'],
        ]);

        $this->actingAs($admin);
        $response = (new ExpoController(new GroupStateMachine, $this->scheduling(), new ExpoEligibilityService))->cancel($this->requestAs($admin), $schedule->id);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('CANCELLED', $schedule->fresh()->status);
        $this->assertSame('PDC2_READY_FOR_EXPO', $group->fresh()->status);

        $this->assertDatabaseHas('capstone_audit_logs', [
            'action' => 'EXPO_CANCELLED', 'target_type' => 'SeminarSchedule', 'target_id' => $schedule->id,
        ]);
        $this->assertDatabaseHas('capstone_notifications', [
            'user_id' => $studentUser->id, 'type' => 'EXPO_CANCELLED', 'related_type' => 'SeminarSchedule',
        ]);
        $this->assertSame(2, DB::table('capstone_notifications')->where('type', 'EXPO_CANCELLED')->whereNot('user_id', $studentUser->id)->count());
    }

    public function test_expo_cancel_blocked_when_evaluations_submitted(): void
    {
        $admin = $this->makeUser('Admin');
        $examinerA = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam A')->id, 'name' => 'Exam A']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-EX', 'status' => 'EXPO_REGISTERED']);
        $schedule = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-10-01',
            'examiner_1_id' => $examinerA, 'status' => 'SCHEDULED',
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            'schedule_id' => $schedule->id, 'examiner_id' => $examinerA, 'status' => 'SUBMITTED',
        ]);

        $this->actingAs($admin);

        try {
            (new ExpoController(new GroupStateMachine, $this->scheduling(), new ExpoEligibilityService))->cancel($this->requestAs($admin), $schedule->id);
            $this->fail('Expected a 422 HttpException.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertSame('SCHEDULED', $schedule->fresh()->status);
        $this->assertSame(0, DB::table('capstone_audit_logs')->where('action', 'EXPO_CANCELLED')->count());
    }

    public function test_ta_cancel_audits_and_notifies_examiners_as_cancelled(): void
    {
        $admin = $this->makeUser('Admin');
        $this->giveRole($admin, 'superadmin');
        $studentUser = $this->makeUser('Student');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student']);
        $examinerA = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam A')->id, 'name' => 'Exam A']);
        $examinerB = DB::table('lecturers')->insertGetId(['user_id' => $this->makeUser('Exam B')->id, 'name' => 'Exam B']);

        $schedule = TaDefenseSchedule::create([
            'group_id' => 1, 'status' => 'SCHEDULED', 'date' => '2026-10-01',
            'examiner_1_id' => $examinerA, 'examiner_2_id' => $examinerB,
        ]);
        DB::table('capstone_ta_defense_schedule_student')->insert(['schedule_id' => $schedule->id, 'student_id' => $studentId]);
        DB::table('capstone_ta_submissions')->insert(['student_id' => $studentId, 'status' => 'TA_READY_FOR_SIDANG']);

        $this->actingAs($admin);
        $response = (new TaDefenseScheduleController($this->scheduling()))->cancel($schedule->id);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('CANCELLED', $schedule->fresh()->status);
        $this->assertSame('TA_DOCUMENTS_APPROVED', DB::table('capstone_ta_submissions')->where('student_id', $studentId)->value('status'));

        $this->assertDatabaseHas('capstone_audit_logs', [
            'action' => 'TA_DEFENSE_CANCELLED', 'target_type' => 'TaDefenseSchedule', 'target_id' => $schedule->id,
        ]);
        $this->assertDatabaseHas('capstone_notifications', [
            'user_id' => $studentUser->id, 'type' => 'TA_DEFENSE_CANCELLED', 'related_type' => 'TaDefenseSchedule',
        ]);

        $examinerNotifs = DB::table('capstone_notifications')->where('type', 'TA_DEFENSE_CANCELLED')->whereNot('user_id', $studentUser->id)->get();
        $this->assertCount(2, $examinerNotifs);
        foreach ($examinerNotifs as $notif) {
            $this->assertStringContainsString('Dibatalkan', $notif->title);
        }
        $this->assertSame(0, DB::table('capstone_notifications')->where('type', 'TA_DEFENSE_UPDATED')->count());
    }

    public function test_cancelled_schedules_hidden_from_student_and_dosen_cards(): void
    {
        $studentUser = $this->makeUser('Student');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student']);
        $lecturerUser = $this->makeUser('Lecturer');
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $lecturerUser->id, 'name' => 'Lecturer']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-CD', 'status' => 'READY_FOR_SEMPRO']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);
        DB::table('capstone_supervisions')->insert(['group_id' => $group->id, 'supervisor_id' => $lecturerId]);

        $cancelled = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-09-01',
            'examiner_1_id' => $lecturerId, 'status' => 'CANCELLED',
        ]);
        $active = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01',
            'examiner_1_id' => $lecturerId, 'status' => 'SCHEDULED',
        ]);
        DB::table('capstone_seminar_evaluations')->insert([
            ['schedule_id' => $cancelled->id, 'examiner_id' => $lecturerId, 'status' => 'PENDING'],
            ['schedule_id' => $active->id, 'examiner_id' => $lecturerId, 'status' => 'PENDING'],
        ]);

        $dash = new SeminarDashboardController;

        $studentPayload = $dash->studentSchedules($this->requestAs($studentUser))->getData(true)['data'];
        $this->assertSame([$active->id], array_column($studentPayload['seminars'], 'id'));

        $examinerPayload = $dash->examinerSchedules($this->requestAs($lecturerUser))->getData(true)['data'];
        $this->assertSame([$active->id], array_column($examinerPayload['seminars'], 'id'));

        $supervisorPayload = $dash->supervisorSchedules($this->requestAs($lecturerUser))->getData(true)['data'];
        $this->assertSame([$active->id], array_column($supervisorPayload['seminars'], 'id'));
    }

    public function test_calendar_feeds_hide_cancelled_for_all_roles(): void
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1']);

        $lecturerUser = $this->makeUser('Lecturer');
        $this->giveRole($lecturerUser, 'dosen');
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $lecturerUser->id, 'name' => 'Lecturer']);

        $studentUser = $this->makeUser('Student');
        $this->giveRole($studentUser, 'mahasiswa');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student']);

        $admin = $this->makeUser('Admin');
        $this->giveRole($admin, 'superadmin');

        $group = Group::create(['period_id' => $periodId, 'code' => 'GRP-CAL', 'status' => 'READY_FOR_SEMPRO']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);
        DB::table('capstone_supervisions')->insert(['group_id' => $group->id, 'supervisor_id' => $lecturerId]);

        SeminarSchedule::create(['group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-09-01', 'room' => 'A', 'examiner_1_id' => $lecturerId, 'status' => 'CANCELLED']);
        $activeSeminar = SeminarSchedule::create(['group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'room' => 'B', 'examiner_1_id' => $lecturerId, 'status' => 'SCHEDULED']);

        $cancelledTa = TaDefenseSchedule::create(['group_id' => $group->id, 'student_id' => $studentId, 'date' => '2026-09-01', 'room' => 'A', 'examiner_1_id' => $lecturerId, 'status' => 'CANCELLED']);
        $activeTa = TaDefenseSchedule::create(['group_id' => $group->id, 'student_id' => $studentId, 'date' => '2026-10-01', 'room' => 'B', 'examiner_1_id' => $lecturerId, 'status' => 'SCHEDULED']);
        DB::table('capstone_ta_defense_schedule_student')->insert([
            ['schedule_id' => $cancelledTa->id, 'student_id' => $studentId],
            ['schedule_id' => $activeTa->id, 'student_id' => $studentId],
        ]);

        foreach ([$lecturerUser, $studentUser, $admin] as $viewer) {
            $data = (new CalendarController)->index($this->requestAs($viewer))->getData(true)['data'];
            $statuses = array_column($data, 'status');

            $this->assertNotContains('CANCELLED', $statuses, 'cancelled leaked to '.$viewer->name);
            $this->assertContains('SCHEDULED', $statuses, 'active missing for '.$viewer->name);
        }

        $dosenData = (new CalendarController)->index($this->requestAs($lecturerUser))->getData(true)['data'];
        $ids = array_column($dosenData, 'id');
        $this->assertContains($activeSeminar->id, $ids);
        $this->assertContains('ta_'.$activeTa->id, $ids);
    }

    public function test_admin_lists_hide_cancelled_but_explicit_status_filter_still_works(): void
    {
        $admin = $this->makeUser('Admin');
        $this->giveRole($admin, 'superadmin');
        $this->actingAs($admin);

        Cache::forget('periods:active_and_finalized_ids');
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true]);

        $group = Group::create(['period_id' => $periodId, 'code' => 'GRP-ADM', 'status' => 'READY_FOR_SEMPRO']);

        SeminarSchedule::create(['group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-09-01', 'status' => 'CANCELLED']);
        SeminarSchedule::create(['group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'status' => 'SCHEDULED']);
        SeminarSchedule::create(['group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-09-01', 'status' => 'CANCELLED']);
        SeminarSchedule::create(['group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-10-01', 'status' => 'SCHEDULED']);
        $cancelledTa = TaDefenseSchedule::create(['group_id' => $group->id, 'date' => '2026-09-01', 'status' => 'CANCELLED']);
        TaDefenseSchedule::create(['group_id' => $group->id, 'date' => '2026-10-01', 'status' => 'SCHEDULED']);

        $this->assertSame(['SCHEDULED'], array_column((new SemproController(new GroupStateMachine, $this->scheduling()))->index()->getData(true)['data'], 'status'));
        $this->assertSame(['SCHEDULED'], array_column((new ExpoController(new GroupStateMachine, $this->scheduling(), new ExpoEligibilityService))->index()->getData(true)['data'], 'status'));
        $this->assertSame(['SCHEDULED'], array_column((new TaDefenseController(new GroupStateMachine, $this->scheduling()))->index()->getData(true)['data'], 'status'));

        $default = (new TaDefenseScheduleController($this->scheduling()))->index(Request::create('/x', 'GET'))->getData(true);
        $this->assertSame(['SCHEDULED'], array_column($default['data'], 'status'));

        $explicit = (new TaDefenseScheduleController($this->scheduling()))->index(Request::create('/x', 'GET', ['status' => 'CANCELLED']))->getData(true);
        $this->assertSame([$cancelledTa->id], array_column($explicit['data'], 'id'));
    }

    public function test_eoffice_rooms_allows_dosen_but_blocks_mahasiswa(): void
    {
        $dosen = $this->makeUser('Dosen');
        $this->giveRole($dosen, 'dosen');
        DB::table('lecturers')->insert(['user_id' => $dosen->id, 'name' => 'Dosen']);
        DB::table('eo_mr_ruangans')->insert(['nama' => 'Ruang A']);

        $student = $this->makeUser('Student');
        $this->giveRole($student, 'mahasiswa');

        $this->actingAs($dosen);
        $dosenResponse = (new LocationController)->eofficeRooms();
        $this->assertSame(200, $dosenResponse->getStatusCode());
        $this->assertSame('Ruang A', $dosenResponse->getData(true)['data'][0]['nama']);

        $this->actingAs($student);
        $this->assertSame(403, (new LocationController)->eofficeRooms()->getStatusCode());
    }

    private function makeUser(string $name): User
    {
        return User::create(['name' => $name, 'email' => str()->slug($name).'@example.test']);
    }

    private function giveRole(User $user, string $role): void
    {
        $roleId = DB::table('roles')->insertGetId(['name' => $role, 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert([
            'role_id' => $roleId, 'model_id' => $user->id, 'model_type' => User::class,
        ]);
    }

    private function scheduling(): SchedulingService
    {
        return new SchedulingService(new GroupStateMachine);
    }

    private function requestAs(User $user): Request
    {
        $request = Request::create('/x', 'GET');
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
