<?php

namespace Modules\Capstone\Services;

use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Evaluations\SupervisorEvaluationPresenter;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Repositories\AssessmentScoreRepository;

/**
 * Supervisor evaluation write orchestration (Batch C).
 *
 * Moved verbatim from SupervisorEvaluationController: the controller keeps
 * lecturer resolution + response envelopes; every cross-row guard lives
 * here. Results use the controller's existing error-array idiom
 * (['error' => message, 'code' => http code] or the payload), so envelopes
 * are byte-identical. Unexpected model lookups (findOrFail) keep bubbling
 * to the framework handler, exactly as before.
 */
class SupervisorEvaluationService
{
    public function __construct(
        protected GroupLifecycleService $lifecycle,
        protected SupervisorEvaluationPresenter $presenter,
        protected GradeCalculationService $grades,
        protected NotificationService $notifications,
    ) {}

    /**
     * Approve or reject the group's EXPO submission (one decision per
     * group). APPROVED locks the submission and lets the EXPO score count
     * towards PDC2; REJECTED reopens it so the representative can re-score
     * and re-upload the document.
     *
     * @param  array<string, mixed>  $validated  Validated review data.
     * @return array<string, mixed> Payload or ['error' => ..., 'code' => ...].
     */
    public function reviewExpoRegistration(int $groupId, array $validated, int $lecturerId): array
    {
        $supervision = Supervision::where('group_id', $groupId)
            ->where('supervisor_id', $lecturerId)
            ->first();

        if (! $supervision) {
            return ['error' => 'You are not a supervisor of this group', 'code' => 403];
        }

        $group = Group::with('members')->findOrFail($groupId);
        $window = $this->lifecycle->formAccess($group, 'EXPO');

        if (! $window['editable']) {
            return ['error' => $window['reason'] ?? 'The EXPO submission cannot be reviewed in the current group status.', 'code' => 403];
        }

        $result = DB::transaction(function () use ($groupId, $validated, $lecturerId) {
            $registration = ExpoRegistration::where('group_id', $groupId)
                ->where('status', 'REGISTERED')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $registration) {
                return ['error' => 'No active expo registration for this group', 'code' => 404];
            }

            if ($registration->review_status !== 'SUBMITTED') {
                return ['error' => 'This submission is not awaiting review.', 'code' => 409];
            }

            if (! ExpoSelfEvaluation::where('expo_registration_id', $registration->id)->exists()) {
                return ['error' => 'The representative has not submitted a self-evaluation yet.', 'code' => 422];
            }

            $registration->update([
                'review_status' => $validated['status'],
                'review_feedback' => $validated['feedback'] ?? null,
                'reviewed_by' => $lecturerId,
                'reviewed_at' => now(),
            ]);

            return ['registration' => $registration->fresh()];
        });

        if (isset($result['error'])) {
            return $result;
        }

        $groupName = $group->name ?? "Group {$groupId}";
        $decision = $validated['status'] === 'APPROVED' ? 'approved' : 'rejected';
        $feedback = $validated['feedback'] ?? null;

        // Notify every member of the decision.
        try {
            $memberUserIds = GroupMember::where('group_id', $groupId)
                ->with('student')
                ->get()
                ->pluck('student.user_id')
                ->filter()
                ->unique()
                ->values();
            foreach ($memberUserIds as $memberUserId) {
                $this->notifications->send(
                    $memberUserId,
                    $validated['status'] === 'APPROVED' ? 'EXPO_APPROVED' : 'EXPO_REJECTED',
                    $validated['status'] === 'APPROVED' ? 'EXPO Submission Approved' : 'EXPO Submission Rejected',
                    $feedback
                        ? "Your EXPO submission for {$groupName} was {$decision} by your supervisor. Feedback: {$feedback}"
                        : "Your EXPO submission for {$groupName} was {$decision} by your supervisor.",
                    'Group',
                    $groupId
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send EXPO review notification: '.$e->getMessage());
        }

        // The EXPO score only counts towards PDC2 once approved.
        if ($validated['status'] === 'APPROVED') {
            try {
                $this->grades->recalculateAndNotify($groupId, 'EXPO');
            } catch (\Exception $e) {
                Log::error("Failed to recalculate PDC2 grades for group {$groupId}: ".$e->getMessage());
            }
        }

        // Centralized lifecycle advancement (idempotent; never blocks).
        try {
            $advancedTo = $this->lifecycle->advanceIfComplete($group);
            if ($advancedTo) {
                Log::info("Group {$groupId} advanced to {$advancedTo} after EXPO {$decision}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to advance lifecycle for group {$groupId}: ".$e->getMessage());
        }

        return [
            'decision' => $decision,
            'feedback' => $feedback,
            'review_status' => $validated['status'],
        ];
    }

    /**
     * Store supervisor evaluation scores (batch upsert).
     *
     * @param  array<string, mixed>  $validated  Validated submission data.
     * @return array<string, mixed> Payload or ['error' => ..., 'code' => ...].
     */
    public function submitEvaluationScores(array $validated, int $lecturerId): array
    {
        $lecturer = Lecturer::findOrFail($lecturerId);

        // Schema-aware validation - accept both field names for flexibility
        $usesPeriodComponents = $this->presenter->usesPeriodAssessmentComponents();
        $componentIdField = $usesPeriodComponents ? 'period_component_id' : 'component_id';
        $componentTable = $usesPeriodComponents ? 'capstone_period_assessment_components' : 'capstone_assessment_components';

        Log::info('SupervisorEvaluationService::submit', [
            'uses_period_components' => $usesPeriodComponents,
            'component_id_field' => $componentIdField,
            'component_table' => $componentTable,
        ]);

        // Ensure each score item has at least one component ID
        foreach ($validated['scores'] as $index => $score) {
            if (empty($score['period_component_id']) && empty($score['component_id'])) {
                return ['error' => "Score item {$index} is missing both period_component_id and component_id", 'code' => 422];
            }
        }

        $groupId = $validated['group_id'];
        $evaluationType = $validated['evaluation_type'];

        // Check if user is supervisor of this group
        $supervision = Supervision::where('group_id', $groupId)
            ->where('supervisor_id', $lecturerId)
            ->first();

        if (! $supervision) {
            return ['error' => 'You are not a supervisor of this group', 'code' => 403];
        }

        $group = Group::findOrFail($groupId);

        // Enforce the submittable lifecycle window (form() exposes the same
        // flag as `editable`, so legit clients never reach this branch).
        $formAccess = $this->lifecycle->formAccess($group, $evaluationType);

        if (! $formAccess['editable']) {
            return ['error' => $formAccess['reason'] ?? 'This evaluation is not submittable in the current group status.', 'code' => 403];
        }

        // Verify all students are members of this group
        $groupStudentIds = GroupMember::where('group_id', $groupId)->pluck('student_id')->toArray();
        foreach ($validated['scores'] as $score) {
            if (! in_array($score['student_id'], $groupStudentIds)) {
                return ['error' => 'Invalid student_id: '.$score['student_id'].' is not a member of this group', 'code' => 400];
            }
        }

        // Get schedule info for deadline tracking (deadline no longer blocks submission).
        // Schedule-less types (NILAI_DOSEN, MILESTONE) fall back to the
        // persisted soft deadline so late detection keeps working.
        $schedule = $this->presenter->getScheduleForType($groupId, $evaluationType);
        $deadline = $schedule['evaluation_deadline'] ?? null;
        if (! $deadline && in_array($evaluationType, ['NILAI_DOSEN', 'MILESTONE'], true)) {
            $deadline = $this->presenter->resolveSoftDeadline($group, $evaluationType);
        }
        $deadlinePassed = $deadline && now() > $deadline;

        DB::beginTransaction();
        try {
            // Prepare batch upsert data for better performance
            $upsertData = [];
            foreach ($validated['scores'] as $scoreData) {
                // Extract component ID from whichever field was provided
                $componentId = $scoreData['period_component_id'] ?? $scoreData['component_id'] ?? null;
                if (! $componentId) {
                    throw new \InvalidArgumentException('Missing component ID in score data');
                }

                $insertData = [
                    $componentIdField => $componentId,
                    'evaluator_id' => $lecturerId,
                    'group_id' => $groupId,
                    'student_id' => $scoreData['student_id'],
                    'evaluation_type' => $evaluationType,
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? null,
                ];

                // For legacy schema, also set period_component_id to same value if column exists
                if (! $usesPeriodComponents && Schema::hasColumn('capstone_assessment_scores', 'period_component_id')) {
                    $insertData['period_component_id'] = $componentId;
                }

                $upsertData[] = $insertData;
            }

            // Batch upsert all scores in a single query
            if (! empty($upsertData)) {
                $updateColumns = ['score', 'notes'];
                if (! $usesPeriodComponents && Schema::hasColumn('capstone_assessment_scores', 'period_component_id')) {
                    $updateColumns[] = 'period_component_id';
                }

                AssessmentScoreRepository::upsert(
                    $evaluationType,
                    $upsertData,
                    [$componentIdField, 'evaluator_id', 'group_id', 'student_id'],
                    $updateColumns
                );
            }

            DB::commit();

            // Check if all bimbingan evaluations are complete after this submission
            if (in_array($evaluationType, ['BIMBINGAN_SEMPRO', 'NILAI_DOSEN', 'MILESTONE'], true)) {
                $isComplete = $this->presenter->areAllBimbinganScoresComplete($groupId, $evaluationType);

                if ($isComplete) {
                    Log::info("All {$evaluationType} evaluations complete for group {$groupId}");

                    // Trigger grade recalculation
                    try {
                        $this->grades->recalculateAndNotify($groupId, $evaluationType);
                    } catch (\Exception $e) {
                        Log::error("Failed to recalculate grades for group {$groupId}: ".$e->getMessage());
                    }
                }
            }

            // Centralized lifecycle advancement (idempotent; never blocks the response)
            try {
                $advancedTo = $this->lifecycle->advanceIfComplete(Group::find($groupId));

                if ($advancedTo) {
                    Log::info("Group {$groupId} advanced to {$advancedTo} after {$evaluationType} submission");
                }
            } catch (\Exception $e) {
                Log::error("Failed to advance lifecycle for group {$groupId}: ".$e->getMessage());
            }

            // Send notification if deadline has passed
            if ($deadlinePassed) {
                try {
                    $groupName = $group->name ?? "Group {$groupId}";
                    $deadlineFormatted = $deadline ? date('d M Y H:i', strtotime($deadline)) : 'Unknown';

                    $evaluationName = match ($evaluationType) {
                        'BIMBINGAN_SEMPRO' => 'SEMPRO',
                        'NILAI_DOSEN' => 'Nilai Dosen Pembimbing',
                        'MILESTONE' => 'Milestone',
                        'BIMBINGAN_TA' => 'TA Defense',
                        default => $evaluationType,
                    };

                    $this->notifications->send(
                        $lecturer->user_id,
                        'EVALUATION_DEADLINE_PASSED',
                        'Evaluation Submitted After Deadline',
                        "Your evaluation for {$groupName} - {$evaluationName} was submitted after the deadline (due: {$deadlineFormatted}).",
                        'Group',
                        $groupId
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send deadline notification: '.$e->getMessage());
                }
            }

            return [
                'evaluation_type' => $evaluationType,
                'group_id' => $groupId,
                'all_complete' => $isComplete ?? null,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['error' => 'Failed to save evaluation: '.$e->getMessage(), 'code' => 500];
        }
    }
}
