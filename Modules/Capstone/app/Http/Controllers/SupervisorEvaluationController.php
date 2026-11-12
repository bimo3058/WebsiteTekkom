<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Capstone\Evaluations\SupervisorEvaluationPresenter;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\ReviewExpoRequest;
use Modules\Capstone\Http\Requests\SupervisorEvaluation\StoreSupervisorEvaluationRequest;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\Schedule;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Repositories\AssessmentScoreRepository;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Services\SchedulingService;
use Modules\Capstone\Services\SupervisorEvaluationService;
use Modules\Capstone\Support\CapstoneActor;

class SupervisorEvaluationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly SupervisorEvaluationService $evaluations,
    ) {}

    /**
     * Get evaluation form with components and existing scores
     */
    public function form(Request $request, int $groupId): JsonResponse
    {
        $user = CapstoneActor::lecturer(Auth::user());
        $evaluationType = $request->input('type'); // BIMBINGAN_SEMPRO, NILAI_DOSEN, MILESTONE, BIMBINGAN_TA

        // Validate evaluation type
        $validTypes = ['BIMBINGAN_SEMPRO', 'NILAI_DOSEN', 'MILESTONE', 'BIMBINGAN_TA'];
        if (! in_array($evaluationType, $validTypes)) {
            return $this->errorResponse('Invalid evaluation type', 400);
        }

        // Check if user is supervisor of this group
        $supervision = Supervision::where('group_id', $groupId)
            ->where('supervisor_id', $user->id)
            ->first();

        if (! $supervision) {
            return $this->unauthorizedResponse('You are not a supervisor of this group');
        }

        $group = Group::with(['members.student', 'period'])->findOrFail($groupId);

        $formAccess = app(GroupLifecycleService::class)->formAccess($group, $evaluationType);

        // Get components for this evaluation type (schema-aware)
        if (app(SupervisorEvaluationPresenter::class)->usesPeriodAssessmentComponents()) {
            $components = PeriodAssessmentComponent::with('template')
                ->where('period_id', $group->period_id)
                ->where('type', $evaluationType)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'template_id' => $c->template_id,
                    'code' => $c->template->code,
                    'name' => $c->template->name,
                    'description' => $c->template->description,
                    'weight' => $c->template->weight,
                    'sort_order' => $c->sort_order,
                ]);
            $existingScoresKeyField = 'period_component_id';
        } else {
            // Legacy schema
            $components = AssessmentComponent::where('period_id', $group->period_id)
                ->where('type', $evaluationType)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(fn ($c) => [
                    'id' => $c->id,
                    'template_id' => null,
                    'code' => $c->code ?? $c->name,
                    'name' => $c->name,
                    'description' => $c->description,
                    'weight' => $c->weight,
                    'sort_order' => $c->sort_order,
                ]);
            $existingScoresKeyField = 'component_id';
        }

        if ($components->isEmpty()) {
            return $this->errorResponse('No assessment components configured for this evaluation type', 400);
        }

        // Get existing scores
        $existingScores = AssessmentScoreRepository::forType($evaluationType)
            ->where('group_id', $groupId)
            ->where('evaluator_id', $user->id)
            ->get()
            ->keyBy(fn ($s) => $s->{$existingScoresKeyField}.'_'.($s->student_id ?? 'group'));

        // Build form structure - filter by student if specified (for per-student evaluations like BIMBINGAN_TA)
        $studentId = $request->input('student_id');
        $students = $group->members
            ->when($studentId, fn ($q) => $q->where('student_id', $studentId))
            ->map(function ($member) use ($components, $existingScores) {
                $student = $member->student;
                $scores = [];

                foreach ($components as $component) {
                    $key = $component['id'].'_'.$student->id;
                    $existing = $existingScores->get($key);

                    $scores[] = [
                        'period_component_id' => $component['id'],
                        'code' => $component['code'],
                        'name' => $component['name'],
                        'weight' => $component['weight'],
                        'score' => $existing ? $existing->score : null,
                        'notes' => $existing ? $existing->notes : null,
                    ];
                }

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim,
                    'is_leader' => $member->is_leader,
                    'scores' => $scores,
                ];
            });

        // NILAI_DOSEN and MILESTONE are schedule-less: scorable from
        // PDC2_ACTIVE onward, no EXPO schedule required.

        // Get schedule info
        $scheduleInfo = app(SupervisorEvaluationPresenter::class)->getScheduleForType($groupId, $evaluationType);

        $studentsArray = $students->values()->toArray();
        foreach ($studentsArray as &$studentRow) {
            $totalWeight = 0.0;
            $weightedSum = 0.0;
            $filled = 0;
            foreach ($studentRow['scores'] as $scoreRow) {
                if ($scoreRow['score'] !== null && $scoreRow['score'] !== '') {
                    $weight = (float) ($scoreRow['weight'] ?? 0);
                    $totalWeight += $weight;
                    $weightedSum += ((float) $scoreRow['score']) * $weight;
                    $filled++;
                }
            }
            $average = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null;
            $studentRow['average_score'] = $average;
            $studentRow['derived_result'] = $average === null ? null : ($average >= SchedulingService::PASS_THRESHOLD ? 'PASS' : 'FAIL');
            $studentRow['scores_complete'] = $filled === count($studentRow['scores']) && count($studentRow['scores']) > 0;
        }
        unset($studentRow);

        return $this->successResponse([
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'evaluation_type' => $evaluationType,
            'editable' => $formAccess['editable'],
            'editable_reason' => $formAccess['reason'],
            'group_status' => $formAccess['group_status'],
            'supervisor_role' => $supervision->role,
            'schedule' => $scheduleInfo,
            'components' => $components->toArray(),
            'students' => $studentsArray,
            'pass_threshold' => SchedulingService::PASS_THRESHOLD,
        ]);
    }

    /**
     * Group-level EXPO submission for review: the representative's
     * self-evaluation scores + supporting document + review state.
     * Read-only counterpart of form(); scoring via store() stays disabled
     * for EXPO — approval/rejection happens through reviewExpo().
     */
    public function expoReview(Request $request, int $groupId): JsonResponse
    {
        $user = CapstoneActor::lecturer(Auth::user());

        $supervision = Supervision::where('group_id', $groupId)
            ->where('supervisor_id', $user->id)
            ->first();

        if (! $supervision) {
            return $this->unauthorizedResponse('You are not a supervisor of this group');
        }

        $group = Group::with(['members.student', 'period'])->findOrFail($groupId);
        $registration = ExpoRegistration::where('group_id', $groupId)
            ->where('status', 'REGISTERED')
            ->orderByDesc('id')
            ->first();

        if (! $registration) {
            return $this->errorResponse('No active expo registration for this group', 404);
        }

        // Cross-period (global) events grade on the group's own period.
        $componentPeriodId = $registration->expoEvent?->period_id ?? $group->period_id;
        $components = PeriodAssessmentComponent::with('template')
            ->where('period_id', $componentPeriodId)
            ->where('type', 'EXPO')
            ->orderBy('sort_order')
            ->get();
        $scores = ExpoSelfEvaluation::where('expo_registration_id', $registration->id)->with('student')->get();
        $scoresByComponent = $scores->keyBy('period_component_id');
        $document = ExpoStudentDocument::where('expo_registration_id', $registration->id)->first();
        $submitter = $scores->first()?->student;

        $totalWeighted = 0.0;
        $totalWeight = 0.0;
        foreach ($components as $component) {
            $score = $scoresByComponent->get($component->id);
            if ($score !== null && $score->score !== null) {
                $weight = (float) ($component->weight ?? 1);
                $totalWeighted += $score->score * $weight;
                $totalWeight += $weight;
            }
        }
        $weighted = $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : null;

        $window = app(GroupLifecycleService::class)->formAccess($group, 'EXPO');

        return $this->successResponse([
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'period' => ['id' => $group->period->id, 'name' => $group->period->name],
            'supervisor_role' => $supervision->role,
            'registration' => [
                'id' => $registration->id,
                'registered_at' => $registration->registered_at,
                'review_status' => $registration->review_status,
                'review_feedback' => $registration->review_feedback,
                'reviewed_at' => $registration->reviewed_at,
            ],
            'submitted_by' => $submitter
                ? ['id' => $submitter->id, 'name' => $submitter->name, 'nim' => $submitter->nim]
                : null,
            'components' => $components->map(fn ($component) => [
                'period_component_id' => $component->id,
                'code' => $component->code,
                'name' => $component->name,
                'weight' => $component->weight,
                'score' => $scoresByComponent->get($component->id)?->score,
                'notes' => $scoresByComponent->get($component->id)?->notes,
            ])->values(),
            'weighted_score' => $weighted,
            'document' => $document
                ? [
                    'id' => $document->id,
                    'original_name' => $document->original_name,
                    'status' => $document->status,
                    'feedback' => $document->feedback,
                    'uploaded_by' => $document->student
                        ? ['id' => $document->student->id, 'name' => $document->student->name]
                        : null,
                    'created_at' => $document->created_at,
                    // Same route the bimbingan review already uses for expo-{id}.
                    'download_path' => "/dosen/documents/expo-{$document->id}/download",
                ]
                : null,
            'members' => $group->members->map(fn ($m) => [
                'id' => $m->student->id,
                'name' => $m->student->name,
                'nim' => $m->student->nim,
                'is_leader' => $m->is_leader,
                'is_representative' => $submitter !== null && $m->student->id === $submitter->id,
            ])->values(),
            'editable' => $registration->review_status === 'SUBMITTED' && (bool) $window['editable'],
            'editable_reason' => $registration->review_status === 'SUBMITTED'
                ? $window['reason']
                : 'The submission is not awaiting review.',
        ]);
    }

    /**
     * Approve or reject the group's EXPO submission (one decision per
     * group). APPROVED locks the submission and lets the EXPO score count
     * towards PDC2; REJECTED reopens it so the representative can re-score
     * and re-upload the document.
     */
    public function reviewExpo(ReviewExpoRequest $request, int $groupId): JsonResponse
    {
        $lecturer = CapstoneActor::lecturer(Auth::user());

        $result = $this->evaluations->reviewExpoRegistration($groupId, $request->validated(), $lecturer->id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse([
            'message' => "EXPO submission {$result['decision']} successfully",
            'group_id' => $groupId,
            'review_status' => $result['review_status'],
            'review_feedback' => $result['feedback'],
        ]);
    }

    /**
     * Store supervisor evaluation scores
     */
    public function store(StoreSupervisorEvaluationRequest $request): JsonResponse
    {
        $lecturer = CapstoneActor::lecturer(Auth::user());

        $result = $this->evaluations->submitEvaluationScores($request->validated(), $lecturer->id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse([
            'message' => 'Evaluation submitted successfully',
            'evaluation_type' => $result['evaluation_type'],
            'group_id' => $result['group_id'],
            'all_complete' => $result['all_complete'],
        ]);
    }
}
