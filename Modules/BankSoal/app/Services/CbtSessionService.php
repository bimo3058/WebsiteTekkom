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
     * Hasilkan soal ujian dengan distribusi tingkat kesulitan adaptif.
     *
     * Alur:
     * 1. Analisis kelemahan CPL dari sesi terakhir yang gagal (jika ada)
     * 2. Tentukan distribusi target (easy/intermediate/advanced) per CPL
     * 3. Balance pool soal jika distribusi di bank soal timpang
     * 4. Pilih soal sesuai distribusi, acak urutan, bulk insert
     *
     * Jika mahasiswa belum pernah ujian → distribusi default (3/4/3).
     * Jika pernah gagal → CPL lemah mendapat soal lebih mudah,
     * CPL kuat mendapat soal lebih sulit.
     */
    public function generateSoal(KompreSession $session): void
    {
        // ── Step 1: Analisis CPL weakness dari riwayat ujian sebelumnya ──
        $cplWeakness = $this->analyzeCplWeakness($session->user_id);

        // ── Step 2: Ambil semua 10 CPL (tetap) dan semua soal disetujui ──
        $cplIds = Cpl::pluck('id');

        $allPertanyaans = Pertanyaan::whereIn('cpl_id', $cplIds)
            ->where('status', Pertanyaan::STATUS_DISETUJUI)
            ->with('jawaban:id,soal_id')
            ->get();

        $soals = collect();

        // ── Step 3: Per CPL, balance pool lalu pilih soal ──
        foreach ($cplIds as $cplId) {
            $pool = $allPertanyaans->where('cpl_id', $cplId);

            // Tentukan distribusi target berdasarkan weakness map
            $kategori     = $cplWeakness[$cplId] ?? 'default';
            $distribution = $this->getDifficultyDistribution($kategori);

            // Balance pool: promosikan soal surplus ke level defisit
            $balancedPool = $this->balancePool($pool, $distribution);

            // Pilih soal sesuai distribusi dari pool yang sudah balanced
            $picked = collect();
            foreach ($distribution as $level => $count) {
                if ($count <= 0) continue;
                $picked = $picked->merge(
                    $balancedPool[$level]->take($count)
                );
            }

            // Fallback terakhir: jika masih kurang, ambil dari sisa apapun
            if ($picked->count() < self::SOAL_PER_CPL) {
                $remaining = $pool->diff($picked)->shuffle();
                $picked = $picked->merge(
                    $remaining->take(self::SOAL_PER_CPL - $picked->count())
                );
            }

            $soals = $soals->merge($picked->take(self::SOAL_PER_CPL));
        }

        // ── Step 4: Acak urutan final semua soal ──
        $soals = $soals->shuffle()->values();

        // ── Step 5: Validasi minimum soal ──
        $required = self::SOAL_PER_CPL * self::TOTAL_CPL;
        if ($soals->count() < $required) {
            throw new \RuntimeException(sprintf(
                'Bank soal tidak mencukupi. Dibutuhkan %d soal (%d CPL × %d soal), hanya %d tersedia. Hubungi admin untuk menambah soal.',
                $required,
                self::TOTAL_CPL,
                self::SOAL_PER_CPL,
                $soals->count()
            ));
        }

        // ── Step 6: Satu bulk INSERT (tidak berubah) ──
        $now  = now();
        $rows = $soals->map(function ($soal, $idx) use ($session, $now) {
            $opsiIds = $soal->jawaban->pluck('id')->shuffle()->toArray();

            return [
                'kompre_session_id' => $session->id,
                'pertanyaan_id'     => $soal->id,
                'urutan_soal'       => $idx + 1,
                'urutan_opsi'       => json_encode($opsiIds),
                'kesulitan_now'     => $soal->kesulitan, // tetap simpan kesulitan ASLI
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

    // =========================================================================
    // Adaptive Difficulty Logic
    // =========================================================================

    /**
     * Analisis kelemahan CPL dari sesi terakhir yang gagal.
     *
     * Selalu mengambil sesi TERAKHIR yang gagal (score < 60).
     * Bersifat iteratif: jika gagal 3x, yang dianalisis adalah kegagalan ke-3.
     * Jika tidak ada riwayat gagal (ujian pertama), return array kosong
     * sehingga distribusi default diterapkan.
     *
     * Mahasiswa yang sudah lulus tidak bisa ujian lagi, jadi tidak perlu
     * cek apakah pernah lulus setelah gagal.
     *
     * @return array<int, string> [cpl_id => 'sangat_lemah'|'lemah'|'cukup'|'baik']
     */
    private function analyzeCplWeakness(int $userId): array
    {
        $lastFailedSession = KompreSession::where('user_id', $userId)
            ->where('status', KompreSessionStatus::Finished)
            ->where('score', '<', 60)
            ->orderByDesc('finished_at')
            ->first();

        // Ujian pertama kali atau tidak ada riwayat gagal
        if (!$lastFailedSession) {
            return [];
        }

        $jawabans = KompreJawaban::where('kompre_session_id', $lastFailedSession->id)
            ->with(['pertanyaan:id,cpl_id', 'opsiTerpilih:id,is_benar'])
            ->get();

        $cplMap = [];

        foreach ($jawabans->groupBy(fn($j) => $j->pertanyaan?->cpl_id) as $cplId => $items) {
            if (!$cplId) continue;

            $total = $items->count();
            $benar = $items->filter(fn($j) => $j->jawaban_dipilih && $j->opsiTerpilih?->is_benar)->count();
            $pct   = $total > 0 ? ($benar / $total) * 100 : 0;

            $cplMap[$cplId] = match (true) {
                $pct <= 30 => 'sangat_lemah',
                $pct <= 50 => 'lemah',
                $pct <= 70 => 'cukup',
                default    => 'baik',
            };
        }

        return $cplMap;
    }

    /**
     * Distribusi target soal per tingkat kesulitan berdasarkan kategori CPL.
     *
     * @return array{easy: int, intermediate: int, advanced: int}
     */
    private function getDifficultyDistribution(string $kategori): array
    {
        return match ($kategori) {
            'sangat_lemah' => ['easy' => 7, 'intermediate' => 3, 'advanced' => 0],
            'lemah'        => ['easy' => 4, 'intermediate' => 4, 'advanced' => 2],
            'cukup'        => ['easy' => 2, 'intermediate' => 5, 'advanced' => 3],
            'baik'         => ['easy' => 1, 'intermediate' => 4, 'advanced' => 5],
            default        => ['easy' => 3, 'intermediate' => 4, 'advanced' => 3],
        };
    }

    /**
     * Smart Pool Balancer — seimbangkan pool soal per CPL.
     *
     * Jika suatu level kesulitan tidak punya cukup soal untuk memenuhi
     * distribusi target, ambil soal dari level TERDEKAT yang surplus
     * sebagai "decoy". Soal yang dipromosikan tetap menyimpan kesulitan
     * aslinya — promosi hanya mempengaruhi slot distribusi.
     *
     * Prioritas promosi (dari level terdekat):
     *   - Defisit intermediate → isi dari easy dulu, baru advanced
     *   - Defisit advanced     → isi dari intermediate dulu, baru easy
     *   - Defisit easy         → isi dari intermediate dulu, baru advanced
     *
     * @param  \Illuminate\Support\Collection  $pool         Semua soal untuk 1 CPL
     * @param  array{easy: int, intermediate: int, advanced: int} $distribution Target distribusi
     * @return array{easy: \Illuminate\Support\Collection, intermediate: \Illuminate\Support\Collection, advanced: \Illuminate\Support\Collection}
     */
    private function balancePool($pool, array $distribution): array
    {
        $levels = ['easy', 'intermediate', 'advanced'];

        // 1. Kelompokkan pool asli berdasarkan kesulitan
        $grouped = [];
        foreach ($levels as $level) {
            $grouped[$level] = $pool->where('kesulitan', $level)->shuffle()->values();
        }

        // 2. Hitung surplus & defisit per level
        $surplus = [];
        $deficit = [];
        foreach ($levels as $level) {
            $have = $grouped[$level]->count();
            $need = $distribution[$level];
            if ($have > $need) {
                $surplus[$level] = $have - $need;
            } elseif ($have < $need) {
                $deficit[$level] = $need - $have;
            }
        }

        // 3. Urutan promosi: ambil dari level TERDEKAT dulu
        $promotionOrder = [
            'easy'         => ['intermediate', 'advanced'],
            'intermediate' => ['easy', 'advanced'],
            'advanced'     => ['intermediate', 'easy'],
        ];

        // 4. Promosikan soal surplus ke level defisit
        foreach ($deficit as $deficitLevel => $deficitCount) {
            $donors = $promotionOrder[$deficitLevel] ?? [];

            foreach ($donors as $donorLevel) {
                if ($deficitCount <= 0) break;
                if (($surplus[$donorLevel] ?? 0) <= 0) continue;

                // Soal yang bisa didonasikan = sisanya setelah slot asli terpenuhi
                $donorPool   = $grouped[$donorLevel];
                $donorNeeded = $distribution[$donorLevel];
                $available   = $donorPool->slice($donorNeeded)->values();
                $toMove      = min($deficitCount, $available->count(), $surplus[$donorLevel]);

                if ($toMove > 0) {
                    // Pindahkan ke pool level defisit
                    $moved = $available->take($toMove);
                    $grouped[$deficitLevel] = $grouped[$deficitLevel]->merge($moved);

                    // Kurangi surplus dan defisit
                    $surplus[$donorLevel] -= $toMove;
                    $deficitCount         -= $toMove;
                }
            }
        }

        return $grouped;
    }
}
