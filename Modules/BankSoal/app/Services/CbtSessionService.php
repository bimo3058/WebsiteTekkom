<?php

namespace Modules\BankSoal\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Jobs\AutoFinishExamJob;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Models\Shared\Cpl;

class CbtSessionService
{
    /** Durasi ujian dalam menit. */
    public const EXAM_DURATION_MINUTES = 100;

    /** Jumlah soal yang diambil per CPL. */
    private const SOAL_PER_CPL = 10;

    /** Jumlah CPL yang digunakan dalam satu sesi. */
    private const TOTAL_CPL = 10;

    /**
     * Mulai sesi ujian baru untuk mahasiswa.
     *
     * Membuat record KompreSession dan menghasilkan soal secara acak
     * dalam satu database transaction.
     */
    public function startSession(JadwalUjian $jadwal, int $userId): KompreSession
    {
        return DB::transaction(function () use ($jadwal, $userId) {
            $session = KompreSession::create([
                'user_id'    => $userId,
                'jadwal_id'  => $jadwal->id,
                'title'      => 'Ujian Komprehensif ' . $jadwal->nama_sesi,
                'started_at' => now(),
                'status'     => KompreSessionStatus::Ongoing,
            ]);

            $this->generateSoal($session);

            // ✅ Ketat: end_time = min(started_at + 100 menit, waktu_selesai jadwal)
            // Mahasiswa yang terlambat masuk mendapat sisa waktu gate, bukan penuh 100 menit.
            $gateClose = Carbon::parse(
                $jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai
            );
            $endByDuration = $session->started_at->copy()->addMinutes(self::EXAM_DURATION_MINUTES);
            $endTime       = $endByDuration->lt($gateClose) ? $endByDuration : $gateClose;

            // Dispatch auto-finish job dari server sebagai perlindungan
            // terhadap manipulasi timer client-side.
            // Hanya aktif jika queue driver bukan 'sync'.
            if (config('queue.default') !== 'sync') {
                AutoFinishExamJob::dispatch($session->id)
                    ->delay($endTime->copy()->addSeconds(30)); // 30 detik buffer
            }

            return $session;
        });
    }

    /**
     * Hasilkan soal ujian secara acak dan alokasikan ke sesi.
     *
     * Mengambil SOAL_PER_CPL soal dari masing-masing TOTAL_CPL CPL
     * secara acak, kemudian mengacak keseluruhan urutan soal dan opsi jawaban.
     *
     * OPTIMASI:
     * - 1 query untuk CPL (ganti 10 query loop individual)
     * - 1 query untuk semua soal via whereIn (ganti N+1 loop pertanyaan)
     * - shuffle() di PHP lebih cepat dari ORDER BY RANDOM() di remote DB
     * - 1 bulk INSERT untuk semua jawabans (ganti 100x individual create)
     */
    public function generateSoal(KompreSession $session): void
    {
        // ✅ Query 1: Ambil semua CPL sekali, acak di PHP
        $cplIds = Cpl::pluck('id')->shuffle()->take(self::TOTAL_CPL);

        // ✅ Query 2: Ambil semua soal dari CPL yang terpilih SEKALIGUS
        // beserta jawabans-nya (eager load, bukan lazy load dalam loop)
        $allPertanyaans = Pertanyaan::whereIn('cpl_id', $cplIds)
            ->where('status', Pertanyaan::STATUS_DISETUJUI)
            ->with('jawabans:id,soal_id') // eager load hanya kolom yang dibutuhkan
            ->get()
            ->groupBy('cpl_id');

        // Pilih soal per CPL secara merata, acak di PHP (lebih cepat dari ORDER BY RANDOM())
        $soals = collect();
        foreach ($cplIds as $cplId) {
            $pool = $allPertanyaans->get($cplId, collect());
            $soals = $soals->merge($pool->shuffle()->take(self::SOAL_PER_CPL));
        }

        // Acak urutan final semua soal
        $soals = $soals->shuffle()->values();

        // ✅ Validasi minimum soal
        $required = self::SOAL_PER_CPL * self::TOTAL_CPL;
        if ($soals->count() < $required) {
            throw new \RuntimeException(sprintf(
                'Bank soal tidak mencukupi. Dibutuhkan %d soal (%d CPL × %d soal), hanya %d tersedia dari CPL yang dipilih. Hubungi admin untuk menambah soal.',
                $required,
                self::TOTAL_CPL,
                self::SOAL_PER_CPL,
                $soals->count()
            ));
        }

        // ✅ Query 3: Satu bulk INSERT menggantikan 100x individual create()
        $now   = now();
        $rows  = $soals->map(function ($soal, $idx) use ($session, $now) {
            // Acak urutan opsi di PHP dari data yang sudah di-eager-load
            $opsiIds = $soal->jawabans->pluck('id')->shuffle()->toArray();

            return [
                'kompre_session_id' => $session->id,
                'pertanyaan_id'     => $soal->id,
                'urutan_soal'       => $idx + 1,
                'urutan_opsi'       => json_encode($opsiIds),
                'kesulitan_now'     => $soal->kesulitan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        })->toArray();

        KompreJawaban::insert($rows);
    }

    /**
     * Hitung skor akhir sebagai persentase jawaban benar.
     *
     * Mengembalikan float antara 0–100 (dibulatkan 2 desimal).
     * Sumber tunggal kebenaran untuk kalkulasi skor — dipakai oleh
     * student self-submit maupun admin force-submit.
     */
    public function calculateScore(KompreSession $session): float
    {
        $jawabans = KompreJawaban::where('kompre_session_id', $session->id)
            ->with('opsiTerpilih')
            ->get();

        $total = $jawabans->count();

        if ($total === 0) {
            return 0.0;
        }

        $benar = $jawabans->filter(
            fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar
        )->count();

        return round(($benar / $total) * 100, 2);
    }

    /**
     * Selesaikan sesi ujian: hitung skor dan tandai sebagai finished.
     */
    public function finishSession(KompreSession $session): void
    {
        $session->update([
            'status'      => KompreSessionStatus::Finished,
            'finished_at' => now(),
            'score'       => $this->calculateScore($session),
        ]);
    }

    /**
     * Hitung waktu berakhirnya ujian.
     *
     * Menggunakan aturan KETAT: end_time = min(started_at + 100 menit, waktu_selesai sesi).
     * Mahasiswa yang terlambat masuk hanya mendapat sisa waktu gate, bukan penuh 100 menit.
     *
     * Membutuhkan relasi 'jadwal' di-load sebelumnya.
     */
    public function getEndTime(KompreSession $session): Carbon
    {
        $endByDuration = $session->started_at->copy()->addMinutes(self::EXAM_DURATION_MINUTES);

        // Jika jadwal tersedia, cap di waktu_selesai gate
        if ($session->relationLoaded('jadwal') && $session->jadwal) {
            $gateClose = Carbon::parse(
                $session->jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $session->jadwal->waktu_selesai
            );
            return $endByDuration->lt($gateClose) ? $endByDuration : $gateClose;
        }

        // Fallback: tanpa jadwal, gunakan durasi penuh (backward-compatible)
        return $endByDuration;
    }
}
