<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\AssessmentScore;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\EvaluationDeadline;

class SeminarDashboardController extends Controller
{
    /**
     * Student: my group's SEMPRO/Expo schedules + results.
     */
    public function studentSchedules(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;
        $membership = GroupMember::where('student_id', $studentId)->first();

        if (! $membership) {
            return response()->json(['data' => ['seminars' => [], 'ta_defense' => null]]);
        }

        // Cancelled rows are kept for audit history but hidden from cards;
        // the cancellation notification is the visible trace for both roles.
        $seminars = SeminarSchedule::with(['examiner1', 'examiner2', 'evaluations.examiner'])
            ->where('group_id', $membership->group_id)
            ->where('status', '!=', 'CANCELLED')
            ->get();

        $taDefense = TaDefenseSchedule::with(['examiners.examiner', 'evaluations.examiner'])
            ->where('student_id', $studentId)
            ->where('status', '!=', 'CANCELLED')
            ->latest('date')
            ->first();

        return response()->json([
            'data' => [
                'seminars' => $seminars,
                'ta_defense' => $taDefense,
            ],
        ]);
    }

    /**
     * Dosen: schedules where I'm a supervisor (read-only view).
     */
    public function supervisorSchedules(Request $request)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        // Groups I supervise
        $groupIds = Supervision::where('supervisor_id', $lecturerId)->pluck('group_id');

        $seminars = SeminarSchedule::with(['group.title', 'examiner1', 'examiner2', 'evaluations.examiner'])
            ->whereIn('group_id', $groupIds)
            ->where('status', '!=', 'CANCELLED')
            ->orderByDesc('date')
            ->get();

        $taDefenses = TaDefenseSchedule::with(['student', 'group.title', 'examiners.examiner', 'evaluations.examiner'])
            ->whereIn('group_id', $groupIds)
            ->where('status', '!=', 'CANCELLED')
            ->orderByDesc('date')
            ->get();

        return response()->json([
            'data' => [
                'seminars' => $seminars,
                'ta_defenses' => $taDefenses,
            ],
        ]);
    }

    /**
     * Dosen: schedules where I'm an examiner (can submit rubric).
     */
    public function examinerSchedules(Request $request)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        // Seminar schedules where I'm examiner
        $seminarScheduleIds = SeminarEvaluation::where('examiner_id', $lecturerId)
            ->pluck('schedule_id');

        $seminars = SeminarSchedule::with([
            'group.title',
            'group.members.student',
            'examiner1',
            'examiner2',
            'evaluations' => function ($q) use ($lecturerId) {
                $q->where('examiner_id', $lecturerId);
            },
        ])
            ->whereIn('id', $seminarScheduleIds)
            ->where('status', '!=', 'CANCELLED')
            ->orderByDesc('date')
            ->get();

        // TA defense schedules where I'm examiner
        $taScheduleIds = TaDefenseExaminer::where('examiner_id', $lecturerId)
            ->pluck('schedule_id');

        $taDefenses = TaDefenseSchedule::with([
            'student',
            'group.title',
            'group.members.student',
            'examiners.examiner',
            'evaluations' => function ($q) use ($lecturerId) {
                $q->where('examiner_id', $lecturerId);
            },
        ])
            ->whereIn('id', $taScheduleIds)
            ->where('status', '!=', 'CANCELLED')
            ->orderByDesc('date')
            ->get();

        return response()->json([
            'data' => [
                'seminars' => $seminars,
                'ta_defenses' => $taDefenses,
            ],
        ]);
    }

    /**
     * Get specific evaluation context (Seminar or TA Defense).
     */
    public function evaluationContext(Request $request, $type, $id)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        if ($type === 'SEMINAR') {
            $evaluation = SeminarEvaluation::where('id', $id)
                ->where('examiner_id', $lecturerId)
                ->firstOrFail();
            $schedule = SeminarSchedule::with(['group.title', 'group.members.student', 'examiner1', 'examiner2'])
                ->findOrFail($evaluation->schedule_id);
            $components = $this->resolveExaminerComponents($schedule->group->period_id, $schedule->type);
            $existingScores = $this->resolveExaminerScores($evaluation, $lecturerId, $schedule->group_id, $schedule->type);
            $deadline = $this->resolveScheduleDeadline($schedule);

            return response()->json([
                'evaluation' => $evaluation,
                'schedule' => $schedule,
                'group' => $schedule->group,
                'components' => $components,
                'existing_scores' => $existingScores,
                'result_editable' => $schedule->status !== 'COMPLETED',
                'schedule_status' => $schedule->status,
                'evaluation_deadline' => $deadline['evaluation_deadline'],
                'deadline_passed' => $deadline['deadline_passed'],
                'type' => 'SEMINAR',
            ]);
        }

        $evaluation = TaDefenseEvaluation::where('id', $id)
            ->where('examiner_id', $lecturerId)
            ->firstOrFail();
        $schedule = TaDefenseSchedule::with(['student', 'group.title', 'group.members.student', 'examiners.examiner'])
            ->findOrFail($evaluation->schedule_id);
        $components = $this->resolveExaminerComponents($schedule->group->period_id, 'SIDANG_TA');
        $existingScores = $this->resolveExaminerScores($evaluation, $lecturerId, $schedule->group_id, 'SIDANG_TA');
        $deadline = $this->resolveScheduleDeadline($schedule);

        return response()->json([
            'evaluation' => $evaluation,
            'schedule' => $schedule,
            'group' => $schedule->group,
            'components' => $components,
            'existing_scores' => $existingScores,
            'result_editable' => $schedule->status !== 'COMPLETED',
            'schedule_status' => $schedule->status,
            'evaluation_deadline' => $deadline['evaluation_deadline'],
            'deadline_passed' => $deadline['deadline_passed'],
            'type' => 'TA_DEFENSE',
        ]);
    }

    /**
     * Examiner evaluation deadline for a schedule.
     *
     * Prefers the stored evaluation_deadline (set at scheduling time);
     * falls back to schedule date + 2 days for legacy rows. Soft only —
     * a passed deadline flags the submission as late, never blocks it.
     *
     * @return array{evaluation_deadline: ?string, deadline_passed: bool}
     */
    private function resolveScheduleDeadline($schedule): array
    {
        $deadline = EvaluationDeadline::storedDeadline($schedule)
            ?? EvaluationDeadline::fromDate($schedule->date);

        return [
            'evaluation_deadline' => $deadline,
            'deadline_passed' => EvaluationDeadline::isPassed($deadline),
        ];
    }

    /**
     * Schema-aware component lookup for examiner forms.
     *
     * Periods configured with period assessment components (templates) take
     * precedence; legacy per-period components remain as fallback.
     */
    private function resolveExaminerComponents(int $periodId, string $type): array
    {
        if (Schema::hasTable('capstone_period_assessment_components')) {
            $components = PeriodAssessmentComponent::with('template')
                ->where('period_id', $periodId)
                ->where('type', $type)
                ->orderBy('sort_order')
                ->get();

            if ($components->isNotEmpty()) {
                return $components->map(fn ($component) => [
                    'id' => $component->id,
                    'code' => $component->template?->code,
                    'name' => $component->template?->name,
                    'description' => $component->template?->description,
                    'weight' => (float) $component->weight,
                    'sort_order' => $component->sort_order,
                ])->values()->all();
            }
        }

        return AssessmentComponent::where('period_id', $periodId)
            ->where('type', $type)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($component) => [
                'id' => $component->id,
                'code' => $component->code,
                'name' => $component->name,
                'description' => $component->description,
                'weight' => (float) $component->weight,
                'sort_order' => $component->sort_order,
            ])->values()->all();
    }

    /**
     * Hydrate per-component scores for examiner forms.
     *
     * Examiner scores are canonically stored in the evaluation's rubric_json;
     * legacy assessment-score rows (written by older dual-write submits) win
     * when present.
     */
    private function resolveExaminerScores($evaluation, int $lecturerId, int $groupId, string $type): array
    {
        $legacy = AssessmentScore::where('evaluator_id', $lecturerId)
            ->where('group_id', $groupId)
            ->where('evaluation_type', $type)
            ->get()
            ->keyBy(fn ($score) => $score->component_id.'_'.$score->student_id)
            ->map(fn ($score) => ['score' => $score->score, 'notes' => $score->notes])
            ->all();

        if ($legacy !== []) {
            return $legacy;
        }

        $rubric = $evaluation->rubric_json ?? [];
        $scores = $rubric['scores'] ?? [];
        $notes = $rubric['notes'] ?? [];
        $hydrated = [];

        foreach ($scores as $key => $score) {
            $hydrated[$key] = ['score' => $score, 'notes' => $notes[$key] ?? null];
        }

        return $hydrated;
    }
}
