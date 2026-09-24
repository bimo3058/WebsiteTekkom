<?php

namespace Modules\Capstone\Services;

use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Location;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Support\EvaluationDeadline;

class SchedulingService
{
    protected GroupStateMachine $stateMachine;

    public function __construct(GroupStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    // ══════════════════════════════════════════
    // Examiner Constraint Validation
    // ══════════════════════════════════════════

    /**
     * Validate examiner constraints for scheduling.
     * - Examiners must be dosen
     * - Examiners must not be supervisors of the group
     * - No duplicate examiners
     * - Minimum 2 examiners
     *
     * @return string|null Error message, or null if valid.
     */
    public function validateExaminerConstraints(Group $group, array $examinerIds): ?string
    {
        // Minimum 2
        if (count($examinerIds) < 2) {
            return 'Minimum 2 examiners required.';
        }

        // No duplicates
        if (count($examinerIds) !== count(array_unique($examinerIds))) {
            return 'Duplicate examiner IDs are not allowed.';
        }

        // All must be dosen
        foreach ($examinerIds as $examinerId) {
            $lecturer = Lecturer::whereKey($examinerId)
                ->whereHas('user.roles', fn ($query) => $query->where('name', 'dosen'))
                ->first();
            if (! $lecturer) {
                return "Examiner ID {$examinerId} must be a dosen.";
            }
        }

        // Examiner ≠ Supervisor (check both supervisor_1_id/supervisor_2_id
        // columns AND the capstone_supervisions pivot table, which is the
        // source of truth per Group::isSupervisedBy()).
        $supervisorIds = array_filter([
            $group->supervisor_1_id,
            $group->supervisor_2_id,
        ]);
        try {
            $pivotIds = $group->supervisions()->pluck('supervisor_id')->all();
            $supervisorIds = array_merge($supervisorIds, $pivotIds);
        } catch (\Throwable $e) {
            // Relation may be unavailable in some contexts; fall back to columns.
        }
        $supervisorIds = array_unique(array_map('intval', array_filter($supervisorIds)));
        $overlap = array_intersect(array_map('intval', $examinerIds), $supervisorIds);
        if (!empty($overlap)) {
            return 'Examiner cannot be the same as the group supervisor.';
        }

        return null;
    }

    // ══════════════════════════════════════════
    // Double-Booking & Room Conflict Checks
    // ══════════════════════════════════════════

    /**
     * Check if an examiner has an overlapping schedule on the given date/time range.
     * Queries BOTH seminar_schedules and ta_defense_schedules.
     * Filtered to non-CANCELLED schedules only.
     *
     * @return array|null The conflicting schedule info, or null if no conflict.
     */
    public function checkDoubleBooking(
        int $examinerId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeSeminarId = null,
        ?int $excludeTaDefenseId = null
    ): ?array {
        // Check seminar_schedules (examiner as examiner_1 or examiner_2)
        $seminarConflict = SeminarSchedule::where('date', $date)
            ->where('status', '!=', 'CANCELLED')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->where(function ($q) use ($examinerId) {
                $q->where('examiner_1_id', $examinerId)
                    ->orWhere('examiner_2_id', $examinerId);
            })
            ->when($excludeSeminarId, fn ($q) => $q->where('id', '!=', $excludeSeminarId))
            ->first();

        if ($seminarConflict) {
            return [
                'type' => 'seminar',
                'schedule' => $seminarConflict,
                'message' => "Examiner has a conflicting {$seminarConflict->type} schedule on {$date} ({$seminarConflict->start_time}-{$seminarConflict->end_time})",
            ];
        }

        // Check ta_defense_schedules via ta_defense_examiners
        $taConflict = TaDefenseSchedule::where('date', $date)
            ->where('status', '!=', 'CANCELLED')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->whereHas('examiners', fn ($q) => $q->where('examiner_id', $examinerId))
            ->when($excludeTaDefenseId, fn ($q) => $q->where('id', '!=', $excludeTaDefenseId))
            ->first();

        if ($taConflict) {
            return [
                'type' => 'ta_defense',
                'schedule' => $taConflict,
                'message' => "Examiner has a conflicting TA defense schedule on {$date} ({$taConflict->start_time}-{$taConflict->end_time})",
            ];
        }

        return null; // No conflict
    }

    /**
     * Check if a room has an overlapping schedule on the given date/time range.
     * Checks internal Capstone schedules AND EOffice bookings (disetujui +
     * internal academic schedules) so a room already taken in EOffice cannot
     * be selected from Capstone.
     */
    public function checkRoomConflict(
        string $room,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeSeminarId = null,
        ?int $excludeTaDefenseId = null,
        ?int $locationId = null,
        ?int $excludeEofficePeminjamanId = null,
        ?int $eofficeId = null
    ): ?array {
        if (empty($room) && ! $eofficeId) {
            return null;
        }

        if (! empty($room)) {
            $seminarConflict = SeminarSchedule::where('room', $room)
                ->where('date', $date)
                ->where('status', '!=', 'CANCELLED')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($excludeSeminarId, fn ($q) => $q->where('id', '!=', $excludeSeminarId))
                ->first();

            if ($seminarConflict) {
                return [
                    'type' => 'seminar',
                    'message' => "Room '{$room}' is already booked for {$seminarConflict->type} on {$date} ({$seminarConflict->start_time}-{$seminarConflict->end_time})",
                ];
            }

            $taConflict = TaDefenseSchedule::where('room', $room)
                ->where('date', $date)
                ->where('status', '!=', 'CANCELLED')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($excludeTaDefenseId, fn ($q) => $q->where('id', '!=', $excludeTaDefenseId))
                ->first();

            if ($taConflict) {
                return [
                    'type' => 'ta_defense',
                    'message' => "Room '{$room}' is already booked for TA defense on {$date} ({$taConflict->start_time}-{$taConflict->end_time})",
                ];
            }
        }

        $eofficeConflict = app(EofficeAvailabilityService::class)->checkByRoom(
            $room,
            $locationId,
            $date,
            $startTime,
            $endTime,
            $excludeEofficePeminjamanId,
            $eofficeId
        );

        if ($eofficeConflict) {
            return [
                'type' => 'eoffice',
                'message' => $eofficeConflict['message'],
            ];
        }

        return null;
    }

    /**
     * Validate all examiners for conflicts. Returns array of errors or empty.
     */
    public function validateScheduleConflicts(
        array $examinerIds,
        string $date,
        string $startTime,
        string $endTime,
        ?string $room = null,
        ?int $excludeSeminarId = null,
        ?int $excludeTaDefenseId = null,
        ?int $locationId = null,
        ?int $excludeEofficePeminjamanId = null,
        ?int $eofficeId = null
    ): array {
        $errors = [];

        foreach ($examinerIds as $examinerId) {
            $conflict = $this->checkDoubleBooking($examinerId, $date, $startTime, $endTime, $excludeSeminarId, $excludeTaDefenseId);
            if ($conflict) {
                $errors[] = $conflict['message'];
            }
        }

        if ($room) {
            $roomConflict = $this->checkRoomConflict($room, $date, $startTime, $endTime, $excludeSeminarId, $excludeTaDefenseId, $locationId, $excludeEofficePeminjamanId, $eofficeId);
            if ($roomConflict) {
                $errors[] = $roomConflict['message'];
            }
        } elseif ($locationId || $eofficeId) {
            $location = $locationId ? Location::find($locationId) : null;
            if ($eofficeId || ($location && ! $location->isOnline())) {
                $roomConflict = $this->checkRoomConflict($location?->name ?? '', $date, $startTime, $endTime, $excludeSeminarId, $excludeTaDefenseId, $locationId, $excludeEofficePeminjamanId, $eofficeId);
                if ($roomConflict) {
                    $errors[] = $roomConflict['message'];
                }
            }
        }

        return $errors;
    }

    // ══════════════════════════════════════════
    // Auto-Generate Evaluation Rows
    // ══════════════════════════════════════════

    /**
     * Auto-generate PENDING evaluation rows for a seminar schedule.
     */
    public function autoGenerateSeminarEvaluations(SeminarSchedule $schedule): void
    {
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            SeminarEvaluation::create([
                'schedule_id' => $schedule->id,
                'examiner_id' => $examinerId,
                'status' => 'PENDING',
            ]);
        }
    }

    /**
     * Auto-generate PENDING evaluation rows for a TA defense schedule.
     */
    public function autoGenerateTaDefenseEvaluations(TaDefenseSchedule $schedule): void
    {
        $examiners = TaDefenseExaminer::where('schedule_id', $schedule->id)->get();

        foreach ($examiners as $examiner) {
            TaDefenseEvaluation::create([
                'schedule_id' => $schedule->id,
                'examiner_id' => $examiner->examiner_id,
                'status' => 'PENDING',
            ]);
        }
    }

    /** Create the examiner assignments and pending evaluation rows together. */
    public function createTaDefenseEvaluations(TaDefenseSchedule $schedule, array $studentIds): void
    {
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $index => $examinerId) {
            TaDefenseExaminer::firstOrCreate(['schedule_id' => $schedule->id, 'examiner_id' => $examinerId], ['role' => 'EXAMINER_'.($index + 1)]);
            TaDefenseEvaluation::firstOrCreate(['schedule_id' => $schedule->id, 'examiner_id' => $examinerId], ['status' => 'PENDING']);
        }
    }

    // ══════════════════════════════════════════
    // Transactional Evaluation Submission
    // ══════════════════════════════════════════

    /**
     * Submit a seminar evaluation. Transactional with lockForUpdate().
     * If all evaluations are submitted → auto state transition.
     *
     * @return array ['evaluation' => ..., 'all_submitted' => bool, 'result' => ?string]
     */
    public function submitSeminarEvaluation(
        int $evaluationId,
        array $rubricJson,
        float $score,
        string $result, // PASS or FAIL
        int $userId
    ): array {
        $lateCheck = null;

        $out = DB::transaction(function () use ($evaluationId, $rubricJson, $score, $result, $userId, &$lateCheck) {
            $evaluation = SeminarEvaluation::lockForUpdate()->findOrFail($evaluationId);
            $schedule = SeminarSchedule::lockForUpdate()->findOrFail($evaluation->schedule_id);
            $lateCheck = $schedule;
            $isUpdate = $evaluation->status === 'SUBMITTED';
            $scheduleCompleted = $schedule->status === 'COMPLETED';

            if ($isUpdate && $scheduleCompleted && $evaluation->result !== null && $result !== $evaluation->result) {
                throw new \InvalidArgumentException('Result is locked after the schedule is completed.');
            }

            // Snapshot the pre-overwrite record: when the membership changed
            // since the exam, a fresh submit replaces the old rubric keys
            // entirely, so the previous scores are archived in the audit log.
            $previousRecord = [
                'rubric_json' => $evaluation->rubric_json,
                'score' => $evaluation->score,
                'result' => $evaluation->result,
                'status' => $evaluation->status,
            ];

            $evaluation->update([
                'rubric_json' => $rubricJson,
                'score' => $score,
                'result' => $scheduleCompleted ? ($evaluation->result ?? $result) : $result,
                'status' => 'SUBMITTED',
            ]);

            // Check if ALL evaluations for this schedule are submitted
            $totalEvals = SeminarEvaluation::where('schedule_id', $schedule->id)->count();
            $submittedEvals = SeminarEvaluation::where('schedule_id', $schedule->id)
                ->where('status', 'SUBMITTED')
                ->count();

            $allSubmitted = $submittedEvals >= $totalEvals;

            if ($allSubmitted && ! $scheduleCompleted) {
                $schedule->update(['status' => 'COMPLETED']);

                // Determine group transition based on result. Guarded: the
                // group may already have moved past this stage (e.g. an old
                // evaluation edited late), in which case the scores still
                // save but the stale transition is skipped, never thrown.
                $group = Group::findOrFail($schedule->group_id);

                $target = match (true) {
                    $schedule->type === 'SEMPRO' && $result === 'PASS' => 'SEMPRO_DONE',
                    $schedule->type === 'SEMPRO' => 'PDC1_ACTIVE',
                    $schedule->type === 'EXPO' && $result === 'PASS' => 'EXPO_DONE',
                    default => 'PDC2_ACTIVE',
                };

                $this->transitionGroupIfAllowed($group, $target, "{$schedule->type}_{$result}", $schedule->id);

                AuditLog::create([
                    'user_id' => $userId,
                    'action' => "{$schedule->type}_{$result}",
                    'target_type' => 'SeminarSchedule',
                    'target_id' => $schedule->id,
                    'payload' => [
                        'group_id' => $group->id,
                        'avg_score' => SeminarEvaluation::where('schedule_id', $schedule->id)->avg('score'),
                        'previous_record' => $isUpdate ? $previousRecord : null,
                    ],
                ]);
            }

            return [
                'evaluation' => $evaluation->fresh(),
                'all_submitted' => $allSubmitted,
                'result' => $allSubmitted ? $result : null,
                'updated' => $isUpdate,
            ];
        });

        $this->notifyLateSubmission($lateCheck->type ?? 'SEMPRO', $lateCheck, $userId);

        return $out;
    }

    /**
     * Submit a TA defense evaluation. Transactional with lockForUpdate().
     * If all evaluations submitted → determine PASS/FAIL → update TA status → check group CLOSED.
     */
    public function submitTaDefenseEvaluation(
        int $evaluationId,
        array $rubricJson,
        float $score,
        string $result, // PASS or FAIL
        int $userId
    ): array {
        $lateCheck = null;

        $out = DB::transaction(function () use ($evaluationId, $rubricJson, $score, $result, $userId, &$lateCheck) {
            $evaluation = TaDefenseEvaluation::lockForUpdate()->findOrFail($evaluationId);
            $schedule = TaDefenseSchedule::lockForUpdate()->findOrFail($evaluation->schedule_id);
            $lateCheck = $schedule;
            $isUpdate = $evaluation->status === 'SUBMITTED';
            $scheduleCompleted = $schedule->status === 'COMPLETED';

            if ($isUpdate && $scheduleCompleted && $evaluation->result !== null && $result !== $evaluation->result) {
                throw new \InvalidArgumentException('Result is locked after the schedule is completed.');
            }

            $evaluation->update([
                'rubric_json' => $rubricJson,
                'score' => $score,
                'result' => $scheduleCompleted ? ($evaluation->result ?? $result) : $result,
                'status' => 'SUBMITTED',
            ]);

            // Check if ALL evaluations for this TA defense are submitted
            $totalEvals = TaDefenseEvaluation::where('schedule_id', $schedule->id)->count();
            $submittedEvals = TaDefenseEvaluation::where('schedule_id', $schedule->id)
                ->where('status', 'SUBMITTED')
                ->count();

            $allSubmitted = $submittedEvals >= $totalEvals;

            if ($allSubmitted && ! $scheduleCompleted) {
                $schedule->update(['status' => 'COMPLETED']);

                // Update TA submission status
                $taSubmission = TaSubmission::where('student_id', $schedule->student_id)
                    ->where('group_id', $schedule->group_id)
                    ->firstOrFail();

                if ($result === 'PASS') {
                    $taSubmission->update(['status' => 'TA_DEFENDED']);

                    // Check if ALL active group members have defended → group CLOSED
                    $group = Group::findOrFail($schedule->group_id);
                    $activeMemberCount = GroupMember::where('group_id', $group->id)->count();
                    $defendedCount = TaSubmission::where('group_id', $group->id)
                        ->where('status', 'TA_DEFENDED')
                        ->count();

                    if ($activeMemberCount > 0 && $defendedCount >= $activeMemberCount) {
                        $this->transitionGroupIfAllowed($group, 'CLOSED', "TA_DEFENSE_{$result}", $schedule->id);
                    }
                } else {
                    $taSubmission->update(['status' => 'TA_REVISED']);
                }

                AuditLog::create([
                    'user_id' => $userId,
                    'action' => "TA_DEFENSE_{$result}",
                    'target_type' => 'TaDefenseSchedule',
                    'target_id' => $schedule->id,
                    'payload' => [
                        'student_id' => $schedule->student_id,
                        'avg_score' => TaDefenseEvaluation::where('schedule_id', $schedule->id)->avg('score'),
                    ],
                ]);
            }

            return [
                'evaluation' => $evaluation->fresh(),
                'all_submitted' => $allSubmitted,
                'result' => $allSubmitted ? $result : null,
                'updated' => $isUpdate,
            ];
        });

        $this->notifyLateSubmission('TA_DEFENSE', $lateCheck, $userId);

        return $out;
    }

    /**
     * Attempt a group transition that is only valid from certain statuses.
     *
     * Late edits of old evaluations can complete a stale schedule after the
     * group has already moved on (e.g. PDC2_ACTIVE with a SCHEDULED sempro).
     * The scores must still save, so a disallowed transition is skipped with
     * a warning instead of throwing and rolling back the submission.
     */
    private function transitionGroupIfAllowed(Group $group, string $target, string $context, int $scheduleId): void
    {
        if ($this->stateMachine->canTransition($group->status, $target)) {
            $this->stateMachine->transition($group, $target);

            return;
        }

        Log::warning("Skipped stale group transition {$group->status} → {$target} ({$context} on schedule {$scheduleId}, group {$group->id}).");
    }

    /**
     * Soft deadline notice for examiner submissions (mirrors the
     * supervisor path). A passed deadline never blocks the submit — the
     * examiner is only notified their evaluation was recorded as late.
     * Never throws; notification failures must not roll back the scores.
     */
    private function notifyLateSubmission(string $scheduleType, $schedule, int $userId): void
    {
        if (! $schedule) {
            return;
        }

        $stored = EvaluationDeadline::storedDeadline($schedule);
        $deadline = $stored ?? EvaluationDeadline::fromDate($schedule->date);

        if (! EvaluationDeadline::isPassed($deadline)) {
            return;
        }

        try {
            $group = Group::find($schedule->group_id);
            $groupName = $group?->name ?? $group?->code ?? 'Group '.$schedule->group_id;

            $evaluationName = match ($scheduleType) {
                'SEMPRO' => 'SEMPRO',
                'EXPO' => 'Evaluasi EXPO',
                'TA_DEFENSE' => 'TA Defense',
                default => $scheduleType,
            };

            $deadlineFormatted = date('d M Y H:i', strtotime($deadline));

            app(NotificationService::class)->send(
                $userId,
                'EVALUATION_DEADLINE_PASSED',
                'Evaluation Submitted After Deadline',
                "Your evaluation for {$groupName} - {$evaluationName} was submitted after the deadline (due: {$deadlineFormatted}).",
                $scheduleType === 'TA_DEFENSE' ? 'TaDefenseSchedule' : 'SeminarSchedule',
                $schedule->id
            );
        } catch (\Exception $e) {
            Log::error('Failed to send deadline notification: '.$e->getMessage());
        }
    }
}
