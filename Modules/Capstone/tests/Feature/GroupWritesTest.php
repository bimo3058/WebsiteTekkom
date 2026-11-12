<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Http\Controllers\GroupController;
use Modules\Capstone\Http\Requests\Group\AddMemberRequest;
use Modules\Capstone\Http\Requests\Group\AssignSupervisor2Request;
use Modules\Capstone\Http\Requests\Group\ChangeReadyRequest;
use Modules\Capstone\Http\Requests\Group\CreateStudentGroupRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Services\GroupService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Characterization: group write endpoints, pinned before extraction to
 * GroupService + Form Requests. Behavior asserts (status transitions,
 * guard messages, DB state) — not envelope mirrors.
 * Isolated real SQL: never migrate the application's configured database.
 */
class GroupWritesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.group_writes_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('group_writes_test');

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
        Schema::create('capstone_bids', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('title_id')->nullable();
            $t->string('status')->default('PENDING');
            $t->string('lecturer_recommendation')->nullable();
            $t->integer('priority')->default(1);
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
        Schema::create('capstone_titles', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('lecturer_id')->nullable();
            $t->string('title')->nullable();
            $t->integer('quota')->default(1);
            $t->string('title_source')->nullable();
            $t->string('supervisor_approval_status')->nullable();
            $t->unsignedBigInteger('period_id')->nullable();
            $t->unsignedBigInteger('proposed_by_group_id')->nullable();
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
        Schema::create('capstone_expo_self_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->timestamps();
        });
        Schema::create('capstone_group_invitations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('inviter_id')->nullable();
            $t->string('status')->default('PENDING');
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
        Schema::create('capstone_period_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->unsignedBigInteger('period_id');
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_group_supervisor_proposals', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('proposed_supervisor_1_id')->nullable();
            $t->unsignedBigInteger('proposed_supervisor_2_id')->nullable();
            $t->string('status')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('group_writes_test');
        parent::tearDown();
    }

    private function makeUser(string $name = 'User', ?string $email = null): User
    {
        $id = DB::table('users')->insertGetId(['name' => $name, 'email' => $email]);

        return User::find($id);
    }

    private function makeStudent(?int $userId = null): int
    {
        return DB::table('students')->insertGetId(['user_id' => $userId, 'name' => 'Student']);
    }

    private function makeDosenLecturer(): int
    {
        $user = $this->makeUser('Dosen', 'dosen@test.local');
        $roleId = DB::table('roles')->insertGetId(['name' => 'dosen', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $user->id]);

        return DB::table('lecturers')->insertGetId(['user_id' => $user->id, 'name' => 'Dosen']);
    }

    private function makePeriod(?int $min = 3, ?int $max = 4): int
    {
        return DB::table('capstone_periods')->insertGetId([
            'name' => 'P1', 'is_active' => true, 'is_finalized' => false,
            'min_group_size' => $min, 'max_group_size' => $max,
        ]);
    }

    private function makeGroup(int $periodId, string $status = 'FORMING', string $mode = 'GROUP', ?int $sup1 = null): int
    {
        return DB::table('capstone_groups')->insertGetId([
            'period_id' => $periodId, 'status' => $status, 'group_mode' => $mode,
            'supervisor_1_id' => $sup1,
        ]);
    }

    private function addMember(int $groupId, int $studentId, bool $leader, int $periodId): void
    {
        DB::table('capstone_group_members')->insert([
            'group_id' => $groupId, 'student_id' => $studentId,
            'is_leader' => $leader, 'period_id' => $periodId,
        ]);
    }

    private function service(): GroupService
    {
        return app(GroupService::class);
    }

    /**
     * Run a FormRequest through the framework validation pass.
     *
     * @throws ValidationException
     */
    private function validateAs(User $user, string $requestClass, array $params = []): void
    {
        $request = $requestClass::create('/x', 'POST', $params);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();
    }

    public function test_create_request_rules_match_inline_contract(): void
    {
        $request = new CreateStudentGroupRequest;

        $this->assertSame(
            ['period_id' => ['required', 'integer', 'exists:capstone_periods,id']],
            $request->rules()
        );
    }

    public function test_create_request_rejects_missing_period_id(): void
    {
        $this->expectException(ValidationException::class);
        $this->validateAs($this->makeUser(), CreateStudentGroupRequest::class, []);
    }

    public function test_create_request_accepts_valid_period_id(): void
    {
        $periodId = $this->makePeriod();

        // Must not throw.
        $this->validateAs($this->makeUser(), CreateStudentGroupRequest::class, ['period_id' => $periodId]);
        $this->assertTrue(true);
    }

    public function test_change_ready_request_rejects_missing_group_id(): void
    {
        $this->expectException(ValidationException::class);
        $this->validateAs($this->makeUser(), ChangeReadyRequest::class, []);
    }

    public function test_change_ready_request_accepts_valid_group_id(): void
    {
        $groupId = $this->makeGroup($this->makePeriod());

        $this->validateAs($this->makeUser(), ChangeReadyRequest::class, ['group_id' => $groupId]);
        $this->assertTrue(true);
    }

    public function test_add_member_request_rejects_invalid_email(): void
    {
        $this->expectException(ValidationException::class);
        $this->validateAs($this->makeUser(), AddMemberRequest::class, ['email' => 'not-an-email']);
    }

    public function test_assign_supervisor2_request_rejects_missing_id(): void
    {
        $this->expectException(ValidationException::class);
        $this->validateAs($this->makeUser(), AssignSupervisor2Request::class, []);
    }

    public function test_group_size_limits_use_period_config(): void
    {
        $group = Group::find($this->makeGroup($this->makePeriod(2, 5)));

        $this->assertSame([2, 5], $this->service()->groupSizeLimits($group));
    }

    public function test_group_size_limits_fall_back_to_defaults(): void
    {
        $group = Group::find($this->makeGroup($this->makePeriod(null, null)));

        $this->assertSame([3, 4], $this->service()->groupSizeLimits($group));
    }

    public function test_assert_size_throws_when_undersized(): void
    {
        $periodId = $this->makePeriod(3, 4);
        $groupId = $this->makeGroup($periodId);
        $this->addMember($groupId, $this->makeStudent(), true, $periodId);

        $this->expectException(DomainRuleException::class);
        $this->expectExceptionMessage('antara 3-4');
        $this->service()->assertGroupSizeForFinalization(Group::find($groupId));
    }

    public function test_destroy_if_empty_keeps_nonempty_group(): void
    {
        $periodId = $this->makePeriod();
        $groupId = $this->makeGroup($periodId);
        $this->addMember($groupId, $this->makeStudent(), true, $periodId);

        $this->assertFalse($this->service()->destroyIfEmpty($groupId, 'member_removed', 1));
        $this->assertNotNull(Group::find($groupId));
    }

    public function test_destroy_if_empty_deletes_empty_group_and_audits(): void
    {
        $periodId = $this->makePeriod();
        $groupId = $this->makeGroup($periodId);

        $this->assertTrue($this->service()->destroyIfEmpty($groupId, 'member_removed', 1));
        $this->assertNull(Group::find($groupId));
        $this->assertDatabaseHas('capstone_audit_logs', ['action' => 'GROUP_AUTO_DELETED', 'target_id' => $groupId]);
    }

    public function test_handle_shrink_reports_adequate_membership(): void
    {
        $periodId = $this->makePeriod(3, 4);
        $groupId = $this->makeGroup($periodId, 'READY_FOR_BIDDING');
        for ($i = 0; $i < 3; $i++) {
            $this->addMember($groupId, $this->makeStudent(), $i === 0, $periodId);
        }

        $shrink = $this->service()->handleMembershipShrink(Group::find($groupId));

        $this->assertSame(3, $shrink['member_count']);
        $this->assertFalse($shrink['undersized']);
        $this->assertFalse($shrink['oversized']);
    }

    public function test_transition_new_group_to_ready_when_min_reached(): void
    {
        $periodId = $this->makePeriod(2, 4);
        $groupId = $this->makeGroup($periodId, 'FORMING');
        $this->addMember($groupId, $this->makeStudent(), true, $periodId);
        $this->addMember($groupId, $this->makeStudent(), false, $periodId);

        $this->service()->transitionNewGroupToReadyIfEligible(Group::find($groupId), Period::find($periodId));

        $this->assertSame('READY_FOR_BIDDING', Group::find($groupId)->status);
    }

    public function test_transition_new_group_ignores_non_forming_status(): void
    {
        $periodId = $this->makePeriod(2, 4);
        $groupId = $this->makeGroup($periodId, 'READY_FOR_BIDDING');
        $this->addMember($groupId, $this->makeStudent(), true, $periodId);
        $this->addMember($groupId, $this->makeStudent(), false, $periodId);

        $this->service()->transitionNewGroupToReadyIfEligible(Group::find($groupId), Period::find($periodId));

        $this->assertSame('READY_FOR_BIDDING', Group::find($groupId)->status);
    }

    public function test_assign_supervisor2_rejects_unknown_lecturer(): void
    {
        $group = Group::find($this->makeGroup($this->makePeriod()));

        $this->expectException(DomainRuleException::class);
        $this->expectExceptionMessage('Supervisor 2 must be a lecturer.');
        $this->service()->assignSupervisor2ByLecturerId($group, 999999, 1);
    }

    public function test_assign_supervisor2_rejects_same_as_supervisor1(): void
    {
        $lecturerId = $this->makeDosenLecturer();
        $group = Group::find($this->makeGroup($this->makePeriod(), 'FORMING', 'GROUP', $lecturerId));

        $this->expectException(DomainRuleException::class);
        $this->expectExceptionMessage('cannot be the same');
        $this->service()->assignSupervisor2ByLecturerId($group, $lecturerId, 1);
    }

    public function test_assign_supervisor2_persists_supervision_and_cache(): void
    {
        $lecturerId = $this->makeDosenLecturer();
        $admin = $this->makeUser('Admin');
        $group = Group::find($this->makeGroup($this->makePeriod()));

        $result = $this->service()->assignSupervisor2ByLecturerId($group, $lecturerId, $admin->id);

        $this->assertSame($lecturerId, (int) $result->supervisor_2_id);
        $this->assertDatabaseHas('capstone_supervisions', [
            'group_id' => $group->id, 'supervisor_id' => $lecturerId, 'role' => 'SUPERVISOR_2',
        ]);
    }

    // ======================================================================
    // Endpoint characterization: remaining inline write bodies.
    // Pinned before extraction to GroupService; envelopes byte-identical.
    // ======================================================================

    /** @return array{user:User, student:int} */
    private function makeMahasiswa(string $name = 'Mhs', ?string $email = null): array
    {
        $user = $this->makeUser($name, $email ?? strtolower(str_replace(' ', '', $name)).'@test.local');
        $roleId = DB::table('roles')->insertGetId(['name' => 'mahasiswa', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $user->id]);
        $studentId = DB::table('students')->insertGetId(['user_id' => $user->id, 'name' => $name]);

        return ['user' => $user, 'student' => $studentId];
    }

    private function approveRegistration(int $studentId, int $periodId): void
    {
        // Mirrors the controller quirk: registration matched on profile id.
        DB::table('capstone_period_registrations')->insert([
            'user_id' => $studentId, 'period_id' => $periodId, 'status' => 'APPROVED',
        ]);
    }

    /** @return array{period:int, group:int} */
    private function seedGroupWithMembers(int $periodId, string $status, array $members): array
    {
        // $members: list of [studentId, isLeader].
        $groupId = $this->makeGroup($periodId, $status);
        foreach ($members as [$studentId, $leader]) {
            $this->addMember($groupId, $studentId, $leader, $periodId);
        }

        return ['period' => $periodId, 'group' => $groupId];
    }

    private function seedApprovedTitle(int $groupId, ?int $periodId = null): void
    {
        DB::table('capstone_titles')->insert([
            'title' => 'Judul A', 'title_source' => 'STUDENT',
            'supervisor_approval_status' => 'APPROVED',
            'period_id' => $periodId, 'proposed_by_group_id' => $groupId,
        ]);
    }

    private function groupController(): GroupController
    {
        return app(GroupController::class);
    }

    private function callStore(User $user, bool $solo, int $periodId)
    {
        $this->actingAs($user);
        $request = CreateStudentGroupRequest::create('/x', 'POST', ['period_id' => $periodId]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $solo
            ? $this->groupController()->storeSolo($request)
            : $this->groupController()->store($request);
    }

    public function test_store_creates_group_with_leader(): void
    {
        $mhs = $this->makeMahasiswa('Mhs1');
        $periodId = $this->makePeriod(2, 4);
        $this->approveRegistration($mhs['student'], $periodId);

        $response = $this->callStore($mhs['user'], false, $periodId);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('Group created successfully.', $response->getData(true)['message']);
        $groupId = $response->getData(true)['group']['id'];
        $this->assertSame('FORMING', DB::table('capstone_groups')->where('id', $groupId)->value('status'));
        $this->assertDatabaseHas('capstone_group_members', ['group_id' => $groupId, 'student_id' => $mhs['student'], 'is_leader' => true]);
    }

    public function test_store_solo_creates_solo_group(): void
    {
        $mhs = $this->makeMahasiswa('MhsSolo');
        $periodId = $this->makePeriod(2, 4);
        $this->approveRegistration($mhs['student'], $periodId);

        $response = $this->callStore($mhs['user'], true, $periodId);

        $this->assertSame(201, $response->getStatusCode());
        $groupId = $response->getData(true)['group']['id'];
        $this->assertSame('FORMING_SOLO', DB::table('capstone_groups')->where('id', $groupId)->value('status'));
    }

    public function test_store_rejects_closed_period(): void
    {
        $mhs = $this->makeMahasiswa('MhsClosed');
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P0', 'is_active' => true, 'is_finalized' => true]);
        $this->approveRegistration($mhs['student'], $periodId);

        $this->actingAs($mhs['user']);
        $request = CreateStudentGroupRequest::create('/x', 'POST', ['period_id' => $periodId]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $mhs['user']);
        $request->validateResolved();

        $this->expectException(HttpException::class);
        $this->groupController()->store($request);
    }

    public function test_store_rejects_unapproved_registration(): void
    {
        $mhs = $this->makeMahasiswa('MhsUnapproved');
        $periodId = $this->makePeriod(2, 4);

        $this->actingAs($mhs['user']);
        $request = CreateStudentGroupRequest::create('/x', 'POST', ['period_id' => $periodId]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $mhs['user']);
        $request->validateResolved();

        $this->expectException(HttpException::class);
        $this->groupController()->store($request);
    }

    public function test_store_rejects_active_group(): void
    {
        $mhs = $this->makeMahasiswa('MhsActive');
        $periodId = $this->makePeriod(2, 4);
        $this->approveRegistration($mhs['student'], $periodId);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$mhs['student'], true]]);

        $this->actingAs($mhs['user']);
        $request = CreateStudentGroupRequest::create('/x', 'POST', ['period_id' => $periodId]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $mhs['user']);
        $request->validateResolved();

        $this->expectException(HttpException::class);
        $this->groupController()->store($request);
        $this->assertNotNull(Group::find($seed['group']));
    }

    private function callChangeReady(User $user, int $groupId, bool $cancel)
    {
        $this->actingAs($user);
        $request = ChangeReadyRequest::create('/x', 'POST', ['group_id' => $groupId]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $cancel
            ? $this->groupController()->cancelReadyForFinalization($request)
            : $this->groupController()->markReadyForFinalization($request);
    }

    public function test_mark_ready_transitions_and_notifies(): void
    {
        $leader = $this->makeMahasiswa('Leader');
        $m2 = $this->makeMahasiswa('M2');
        $m3 = $this->makeMahasiswa('M3');
        $periodId = $this->makePeriod(3, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'READY_FOR_BIDDING', [
            [$leader['student'], true], [$m2['student'], false], [$m3['student'], false],
        ]);
        $this->seedApprovedTitle($seed['group'], $periodId);

        $response = $this->callChangeReady($leader['user'], $seed['group'], false);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('READY_FOR_FINALIZATION', Group::find($seed['group'])->status);
        $this->assertSame(3, DB::table('capstone_notifications')->where('type', 'GROUP_READY_FOR_FINALIZATION')->count());
    }

    public function test_cancel_ready_reverts_to_title_approved(): void
    {
        $leader = $this->makeMahasiswa('LeaderC');
        $m2 = $this->makeMahasiswa('M2C');
        $m3 = $this->makeMahasiswa('M3C');
        $periodId = $this->makePeriod(3, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'READY_FOR_FINALIZATION', [
            [$leader['student'], true], [$m2['student'], false], [$m3['student'], false],
        ]);
        $this->seedApprovedTitle($seed['group'], $periodId);

        $response = $this->callChangeReady($leader['user'], $seed['group'], true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('TITLE_APPROVED', Group::find($seed['group'])->status);
    }

    public function test_delete_group_as_leader(): void
    {
        $leader = $this->makeMahasiswa('LeaderD');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);

        $this->actingAs($leader['user']);
        $request = Request::create('/x', 'DELETE');
        $request->setUserResolver(fn () => $leader['user']);
        $response = $this->groupController()->deleteGroup($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Group deleted successfully.', $response->getData(true)['message']);
        $this->assertNull(Group::find($seed['group']));
        // Member rows soft-delete (model uses SoftDeletes).
        $this->assertSame(0, DB::table('capstone_group_members')->where('group_id', $seed['group'])->whereNull('deleted_at')->count());
    }

    public function test_delete_group_rejects_non_leader(): void
    {
        $leader = $this->makeMahasiswa('LeaderE');
        $member = $this->makeMahasiswa('MemberE');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [
            [$leader['student'], true], [$member['student'], false],
        ]);

        $this->actingAs($member['user']);
        $request = Request::create('/x', 'DELETE');
        $request->setUserResolver(fn () => $member['user']);
        $response = $this->groupController()->deleteGroup($request);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertNotNull(Group::find($seed['group']));
    }

    public function test_delete_group_rejects_after_finalization(): void
    {
        $leader = $this->makeMahasiswa('LeaderF');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'KELOMPOK_FINAL', [[$leader['student'], true]]);

        $this->actingAs($leader['user']);
        $request = Request::create('/x', 'DELETE');
        $request->setUserResolver(fn () => $leader['user']);

        // The presenter lock fires before the finalized-status branch, so a
        // finalized delete reports the lock (the 400 branch below stays as
        // unreachable defense-in-depth).
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Group action is locked.');
        $this->groupController()->deleteGroup($request);
    }

    private function callAddMember(User $user, string $email)
    {
        $this->actingAs($user);
        $request = AddMemberRequest::create('/x', 'POST', ['email' => $email]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $this->groupController()->addMember($request);
    }

    public function test_add_member_sends_invitation(): void
    {
        $leader = $this->makeMahasiswa('LeaderG');
        $invitee = $this->makeMahasiswa('InviteeG', 'invitee@test.local');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);

        $response = $this->callAddMember($leader['user'], 'invitee@test.local');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Invitation sent successfully', $response->getData(true)['message']);
        $this->assertDatabaseHas('capstone_group_invitations', [
            'group_id' => $seed['group'], 'student_id' => $invitee['student'], 'status' => 'PENDING',
        ]);
        $this->assertDatabaseHas('capstone_notifications', ['type' => 'GROUP_INVITATION']);
    }

    public function test_add_member_rejects_full_group(): void
    {
        $leader = $this->makeMahasiswa('LeaderH');
        $m2 = $this->makeMahasiswa('M2H');
        $invitee = $this->makeMahasiswa('InviteeH', 'inviteeh@test.local');
        $periodId = $this->makePeriod(2, 2);
        $this->seedGroupWithMembers($periodId, 'FORMING', [
            [$leader['student'], true], [$m2['student'], false],
        ]);

        // The presenter lock fires first when full (same max condition), so
        // the 400 branch below stays as unreachable defense-in-depth.
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Group action is locked.');
        $this->callAddMember($leader['user'], 'inviteeh@test.local');
    }

    public function test_accept_invite_joins_group(): void
    {
        $leader = $this->makeMahasiswa('LeaderI');
        $invitee = $this->makeMahasiswa('InviteeI');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);
        $inviteId = DB::table('capstone_group_invitations')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $invitee['student'],
            'inviter_id' => $leader['student'], 'status' => 'PENDING',
        ]);

        $this->actingAs($invitee['user']);
        $request = Request::create('/x', 'POST');
        $request->setUserResolver(fn () => $invitee['user']);
        $response = $this->groupController()->acceptInvite($request, $inviteId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Invitation accepted.', $response->getData(true)['message']);
        $this->assertDatabaseHas('capstone_group_members', ['group_id' => $seed['group'], 'student_id' => $invitee['student']]);
        $this->assertSame('ACCEPTED', DB::table('capstone_group_invitations')->where('id', $inviteId)->value('status'));
    }

    public function test_accept_invite_rejects_finalized_group(): void
    {
        $leader = $this->makeMahasiswa('LeaderJ');
        $invitee = $this->makeMahasiswa('InviteeJ');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'KELOMPOK_FINAL', [[$leader['student'], true]]);
        $inviteId = DB::table('capstone_group_invitations')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $invitee['student'],
            'inviter_id' => $leader['student'], 'status' => 'PENDING',
        ]);

        $this->actingAs($invitee['user']);
        $request = Request::create('/x', 'POST');
        $request->setUserResolver(fn () => $invitee['user']);
        $response = $this->groupController()->acceptInvite($request, $inviteId);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_reject_invite_notifies_inviter(): void
    {
        $leader = $this->makeMahasiswa('LeaderK');
        $invitee = $this->makeMahasiswa('InviteeK');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);
        $inviteId = DB::table('capstone_group_invitations')->insertGetId([
            'group_id' => $seed['group'], 'student_id' => $invitee['student'],
            'inviter_id' => $leader['student'], 'status' => 'PENDING',
        ]);

        $this->actingAs($invitee['user']);
        $request = Request::create('/x', 'POST');
        $request->setUserResolver(fn () => $invitee['user']);
        $response = $this->groupController()->rejectInvite($request, $inviteId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Invitation rejected.', $response->getData(true)['message']);
        $this->assertSame('REJECTED', DB::table('capstone_group_invitations')->where('id', $inviteId)->value('status'));
        $this->assertDatabaseHas('capstone_notifications', ['type' => 'INVITE_REJECTED']);
    }

    public function test_remove_member_shrinks_group(): void
    {
        $leader = $this->makeMahasiswa('LeaderL');
        $m2 = $this->makeMahasiswa('M2L');
        $m3 = $this->makeMahasiswa('M3L');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [
            [$leader['student'], true], [$m2['student'], false], [$m3['student'], false],
        ]);
        $memberId = DB::table('capstone_group_members')->where('group_id', $seed['group'])->where('student_id', $m2['student'])->value('id');

        $this->actingAs($leader['user']);
        $request = Request::create('/x', 'DELETE');
        $request->setUserResolver(fn () => $leader['user']);
        $response = $this->groupController()->removeMember($request, $memberId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Member removed', $response->getData(true)['message']);
        // Member rows soft-delete.
        $this->assertNotNull(DB::table('capstone_group_members')->where('id', $memberId)->value('deleted_at'));
        $this->assertArrayHasKey('membership_shrink', $response->getData(true));
    }

    public function test_remove_member_rejects_self_removal(): void
    {
        $leader = $this->makeMahasiswa('LeaderM');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);
        $memberId = DB::table('capstone_group_members')->where('group_id', $seed['group'])->value('id');

        $this->actingAs($leader['user']);
        $request = Request::create('/x', 'DELETE');
        $request->setUserResolver(fn () => $leader['user']);
        $response = $this->groupController()->removeMember($request, $memberId);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_leave_group_as_member(): void
    {
        $leader = $this->makeMahasiswa('LeaderN');
        $member = $this->makeMahasiswa('MemberN');
        $periodId = $this->makePeriod(2, 4);
        $seed = $this->seedGroupWithMembers($periodId, 'FORMING', [
            [$leader['student'], true], [$member['student'], false],
        ]);

        $this->actingAs($member['user']);
        $request = Request::create('/x', 'POST');
        $request->setUserResolver(fn () => $member['user']);
        $response = $this->groupController()->leaveGroup($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('You have left the group.', $response->getData(true)['message']);
        // Member rows soft-delete.
        $this->assertNotNull(DB::table('capstone_group_members')->where('group_id', $seed['group'])->where('student_id', $member['student'])->value('deleted_at'));
    }

    public function test_leave_group_rejects_leader(): void
    {
        $leader = $this->makeMahasiswa('LeaderO');
        $periodId = $this->makePeriod(2, 4);
        $this->seedGroupWithMembers($periodId, 'FORMING', [[$leader['student'], true]]);

        $this->actingAs($leader['user']);
        $request = Request::create('/x', 'POST');
        $request->setUserResolver(fn () => $leader['user']);
        $response = $this->groupController()->leaveGroup($request);

        $this->assertSame(400, $response->getStatusCode());
    }
}
