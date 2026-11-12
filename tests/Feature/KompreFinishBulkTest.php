<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Http\Controllers\Komprehensif\CbtEngineController;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Jawaban;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Pertanyaan;
use Tests\TestCase;

class KompreFinishBulkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigration('database/migrations/2026_02_15_111353_create_users_table.php');
        $this->runMigration('database/migrations/2026_02_18_104631_add_auth_columns_to_users_table.php');
        $this->runMigration('database/migrations/2026_02_20_085615_add_password_to_users_table.php');
        $this->runMigration('database/migrations/2026_08_08_000002_add_presence_columns_to_users_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013401_create_bs_cpl_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013516_create_bs_pertanyaan_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013547_create_bs_jawaban_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032833_create_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_11_10_000001_add_result_columns_to_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_11_11_000004_add_passing_rule_to_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032857_create_bs_kompre_jawaban_table.php');
        $this->runMigration('database/migrations/2026_04_23_202643_alter_bs_kompre_jawaban_make_nullable_add_urutan_opsi.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_04_26_130000_add_is_ragu_to_bs_kompre_jawaban_table.php');
    }

    public function test_finish_bulk_menyimpan_jawaban_terakhir_dan_skor(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $session = KompreSession::create([
            'user_id' => $user->id,
            'title' => 'Ujian Komprehensif',
            'status' => KompreSessionStatus::Ongoing,
        ]);

        $cpl = Cpl::create([
            'kode' => 'CPL-001',
            'deskripsi' => 'CPL Test',
        ]);

        $questions = [];

        foreach ([1, 2] as $index) {
            $pertanyaan = Pertanyaan::create([
                'soal' => "Soal {$index}",
                'bobot' => 1,
                'cpl_id' => $cpl->id,
                'mk_id' => 1,
                'kesulitan' => 'easy',
                'status' => Pertanyaan::STATUS_DISETUJUI,
            ]);

            $benar = Jawaban::create([
                'soal_id' => $pertanyaan->id,
                'opsi' => 'A',
                'deskripsi' => 'Benar',
                'is_benar' => true,
            ]);

            $salah = Jawaban::create([
                'soal_id' => $pertanyaan->id,
                'opsi' => 'B',
                'deskripsi' => 'Salah',
                'is_benar' => false,
            ]);

            $jawaban = KompreJawaban::create([
                'kompre_session_id' => $session->id,
                'pertanyaan_id' => $pertanyaan->id,
                'jawaban_dipilih' => $index === 1 ? $salah->id : $benar->id,
                'urutan_soal' => $index,
                'urutan_opsi' => [$benar->id, $salah->id],
                'kesulitan_now' => 'easy',
                'is_benar_now' => false,
                'is_ragu' => false,
            ]);

            $questions[] = [
                'jawaban_id' => $jawaban->id,
                'opsi_terpilih' => $jawaban->jawaban_dipilih,
                'opsi_baru' => $jawaban->jawaban_dipilih === $benar->id ? $salah->id : $benar->id,
            ];
        }

        $response = app(CbtEngineController::class)->finishBulk(Request::create(
            '/ujian-komprehensif/engine/finish',
            'POST',
            [
                'answers' => [
                    [
                        'jawaban_id' => $questions[0]['jawaban_id'],
                        'opsi_terpilih' => $questions[0]['opsi_baru'],
                        'is_ragu' => false,
                    ],
                    [
                        'jawaban_id' => $questions[1]['jawaban_id'],
                        'opsi_terpilih' => $questions[1]['opsi_baru'],
                        'is_ragu' => false,
                    ],
                ],
            ]
        ));

        $this->assertSame(200, $response->getStatusCode());

        $session->refresh();

        $this->assertSame(KompreSessionStatus::Finished, $session->status);
        $this->assertSame(50.0, (float) $session->score);
        $this->assertFalse($session->lulus);

        $this->assertSame(
            $questions[0]['opsi_baru'],
            KompreJawaban::find($questions[0]['jawaban_id'])->jawaban_dipilih
        );
        $this->assertSame(
            $questions[1]['opsi_baru'],
            KompreJawaban::find($questions[1]['jawaban_id'])->jawaban_dipilih
        );
    }

    private function runMigration(string $relativePath): void
    {
        $migration = require base_path($relativePath);
        $migration->up();
    }
}
