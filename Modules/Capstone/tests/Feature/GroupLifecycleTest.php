<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\MilestoneScore;
use Modules\Capstone\Models\NilaiDosenScore;
use Modules\Capstone\Models\Period;
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
            $t->string('status')->default('SCHEDULED');
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
}
