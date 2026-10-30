<?php

namespace Tests\Feature;

use App\Models\User;
use Mockery;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\CbtSessionService;
use Tests\TestCase;

/**
 * Regresi T1: dua request paralel (dobel klik / dua tab) dulu bisa membuat
 * dua sesi ujian "ongoing" untuk satu mahasiswa. startSession harus idempoten.
 *
 * Catatan: test ini menjalankan subset migrasi yang dibutuhkan saja —
 * migrasi modul lain saat ini tidak kompatibel dengan SQLite in-memory.
 */
class KompreSessionUniquenessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigration('database/migrations/2026_02_15_111353_create_users_table.php');
        $this->runMigration('database/migrations/2026_02_18_104631_add_auth_columns_to_users_table.php');
        $this->runMigration('database/migrations/2026_02_20_085615_add_password_to_users_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_092214_create_bs_periode_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_092217_create_bs_jadwal_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_102724_add_tanggal_ujian_to_bs_jadwal_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032833_create_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_04_23_225401_add_jadwal_id_to_bs_kompre_session_table.php');
    }

    public function test_start_session_hanya_membuat_satu_sesi_ongoing_per_user(): void
    {
        $user = User::factory()->create();

        $periode = PeriodeUjian::create([
            'nama_periode' => 'Periode Test',
            'slug' => 'periode-test-'.uniqid(),
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->addMonth(),
            'status' => 'aktif',
        ]);

        $jadwal = JadwalUjian::create([
            'periode_ujian_id' => $periode->id,
            'nama_sesi' => 'Sesi 1',
            'tanggal_ujian' => now()->toDateString(),
            'kuota' => 10,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
        ]);

        $service = Mockery::mock(CbtSessionService::class)->makePartial();
        $service->shouldReceive('generateSoal')->once()->andReturnNull();

        $first = $service->startSession($jadwal, $user->id);
        $second = $service->startSession($jadwal, $user->id);

        $this->assertSame($first->id, $second->id, 'startSession harus mengembalikan sesi yang sama, bukan membuat sesi baru.');
        $this->assertSame(1, KompreSession::where('user_id', $user->id)->count());
        $this->assertSame(KompreSessionStatus::Ongoing, $first->status);
    }

    public function test_start_session_mengembalikan_sesi_ongoing_yang_sudah_ada(): void
    {
        $user = User::factory()->create();

        $session = KompreSession::create([
            'user_id' => $user->id,
            'title' => 'Ujian Komprehensif Sesi Lama',
            'started_at' => now()->subMinutes(10),
            'status' => KompreSessionStatus::Ongoing,
        ]);

        $jadwal = new JadwalUjian(['nama_sesi' => 'Sesi Baru']);
        $jadwal->id = 999;

        $service = Mockery::mock(CbtSessionService::class)->makePartial();
        $service->shouldNotReceive('generateSoal');

        $result = $service->startSession($jadwal, $user->id);

        $this->assertSame($session->id, $result->id);
        $this->assertSame(1, KompreSession::where('user_id', $user->id)->count());
    }

    private function runMigration(string $relativePath): void
    {
        $migration = require base_path($relativePath);
        $migration->up();
    }
}
