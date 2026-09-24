<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;

class GroupLifecycleService
{
    /**
     * Group statuses in which each supervisor evaluation type accepts submissions.
     * Outside these windows the penilaian form renders view-only.
     */
    public const EDITABLE_STATUSES = [
        'BIMBINGAN_SEMPRO' => ['PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE'],
        'NILAI_DOSEN' => ['PDC2_ACTIVE', 'TA_DRAFT'],
        'MILESTONE' => ['PDC2_ACTIVE', 'TA_DRAFT'],
        'EXPO' => ['EXPO_REGISTERED', 'EXPO_DONE'],
        'BIMBINGAN_TA' => ['READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'PDC2_COMPLETED'],
    ];

    public function __construct(
        protected GroupStateMachine $stateMachine,
        protected WorkflowService $workflowService
    ) {}

    /**
     * Check whether a supervisor evaluation form is submittable for this group.
     *
     * @return array{editable: bool, reason: ?string, group_status: string}
     */
    public function formAccess(Group $group, string $evaluationType): array
    {
        $allowed = self::EDITABLE_STATUSES[$evaluationType] ?? [];

        if (in_array($group->status, $allowed, true)) {
            return ['editable' => true, 'reason' => null, 'group_status' => $group->status];
        }

        return [
            'editable' => false,
            'reason' => 'This evaluation can only be submitted while the group is in: '.implode(', ', $allowed).". Current status: {$group->status}.",
            'group_status' => $group->status,
        ];
    }

    /**
     * Check TA_DRAFT entry readiness for a group in PDC2_ACTIVE.
     *
     * Entry requires both supervisor-driven PDC2 components to be complete
     * (every assigned supervisor submitted all components x all members).
     * EXPO scores, peer review and expo documents are deliberately excluded;
     * they belong to the later READY_FOR_TA_INDIVIDUAL gate.
     *
     * @return array{ready: bool, pending: string[]}
     */
    public function isTaDraftReady(Group $group): array
    {
        if ($group->status !== 'PDC2_ACTIVE') {
            return ['ready' => false, 'pending' => ['Group is not in PDC2_ACTIVE status.']];
        }

        $pending = [];

        foreach (['NILAI_DOSEN', 'MILESTONE'] as $type) {
            $status = $this->workflowService->getSupervisorEvaluationStatus($group, $type);

            if (($status['component_count'] ?? 0) <= 0) {
                $pending[] = "{$type} components are not configured.";

                continue;
            }

            if (empty($status['supervisors'])) {
                $pending[] = "{$type} has no assigned supervisors.";

                continue;
            }

            if (! ($status['completed'] ?? false)) {
                foreach ($status['supervisors'] as $supervisor) {
                    if (($supervisor['status'] ?? '') !== 'completed') {
                        $pending[] = "{$type} pending: ".($supervisor['name'] ?? 'supervisor')." ({$supervisor['submitted_components']}/{$supervisor['total_components']} scores).";
                    }
                }
            }
        }

        return ['ready' => empty($pending), 'pending' => $pending];
    }

    /**
     * Advance a group to its next lifecycle status when completion criteria
     * are met. Idempotent: returns null (without throwing) when no
     * transition applies or the transition is not allowed from the
     * current status.
     *
     * @return string|null The new status, or null when nothing changed.
     */
    public function advanceIfComplete(Group $group): ?string
    {
        $group->refresh();

        // READY_FOR_SEMPRO → SEMPRO_DONE safety net: the examiner path
        // (SchedulingService::submitSeminarEvaluation) owns this transition
        // including the PASS/FAIL branch, so only mirror a COMPLETED
        // schedule here. Never throws thanks to the canTransition guard.
        if ($group->status === 'READY_FOR_SEMPRO') {
            $sempro = SeminarSchedule::where('group_id', $group->id)
                ->where('type', 'SEMPRO')
                ->orderByDesc('id')
                ->first();

            if ($sempro && $sempro->status === 'COMPLETED') {
                return $this->tryTransition($group, 'SEMPRO_DONE');
            }

            return null;
        }

        // PDC2_ACTIVE → TA_DRAFT once PDC2 supervision scoring is done.
        if ($group->status === 'PDC2_ACTIVE') {
            $readiness = $this->isTaDraftReady($group);

            if ($readiness['ready']) {
                return $this->tryTransition($group, 'TA_DRAFT');
            }

            return null;
        }

        // EXPO schedule completed → EXPO_DONE safety net (examiner path
        // owns the PASS/FAIL branch; mirror a COMPLETED schedule only).
        if ($group->status === 'EXPO_REGISTERED') {
            $expo = SeminarSchedule::where('group_id', $group->id)
                ->where('type', 'EXPO')
                ->orderByDesc('id')
                ->first();

            if ($expo && $expo->status === 'COMPLETED') {
                return $this->tryTransition($group, 'EXPO_DONE');
            }
        }

        return null;
    }

    /**
     * Attempt a guarded transition, logging the advancement.
     *
     * @return string|null The new status, or null when not allowed.
     */
    protected function tryTransition(Group $group, string $target): ?string
    {
        if (! $this->stateMachine->canTransition($group->status, $target)) {
            return null;
        }

        $from = $group->status;
        $this->stateMachine->transition($group, $target);

        Log::info("Group {$group->id} advanced {$from} → {$target} by GroupLifecycleService.");

        return $target;
    }
}
