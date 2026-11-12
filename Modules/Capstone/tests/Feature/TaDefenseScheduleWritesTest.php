<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\TaDefenseScheduleController;
use Modules\Capstone\Http\Requests\Admin\StoreTaDefenseRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Tests\TestCase;

class FakeAvailableEoffice extends EofficeAvailabilityService
{
    public function checkByRoom(
        ?string $room = null,
        ?int $locationId = null,
        ?string $date = null,
        ?string $startTime = null,
        ?string $endTime = null,
        ?int $excludePeminjamanId = null,
        ?int $eofficeId = null
    ): ?array {
        return null;
    }
}

class FakeConflictEoffice extends EofficeAvailabilityService
{
    public function checkByRoom(
        ?string $room = null,
        ?int $locationId = null,
        ?string $date = null,
        ?string $startTime = null,
        ?string $endTime = null,
        ?int $excludePeminjamanId = null,
        ?int $eofficeId = null
    ): ?array {
        return ['message' => 'Room already booked'];
    }
}

/**
 * Characterization: TA defense schedule writes, pinned before extraction
 * to SchedulingService. EOffice boundary is faked at the availability
 * service seam (service-locator call inside SchedulingService); one room
 * row is seeded for the Ruangan name lookup. No changes to Modules/EOffice.
 * Isolated real SQL: never migrate the application's configured database.
 */
class TaDefenseScheduleWritesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.ta_defense_writes_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('ta_defense_writes_test');

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
        Schema::create('roles', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('guard_name')->default('web');
            $t->timestamps();
        });
        Schema::create('model_has_roles', function (Blueprint $t) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger('model_id');
            $t->index(['model_id', 'model_type']);
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_finalized')->default(false);
            $t->integer('min_group_size')->nullable();
            $t->integer('max_group_size')->nullable();
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
            $t->boolean('is_solo')->default(false);
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
        Schema::create('capstone_supervisions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('supervisor_id');
            $t->string('role')->nullable();
            $t->unsignedBigInteger('assigned_by')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_ta_submissions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('status')->default('TA_DRAFT');
            $t->timestamps();
        });
        Schema::create('capstone_ta_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id')->nullable();
            $t->string('type')->default('SEMPRO');
            $t->date('date')->nullable();
            $t->string('start_time')->nullable();
            $t->string('end_time')->nullable();
            $t->string('room')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->date('date')->nullable();
            $t->string('start_time')->nullable();
            $t->string('end_time')->nullable();
            $t->string('room')->nullable();
            $t->unsignedBigInteger('location_id')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->text('notes')->nullable();
            $t->dateTime('evaluation_deadline')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_schedule_student', function (Blueprint $t) {
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('student_id');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->text('rubric_json')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->string('result')->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_ta_defense_examiners', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->string('role')->nullable();
            $t->timestamps();
        });
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('lokasi')->nullable();
            $t->integer('kapasitas')->default(0);
            $t->boolean('is_active')->default(true);
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
        Schema::create('capstone_notifications', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('type');
            $t->string('title');
            $t->text('message')->nullable();
            $t->string('related_type')->nullable();
            $t->unsignedBigInteger('related_id')->nullable();
            $t->boolean('is_read')->default(false);
            $t->timestamps();
        });

        app()->instance(EofficeAvailabilityService::class, new FakeAvailableEoffice);
    }

    protected function tearDown(): void
    {
        DB::purge('ta_defense_writes_test');
        parent::tearDown();
    }

    private function makeUser(string $name = 'User', ?string $email = null): User
    {
        return User::find(DB::table('users')->insertGetId(['name' => $name, 'email' => $email]));
    }

    private function withRole(User $user, string $role): User
    {
        $roleId = DB::table('roles')->insertGetId(['name' => $role, 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $user->id]);

        return $user;
    }

    private function makeAdmin(): User
    {
        return $this->withRole($this->makeUser('Admin', 'admin@test.local'), 'admin_capstone');
    }

    private function makeStudent(?int $userId = null, string $name = 'Student'): int
    {
        return DB::table('students')->insertGetId(['user_id' => $userId, 'name' => $name]);
    }

    private function makeLecturer(?int $userId = null, string $name = 'Dosen'): int
    {
        return DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => $name]);
    }

    private function makeRoom(string $nama = 'Ruang Sidang 1'): int
    {
        return DB::table('eo_mr_ruangans')->insertGetId(['nama' => $nama]);
    }

    /** @return array{period:int, group:int, students:array<int>, examiners:array<int>, room:int} */
    private function seedReadyGroup(): array
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true, 'is_finalized' => false]);
        $groupId = DB::table('capstone_groups')->insertGetId(['period_id' => $periodId, 'status' => 'READY_FOR_BIDDING']);
        $studentIds = [];
        for ($i = 1; $i <= 2; $i++) {
            $userId = DB::table('users')->insertGetId(['name' => "M{$i}"]);
            $studentId = $this->makeStudent($userId, "M{$i}");
            $studentIds[] = $studentId;
            DB::table('capstone_group_members')->insert(['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => $i === 1, 'period_id' => $periodId]);
            DB::table('capstone_ta_submissions')->insert(['student_id' => $studentId, 'group_id' => $groupId, 'period_id' => $periodId, 'status' => 'TA_DOCUMENTS_APPROVED']);
            DB::table('capstone_ta_registrations')->insert(['student_id' => $studentId, 'group_id' => $groupId, 'period_id' => $periodId, 'status' => 'APPROVED']);
        }
        $examiners = [$this->makeLecturer(), $this->makeLecturer()];

        return ['period' => $periodId, 'group' => $groupId, 'students' => $studentIds, 'examiners' => $examiners, 'room' => $this->makeRoom()];
    }

    private function controller(): TaDefenseScheduleController
    {
        return app(TaDefenseScheduleController::class);
    }

    private function storeAs(User $user, array $params)
    {
        $this->actingAs($user);
        /** @var FormRequest $request */
        $request = StoreTaDefenseRequest::create('/x', 'POST', $params);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $this->controller()->store($request);
    }

    private function storeParams(array $seed, array $overrides = []): array
    {
        return array_merge([
            'group_id' => $seed['group'],
            'student_ids' => $seed['students'],
            'period_id' => $seed['period'],
            'examiner_1_id' => $seed['examiners'][0],
            'examiner_2_id' => $seed['examiners'][1],
            'date' => now()->addDays(7)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'eoffice_ruangan_id' => $seed['room'],
        ], $overrides);
    }

    public function test_store_rejects_non_admin(): void
    {
        $seed = $this->seedReadyGroup();
        $user = $this->withRole($this->makeUser('Dosen X'), 'dosen');

        $response = $this->storeAs($user, $this->storeParams($seed));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Unauthorized', $response->getData(true)['message']);
        $this->assertSame(0, DB::table('capstone_ta_defense_schedules')->count());
    }

    public function test_store_rejects_student_not_in_group(): void
    {
        $seed = $this->seedReadyGroup();
        $outsider = $this->makeStudent();

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed, [
            'student_ids' => [$seed['students'][0], $outsider],
        ]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('All selected students must be from the same group', $response->getData(true)['message']);
    }

    public function test_store_rejects_without_approved_submission(): void
    {
        $seed = $this->seedReadyGroup();
        DB::table('capstone_ta_submissions')->where('student_id', $seed['students'][1])->update(['status' => 'TA_DRAFT']);

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('All selected students must have TA_DOCUMENTS_APPROVED status', $response->getData(true)['message']);
    }

    public function test_store_rejects_without_approved_registration(): void
    {
        $seed = $this->seedReadyGroup();
        DB::table('capstone_ta_registrations')->where('student_id', $seed['students'][0])->update(['status' => 'PENDING']);

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('All selected students must have an approved sidang TA registration', $response->getData(true)['message']);
    }

    public function test_store_rejects_duplicate_defense(): void
    {
        $seed = $this->seedReadyGroup();
        DB::table('capstone_ta_defense_schedules')->insert([
            'group_id' => $seed['group'], 'student_id' => $seed['students'][0],
            'examiner_1_id' => $seed['examiners'][0], 'examiner_2_id' => $seed['examiners'][1],
            'date' => now()->addDays(8)->format('Y-m-d'), 'status' => 'SCHEDULED',
        ]);

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('One or more selected students already have a scheduled or completed defense', $response->getData(true)['message']);
    }

    public function test_store_rejects_supervisor_as_examiner(): void
    {
        $seed = $this->seedReadyGroup();
        DB::table('capstone_supervisions')->insert([
            'group_id' => $seed['group'], 'supervisor_id' => $seed['examiners'][0], 'role' => 'SUPERVISOR_1',
        ]);

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Examiner 1 cannot be a supervisor of this group', $response->getData(true)['message']);
    }

    public function test_store_rejects_conflicting_schedule(): void
    {
        $seed = $this->seedReadyGroup();
        app()->instance(EofficeAvailabilityService::class, new FakeConflictEoffice);

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Scheduling conflicts detected.', $response->getData(true)['message']);
        $this->assertSame(0, DB::table('capstone_ta_defense_schedules')->count());
    }

    public function test_store_creates_schedule_and_side_effects(): void
    {
        $seed = $this->seedReadyGroup();

        $response = $this->storeAs($this->makeAdmin(), $this->storeParams($seed));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Resource created successfully', $response->getData(true)['message']);
        $this->assertSame('TA defense scheduled successfully', $response->getData(true)['data']['message']);
        $scheduleId = $response->getData(true)['data']['data']['id'];
        $this->assertDatabaseHas('capstone_ta_defense_schedules', ['id' => $scheduleId, 'group_id' => $seed['group'], 'status' => 'SCHEDULED']);
        foreach ($seed['students'] as $studentId) {
            $this->assertDatabaseHas('capstone_ta_defense_schedule_student', ['schedule_id' => $scheduleId, 'student_id' => $studentId]);
            $this->assertSame('TA_READY_FOR_SIDANG', DB::table('capstone_ta_submissions')->where('student_id', $studentId)->value('status'));
        }
        $this->assertSame(2, DB::table('capstone_ta_defense_evaluations')->where('schedule_id', $scheduleId)->where('status', 'PENDING')->count());
        $this->assertSame(2, DB::table('capstone_ta_defense_examiners')->where('schedule_id', $scheduleId)->count());
        $this->assertTrue(DB::table('capstone_notifications')->where('related_id', $scheduleId)->exists());
    }

    public function test_cancel_reverts_submissions_and_audits(): void
    {
        $seed = $this->seedReadyGroup();
        $admin = $this->makeAdmin();
        $scheduleId = $this->storeAs($admin, $this->storeParams($seed))->getData(true)['data']['data']['id'];

        $this->actingAs($admin);
        $response = $this->controller()->cancel($scheduleId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('CANCELLED', DB::table('capstone_ta_defense_schedules')->where('id', $scheduleId)->value('status'));
        foreach ($seed['students'] as $studentId) {
            $this->assertSame('TA_DOCUMENTS_APPROVED', DB::table('capstone_ta_submissions')->where('student_id', $studentId)->value('status'));
        }
        $this->assertSame(0, DB::table('capstone_ta_defense_evaluations')->where('schedule_id', $scheduleId)->where('status', 'PENDING')->count());
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'TA_DEFENSE_CANCELLED', 'target_id' => $scheduleId]);
    }

    public function test_cancel_rejects_done_schedule(): void
    {
        $seed = $this->seedReadyGroup();
        $admin = $this->makeAdmin();
        $scheduleId = $this->storeAs($admin, $this->storeParams($seed))->getData(true)['data']['data']['id'];
        DB::table('capstone_ta_defense_schedules')->where('id', $scheduleId)->update(['status' => 'DONE']);

        $this->actingAs($admin);
        $response = $this->controller()->cancel($scheduleId);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Cannot cancel completed schedule', $response->getData(true)['message']);
    }

    public function test_eligible_students_flags_readiness(): void
    {
        $seed = $this->seedReadyGroup();
        // Second student loses approval → not ready.
        DB::table('capstone_ta_registrations')->where('student_id', $seed['students'][1])->update(['status' => 'PENDING']);
        $admin = $this->makeAdmin();

        $this->actingAs($admin);
        $request = new Request(['period_id' => $seed['period']]);
        $request->setUserResolver(fn () => $admin);
        $response = $this->controller()->eligibleStudents($request);

        $data = collect($response->getData(true)['data']);
        $group = $data->firstWhere('id', $seed['group']);
        $this->assertNotNull($group);
        $flags = collect($group['members'])->mapWithKeys(fn ($m) => [$m['student']['id'] => $m['is_ready_for_sidang']])->all();
        $this->assertTrue($flags[$seed['students'][0]]);
        $this->assertFalse($flags[$seed['students'][1]]);
    }

    public function test_eligible_students_excludes_active_defense(): void
    {
        $seed = $this->seedReadyGroup();
        $admin = $this->makeAdmin();
        $this->storeAs($admin, $this->storeParams($seed));

        $this->actingAs($admin);
        $request = new Request(['period_id' => $seed['period']]);
        $request->setUserResolver(fn () => $admin);
        $response = $this->controller()->eligibleStudents($request);

        $group = collect($response->getData(true)['data'])->firstWhere('id', $seed['group']);
        foreach ($group['members'] as $member) {
            $this->assertFalse($member['is_ready_for_sidang']);
            $this->assertTrue($member['has_active_defense']);
        }
        $this->assertSame(Group::find($seed['group'])->status, DB::table('capstone_groups')->where('id', $seed['group'])->value('status'));
        $this->assertInstanceOf(TaDefenseSchedule::class, TaDefenseSchedule::find(
            DB::table('capstone_ta_defense_schedules')->where('group_id', $seed['group'])->value('id')
        ));
    }
}
