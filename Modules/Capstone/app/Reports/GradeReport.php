<?php

namespace Modules\Capstone\Reports;

use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GradeCalculationService;

class GradeReport
{
    use ApiResponseTrait;

    /**
     * Get detailed final grades data.
     */
    public function finalGrades(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_id' => 'nullable|integer',
            'grade_range' => 'nullable|in:A,B,C,D,E',
            'status' => 'nullable|in:Complete,Incomplete',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);
        $gradeService = app(GradeCalculationService::class);

        // Preload period data for batch calculations
        $gradeService->preloadPeriodData($periodId);

        $groups = Group::where('period_id', $periodId)
            ->with(['title', 'members' => function ($query) {
                $query->with('student');
            }]);

        if ($request->filled('group_id')) {
            $groups->where('id', $request->group_id);
        }

        $groups = $groups->get();

        $studentGroupPairs = [];
        $studentInfo = [];

        foreach ($groups as $group) {
            foreach ($group->members as $member) {
                $student = $member->student;
                if (! $student) {
                    continue;
                }

                $studentGroupPairs[] = [
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                ];

                $studentInfo[$student->id] = [
                    'student' => $student,
                    'group' => $group,
                    'is_flagged' => $member->status === 'flagged' || $member->deleted_at !== null,
                ];
            }
        }

        $pdc1Results = $gradeService->calculatePDC1ForStudentsBatch($studentGroupPairs);
        $pdc2Results = $gradeService->calculatePDC2ForStudentsBatch($studentGroupPairs);
        $taResults = $gradeService->calculateSidangTAForStudentsBatch($studentGroupPairs);

        // Clear cache after calculations
        $gradeService->clearCache();

        $students = [];

        foreach ($studentGroupPairs as $pair) {
            $studentId = $pair['student_id'];
            $info = $studentInfo[$studentId];
            $student = $info['student'];
            $group = $info['group'];

            $pdc1Data = $pdc1Results[$studentId] ?? null;
            $pdc2Data = $pdc2Results[$studentId] ?? null;
            $taData = $taResults[$studentId] ?? null;

            $pdc1Score = $pdc1Data ? $pdc1Data['grade'] : null;
            $pdc2Score = $pdc2Data ? $pdc2Data['grade'] : null;
            $taScore = $taData ? $taData['grade'] : null;

            if ($pdc1Score === null && $pdc2Score === null && $taScore === null) {
                continue;
            }

            $availableScores = [];
            if ($pdc1Score !== null) {
                $availableScores[] = $pdc1Score;
            }
            if ($pdc2Score !== null) {
                $availableScores[] = $pdc2Score;
            }
            if ($taScore !== null) {
                $availableScores[] = $taScore;
            }

            $finalGrade = count($availableScores) > 0 ? array_sum($availableScores) / count($availableScores) : 0;

            $letterGrade = $gradeService->getLetterGrade($finalGrade);
            $isPDC1Complete = $pdc1Data && $pdc1Data['status'] === 'COMPLETE';
            $isPDC2Complete = $pdc2Data && $pdc2Data['status'] === 'COMPLETE';
            $isTAComplete = $taData && $taData['status'] === 'COMPLETE';
            $isComplete = $isPDC1Complete && $isPDC2Complete && $isTAComplete;

            // Apply filters
            if ($request->filled('grade_range') && $letterGrade !== $request->grade_range) {
                continue;
            }

            if ($request->filled('status')) {
                $requestedStatus = $request->status === 'Complete';
                if ($isComplete !== $requestedStatus) {
                    continue;
                }
            }

            $students[] = [
                'group_id' => $group->id,
                'group_name' => $group->code ?? "Group {$group->id}",
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_nim' => $student->nim ?? '',
                'pdc1_score' => $pdc1Score !== null ? round($pdc1Score, 2) : null,
                'pdc2_score' => $pdc2Score !== null ? round($pdc2Score, 2) : null,
                'ta_score' => $taScore !== null ? round($taScore, 2) : null,
                'pdc1_complete' => $isPDC1Complete,
                'pdc2_complete' => $isPDC2Complete,
                'ta_complete' => $isTAComplete,
                'is_flagged' => $info['is_flagged'],
            ];
        }

        usort($students, function ($a, $b) {
            return $a['student_name'] <=> $b['student_name'];
        });

        if ($request->input('format') === 'csv') {
            return CsvExporter::finalGradesCsv($students);
        }

        $total = count($students);
        $page = $request->input('page', 1);
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
     * Get grade consistency data.
     */
    public function gradeConsistency(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'status' => 'nullable|in:CONSISTENT,INCONSISTENT',
            'student_search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);
        $gradeService = app(GradeCalculationService::class);

        // Preload period data for batch calculations
        $gradeService->preloadPeriodData($periodId);

        $groups = Group::where('period_id', $periodId)
            ->with(['title', 'members' => function ($query) {
                $query->with('student');
            }])
            ->get();

        $studentGroupPairs = [];
        $studentInfo = [];

        foreach ($groups as $group) {
            foreach ($group->members as $member) {
                $student = $member->student;
                if (! $student) {
                    continue;
                }

                $studentGroupPairs[] = [
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                ];

                $studentInfo[$student->id] = [
                    'student' => $student,
                    'group' => $group,
                ];
            }
        }

        $pdc1Results = $gradeService->calculatePDC1ForStudentsBatch($studentGroupPairs);
        $pdc2Results = $gradeService->calculatePDC2ForStudentsBatch($studentGroupPairs);

        // Clear cache after calculations
        $gradeService->clearCache();

        $students = [];

        foreach ($studentGroupPairs as $pair) {
            $studentId = $pair['student_id'];
            $info = $studentInfo[$studentId];
            $student = $info['student'];
            $group = $info['group'];

            $pdc1Data = $pdc1Results[$studentId] ?? null;
            $pdc2Data = $pdc2Results[$studentId] ?? null;

            $pdc1Score = $pdc1Data ? $pdc1Data['grade'] : 0;
            $pdc2Score = $pdc2Data ? $pdc2Data['grade'] : 0;

            if ($pdc1Score > 0 && $pdc2Score > 0) {
                $deviation = abs($pdc1Score - $pdc2Score);
                $isConsistent = $deviation <= 15;

                $status = $isConsistent ? 'CONSISTENT' : 'INCONSISTENT';

                if ($request->filled('status') && $status !== $request->status) {
                    continue;
                }

                if ($request->filled('student_search')) {
                    $search = strtolower($request->student_search);
                    if (! str_contains(strtolower($student->name), $search) &&
                        ! str_contains(strtolower($student->nim ?? ''), $search)) {
                        continue;
                    }
                }

                $students[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->code ?? "Group {$group->id}",
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'pdc1_score' => round($pdc1Score, 2),
                    'pdc2_score' => round($pdc2Score, 2),
                    'deviation' => round($deviation, 2),
                    'is_consistent' => $isConsistent,
                    'status' => $status,
                    'created_at' => now()->toDateTimeString(),
                ];
            }
        }

        usort($students, function ($a, $b) {
            return $b['deviation'] <=> $a['deviation'];
        });

        if ($request->input('format') === 'csv') {
            return CsvExporter::gradeConsistencyCsv($students);
        }

        $total = count($students);
        $page = $request->input('page', 1);
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
