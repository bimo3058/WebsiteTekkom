<?php

namespace Modules\BankSoal\Services\Komprehensif;

use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;

/**
 * Sumber tunggal aturan kelulusan ujian komprehensif.
 *
 * Mahasiswa dinyatakan lulus jika seluruh CPL yang dinilai memiliki
 * nilai minimal 6.0 (skala 0-10). Nilai total tidak lagi menjadi syarat
 * karena total adalah rata-rata tertimbang dari seluruh CPL.
 */
class KompreResultService
{
    /** Jumlah CPL yang dipakai dalam satu sesi ujian. */
    public const EXPECTED_CPL_COUNT = 10;

    /** Jumlah soal per CPL pada sesi yang dihasilkan sistem. */
    public const QUESTIONS_PER_CPL = 10;

    /** Nilai minimum per CPL (skala 0-10). */
    public const CPL_MIN_SCORE = 6.0;

    /**
     * Hitung nilai setiap CPL untuk satu sesi.
     *
     * CPL yang tidak memiliki soal tetap disertakan dengan nilai 0
     * sehingga dianggap tidak lulus.
     *
     * @return array<int, array{cpl_id: int, kode: string, deskripsi: ?string, total: int, benar: int, nilai: float, lulus: bool}>
     */
    public function calculateCplScores(KompreSession $session): array
    {
        $jawabans = KompreJawaban::where('kompre_session_id', $session->id)
            ->with([
                'pertanyaan:id,cpl_id',
                'pertanyaan.cpl:id,kode,deskripsi',
                'opsiTerpilih:id,is_benar',
            ])
            ->get();

        $grouped = $jawabans->groupBy(fn (KompreJawaban $jawaban) => (int) $jawaban->pertanyaan?->cpl_id);

        $expectedCpls = Cpl::orderBy('kode')
            ->limit(self::EXPECTED_CPL_COUNT)
            ->get(['id', 'kode', 'deskripsi'])
            ->keyBy('id');

        // Sesi lama bisa memakai CPL di luar 10 CPL default; tetap sertakan.
        $extraCpls = $jawabans
            ->map(fn (KompreJawaban $jawaban) => $jawaban->pertanyaan?->cpl)
            ->filter()
            ->unique('id')
            ->keyBy('id');

        $allCpls = $expectedCpls->union($extraCpls)->sortBy('kode');

        $result = [];

        foreach ($allCpls as $cplId => $cpl) {
            $items = $grouped->get((int) $cplId, collect());
            $total = $items->count();
            $benar = $items->filter(
                fn (KompreJawaban $jawaban) => (bool) $jawaban->opsiTerpilih?->is_benar
            )->count();
            $nilai = $total > 0 ? round(($benar / $total) * 10, 2) : 0.0;

            $result[] = [
                'cpl_id' => (int) $cplId,
                'kode' => $cpl->kode,
                'deskripsi' => $cpl->deskripsi,
                'total' => $total,
                'benar' => $benar,
                'nilai' => $nilai,
                'lulus' => $total > 0 && $nilai >= self::CPL_MIN_SCORE,
            ];
        }

        return $result;
    }

    /**
     * Evaluasi kelulusan berdasarkan nilai per CPL.
     *
     * @param  array<int, array<string, mixed>>  $cplScores
     */
    public function isPassed(array $cplScores): bool
    {
        if (count($cplScores) === 0) {
            return false;
        }

        foreach ($cplScores as $cpl) {
            if ((float) ($cpl['nilai'] ?? 0) < self::CPL_MIN_SCORE) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{lulus: bool, cpl_scores: array<int, array<string, mixed>>, failed_cpls: array<int, array<string, mixed>>}
     */
    public function evaluate(KompreSession $session): array
    {
        $cplScores = $this->calculateCplScores($session);
        $failedCpls = array_values(array_filter(
            $cplScores,
            fn (array $cpl) => ! $cpl['lulus']
        ));

        return [
            'lulus' => $this->isPassed($cplScores),
            'cpl_scores' => $cplScores,
            'failed_cpls' => $failedCpls,
        ];
    }

    /**
     * Hitung dan simpan status kelulusan ke sesi.
     *
     * @return array{lulus: bool, cpl_scores: array<int, array<string, mixed>>, failed_cpls: array<int, array<string, mixed>>}
     */
    public function persist(KompreSession $session): array
    {
        $result = $this->evaluate($session);

        $session->update([
            'lulus' => $result['lulus'],
            'cpl_scores' => $result['cpl_scores'],
        ]);

        return $result;
    }
}
