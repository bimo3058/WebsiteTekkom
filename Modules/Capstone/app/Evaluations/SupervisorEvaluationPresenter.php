<?php

namespace Modules\Capstone\Evaluations;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\AssessmentComponent;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Models\Schedule;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Repositories\AssessmentScoreRepository;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Support\EvaluationDeadline;

class SupervisorEvaluationPresenter
{
    /**
     * Get detailed schedules for a group with evaluation status
     */
    public function getGroupSchedulesDetailed($group, $supervision, $supervisorId): array
    {
        $schedules = [];

        // SEMPRO → BIMBINGAN_SEMPRO (cancelled stays hidden; notified only)
        $seminarSchedules = SeminarSchedule::where('group_id', $group->id)
            ->whereIn('type', ['SEMPRO'])
            ->where('status', '!=', 'CANCELLED')
            ->get();

        foreach ($seminarSchedules as $sempro) {
            $schedules[] = app(SupervisorEvaluationPresenter::class)->formatSeminarScheduleForSupervisor(
                $sempro, 'SEMINAR', 'BIMBINGAN_SEMPRO', $group, $supervision, $supervisorId
            );
        }

        // NILAI_DOSEN and MILESTONE open as soon as the group reaches
        // PDC2_ACTIVE — no EXPO schedule or registration is required.
        // EXPO is the group-level review (approve/reject) of the
        // representative's self-evaluation + document submission.
        foreach (['NILAI_DOSEN', 'MILESTONE'] as $pdc2Type) {
            if (in_array($group->status, GroupLifecycleService::EDITABLE_STATUSES[$pdc2Type] ?? [], true)) {
                $schedules[] = app(SupervisorEvaluationPresenter::class)->formatScheduleLessEvaluationForSupervisor(
                    $group,
                    $supervision,
                    $supervisorId,
                    $pdc2Type
                );
            }
        }

        // EXPO: show the review card only when a submission is on the table
        // (submitted, previously approved, or rejected awaiting a re-submit).
        $expoRegistration = ExpoRegistration::where('group_id', $group->id)
            ->where('status', 'REGISTERED')
            ->orderByDesc('id')
            ->first();
        if ($expoRegistration && $expoRegistration->review_status !== null) {
            $schedules[] = app(SupervisorEvaluationPresenter::class)->formatExpoReviewForSupervisor(
                $expoRegistration,
                $group,
                $supervision,
                $supervisorId
            );
        }

        // TA_DEFENSE → BIMBINGAN_TA (per-student, only if student has TA_READY_FOR_SIDANG or higher status)
        $taSchedules = TaDefenseSchedule::where('group_id', $group->id)
            ->where('status', '!=', 'CANCELLED')
            ->with(['student', 'students'])
            ->get();

        // Batch-load all TaSubmission records for students in these defense schedules to avoid N+1
        $allStudentIds = collect();
        foreach ($taSchedules as $ta) {
            $students = $ta->students;
            if ($students->isEmpty()) {
                $students = collect([$ta->student]);
            }
            foreach ($students as $student) {
                $allStudentIds->push($student->id);
            }
        }
        $submissionMap = TaSubmission::whereIn('student_id', $allStudentIds->unique()->values())
            ->get()
            ->keyBy('student_id');

        foreach ($taSchedules as $ta) {
            // Get all students in this defense schedule (supports multi-student)
            $students = $ta->students;
            if ($students->isEmpty()) {
                // Fallback to backward-compat single student
                $students = collect([$ta->student]);
            }

            foreach ($students as $student) {
                // Check if student is ready for sidang (use pre-fetched map)
                $studentSubmission = $submissionMap->get($student->id);
                if ($studentSubmission && $studentSubmission->statusIsAtLeast('TA_READY_FOR_SIDANG')) {
                    $schedules[] = app(SupervisorEvaluationPresenter::class)->formatTaDefenseScheduleForSupervisor(
                        $ta, 'TA_DEFENSE', 'BIMBINGAN_TA', $group, $supervision, $supervisorId, $student
                    );
                }
            }
        }

        return $schedules;
    }

    /**
     * Format seminar schedule data for supervisor response
     */
    public function formatSeminarScheduleForSupervisor($seminarSchedule, string $scheduleType, string $evalType, $group, $supervision, int $supervisorId): array
    {
        $status = app(SupervisorEvaluationPresenter::class)->getEvaluationStatus($group, $supervisorId, $evalType);

        // Calculate deadline (date + 2 days if not set)
        $deadline = null;
        if ($seminarSchedule->date) {
            $deadline = date('Y-m-d H:i:s', strtotime($seminarSchedule->date.' +2 days'));
        }

        return [
            'schedule_id' => $seminarSchedule->id,
            'schedule_type' => $scheduleType,
            'evaluation_type' => $evalType,
            'date' => $seminarSchedule->date,
            'start_time' => $seminarSchedule->start_time,
            'end_time' => $seminarSchedule->end_time,
            'room' => $seminarSchedule->room,
            'deadline' => $deadline,
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'students' => $group->members->map(fn ($m) => [
                'id' => $m->student->id,
                'name' => $m->student->name,
                'nim' => $m->student->nim,
                'is_leader' => $m->is_leader,
            ])->values(),
            'status' => $status,
            'supervisor_role' => $supervision->role,
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
        ];
    }

    /**
     * Format TA defense schedule data for supervisor response
     */
    public function formatTaDefenseScheduleForSupervisor($taDefenseSchedule, string $scheduleType, string $evalType, $group, $supervision, int $supervisorId, ?Student $student = null): array
    {
        // Use provided student or fall back to backward-compat
        $student = $student ?? $taDefenseSchedule->student;

        $status = app(SupervisorEvaluationPresenter::class)->getEvaluationStatus($group, $supervisorId, $evalType, $student?->id);

        // Calculate deadline (date + 2 days if not set)
        $deadline = null;
        if ($taDefenseSchedule->date) {
            $deadline = date('Y-m-d H:i:s', strtotime($taDefenseSchedule->date.' +2 days'));
        }

        return [
            'schedule_id' => $taDefenseSchedule->id,
            'schedule_type' => $scheduleType,
            'evaluation_type' => $evalType,
            'date' => $taDefenseSchedule->date,
            'start_time' => $taDefenseSchedule->start_time,
            'end_time' => $taDefenseSchedule->end_time,
            'room' => $taDefenseSchedule->room,
            'deadline' => $deadline,
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'student' => $student ? [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim,
                'is_leader' => $group->members->firstWhere('student_id', $student->id)?->is_leader ?? false,
            ] : null,
            'status' => $status,
            'supervisor_role' => $supervision->role,
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
        ];
    }

    /**
     * Format schedule-less PDC2 evaluation card (NILAI_DOSEN, MILESTONE).
     * No seminar schedule is required; the soft deadline is persisted on
     * the group so the value is stable across requests.
     */
    public function formatScheduleLessEvaluationForSupervisor($group, $supervision, int $supervisorId, string $evalType): array
    {
        $status = app(SupervisorEvaluationPresenter::class)->getEvaluationStatus($group, $supervisorId, $evalType);

        $deadline = app(SupervisorEvaluationPresenter::class)->resolveSoftDeadline($group, $evalType);

        return [
            'schedule_id' => null,
            'schedule_type' => 'PDC2',
            'evaluation_type' => $evalType,
            'date' => null,
            'start_time' => null,
            'end_time' => null,
            'room' => null,
            'deadline' => $deadline,
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'students' => $group->members->map(fn ($m) => [
                'id' => $m->student->id,
                'name' => $m->student->name,
                'nim' => $m->student->nim,
                'is_leader' => $m->is_leader,
            ])->values(),
            'status' => $status,
            'supervisor_role' => $supervision->role,
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
        ];
    }

    /**
     * Format the group-level EXPO review card: the representative's
     * self-evaluation + document submission, approved or rejected once per
     * group. No scoring — status maps from the registration review state.
     */
    public function formatExpoReviewForSupervisor(ExpoRegistration $registration, $group, $supervision, int $supervisorId): array
    {
        $status = match ($registration->review_status) {
            'APPROVED' => 'COMPLETED',
            'REJECTED' => 'REJECTED',
            default => 'PENDING',
        };

        $window = app(GroupLifecycleService::class)->formAccess($group, 'EXPO');

        return [
            'schedule_id' => null,
            'schedule_type' => 'EXPO',
            'evaluation_type' => 'EXPO',
            'date' => $registration->registered_at?->toDateString(),
            'start_time' => null,
            'end_time' => null,
            'room' => null,
            'deadline' => null,
            'group' => [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
            ],
            'students' => $group->members->map(fn ($m) => [
                'id' => $m->student->id,
                'name' => $m->student->name,
                'nim' => $m->student->nim,
                'is_leader' => $m->is_leader,
            ])->values(),
            'status' => $status,
            // Review is only actionable while a fresh submission is pending
            // review and the group is still inside the EXPO window.
            'editable' => $status === 'PENDING' && $window['editable'],
            'review_status' => $registration->review_status,
            'review_feedback' => $registration->review_feedback,
            'supervisor_role' => $supervision->role,
            'period' => [
                'id' => $group->period->id,
                'name' => $group->period->name,
            ],
        ];
    }

    /**
     * Check if all bimbingan evaluations are complete from both supervisors
     */
    public function areAllBimbinganScoresComplete(int $groupId, string $evaluationType): bool
    {
        $group = Group::find($groupId);
        if (! $group) {
            return false;
        }

        // Get both supervisors
        $supervisorIds = [
            $group->supervisor_1_id,
            $group->supervisor_2_id,
        ];

        // Remove null values
        $supervisorIds = array_filter($supervisorIds);

        if (empty($supervisorIds)) {
            return false;
        }

        // Check if all supervisors have submitted scores for this evaluation type
        foreach ($supervisorIds as $supervisorId) {
            $hasScore = AssessmentScoreRepository::existsForGroupAndEvaluator(
                $groupId,
                $supervisorId,
                $evaluationType
            );

            if (! $hasScore) {
                return false; // This supervisor hasn't submitted yet
            }
        }

        return true;
    }

    /**
     * Check if period_assessment_components table exists
     */
    public function usesPeriodAssessmentComponents(): bool
    {
        return Schema::hasTable('capstone_period_assessment_components');
    }

    /**
     * Get evaluation status for a group
     */
    public function getEvaluationStatus(Group $group, int $supervisorId, string $evaluationType, ?int $studentId = null): string
    {
        $groupId = $group->id;

        // Schema-aware component count
        if (app(SupervisorEvaluationPresenter::class)->usesPeriodAssessmentComponents()) {
            $componentCount = PeriodAssessmentComponent::where('period_id', $group->period_id)
                ->where('type', $evaluationType)
                ->count();
        } else {
            // Legacy schema: use assessment_components
            $componentCount = AssessmentComponent::where('period_id', $group->period_id)
                ->where('type', $evaluationType)
                ->count();
        }

        if ($componentCount === 0) {
            return 'not_configured';
        }

        // For per-student evaluations (BIMBINGAN_TA), only check for the specific student
        if ($studentId !== null) {
            $expectedScores = $componentCount;

            $actualScores = AssessmentScoreRepository::forType($evaluationType)
                ->where('group_id', $groupId)
                ->where('evaluator_id', $supervisorId)
                ->where('student_id', $studentId)
                ->count();
        } else {
            // For group-level evaluations, check all students
            $studentCount = GroupMember::where('group_id', $groupId)->count();
            $expectedScores = $componentCount * $studentCount;

            $actualScores = AssessmentScoreRepository::forType($evaluationType)
                ->where('group_id', $groupId)
                ->where('evaluator_id', $supervisorId)
                ->count();
        }

        if ($actualScores === 0) {
            return 'PENDING';
        } elseif ($actualScores < $expectedScores) {
            return 'PARTIAL';
        } else {
            return 'COMPLETED';
        }
    }

    /**
     * Get schedule info for a specific evaluation type
     */
    public function getScheduleForType(int $groupId, string $evaluationType): ?array
    {
        $scheduleType = match ($evaluationType) {
            'BIMBINGAN_SEMPRO' => 'SEMINAR',
            'BIMBINGAN_TA' => 'TA_DEFENSE',
            'MILESTONE' => null,
            'NILAI_DOSEN' => null,
            default => null,
        };

        if (! $scheduleType) {
            return null;
        }

        if ($scheduleType === 'SEMINAR') {
            $schedule = SeminarSchedule::where('group_id', $groupId)
                ->latest()
                ->first();
            if ($schedule) {
                $deadline = null;
                if ($schedule->date) {
                    $deadline = date('Y-m-d H:i:s', strtotime($schedule->date.' +2 days'));
                }

                return [
                    'id' => $schedule->id,
                    'type' => 'SEMINAR',
                    'date' => $schedule->date,
                    'room' => $schedule->room,
                    'evaluation_deadline' => $deadline,
                ];
            }
        } elseif ($scheduleType === 'TA_DEFENSE') {
            $schedule = TaDefenseSchedule::where('group_id', $groupId)
                ->latest()
                ->first();
            if ($schedule) {
                $deadline = null;
                if ($schedule->date) {
                    $deadline = date('Y-m-d H:i:s', strtotime($schedule->date.' +2 days'));
                }

                return [
                    'id' => $schedule->id,
                    'type' => 'TA_DEFENSE',
                    'date' => $schedule->date,
                    'room' => $schedule->room,
                    'evaluation_deadline' => $deadline,
                ];
            }
        }

        return null;
    }

    /**
     * Persisted soft deadline for schedule-less supervisor evaluations.
     *
     * Previously recomputed as now + 7 days on every request, so the value
     * drifted forever and could never pass. Now resolved once and stored
     * on the group: seeded from the EXPO schedule (date + 2 days) when one
     * exists, otherwise now + 7 days.
     */
    public function resolveSoftDeadline(Group $group, string $evaluationType): ?string
    {
        $column = match ($evaluationType) {
            'NILAI_DOSEN' => 'nilai_dosen_deadline',
            'MILESTONE' => 'milestone_deadline',
            default => null,
        };

        if (! $column) {
            return null;
        }

        // Pre-migration fallback: columns missing → previous transient
        // behavior (no persistence), so old databases keep working.
        if (! Schema::hasColumn('capstone_groups', $column)) {
            $expo = SeminarSchedule::where('group_id', $group->id)
                ->where('type', 'EXPO')
                ->orderByDesc('id')
                ->first();

            return ($expo && $expo->date)
                ? EvaluationDeadline::fromDate($expo->date)
                : date('Y-m-d H:i:s', strtotime('+7 days'));
        }

        if ($group->{$column}) {
            return date('Y-m-d H:i:s', strtotime((string) $group->{$column}));
        }

        $expo = SeminarSchedule::where('group_id', $group->id)
            ->where('type', 'EXPO')
            ->orderByDesc('id')
            ->first();

        $deadline = ($expo && $expo->date)
            ? EvaluationDeadline::fromDate($expo->date)
            : date('Y-m-d H:i:s', strtotime('+7 days'));

        $group->update([$column => $deadline]);

        return $deadline;
    }

    /**
     * Get evaluator role (examiner or supervisor)
     */
    public function getEvaluatorRole(int $evaluatorId, int $groupId): string
    {
        // Check if supervisor
        $supervision = Supervision::where('group_id', $groupId)
            ->where('supervisor_id', $evaluatorId)
            ->first();

        if ($supervision) {
            return $supervision->role === 'SUPERVISOR_1' ? 'Dosbing 1' : 'Dosbing 2';
        }

        // Check if examiner
        $isSeminarExaminer = SeminarEvaluation::whereHas('schedule', fn ($q) => $q->where('group_id', $groupId))
            ->where('examiner_id', $evaluatorId)
            ->exists();

        if ($isSeminarExaminer) {
            return 'Examiner';
        }

        $isTaExaminer = TaDefenseExaminer::whereHas('schedule', fn ($q) => $q->where('group_id', $groupId))
            ->where('examiner_id', $evaluatorId)
            ->exists();

        if ($isTaExaminer) {
            return 'Examiner';
        }

        return 'Unknown';
    }
}
