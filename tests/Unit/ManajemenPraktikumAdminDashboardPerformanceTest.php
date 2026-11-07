<?php

namespace Tests\Unit;

use App\Services\UserPresence;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin\DashboardController;
use Tests\TestCase;

class ManajemenPraktikumAdminDashboardPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.manprak_dashboard_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
            ],
            'cache.default' => 'array',
        ]);

        DB::setDefaultConnection('manprak_dashboard_test');
        Cache::flush();
        $this->travelTo(now()->setDate(2026, 9, 17)->setTime(12, 0));

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('employee_number');
            $table->timestamps();
        });

        Schema::create('eo_prak_praktikum', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('eo_prak_praktikum_dosen', function (Blueprint $table) {
            $table->id();
            $table->string('praktikum_id');
            $table->unsignedBigInteger('dosen_id');
        });

        Schema::create('eo_prak_pendaftaran_asprak', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('status_koor');
        });

        Schema::create('eo_prak_pendaftaran_koordinator', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('status_dosen');
        });

        Schema::create('eo_prak_periode_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('praktikum_id');
            $table->string('jenis');
            $table->boolean('is_aktif');
            $table->timestamp('dibuka_pada')->nullable();
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        DB::purge('manprak_dashboard_test');
        parent::tearDown();
    }

    public function test_dashboard_query_count_does_not_grow_with_lecturers(): void
    {
        $this->seedDashboard(20);

        DB::enableQueryLog();
        DB::flushQueryLog();

        $data = (new DashboardController)->index(
            Request::create('/eoffice/manajemen-praktikum/admin/dashboard?per_page=20')
        )->getData();

        $queries = collect(DB::getQueryLog())->pluck('query');
        DB::disableQueryLog();

        $this->assertCount(7, $queries);
        $this->assertSame(20, $data['dosenPaginator']->count());
        $this->assertSame(1, $data['dosenTerbaru']->first()['jumlah_praktikum']);
        $this->assertSame(1, $data['totalPraktikumAktif']);
        $this->assertSame(20, $data['totalDosen']);
        $this->assertSame(2, $data['pendingTindakan']);
        $this->assertCount(
            1,
            $queries->filter(fn (string $sql) => str_contains($sql, 'eo_prak_praktikum_dosen'))
        );
    }

    public function test_presence_updates_are_throttled(): void
    {
        DB::table('users')->insert([
            'id' => 99,
            'name' => 'Presence Test',
            'is_online' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $presence = new UserPresence;

        $this->assertTrue($presence->touch(99));
        $firstSeenAt = DB::table('users')->where('id', 99)->value('last_seen_at');

        $this->travel(30)->seconds();
        $this->assertFalse($presence->touch(99));
        $this->assertSame($firstSeenAt, DB::table('users')->where('id', 99)->value('last_seen_at'));

        $this->travel(91)->seconds();
        $this->assertTrue($presence->touch(99));
        $this->assertNotSame($firstSeenAt, DB::table('users')->where('id', 99)->value('last_seen_at'));
    }

    private function seedDashboard(int $lecturerCount): void
    {
        DB::table('eo_prak_praktikum')->insert([
            ['id' => 'active', 'nama' => 'Praktikum Aktif', 'status' => 'aktif', 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 'deleted', 'nama' => 'Praktikum Terhapus', 'status' => 'aktif', 'deleted_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        for ($id = 1; $id <= $lecturerCount; $id++) {
            DB::table('users')->insert([
                'id' => $id,
                'name' => "Dosen {$id}",
                'email' => "dosen{$id}@example.test",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('lecturers')->insert([
                'id' => $id,
                'user_id' => $id,
                'employee_number' => "NIP{$id}",
                'created_at' => now()->subSeconds($id),
                'updated_at' => now(),
            ]);
        }

        DB::table('eo_prak_praktikum_dosen')->insert([
            ['praktikum_id' => 'active', 'dosen_id' => 1],
            ['praktikum_id' => 'deleted', 'dosen_id' => 1],
        ]);

        DB::table('eo_prak_pendaftaran_asprak')->insert([
            'status' => 'pending',
            'status_koor' => 'disetujui',
        ]);
        DB::table('eo_prak_pendaftaran_koordinator')->insert([
            'status' => 'pending',
            'status_dosen' => 'disetujui',
        ]);
        DB::table('eo_prak_periode_pendaftaran')->insert([
            'praktikum_id' => 'active',
            'jenis' => 'asprak',
            'is_aktif' => true,
            'dibuka_pada' => now()->subDay(),
            'ditutup_pada' => now()->addDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
