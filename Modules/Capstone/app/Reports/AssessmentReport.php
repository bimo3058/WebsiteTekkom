<?php

namespace Modules\Capstone\Reports;

use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Repositories\AssessmentScoreRepository;
use Modules\Capstone\Services\GradeCalculationService;

class AssessmentReport
{
    use ApiResponseTrait;

    /**
     * Get detailed assessment scores data.
     */
    public function assessments(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'evaluation_type' => 'nullable|string',
            'group_id' => 'nullable|integer',
            'student_search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);

        $query = null;
        $first = true;
        foreach (AssessmentScoreRepository::getSupportedTypes() as $type) {
            $typeQuery = AssessmentScoreRepository::forType($type)
                ->with(['component', 'periodComponent.template', 'evaluator', 'student', 'group.title'])
                ->whereHas('group', function ($q) use ($periodId) {
                    $q->where('period_id', $periodId);
                });

            if ($first) {
                $query = $typeQuery;
                $first = false;
            } else {
                $query->union($typeQuery);
            }
        }

        if ($request->filled('evaluation_type')) {
            $query->where('evaluation_type', $request->evaluation_type);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('student_search')) {
            $search = $request->student_search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where(function ($student) use ($search) {
                    $student->where('student_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            });
        }

        $query->orderBy('created_at', 'desc');

        if ($request->input('format') === 'csv') {
            return CsvExporter::assessmentsCsv($query->get());
        }

        $scores = $query->paginate($perPage);

        // Transform data to ensure score is a number and component info is unified
        $data = collect($scores->items())->map(function ($score) {
            $score->score = (float) $score->score;
            // Create unified component display from either component or periodComponent
            if ($score->component) {
                $score->component_display = [
                    'name' => $score->component->name,
                    'code' => $score->component->code,
                ];
            } elseif ($score->periodComponent?->template) {
                $score->component_display = [
                    'name' => $score->periodComponent->template->name,
                    'code' => $score->periodComponent->template->code,
                ];
            } else {
                $score->component_display = null;
            }

            return $score;
        });

        return $this->envelopeResponse($data, [
            'meta' => [
                'current_page' => $scores->currentPage(),
                'last_page' => $scores->lastPage(),
                'per_page' => $scores->perPage(),
                'total' => $scores->total(),
            ],
        ]);
    }

    /**
     * Get student evaluation scores grouped by phase and evaluator for table views.
     */
    public function phaseEvaluatorScores(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'phase' => 'required|string|in:pdc1,pdc2,ta',
            'student_search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:group,name',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $phase = $request->phase;
        $perPage = $request->input('per_page', 50);
        $sortBy = $request->input('sort_by', 'group');
        $search = $request->input('student_search', '');

        $phaseTypes = match ($phase) {
            'pdc1' => ['SEMPRO', 'BIMBINGAN_SEMPRO'],
            'pdc2' => ['NILAI_DOSEN', 'MILESTONE', 'EXPO', 'PEER_REVIEW'],
            'ta' => ['SIDANG_TA', 'BIMBINGAN_TA'],
            default => [],
        };

        $groups = Group::with(['members' => function ($query) {
            $query->with('student');
        }, 'title'])
            ->where('period_id', $periodId)
            ->get();

        $gradeService = app(GradeCalculationService::class);
        $gradeService->preloadPeriodData($periodId);

        $students = [];
        foreach ($groups as $group) {
            foreach ($group->members as $member) {
                $student = $member->student;
                if (! $student) {
                    continue;
                }

                if ($search) {
                    $searchLower = strtolower($search);
                    $nameMatch = stripos(strtolower($student->name), $searchLower) !== false;
                    $nimMatch = stripos(strtolower($student->nim ?? ''), $searchLower) !== false;
                    if (! $nameMatch && ! $nimMatch) {
                        continue;
                    }
                }

                $studentId = $student->id;
                $groupId = $group->id;

                $evaluations = [];
                $overallComplete = 0;
                $overallTotal = 0;

                foreach ($phaseTypes as $type) {
                    if ($type === 'PEER_REVIEW') {
                        $peerReviewScores = $gradeService->getPeerReviewsFromCache($studentId, $groupId);
                        $hasScore = ! empty($peerReviewScores);
                        $score = $hasScore ? $gradeService->calculatePeerReviewAverageFromCache($peerReviewScores) : null;

                        $evaluations['PEER_REVIEW'] = [
                            'score' => $score !== null ? round($score, 2) : null,
                            'status' => $hasScore ? 'COMPLETE' : 'NOT_STARTED',
                            'evaluators' => [['evaluator_id' => null, 'name' => 'Peers', 'role' => 'PEER', 'score' => $score !== null ? round($score, 2) : null]],
                        ];

                        $overallTotal++;
                        if ($score !== null) {
                            $overallComplete++;
                        }

                        continue;
                    }

                    $evaluators = $gradeService->getEvaluatorsWithScoresFromCache($studentId, $groupId, $type);
                    $hasEvaluators = ! empty($evaluators);
                    $evaluatorScores = array_filter(array_column($evaluators, 'score'), fn ($s) => $s !== null);
                    $score = $hasEvaluators && ! empty($evaluatorScores)
                        ? round(array_sum($evaluatorScores) / count($evaluatorScores), 2)
                        : null;

                    $totalComponents = count($evaluators);
                    $completedComponents = count($evaluatorScores);

                    $status = 'NOT_STARTED';
                    if ($completedComponents === $totalComponents && $totalComponents > 0) {
                        $status = 'COMPLETE';
                    } elseif ($completedComponents > 0 || $hasEvaluators) {
                        $status = 'PARTIAL';
                    }

                    $evaluations[$type] = [
                        'score' => $score,
                        'status' => $status,
                        'evaluators' => $evaluators,
                    ];

                    $overallTotal++;
                    if ($status === 'COMPLETE') {
                        $overallComplete++;
                    }
                }

                $students[] = [
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'student_nim' => $student->nim ?? '',
                    'group_id' => $groupId,
                    'group_name' => $group->code ?? "Group {$groupId}",
                    'evaluations' => $evaluations,
                    'overall_status' => $overallTotal === 0 ? 'NOT_STARTED' : ($overallComplete === $overallTotal ? 'COMPLETE' : ($overallComplete > 0 || $overallTotal > 0 ? 'PARTIAL' : 'NOT_STARTED')),
                ];
            }
        }

        $gradeService->clearCache();

        if ($sortBy === 'name') {
            usort($students, fn ($a, $b) => strcmp($a['student_name'], $b['student_name']));
        } else {
            usort($students, fn ($a, $b) => strcmp($a['group_name'], $b['group_name']) ?: strcmp($a['student_name'], $b['student_name']));
        }

        $page = $request->input('page', 1);
        $total = count($students);
        $offset = ($page - 1) * $perPage;
        $paginatedStudents = array_slice($students, $offset, $perPage);

        return $this->envelopeResponse($paginatedStudents, [
            'meta' => [
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }
}
