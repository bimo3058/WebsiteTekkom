<?php

namespace Modules\Capstone\Services;

use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Repositories\AssessmentScoreRepository;

class GradeCalculationService
{
    /**
     * In-memory cache for batch data loading.
     * Used to eliminate N+1 query problems.
     */
    private array $batchCache = [];

    /**
     * Preload all period data into memory cache.
     * Call this ONCE before processing multiple students.
     * Reduces queries from 3,200+ to ~5 for 200 students.
     */
    public function preloadPeriodData(int $periodId): void
    {
        $this->clearCache();

        // 1. Get all group IDs for this period
        $groupIds = Group::where('period_id', $periodId)->pluck('id')->toArray();
        $this->batchCache['group_ids'] = $groupIds;

        // 2. Batch load ALL assessment scores with relationships from all supported types.
        // EXPO is skipped here: EXPO grades come from the members'
        // self-evaluations in daftar expo (see 2b), not the ExpoScore
        // split table, whose legacy rows are ignored.
        $allScores = collect();
        foreach (AssessmentScoreRepository::getSupportedTypes() as $type) {
            if ($type === 'EXPO') {
                continue;
            }
            $typeScores = AssessmentScoreRepository::forType($type)
                ->with(['evaluator:id,user_id', 'periodComponent.template'])
                ->whereIn('group_id', $groupIds)
                ->get();
            $allScores = $allScores->merge($typeScores);
        }

        foreach ($allScores as $score) {
            $key = "{$score->student_id}:{$score->group_id}:{$score->evaluation_type}";
            if (! isset($this->batchCache['assessment_scores'][$key])) {
                $this->batchCache['assessment_scores'][$key] = [];
            }
            $this->batchCache['assessment_scores'][$key][] = $score;
        }

        // 2b. Batch load EXPO self-evaluations (the representative member
        // write path in daftar expo). Pembimbing EXPO evaluation was removed,
        // so these rows are the sole source for EXPO grades — and only once
        // the group's supervisor review has APPROVED them (PDC2 gate).
        // Indexed twice: by student (own submission) and by group (so members
        // without their own scores inherit the representative's submission).
        $selfEvals = ExpoSelfEvaluation::with('periodComponent.template')
            ->whereIn('group_id', $groupIds)
            ->whereHas('expoRegistration', fn ($query) => $query->where('review_status', 'APPROVED'))
            ->get();
        foreach ($selfEvals as $eval) {
            $key = "{$eval->student_id}:{$eval->group_id}";
            if (! isset($this->batchCache['expo_self_evals'][$key])) {
                $this->batchCache['expo_self_evals'][$key] = [];
            }
            $this->batchCache['expo_self_evals'][$key][] = $eval;
            if (! isset($this->batchCache['expo_self_evals_by_group'][$eval->group_id])) {
                $this->batchCache['expo_self_evals_by_group'][$eval->group_id] = [];
            }
            $this->batchCache['expo_self_evals_by_group'][$eval->group_id][] = $eval;
        }

        // 3. Batch load ALL peer reviews
        $reviews = PeerReview::with(['periodIndicator.template'])
            ->whereIn('group_id', $groupIds)
            ->where('is_final_submission', true)
            ->get();

        foreach ($reviews as $review) {
            $key = "{$review->reviewee_id}:{$review->group_id}";
            if (! isset($this->batchCache['peer_reviews'][$key])) {
                $this->batchCache['peer_reviews'][$key] = [];
            }
            $this->batchCache['peer_reviews'][$key][] = $review;
        }

        // 4. Batch load ALL groups with supervisors
        $groups = Group::with(['supervisor1:id,user_id', 'supervisor2:id,user_id'])
            ->where('period_id', $periodId)
            ->get();

        $this->batchCache['groups'] = $groups->keyBy('id');

        // 5. Batch load examiner schedules for TA defense role resolution
        $defenseSchedules = TaDefenseSchedule::where('period_id', $periodId)
            ->where('status', '!=', 'CANCELLED')
            ->with('students:id')
            ->get();

        $this->batchCache['ta_defense_schedules'] = $defenseSchedules->keyBy('id');
        $this->batchCache['evaluator_roles'] = [];
        foreach ($defenseSchedules as $schedule) {
            foreach ($schedule->students as $student) {
                if ($schedule->examiner_1_id) {
                    $this->batchCache['evaluator_roles'][$schedule->group_id][$student->id][$schedule->examiner_1_id] = 'EXAMINER_1';
                }
                if ($schedule->examiner_2_id) {
                    $this->batchCache['evaluator_roles'][$schedule->group_id][$student->id][$schedule->examiner_2_id] = 'EXAMINER_2';
                }
            }
        }

        // 5b. Batch load seminar schedules (SEMPRO/EXPO) for examiner role resolution
        $seminarSchedules = SeminarSchedule::whereIn('group_id', $groupIds)
            ->where('status', '!=', 'CANCELLED')
            ->whereIn('type', ['SEMPRO', 'EXPO'])
            ->get();

        // Get group_id => student_ids mapping for seminar examiner roles
        $groupStudentMap = [];
        if (count($groupIds) > 0) {
            $members = GroupMember::whereIn('group_id', $groupIds)
                ->select('group_id', 'student_id')
                ->get();
            foreach ($members as $m) {
                $groupStudentMap[$m->group_id][] = $m->student_id;
            }
        }

        foreach ($seminarSchedules as $schedule) {
            $groupId = $schedule->group_id;
            $studentIds = $groupStudentMap[$groupId] ?? [];
            foreach ($studentIds as $studentId) {
                if ($schedule->examiner_1_id) {
                    $this->batchCache['evaluator_roles'][$groupId][$studentId][$schedule->examiner_1_id] = 'EXAMINER_1';
                }
                if ($schedule->examiner_2_id) {
                    $this->batchCache['evaluator_roles'][$groupId][$studentId][$schedule->examiner_2_id] = 'EXAMINER_2';
                }
            }
        }

        // 6. Pre-compute supervisor evaluator roles
        foreach ($groups as $group) {
            if ($group->supervisor_1_id) {
                $this->batchCache['evaluator_roles'][$group->id]['_group'][$group->supervisor_1_id] = 'SUPERVISOR_1';
            }
            if ($group->supervisor_2_id) {
                $this->batchCache['evaluator_roles'][$group->id]['_group'][$group->supervisor_2_id] = 'SUPERVISOR_2';
            }
        }

        // 7. Cache evaluator names from all loaded scores
        $evaluatorNames = [];
        if (! empty($this->batchCache['assessment_scores'])) {
            foreach ($this->batchCache['assessment_scores'] as $scores) {
                foreach ($scores as $score) {
                    $idField = (in_array($score->evaluation_type, ['SEMPRO', 'SIDANG_TA'])) ? 'examiner_id' : 'evaluator_id';
                    $evaluatorId = $score->{$idField};
                    if ($evaluatorId && $score->evaluator && ! isset($evaluatorNames[$evaluatorId])) {
                        $evaluatorNames[$evaluatorId] = $score->evaluator->name;
                    }
                }
            }
        }

        $this->batchCache['evaluator_names'] = $evaluatorNames;

        // 8. Batch load period components
        $components = PeriodAssessmentComponent::with('template')
            ->whereHas('period', fn ($q) => $q->where('id', $periodId))
            ->get();

        $this->batchCache['period_components'] = $components->keyBy('id');

        // 9. Batch load peer review indicators
        $indicators = PeriodPeerReviewIndicator::with('template')
            ->whereHas('period', fn ($q) => $q->where('id', $periodId))
            ->get();

        $this->batchCache['peer_review_indicators'] = $indicators->keyBy('id');
    }

    /**
     * Clear the batch cache to free memory.
     */
    public function clearCache(): void
    {
        $this->batchCache = [];
    }

    /**
     * Calculate PDC1 for multiple students using cached data.
     */
    public function calculatePDC1ForStudentsBatch(array $studentGroupPairs): array
    {
        $results = [];
        foreach ($studentGroupPairs as $pair) {
            $studentId = $pair['student_id'];
            $groupId = $pair['group_id'];
            $results[$studentId] = $this->calculatePDC1FromCache($studentId, $groupId);
        }

        return $results;
    }

    /**
     * Calculate PDC2 for multiple students using cached data.
     */
    public function calculatePDC2ForStudentsBatch(array $studentGroupPairs): array
    {
        $results = [];
        foreach ($studentGroupPairs as $pair) {
            $studentId = $pair['student_id'];
            $groupId = $pair['group_id'];
            $results[$studentId] = $this->calculatePDC2FromCache($studentId, $groupId);
        }

        return $results;
    }

    /**
     * Calculate SIDANG_TA for multiple students using cached data.
     */
    public function calculateSidangTAForStudentsBatch(array $studentGroupPairs): array
    {
        $results = [];
        foreach ($studentGroupPairs as $pair) {
            $studentId = $pair['student_id'];
            $groupId = $pair['group_id'];
            $results[$studentId] = $this->calculateSidangTAFromCache($studentId, $groupId);
        }

        return $results;
    }

    /**
     * Calculate PDC1 using cached data (no database queries).
     */
    private function calculatePDC1FromCache(int $studentId, int $groupId): ?array
    {
        $semproKey = "{$studentId}:{$groupId}:SEMPRO";
        $bimbinganKey = "{$studentId}:{$groupId}:BIMBINGAN_SEMPRO";

        $semproScores = $this->batchCache['assessment_scores'][$semproKey] ?? [];
        $bimbinganScores = $this->batchCache['assessment_scores'][$bimbinganKey] ?? [];

        $semproScore = $this->calculateWeightedAverageFromCache($semproScores);
        $bimbinganSemproScore = $this->calculateWeightedAverageFromCache($bimbinganScores);

        $hasSempro = $semproScore !== null;
        $hasBimbingan = $bimbinganSemproScore !== null;

        if (! $hasSempro && ! $hasBimbingan) {
            return null;
        }

        $scores = [];
        if ($hasSempro) {
            $scores[] = $semproScore;
        }
        if ($hasBimbingan) {
            $scores[] = $bimbinganSemproScore;
        }

        $average = ! empty($scores) ? (array_sum($scores) / count($scores)) : 0;

        return [
            'grade' => round($average, 2),
            'components' => [
                'SEMPRO' => [
                    'score' => $semproScore,
                    'evaluators' => $this->getEvaluatorsFromCache($studentId, $groupId, 'SEMPRO'),
                ],
                'BIMBINGAN_SEMPRO' => [
                    'score' => $bimbinganSemproScore,
                    'evaluators' => $this->getEvaluatorsFromCache($studentId, $groupId, 'BIMBINGAN_SEMPRO'),
                ],
            ],
            'component_count' => count($scores),
            'status' => count($scores) === 2 ? 'COMPLETE' : 'PARTIAL',
        ];
    }

    /**
     * Calculate PDC2 using cached data (no database queries).
     */
    private function calculatePDC2FromCache(int $studentId, int $groupId): ?array
    {
        $nilaiDosenScores = $this->getScoresFromCache($studentId, $groupId, 'NILAI_DOSEN');
        $milestoneScores = $this->getScoresFromCache($studentId, $groupId, 'MILESTONE');
        $peerReviewScores = $this->getPeerReviewsFromCache($studentId, $groupId);

        $nilaiDosenScore = $this->calculateWeightedAverageFromCache($nilaiDosenScores);
        $milestoneScore = $this->calculateWeightedAverageFromCache($milestoneScores);
        // EXPO comes from the members' self-evaluations, not the split table.
        $expoScore = $this->getExpoSelfScoreFromCache($studentId, $groupId);
        $peerReviewScore = $this->calculatePeerReviewAverageFromCache($peerReviewScores);

        $componentCount = 0;
        $totalScore = 0;

        if ($nilaiDosenScore !== null) {
            $componentCount++;
            $totalScore += $nilaiDosenScore;
        }
        if ($milestoneScore !== null) {
            $componentCount++;
            $totalScore += $milestoneScore;
        }
        if ($expoScore !== null) {
            $componentCount++;
            $totalScore += $expoScore;
        }
        if ($peerReviewScore !== null) {
            $componentCount++;
            $totalScore += $peerReviewScore;
        }

        if ($componentCount === 0) {
            return null;
        }

        $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

        return [
            'grade' => round($average, 2),
            'components' => [
                'NILAI_DOSEN' => [
                    'score' => $nilaiDosenScore,
                    'evaluators' => $this->getEvaluatorsFromCache($studentId, $groupId, 'NILAI_DOSEN'),
                ],
                'MILESTONE' => [
                    'score' => $milestoneScore,
                    'evaluators' => $this->getEvaluatorsFromCache($studentId, $groupId, 'MILESTONE'),
                ],
                'EXPO' => [
                    'score' => $expoScore,
                    'evaluators' => $this->getExpoSelfEvaluatorsFromCache($studentId, $groupId),
                ],
                'PEER_REVIEW' => [
                    'score' => $peerReviewScore,
                    'evaluators' => [['name' => 'Peers', 'role' => 'STUDENT', 'score' => $peerReviewScore]],
                ],
            ],
            'component_count' => $componentCount,
            'status' => $componentCount === 4 ? 'COMPLETE' : 'PARTIAL',
        ];
    }

    /**
     * Calculate SIDANG_TA using cached data (no database queries).
     */
    private function calculateSidangTAFromCache(int $studentId, int $groupId): ?array
    {
        $sidangScores = $this->getScoresFromCache($studentId, $groupId, 'SIDANG_TA');
        $sidangScore = $this->calculateWeightedAverageFromCache($sidangScores);

        if ($sidangScore === null) {
            return null;
        }

        return [
            'grade' => round($sidangScore, 2),
            'components' => [
                'SIDANG_TA' => [
                    'score' => $sidangScore,
                    'evaluators' => $this->getEvaluatorsFromCache($studentId, $groupId, 'SIDANG_TA'),
                ],
            ],
            'component_count' => 1,
            'status' => 'COMPLETE',
        ];
    }

    /**
     * Get assessment scores from cache.
     */
    private function getScoresFromCache(int $studentId, int $groupId, string $evaluationType): array
    {
        $key = "{$studentId}:{$groupId}:{$evaluationType}";

        return $this->batchCache['assessment_scores'][$key] ?? [];
    }

    /**
     * Get a student's EXPO self-evaluations from cache.
     * EXPO grades come from the group's approved self-evaluation submitted
     * by the representative member, not the ExpoScore split table. Members
     * without their own scores inherit the group's submission.
     *
     * @return array<int, ExpoSelfEvaluation>
     */
    private function getExpoSelfEvalsFromCache(int $studentId, int $groupId): array
    {
        $key = "{$studentId}:{$groupId}";
        $own = $this->batchCache['expo_self_evals'][$key] ?? [];
        if ($own !== []) {
            return $own;
        }

        return $this->batchCache['expo_self_evals_by_group'][$groupId] ?? [];
    }

    /**
     * Weighted EXPO average from cached self-evaluations.
     */
    private function getExpoSelfScoreFromCache(int $studentId, int $groupId): ?float
    {
        return $this->calculateWeightedAverageFromCache($this->getExpoSelfEvalsFromCache($studentId, $groupId));
    }

    /**
     * EXPO evaluators display: the representative member who submitted the
     * group's self-evaluation, with role SELF.
     */
    private function getExpoSelfEvaluatorsFromCache(int $studentId, int $groupId): array
    {
        $evals = $this->getExpoSelfEvalsFromCache($studentId, $groupId);
        if ($evals === []) {
            return [];
        }
        $submitterId = (int) reset($evals)->student_id;

        return [
            [
                'name' => $this->resolveStudentName($submitterId),
                'role' => 'SELF',
            ],
        ];
    }

    /**
     * EXPO evaluators with scores, matching getEvaluatorsWithScoresFromCache()
     * output shape for the reports page.
     */
    public function getExpoSelfEvaluatorsWithScoresFromCache(int $studentId, int $groupId): array
    {
        $evals = $this->getExpoSelfEvalsFromCache($studentId, $groupId);
        $score = $this->calculateWeightedAverageFromCache($evals);
        if ($score === null) {
            return [];
        }
        $submitterId = (int) reset($evals)->student_id;

        return [
            [
                'evaluator_id' => $submitterId,
                'name' => $this->resolveStudentName($submitterId),
                'role' => 'SELF',
                'score' => round($score, 2),
            ],
        ];
    }

    /**
     * Resolve a student's display name, memoized to avoid N+1 queries.
     */
    private function resolveStudentName(int $studentId): string
    {
        if (! array_key_exists($studentId, $this->batchCache['student_names'] ?? [])) {
            $student = Student::with('user:id,name')->find($studentId);
            $this->batchCache['student_names'][$studentId] = $student?->name ?? 'Unknown';
        }

        return $this->batchCache['student_names'][$studentId];
    }

    /**
     * Single-query EXPO self-evaluations for a student, limited to the
     * group's APPROVED supervisor review. Prefers the student's own rows and
     * falls back to the representative's (group-level) submission.
     *
     * @return Collection<int, ExpoSelfEvaluation>
     */
    private function approvedExpoSelfEvals(int $studentId, int $groupId)
    {
        $evals = ExpoSelfEvaluation::with('periodComponent.template')
            ->where('group_id', $groupId)
            ->whereHas('expoRegistration', fn ($query) => $query->where('review_status', 'APPROVED'))
            ->get();
        $own = $evals->where('student_id', $studentId)->values();

        return $own->isNotEmpty() ? $own : $evals->values();
    }

    /**
     * Single-query EXPO average from the approved group self-evaluation.
     * Returns null until the supervisor approves the submission.
     */
    private function getStudentExpoSelfScore(int $studentId, int $groupId): ?float
    {
        $flagService = app(StudentFlagService::class);
        if (! $flagService->canBeScored($studentId, $groupId)) {
            return null;
        }

        $evals = $this->approvedExpoSelfEvals($studentId, $groupId);

        if ($evals->isEmpty()) {
            return null;
        }

        return $this->calculateWeightedAverageFromScores($evals);
    }

    /**
     * Single-query EXPO evaluators display (the representative member, role SELF).
     */
    private function getExpoSelfEvaluatorsForStudent(int $studentId, int $groupId): array
    {
        $flagService = app(StudentFlagService::class);
        if (! $flagService->canBeScored($studentId, $groupId)) {
            return [];
        }

        $evals = $this->approvedExpoSelfEvals($studentId, $groupId);
        if ($evals->isEmpty()) {
            return [];
        }

        $score = $this->calculateWeightedAverageFromScores($evals);
        if ($score === null) {
            return [];
        }

        $submitterId = (int) ($evals->first()->student_id ?? $studentId);

        return [
            [
                'name' => $this->resolveStudentName($submitterId),
                'role' => 'SELF',
                'score' => round($score, 2),
            ],
        ];
    }

    /**
     * Get peer reviews from cache.
     */
    public function getPeerReviewsFromCache(int $studentId, int $groupId): array
    {
        $key = "{$studentId}:{$groupId}";

        return $this->batchCache['peer_reviews'][$key] ?? [];
    }

    /**
     * Calculate peer review average from cached reviews.
     * Same weight-then-fallback semantics as peerReviewWeightedAverage();
     * the indicator weight comes from the preloaded batch cache.
     */
    public function calculatePeerReviewAverageFromCache(array $reviews): ?float
    {
        if (empty($reviews)) {
            return null;
        }

        $rows = [];
        foreach ($reviews as $review) {
            $indicator = $this->batchCache['peer_review_indicators'][$review->period_indicator_id] ?? null;
            $rows[] = [
                'weight' => $indicator?->template?->weight !== null ? (float) $indicator->template->weight : null,
                'value' => $review->raw_score !== null ? (float) $review->raw_score * 25 : null,
                'plain' => $review->score ?? null,
            ];
        }

        return self::combinePeerRows($rows);
    }

    /**
     * Single home for the peer-review combination shape: indicator-weighted
     * average of converted values, falling back to the plain average when no
     * weight is usable. Every peer-review caller combines rows the same way.
     * Returns the unrounded average; callers keep their own rounding.
     *
     * @param  array<int, array{weight: float|null, value: float|null, plain: float|null}>  $rows
     */
    private static function combinePeerRows(array $rows): ?float
    {
        if (empty($rows)) {
            return null;
        }

        $totalWeighted = 0.0;
        $totalWeight = 0.0;
        $plains = [];

        foreach ($rows as $row) {
            if ($row['weight'] !== null && $row['value'] !== null) {
                $totalWeighted += $row['value'] * $row['weight'];
                $totalWeight += $row['weight'];
            }
            if ($row['plain'] !== null) {
                $plains[] = (float) $row['plain'];
            }
        }

        if ($totalWeight > 0) {
            return $totalWeighted / $totalWeight;
        }

        if ($plains !== []) {
            return array_sum($plains) / count($plains);
        }

        return null;
    }

    /**
     * Calculate weighted average from cached score objects.
     * Falls back to equal weighting (1.0) when period_component_id is NULL.
     */
    private function calculateWeightedAverageFromCache(array $scores): ?float
    {
        return self::weightedAverageOf($scores);
    }

    /**
     * Single home for the weighted-average shape: period-component template
     * weight preferred, component weight next, equal weighting (1.0) when no
     * component info is available. Every grade caller combines scores the
     * same way.
     *
     * @param  iterable<mixed>  $scores  Objects with `score` plus
     *                                   `periodComponent`/`component` relations.
     */
    private static function weightedAverageOf(iterable $scores): ?float
    {
        $totalWeighted = 0.0;
        $totalWeight = 0.0;
        $hasValidScores = false;

        foreach ($scores as $score) {
            $weight = self::scoreWeight($score);

            if ($weight !== null && $score->score !== null) {
                $totalWeighted += $score->score * $weight;
                $totalWeight += $weight;
                $hasValidScores = true;
            }
        }

        return $hasValidScores && $totalWeight > 0 ? ($totalWeighted / $totalWeight) : null;
    }

    /**
     * Resolve one score's weight: period-component template first, component
     * weight next, equal weighting (1.0) when neither is available.
     */
    private static function scoreWeight($score): ?float
    {
        $component = $score->periodComponent;
        if ($component && $component->template) {
            return (float) $component->template->weight;
        }

        if ($score->component) {
            return (float) ($score->component->weight ?? 1);
        }

        return 1.0;
    }

    /**
     * Get evaluators from cache.
     */
    private function getEvaluatorsFromCache(int $studentId, int $groupId, string $evaluationType): array
    {
        $scores = $this->getScoresFromCache($studentId, $groupId, $evaluationType);
        $evaluators = [];
        $seen = [];

        foreach ($scores as $score) {
            if ($score->evaluator) {
                $evaluatorId = $score->evaluator_id ?? $score->examiner_id;
                $role = $evaluatorId !== null
                    ? $this->getEvaluatorRoleFromCache($evaluatorId, $groupId, $studentId)
                    : 'EXAMINER';

                $key = $score->evaluator->id ?? $score->evaluator->name;
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $evaluators[] = [
                    'name' => $score->evaluator->name,
                    'role' => $role,
                ];
            }
        }

        return $evaluators;
    }

    /**
     * Get evaluators from cache including their per-evaluator weighted average score.
     * Falls back to equal weighting (1.0) when period_component_id is NULL.
     *
     * @return array<int, array{name: string, role: string, score: float|null}>
     */
    public function getEvaluatorsWithScoresFromCache(int $studentId, int $groupId, string $evaluationType): array
    {
        // EXPO comes from the members' self-evaluations, not the split table.
        if ($evaluationType === 'EXPO') {
            return $this->getExpoSelfEvaluatorsWithScoresFromCache($studentId, $groupId);
        }

        $scores = $this->getScoresFromCache($studentId, $groupId, $evaluationType);
        $evaluatorData = [];

        foreach ($scores as $score) {
            $idField = (in_array($evaluationType, ['SEMPRO', 'SIDANG_TA'])) ? 'examiner_id' : 'evaluator_id';
            $evaluatorId = $score->{$idField};
            if (! $evaluatorId) {
                continue;
            }

            $role = $this->getEvaluatorRoleFromCache($evaluatorId, $groupId, $studentId);

            if (! isset($evaluatorData[$evaluatorId])) {
                $evaluatorData[$evaluatorId] = [
                    'name' => $score->evaluator->name ?? ($this->batchCache['evaluator_names'][$evaluatorId] ?? 'Unknown'),
                    'role' => $role,
                    'total_weighted' => 0,
                    'total_weight' => 0,
                ];
            }

            $weight = self::scoreWeight($score);

            if ($weight !== null && $score->score !== null) {
                $evaluatorData[$evaluatorId]['total_weighted'] += $score->score * $weight;
                $evaluatorData[$evaluatorId]['total_weight'] += $weight;
            }
        }

        $evaluators = [];
        foreach ($evaluatorData as $evaluatorId => $data) {
            $score = $data['total_weight'] > 0 ? round($data['total_weighted'] / $data['total_weight'], 2) : null;
            $evaluators[] = [
                'evaluator_id' => $evaluatorId,
                'name' => $data['name'],
                'role' => $data['role'],
                'score' => $score,
            ];
        }

        return $evaluators;
    }

    /**
     * Get evaluator role from cache.
     */
    private function getEvaluatorRoleFromCache(int $evaluatorId, int $groupId, ?int $studentId = null): string
    {
        $groupRoles = $this->batchCache['evaluator_roles'][$groupId] ?? [];
        if (isset($groupRoles['_group'][$evaluatorId])) {
            return $groupRoles['_group'][$evaluatorId];
        }

        if ($studentId !== null && isset($groupRoles[$studentId][$evaluatorId])) {
            return $groupRoles[$studentId][$evaluatorId];
        }

        return 'EXAMINER';
    }

    /**
     * Calculate SIDANG_TA grade for a student (individual method).
     */
    public function calculateSidangTAForStudent(int $studentId, int $groupId): ?array
    {
        try {
            $sidangScore = $this->getStudentEvaluationScore($studentId, $groupId, 'SIDANG_TA');

            if ($sidangScore === null) {
                return null;
            }

            return [
                'grade' => round($sidangScore, 2),
                'components' => [
                    'SIDANG_TA' => [
                        'score' => $sidangScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'SIDANG_TA'),
                    ],
                ],
                'component_count' => 1,
                'status' => 'COMPLETE',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate SIDANG_TA for student {$studentId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate PDC1 grade for a student.
     * PDC1 = (SEMPRO_avg + BIMBINGAN_SEMPRO_avg) / 2
     * Returns 0-100 scale
     */
    public function calculatePDC1ForStudent(int $studentId, int $groupId): ?array
    {
        try {
            $semproScore = $this->getStudentEvaluationScore($studentId, $groupId, 'SEMPRO');
            $bimbinganSemproScore = $this->getStudentEvaluationScore($studentId, $groupId, 'BIMBINGAN_SEMPRO');

            // Check if any scores exist
            $hasScore = false;
            $componentCount = 0;
            $totalScore = 0;

            if ($semproScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $semproScore;
            }

            if ($bimbinganSemproScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $bimbinganSemproScore;
            }

            if (! $hasScore) {
                return null;
            }

            $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

            return [
                'grade' => round($average, 2),
                'components' => [
                    'SEMPRO' => [
                        'score' => $semproScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'SEMPRO'),
                    ],
                    'BIMBINGAN_SEMPRO' => [
                        'score' => $bimbinganSemproScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'BIMBINGAN_SEMPRO'),
                    ],
                ],
                'component_count' => $componentCount,
                'status' => $componentCount === 2 ? 'COMPLETE' : 'PARTIAL',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate PDC1 for student {$studentId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate PDC2 grade for a student.
     * PDC2 = (NILAI_DOSEN_avg + MILESTONE_avg + EXPO_avg + PEER_REVIEW_avg) / 4
     * Returns 0-100 scale
     */
    public function calculatePDC2ForStudent(int $studentId, int $groupId): ?array
    {
        try {
            $nilaiDosenScore = $this->getStudentEvaluationScore($studentId, $groupId, 'NILAI_DOSEN');
            $milestoneScore = $this->getStudentEvaluationScore($studentId, $groupId, 'MILESTONE');
            // EXPO comes from the members' self-evaluations, not the split table.
            $expoScore = $this->getStudentExpoSelfScore($studentId, $groupId);
            $peerReviewScore = $this->getStudentPeerReviewAverage($studentId, $groupId);

            // Check if any scores exist
            $hasScore = false;
            $componentCount = 0;
            $totalScore = 0;

            if ($nilaiDosenScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $nilaiDosenScore;
            }

            if ($milestoneScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $milestoneScore;
            }

            if ($expoScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $expoScore;
            }

            if ($peerReviewScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $peerReviewScore;
            }

            if (! $hasScore) {
                return null;
            }

            $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

            return [
                'grade' => round($average, 2),
                'components' => [
                    'NILAI_DOSEN' => [
                        'score' => $nilaiDosenScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'NILAI_DOSEN'),
                    ],
                    'MILESTONE' => [
                        'score' => $milestoneScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'MILESTONE'),
                    ],
                    'EXPO' => [
                        'score' => $expoScore,
                        'evaluators' => $this->getExpoSelfEvaluatorsForStudent($studentId, $groupId),
                    ],
                    'PEER_REVIEW' => [
                        'score' => $peerReviewScore,
                        'evaluators' => [['name' => 'Peers', 'role' => 'STUDENT', 'score' => $peerReviewScore]],
                    ],
                ],
                'component_count' => $componentCount,
                'status' => $componentCount === 4 ? 'COMPLETE' : 'PARTIAL',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate PDC2 for student {$studentId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate TA grade for a student.
     * TA = (BIMBINGAN_TA_avg + SIDANG_TA_avg) / 2
     * Returns 0-100 scale, or null if no scores exist.
     */
    public function calculateTAForStudent(int $studentId, int $groupId): ?array
    {
        try {
            $bimbinganTaScore = $this->getStudentEvaluationScore($studentId, $groupId, 'BIMBINGAN_TA');
            $sidangTaScore = $this->getStudentEvaluationScore($studentId, $groupId, 'SIDANG_TA');

            $hasScore = false;
            $componentCount = 0;
            $totalScore = 0;

            if ($bimbinganTaScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $bimbinganTaScore;
            }

            if ($sidangTaScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $sidangTaScore;
            }

            if (! $hasScore) {
                return null;
            }

            $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

            return [
                'grade' => round($average, 2),
                'components' => [
                    'BIMBINGAN_TA' => [
                        'score' => $bimbinganTaScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'BIMBINGAN_TA'),
                    ],
                    'SIDANG_TA' => [
                        'score' => $sidangTaScore,
                        'evaluators' => $this->getEvaluatorsForStudent($studentId, $groupId, 'SIDANG_TA'),
                    ],
                ],
                'component_count' => $componentCount,
                'status' => $componentCount === 2 ? 'COMPLETE' : 'PARTIAL',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate TA for student {$studentId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate final grade for a student.
     * Final = (PDC1 + PDC2) / 2
     * Returns 0-100 scale
     */
    public function calculateFinalGradeForStudent(int $studentId, int $groupId): ?array
    {
        $pdc1 = $this->calculatePDC1ForStudent($studentId, $groupId);
        $pdc2 = $this->calculatePDC2ForStudent($studentId, $groupId);
        $ta = $this->calculateTAForStudent($studentId, $groupId);

        if (! $pdc1 && ! $pdc2 && ! $ta) {
            return null;
        }

        $pdc1Grade = $pdc1['grade'] ?? null;
        $pdc2Grade = $pdc2['grade'] ?? null;

        $grades = [];
        if ($pdc1Grade !== null) {
            $grades[] = $pdc1Grade;
        }
        if ($pdc2Grade !== null) {
            $grades[] = $pdc2Grade;
        }

        $finalGrade = count($grades) > 0 ? (array_sum($grades) / count($grades)) : 0;

        return [
            'final_grade' => round($finalGrade, 2),
            'letter_grade' => $this->getLetterGrade($finalGrade),
            'pdc1' => $pdc1,
            'pdc2' => $pdc2,
            'ta' => $ta,
            'status' => ($pdc1 && $pdc2) ? 'COMPLETE' : 'PARTIAL',
        ];
    }

    /**
     * Get letter grade from numeric score.
     */
    public function getLetterGrade(float $score): string
    {
        return match (true) {
            $score >= 85 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            default => 'E',
        };
    }

    /**
     * Mean of the non-null inputs, rounded to 2 decimals.
     * Single home for the PDC1/PDC2/TA/Final combination shape so every
     * caller combines partial scores the same way.
     */
    public static function mean(?float ...$scores): ?float
    {
        $valid = array_filter($scores, fn ($s) => $s !== null);

        return ! empty($valid) ? round(array_sum($valid) / count($valid), 2) : null;
    }

    /**
     * Peer-review weighted average over raw review rows.
     * Single home for the weight-then-fallback-to-plain-average shape used
     * by report reads and CSV exports.
     *
     * @param  iterable<mixed>|null  $reviews
     */
    public static function peerReviewWeightedAverage($reviews): ?float
    {
        if ($reviews === null || count($reviews) === 0) {
            return null;
        }

        $average = self::combinePeerRows(self::peerRows($reviews));

        return $average === null ? null : round($average, 2);
    }

    /**
     * Adapt review rows to combiner input: indicator weight (null when
     * unknown), converted value (`raw_score * 25`, null when missing), and
     * the plain score used only when no weight is usable.
     *
     * @param  iterable<mixed>  $reviews
     * @return array<int, array{weight: float|null, value: float|null, plain: float|null}>
     */
    private static function peerRows(iterable $reviews): array
    {
        $rows = [];
        foreach ($reviews as $review) {
            $weight = $review->periodIndicator?->template?->weight;
            $rows[] = [
                'weight' => $weight !== null ? (float) $weight : null,
                'value' => $review->raw_score !== null ? (float) $review->raw_score * 25 : null,
                'plain' => $review->score ?? null,
            ];
        }

        return $rows;
    }

    /**
     * Get evaluation score for a specific student.
     */
    private function getStudentEvaluationScore(int $studentId, int $groupId, string $evaluationType): ?float
    {
        // Check if student is flagged from this period - flagged students cannot receive new scores
        $flagService = app(StudentFlagService::class);
        if (! $flagService->canBeScored($studentId, $groupId)) {
            return null;
        }

        if (! AssessmentScoreRepository::isSupportedType($evaluationType)) {
            return null;
        }

        $scores = AssessmentScoreRepository::forType($evaluationType)
            ->with(['periodComponent.template', 'component'])
            ->where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->get();

        if ($scores->isEmpty()) {
            return null;
        }

        return $this->calculateWeightedAverageFromScores($scores);
    }

    /**
     * Get evaluators and their individual scores for a student.
     */
    private function getEvaluatorsForStudent(int $studentId, int $groupId, string $evaluationType): array
    {
        if (! AssessmentScoreRepository::isSupportedType($evaluationType)) {
            return [];
        }

        $scores = AssessmentScoreRepository::forType($evaluationType)
            ->with('evaluator')
            ->where('student_id', $studentId)
            ->where('group_id', $groupId)
            ->get();

        // Group scores by evaluator and calculate average
        $evaluatorScores = [];

        foreach ($scores as $score) {
            // Handle different column names: SidangTaScore uses examiner_id, BimbinganTaScore uses evaluator_id
            $evaluatorId = $score->examiner_id ?? $score->evaluator_id;
            $evaluatorName = $score->evaluator->name ?? 'Unknown';
            $evaluatorRole = $this->getEvaluatorRole($evaluatorId, $groupId, $studentId);

            $key = $evaluatorId ?? $evaluatorName;

            if (! isset($evaluatorScores[$key])) {
                $evaluatorScores[$key] = [
                    'id' => $evaluatorId,
                    'name' => $evaluatorName,
                    'role' => $evaluatorRole,
                    'totalScore' => 0,
                    'count' => 0,
                ];
            }

            $evaluatorScores[$key]['totalScore'] += $score->score;
            $evaluatorScores[$key]['count']++;
        }

        // Build final evaluators array with averaged scores
        $evaluators = [];
        foreach ($evaluatorScores as $evaluatorData) {
            $avgScore = $evaluatorData['count'] > 0
                ? round($evaluatorData['totalScore'] / $evaluatorData['count'], 2)
                : null;

            $evaluators[] = [
                'name' => $evaluatorData['name'],
                'role' => $evaluatorData['role'],
                'score' => $avgScore,
            ];
        }

        return $evaluators;
    }

    /**
     * Get peer review average for a student.
     * Same weight-then-fallback semantics as peerReviewWeightedAverage():
     * indicator-weighted converted values first, plain average when no
     * weight is usable.
     */
    private function getStudentPeerReviewAverage(int $studentId, int $groupId): ?float
    {
        $reviews = PeerReview::with(['periodIndicator.template'])
            ->where('reviewee_id', $studentId)
            ->where('group_id', $groupId)
            ->where('is_final_submission', true)
            ->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        return self::combinePeerRows(self::peerRows($reviews));
    }

    /**
     * Get peer review average pooled across a group (one query, no per-member
     * loop). Same kernel as the student-level average, matching the pooled
     * semantics of the other group-level components.
     */
    private function getGroupPeerReviewAverage(int $groupId): ?float
    {
        $reviews = PeerReview::with(['periodIndicator.template'])
            ->where('group_id', $groupId)
            ->where('is_final_submission', true)
            ->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        return self::combinePeerRows(self::peerRows($reviews));
    }

    /**
     * Get evaluator role (Supervisor 1/2, Examiner 1/2, etc.).
     * For examiners, determines EXAMINER_1 vs EXAMINER_2 based on the student's schedule.
     */
    private function getEvaluatorRole(?int $evaluatorId, int $groupId, ?int $studentId = null): string
    {
        if ($evaluatorId === null) {
            return 'UNKNOWN';
        }

        $group = Group::with(['supervisor1', 'supervisor2'])->find($groupId);

        if (! $group) {
            return 'EVALUATOR';
        }

        if ($group->supervisor_1_id === $evaluatorId) {
            return 'SUPERVISOR_1';
        }

        if ($group->supervisor_2_id === $evaluatorId) {
            return 'SUPERVISOR_2';
        }

        // For examiners, check if this student has a TA defense schedule
        // to determine if this is EXAMINER_1 or EXAMINER_2
        if ($studentId !== null) {
            $schedule = TaDefenseSchedule::where('group_id', $groupId)
                ->where('status', '!=', 'CANCELLED')
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->orderByDesc('id')
                ->first();

            if ($schedule) {
                if ($schedule->examiner_1_id === $evaluatorId) {
                    return 'EXAMINER_1';
                }

                if ($schedule->examiner_2_id === $evaluatorId) {
                    return 'EXAMINER_2';
                }
            }
        }

        return 'EXAMINER';
    }

    /**
     * Calculate weighted average from a collection of scores.
     * Falls back to equal weighting (1.0) when no component weight is
     * available, mirroring calculateWeightedAverageFromCache().
     */
    private function calculateWeightedAverageFromScores($scores): ?float
    {
        if ($scores->isEmpty()) {
            return null;
        }

        return self::weightedAverageOf($scores);
    }

    /**
     * Legacy: Calculate PDC1 grade for a group (for backward compatibility).
     */
    public function calculatePDC1(int $groupId): ?array
    {
        try {
            $group = Group::with('members')->findOrFail($groupId);

            $semproScore = $this->getAverageEvaluationScore($groupId, 'SEMPRO');
            $bimbinganSemproScore = $this->getAverageEvaluationScore($groupId, 'BIMBINGAN_SEMPRO');

            $hasScore = false;
            $componentCount = 0;
            $totalScore = 0;

            if ($semproScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $semproScore;
            }

            if ($bimbinganSemproScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $bimbinganSemproScore;
            }

            if (! $hasScore) {
                return null;
            }

            $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

            return [
                'grade' => round($average, 2),
                'components' => [
                    'SEMPRO' => ['score' => $semproScore],
                    'BIMBINGAN_SEMPRO' => ['score' => $bimbinganSemproScore],
                ],
                'component_count' => $componentCount,
                'status' => $componentCount === 2 ? 'COMPLETE' : 'PARTIAL',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate PDC1 for group {$groupId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Legacy: Calculate PDC2 grade for a group (for backward compatibility).
     * Four components like the student-level path: NILAI_DOSEN, MILESTONE,
     * EXPO, and PEER_REVIEW pooled across the group.
     */
    public function calculatePDC2(int $groupId): ?array
    {
        try {
            $nilaiDosenScore = $this->getAverageEvaluationScore($groupId, 'NILAI_DOSEN');
            $milestoneScore = $this->getAverageEvaluationScore($groupId, 'MILESTONE');
            $expoScore = $this->getAverageEvaluationScore($groupId, 'EXPO');
            $peerReviewScore = $this->getGroupPeerReviewAverage($groupId);

            $hasScore = false;
            $componentCount = 0;
            $totalScore = 0;

            if ($nilaiDosenScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $nilaiDosenScore;
            }

            if ($milestoneScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $milestoneScore;
            }

            if ($expoScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $expoScore;
            }

            if ($peerReviewScore !== null) {
                $hasScore = true;
                $componentCount++;
                $totalScore += $peerReviewScore;
            }

            if (! $hasScore) {
                return null;
            }

            $average = $componentCount > 0 ? ($totalScore / $componentCount) : 0;

            return [
                'grade' => round($average, 2),
                'components' => [
                    'NILAI_DOSEN' => ['score' => $nilaiDosenScore],
                    'MILESTONE' => ['score' => $milestoneScore],
                    'EXPO' => ['score' => $expoScore],
                    'PEER_REVIEW' => ['score' => $peerReviewScore],
                ],
                'component_count' => $componentCount,
                'status' => $componentCount === 4 ? 'COMPLETE' : 'PARTIAL',
            ];
        } catch (\Exception $e) {
            Log::error("Failed to calculate PDC2 for group {$groupId}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Resolve phase weights for a period: admin-configured phase weights
     * from grade_configuration, falling back to the given defaults when
     * unconfigured. Unconfigured periods therefore grade exactly as the
     * legacy unweighted endpoints did (see weightedGroupPhaseGrade).
     *
     * @param  array<string, int|float>  $defaults
     * @return array<string, int|float>
     */
    public function phaseWeights(Period $period, string $phase, array $defaults): array
    {
        $configured = $period->grade_configuration[$phase] ?? null;

        return is_array($configured) ? $configured : $defaults;
    }

    /**
     * Weighted phase grade for a group (single weighted math for phase
     * grades). Each configured type contributes avg(scores) × weight / 100
     * over the types that have scores; missing types are reported, never
     * zero-filled. Types the score repository does not support are reported
     * as not evaluated, mirroring the grade endpoints.
     *
     * With an unconfigured period this is numerically identical to the
     * former controller math (same avgs, same weights, same rounding) —
     * pinned by PhaseWeightsTest with strict identity.
     *
     * Terminology: these are phase weights (admin-configured per phase),
     * not component weights (template weights in grade math) or indicator
     * weights (peer-review importance).
     *
     * @param  array<string, int|float>  $defaults
     * @return array{grades: array<string, array{status: string, average_score: ?float, weight: int|float, weighted_score: ?float}>, final: ?float, total_weight: int|float}
     */
    public function weightedGroupPhaseGrade(Group $group, string $phase, array $defaults): array
    {
        $period = $group->relationLoaded('period') && $group->period !== null
            ? $group->period
            : Period::findOrFail($group->period_id);

        $weights = $this->phaseWeights($period, $phase, $defaults);

        $grades = [];
        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($weights as $type => $weight) {
            // Check if type is supported by repository
            if (AssessmentScoreRepository::isSupportedType($type)) {
                $scores = AssessmentScoreRepository::getByGroupAndType($group->id, $type);
            } else {
                $scores = collect();
            }

            if ($scores->isEmpty()) {
                $grades[$type] = [
                    'status' => 'not_evaluated',
                    'average_score' => null,
                    'weight' => $weight,
                    'weighted_score' => null,
                ];

                continue;
            }

            // Calculate average score for this evaluation type
            $avgScore = $scores->avg('score');
            $weightedScore = ($avgScore * $weight) / 100;

            $grades[$type] = [
                'status' => 'evaluated',
                'average_score' => round($avgScore, 2),
                'weight' => $weight,
                'weighted_score' => round($weightedScore, 2),
            ];

            $totalWeightedScore += $weightedScore;
            $totalWeight += $weight;
        }

        return [
            'grades' => $grades,
            'final' => $totalWeight > 0 ? round($totalWeightedScore, 2) : null,
            'total_weight' => $totalWeight,
        ];
    }

    /**
     * Weighted phase grade for a student (per-student scores, e.g. TA).
     * Same math shape as the group variant; mirrors the former TA endpoint
     * (no repository-type guard there, so unsupported types bubble exactly
     * as they did).
     *
     * @param  array<string, int|float>  $defaults
     * @return array{grades: array<string, array{status: string, average_score: ?float, weight: int|float, weighted_score: ?float}>, final: ?float, total_weight: int|float}
     */
    public function weightedStudentPhaseGrade(int $studentId, Period $period, string $phase, array $defaults): array
    {
        $weights = $this->phaseWeights($period, $phase, $defaults);

        $grades = [];
        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($weights as $type => $weight) {
            $scores = AssessmentScoreRepository::getByStudentAndType($studentId, $type);

            if ($scores->isEmpty()) {
                $grades[$type] = [
                    'status' => 'not_evaluated',
                    'average_score' => null,
                    'weight' => $weight,
                    'weighted_score' => null,
                ];

                continue;
            }

            // Calculate average score for this evaluation type
            $avgScore = $scores->avg('score');
            $weightedScore = ($avgScore * $weight) / 100;

            $grades[$type] = [
                'status' => 'evaluated',
                'average_score' => round($avgScore, 2),
                'weight' => $weight,
                'weighted_score' => round($weightedScore, 2),
            ];

            $totalWeightedScore += $weightedScore;
            $totalWeight += $weight;
        }

        return [
            'grades' => $grades,
            'final' => $totalWeight > 0 ? round($totalWeightedScore, 2) : null,
            'total_weight' => $totalWeight,
        ];
    }

    /**
     * Legacy: Get average evaluation score for a group.
     */
    private function getAverageEvaluationScore(int $groupId, string $evaluationType): ?float
    {
        // EXPO comes from the group's approved self-evaluations, not the
        // split table (supervisor approval gates the grade).
        if ($evaluationType === 'EXPO') {
            $evals = ExpoSelfEvaluation::with('periodComponent.template')
                ->where('group_id', $groupId)
                ->whereHas('expoRegistration', fn ($query) => $query->where('review_status', 'APPROVED'))
                ->get();

            if ($evals->isEmpty()) {
                return null;
            }

            return $this->calculateWeightedAverageFromScores($evals);
        }

        if (! AssessmentScoreRepository::isSupportedType($evaluationType)) {
            return null;
        }

        $scores = AssessmentScoreRepository::forType($evaluationType)
            ->with(['periodComponent.template', 'component'])
            ->where('group_id', $groupId)
            ->get();

        if ($scores->isEmpty()) {
            return null;
        }

        return $this->calculateWeightedAverageFromScores($scores);
    }

    public function recalculateAndNotify(int $groupId, string $evaluationType): ?array
    {
        return match ($evaluationType) {
            'BIMBINGAN_SEMPRO' => $this->calculatePDC1($groupId),
            'NILAI_DOSEN', 'EXPO', 'MILESTONE' => $this->calculatePDC2($groupId),
            default => null,
        };
    }

    /**
     * Get grades using only the latest active period's scores.
     * Called when a student has been flagged in previous periods.
     */
    public function calculateGradesForLatestPeriod(int $studentId): ?array
    {
        $flagService = app(StudentFlagService::class);
        $latestPeriodId = $flagService->getLatestPeriodIdForStudent($studentId);

        if (! $latestPeriodId) {
            return null;
        }

        // Get the student's group in the latest period
        $latestGroup = Group::where('period_id', $latestPeriodId)
            ->whereHas('members', fn ($q) => $q->where('student_id', $studentId))
            ->first();

        if (! $latestGroup) {
            return null;
        }

        // Calculate final grade using only this group's scores
        return $this->calculateFinalGradeForStudent($studentId, $latestGroup->id);
    }
}
