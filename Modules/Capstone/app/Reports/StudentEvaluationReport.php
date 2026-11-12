<?php

namespace Modules\Capstone\Reports;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Repositories\AssessmentScoreRepository;
use Modules\Capstone\Services\GradeCalculationService;

class StudentEvaluationReport
{
    use ApiResponseTrait;

    /**
     * Get student evaluation summary with all 8 evaluation types.
     */
    public function studentEvaluationsSummary(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'student_search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:group,name',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);
        $sortBy = $request->input('sort_by', 'group');
        $search = $request->input('student_search', '');

        $groupsQuery = Group::with(['members' => function ($query) {
            $query->with('student');
        }, 'title'])
            ->where('period_id', $periodId);

        $groups = $groupsQuery->get();

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

                $studentGroupPairs = [['student_id' => $student->id, 'group_id' => $group->id]];
                $pdc1Data = $gradeService->calculatePDC1ForStudentsBatch($studentGroupPairs)[$student->id] ?? null;
                $pdc2Data = $gradeService->calculatePDC2ForStudentsBatch($studentGroupPairs)[$student->id] ?? null;
                $taData = $gradeService->calculateSidangTAForStudentsBatch($studentGroupPairs)[$student->id] ?? null;

                $evaluations = $this->evaluateStudentEvaluations($pdc1Data, $pdc2Data, $taData);

                $bimbinganTaScores = AssessmentScoreRepository::forType('BIMBINGAN_TA')
                    ->where('student_id', $student->id)
                    ->where('group_id', $group->id)
                    ->with('periodComponent.template')
                    ->get();

                if ($bimbinganTaScores->count() > 0) {
                    $totalComponents = $bimbinganTaScores->count();
                    $scoredComponents = $bimbinganTaScores->whereNotNull('score')->count();
                    $avgScore = $bimbinganTaScores->whereNotNull('score')->avg('score');

                    $evaluations['BIMBINGAN_TA'] = [
                        'score' => $avgScore ? round($avgScore, 2) : null,
                        'status' => $scoredComponents === $totalComponents ? 'COMPLETE' : ($scoredComponents > 0 ? 'PARTIAL' : 'NOT_STARTED'),
                        'total_components' => $totalComponents,
                        'scored_components' => $scoredComponents,
                    ];
                }

                $students[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_nim' => $student->nim ?? '',
                    'group_id' => $group->id,
                    'group_name' => $group->code ?? "Group {$group->id}",
                    'evaluations' => $evaluations,
                ];
            }
        }

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

    /**
     * Calculate evaluation status from grade data.
     */
    private function calculateEvaluationStatus($data, string $type): array
    {
        if (! $data) {
            return [
                'score' => null,
                'status' => 'NOT_STARTED',
                'total_components' => 0,
                'scored_components' => 0,
            ];
        }

        // The components are keyed by type name in the data structure
        // e.g., ['SEMPRO' => ['score' => 85, 'evaluators' => [...]], ...]
        if (isset($data['components']) && is_array($data['components'])) {
            // Check if the specific evaluation type exists in components
            if (isset($data['components'][$type])) {
                $component = $data['components'][$type];
                $hasScore = isset($component['score']) && $component['score'] !== null;

                return [
                    'score' => $hasScore ? round($component['score'], 2) : null,
                    'status' => $hasScore ? 'COMPLETE' : 'NOT_STARTED',
                    'total_components' => 1,
                    'scored_components' => $hasScore ? 1 : 0,
                ];
            }
        }

        // Fallback: if there's a grade but no component breakdown
        $score = $data['grade'] ?? null;
        if ($score !== null) {
            return [
                'score' => round($score, 2),
                'status' => 'COMPLETE',
                'total_components' => 1,
                'scored_components' => 1,
            ];
        }

        return [
            'score' => null,
            'status' => 'NOT_STARTED',
            'total_components' => 0,
            'scored_components' => 0,
        ];
    }

    /**
     * Per-student evaluation map shared by the summary and the CSV export.
     */
    private function evaluateStudentEvaluations($pdc1Data, $pdc2Data, $taData): array
    {
        return [
            'SEMPRO' => $this->calculateEvaluationStatus($pdc1Data, 'SEMPRO'),
            'BIMBINGAN_SEMPRO' => $this->calculateEvaluationStatus($pdc1Data, 'BIMBINGAN_SEMPRO'),
            'SIDANG_TA' => $this->calculateEvaluationStatus($taData, 'SIDANG_TA'),
            'BIMBINGAN_TA' => ['score' => null, 'status' => 'NOT_STARTED', 'total_components' => 0, 'scored_components' => 0],
            'EXPO' => $this->calculateEvaluationStatus($pdc2Data, 'EXPO'),
            'MILESTONE' => $this->calculateEvaluationStatus($pdc2Data, 'MILESTONE'),
            'NILAI_DOSEN' => $this->calculateEvaluationStatus($pdc2Data, 'NILAI_DOSEN'),
        ];
    }

    /**
     * Get detailed breakdown for a specific student and evaluation type.
     */
    public function studentEvaluationDetail(Request $request, $studentId, $evaluationType)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

        $periodId = $request->period_id;

        $supportedTypes = AssessmentScoreRepository::getSupportedTypes();
        if (! in_array($evaluationType, $supportedTypes)) {
            return $this->errorResponse('Invalid evaluation type', 400);
        }

        $group = Group::where('period_id', $periodId)
            ->whereHas('members', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->with(['members' => function ($q) {
                $q->with('student');
            }, 'title'])
            ->first();

        if (! $group) {
            return $this->notFoundResponse('Student not found in this period');
        }

        $student = $group->members->firstWhere('student_id', $studentId)?->student;
        if (! $student) {
            return $this->notFoundResponse('Student not found');
        }

        $allComponents = PeriodAssessmentComponent::with('template')
            ->where('period_id', $periodId)
            ->where('type', $evaluationType)
            ->get()
            ->sortBy(fn ($c) => $c->template?->code ?? '')
            ->values();

        $scores = AssessmentScoreRepository::forType($evaluationType)
            ->where('student_id', $studentId)
            ->where('group_id', $group->id)
            ->with(['periodComponent.template', 'evaluator:id,user_id'])
            ->get();

        // EXPO special handling - simplified view with only combined score
        if ($evaluationType === 'EXPO') {
            $gradeService = app(GradeCalculationService::class);
            $pdc2Data = $gradeService->calculatePDC2ForStudent($studentId, $group->id);
            $expoData = $pdc2Data['components']['EXPO'] ?? null;

            $overallScore = $expoData['score'] ?? null;
            $status = $overallScore !== null ? 'COMPLETE' : 'NOT_STARTED';
            $lastEvaluated = null;

            foreach ($scores as $score) {
                if ($score->created_at && (! $lastEvaluated || $score->created_at > $lastEvaluated)) {
                    $lastEvaluated = $score->created_at;
                }
            }

            return $this->successResponse([
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim ?? '',
                    'group_id' => $group->id,
                    'group_name' => $group->code ?? "Group {$group->id}",
                ],
                'evaluation_type' => $evaluationType,
                'overall' => [
                    'score' => $overallScore,
                    'status' => $status,
                    'total_evaluators' => 0,
                    'completed_evaluators' => 0,
                    'last_evaluated_at' => $lastEvaluated ? $lastEvaluated->format('M d, Y') : null,
                ],
                'evaluators' => [], // No evaluator breakdown for EXPO
                'unassigned' => [
                    'components' => [],
                    'total' => 0,
                ],
            ]);
        }

        $evaluatorScores = [];
        $lastEvaluatedOverall = null;

        foreach ($scores as $score) {
            if ($score->created_at && (! $lastEvaluatedOverall || $score->created_at > $lastEvaluatedOverall)) {
                $lastEvaluatedOverall = $score->created_at;
            }

            $evaluatorId = null;
            if ($score->evaluator) {
                $evaluatorId = $score->evaluator->id;
            }

            if (! isset($evaluatorScores[$evaluatorId])) {
                $evaluatorScores[$evaluatorId] = [
                    'evaluator_id' => $evaluatorId,
                    'name' => $score->evaluator?->name ?? 'Unknown',
                    'role' => $score->evaluator?->role ?? 'Evaluator',
                    'components' => [],
                ];
            }

            $evaluatorScores[$evaluatorId]['components'][$score->periodComponent?->id ?? $score->component_id] = [
                'component_id' => $score->periodComponent?->id ?? $score->component_id,
                'score' => $score->score,
                'notes' => $score->notes,
                'evaluated_at' => $score->created_at ? $score->created_at->format('M d, Y') : null,
            ];
        }

        foreach ($evaluatorScores as $evaluatorId => &$evaluatorData) {
            if ($group->supervisor_1_id == $evaluatorId) {
                $evaluatorData['role'] = 'SUPERVISOR_1';
            } elseif ($group->supervisor_2_id == $evaluatorId) {
                $evaluatorData['role'] = 'SUPERVISOR_2';
            } else {
                $taSchedule = TaDefenseSchedule::where('group_id', $group->id)
                    ->where('status', '!=', 'CANCELLED')
                    ->whereHas('students', function ($query) use ($studentId) {
                        $query->where('student_id', $studentId);
                    })
                    ->first();

                if ($taSchedule) {
                    if ($taSchedule->examiner_1_id == $evaluatorId) {
                        $evaluatorData['role'] = 'EXAMINER_1';
                    } elseif ($taSchedule->examiner_2_id == $evaluatorId) {
                        $evaluatorData['role'] = 'EXAMINER_2';
                    }
                } else {
                    $seminarSchedule = SeminarSchedule::where('group_id', $group->id)
                        ->where('status', '!=', 'CANCELLED')
                        ->whereIn('type', ['SEMPRO', 'EXPO'])
                        ->first();

                    if ($seminarSchedule) {
                        if ($seminarSchedule->examiner_1_id == $evaluatorId) {
                            $evaluatorData['role'] = 'EXAMINER_1';
                        } elseif ($seminarSchedule->examiner_2_id == $evaluatorId) {
                            $evaluatorData['role'] = 'EXAMINER_2';
                        }
                    }
                }
            }
        }
        unset($evaluatorData); // Break reference

        $evaluators = [];
        $completedEvaluators = 0;

        foreach ($evaluatorScores as $evaluatorId => $evaluatorData) {
            $components = [];
            $scoredCount = 0;
            $totalWeighted = 0;
            $calculationBreakdown = [];

            $totalWeight = 0;
            foreach ($allComponents as $periodComponent) {
                $totalWeight += $periodComponent->template?->weight ?? 1;
            }

            foreach ($allComponents as $periodComponent) {
                $template = $periodComponent->template;
                $componentId = $periodComponent->id;
                $scoreData = $evaluatorData['components'][$componentId] ?? null;
                $normalizedWeight = $totalWeight > 0 ? round((($template?->weight ?? 1) / $totalWeight) * 100, 2) : 0;

                $componentData = [
                    'component_id' => $componentId,
                    'component_code' => $template?->code ?? 'N/A',
                    'component_name' => $template?->name ?? 'Unknown',
                    'weight' => $template?->weight ?? 1,
                    'normalized_weight' => $normalizedWeight,
                    'score' => $scoreData['score'] ?? null,
                    'notes' => $scoreData['notes'] ?? null,
                    'evaluated_at' => $scoreData['evaluated_at'] ?? null,
                ];

                $components[] = $componentData;

                if ($scoreData && $scoreData['score'] !== null) {
                    $scoredCount++;
                    $totalWeighted += $scoreData['score'] * $normalizedWeight;

                    $calculationBreakdown[] = [
                        'component' => $template?->name ?? 'Unknown',
                        'score' => $scoreData['score'],
                        'weight' => $normalizedWeight,
                        'weighted' => round($scoreData['score'] * $normalizedWeight / 100, 2),
                    ];
                } else {
                    $calculationBreakdown[] = [
                        'component' => $template?->name ?? 'Unknown',
                        'score' => 0,
                        'weight' => $normalizedWeight,
                        'weighted' => 0,
                    ];
                }
            }

            $finalScore = $scoredCount > 0 ? round($totalWeighted / 100, 2) : null;

            $totalComponents = count($allComponents);
            $evaluatorStatus = 'NOT_STARTED';
            if ($scoredCount === $totalComponents && $totalComponents > 0) {
                $evaluatorStatus = 'COMPLETE';
                $completedEvaluators++;
            } elseif ($scoredCount > 0) {
                $evaluatorStatus = 'PARTIAL';
            }

            $evaluators[] = [
                'evaluator_id' => $evaluatorId,
                'name' => $evaluatorData['name'],
                'role' => $evaluatorData['role'],
                'status' => $evaluatorStatus,
                'score' => $finalScore,
                'total_components' => $totalComponents,
                'scored_components' => $scoredCount,
                'components' => $components,
                'calculation_summary' => [
                    'formula' => 'Σ(score × normalized_weight) / 100',
                    'breakdown' => $calculationBreakdown,
                    'total_weight' => 100,
                    'weighted_sum' => $finalScore !== null ? round($totalWeighted / 100, 2) : 0,
                    'final_score' => $finalScore,
                ],
            ];
        }

        $evaluatorScores = array_filter(array_column($evaluators, 'score'), fn ($s) => $s !== null);
        $overallScore = count($evaluatorScores) > 0 ? round(array_sum($evaluatorScores) / count($evaluatorScores), 2) : null;

        $overallStatus = 'NOT_STARTED';
        $totalEvaluators = count($evaluators);

        if ($totalEvaluators === 0) {
            $overallStatus = 'NOT_STARTED';
        } elseif ($completedEvaluators === $totalEvaluators && $totalEvaluators > 0) {
            $overallStatus = 'COMPLETE';
        } elseif ($completedEvaluators > 0 || count(array_filter($evaluators, fn ($e) => $e['status'] === 'PARTIAL')) > 0) {
            $overallStatus = 'PARTIAL';
        }

        return $this->successResponse([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim ?? '',
                'group_id' => $group->id,
                'group_name' => $group->code ?? "Group {$group->id}",
            ],
            'evaluation_type' => $evaluationType,
            'overall' => [
                'score' => $overallScore,
                'status' => $overallStatus,
                'total_evaluators' => $totalEvaluators,
                'completed_evaluators' => $completedEvaluators,
                'last_evaluated_at' => $lastEvaluatedOverall ? $lastEvaluatedOverall->format('M d, Y') : null,
            ],
            'evaluators' => $evaluators,
            'unassigned' => [
                'components' => [],
                'total' => 0,
            ],
        ]);
    }

    /**
     * Export student evaluation summary to CSV.
     */
    public function exportStudentEvaluationsSummary(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'student_search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:group,name',
        ]);

        $periodId = $request->period_id;
        $sortBy = $request->input('sort_by', 'group');
        $search = $request->input('student_search', '');

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

                $studentGroupPairs = [['student_id' => $student->id, 'group_id' => $group->id]];
                $pdc1Data = $gradeService->calculatePDC1ForStudentsBatch($studentGroupPairs)[$student->id] ?? null;
                $pdc2Data = $gradeService->calculatePDC2ForStudentsBatch($studentGroupPairs)[$student->id] ?? null;
                $taData = $gradeService->calculateSidangTAForStudentsBatch($studentGroupPairs)[$student->id] ?? null;

                $evaluations = $this->evaluateStudentEvaluations($pdc1Data, $pdc2Data, $taData);

                $students[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_nim' => $student->nim ?? '',
                    'group_name' => $group->code ?? "Group {$group->id}",
                    'evaluations' => $evaluations,
                ];
            }
        }

        if ($sortBy === 'name') {
            usort($students, fn ($a, $b) => strcmp($a['student_name'], $b['student_name']));
        } else {
            usort($students, fn ($a, $b) => strcmp($a['group_name'], $b['group_name']) ?: strcmp($a['student_name'], $b['student_name']));
        }

        return CsvExporter::studentEvaluationsCsv($students);
    }

    /**
     * Get detailed breakdown for a specific evaluator.
     * Returns all components (scored and unscored) ordered by component code.
     */
    public function evaluatorDetail(Request $request, $studentId, $evaluationType, $evaluatorId)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

        $periodId = $request->period_id;

        $supportedTypes = AssessmentScoreRepository::getSupportedTypes();
        if (! in_array($evaluationType, $supportedTypes)) {
            return $this->errorResponse('Invalid evaluation type', 400);
        }

        $group = Group::where('period_id', $periodId)
            ->whereHas('members', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->with(['members' => function ($q) {
                $q->with('student');
            }, 'title'])
            ->first();

        if (! $group) {
            return $this->notFoundResponse('Student not found in this period');
        }

        $student = $group->members->firstWhere('student_id', $studentId)?->student;
        if (! $student) {
            return $this->notFoundResponse('Student not found');
        }

        $allComponents = PeriodAssessmentComponent::with('template')
            ->where('period_id', $periodId)
            ->where('type', $evaluationType)
            ->get()
            ->sortBy(fn ($c) => $c->template?->code ?? '')
            ->values();

        $usesExaminer = in_array($evaluationType, ['SEMPRO', 'SIDANG_TA']);

        // SEMPRO/SIDANG_TA scores key the evaluator by examiner_id, all other types by evaluator_id.
        $scoreQuery = AssessmentScoreRepository::forType($evaluationType)
            ->where('student_id', $studentId)
            ->where('group_id', $group->id);

        if ($usesExaminer) {
            $scoreQuery->where('examiner_id', $evaluatorId);
        } else {
            $scoreQuery->where('evaluator_id', $evaluatorId);
        }

        $allScores = $scoreQuery
            ->with(['periodComponent.template', 'evaluator:id,user_id'])
            ->get();

        // Create lookup maps for efficient matching
        $scoresByPeriodComponent = $allScores
            ->filter(fn ($s) => $s->period_component_id !== null)
            ->keyBy('period_component_id');

        $scoresByComponent = $allScores
            ->filter(fn ($s) => $s->component_id !== null)
            ->keyBy('component_id');

        // Find the evaluator info - use evaluator relationship (which is aliased in models with examiner_id)
        $evaluatorInfo = $allScores->first()?->evaluator;
        if (! $evaluatorInfo) {
            // Try to get evaluator info directly
            $evaluatorInfo = Lecturer::find($evaluatorId);
            if (! $evaluatorInfo) {
                return $this->notFoundResponse('Evaluator not found');
            }
        }

        // Resolve evaluator role
        $role = 'Evaluator';
        if ($group->supervisor_1_id == $evaluatorId) {
            $role = 'SUPERVISOR_1';
        } elseif ($group->supervisor_2_id == $evaluatorId) {
            $role = 'SUPERVISOR_2';
        } else {
            // Check if evaluator is an examiner for this student
            // Check TA defense schedules
            $taSchedule = TaDefenseSchedule::where('group_id', $group->id)
                ->where('status', '!=', 'CANCELLED')
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->first();

            if ($taSchedule) {
                if ($taSchedule->examiner_1_id == $evaluatorId) {
                    $role = 'EXAMINER_1';
                } elseif ($taSchedule->examiner_2_id == $evaluatorId) {
                    $role = 'EXAMINER_2';
                }
            } else {
                // Check seminar schedules (SEMPRO/EXPO)
                $seminarSchedule = SeminarSchedule::where('group_id', $group->id)
                    ->where('status', '!=', 'CANCELLED')
                    ->whereIn('type', ['SEMPRO', 'EXPO'])
                    ->first();

                if ($seminarSchedule) {
                    if ($seminarSchedule->examiner_1_id == $evaluatorId) {
                        $role = 'EXAMINER_1';
                    } elseif ($seminarSchedule->examiner_2_id == $evaluatorId) {
                        $role = 'EXAMINER_2';
                    }
                }
            }
        }

        // Build component list with all components (scored and unscored)
        $components = [];
        $scoredCount = 0;
        $totalScore = 0;
        $calculationBreakdown = [];
        $lastEvaluatedAt = null;

        // Calculate total weight first for normalized weight calculation
        $totalWeight = 0;
        foreach ($allComponents as $periodComponent) {
            $totalWeight += $periodComponent->template?->weight ?? 1;
        }

        // Track which scores have been assigned to prevent duplicates
        $assignedScoreIds = [];

        foreach ($allComponents as $periodComponent) {
            $template = $periodComponent->template;

            // Try to match score by period_component_id first, then by component_id
            $score = null;

            // Method 1: Match by period_component_id (preferred for future data)
            if ($scoresByPeriodComponent->has($periodComponent->id)) {
                $score = $scoresByPeriodComponent->get($periodComponent->id);
                $assignedScoreIds[] = $score->id;
            }
            // Method 2: Match by component_id using the template's component_id
            elseif ($template?->id && $scoresByComponent->has($template->id)) {
                $score = $scoresByComponent->get($template->id);
                $assignedScoreIds[] = $score->id;
            }
            // Method 3: For legacy data with NULL component_id, match by unassigned scores
            else {
                // Find first unassigned score that hasn't been used yet
                $unassignedScore = $allScores
                    ->filter(fn ($s) => $s->score !== null &&
                        ! in_array($s->id, $assignedScoreIds) &&
                        ($s->period_component_id === null || ! $scoresByPeriodComponent->has($s->period_component_id))
                    )
                    ->first();

                if ($unassignedScore) {
                    $score = $unassignedScore;
                    $assignedScoreIds[] = $score->id;
                }
            }

            $componentData = [
                'component_id' => $periodComponent->id,
                'component_code' => $template?->code ?? 'N/A',
                'component_name' => $template?->name ?? 'Unknown',
                'weight' => $template?->weight ?? 1,
                'normalized_weight' => $totalWeight > 0 ? round((($template?->weight ?? 1) / $totalWeight) * 100, 2) : 0,
                'score' => $score?->score,
                'notes' => $score?->notes,
                'evaluated_at' => $score?->created_at ? $score->created_at->format('M d, Y') : null,
            ];

            $components[] = $componentData;

            if ($score && $score->score !== null) {
                $scoredCount++;
                $totalScore += $score->score * ($template?->weight ?? 1);

                // Add to calculation breakdown
                $calculationBreakdown[] = [
                    'component' => $template?->name ?? 'Unknown',
                    'score' => $score->score,
                    'weight' => $template?->weight ?? 1,
                    'weighted' => round($score->score * ($template?->weight ?? 1) / 100, 2),
                ];

                if ($score->created_at && (! $lastEvaluatedAt || $score->created_at > $lastEvaluatedAt)) {
                    $lastEvaluatedAt = $score->created_at;
                }
            } else {
                // Add unscored component to breakdown with 0 contribution
                $calculationBreakdown[] = [
                    'component' => $template?->name ?? 'Unknown',
                    'score' => 0,
                    'weight' => $template?->weight ?? 1,
                    'weighted' => 0,
                ];
            }
        }

        // Calculate final score (totalWeight already calculated above)
        $finalScore = $scoredCount > 0 && $totalWeight > 0 ? round($totalScore / $totalWeight, 2) : null;

        // Determine status
        $totalComponents = count($allComponents);
        $status = 'NOT_STARTED';
        if ($scoredCount === $totalComponents && $totalComponents > 0) {
            $status = 'COMPLETE';
        } elseif ($scoredCount > 0) {
            $status = 'PARTIAL';
        }

        return $this->successResponse([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim ?? '',
                'group_id' => $group->id,
                'group_name' => $group->code ?? "Group {$group->id}",
            ],
            'evaluation_type' => $evaluationType,
            'evaluator' => [
                'evaluator_id' => $evaluatorId,
                'name' => $evaluatorInfo->name,
                'role' => $role,
                'status' => $status,
                'score' => $finalScore,
                'total_components' => $totalComponents,
                'scored_components' => $scoredCount,
                'components' => $components,
                'calculation_summary' => [
                    'formula' => 'Σ(score × weight) / Σ(weights)',
                    'breakdown' => $calculationBreakdown,
                    'total_weight' => $totalWeight,
                    'weighted_sum' => $finalScore !== null ? round($totalScore / 100, 2) : 0,
                    'final_score' => $finalScore,
                ],
                'last_evaluated_at' => $lastEvaluatedAt ? $lastEvaluatedAt->format('M d, Y') : null,
            ],
        ]);
    }
}
