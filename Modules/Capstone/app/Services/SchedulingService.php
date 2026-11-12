<?php

namespace Modules\Capstone\Services;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Exceptions\ScheduleConflictException;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Location;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\SemproScore;
use Modules\Capstone\Models\SidangTaScore;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaRegistration;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Support\EvaluationDeadline;
use Modules\EOffice\Models\Ruangan;

class SchedulingService
{
    public const PASS_THRESHOLD = 60.0;

    protected GroupStateMachine $stateMachine;

    public function __construct(GroupStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Derive PASS/FAIL from the weighted average (single source of truth).
     */
    public static function deriveResult(float $score): string
    {
        return $score >= self::PASS_THRESHOLD ? 'PASS' : 'FAIL';
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
        if (! empty($overlap)) {
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
     *
     * Idempotent: re-running for the same examiner returns the existing
     * row instead of duplicating it, and null examiner slots (e.g. a
     * registration-created EXPO schedule before examiners are assigned)
     * are skipped.
     */
    public function autoGenerateSeminarEvaluations(SeminarSchedule $schedule): void
    {
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            if ($examinerId === null) {
                continue;
            }

            SeminarEvaluation::firstOrCreate([
                'schedule_id' => $schedule->id,
                'examiner_id' => $examinerId,
            ], [
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

            if (! $scheduleCompleted) {
                $expected = self::deriveResult($score);
                if ($result !== $expected) {
                    throw new \InvalidArgumentException('Hasil harus mengikuti rata-rata nilai (≥60 Lulus, <60 Tidak Lulus).');
                }
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

            // Dual-write the per-student rubric breakdown into the split
            // score tables read by the grades page (SEMPRO/EXPO). The
            // evaluation row alone is invisible to GradeCalculationService.
            $this->syncExaminerScores($schedule->type, (int) $evaluation->examiner_id, (int) $schedule->group_id, $rubricJson);

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

        // A late examiner submit can complete the SEMPRO stage after the
        // group already reached SEMPRO_DONE (e.g. via the READY_FOR_SEMPRO
        // safety net), so offer the lifecycle a chance to advance to
        // PDC2_ACTIVE here as well. Supervisor submits already trigger
        // this in SupervisorEvaluationController. Never throws: unmatched
        // statuses return null via the canTransition guard.
        if ($lateCheck && $lateCheck->group_id) {
            $completedGroup = Group::find($lateCheck->group_id);
            if ($completedGroup) {
                app(GroupLifecycleService::class)->advanceIfComplete($completedGroup);
            }
        }

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

            if (! $scheduleCompleted) {
                $expectedTa = self::deriveResult($score);
                if ($result !== $expectedTa) {
                    throw new \InvalidArgumentException('Hasil harus mengikuti rata-rata nilai (≥60 Lulus, <60 Tidak Lulus).');
                }
            }

            $evaluation->update([
                'rubric_json' => $rubricJson,
                'score' => $score,
                'result' => $scheduleCompleted ? ($evaluation->result ?? $result) : $result,
                'status' => 'SUBMITTED',
            ]);

            // Dual-write the per-student rubric breakdown into the split
            // score table read by the grades page (SIDANG_TA).
            $this->syncExaminerScores('SIDANG_TA', (int) $evaluation->examiner_id, (int) $schedule->group_id, $rubricJson);

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
     * Expand an examiner's rubric_json into per-student rows in the split
     * score tables (capstone_sempro_scores / capstone_sidang_ta_scores)
     * that GradeCalculationService reads.
     *
     * EXPO has no examiner evaluation at all: EXPO grades come only from
     * the members' self-evaluations in daftar expo, so no EXPO evaluation
     * rows are ever created and this method is never called with EXPO
     * (the match default below is a defensive no-op).
     *
     * Rubric keys are "{componentId}_{studentId}" (see
     * resources/assets/js/pages/dosen/evaluations.js). Unknown keys, empty
     * values, and non-member students are skipped. Missing split tables
     * (e.g. isolated test schemas) are a no-op so examiner submits never
     * break outside a fully migrated database.
     */
    public function syncExaminerScores(string $type, int $examinerId, int $groupId, array $rubricJson): void
    {
        $scores = $rubricJson['scores'] ?? [];
        $notes = $rubricJson['notes'] ?? [];
        if (! is_array($scores) || $scores === []) {
            return;
        }

        $modelClass = match ($type) {
            'SEMPRO' => SemproScore::class,
            'SIDANG_TA' => SidangTaScore::class,
            default => null,
        };
        if ($modelClass === null) {
            return;
        }
        if (! Schema::hasTable((new $modelClass)->getTable())) {
            return;
        }

        $actorColumn = 'examiner_id';
        $memberIds = GroupMember::where('group_id', $groupId)->pluck('student_id')->all();
        $memberLookup = array_flip(array_map('intval', $memberIds));

        $componentIds = [];
        foreach (array_keys($scores) as $key) {
            $parts = explode('_', (string) $key);
            if (count($parts) === 2 && is_numeric($parts[0])) {
                $componentIds[] = (int) $parts[0];
            }
        }
        $componentIds = array_values(array_unique($componentIds));

        $periodComponentIds = Schema::hasTable('capstone_period_assessment_components') && $componentIds !== []
            ? PeriodAssessmentComponent::whereIn('id', $componentIds)->pluck('id')->all()
            : [];
        $periodLookup = array_flip(array_map('intval', $periodComponentIds));
        $legacyLookup = [];
        if (Schema::hasTable('capstone_assessment_components') && $componentIds !== []) {
            $legacyIds = AssessmentComponent::whereIn('id', $componentIds)->pluck('id')->all();
            $legacyLookup = array_flip(array_map('intval', $legacyIds));
        }

        foreach ($scores as $key => $value) {
            if ($value === null || $value === '' || ! is_numeric($value)) {
                continue;
            }
            $numericScore = (float) $value;
            if ($numericScore < 0 || $numericScore > 100) {
                continue;
            }
            $parts = explode('_', (string) $key);
            if (count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
                continue;
            }
            $componentId = (int) $parts[0];
            $studentId = (int) $parts[1];
            if (! isset($memberLookup[$studentId])) {
                continue;
            }

            // Prefer period components (the examiner form's component ids);
            // fall back to legacy components so the FK is never violated.
            if (isset($periodLookup[$componentId])) {
                $attributes = [
                    $actorColumn => $examinerId,
                    'group_id' => $groupId,
                    'student_id' => $studentId,
                    'period_component_id' => $componentId,
                ];
            } elseif (isset($legacyLookup[$componentId])) {
                $attributes = [
                    $actorColumn => $examinerId,
                    'group_id' => $groupId,
                    'student_id' => $studentId,
                    'component_id' => $componentId,
                ];
            } else {
                continue;
            }

            $note = $notes[$key] ?? null;

            $modelClass::updateOrCreate($attributes, [
                'score' => $numericScore,
                'notes' => is_string($note) ? $note : null,
            ]);
        }
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

    // ══════════════════════════════════════════
    // TA Defense Write Orchestration
    // ══════════════════════════════════════════
    //
    // Moved verbatim from TaDefenseScheduleController (Batch B): the
    // controller keeps admin authorization + response envelopes; every
    // cross-row guard lives here and throws with the same message the
    // endpoint returns. Envelope mapping (controller side):
    // DomainRuleException → 400 same message,
    // ScheduleConflictException → 400 same message + conflicts detail,
    // AuthorizationException → 403 same message (evaluation lock),
    // other \Exception → 500 'Failed ...' same prefix.

    /**
     * Create a TA defense schedule with multi-student support.
     *
     * @param  array<string, mixed>  $validated  Validated StoreTaDefenseRequest data.
     */
    public function scheduleTaDefense(array $validated, int $adminId): TaDefenseSchedule
    {
        return DB::transaction(function () use ($validated) {
            $studentIds = $validated['student_ids'];
            $group = Group::with(['supervisors', 'period', 'members'])->whereKey($validated['group_id'])->lockForUpdate()->firstOrFail();
            if (isset($validated['period_id']) && (int) $validated['period_id'] !== $group->period_id) {
                throw new DomainRuleException('The selected group belongs to another period');
            }

            // Validate all students are from the same group
            $groupMemberIds = $group->members->pluck('student_id')->toArray();
            $invalidStudents = array_diff($studentIds, $groupMemberIds);

            if (! empty($invalidStudents)) {
                throw new DomainRuleException('All selected students must be from the same group');
            }

            // Validate all students have TA_DOCUMENTS_APPROVED status
            $validSubmissions = TaSubmission::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('status', 'TA_DOCUMENTS_APPROVED')
                ->pluck('student_id')
                ->toArray();

            $invalidStatusStudents = array_diff($studentIds, $validSubmissions);
            if (! empty($invalidStatusStudents)) {
                throw new DomainRuleException('All selected students must have TA_DOCUMENTS_APPROVED status');
            }

            // Validate all students have an APPROVED sidang TA registration
            $approvedRegistrations = TaRegistration::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('status', TaRegistration::STATUS_APPROVED)
                ->pluck('student_id')
                ->toArray();

            $unapprovedStudents = array_diff($studentIds, $approvedRegistrations);
            if (! empty($unapprovedStudents)) {
                throw new DomainRuleException('All selected students must have an approved sidang TA registration');
            }

            // Check for existing scheduled defenses for these students
            $existingScheduled = TaDefenseSchedule::where(fn ($q) => $q->whereIn('student_id', $studentIds)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $studentIds)))
                ->whereIn('status', ['SCHEDULED', 'DONE'])
                ->exists();

            if ($existingScheduled) {
                throw new DomainRuleException('One or more selected students already have a scheduled or completed defense');
            }

            // Validate examiners are not supervisors
            $supervisorIds = array_unique(array_merge($group->supervisors->pluck('id')->all(), array_filter([$group->supervisor_1_id, $group->supervisor_2_id])));

            if (in_array($validated['examiner_1_id'], $supervisorIds)) {
                throw new DomainRuleException('Examiner 1 cannot be a supervisor of this group');
            }

            if (in_array($validated['examiner_2_id'], $supervisorIds)) {
                throw new DomainRuleException('Examiner 2 cannot be a supervisor of this group');
            }

            // Validate examiners are dosen
            $examiner1 = Lecturer::find($validated['examiner_1_id']);
            $examiner2 = Lecturer::find($validated['examiner_2_id']);

            if (! $examiner1) {
                throw new DomainRuleException('Examiner 1 must be a dosen');
            }

            if (! $examiner2) {
                throw new DomainRuleException('Examiner 2 must be a dosen');
            }

            // Validate scheduling conflicts
            $examinerIds = [$validated['examiner_1_id'], $validated['examiner_2_id']];

            // Determine room for conflict checking — single source: EOffice.
            $ruangan = Ruangan::findOrFail($validated['eoffice_ruangan_id']);
            $room = $ruangan->nama;

            $conflicts = $this->validateScheduleConflicts(
                $examinerIds,
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $room,
                null,
                null,
                null,
                null,
                $ruangan->id
            );

            if (! empty($conflicts)) {
                throw new ScheduleConflictException('Scheduling conflicts detected.', $conflicts);
            }

            // Auto-calculate evaluation deadline
            $evaluationDeadline = date('Y-m-d H:i:s', strtotime($validated['date'].' +2 days'));

            DB::beginTransaction();
            try {
                $payload = [
                    'group_id' => $validated['group_id'],
                    'student_id' => $studentIds[0], // Backward compatibility
                    'examiner_1_id' => $validated['examiner_1_id'],
                    'examiner_2_id' => $validated['examiner_2_id'],
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'room' => $room,
                    'eoffice_ruangan_id' => $ruangan->id,
                    'status' => 'SCHEDULED',
                    'evaluation_deadline' => $evaluationDeadline,
                    'notes' => $validated['notes'] ?? null,
                ];

                if (Schema::hasColumn('capstone_ta_defense_schedules', 'period_id')) {
                    $payload['period_id'] = $validated['period_id'] ?? $group->period_id;
                }

                $schedule = TaDefenseSchedule::create($payload);

                // Attach all students to pivot table
                $schedule->students()->attach($studentIds);

                // Update or create TA submissions for all scheduled students
                foreach ($studentIds as $studentId) {
                    TaSubmission::updateOrCreate(
                        ['student_id' => $studentId, 'group_id' => $group->id],
                        [
                            'status' => 'TA_READY_FOR_SIDANG',
                            'group_id' => $group->id,
                            'period_id' => $payload['period_id'] ?? $group->period_id,
                        ]
                    );
                }

                // Create evaluation records
                $this->createTaDefenseEvaluations($schedule, $studentIds);

                // Reload schedule with students relationship for notification
                $schedule->load('students');

                // Notify all students and examiners
                $this->notifyTaDefenseScheduled($schedule, $studentIds);

                DB::commit();

                return $schedule->load(['students', 'group.title', 'group.period', 'examiner1', 'examiner2']);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to create TA defense schedule', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $validated,
                ]);

                throw $e;
            }
        });
    }

    /**
     * Update a TA defense schedule with multi-student support.
     *
     * @param  array<string, mixed>  $validated  Validated Update/AssignExaminers data.
     */
    public function updateTaDefenseSchedule(int $id, array $validated): TaDefenseSchedule
    {
        return DB::transaction(function () use ($validated, $id) {
            $initial = TaDefenseSchedule::findOrFail($id);
            $group = Group::with(['members', 'supervisors'])->whereKey($initial->group_id)->lockForUpdate()->firstOrFail();
            $schedule = TaDefenseSchedule::with('students')->whereKey($id)->lockForUpdate()->firstOrFail();
            if ($schedule->status !== 'SCHEDULED' && ! in_array($schedule->status, ['PENDING', 'PENDING_APPROVAL'], true)) {
                throw new DomainRuleException('Cannot update a completed or cancelled schedule');
            }
            $currentIds = $schedule->students->pluck('id')->all() ?: array_filter([$schedule->student_id]);
            $studentIds = array_values(array_unique(array_map('intval', $validated['student_ids'] ?? $currentIds)));
            if (array_diff($currentIds, $studentIds)) {
                throw new DomainRuleException('Already selected students cannot be removed');
            }
            if (array_diff($studentIds, $group->members->pluck('student_id')->all())) {
                throw new DomainRuleException('All selected students must be from the same group');
            }
            $added = array_values(array_diff($studentIds, $currentIds));
            $ready = TaSubmission::where('group_id', $group->id)->whereIn('student_id', $added)->where('status', 'TA_DOCUMENTS_APPROVED')->pluck('student_id')->all();
            if (array_diff($added, $ready)) {
                throw new DomainRuleException('All selected students must have TA_DOCUMENTS_APPROVED status');
            }
            $registered = TaRegistration::where('group_id', $group->id)->whereIn('student_id', $added)->where('status', TaRegistration::STATUS_APPROVED)->pluck('student_id')->all();
            if (array_diff($added, $registered)) {
                throw new DomainRuleException('All selected students must have an approved sidang TA registration');
            }
            $hasOtherDefense = TaDefenseSchedule::whereKeyNot($id)->whereIn('status', ['SCHEDULED', 'DONE', 'COMPLETED'])
                ->where(fn ($q) => $q->whereIn('student_id', $added)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $added)))->exists();
            if ($hasOtherDefense) {
                throw new DomainRuleException('A selected student already has an active defense');
            }
            $examinerIds = [(int) ($validated['examiner_1_id'] ?? $schedule->examiner_1_id), (int) ($validated['examiner_2_id'] ?? $schedule->examiner_2_id)];
            $supervisorIds = array_unique(array_merge($group->supervisors->pluck('id')->all(), array_filter([$group->supervisor_1_id, $group->supervisor_2_id])));
            if ($examinerIds[0] === $examinerIds[1] || count(array_filter($examinerIds)) !== 2) {
                throw new DomainRuleException('Choose two different examiners');
            }
            if (array_intersect($examinerIds, $supervisorIds)) {
                throw new DomainRuleException('Examiners cannot be supervisors of this group');
            }
            $examinersChanged = $examinerIds !== [(int) $schedule->examiner_1_id, (int) $schedule->examiner_2_id];
            if (($examinersChanged || $added) && TaDefenseEvaluation::where('schedule_id', $id)->where('status', '!=', 'PENDING')->exists()) {
                throw new AuthorizationException('Examiners and students are locked after evaluation submission');
            }
            $payload = collect($validated)->only(['date', 'start_time', 'end_time', 'eoffice_ruangan_id', 'notes', 'status', 'examiner_1_id', 'examiner_2_id'])->all();
            unset($payload['room'], $payload['location_id']);
            if (! empty($payload['eoffice_ruangan_id'])) {
                $payload['room'] = Ruangan::findOrFail($payload['eoffice_ruangan_id'])->nama;
            }
            $eofficeId = isset($payload['eoffice_ruangan_id']) ? (int) $payload['eoffice_ruangan_id'] : ($schedule->getAttributes()['eoffice_ruangan_id'] ?? null);
            $conflicts = $this->validateScheduleConflicts($examinerIds, $payload['date'] ?? $schedule->date->format('Y-m-d'), $payload['start_time'] ?? $schedule->start_time, $payload['end_time'] ?? $schedule->end_time, $payload['room'] ?? $schedule->room, null, $schedule->id, null, ($schedule->getAttributes()['eoffice_peminjaman_id'] ?? null), $eofficeId ? (int) $eofficeId : null);
            if ($conflicts) {
                throw new ScheduleConflictException('Scheduling conflicts detected.', $conflicts);
            }
            $schedule->update($payload);
            if ($added) {
                $schedule->students()->syncWithoutDetaching($added);
                TaSubmission::where('group_id', $group->id)->whereIn('student_id', $added)->update(['status' => 'TA_READY_FOR_SIDANG']);
            }
            if ($examinersChanged) {
                TaDefenseEvaluation::where('schedule_id', $id)->where('status', 'PENDING')->delete();
                TaDefenseExaminer::where('schedule_id', $id)->delete();
                $this->createTaDefenseEvaluations($schedule->fresh(), $studentIds);
            }
            $this->notifyDefenseUpdated($schedule, $studentIds);

            return $schedule->fresh()->load(['students', 'group.title', 'group.period', 'examiner1', 'examiner2']);
        });
    }

    /**
     * Cancel a TA defense schedule: revert submissions, clean pending
     * evaluations, audit, notify.
     */
    public function cancelTaDefenseSchedule(int $id, int $adminId): void
    {
        $schedule = TaDefenseSchedule::with(['students', 'group'])->findOrFail($id);

        if ($schedule->status === 'DONE') {
            throw new DomainRuleException('Cannot cancel completed schedule');
        }

        $studentIds = $schedule->students->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            $schedule->update(['status' => 'CANCELLED']);

            // Revert status for all students
            TaSubmission::whereIn('student_id', $studentIds)
                ->where('status', 'TA_READY_FOR_SIDANG')
                ->update(['status' => 'TA_DOCUMENTS_APPROVED']);

            // Clean up pending evaluations
            TaDefenseEvaluation::where('schedule_id', $schedule->id)
                ->where('status', 'PENDING')
                ->delete();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'TA_DEFENSE_CANCELLED',
                'target_type' => 'TaDefenseSchedule',
                'target_id' => $schedule->id,
                'payload' => ['group_id' => $schedule->group_id],
            ]);

            // Notify all students of cancellation
            $this->notifyDefenseCancelled($schedule, $studentIds);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Notify students and examiners when defense is scheduled
     */
    private function notifyDefenseStudents($schedule, array $studentIds, string $type, string $title, string $message): void
    {
        foreach ($studentIds as $studentId) {
            Notification::create([
                'user_id' => Student::find($studentId)?->user_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    private function notifyDefenseExaminers($schedule, string $type, string $title, string $message): void
    {
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            Notification::create([
                'user_id' => Lecturer::find($examinerId)?->user_id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    private function notifyTaDefenseScheduled($schedule, array $studentIds): void
    {
        $studentNames = $schedule->students->pluck('name')->join(', ');

        $this->notifyDefenseStudents(
            $schedule,
            $studentIds,
            'TA_DEFENSE_SCHEDULED',
            'Jadwal Sidang TA',
            "Anda dijadwalkan sidang TA pada {$schedule->date} pukul {$schedule->start_time} di {$schedule->room}"
        );
        $this->notifyDefenseExaminers(
            $schedule,
            'TA_DEFENSE_EXAMINER_ASSIGNED',
            'Penugasan Penguji Sidang TA',
            "Anda ditugaskan sebagai penguji sidang TA untuk mahasiswa: {$studentNames}"
        );
    }

    /**
     * Notify all students when defense is updated
     */
    private function notifyDefenseUpdated($schedule, array $studentIds): void
    {
        $this->notifyDefenseStudents(
            $schedule,
            $studentIds,
            'TA_DEFENSE_UPDATED',
            'Jadwal Sidang TA Diperbarui',
            "Jadwal sidang TA Anda telah diperbarui. Tanggal: {$schedule->date}, Waktu: {$schedule->start_time}, Ruang: {$schedule->room}"
        );
        $this->notifyDefenseExaminers(
            $schedule,
            'TA_DEFENSE_UPDATED',
            'Jadwal Sidang TA Diperbarui',
            "Jadwal sidang TA yang Anda uji telah diperbarui. Tanggal: {$schedule->date}, Waktu: {$schedule->start_time}, Ruang: {$schedule->room}"
        );
    }

    /**
     * Notify all students when defense is cancelled
     */
    private function notifyDefenseCancelled($schedule, array $studentIds): void
    {
        $this->notifyDefenseStudents(
            $schedule,
            $studentIds,
            'TA_DEFENSE_CANCELLED',
            'Jadwal Sidang TA Dibatalkan',
            "Jadwal sidang TA Anda pada {$schedule->date} telah dibatalkan. Anda dapat menjadwalkan ulang."
        );
        $this->notifyDefenseExaminers(
            $schedule,
            'TA_DEFENSE_CANCELLED',
            'Jadwal Sidang TA Dibatalkan',
            "Jadwal sidang TA yang Anda uji pada {$schedule->date} telah dibatalkan. Anda dapat mengabaikan penugasan ini."
        );
    }
}
