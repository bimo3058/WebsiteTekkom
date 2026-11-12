<?php

namespace Modules\Capstone\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Http\Controllers\GradeConfigurationController;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Services\GradeCalculationService;
use Tests\TestCase;

/**
 * Phase weights (Batch E): the engine owns the single weighted math and
 * the grade endpoints delegate to it. Unconfigured periods grade
 * strictly identically to the former controller math (same avgs, same
 * weights, same rounding) — pinned here with ===, no tolerance.
 * Configured periods combine by their phase weights.
 * Isolated real SQL: never migrate the application's configured database.
 */
class PhaseWeightsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.phase_weights_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::setDefaultConnection('phase_weights_test');

        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name')->nullable();
            $t->timestamps();
        });
        Schema::create('capstone_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->nullable();
            $t->boolean('is_active')->default(true);
            $t->boolean('is_finalized')->default(false);
            $t->text('grade_configuration')->nullable();
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
        foreach (['capstone_sempro_scores', 'capstone_bimbingan_sempro_scores', 'capstone_nilai_dosen_scores', 'capstone_milestone_scores', 'capstone_bimbingan_ta_scores', 'capstone_sidang_ta_scores', 'capstone_expo_scores'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('component_id')->nullable();
                $t->unsignedBigInteger('evaluator_id')->nullable();
                $t->unsignedBigInteger('examiner_id')->nullable();
                $t->unsignedBigInteger('group_id');
                $t->unsignedBigInteger('student_id')->nullable();
                $t->decimal('score', 5, 2)->nullable();
                $t->text('notes')->nullable();
                $t->string('evaluation_type')->nullable();
                $t->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        DB::purge('phase_weights_test');
        parent::tearDown();
    }

    /** @return array{period:int, group:int} */
    private function seedGroup(): array
    {
        $periodId = DB::table('capstone_periods')->insertGetId(['name' => 'P1']);
        $groupId = DB::table('capstone_groups')->insertGetId(['period_id' => $periodId, 'code' => 'G1', 'status' => 'PDC2_ACTIVE']);

        return ['period' => $periodId, 'group' => $groupId];
    }

    private function seedScore(string $table, int $groupId, float $score, ?int $studentId = null): void
    {
        DB::table($table)->insert(['group_id' => $groupId, 'student_id' => $studentId, 'score' => $score]);
    }

    private function engine(): GradeCalculationService
    {
        return app(GradeCalculationService::class);
    }

    public function test_unconfigured_pdc1_is_strictly_identical_to_formula(): void
    {
        $seed = $this->seedGroup();
        $this->seedScore('capstone_sempro_scores', $seed['group'], 80);
        $this->seedScore('capstone_sempro_scores', $seed['group'], 90);
        $this->seedScore('capstone_bimbingan_sempro_scores', $seed['group'], 70);

        $result = $this->engine()->weightedGroupPhaseGrade(
            Group::find($seed['group']), 'pdc1', ['SEMPRO' => 50, 'BIMBINGAN_SEMPRO' => 50]
        );

        $this->assertSame('evaluated', $result['grades']['SEMPRO']['status']);
        $this->assertSame(85.0, $result['grades']['SEMPRO']['average_score']);
        $this->assertSame(42.5, $result['grades']['SEMPRO']['weighted_score']);
        $this->assertSame(70.0, $result['grades']['BIMBINGAN_SEMPRO']['average_score']);
        $this->assertSame(35.0, $result['grades']['BIMBINGAN_SEMPRO']['weighted_score']);
        $this->assertSame(77.5, $result['final']);
        $this->assertSame(100, $result['total_weight']);
    }

    public function test_pdc1_endpoint_envelope_unchanged(): void
    {
        $seed = $this->seedGroup();
        $this->seedScore('capstone_sempro_scores', $seed['group'], 80);
        $this->seedScore('capstone_bimbingan_sempro_scores', $seed['group'], 70);

        $response = app(GradeConfigurationController::class)->calculatePDC1Grade(new Request, $seed['group']);

        $data = $response->getData(true)['data'];
        $this->assertSame($seed['group'], $data['group_id']);
        $this->assertSame('G1', $data['group_name']);
        $this->assertSame($seed['period'], $data['period_id']);
        // Endpoint layer: JSON drops .0 on whole floats, so values assert
        // loosely here; strict math identity is pinned engine-direct above.
        $this->assertEquals(80.0, $data['pdc1_grades']['SEMPRO']['average_score']);
        $this->assertEquals(40.0, $data['pdc1_grades']['SEMPRO']['weighted_score']);
        $this->assertEquals(35.0, $data['pdc1_grades']['BIMBINGAN_SEMPRO']['weighted_score']);
        $this->assertEquals(75.0, $data['final_pdc1_score']);
        $this->assertSame(100, $data['total_weight']);
    }

    public function test_configured_weights_change_final(): void
    {
        $seed = $this->seedGroup();
        Period::find($seed['period'])->update(['grade_configuration' => [
            'pdc2' => ['NILAI_DOSEN' => 70, 'MILESTONE' => 30, 'EXPO' => 25, 'PEER_REVIEW' => 25],
        ]]);
        $this->seedScore('capstone_nilai_dosen_scores', $seed['group'], 80);
        $this->seedScore('capstone_milestone_scores', $seed['group'], 60);

        $result = $this->engine()->weightedGroupPhaseGrade(
            Group::find($seed['group']), 'pdc2',
            ['NILAI_DOSEN' => 25, 'MILESTONE' => 25, 'EXPO' => 25, 'PEER_REVIEW' => 25]
        );

        $this->assertSame(56.0, $result['grades']['NILAI_DOSEN']['weighted_score']);
        $this->assertSame(18.0, $result['grades']['MILESTONE']['weighted_score']);
        $this->assertSame('not_evaluated', $result['grades']['EXPO']['status']);
        $this->assertSame('not_evaluated', $result['grades']['PEER_REVIEW']['status']);
        $this->assertSame(74.0, $result['final']);
        $this->assertSame(100, $result['total_weight']);
    }

    public function test_default_pdc2_weights_without_config(): void
    {
        $seed = $this->seedGroup();
        $this->seedScore('capstone_nilai_dosen_scores', $seed['group'], 80);
        $this->seedScore('capstone_milestone_scores', $seed['group'], 60);

        $result = $this->engine()->weightedGroupPhaseGrade(
            Group::find($seed['group']), 'pdc2',
            ['NILAI_DOSEN' => 25, 'MILESTONE' => 25, 'EXPO' => 25, 'PEER_REVIEW' => 25]
        );

        $this->assertSame(20.0, $result['grades']['NILAI_DOSEN']['weighted_score']);
        $this->assertSame(15.0, $result['grades']['MILESTONE']['weighted_score']);
        $this->assertSame(35.0, $result['final']);
        $this->assertSame(50, $result['total_weight']);
    }

    public function test_ta_grade_delegates_to_engine(): void
    {
        $seed = $this->seedGroup();
        $studentId = DB::table('students')->insertGetId(['name' => 'Mhs']);
        DB::table('capstone_group_members')->insert([
            'group_id' => $seed['group'], 'student_id' => $studentId, 'is_leader' => true, 'period_id' => $seed['period'],
        ]);
        $this->seedScore('capstone_bimbingan_ta_scores', $seed['group'], 80, $studentId);
        $this->seedScore('capstone_sidang_ta_scores', $seed['group'], 90, $studentId);

        $response = app(GradeConfigurationController::class)->calculateTAGrade(new Request, $studentId);

        $data = $response->getData(true)['data'];
        $this->assertSame($studentId, $data['student_id']);
        $this->assertSame($seed['group'], $data['group_id']);
        $this->assertEquals(40.0, $data['ta_grades']['BIMBINGAN_TA']['weighted_score']);
        $this->assertEquals(45.0, $data['ta_grades']['SIDANG_TA']['weighted_score']);
        $this->assertEquals(85.0, $data['final_ta_score']);
        $this->assertSame(100, $data['total_weight']);
    }

    public function test_missing_types_are_reported_never_zero_filled(): void
    {
        $seed = $this->seedGroup();

        $result = $this->engine()->weightedGroupPhaseGrade(
            Group::find($seed['group']), 'pdc1', ['SEMPRO' => 50, 'BIMBINGAN_SEMPRO' => 50]
        );

        $this->assertSame('not_evaluated', $result['grades']['SEMPRO']['status']);
        $this->assertNull($result['grades']['SEMPRO']['average_score']);
        $this->assertNull($result['final']);
        $this->assertSame(0, $result['total_weight']);
    }
}
