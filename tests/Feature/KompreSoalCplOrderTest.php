<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\BankSoal\Models\Jawaban;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Models\Shared\Cpl;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Services\CbtSessionService;
use Tests\TestCase;

/**
 * Regresi: soal ujian komprehensif harus terkelompok per CPL berurutan
 * (no. 1-10 = CPL 1, 11-20 = CPL 2, ..., 91-100 = CPL 10),
 * tetapi acak di dalam tiap CPL dan opsi tiap soal diacak.
 * Setiap mahasiswa mendapat set/urutan soal yang berbeda.
 *
 * Catatan: menjalankan subset migrasi yang dibutuhkan saja (pola yang sama
 * dengan KompreSessionUniquenessTest) + skema final bs_kompre_jawaban manual
 * karena alter migration memakai ->change() yang butuh doctrine/dbal.
 */
class KompreSoalCplOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->runMigration('database/migrations/2026_02_15_111353_create_users_table.php');
        $this->runMigration('database/migrations/2026_02_18_104631_add_auth_columns_to_users_table.php');
        $this->runMigration('database/migrations/2026_02_20_085615_add_password_to_users_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013401_create_bs_cpl_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013516_create_bs_pertanyaan_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_20_013547_create_bs_jawaban_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_092214_create_bs_periode_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_092217_create_bs_jadwal_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_03_27_102724_add_tanggal_ujian_to_bs_jadwal_ujians_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_02_24_032833_create_bs_kompre_session_table.php');
        $this->runMigration('Modules/BankSoal/database/migrations/2026_04_23_225401_add_jadwal_id_to_bs_kompre_session_table.php');

        // Skema final bs_kompre_jawaban (setara hasil alter migration produksi).
        Schema::create('bs_kompre_jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kompre_session_id');
            $table->unsignedBigInteger('pertanyaan_id');
            $table->unsignedBigInteger('jawaban_dipilih')->nullable();
            $table->integer('urutan_soal');
            $table->string('kesulitan_now')->nullable();
            $table->boolean('is_benar_now')->nullable();
            $table->json('urutan_opsi')->nullable();
            $table->boolean('is_ragu')->default(false);
            $table->timestamps();
        });
    }

    public function test_soal_terkelompok_per_cpl_berurutan_dan_opsi_teracak(): void
    {
        $this->seedBankSoal();
        $jadwal = $this->buatJadwal();
        $user = User::factory()->create();

        $session = (new CbtSessionService())->startSession($jadwal, $user->id);

        $jawabans = KompreJawaban::where('kompre_session_id', $session->id)
            ->with(['pertanyaan.cpl', 'pertanyaan.jawaban'])
            ->orderBy('urutan_soal')
            ->get();

        // 100 soal, nomor urut 1..100 tanpa lubang.
        $this->assertCount(100, $jawabans);
        $this->assertSame(range(1, 100), $jawabans->pluck('urutan_soal')->all());

        // Blok 10 soal berurutan = 1 CPL, CPL-001 .. CPL-010.
        foreach (range(0, 9) as $blok) {
            $diharapkan = sprintf('CPL-%03d', $blok + 1);
            $potongan = $jawabans->slice($blok * 10, 10);
            foreach ($potongan as $jawaban) {
                $this->assertSame(
                    $diharapkan,
                    $jawaban->pertanyaan->cpl->kode,
                    "Soal no. {$jawaban->urutan_soal} seharusnya dari {$diharapkan}."
                );
            }
        }

        // Opsi tiap soal: set id sama dengan opsi pertanyaan, dalam urutan acak
        // (urutan_opsi adalah permutasi, bukan urutan natural id ASC).
        $urutanNatural = 0;
        foreach ($jawabans as $jawaban) {
            $alami = $jawaban->pertanyaan->jawaban->pluck('id')->sort()->values()->all();
            $acak = $jawaban->urutan_opsi;
            $this->assertIsArray($acak);
            $this->assertSame($alami, collect($acak)->sort()->values()->all());
            if ($acak === $alami) {
                $urutanNatural++;
            }
        }
        // Hampir mustahil semua 100 soal kebetulan berurutan natural.
        $this->assertLessThan(
            100,
            $urutanNatural,
            'Opsi jawaban seharusnya diacak, bukan urutan natural di semua soal.'
        );
    }

    public function test_setiap_mahasiswa_mendapat_urutan_soal_berbeda(): void
    {
        $this->seedBankSoal();
        $jadwal = $this->buatJadwal();

        $urutan = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create();
            $session = (new CbtSessionService())->startSession($jadwal, $user->id);
            $urutan[] = KompreJawaban::where('kompre_session_id', $session->id)
                ->orderBy('urutan_soal')
                ->pluck('pertanyaan_id')
                ->all();
        }

        $this->assertCount(3, array_unique(array_map('serialize', $urutan)), 'Tiga mahasiswa seharusnya tidak mendapat urutan soal yang identik.');
    }

    /**
     * 10 CPL x 12 soal (4 easy / 4 intermediate / 4 advanced),
     * tiap soal 4 opsi (opsi pertama benar).
     */
    private function seedBankSoal(): void
    {
        foreach (range(1, 10) as $c) {
            $cpl = Cpl::create([
                'kode' => sprintf('CPL-%03d', $c),
                'deskripsi' => "CPL $c",
            ]);

            foreach (['easy', 'intermediate', 'advanced'] as $level) {
                for ($q = 1; $q <= 4; $q++) {
                    $pertanyaan = Pertanyaan::create([
                        'soal' => "Soal CPL $c $level $q",
                        'bobot' => 1,
                        'cpl_id' => $cpl->id,
                        'mk_id' => 1,
                        'kesulitan' => $level,
                        'status' => Pertanyaan::STATUS_DISETUJUI,
                    ]);

                    foreach (['A', 'B', 'C', 'D'] as $index => $opsi) {
                        Jawaban::create([
                            'soal_id' => $pertanyaan->id,
                            'opsi' => $opsi,
                            'deskripsi' => "Opsi $opsi soal {$pertanyaan->id}",
                            'is_benar' => $index === 0,
                        ]);
                    }
                }
            }
        }
    }

    private function buatJadwal(): JadwalUjian
    {
        $periode = PeriodeUjian::create([
            'nama_periode' => 'Periode Test',
            'slug' => 'periode-test-'.uniqid(),
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->addMonth(),
            'status' => 'aktif',
        ]);

        return JadwalUjian::create([
            'periode_ujian_id' => $periode->id,
            'nama_sesi' => 'Sesi 1',
            'tanggal_ujian' => now()->toDateString(),
            'kuota' => 10,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
        ]);
    }

    private function runMigration(string $relativePath): void
    {
        $migration = require base_path($relativePath);
        $migration->up();
    }
}
