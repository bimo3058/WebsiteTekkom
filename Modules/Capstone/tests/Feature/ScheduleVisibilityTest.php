<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\CalendarController;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Tests\TestCase;

/**
 * Slot-split schedule visibility: SEMPRO belongs to supervisor 2, TA
 * defense to supervisor 1 (visibility only), EXPO is global read-only.
 * Isolated real SQL: never migrate the application's configured database.
 */
class ScheduleVisibilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.schedule_visibility_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('schedule_visibility_test');

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
            $t->unsignedBigInteger('supervisor_1_id')->nullable();
            $t->unsignedBigInteger('supervisor_2_id')->nullable();
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
            $t->string('role')->nullable();
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
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
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
        DB::purge('schedule_visibility_test');
        parent::tearDown();
    }

    public function test_sempro_visible_only_to_supervisor_2(): void
    {
        ['slot1' => $slot1, 'slot2' => $slot2, 'group' => $group] = $this->supervisedGroup();
        $sempro = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'status' => 'SCHEDULED',
        ]);

        $dash = new SeminarDashboardController;
        $slot1Ids = array_column($dash->supervisorSchedules($this->requestAs($slot1))->getData(true)['data']['seminars'], 'id');
        $slot2Ids = array_column($dash->supervisorSchedules($this->requestAs($slot2))->getData(true)['data']['seminars'], 'id');

        $this->assertNotContains($sempro->id, $slot1Ids);
        $this->assertContains($sempro->id, $slot2Ids);

        $calSlot1 = array_column((new CalendarController)->index($this->requestAs($slot1))->getData(true)['data'], 'id');
        $calSlot2 = array_column((new CalendarController)->index($this->requestAs($slot2))->getData(true)['data'], 'id');
        $this->assertNotContains($sempro->id, $calSlot1);
        $this->assertContains($sempro->id, $calSlot2);
    }

    public function test_ta_defense_visible_only_to_supervisor_1(): void
    {
        ['slot1' => $slot1, 'slot2' => $slot2, 'group' => $group, 'studentId' => $studentId] = $this->supervisedGroup();
        $defense = TaDefenseSchedule::create([
            'group_id' => $group->id, 'student_id' => $studentId, 'date' => '2026-10-01', 'status' => 'SCHEDULED',
        ]);

        $dash = new SeminarDashboardController;
        $slot1Ids = array_column($dash->supervisorSchedules($this->requestAs($slot1))->getData(true)['data']['ta_defenses'], 'id');
        $slot2Ids = array_column($dash->supervisorSchedules($this->requestAs($slot2))->getData(true)['data']['ta_defenses'], 'id');

        $this->assertContains($defense->id, $slot1Ids);
        $this->assertNotContains($defense->id, $slot2Ids);

        $calSlot1 = array_column((new CalendarController)->index($this->requestAs($slot1))->getData(true)['data'], 'id');
        $calSlot2 = array_column((new CalendarController)->index($this->requestAs($slot2))->getData(true)['data'], 'id');
        $this->assertContains('ta_'.$defense->id, $calSlot1);
        $this->assertNotContains('ta_'.$defense->id, $calSlot2);
    }

    public function test_expo_visible_to_both_supervisors_and_unrelated_dosen(): void
    {
        ['slot1' => $slot1, 'slot2' => $slot2, 'group' => $group] = $this->supervisedGroup();
        $expo = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'EXPO', 'date' => '2026-10-05', 'status' => 'SCHEDULED',
        ]);
        $stranger = $this->makeLecturer('Stranger');

        $dash = new SeminarDashboardController;
        foreach ([$slot1, $slot2] as $viewer) {
            $ids = array_column($dash->supervisorSchedules($this->requestAs($viewer))->getData(true)['data']['seminars'], 'id');
            $this->assertContains($expo->id, $ids, 'expo hidden from supervisor '.$viewer->name);
        }

        $calIds = array_column((new CalendarController)->index($this->requestAs($stranger))->getData(true)['data'], 'id');
        $this->assertContains($expo->id, $calIds, 'expo hidden from unrelated dosen calendar');
    }

    public function test_column_fallback_grants_slot_without_supervision_row(): void
    {
        $lecturer = $this->makeLecturer('ColumnFallback');
        $lecturerId = DB::table('lecturers')->where('user_id', $lecturer->id)->value('id');
        $group = Group::create(['period_id' => 1, 'code' => 'GRP-COL', 'status' => 'READY_FOR_SEMPRO', 'supervisor_2_id' => $lecturerId]);
        $sempro = SeminarSchedule::create([
            'group_id' => $group->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'status' => 'SCHEDULED',
        ]);

        $ids = array_column((new SeminarDashboardController)->supervisorSchedules($this->requestAs($lecturer))->getData(true)['data']['seminars'], 'id');
        $this->assertContains($sempro->id, $ids);
    }

    public function test_student_expo_feed_is_global_while_seminars_stay_own_group(): void
    {
        $period2 = DB::table('capstone_periods')->insertGetId(['name' => 'P2']);
        $studentUser = $this->makeUser('Student A');
        $this->giveRole($studentUser, 'mahasiswa');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Student A']);
        $groupA = Group::create(['period_id' => 1, 'code' => 'GRP-A', 'status' => 'EXPO_REGISTERED']);
        DB::table('capstone_group_members')->insert(['group_id' => $groupA->id, 'student_id' => $studentId]);

        $groupB = Group::create(['period_id' => $period2, 'code' => 'GRP-B', 'status' => 'EXPO_REGISTERED']);
        $foreignExpo = SeminarSchedule::create([
            'group_id' => $groupB->id, 'type' => 'EXPO', 'date' => '2026-11-01', 'status' => 'SCHEDULED',
        ]);
        $ownSempro = SeminarSchedule::create([
            'group_id' => $groupA->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'status' => 'SCHEDULED',
        ]);

        $data = (new SeminarDashboardController)->studentSchedules($this->requestAs($studentUser))->getData(true)['data'];

        $this->assertSame([$ownSempro->id], array_column($data['seminars'], 'id'));
        $this->assertContains($foreignExpo->id, array_column($data['expo_schedules'], 'id'));
    }

    public function test_admin_calendar_still_sees_everything_cross_period(): void
    {
        $period2 = DB::table('capstone_periods')->insertGetId(['name' => 'P2']);
        $admin = $this->makeUser('Admin');
        $this->giveRole($admin, 'superadmin');

        $groupA = Group::create(['period_id' => 1, 'code' => 'GRP-A', 'status' => 'READY_FOR_SEMPRO']);
        $groupB = Group::create(['period_id' => $period2, 'code' => 'GRP-B', 'status' => 'EXPO_REGISTERED']);
        $sempro = SeminarSchedule::create(['group_id' => $groupA->id, 'type' => 'SEMPRO', 'date' => '2026-10-01', 'status' => 'SCHEDULED']);
        $expo = SeminarSchedule::create(['group_id' => $groupB->id, 'type' => 'EXPO', 'date' => '2026-11-01', 'status' => 'SCHEDULED']);

        $ids = array_column((new CalendarController)->index($this->requestAs($admin))->getData(true)['data'], 'id');
        $this->assertContains($sempro->id, $ids);
        $this->assertContains($expo->id, $ids);
    }

    /** Group with two slot supervisors (SUPERVISOR_1 + SUPERVISOR_2 roles) and one member. */
    private function supervisedGroup(): array
    {
        $slot1 = $this->makeLecturer('Slot Satu');
        $slot2 = $this->makeLecturer('Slot Dua');
        $slot1Id = DB::table('lecturers')->where('user_id', $slot1->id)->value('id');
        $slot2Id = DB::table('lecturers')->where('user_id', $slot2->id)->value('id');

        $studentUser = $this->makeUser('Member');
        $this->giveRole($studentUser, 'mahasiswa');
        $studentId = DB::table('students')->insertGetId(['user_id' => $studentUser->id, 'name' => 'Member']);

        $group = Group::create(['period_id' => 1, 'code' => 'GRP-SLOT', 'status' => 'READY_FOR_SEMPRO']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);
        DB::table('capstone_supervisions')->insert([
            ['group_id' => $group->id, 'supervisor_id' => $slot1Id, 'role' => 'SUPERVISOR_1'],
            ['group_id' => $group->id, 'supervisor_id' => $slot2Id, 'role' => 'SUPERVISOR_2'],
        ]);

        return ['slot1' => $slot1, 'slot2' => $slot2, 'group' => $group, 'studentId' => $studentId];
    }

    private function makeLecturer(string $name): User
    {
        $user = $this->makeUser($name);
        $this->giveRole($user, 'dosen');
        DB::table('lecturers')->insert(['user_id' => $user->id, 'name' => $name]);

        return $user;
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

    private function requestAs(User $user): Request
    {
        $request = Request::create('/x', 'GET');
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
