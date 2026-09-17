<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\SupabaseStorage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DaftarAsprakController;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa\DashboardController;
use Tests\TestCase;

class EOfficeReadPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.eoffice_read_test' => ['driver' => 'sqlite', 'database' => ':memory:']]);
        DB::setDefaultConnection('eoffice_read_test');
        $this->travelTo(now()->setDate(2026, 9, 8)->startOfDay());

        // Isolated fixtures: never migrate or connect to the configured Supabase database.
        $tables = [
            'eo_praktikum' => ['nama', 'status', 'is_active', 'matkul_id', 'koor_id', 'deleted_at'],
            'eo_matkul_praktikum' => ['nama', 'kode'],
            'manprak_periode_pendaftaran' => ['praktikum_id', 'jenis', 'is_aktif', 'dibuka_pada', 'ditutup_pada', 'created_at'],
            'pendaftaran_asprak' => ['user_id', 'praktikum_id', 'status', 'created_at'],
            'pendaftaran_koordinator' => ['user_id', 'praktikum_id', 'status', 'status_dosen', 'created_at'],
            'asprak_praktikum' => ['user_id', 'praktikum_id', 'role', 'deleted_at'],
            'daftar_praktikan' => ['user_id', 'praktikum_id'],
            'eo_praktikum_dosen' => ['praktikum_id', 'dosen_id'],
            'users' => ['name', 'deleted_at'],
            'modul_praktikum' => ['praktikum_id'],
            'tugas_praktikum' => ['modul_id', 'is_published', 'deadline'],
            'pengumpulan_tugas' => ['tugas_id', 'daftar_praktikan_id', 'status_pengumpulan'],
            'nilai_praktikum' => ['daftar_praktikan_id', 'dipublikasikan'],
            'pengumuman_praktikum' => ['praktikum_id', 'tipe_sistem', 'is_published', 'created_at', 'deleted_at'],
            'absensi_praktikum' => ['daftar_praktikan_id', 'status'],
        ];
        foreach ($tables as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->string('id')->primary();
                foreach ($columns as $column) {
                    if (in_array($column, ['is_active', 'is_aktif', 'is_published', 'dipublikasikan'], true)) {
                        $table->boolean($column)->default(false);
                    } else {
                        $table->string($column)->nullable();
                    }
                }
            });
        }
        $user = \Mockery::mock(User::class)->makePartial();
        $user->setRawAttributes(['id' => 7]);
        $user->shouldReceive('hasRole')->andReturn(false);
        $this->actingAs($user);
    }

    protected function tearDown(): void
    {
        DB::purge('eoffice_read_test');
        parent::tearDown();
    }

    public function test_registration_queries_stay_constant_and_preserve_selected_student_status(): void
    {
        $controller = new DaftarAsprakController(\Mockery::mock(SupabaseStorage::class));
        foreach ([2, 20] as $size) {
            DB::table('eo_praktikum')->delete();
            DB::table('manprak_periode_pendaftaran')->delete();
            for ($i = 1; $i <= $size; $i++) {
                $this->seedPraktikum($i);
            }
            DB::table('manprak_periode_pendaftaran')->insert([
                ['id' => 'old', 'praktikum_id' => 'p1', 'jenis' => 'asprak', 'is_aktif' => true, 'dibuka_pada' => null, 'ditutup_pada' => null, 'created_at' => '2026-01-01'],
                ['id' => 'future', 'praktikum_id' => 'p1', 'jenis' => 'koor', 'is_aktif' => true, 'dibuka_pada' => '2026-10-01', 'ditutup_pada' => null, 'created_at' => '2026-09-07'],
                ['id' => 'expired', 'praktikum_id' => 'p1', 'jenis' => 'koor', 'is_aktif' => true, 'dibuka_pada' => null, 'ditutup_pada' => '2026-09-08 00:00:00', 'created_at' => '2026-09-07'],
            ]);

            DB::enableQueryLog();
            DB::flushQueryLog();
            $data = $controller->index(Request::create('/daftar-asprak?praktikum_id=p1'))->getData();
            $this->assertCount(7, DB::getQueryLog());
            DB::disableQueryLog();
            $this->assertCount($size, $data['praktikumDenganPeriode']);
            $this->assertSame(1, $data['periodeAktif']['p1']['asprak']->id);
            $this->assertNull($data['periodeAktif']['p1']['koor']);
        }

        DB::table('pendaftaran_asprak')->insert([
            ['id' => '1', 'user_id' => 7, 'praktikum_id' => 'p1', 'status' => 'rejected', 'created_at' => '2026-08-01'],
            ['id' => '2', 'user_id' => 7, 'praktikum_id' => 'p1', 'status' => 'pending', 'created_at' => '2026-09-01'],
            ['id' => '3', 'user_id' => 8, 'praktikum_id' => 'p1', 'status' => 'approved', 'created_at' => '2026-09-07'],
        ]);
        DB::table('asprak_praktikum')->insert([
            ['id' => '1', 'user_id' => 7, 'praktikum_id' => 'p1', 'role' => 'koor', 'deleted_at' => null],
            ['id' => '2', 'user_id' => 7, 'praktikum_id' => 'p1', 'role' => 'asprak', 'deleted_at' => '2026-09-01'],
        ]);
        $data = $controller->index(Request::create('/daftar-asprak?praktikum_id=p1'))->getData();
        $this->assertSame('pending', $data['existingAsprak']->status);
        $this->assertTrue($data['isKoorDiPraktikumIni']);
        $this->assertFalse($data['isAsprakDiPraktikumIni']);

        DB::enableQueryLog();
        DB::flushQueryLog();
        $data = $controller->index(Request::create('/daftar-asprak?praktikum_id=unavailable'))->getData();
        $this->assertNull($data['selectedPraktikum']);
        $this->assertNull($data['existingAsprak']);
        $this->assertCount(7, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_empty_registration_page_does_not_repeat_fallback_queries(): void
    {
        DB::enableQueryLog();
        $controller = new DaftarAsprakController(\Mockery::mock(SupabaseStorage::class));
        $data = $controller->index(Request::create('/daftar-asprak'))->getData();
        $this->assertTrue($data['praktikumDenganPeriode']->isEmpty());
        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_dashboard_batches_submissions_across_enrollments_for_current_student(): void
    {
        $this->seedPraktikum(1);
        DB::table('daftar_praktikan')->insert(['id' => 'dp1', 'user_id' => 7, 'praktikum_id' => 'p1']);
        DB::table('modul_praktikum')->insert(['id' => '1', 'praktikum_id' => 'p1']);
        for ($i = 1; $i <= 8; $i++) {
            DB::table('tugas_praktikum')->insert(['id' => $i, 'modul_id' => 1, 'is_published' => true, 'deadline' => sprintf('2026-09-%02d', 8 + $i)]);
        }
        DB::table('pengumpulan_tugas')->insert([
            ['id' => 1, 'tugas_id' => 1, 'daftar_praktikan_id' => 'dp1', 'status_pengumpulan' => 'acc'],
            ['id' => 2, 'tugas_id' => 2, 'daftar_praktikan_id' => 'other', 'status_pengumpulan' => 'acc'],
        ]);
        DB::table('absensi_praktikum')->insert([
            ['id' => 1, 'daftar_praktikan_id' => 'dp1', 'status' => 'hadir'],
            ['id' => 2, 'daftar_praktikan_id' => 'dp1', 'status' => 'izin'],
            ['id' => 3, 'daftar_praktikan_id' => 'other', 'status' => 'hadir'],
        ]);
        DB::enableQueryLog();
        $data = (new DashboardController)->index(Request::create('/dashboard'))->getData();
        $queries = collect(DB::getQueryLog())->pluck('query');
        DB::disableQueryLog();
        $this->assertCount(7, $data['tugasMendatang']);
        $this->assertCount(5, $data['tugasPaginator']->items());
        $this->assertFalse($data['tugasMendatang'][0]->sudah_kumpul);
        $this->assertSame(2, $data['tugasMendatang'][0]->id);
        $this->assertCount(1, $queries->filter(fn ($sql) => str_contains($sql, 'from "pengumpulan_tugas"')));

        $this->seedPraktikum(2);
        DB::table('daftar_praktikan')->insert(['id' => 'dp2', 'user_id' => 7, 'praktikum_id' => 'p2']);
        DB::table('modul_praktikum')->insert(['id' => 2, 'praktikum_id' => 'p2']);
        DB::table('tugas_praktikum')->insert([
            ['id' => 9, 'modul_id' => 2, 'is_published' => true, 'deadline' => '2026-09-07'],
            ['id' => 10, 'modul_id' => 2, 'is_published' => false, 'deadline' => '2026-09-07'],
        ]);
        $data = (new DashboardController)->index(Request::create('/dashboard?per_page=0'))->getData();
        $this->assertCount(7, $data['tugasMendatang']);
        $this->assertCount(1, $data['tugasTerlambat']);
        $this->assertSame(9, $data['tugasTerlambat'][0]->id);
        $this->assertSame(8, $data['tugasPaginator']->total());
        $this->assertSame(1, $data['tugasPaginator']->perPage());
    }

    public function test_praktikum_indexes_can_be_created_and_rolled_back(): void
    {
        $migration = require base_path('Modules/EOffice/database/migrations/2026_09_08_100000_add_praktikum_read_indexes.php');
        $migration->up();
        $this->assertTrue(Schema::hasIndex('manprak_periode_pendaftaran', ['praktikum_id', 'is_aktif', 'jenis', 'created_at']));
        $this->assertTrue(Schema::hasIndex('pengumpulan_tugas', ['daftar_praktikan_id', 'tugas_id']));
        $migration->down();
        $this->assertFalse(Schema::hasIndex('manprak_periode_pendaftaran', 'eo_periode_active_lookup_idx'));
    }

    private function seedPraktikum(int $id): void
    {
        DB::table('eo_matkul_praktikum')->insertOrIgnore(['id' => $id, 'nama' => 'Matkul '.$id]);
        DB::table('eo_praktikum')->insert(['id' => 'p'.$id, 'nama' => 'Praktikum '.$id, 'status' => 'aktif', 'matkul_id' => $id]);
        DB::table('manprak_periode_pendaftaran')->insert([
            'id' => $id, 'praktikum_id' => 'p'.$id, 'jenis' => 'asprak', 'is_aktif' => true, 'created_at' => '2026-09-01',
        ]);
    }
}
