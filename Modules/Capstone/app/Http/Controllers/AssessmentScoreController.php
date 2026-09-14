<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\AssessmentScore;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;

class AssessmentScoreController extends Controller
{
    /**
     * Get the assessment form: components + any existing scores for a group.
     * Used by Dosen to fill evaluations.
     */
    public function index(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN',
        ]);

        $lecturerId = CapstoneActor::lecturer($request->user())->id;
        $group = Group::with('members.student', 'period')->findOrFail($request->group_id);
        $this->authorizeEvaluator($group, $lecturerId);

        // Get components for this period + type
        $components = AssessmentComponent::where('period_id', $group->period_id)
            ->where('type', $request->type)
            ->orderBy('sort_order')
            ->get();

        // Get existing scores by this evaluator for this group
        $existingScores = AssessmentScore::where('evaluator_id', $lecturerId)
            ->where('group_id', $group->id)
            ->where('evaluation_type', $request->type)
            ->get()
            ->keyBy(function ($score) {
                return $score->component_id . '_' . $score->student_id;
            });

        return response()->json([
            'components' => $components,
            'existing_scores' => $existingScores,
            'group' => $group,
        ]);
    }

    /**
     * Submit batch scores for a group.
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'evaluation_type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN',
            'scores' => 'required|array|min:1',
            'scores.*.component_id' => 'required|exists:capstone_assessment_components,id',
            'scores.*.student_id' => 'nullable|exists:students,id',
            'scores.*.score' => 'required|numeric|min:0|max:100',
            'scores.*.notes' => 'nullable|string',
        ]);

        $lecturerId = CapstoneActor::lecturer($request->user())->id;
        $group = Group::findOrFail($request->group_id);
        $this->authorizeEvaluator($group, $lecturerId);
        $saved = [];

        foreach ($request->scores as $scoreData) {
            if (($scoreData['student_id'] ?? null) !== null) {
                abort_unless(
                    GroupMember::where('group_id', $group->id)
                        ->where('student_id', $scoreData['student_id'])
                        ->exists(),
                    422,
                    'Mahasiswa bukan anggota kelompok yang dinilai.'
                );
            }

            abort_unless(
                AssessmentComponent::whereKey($scoreData['component_id'])
                    ->where('period_id', $group->period_id)
                    ->where('type', $request->evaluation_type)
                    ->exists(),
                422,
                'Komponen penilaian tidak sesuai periode atau jenis evaluasi.'
            );

            $saved[] = AssessmentScore::updateOrCreate(
                [
                    'component_id' => $scoreData['component_id'],
                    'evaluator_id' => $lecturerId,
                    'student_id' => $scoreData['student_id'] ?? null,
                ],
                [
                    'group_id' => $request->group_id,
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? null,
                    'evaluation_type' => $request->evaluation_type,
                ]
            );
        }

        return response()->json(['message' => 'Scores submitted', 'count' => count($saved)], 201);
    }

    /**
     * Admin summary: aggregated scores per group/student for a period.
     */
    public function summary(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'type' => 'required|string|in:SEMPRO,SIDANG_TA,EXPO,BIMBINGAN',
        ]);

        $scores = AssessmentScore::with(['component', 'evaluator', 'student', 'group'])
            ->whereHas('group', fn($q) => $q->where('period_id', $request->period_id))
            ->where('evaluation_type', $request->type)
            ->get();

        // Group by group_id, then by student_id
        $grouped = $scores->groupBy('group_id')->map(function ($groupScores) {
            return $groupScores->groupBy('student_id')->map(function ($studentScores) {
                $totalWeighted = 0;
                $totalWeight = 0;

                foreach ($studentScores as $score) {
                    $weight = $score->component->weight;
                    $totalWeighted += $score->score * $weight;
                    $totalWeight += $weight;
                }

                return [
                    'student' => $studentScores->first()->student,
                    'scores' => $studentScores,
                    'weighted_avg' => $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : 0,
                ];
            });
        });

        return response()->json($grouped);
    }

    private function authorizeEvaluator(Group $group, int $lecturerId): void
    {
        $allowed = $group->supervisions()->where('supervisor_id', $lecturerId)->exists()
            || $group->seminarSchedules()
                ->where(fn ($query) => $query
                    ->where('examiner_1_id', $lecturerId)
                    ->orWhere('examiner_2_id', $lecturerId))
                ->exists()
            || $group->taDefenseSchedules()
                ->whereHas('examiners', fn ($query) => $query->where('examiner_id', $lecturerId))
                ->exists()
            || $group->title?->lecturer_id === $lecturerId;

        abort_unless($allowed, 403, 'Anda tidak ditugaskan untuk menilai kelompok ini.');
    }
}
