<?php

namespace Modules\Capstone\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Modules\Capstone\Http\Controllers\AssessmentScoreController;
use Modules\Capstone\Http\Requests\Assessment\ScoreFormRequest;
use Modules\Capstone\Http\Requests\Assessment\ScoreSummaryRequest;
use Modules\Capstone\Http\Requests\Assessment\StoreScoresRequest;
use Tests\TestCase;

/**
 * Contract: split-table EXPO writes are rejected at validation with a
 * redirect to the member self-evaluation flow (ADR 0012). The engine
 * grades EXPO only from self-evaluations; accepting the writes would
 * silently ignore them. The summary endpoint keeps serving old EXPO rows
 * read-only for audit.
 * Isolated real SQL: never migrate the application's configured database.
 */
class ExpoWriteRejectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.expo_rejection_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('expo_rejection_test');

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
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('code')->nullable();
            $t->string('status')->default('FORMING');
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
    }

    protected function tearDown(): void
    {
        DB::purge('expo_rejection_test');
        parent::tearDown();
    }

    /** @return array{user:User, lecturer:int, group:int, student:int, component:int, period:int} */
    private function seedScores(): array
    {
        $user = User::find(DB::table('users')->insertGetId(['name' => 'Dosen']));
        $roleId = DB::table('roles')->insertGetId(['name' => 'dosen', 'guard_name' => 'web']);
        DB::table('model_has_roles')->insert(['role_id' => $roleId, 'model_type' => User::class, 'model_id' => $user->id]);
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $user->id, 'name' => 'Dosen']);
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1']);
        $groupId = DB::table('capstone_groups')->insertGetId(['period_id' => $periodId, 'status' => 'PDC2_ACTIVE']);
        $memberUserId = DB::table('users')->insertGetId(['name' => 'Mhs']);
        $studentId = DB::table('students')->insertGetId(['user_id' => $memberUserId, 'name' => 'Mhs']);
        DB::table('capstone_group_members')->insert(['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => true, 'period_id' => $periodId]);
        DB::table('capstone_supervisions')->insert(['group_id' => $groupId, 'supervisor_id' => $lecturerId, 'role' => 'SUPERVISOR_1']);
        $componentId = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $periodId, 'type' => 'BIMBINGAN', 'name' => 'Komitmen', 'weight' => 100,
        ]);

        return ['user' => $user, 'lecturer' => $lecturerId, 'group' => $groupId, 'student' => $studentId, 'component' => $componentId, 'period' => $periodId];
    }

    private function controller(): AssessmentScoreController
    {
        return app(AssessmentScoreController::class);
    }

    public function test_store_rejects_expo_with_self_eval_redirect(): void
    {
        $seed = $this->seedScores();
        $this->actingAs($seed['user']);

        $request = StoreScoresRequest::create('/x', 'POST', [
            'group_id' => $seed['group'],
            'evaluation_type' => 'EXPO',
            'scores' => [['component_id' => $seed['component'], 'student_id' => $seed['student'], 'score' => 80]],
        ]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $seed['user']);

        try {
            $request->validateResolved();
            $this->fail('EXPO split-table writes must be rejected.');
        } catch (ValidationException $e) {
            $this->assertSame(422, $e->status);
            $this->assertStringContainsString(
                'self-evaluation',
                (string) $e->validator->errors()->first('evaluation_type')
            );
        }

        $this->assertSame(0, DB::table('capstone_assessment_scores')->count());
    }

    public function test_index_rejects_expo(): void
    {
        $seed = $this->seedScores();
        $this->actingAs($seed['user']);

        $request = ScoreFormRequest::create('/x', 'POST', ['group_id' => $seed['group'], 'type' => 'EXPO']);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $seed['user']);

        try {
            $request->validateResolved();
            $this->fail('EXPO form must be rejected.');
        } catch (ValidationException $e) {
            $this->assertSame(422, $e->status);
            $this->assertStringContainsString('self-evaluation', (string) $e->validator->errors()->first('type'));
        }
    }

    public function test_store_still_accepts_bimbingan(): void
    {
        $seed = $this->seedScores();
        $this->actingAs($seed['user']);

        $request = StoreScoresRequest::create('/x', 'POST', [
            'group_id' => $seed['group'],
            'evaluation_type' => 'BIMBINGAN',
            'scores' => [['component_id' => $seed['component'], 'student_id' => $seed['student'], 'score' => 80]],
        ]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $seed['user']);
        $request->validateResolved();

        $response = $this->controller()->store($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertDatabaseHas('capstone_assessment_scores', [
            'group_id' => $seed['group'], 'evaluation_type' => 'BIMBINGAN', 'score' => 80,
        ]);
    }

    public function test_summary_still_serves_expo_rows_read_only(): void
    {
        $seed = $this->seedScores();
        $expoComponent = DB::table('capstone_assessment_components')->insertGetId([
            'period_id' => $seed['period'], 'type' => 'EXPO', 'name' => 'Expo', 'weight' => 25,
        ]);
        DB::table('capstone_assessment_scores')->insert([
            'component_id' => $expoComponent, 'evaluator_id' => $seed['lecturer'],
            'group_id' => $seed['group'], 'student_id' => $seed['student'],
            'score' => 84, 'evaluation_type' => 'EXPO',
        ]);
        $this->actingAs($seed['user']);

        $request = ScoreSummaryRequest::create('/x', 'POST', ['period_id' => $seed['period'], 'type' => 'EXPO']);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $seed['user']);
        $request->validateResolved();
        $response = $this->controller()->summary($request);

        $this->assertSame(200, $response->getStatusCode());
        $grouped = $response->getData(true);
        $this->assertSame(84.0, (float) $grouped[(string) $seed['group']][(string) $seed['student']]['weighted_avg']);
    }
}
