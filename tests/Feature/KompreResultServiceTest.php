<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Jawaban;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Services\CbtSessionService;
use Modules\BankSoal\Services\Komprehensif\PendaftaranService;
use Tests\TestCase;

class KompreResultServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013401_create_bs_cpl_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013516_create_bs_pertanyaan_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013547_create_bs_jawaban_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032833_create_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032857_create_bs_kompre_jawaban_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_11_10_000001_add_result_columns_to_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_11_11_000004_add_passing_rule_to_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_05_15_000005_create_bs_pengaturan_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_11_11_000003_add_kompre_passing_rule_settings.php');
    }

    public function test_finish_session_lulus_jika_semua_cpl_minimal_enam(): void
    {
        $session = $this->makeSession([6, 6]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame(60.0, (float) $session->score);
        $this->assertTrue($session->lulus);
        $this->assertCount(2, $session->cpl_scores);
        $this->assertSame([6.0, 6.0], array_map('floatval', array_column($session->cpl_scores, 'nilai')));
    }

    public function test_finish_session_tidak_lulus_walau_total_tinggi_jika_ada_cpl_di_bawah_enam(): void
    {
        $session = $this->makeSession([10, 5]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame(75.0, (float) $session->score);
        $this->assertFalse($session->lulus);
        $this->assertSame([10.0, 5.0], array_map('floatval', array_column($session->cpl_scores, 'nilai')));
        $this->assertFalse($session->cpl_scores[1]['lulus']);
    }

    public function test_finish_session_mode_cpl_menyimpan_snapshot_aturan(): void
    {
        $session = $this->makeSession([6, 6]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame('cpl_all_min', $session->passing_rule);
        $this->assertTrue($session->lulus);
    }

    public function test_finish_session_mode_total_lulus_meski_ada_cpl_di_bawah_enam(): void
    {
        DB::table('bs_pengaturan')->where('kunci', 'kompre_passing_rule')
            ->update(['nilai' => 'total_min']);

        $session = $this->makeSession([10, 5]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame(75.0, (float) $session->score);
        $this->assertTrue($session->lulus);
        $this->assertSame('total_min', $session->passing_rule);
        // Flag lulus per CPL tetap berdasarkan ambang 6, tetapi hanya informasi.
        $this->assertFalse($session->cpl_scores[1]['lulus']);
    }

    public function test_finish_session_snapshot_aturan_sesi_tidak_berubah_oleh_setting_baru(): void
    {
        $session = $this->makeSession([10, 5]);
        $session->update(['passing_rule' => 'cpl_all_min']);

        // Admin mengubah aturan menjadi total_min sebelum sesi diselesaikan.
        DB::table('bs_pengaturan')->where('kunci', 'kompre_passing_rule')
            ->update(['nilai' => 'total_min']);

        $this->finish($session);

        $session->refresh();

        // Sesi tetap dievaluasi dengan aturan saat ujian dimulai.
        $this->assertSame('cpl_all_min', $session->passing_rule);
        $this->assertFalse($session->lulus);
    }

    public function test_finish_session_mode_total_tidak_lulus_jika_total_di_bawah_ambang(): void
    {
        DB::table('bs_pengaturan')->where('kunci', 'kompre_passing_rule')
            ->update(['nilai' => 'total_min']);

        $session = $this->makeSession([5, 5]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame(50.0, (float) $session->score);
        $this->assertFalse($session->lulus);
    }

    public function test_sesi_tanpa_jawaban_tidak_lulus(): void
    {
        $this->createCpls(2);
        $session = KompreSession::create([
            'user_id' => 1,
            'title' => 'Ujian Komprehensif',
            'status' => KompreSessionStatus::Ongoing,
        ]);

        $this->finish($session);

        $session->refresh();

        $this->assertSame(0.0, (float) $session->score);
        $this->assertFalse($session->lulus);
        $this->assertCount(2, $session->cpl_scores);
        $this->assertSame([0.0, 0.0], array_map('floatval', array_column($session->cpl_scores, 'nilai')));
    }

    public function test_cpl_tanpa_soal_dianggap_gagal(): void
    {
        // 3 CPL dibuat, tetapi hanya CPL pertama yang punya soal.
        $session = $this->makeSession([10], 3);

        $this->finish($session);

        $session->refresh();

        $this->assertFalse($session->lulus);
        $this->assertCount(3, $session->cpl_scores);
        $this->assertSame([10.0, 0.0, 0.0], array_map('floatval', array_column($session->cpl_scores, 'nilai')));
    }

    public function test_has_passed_mendeteksi_sesi_lulus(): void
    {
        KompreSession::create([
            'user_id' => 1,
            'title' => 'Ujian Lulus',
            'status' => KompreSessionStatus::Finished,
            'lulus' => true,
            'score' => 80,
            'cpl_scores' => [],
        ]);

        $service = new PendaftaranService;

        $this->assertTrue($service->hasPassed(1));
        $this->assertFalse($service->hasPassed(2));
    }

    private function finish(KompreSession $session): void
    {
        (new CbtSessionService)->finishSession($session);
    }

    /**
     * @param  array<int, int>  $correctCounts  Jumlah jawaban benar per CPL.
     */
    private function makeSession(array $correctCounts, ?int $totalCpls = null): KompreSession
    {
        $cpls = $this->createCpls($totalCpls ?? count($correctCounts));

        $session = KompreSession::create([
            'user_id' => 1,
            'title' => 'Ujian Komprehensif',
            'status' => KompreSessionStatus::Ongoing,
        ]);

        $urutan = 1;

        foreach ($correctCounts as $cplIndex => $correctCount) {
            $cpl = $cpls[$cplIndex];

            for ($i = 0; $i < 10; $i++) {
                $pertanyaan = Pertanyaan::create([
                    'soal' => "Soal {$cpl->kode}-{$i}",
                    'bobot' => 1,
                    'cpl_id' => $cpl->id,
                    'mk_id' => 1,
                    'kesulitan' => 'easy',
                    'status' => Pertanyaan::STATUS_DISETUJUI,
                ]);

                $benar = Jawaban::create([
                    'soal_id' => $pertanyaan->id,
                    'opsi' => 'A',
                    'deskripsi' => 'Jawaban benar',
                    'is_benar' => true,
                ]);

                $salah = Jawaban::create([
                    'soal_id' => $pertanyaan->id,
                    'opsi' => 'B',
                    'deskripsi' => 'Jawaban salah',
                    'is_benar' => false,
                ]);

                KompreJawaban::create([
                    'kompre_session_id' => $session->id,
                    'pertanyaan_id' => $pertanyaan->id,
                    'jawaban_dipilih' => $i < $correctCount ? $benar->id : $salah->id,
                    'urutan_soal' => $urutan++,
                    'kesulitan_now' => 'easy',
                    'is_benar_now' => $i < $correctCount,
                ]);
            }
        }

        return $session;
    }

    /**
     * @return array<int, Cpl>
     */
    private function createCpls(int $count): array
    {
        $cpls = [];

        for ($i = 1; $i <= $count; $i++) {
            $cpls[] = Cpl::create([
                'kode' => 'CPL-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'deskripsi' => "Deskripsi CPL {$i}",
            ]);
        }

        return $cpls;
    }

    private function runMigration(string $relativePath): void
    {
        $migration = require base_path($relativePath);
        $migration->up();
    }
}
