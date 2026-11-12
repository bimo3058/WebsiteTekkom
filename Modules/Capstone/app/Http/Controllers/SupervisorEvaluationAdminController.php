<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Evaluations\SupervisorEvaluationPresenter;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\AdminScheduleSummaryRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Schedule;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Repositories\AssessmentScoreRepository;
use Modules\Capstone\Services\GradeCalculationService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupervisorEvaluationAdminController extends Controller
{
    use ApiResponseTrait;

    /**
     * Admin: Get evaluation summary for a schedule
     */
    public function adminScheduleSummary(AdminScheduleSummaryRequest $request, int $scheduleId): JsonResponse
    {
        $request->validated();
        $source = $request->input('schedule_source');
        // Explicit sources prevent collisions between the independent schedule tables.
        $schedule = in_array($source, [null, 'schedule'], true)
            ? Schedule::with(['group.period', 'group.members.student'])->find($scheduleId) : null;
        if (! $schedule && $source === 'schedule') {
            return $this->notFoundResponse('Schedule not found');
        }
        $isTaDefense = false;
        $isSeminar = false;
        $taSchedule = null;
        $seminarSchedule = null;

        if (! $schedule) {
            // Try TaDefenseSchedule for TA defense schedules
            $taSchedule = in_array($source, [null, 'ta'], true)
                ? TaDefenseSchedule::with(['group.period', 'group.members.student', 'student', 'students'])->find($scheduleId) : null;
            if (! $taSchedule && $source === 'ta') {
                return $this->notFoundResponse('Schedule not found');
            }
            if ($taSchedule) {
                $isTaDefense = true;
                $schedule = (object) [
                    'id' => $taSchedule->id,
                    'type' => 'TA_DEFENSE',
                    'date' => $taSchedule->date,
                    'room' => $taSchedule->room,
                ];
            } else {
                // Try SeminarSchedule for SEMPRO schedules
                $seminarSchedule = SeminarSchedule::with(['group.period', 'group.members.student'])->find($scheduleId);
                if ($seminarSchedule) {
                    $isSeminar = true;
                    $schedule = (object) [
                        'id' => $seminarSchedule->id,
                        'type' => $seminarSchedule->type === 'EXPO' ? 'EXPO' : 'SEMINAR',
                        'date' => $seminarSchedule->date,
                        'room' => $seminarSchedule->room,
                    ];
                } else {
                    return $this->notFoundResponse('Schedule not found');
                }
            }
        }

        if ($isTaDefense) {
            $group = $taSchedule->group;
        } elseif ($isSeminar) {
            $group = $seminarSchedule->group;
        } else {
            $group = $schedule->group;
        }

        // Determine evaluation types based on schedule type
        $evaluationTypes = match ($schedule->type) {
            'SEMINAR', 'SEMPRO' => ['SEMPRO', 'BIMBINGAN_SEMPRO'],
            'TA_DEFENSE' => ['SIDANG_TA', 'BIMBINGAN_TA'],
            'EXPO' => ['MILESTONE'],
            default => [],
        };

        $summary = [];

        // For TA_DEFENSE, only show the specific student (not all group members)
        if ($isTaDefense) {
            $students = $taSchedule->students->isNotEmpty() ? $taSchedule->students : collect([$taSchedule->student])->filter();
        } else {
            $students = $group->members->map(fn ($m) => $m->student)->filter();
        }

        foreach ($students as $student) {
            $studentScores = [];

            foreach ($evaluationTypes as $evalType) {
                if ($evalType === 'SIDANG_TA') {
                    // SIDANG_TA examiner evaluations are in ta_defense_evaluations table
                    $evaluations = TaDefenseEvaluation::with('examiner')
                        ->where('schedule_id', $scheduleId)
                        ->where('student_id', $student->id)
                        ->whereIn('status', ['SUBMITTED', 'COMPLETED'])
                        ->get();

                    if ($evaluations->isEmpty()) {
                        continue;
                    }

                    $byEvaluator = $evaluations->map(function ($eval) use ($scheduleId, $group, $student) {
                        $componentScores = AssessmentScoreRepository::forType('SIDANG_TA')
                            ->with('component')
                            ->where('group_id', $group->id)
                            ->where('student_id', $student->id)
                            ->where('examiner_id', $eval->examiner_id)
                            ->get()
                            ->map(fn ($score) => [
                                'component' => $score->component?->name ?? 'Unknown',
                                'score' => $score->score,
                                'weight' => $score->component?->weight ?? 0,
                            ]);

                        // Get examiner role
                        $examinerRecord = TaDefenseExaminer::where('schedule_id', $scheduleId)
                            ->where('examiner_id', $eval->examiner_id)
                            ->first();

                        return [
                            'evaluator' => [
                                'id' => $eval->examiner->id,
                                'name' => $eval->examiner->name,
                                'role' => $examinerRecord?->role ?? 'Examiner',
                            ],
                            'weighted_average' => $eval->score,
                            'scores' => $componentScores,
                        ];
                    })->values();

                    $studentScores[$evalType] = $byEvaluator;
                } elseif ($evalType === 'SEMPRO') {
                    // Get examiner role mapping from schedule
                    $seminarSchedule = SeminarSchedule::find($scheduleId);
                    $examinerRoles = [];
                    if ($seminarSchedule) {
                        if ($seminarSchedule->examiner_1_id) {
                            $examinerRoles[$seminarSchedule->examiner_1_id] = 'Examiner 1';
                        }
                        if ($seminarSchedule->examiner_2_id) {
                            $examinerRoles[$seminarSchedule->examiner_2_id] = 'Examiner 2';
                        }
                    }

                    // Read from sempro_scores via repository
                    $scores = AssessmentScoreRepository::forType('SEMPRO')
                        ->with(['component', 'examiner'])
                        ->where('group_id', $group->id)
                        ->where('student_id', $student->id)
                        ->get();

                    if ($scores->isEmpty()) {
                        continue;
                    }

                    $byEvaluator = $scores->groupBy('examiner_id')->map(function ($examinerScores, $examinerId) use ($examinerRoles) {
                        $examiner = $examinerScores->first()->examiner;

                        return [
                            'evaluator' => [
                                'id' => $examiner->id,
                                'name' => $examiner->name,
                                'role' => $examinerRoles[$examinerId] ?? 'Examiner',
                            ],
                            'weighted_average' => $examinerScores->avg('score'),
                            'scores' => $examinerScores->map(fn ($s) => [
                                'component' => $s->component?->name ?? 'Unknown',
                                'score' => $s->score,
                                'weight' => $s->component?->weight ?? 0,
                            ])->values(),
                        ];
                    })->values();

                    $studentScores[$evalType] = $byEvaluator;
                } else {
                    // BIMBINGAN_SEMPRO, BIMBINGAN_TA, EXPO, MILESTONE are in split score tables
                    $scores = AssessmentScoreRepository::forType($evalType)
                        ->with(['component', 'evaluator'])
                        ->where('group_id', $group->id)
                        ->where('student_id', $student->id)
                        ->get();

                    if ($scores->isEmpty()) {
                        continue;
                    }

                    // Group by evaluator
                    $byEvaluator = $scores->groupBy('evaluator_id')->map(function ($evaluatorScores) use ($group) {
                        $evaluator = $evaluatorScores->first()->evaluator;
                        $componentScores = $evaluatorScores->map(fn ($s) => [
                            'component' => $s->component?->name ?? 'Unknown',
                            'score' => $s->score,
                            'weight' => $s->component?->weight ?? 0,
                        ]);

                        $totalWeighted = $componentScores->sum(fn ($s) => $s['score'] * $s['weight']);
                        $totalWeight = $componentScores->sum('weight');
                        $weightedAvg = $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : 0;

                        return [
                            'evaluator' => [
                                'id' => $evaluator->id,
                                'name' => $evaluator->name,
                                'role' => app(SupervisorEvaluationPresenter::class)->getEvaluatorRole($evaluator->id, $group->id),
                            ],
                            'weighted_average' => $weightedAvg,
                            'scores' => $componentScores,
                        ];
                    })->values();

                    $studentScores[$evalType] = $byEvaluator;
                }
            }

            $summary[] = [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim,
                ],
                'scores' => $studentScores,
            ];
        }

        return $this->successResponse([
            'schedule' => [
                'id' => $schedule->id,
                'type' => $schedule->type,
                'date' => $schedule->date,
                'room' => $schedule->room,
            ],
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'summary' => $summary,
        ]);
    }

    /**
     * Admin: Export evaluation summary as CSV
     */
    public function exportScheduleSummary(Request $request, int $scheduleId): StreamedResponse
    {
        // Try Schedule table first, then TaDefenseSchedule for TA_DEFENSE
        $schedule = Schedule::with(['group.period', 'group.members.student', 'group.title'])->find($scheduleId);
        $isTaDefense = false;
        $taSchedule = null;

        if (! $schedule) {
            // Try TaDefenseSchedule for TA defense schedules
            $taSchedule = TaDefenseSchedule::with(['group.period', 'group.members.student', 'student'])->find($scheduleId);
            if (! $taSchedule) {
                return $this->notFoundResponse('Schedule not found');
            }
            $isTaDefense = true;
            $schedule = (object) [
                'id' => $taSchedule->id,
                'type' => 'TA_DEFENSE',
                'date' => $taSchedule->date,
                'room' => $taSchedule->room,
            ];
        }

        $group = $isTaDefense ? $taSchedule->group : $schedule->group;

        // Determine evaluation types based on schedule type
        $evaluationTypes = match ($schedule->type) {
            'SEMINAR' => ['SEMPRO', 'BIMBINGAN_SEMPRO'],
            'TA_DEFENSE' => ['SIDANG_TA', 'BIMBINGAN_TA'],
            'EXPO' => ['MILESTONE'],
            default => [],
        };

        $csvData = [];
        $headers = ['Student', 'NIM', 'Evaluation Type', 'Evaluator', 'Role', 'Component', 'Weight', 'Score', 'Weighted Average'];

        // For TA_DEFENSE, only export the specific student
        if ($isTaDefense) {
            $students = collect([$taSchedule->student]);
        } else {
            $students = $group->members->map(fn ($m) => $m->student);
        }

        foreach ($students as $student) {
            foreach ($evaluationTypes as $evalType) {
                if ($evalType === 'SIDANG_TA') {
                    // SIDANG_TA examiner evaluations are in ta_defense_evaluations table
                    $evaluations = TaDefenseEvaluation::with('examiner')
                        ->where('schedule_id', $scheduleId)
                        ->where('student_id', $student->id)
                        ->where('status', 'SUBMITTED')
                        ->get();

                    if ($evaluations->isEmpty()) {
                        continue;
                    }

                    foreach ($evaluations as $eval) {
                        $componentScores = AssessmentScoreRepository::forType('SIDANG_TA')
                            ->with('component')
                            ->where('group_id', $group->id)
                            ->where('student_id', $student->id)
                            ->where('examiner_id', $eval->examiner_id)
                            ->get()
                            ->map(fn ($score) => [
                                'component' => $score->component?->name ?? 'Unknown',
                                'score' => $score->score,
                                'weight' => $score->component?->weight ?? 0,
                            ]);

                        $examinerRecord = TaDefenseExaminer::where('schedule_id', $scheduleId)
                            ->where('examiner_id', $eval->examiner_id)
                            ->first();

                        $role = $examinerRecord?->role ?? 'Examiner';
                        $weightedAvg = $eval->score;

                        foreach ($componentScores as $cs) {
                            $csvData[] = [
                                $student->name,
                                $student->nim,
                                $evalType,
                                $eval->examiner->name,
                                $role,
                                $cs['component'],
                                $cs['weight'].'%',
                                $cs['score'],
                                $weightedAvg,
                            ];
                        }
                    }
                } else {
                    // BIMBINGAN_TA and other evaluations are in split score tables
                    $scores = AssessmentScoreRepository::forType($evalType)
                        ->with(['component', 'evaluator'])
                        ->where('group_id', $group->id)
                        ->where('student_id', $student->id)
                        ->get();

                    if ($scores->isEmpty()) {
                        continue;
                    }

                    // Group by evaluator
                    $byEvaluator = $scores->groupBy('evaluator_id');

                    foreach ($byEvaluator as $evaluatorId => $evaluatorScores) {
                        $evaluator = $evaluatorScores->first()->evaluator;
                        $role = app(SupervisorEvaluationPresenter::class)->getEvaluatorRole($evaluatorId, $group->id);

                        $componentScores = $evaluatorScores->map(fn ($s) => [
                            'component' => $s->component?->name ?? 'Unknown',
                            'weight' => $s->component?->weight ?? 0,
                            'score' => $s->score,
                        ]);

                        $totalWeighted = $componentScores->sum(fn ($s) => $s['score'] * $s['weight']);
                        $totalWeight = $componentScores->sum('weight');
                        $weightedAvg = $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : 0;

                        foreach ($componentScores as $cs) {
                            $csvData[] = [
                                $student->name,
                                $student->nim,
                                $evalType,
                                $evaluator->name,
                                $role,
                                $cs['component'],
                                $cs['weight'].'%',
                                $cs['score'],
                                $weightedAvg,
                            ];
                        }
                    }
                }
            }
        }

        $filename = "evaluation_summary_schedule_{$scheduleId}.csv";

        return response()->streamDownload(function () use ($headers, $csvData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Admin: Get grades for a schedule
     */
    public function getGradesForSchedule(Request $request, int $scheduleId): JsonResponse
    {
        // Try Schedule table first, then TaDefenseSchedule for TA_DEFENSE
        $schedule = Schedule::with(['group'])->find($scheduleId);
        $isTaDefense = false;

        if (! $schedule) {
            $taSchedule = TaDefenseSchedule::with(['group'])->find($scheduleId);
            if (! $taSchedule) {
                return $this->notFoundResponse('Schedule not found');
            }
            $isTaDefense = true;
            $group = $taSchedule->group;
        } else {
            $group = $schedule->group;
        }

        $grades = [];
        $gradeService = app(GradeCalculationService::class);

        // For TA_DEFENSE, only get grades for the specific student
        if ($isTaDefense) {
            $students = collect([$taSchedule->student]);
        } else {
            $students = $group->members->map(fn ($m) => $m->student);
        }

        foreach ($students as $student) {
            try {
                $finalGradeData = $gradeService->calculateFinalGradeForStudent($student->id, $group->id);
                $pdc1Data = $gradeService->calculatePDC1ForStudent($student->id, $group->id);
                $pdc2Data = $gradeService->calculatePDC2ForStudent($student->id, $group->id);

                $grades[$student->id] = [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nim' => $student->nim,
                    ],
                    'pdc1_score' => $pdc1Data['grade'] ?? null,
                    'pdc2_score' => $pdc2Data['grade'] ?? null,
                    'final_grade' => $finalGradeData['final_grade'] ?? null,
                    'letter_grade' => $finalGradeData['letter_grade'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::error('Failed to calculate grades for student in schedule summary', [
                    'schedule_id' => $scheduleId,
                    'student_id' => $student->id,
                    'exception' => $e,
                ]);
                $grades[$student->id] = [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'nim' => $student->nim,
                    ],
                    'error' => 'Failed to calculate grades. Please contact administrator if the issue persists.',
                ];
            }
        }

        return $this->successResponse([
            'schedule_id' => $scheduleId,
            'grades' => $grades,
        ]);
    }
}
