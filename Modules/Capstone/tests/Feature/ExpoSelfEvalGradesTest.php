<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Services\GradeCalculationService;
use Tests\TestCase;

/** Isolated real SQL: EXPO grade must come from member self-evaluations. */
class ExpoSelfEvalGradesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.expo_self_eval_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('expo_self_eval_test');

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
            $t->string('student_number')->nullable();
            $t->integer('cohort_year')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_groups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('period_id');
            $t->string('status')->default('EXPO_REGISTERED');
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
        Schema::create('capstone_period_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('period_id');
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        foreach (['capstone_nilai_dosen_scores', 'capstone_milestone_scores'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('component_id')->nullable();
                $t->unsignedBigInteger('period_component_id')->nullable();
                $t->unsignedBigInteger('evaluator_id');
                $t->unsignedBigInteger('group_id');
                $t->unsignedBigInteger('student_id')->nullable();
                $t->decimal('score', 5, 2)->nullable();
                $t->text('notes')->nullable();
                $t->timestamps();
            });
        }
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
        Schema::create('capstone_expo_self_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('period_component_id');
            $t->decimal('score', 5, 2);
            $t->text('notes')->nullable();
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
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_expo_scores', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('evaluator_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('period_component_id');
            $t->decimal('score', 5, 2);
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_peer_reviews', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('reviewer_id');
            $t->unsignedBigInteger('reviewee_id');
            $t->unsignedBigInteger('indicator_id')->nullable();
            $t->decimal('score', 5, 2);
            $t->text('comment')->nullable();
            $t->boolean('is_final_submission')->default(false);
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('expo_self_eval_test');
        parent::tearDown();
    }

    /**
     * Seed an EXPO group whose representative submitted both components on
     * a REGISTERED registration left at $reviewStatus.
     *
     * @return array{int, int, int} [studentId, groupId, secondStudentId]
     */
    private function seedGroupWithApprovedSelfEvaluation(string $reviewStatus = 'APPROVED'): array
    {
        $userId = DB::table('users')->insertGetId(['name' => 'Sinta']);
        $studentId = DB::table('students')->insertGetId(['user_id' => $userId, 'student_number' => '001']);
        $otherUserId = DB::table('users')->insertGetId(['name' => 'Bagas']);
        $otherStudentId = DB::table('students')->insertGetId(['user_id' => $otherUserId, 'student_number' => '002']);
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P2']);
        $groupId = DB::table('capstone_groups')->insertGetId(['period_id' => $periodId, 'status' => 'EXPO_REGISTERED']);
        DB::table('capstone_group_members')->insert([
            ['group_id' => $groupId, 'student_id' => $studentId, 'is_leader' => true],
            ['group_id' => $groupId, 'student_id' => $otherStudentId, 'is_leader' => false],
        ]);
        DB::table('capstone_period_registrations')->insert(['user_id' => $studentId, 'period_id' => $periodId, 'status' => 'APPROVED']);
        DB::table('capstone_period_registrations')->insert(['user_id' => $otherStudentId, 'period_id' => $periodId, 'status' => 'APPROVED']);

        $t1 = DB::table('capstone_assessment_component_templates')->insertGetId(['code' => 'EXPO-1', 'name' => 'Prototype', 'weight' => 60]);
        $t2 = DB::table('capstone_assessment_component_templates')->insertGetId(['code' => 'EXPO-2', 'name' => 'Poster', 'weight' => 40]);
        $c1 = DB::table('capstone_period_assessment_components')->insertGetId(['period_id' => $periodId, 'template_id' => $t1, 'type' => 'EXPO']);
        $c2 = DB::table('capstone_period_assessment_components')->insertGetId(['period_id' => $periodId, 'template_id' => $t2, 'type' => 'EXPO']);

        $registrationId = DB::table('capstone_expo_registrations')->insertGetId([
            'group_id' => $groupId, 'status' => 'REGISTERED', 'review_status' => $reviewStatus,
        ]);
        ExpoSelfEvaluation::create(['expo_registration_id' => $registrationId, 'group_id' => $groupId, 'student_id' => $studentId, 'period_component_id' => $c1, 'score' => 90]);
        ExpoSelfEvaluation::create(['expo_registration_id' => $registrationId, 'group_id' => $groupId, 'student_id' => $studentId, 'period_component_id' => $c2, 'score' => 75]);

        // A legacy examiner ExpoScore row must NOT influence the grade.
        DB::table('capstone_expo_scores')->insert(['evaluator_id' => 999, 'group_id' => $groupId, 'student_id' => $studentId, 'period_component_id' => $c1, 'score' => 10]);

        return [$studentId, $groupId, $otherStudentId];
    }

    public function test_pdc2_expo_component_comes_from_member_self_evaluations(): void
    {
        [$studentId, $groupId] = $this->seedGroupWithApprovedSelfEvaluation();

        $result = (new GradeCalculationService)->calculatePDC2ForStudent($studentId, $groupId);

        // (90*60 + 75*40) / 100 = 84, the only non-null PDC2 component.
        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(84.0, $result['grade'], 0.01);
        $this->assertEqualsWithDelta(84.0, $result['components']['EXPO']['score'], 0.01);
        $this->assertSame(1, $result['component_count']);
        $this->assertSame('PARTIAL', $result['status']);
        $this->assertSame([['name' => 'Sinta', 'role' => 'SELF', 'score' => 84.0]], $result['components']['EXPO']['evaluators']);
    }

    public function test_pdc2_expo_component_waits_for_supervisor_review_approval(): void
    {
        [$studentId, $groupId] = $this->seedGroupWithApprovedSelfEvaluation('SUBMITTED');

        $result = (new GradeCalculationService)->calculatePDC2ForStudent($studentId, $groupId);

        $this->assertTrue($result === null || ($result['components']['EXPO']['score'] ?? null) === null);
    }

    public function test_pdc2_expo_component_stays_locked_after_review_rejection(): void
    {
        [$studentId, $groupId] = $this->seedGroupWithApprovedSelfEvaluation('REJECTED');

        $result = (new GradeCalculationService)->calculatePDC2ForStudent($studentId, $groupId);

        $this->assertTrue($result === null || ($result['components']['EXPO']['score'] ?? null) === null);
    }

    public function test_member_without_own_scores_inherits_approved_representative_submission(): void
    {
        [, $groupId, $otherStudentId] = $this->seedGroupWithApprovedSelfEvaluation();

        $result = (new GradeCalculationService)->calculatePDC2ForStudent($otherStudentId, $groupId);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(84.0, $result['components']['EXPO']['score'], 0.01);
    }

    public function test_inheritance_is_blocked_while_review_is_pending(): void
    {
        [, $groupId, $otherStudentId] = $this->seedGroupWithApprovedSelfEvaluation('SUBMITTED');

        $result = (new GradeCalculationService)->calculatePDC2ForStudent($otherStudentId, $groupId);

        $this->assertTrue($result === null || ($result['components']['EXPO']['score'] ?? null) === null);
    }
}
