<?php

namespace Modules\EOffice\Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator\DashboardController;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Services\KoorPraktikumService;
use Modules\EOffice\Services\PraktikanGroupingService;
use Tests\TestCase;

class PraktikanGroupingWorkflowTest extends TestCase
{
    private const PRAKTIKUM_ID = '0199f001-0000-7000-8000-000000000001';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.connections.grouping_test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'cache.default' => 'array',
            'session.driver' => 'array',
        ]);
        DB::setDefaultConnection('grouping_test');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('eo_praktikum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->unsignedBigInteger('koor_id');
            $table->string('status')->default('aktif');
            $table->unsignedInteger('jumlah_kelompok')->nullable();
            $table->unsignedInteger('jumlah_shift')->nullable();
            $table->string('metode_pembagian')->nullable();
            $table->json('draft_pembagian')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('daftar_praktikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('terdaftar');
            $table->string('kelompok')->nullable();
            $table->string('shift')->nullable();
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Koordinator', 'email' => 'koor@example.test'],
            ['id' => 2, 'name' => 'Praktikan Satu', 'email' => 'p1@example.test'],
            ['id' => 3, 'name' => 'Praktikan Dua', 'email' => 'p2@example.test'],
        ]);
        DB::table('eo_praktikum')->insert([
            'id' => self::PRAKTIKUM_ID,
            'nama' => 'Praktikum Test',
            'koor_id' => 1,
            'status' => 'aktif',
        ]);
        DB::table('daftar_praktikan')->insert([
            ['id' => '0199f002-0000-7000-8000-000000000001', 'praktikum_id' => self::PRAKTIKUM_ID, 'user_id' => 2],
            ['id' => '0199f002-0000-7000-8000-000000000002', 'praktikum_id' => self::PRAKTIKUM_ID, 'user_id' => 3],
        ]);

        $this->actingAs(User::findOrFail(1));
    }

    public function test_automatic_grouping_stays_as_draft_until_applied(): void
    {
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->updatePlotSettings(Request::create('/settings', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 2,
            'method' => 'urutan_sistem',
        ]));

        $this->assertSame(0, DB::table('daftar_praktikan')->whereNotNull('kelompok')->count());
        $this->assertNotNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);

        $controller->applyPlot(Request::create('/apply', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
        ]));

        $this->assertSame(['1', '2'], DB::table('daftar_praktikan')->orderBy('user_id')->pluck('kelompok')->all());
        $this->assertSame(['1', '2'], DB::table('daftar_praktikan')->orderBy('user_id')->pluck('shift')->all());
        $this->assertNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);
    }

    public function test_discarding_draft_keeps_active_grouping_unchanged(): void
    {
        DB::table('daftar_praktikan')->where('user_id', 2)->update(['kelompok' => 'Lama', 'shift' => 'Pagi']);
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->updatePlotSettings(Request::create('/settings', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 1,
            'method' => 'manual',
        ]));
        $controller->discardPlotDraft(Request::create('/discard', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
        ]));

        $praktikan = DB::table('daftar_praktikan')->where('user_id', 2)->first();
        $this->assertSame('Lama', $praktikan->kelompok);
        $this->assertSame('Pagi', $praktikan->shift);
        $this->assertNull(Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian);
    }

    public function test_editing_an_active_group_automatically_creates_a_new_draft(): void
    {
        DB::table('eo_praktikum')->where('id', self::PRAKTIKUM_ID)->update([
            'jumlah_kelompok' => 2,
            'jumlah_shift' => 2,
            'metode_pembagian' => 'manual',
        ]);
        DB::table('daftar_praktikan')->where('user_id', 2)->update(['kelompok' => '1', 'shift' => '1']);
        DB::table('daftar_praktikan')->where('user_id', 3)->update(['kelompok' => '2', 'shift' => '2']);
        $controller = new DashboardController(
            $this->createMock(KoorPraktikumService::class),
            new PraktikanGroupingService,
        );

        $controller->saveGroupMembers(Request::create('/members', 'POST', [
            'praktikum_id' => self::PRAKTIKUM_ID,
            'kelompok' => '1',
            'shift' => '1',
            'members' => [
                '0199f002-0000-7000-8000-000000000001',
                '0199f002-0000-7000-8000-000000000002',
            ],
        ]));

        $this->assertSame(['1', '2'], DB::table('daftar_praktikan')->orderBy('user_id')->pluck('kelompok')->all());
        $draft = Praktikum::findOrFail(self::PRAKTIKUM_ID)->draft_pembagian;
        $this->assertSame('1', $draft['assignments']['0199f002-0000-7000-8000-000000000001']);
        $this->assertSame('1', $draft['assignments']['0199f002-0000-7000-8000-000000000002']);
    }
}
