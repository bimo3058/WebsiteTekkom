<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\BankSoal\Http\Controllers\BS\DashboardController;
use Tests\TestCase;

class BankSoalReadPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.banksoal_read_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('banksoal_read_test');
        foreach ([
            'bs_mata_kuliah' => ['kode', 'nama'],
            'bs_dosen_pengampu_mk' => ['mk_id', 'user_id'],
            'bs_pertanyaan' => ['mk_id', 'status', 'cpl_id'],
            'bs_rps_detail' => ['mk_id'],
            'bs_cpl' => ['kode'],
        ] as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->string($column)->nullable();
                }
            });
        }
        $user = \Mockery::mock(User::class)->makePartial();
        $user->setRawAttributes(['id' => 7]);
        $user->setRelation('roles', collect([new Role(['name' => 'dosen'])]));
        $user->shouldReceive('can')->with('banksoal.view')->andReturn(true);
        $this->actingAs($user);
    }

    protected function tearDown(): void
    {
        DB::purge('banksoal_read_test');
        parent::tearDown();
    }

    public function test_lecturer_dashboard_has_constant_query_count_and_scoped_totals(): void
    {
        DB::table('bs_cpl')->insert(['id' => 1, 'kode' => 'CPL1']);
        foreach ([2, 20] as $size) {
            foreach (['bs_mata_kuliah', 'bs_dosen_pengampu_mk', 'bs_pertanyaan', 'bs_rps_detail'] as $table) {
                DB::table($table)->delete();
            }
            for ($i = 1; $i <= $size; $i++) {
                DB::table('bs_mata_kuliah')->insert(['id' => $i, 'kode' => 'MK'.$i, 'nama' => 'Matkul '.$i]);
                DB::table('bs_dosen_pengampu_mk')->insert(['mk_id' => $i, 'user_id' => 7]);
                DB::table('bs_pertanyaan')->insert(['mk_id' => $i, 'status' => 'disetujui', 'cpl_id' => 1]);
            }
            DB::table('bs_pertanyaan')->insert([
                ['mk_id' => 1, 'status' => 'draft', 'cpl_id' => 1],
                ['mk_id' => 1, 'status' => 'revisi', 'cpl_id' => 1],
                ['mk_id' => 999, 'status' => 'disetujui', 'cpl_id' => 1],
            ]);
            DB::table('bs_rps_detail')->insert([['mk_id' => 1], ['mk_id' => 1]]);
            DB::enableQueryLog();
            DB::flushQueryLog();
            $data = (new DashboardController)->index()->getData();
            $this->assertCount(4, DB::getQueryLog());
            DB::disableQueryLog();
            $this->assertSame($size + 2, $data['totalSoal']);
            $this->assertSame($size, $data['approved']);
            $this->assertSame(1, $data['revisi']);
            $this->assertSame(0, $data['perluReview']);
            $this->assertCount($size - 1, $data['mkTanpaRps']);
            $this->assertSame(3, $data['mkDist'][0]['count']);
        }
    }

    public function test_lecturer_without_courses_has_zero_totals(): void
    {
        $data = (new DashboardController)->index()->getData();
        $this->assertSame(0, $data['totalSoal']);
        $this->assertSame(0, $data['approved']);
        $this->assertSame([], $data['mkTanpaRps']);
    }

    public function test_dashboard_indexes_can_be_created_and_rolled_back(): void
    {
        $migration = require base_path('Modules/BankSoal/database/migrations/2026_09_08_100001_add_dashboard_read_indexes.php');
        $migration->up();
        $this->assertTrue(Schema::hasIndex('bs_pertanyaan', ['mk_id', 'status']));
        $this->assertTrue(Schema::hasIndex('bs_rps_detail', ['mk_id']));
        $migration->down();
        $this->assertFalse(Schema::hasIndex('bs_pertanyaan', 'bs_question_course_status_idx'));
    }
}
