<?php

namespace Tests\Feature;

use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Jawaban;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Services\CbtSessionService;
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
