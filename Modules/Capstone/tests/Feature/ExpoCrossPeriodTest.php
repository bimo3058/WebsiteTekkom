<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\ExpoEventController;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Modules\Capstone\Services\ExpoService;
use Modules\Capstone\Services\GroupStateMachine;
use Tests\TestCase;

/**
 * Cross-period (global, period_id NULL) expo events: visible and
 * registrable from every period, while period-scoped events keep
 * their same-period guard.
 * Isolated real SQL: never migrate the application's configured database.
 */
class ExpoCrossPeriodTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.expo_cross_period_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('expo_cross_period_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
            $t->softDeletes();
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
        Schema::create('students', function (Blueprint $t) {
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
            $t->string('status')->default('PDC2_READY_FOR_EXPO');
            $t->timestamps();
        });
        Schema::create('capstone_group_members', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_documents', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('phase')->nullable();
            $t->string('status')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_expo_events', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->string('name');
            $t->date('date')->nullable();
            $t->time('start_time')->nullable();
            $t->time('end_time')->nullable();
            $t->string('room')->nullable();
            $t->unsignedBigInteger('eoffice_ruangan_id')->nullable();
            $t->unsignedBigInteger('eoffice_peminjaman_id')->nullable();
            $t->integer('capacity')->default(100);
            $t->boolean('is_published')->default(false);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_expo_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_event_id');
            $t->unsignedBigInteger('group_id');
            $t->timestamp('registered_at')->nullable();
            $t->string('status')->default('REGISTERED');
            $t->timestamps();
            $t->unique(['expo_event_id', 'group_id']);
        });
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('type');
            $t->date('date')->nullable();
            $t->time('start_time')->nullable();
            $t->time('end_time')->nullable();
            $t->string('room')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('action')->nullable();
            $t->string('target_type')->nullable();
            $t->unsignedBigInteger('target_id')->nullable();
            $t->text('payload')->nullable();
            $t->timestamps();
        });
        Schema::create('eo_mr_ruangans', function (Blueprint $t) {
            $t->id();
            $t->string('nama')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('expo_cross_period_test');
        parent::tearDown();
    }

    public function test_is_cross_period_flags_null_period(): void
    {
        $this->assertTrue((new ExpoEvent(['period_id' => null]))->isCrossPeriod());
        $this->assertFalse((new ExpoEvent(['period_id' => 3]))->isCrossPeriod());
    }

    public function test_global_event_visible_to_students_of_every_period(): void
    {
        [$period1, $period2] = $this->makePeriods();
        $student = $this->makeStudent('Student P1', $period1);

        $global = $this->makeEvent(null, 'Global Expo');
        $ownPeriod = $this->makeEvent($period1, 'P1 Expo');
        $foreignPeriod = $this->makeEvent($period2, 'P2 Expo');

        $ids = $this->eventIds((new ExpoEventController($this->expoService()))->studentEvents($this->requestAs($student)));

        $this->assertContains($global->id, $ids);
        $this->assertContains($ownPeriod->id, $ids);
        $this->assertNotContains($foreignPeriod->id, $ids);
    }

    public function test_admin_index_period_filter_keeps_global_events(): void
    {
        [$period1, $period2] = $this->makePeriods();

        $global = $this->makeEvent(null, 'Global Expo');
        $ownPeriod = $this->makeEvent($period1, 'P1 Expo');
        $foreignPeriod = $this->makeEvent($period2, 'P2 Expo');

        $controller = new ExpoEventController($this->expoService());

        $filtered = $this->eventIds($controller->index(Request::create('/x', 'GET', ['period_id' => $period1])));
        $this->assertContains($global->id, $filtered);
        $this->assertContains($ownPeriod->id, $filtered);
        $this->assertNotContains($foreignPeriod->id, $filtered);

        $all = $this->eventIds($controller->index(Request::create('/x', 'GET')));
        $this->assertContains($global->id, $all);
        $this->assertContains($ownPeriod->id, $all);
        $this->assertContains($foreignPeriod->id, $all);
    }

    public function test_cross_period_registration_allowed_for_global_event(): void
    {
        [, $period2] = $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $group = $this->makeReadyGroup($period2);
        $event = $this->makeEvent(null, 'Global Expo');

        $registration = $this->expoService()->registerGroupToEvent($event->id, $group->id, $admin->id);

        $this->assertSame('REGISTERED', $registration->status);
        $this->assertSame('EXPO_REGISTERED', $group->fresh()->status);
        $this->assertTrue(SeminarSchedule::where('group_id', $group->id)->where('type', 'EXPO')->where('status', 'APPROVED')->exists());
    }

    public function test_period_scoped_event_still_rejects_foreign_group(): void
    {
        [$period1, $period2] = $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $group = $this->makeReadyGroup($period2);
        $event = $this->makeEvent($period1, 'P1 Expo');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('same period');
        $this->expoService()->registerGroupToEvent($event->id, $group->id, $admin->id);
    }

    public function test_same_period_registration_still_works(): void
    {
        [$period1] = $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $group = $this->makeReadyGroup($period1);
        $event = $this->makeEvent($period1, 'P1 Expo');

        $registration = $this->expoService()->registerGroupToEvent($event->id, $group->id, $admin->id);

        $this->assertSame('REGISTERED', $registration->status);
        $this->assertTrue(ExpoRegistration::where('expo_event_id', $event->id)->where('group_id', $group->id)->exists());
    }

    /**
     * Stale clients send period_id 0 for "global" (Number('') === 0).
     * The controller must normalize it to NULL instead of failing
     * the `exists` rule ("period id yang dipilih tidak valid").
     */
    public function test_store_with_zero_period_id_creates_global_event(): void
    {
        $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $roomId = DB::table('eo_mr_ruangans')->insertGetId(['nama' => 'Hall A']);

        $response = $this->eventController()->store($this->writeRequest($admin, [
            'period_id' => 0,
            'name' => 'Global Expo',
            'date' => '2026-12-01',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'eoffice_ruangan_id' => $roomId,
            'capacity' => 50,
        ]));

        $this->assertSame(201, $response->getStatusCode());
        $event = ExpoEvent::findOrFail($response->getData(true)['id']);
        $this->assertNull($event->period_id);
        $this->assertTrue($event->isCrossPeriod());
    }

    public function test_store_with_empty_string_period_id_creates_global_event(): void
    {
        $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $roomId = DB::table('eo_mr_ruangans')->insertGetId(['nama' => 'Hall A']);

        $response = $this->eventController()->store($this->writeRequest($admin, [
            'period_id' => '',
            'name' => 'Global Expo',
            'date' => '2026-12-01',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'eoffice_ruangan_id' => $roomId,
            'capacity' => 50,
        ]));

        $this->assertSame(201, $response->getStatusCode());
        $this->assertNull(ExpoEvent::findOrFail($response->getData(true)['id'])->period_id);
    }

    public function test_update_with_zero_period_id_converts_to_global(): void
    {
        [$period1] = $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $event = $this->makeEvent($period1, 'P1 Expo');

        $response = $this->eventController()->update($this->writeRequest($admin, ['period_id' => 0], 'PUT'), $event);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNull($event->fresh()->period_id);
    }

    public function test_update_without_period_id_keeps_period(): void
    {
        [$period1] = $this->makePeriods();
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test']);
        $event = $this->makeEvent($period1, 'P1 Expo');

        $response = $this->eventController()->update($this->writeRequest($admin, ['name' => 'Renamed Expo'], 'PUT'), $event);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($period1, (int) $event->fresh()->period_id);
    }

    private function eventController(): ExpoEventController
    {
        app()->instance(EofficeAvailabilityService::class, new class extends EofficeAvailabilityService
        {
            public function checkByEofficeId(int $eofficeId, string $date, string $startTime, string $endTime, ?int $excludePeminjamanId = null): ?array
            {
                return null;
            }
        });

        return new ExpoEventController($this->expoService());
    }

    private function writeRequest(User $user, array $params, string $method = 'POST'): Request
    {
        $request = Request::create('/x', $method, $params);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    /** @return int[] [period1, period2] */
    private function makePeriods(): array
    {
        return [
            DB::table('capstone_periods')->insertGetId(['name' => 'P1']),
            DB::table('capstone_periods')->insertGetId(['name' => 'P2']),
        ];
    }

    private function expoService(): ExpoService
    {
        return new ExpoService(new GroupStateMachine);
    }

    private function makeEvent(?int $periodId, string $name): ExpoEvent
    {
        return ExpoEvent::create([
            'period_id' => $periodId,
            'name' => $name,
            'date' => '2026-11-01',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'room' => 'Hall A',
            'capacity' => 100,
            'is_published' => true,
        ]);
    }

    /** Group in PDC2_READY_FOR_EXPO with an approved TA draft document. */
    private function makeReadyGroup(int $periodId): Group
    {
        $group = Group::create(['period_id' => $periodId, 'code' => 'GRP-'.$periodId.'-'.uniqid(), 'status' => 'PDC2_READY_FOR_EXPO']);
        Document::create(['group_id' => $group->id, 'phase' => 'TA', 'status' => 'APPROVED']);

        return $group;
    }

    private function makeStudent(string $name, int $periodId): User
    {
        $user = User::create(['name' => $name, 'email' => str()->slug($name).'@example.test']);
        $roleId = DB::table('roles')->insertGetId(['name' => 'mahasiswa', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_id' => $user->id, 'model_type' => User::class]);
        $studentId = DB::table('students')->insertGetId(['user_id' => $user->id, 'name' => $name]);
        $group = Group::create(['period_id' => $periodId, 'code' => 'GRP-'.$studentId, 'status' => 'PDC2_READY_FOR_EXPO']);
        DB::table('capstone_group_members')->insert(['group_id' => $group->id, 'student_id' => $studentId]);

        return $user;
    }

    /** @return int[] */
    private function eventIds($response): array
    {
        return array_column($response->getData(true), 'id');
    }

    private function requestAs(User $user): Request
    {
        $request = Request::create('/x', 'GET');
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
