<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\SupervisorEvaluationController;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\ReviewExpoRequest;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\StoreSupervisorEvaluationRequest;
use Tests\TestCase;

/**
 * Characterization: supervisor EXPO review + evaluation submit, pinned
 * before extraction to SupervisorEvaluationService + Form Requests.
 * Guards keep their exact codes (403 supervision/window, 404/409/422
 * registration states, 400 membership, 422 component) via the error-array
 * idiom; side effects (notify, recalc, lifecycle) stay fail-open.
 * Legacy component schema (no period table) is covered; the period schema
 * path is owned by existing suites.
 * Isolated real SQL: never migrate the application's configured database.
 */
class SupervisorEvaluationWritesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.supervisor_eval_writes_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('supervisor_eval_writes_test');

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
            $t->dateTime('nilai_dosen_deadline')->nullable();
            $t->dateTime('milestone_deadline')->nullable();
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
        Schema::create('capstone_expo_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_event_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->string('status')->default('REGISTERED');
            $t->string('review_status')->nullable();
            $t->text('review_feedback')->nullable();
            $t->unsignedBigInteger('reviewed_by')->nullable();
            $t->dateTime('reviewed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_expo_self_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->unsignedBigInteger('period_component_id')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_assessment_components', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('type');
            $t->string('code')->nullable();
            $t->string('name')->nullable();
            $t->decimal('weight', 5, 2)->default(0);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('capstone_nilai_dosen_scores', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('component_id')->nullable();
            $t->unsignedBigInteger('period_component_id')->nullable();
            $t->unsignedBigInteger('evaluator_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->text('notes')->nullable();
            $t->string('evaluation_type')->nullable();
            $t->timestamps();
            // Production upsert contract (unique keys the repository upserts on).
            $t->unique(['component_id', 'evaluator_id', 'group_id', 'student_id']);
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
    }

    protected function tearDown(): void
    {
        DB::purge('supervisor_eval_writes_test');
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

    /** @return array{user:User, lecturer:int} */
    private function makeDosen(string $name = 'Dosen'): array
    {
        $user = $this->withRole($this->makeUser($name, strtolower(str_replace(' ', '', $name)).'@test.local'), 'dosen');
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $user->id, 'name' => $name]);

        return ['user' => $user, 'lecturer' => $lecturerId];
    }

    /** @return array{period:int, group:int, students:array<int>} */
    private function seedGroup(string $status = 'EXPO_REGISTERED', ?int $supervisorId = null): array
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1', 'is_active' => true, 'is_finalized' => false]);
        $groupId = DB::table('capstone_groups')->insertGetId([
            'period_id' => $periodId, 'status' => $status, 'supervisor_1_id' => $supervisorId,
        ]);
        $studentIds = [];
        for ($i = 1; $i <= 2; $i++) {
            $userId = DB::table('users')->insertGetId(['name' => "M{$i}"]);
            $studentId = DB::table('students')->insertGetId(['user_id' => $userId, 'name' => "M{$i}"]);
            $studentIds[] = $studentId;
            DB::table('capstone_group_members')->insert(['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => $i === 1, 'period_id' => $periodId]);
        }
        if ($supervisorId !== null) {
            DB::table('capstone_supervisions')->insert(['group_id' => $groupId, 'supervisor_id' => $supervisorId, 'role' => 'SUPERVISOR_1']);
        }

        return ['period' => $periodId, 'group' => $groupId, 'students' => $studentIds];
    }

    private function seedRegistration(int $groupId, string $reviewStatus = 'SUBMITTED', bool $withSelfEval = true): int
    {
        $regId = DB::table('capstone_expo_registrations')->insertGetId([
            'group_id' => $groupId, 'status' => 'REGISTERED', 'review_status' => $reviewStatus,
        ]);
        if ($withSelfEval) {
            $studentId = DB::table('capstone_group_members')->where('group_id', $groupId)->value('student_id');
            DB::table('capstone_expo_self_evaluations')->insert([
                'expo_registration_id' => $regId, 'group_id' => $groupId, 'student_id' => $studentId, 'score' => 85,
            ]);
        }

        return $regId;
    }

    private function controller(): SupervisorEvaluationController
    {
        return app(SupervisorEvaluationController::class);
    }

    private function reviewAs(User $user, int $groupId, array $params)
    {
        $this->actingAs($user);
        $request = ReviewExpoRequest::create('/x', 'POST', $params);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $this->controller()->reviewExpo($request, $groupId);
    }

    private function submitAs(User $user, array $params)
    {
        $this->actingAs($user);
        $request = StoreSupervisorEvaluationRequest::create('/x', 'POST', $params);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $user);
        $request->validateResolved();

        return $this->controller()->store($request);
    }

    public function test_review_rejects_non_supervisor(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup();

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('You are not a supervisor of this group', $response->getData(true)['message']);
    }

    public function test_review_rejects_uneditable_window(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('FORMING', $dosen['lecturer']);

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('EXPO', $response->getData(true)['message']);
    }

    public function test_review_rejects_missing_registration(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('EXPO_REGISTERED', $dosen['lecturer']);

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('No active expo registration for this group', $response->getData(true)['message']);
    }

    public function test_review_rejects_unsubmitted_review(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('EXPO_REGISTERED', $dosen['lecturer']);
        $this->seedRegistration($seed['group'], 'DRAFT');

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame('This submission is not awaiting review.', $response->getData(true)['message']);
    }

    public function test_review_rejects_missing_self_eval(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('EXPO_REGISTERED', $dosen['lecturer']);
        $this->seedRegistration($seed['group'], 'SUBMITTED', false);

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('The representative has not submitted a self-evaluation yet.', $response->getData(true)['message']);
    }

    public function test_review_approves_and_notifies_members(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('EXPO_REGISTERED', $dosen['lecturer']);
        $this->seedRegistration($seed['group']);

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'APPROVED']);

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true)['data'];
        $this->assertSame('EXPO submission approved successfully', $data['message']);
        $this->assertSame('APPROVED', $data['review_status']);
        $this->assertDatabaseHas('capstone_expo_registrations', [
            'group_id' => $seed['group'], 'review_status' => 'APPROVED', 'reviewed_by' => $dosen['lecturer'],
        ]);
        $this->assertSame(2, DB::table('capstone_notifications')->where('type', 'EXPO_APPROVED')->where('related_id', $seed['group'])->count());
    }

    public function test_review_rejects_and_reopens(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('EXPO_REGISTERED', $dosen['lecturer']);
        $this->seedRegistration($seed['group']);

        $response = $this->reviewAs($dosen['user'], $seed['group'], ['status' => 'REJECTED', 'feedback' => 'Perbaiki dokumen']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('EXPO submission rejected successfully', $response->getData(true)['data']['message']);
        $this->assertDatabaseHas('capstone_expo_registrations', [
            'group_id' => $seed['group'], 'review_status' => 'REJECTED', 'review_feedback' => 'Perbaiki dokumen',
        ]);
        $this->assertSame(2, DB::table('capstone_notifications')->where('type', 'EXPO_REJECTED')->where('related_id', $seed['group'])->count());
    }

    public function test_submit_rejects_non_supervisor(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('PDC2_ACTIVE');
        $componentId = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $seed['period'], 'type' => 'NILAI_DOSEN', 'name' => 'Komitmen',
        ]);

        $response = $this->submitAs($dosen['user'], [
            'group_id' => $seed['group'], 'evaluation_type' => 'NILAI_DOSEN',
            'scores' => [['student_id' => $seed['students'][0], 'component_id' => $componentId, 'score' => 80]],
        ]);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('You are not a supervisor of this group', $response->getData(true)['message']);
    }

    public function test_submit_rejects_uneditable_window(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('FORMING', $dosen['lecturer']);
        $componentId = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $seed['period'], 'type' => 'NILAI_DOSEN', 'name' => 'Komitmen',
        ]);

        $response = $this->submitAs($dosen['user'], [
            'group_id' => $seed['group'], 'evaluation_type' => 'NILAI_DOSEN',
            'scores' => [['student_id' => $seed['students'][0], 'component_id' => $componentId, 'score' => 80]],
        ]);

        $this->assertSame(403, $response->getStatusCode());
        $this->assertStringContainsString('Current status: FORMING', $response->getData(true)['message']);
    }

    public function test_submit_rejects_non_member_student(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('PDC2_ACTIVE', $dosen['lecturer']);
        $outsider = DB::table('students')->insertGetId(['name' => 'Outsider']);
        $componentId = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $seed['period'], 'type' => 'NILAI_DOSEN', 'name' => 'Komitmen',
        ]);

        $response = $this->submitAs($dosen['user'], [
            'group_id' => $seed['group'], 'evaluation_type' => 'NILAI_DOSEN',
            'scores' => [['student_id' => $outsider, 'component_id' => $componentId, 'score' => 80]],
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('is not a member of this group', $response->getData(true)['message']);
        $this->assertSame(0, DB::table('capstone_nilai_dosen_scores')->count());
    }

    public function test_submit_rejects_missing_component_id(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('PDC2_ACTIVE', $dosen['lecturer']);

        $response = $this->submitAs($dosen['user'], [
            'group_id' => $seed['group'], 'evaluation_type' => 'NILAI_DOSEN',
            'scores' => [['student_id' => $seed['students'][0], 'score' => 80]],
        ]);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertStringContainsString('missing both period_component_id and component_id', $response->getData(true)['message']);
    }

    public function test_submit_upserts_scores(): void
    {
        $dosen = $this->makeDosen();
        $seed = $this->seedGroup('PDC2_ACTIVE', $dosen['lecturer']);
        $componentId = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $seed['period'], 'type' => 'NILAI_DOSEN', 'name' => 'Komitmen',
        ]);

        $params = [
            'group_id' => $seed['group'], 'evaluation_type' => 'NILAI_DOSEN',
            'scores' => [
                ['student_id' => $seed['students'][0], 'component_id' => $componentId, 'score' => 80, 'notes' => 'Baik'],
                ['student_id' => $seed['students'][1], 'component_id' => $componentId, 'score' => 90],
            ],
        ];
        $response = $this->submitAs($dosen['user'], $params);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Evaluation submitted successfully', $response->getData(true)['data']['message']);
        $this->assertDatabaseHas('capstone_nilai_dosen_scores', [
            'component_id' => $componentId, 'evaluator_id' => $dosen['lecturer'],
            'group_id' => $seed['group'], 'student_id' => $seed['students'][0], 'score' => 80,
        ]);

        // Resubmit updates in place (no duplicate rows).
        $params['scores'][0]['score'] = 85;
        $this->submitAs($dosen['user'], $params);
        $this->assertSame(2, DB::table('capstone_nilai_dosen_scores')->count());
        $this->assertSame(85.0, (float) DB::table('capstone_nilai_dosen_scores')
            ->where('student_id', $seed['students'][0])->value('score'));
    }
}
