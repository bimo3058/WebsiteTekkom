<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\BimbinganSemproScore;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\MilestoneScore;
use Modules\Capstone\Models\NilaiDosenScore;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\GroupLifecycleService;
use Tests\TestCase;

/** Isolated real SQL: never migrate the application's configured database. */
class GroupLifecycleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.capstone_lifecycle_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('capstone_lifecycle_test');

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
            $t->decimal('weight', 5, 2)->default(0);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });
        foreach (['capstone_nilai_dosen_scores', 'capstone_milestone_scores'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('component_id')->nullable();
                $t->unsignedBigInteger('evaluator_id');
                $t->unsignedBigInteger('group_id');
                $t->unsignedBigInteger('student_id')->nullable();
                $t->decimal('score', 5, 2)->nullable();
                $t->text('notes')->nullable();
                $t->timestamps();
            });
        }
        Schema::create('capstone_seminar_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('group_id');
            $t->string('type');
            $t->unsignedBigInteger('examiner_1_id')->nullable();
            $t->unsignedBigInteger('examiner_2_id')->nullable();
            $t->string('status')->default('SCHEDULED');
            $t->timestamps();
        });
        Schema::create('capstone_seminar_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('schedule_id');
            $t->unsignedBigInteger('examiner_id');
            $t->text('rubric_json')->nullable();
            $t->decimal('score', 5, 2)->nullable();
            $t->string('result')->nullable();
            $t->string('status')->default('PENDING');
            $t->timestamps();
        });
        Schema::create('capstone_bimbingan_sempro_scores', function (Blueprint $t) {
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
        Schema::create('capstone_expo_registrations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_event_id')->nullable();
            $t->unsignedBigInteger('group_id');
            $t->string('status')->default('REGISTERED');
            $t->timestamps();
        });
        Schema::create('capstone_expo_student_documents', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->string('file_path')->nullable();
            $t->string('original_name')->nullable();
            $t->string('status')->default('SUBMITTED');
            $t->timestamps();
        });
        Schema::create('capstone_expo_self_evaluations', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('expo_registration_id');
            $t->unsignedBigInteger('group_id');
            $t->unsignedBigInteger('student_id');
            $t->unsignedBigInteger('period_component_id');
            $t->decimal('score', 5, 2);
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::purge('capstone_lifecycle_test');
        parent::tearDown();
    }

    private function makeGroup(string $status): Group
    {
        $period = Period::create(['name' => 'TA 2026']);
        $userId = DB::table('users')->insertGetId(['name' => 'Dr. Supervisor']);
        $lecturerId = DB::table('lecturers')->insertGetId(['user_id' => $userId, 'name' => 'Dr. Supervisor']);

        $group = Group::create([
            'period_id' => $period->id,
            'code' => 'GRP-001',
            'status' => $status,
            'supervisor_1_id' => $lecturerId,
        ]);

        GroupMember::create(['group_id' => $group->id, 'student_id' => 1]);

        foreach (['NILAI_DOSEN', 'MILESTONE'] as $type) {
            AssessmentComponent::create([
                'period_id' => $period->id,
                'type' => $type,
                'code' => $type.'-1',
                'name' => $type.' component',
                'weight' => 100,
                'sort_order' => 1,
            ]);
        }

        return $group;
    }

    private function submitFullScores(Group $group, string $type): void
    {
        $component = AssessmentComponent::where('period_id', $group->period_id)
            ->where('type', $type)
            ->firstOrFail();

        $model = $type === 'NILAI_DOSEN' ? NilaiDosenScore::class : MilestoneScore::class;
        $model::create([
            'component_id' => $component->id,
            'evaluator_id' => $group->supervisor_1_id,
            'group_id' => $group->id,
            'student_id' => 1,
            'score' => 85,
        ]);
    }

    public function test_advances_pdc2_active_to_ta_draft_when_grading_complete(): void
    {
        $group = $this->makeGroup('PDC2_ACTIVE');
        $this->submitFullScores($group, 'NILAI_DOSEN');
        $this->submitFullScores($group, 'MILESTONE');

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('TA_DRAFT', $advanced);
        $this->assertSame('TA_DRAFT', $group->fresh()->status);
    }

    public function test_stays_pdc2_active_when_scores_incomplete(): void
    {
        $group = $this->makeGroup('PDC2_ACTIVE');
        $this->submitFullScores($group, 'NILAI_DOSEN');

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('PDC2_ACTIVE', $group->fresh()->status);
    }

    public function test_advance_is_idempotent(): void
    {
        $group = $this->makeGroup('PDC2_ACTIVE');
        $this->submitFullScores($group, 'NILAI_DOSEN');
        $this->submitFullScores($group, 'MILESTONE');

        $service = app(GroupLifecycleService::class);
        $this->assertSame('TA_DRAFT', $service->advanceIfComplete($group));
        $this->assertNull($service->advanceIfComplete($group));
        $this->assertSame('TA_DRAFT', $group->fresh()->status);
    }

    public function test_ready_for_sempro_without_completed_schedule_does_nothing(): void
    {
        $group = $this->makeGroup('READY_FOR_SEMPRO');

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('READY_FOR_SEMPRO', $group->fresh()->status);
    }

    private function makeSemproGroup(string $status, bool $examinersSubmitted = true): Group
    {
        $group = $this->makeGroup($status);

        AssessmentComponent::create([
            'period_id' => $group->period_id,
            'type' => 'BIMBINGAN_SEMPRO',
            'code' => 'BIMBINGAN_SEMPRO-1',
            'name' => 'Bimbingan sempro component',
            'weight' => 100,
            'sort_order' => 1,
        ]);

        $examiner1 = DB::table('lecturers')->insertGetId(['name' => 'Penguji 1']);
        $examiner2 = DB::table('lecturers')->insertGetId(['name' => 'Penguji 2']);

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id,
            'type' => 'SEMPRO',
            'status' => 'COMPLETED',
            'examiner_1_id' => $examiner1,
            'examiner_2_id' => $examiner2,
        ]);

        SeminarEvaluation::create([
            'schedule_id' => $schedule->id,
            'examiner_id' => $examiner1,
            'score' => 86,
            'result' => 'PASS',
            'status' => 'SUBMITTED',
        ]);
        SeminarEvaluation::create([
            'schedule_id' => $schedule->id,
            'examiner_id' => $examiner2,
            'score' => 86,
            'result' => 'PASS',
            'status' => $examinersSubmitted ? 'SUBMITTED' : 'PENDING',
        ]);

        return $group;
    }

    private function submitBimbinganSempro(Group $group): void
    {
        $component = AssessmentComponent::where('period_id', $group->period_id)
            ->where('type', 'BIMBINGAN_SEMPRO')
            ->firstOrFail();

        BimbinganSemproScore::create([
            'component_id' => $component->id,
            'evaluator_id' => $group->supervisor_1_id,
            'group_id' => $group->id,
            'student_id' => 1,
            'score' => 86,
        ]);
    }

    public function test_advances_sempro_done_to_pdc2_active_when_sempro_complete(): void
    {
        $group = $this->makeSemproGroup('SEMPRO_DONE');
        $this->submitBimbinganSempro($group);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('PDC2_ACTIVE', $advanced);
        $this->assertSame('PDC2_ACTIVE', $group->fresh()->status);
    }

    public function test_stays_sempro_done_when_bimbingan_pending(): void
    {
        $group = $this->makeSemproGroup('SEMPRO_DONE');

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('SEMPRO_DONE', $group->fresh()->status);
    }

    public function test_stays_sempro_done_when_examiner_pending(): void
    {
        $group = $this->makeSemproGroup('SEMPRO_DONE', examinersSubmitted: false);
        $this->submitBimbinganSempro($group);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('SEMPRO_DONE', $group->fresh()->status);
    }

    public function test_sempro_done_advance_is_idempotent(): void
    {
        $group = $this->makeSemproGroup('SEMPRO_DONE');
        $this->submitBimbinganSempro($group);

        $service = app(GroupLifecycleService::class);
        $this->assertSame('PDC2_ACTIVE', $service->advanceIfComplete($group));
        $this->assertNull($service->advanceIfComplete($group));
        $this->assertSame('PDC2_ACTIVE', $group->fresh()->status);
    }

    /**
     * Two-member EXPO fixture: COMPLETED expo schedule + REGISTERED
     * registration + one EXPO component. Members complete via
     * approveExpoDocs()/submitExpoSelfEvaluations().
     *
     * @return array{Group, int, int} [group, registrationId, componentId]
     */
    private function makeExpoGroup(bool $scheduleCompleted = true): array
    {
        $group = $this->makeGroup('EXPO_REGISTERED');
        GroupMember::create(['group_id' => $group->id, 'student_id' => 2]);

        // isExpoComplete reads EXPO components from the period table when
        // it exists; create it lazily here so the legacy component fallback
        // used by the older tests stays untouched.
        if (! Schema::hasTable('capstone_period_assessment_components')) {
            Schema::create('capstone_period_assessment_components', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('period_id');
                $t->unsignedBigInteger('template_id')->nullable();
                $t->string('type');
                $t->integer('sort_order')->default(0);
                $t->timestamps();
            });
        }

        $componentId = DB::table('capstone_period_assessment_components')->insertGetId([
            'period_id' => $group->period_id, 'type' => 'EXPO', 'sort_order' => 1,
        ]);

        $registrationId = DB::table('capstone_expo_registrations')->insertGetId([
            'group_id' => $group->id, 'status' => 'REGISTERED',
        ]);

        SeminarSchedule::create([
            'group_id' => $group->id,
            'type' => 'EXPO',
            'status' => $scheduleCompleted ? 'COMPLETED' : 'SCHEDULED',
        ]);

        return [$group, $registrationId, $componentId];
    }

    private function approveExpoDocs(Group $group, int $registrationId, string $status = 'APPROVED'): void
    {
        foreach ([1, 2] as $studentId) {
            DB::table('capstone_expo_student_documents')->insert([
                'expo_registration_id' => $registrationId,
                'group_id' => $group->id,
                'student_id' => $studentId,
                'file_path' => 'expo.pdf',
                'original_name' => 'expo.pdf',
                'status' => $status,
            ]);
        }
    }

    private function submitExpoSelfEvaluations(Group $group, int $registrationId, int $componentId, array $studentIds = [1, 2]): void
    {
        foreach ($studentIds as $studentId) {
            DB::table('capstone_expo_self_evaluations')->insert([
                'expo_registration_id' => $registrationId,
                'group_id' => $group->id,
                'student_id' => $studentId,
                'period_component_id' => $componentId,
                'score' => 85,
            ]);
        }
    }

    public function test_advances_expo_registered_to_expo_done_when_members_complete(): void
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup();
        $this->approveExpoDocs($group, $registrationId);
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('EXPO_DONE', $advanced);
        $this->assertSame('EXPO_DONE', $group->fresh()->status);
    }

    public function test_stays_expo_registered_when_doc_not_approved(): void
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup();
        $this->approveExpoDocs($group, $registrationId, 'SUBMITTED');
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('EXPO_REGISTERED', $group->fresh()->status);
    }

    public function test_stays_expo_registered_when_a_member_self_evaluation_pending(): void
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup();
        $this->approveExpoDocs($group, $registrationId);
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId, [1]);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('EXPO_REGISTERED', $group->fresh()->status);
    }

    /**
     * EXPO has no examiner evaluation: the gate is member state only, so
     * the group advances even when the EXPO schedule is still pending —
     * or when no schedule row exists at all.
     */
    public function test_advances_expo_registered_without_completed_schedule(): void
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup(false);
        $this->approveExpoDocs($group, $registrationId);
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('EXPO_DONE', $advanced);
        $this->assertSame('EXPO_DONE', $group->fresh()->status);

        // No schedule row at all: same outcome.
        [$group2, $registrationId2, $componentId2] = $this->makeExpoGroup(false);
        SeminarSchedule::where('group_id', $group2->id)->where('type', 'EXPO')->delete();
        $this->approveExpoDocs($group2, $registrationId2);
        $this->submitExpoSelfEvaluations($group2, $registrationId2, $componentId2);

        $this->assertSame('EXPO_DONE', app(GroupLifecycleService::class)->advanceIfComplete($group2));
        $this->assertSame('EXPO_DONE', $group2->fresh()->status);
    }

    public function test_expo_done_advance_is_idempotent(): void
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup();
        $this->approveExpoDocs($group, $registrationId);
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId);

        $service = app(GroupLifecycleService::class);
        $this->assertSame('EXPO_DONE', $service->advanceIfComplete($group));
        $this->assertNull($service->advanceIfComplete($group));
        $this->assertSame('EXPO_DONE', $group->fresh()->status);
    }

    /**
     * EXPO_DONE fixture: expo stage complete (approved docs + self-evals
     * for both members) plus the final TA readiness pieces — NILAI_DOSEN
     * and MILESTONE scores from supervisor 1 for both members, and peer
     * review completion rows. Lazily creates the students + peer tables
     * that getFinalReadyForTaIndividual reads.
     *
     * @return array{Group, int, int} [group, registrationId, expoComponentId]
     */
    private function makeFinalReadyGroup(array $completedPeerStudents = [1, 2], bool $withSupervisionScores = true, string $status = 'EXPO_DONE'): array
    {
        [$group, $registrationId, $componentId] = $this->makeExpoGroup();
        $group->update(['status' => $status]);

        $this->approveExpoDocs($group, $registrationId);
        $this->submitExpoSelfEvaluations($group, $registrationId, $componentId);

        if ($withSupervisionScores) {
            foreach (['NILAI_DOSEN', 'MILESTONE'] as $type) {
                DB::table('capstone_period_assessment_components')->insert([
                    'period_id' => $group->period_id, 'type' => $type, 'sort_order' => 1,
                ]);

                $table = $type === 'NILAI_DOSEN' ? 'capstone_nilai_dosen_scores' : 'capstone_milestone_scores';
                foreach ([1, 2] as $studentId) {
                    DB::table($table)->insert([
                        'evaluator_id' => $group->supervisor_1_id,
                        'group_id' => $group->id,
                        'student_id' => $studentId,
                        'score' => 85,
                    ]);
                }
            }
        }

        if (! Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('user_id')->nullable();
                $t->string('student_number')->nullable();
                $t->integer('cohort_year')->nullable();
                $t->timestamps();
            });
        }

        foreach ([1, 2] as $studentId) {
            DB::table('students')->updateOrInsert(
                ['id' => $studentId],
                ['student_number' => 'NIM'.$studentId]
            );
        }

        // getPeerReviewRequirementStatus reads the capstone_-prefixed
        // peer tables through their models.
        if (! Schema::hasTable('capstone_period_peer_review_indicators')) {
            Schema::create('capstone_period_peer_review_indicators', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('period_id');
            });
        }

        if (! Schema::hasTable('capstone_student_peer_review_status')) {
            Schema::create('capstone_student_peer_review_status', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('student_id');
                $t->unsignedBigInteger('group_id');
                $t->unsignedBigInteger('period_id')->nullable();
                $t->boolean('has_completed_peer_review')->default(false);
            });
        }

        DB::table('capstone_period_peer_review_indicators')->updateOrInsert(
            ['period_id' => $group->period_id],
            ['period_id' => $group->period_id]
        );

        foreach ($completedPeerStudents as $studentId) {
            DB::table('capstone_student_peer_review_status')->updateOrInsert(
                ['student_id' => $studentId, 'group_id' => $group->id],
                ['period_id' => $group->period_id, 'has_completed_peer_review' => true]
            );
        }

        return [$group->fresh(), $registrationId, $componentId];
    }

    public function test_advances_expo_done_to_pdc2_completed_when_final_ready(): void
    {
        [$group] = $this->makeFinalReadyGroup();

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('PDC2_COMPLETED', $advanced);
        $this->assertSame('PDC2_COMPLETED', $group->fresh()->status);
    }

    public function test_stays_expo_done_when_peer_review_pending(): void
    {
        [$group] = $this->makeFinalReadyGroup([1]);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('EXPO_DONE', $group->fresh()->status);
    }

    public function test_stays_expo_done_when_supervision_scores_pending(): void
    {
        [$group] = $this->makeFinalReadyGroup([1, 2], false);

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertNull($advanced);
        $this->assertSame('EXPO_DONE', $group->fresh()->status);
    }

    public function test_expo_done_to_pdc2_completed_is_idempotent(): void
    {
        [$group] = $this->makeFinalReadyGroup();

        $service = app(GroupLifecycleService::class);
        $this->assertSame('PDC2_COMPLETED', $service->advanceIfComplete($group));
        $this->assertNull($service->advanceIfComplete($group));
        $this->assertSame('PDC2_COMPLETED', $group->fresh()->status);
    }

    public function test_cascades_expo_registered_to_pdc2_completed_when_everything_complete(): void
    {
        [$group] = $this->makeFinalReadyGroup([1, 2], true, 'EXPO_REGISTERED');

        $advanced = app(GroupLifecycleService::class)->advanceIfComplete($group);

        $this->assertSame('PDC2_COMPLETED', $advanced);
        $this->assertSame('PDC2_COMPLETED', $group->fresh()->status);
    }
}
