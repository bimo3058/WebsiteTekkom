<?php

namespace Modules\BankSoal\Services;

use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\KompreSession;
use Modules\BankSoal\Models\KompreJawaban;
use Modules\BankSoal\Models\Pertanyaan;

class KompreService
{
    /**
     * Jumlah soal per sesi komprehensif.
     */
    private const TOTAL_SOAL = 20;

    /**
     * Batas benar/salah berturut-turut sebelum level naik/turun.
     */
    private const STREAK_NAIK  = 2;
    private const STREAK_TURUN = 2;

    // =========================================================================
    // Session management
    // =========================================================================

    /**
     * Mulai sesi komprehensif baru.
     * Selalu dimulai dari level 'easy'.
     */
    public function mulaiSesi(int $userId, string $title): KompreSession
    {
        // Pastikan tidak ada sesi ongoing yang belum selesai
        $existing = KompreSession::byUser($userId)->ongoing()->first();

        if ($existing) {
            throw new \RuntimeException('Masih ada sesi komprehensif yang belum selesai.');
        }

        return KompreSession::create([
            'user_id'    => $userId,
            'title'      => $title,
            'started_at' => now(),
            'status'     => KompreSession::STATUS_ONGOING,
            'score'      => 0,
        ]);
    }

    /**
     * Ambil soal berikutnya berdasarkan performa adaptif.
     * Mengembalikan null jika sesi sudah selesai (20 soal terpenuhi).
     */
    public function getSoalBerikutnya(int $sessionId): ?Pertanyaan
    {
        $session = KompreSession::with('jawaban')->findOrFail($sessionId);

        if (!$session->isOngoing()) {
            throw new \RuntimeException('Sesi sudah berakhir.');
        }

        if ($session->jawaban->count() >= self::TOTAL_SOAL) {
            $this->selesaikanSesi($sessionId);
            return null;
        }

        $kesulitan        = $this->tentukanKesulitan($session);
        $soalSudahDijawab = $session->jawaban->pluck('pertanyaan_id')->toArray();

        // Ambil soal yang belum pernah muncul di sesi ini
        $soal = Pertanyaan::disetujui()
            ->byKesulitan($kesulitan)
            ->whereNotIn('id', $soalSudahDijawab)
            ->inRandomOrder()
            ->with('jawaban')
            ->first();

        // Fallback: kalau soal di level ini habis, cari di level lain
        if (!$soal) {
            $soal = Pertanyaan::disetujui()
                ->whereNotIn('id', $soalSudahDijawab)
                ->inRandomOrder()
                ->with('jawaban')
                ->first();
        }

        return $soal;
    }

    /**
     * Catat jawaban mahasiswa dan tentukan apakah benar.
     */
    public function jawab(int $sessionId, int $pertanyaanId, int $jawabanId): KompreJawaban
    {
        return DB::transaction(function () use ($sessionId, $pertanyaanId, $jawabanId) {
            $session    = KompreSession::findOrFail($sessionId);
            $pertanyaan = Pertanyaan::with('jawaban')->findOrFail($pertanyaanId);

            if (!$session->isOngoing()) {
                throw new \RuntimeException('Sesi sudah berakhir.');
            }

            // Validasi jawaban milik soal ini
            $jawaban = $pertanyaan->jawaban->firstWhere('id', $jawabanId);

            if (!$jawaban) {
                throw new \RuntimeException('Pilihan jawaban tidak valid untuk soal ini.');
            }

            $isBenar     = $jawaban->is_benar;
            $urutanSoal  = $session->jawaban()->count() + 1;
            $kesulitanNow = $this->tentukanKesulitan($session);

            $kompreJawaban = KompreJawaban::create([
                'kompre_session_id' => $sessionId,
                'pertanyaan_id'     => $pertanyaanId,
                'jawaban_dipilih'   => $jawabanId,
                'urutan_soal'       => $urutanSoal,
                'kesulitan_now'     => $kesulitanNow,
                'is_benar_now'      => $isBenar,
            ]);

            // Otomatis selesaikan sesi jika sudah 20 soal
            if ($urutanSoal >= self::TOTAL_SOAL) {
                $this->selesaikanSesi($sessionId);
            }

            return $kompreJawaban;
        });
    }

    /**
     * Selesaikan sesi dan hitung skor akhir.
     */
    public function selesaikanSesi(int $sessionId): KompreSession
    {
        return DB::transaction(function () use ($sessionId) {
            $session = KompreSession::with('jawaban')->findOrFail($sessionId);

            if (!$session->isOngoing()) {
                return $session; // idempotent
            }

            $score = $this->hitungSkor($session);

            $session->update([
                'status'      => KompreSession::STATUS_FINISHED,
                'finished_at' => now(),
                'score'       => $score,
            ]);

            return $session->fresh();
        });
    }

    /**
     * Batalkan sesi yang sedang berjalan.
     */
    public function batalkanSesi(int $sessionId): void
    {
        $session = KompreSession::findOrFail($sessionId);

        if (!$session->isOngoing()) {
            throw new \RuntimeException('Sesi tidak sedang berjalan.');
        }

        $session->update([
            'status'      => KompreSession::STATUS_ABORTED,
            'finished_at' => now(),
        ]);
    }

    /**
     * Riwayat sesi komprehensif seorang user.
     */
    public function getRiwayat(int $userId, int $perPage = 10)
    {
        return KompreSession::byUser($userId)
            ->withCount('jawaban')
            ->orderByDesc('started_at')
            ->paginate($perPage);
    }

    /**
     * Detail hasil sesi lengkap dengan breakdown per soal.
     */
    public function getHasilSesi(int $sessionId): KompreSession
    {
        return KompreSession::with([
            'jawaban.pertanyaan.cpl',
            'jawaban.pertanyaan.mataKuliah',
            'jawaban.jawabanDipilih',
        ])->findOrFail($sessionId);
    }

    // =========================================================================
    // Adaptive logic
    // =========================================================================

    /**
     * Tentukan level kesulitan soal berikutnya berdasarkan streak jawaban.
     *
     * Aturan:
     * - Mulai dari 'easy'
     * - Benar {STREAK_NAIK} kali berturut-turut → naik level
     * - Salah {STREAK_TURUN} kali berturut-turut → turun level
     * - Level tidak bisa melebihi 'advanced' atau di bawah 'easy'
     */
    private function tentukanKesulitan(KompreSession $session): string
    {
        $jawaban = $session->jawaban->sortBy('urutan_soal');

        if ($jawaban->isEmpty()) {
            return Pertanyaan::KESULITAN_EASY;
        }

        // Ambil level terakhir yang dipakai
        $levelSekarang = $jawaban->last()->kesulitan_now;

        // Cek streak N terakhir
        $nTerakhir = $jawaban->takLast(max(self::STREAK_NAIK, self::STREAK_TURUN));

        $streakBenar = $nTerakhir->takLast(self::STREAK_NAIK)->every(fn($j) => $j->is_benar_now);
        $streakSalah = $nTerakhir->takLast(self::STREAK_TURUN)->every(fn($j) => !$j->is_benar_now);

        if ($streakBenar) {
            return $this->naikkanLevel($levelSekarang);
        }

        if ($streakSalah) {
            return $this->turunkanLevel($levelSekarang);
        }

        return $levelSekarang;
    }

    private function naikkanLevel(string $level): string
    {
        return match ($level) {
            Pertanyaan::KESULITAN_EASY         => Pertanyaan::KESULITAN_INTERMEDIATE,
            Pertanyaan::KESULITAN_INTERMEDIATE => Pertanyaan::KESULITAN_ADVANCED,
            default                            => Pertanyaan::KESULITAN_ADVANCED,
        };
    }

    private function turunkanLevel(string $level): string
    {
        return match ($level) {
            Pertanyaan::KESULITAN_ADVANCED     => Pertanyaan::KESULITAN_INTERMEDIATE,
            Pertanyaan::KESULITAN_INTERMEDIATE => Pertanyaan::KESULITAN_EASY,
            default                            => Pertanyaan::KESULITAN_EASY,
        };
    }

    // =========================================================================
    // Scoring
    // =========================================================================

    /**
     * Skor berbobot berdasarkan tingkat kesulitan soal yang dijawab benar.
     *
     * Bobot: easy = 1, intermediate = 2, advanced = 3
     * Skor  = (total bobot benar / total bobot semua soal) × 100
     */
    private function hitungSkor(KompreSession $session): float
    {
        $jawaban = $session->jawaban;

        if ($jawaban->isEmpty()) {
            return 0;
        }

        $totalBobot = $jawaban->sum(fn($j) => Pertanyaan::KESULITAN_ORDER[$j->kesulitan_now] ?? 1);
        $bobotBenar = $jawaban
            ->where('is_benar_now', true)
            ->sum(fn($j) => Pertanyaan::KESULITAN_ORDER[$j->kesulitan_now] ?? 1);

        if ($totalBobot === 0) {
            return 0;
        }

        return round(($bobotBenar / $totalBobot) * 100, 2);
    }
}