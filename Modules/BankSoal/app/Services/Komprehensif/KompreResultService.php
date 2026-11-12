<?php

namespace Modules\BankSoal\Services\Komprehensif;

use Illuminate\Support\Collection;
use Modules\BankSoal\Models\Cpl;
use Modules\BankSoal\Models\Komprehensif\KompreJawaban;
use Modules\BankSoal\Models\Komprehensif\KompreSession;

/**
 * Sumber tunggal aturan kelulusan ujian komprehensif.
 *
 * Ada dua mode syarat kelulusan yang dikonfigurasi admin:
 * 1. cpl_all_min — lulus jika setiap CPL memperoleh nilai minimal 6.0.
 * 2. total_min   — lulus jika nilai total minimal 60.
 *
 * Aturan yang dipakai disimpan (snapshot) pada sesi saat ujian dimulai,
 * sehingga perubahan setting hanya berlaku untuk ujian berikutnya.
 */
class KompreResultService
{
    /** Jumlah CPL yang dipakai dalam satu sesi ujian. */
    public const EXPECTED_CPL_COUNT = 10;

    /** Jumlah soal per CPL pada sesi yang dihasilkan sistem. */
    public const QUESTIONS_PER_CPL = 10;

    /** Nilai minimum per CPL default (skala 0-10). */
    public const CPL_MIN_SCORE = 6.0;

    public function __construct(private ?KompreSettingService $settings = null)
    {
        $this->settings ??= new KompreSettingService;
    }

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
        return $this->calculateCplScoresFrom($this->loadJawabans($session));
    }

    private function loadJawabans(KompreSession $session): Collection
    {
        return KompreJawaban::where('kompre_session_id', $session->id)
            ->with([
                'pertanyaan:id,cpl_id',
                'pertanyaan.cpl:id,kode,deskripsi',
                'opsiTerpilih:id,is_benar',
            ])
            ->get();
    }

    /**
     * @param  Collection<int, KompreJawaban>  $jawabans
     * @return array<int, array{cpl_id: int, kode: string, deskripsi: ?string, total: int, benar: int, nilai: float, lulus: bool}>
     */
    private function calculateCplScoresFrom(Collection $jawabans): array
    {
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
                'lulus' => $total > 0 && $nilai >= $this->settings->cplMinScore(),
            ];
        }

        return $result;
    }

    /**
     * @param  Collection<int, KompreJawaban>  $jawabans
     */
    private function calculateScoreFrom(Collection $jawabans): float
    {
        $total = $jawabans->count();

        if ($total === 0) {
            return 0.0;
        }

        $benar = $jawabans->filter(
            fn (KompreJawaban $jawaban) => (bool) $jawaban->opsiTerpilih?->is_benar
        )->count();

        return round(($benar / $total) * 100, 2);
    }

    /**
     * Evaluasi kelulusan berdasarkan aturan yang berlaku.
     *
     * @param  array<int, array<string, mixed>>  $cplScores
     */
    public function isPassed(array $cplScores, ?float $score = null, ?string $rule = null): bool
    {
        $rule ??= $this->settings->passingRule();

        if ($rule === KompreSettingService::RULE_TOTAL) {
            return $score !== null && $score >= $this->settings->totalMinScore();
        }

        if (count($cplScores) === 0) {
            return false;
        }

        foreach ($cplScores as $cpl) {
            if ((float) ($cpl['nilai'] ?? 0) < $this->settings->cplMinScore()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{score: float, lulus: bool, cpl_scores: array<int, array<string, mixed>>, failed_cpls: array<int, array<string, mixed>>, passing_rule: string}
     */
    public function evaluate(KompreSession $session): array
    {
        $jawabans = $this->loadJawabans($session);
        $cplScores = $this->calculateCplScoresFrom($jawabans);
        $score = $this->calculateScoreFrom($jawabans);
        $passingRule = $session->passing_rule ?: $this->settings->passingRule();
        $failedCpls = array_values(array_filter(
            $cplScores,
            fn (array $cpl) => ! $cpl['lulus']
        ));

        return [
            'score' => $score,
            'lulus' => $this->isPassed($cplScores, $score, $passingRule),
            'cpl_scores' => $cplScores,
            'failed_cpls' => $failedCpls,
            'passing_rule' => $passingRule,
        ];
    }

    /**
     * Hitung dan simpan status kelulusan ke sesi.
     *
     * @return array{score: float, lulus: bool, cpl_scores: array<int, array<string, mixed>>, failed_cpls: array<int, array<string, mixed>>}
     */
    public function persist(KompreSession $session): array
    {
        $result = $this->evaluate($session);

        $session->update([
            'score' => $result['score'],
            'lulus' => $result['lulus'],
            'cpl_scores' => $result['cpl_scores'],
            'passing_rule' => $result['passing_rule'],
        ]);

        return $result;
    }
}
