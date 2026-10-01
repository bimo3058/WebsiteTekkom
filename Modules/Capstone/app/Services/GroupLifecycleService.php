<?php

namespace Modules\Capstone\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;

class GroupLifecycleService
{
    /**
     * Group statuses in which each supervisor evaluation type accepts submissions.
     * Outside these windows the penilaian form renders view-only.
     *
     * NILAI_DOSEN and MILESTONE open at PDC2_ACTIVE and stay open through
     * every later status (no EXPO schedule or registration required).
     * Pembimbing EXPO evaluation was removed — EXPO grades come from the
     * members' self-evaluations instead, so EXPO has no editable window.
     */
    public const EDITABLE_STATUSES = [
        'BIMBINGAN_SEMPRO' => ['PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE'],
        'NILAI_DOSEN' => ['PDC2_ACTIVE', 'TA_DRAFT', 'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS'],
        'MILESTONE' => ['PDC2_ACTIVE', 'TA_DRAFT', 'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS'],
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
     * Cascades: a transition may immediately unlock the next one (e.g. the
     * examiner submit that completes both the EXPO stage and final TA
     * readiness lands the group straight on PDC2_COMPLETED instead of
     * stranding it on EXPO_DONE with no further submit to trigger the
     * next step). Statuses only move forward, so the loop always
     * terminates; the iteration cap is a backstop.
     *
     * Never throws: readiness checks touch several score tables, so a
     * degraded/partial schema (or any query failure) degrades to null
     * instead of breaking the caller's submit flow.
     *
     * @return string|null The final status reached, or null when nothing changed.
     */
    public function advanceIfComplete(Group $group): ?string
    {
        try {
            $last = null;

            for ($i = 0; $i < 5; $i++) {
                try {
                    $advanced = $this->doAdvanceIfComplete($group);
                } catch (QueryException $e) {
                    Log::warning("Group {$group->id} lifecycle check skipped: {$e->getMessage()}");

                    return $last;
                }

                if ($advanced === null) {
                    return $last;
                }

                $last = $advanced;
            }

            return $last;
        } catch (QueryException $e) {
            Log::warning("Group {$group->id} lifecycle check skipped: {$e->getMessage()}");

            return null;
        }
    }

    protected function doAdvanceIfComplete(Group $group): ?string
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

        // SEMPRO_DONE → PDC2_ACTIVE once the whole SEMPRO stage is
        // complete (all examiners submitted + BIMBINGAN_SEMPRO done).
        // Scores alone never advanced the group past SEMPRO_DONE; this
        // closes that gap so PDC2 unlocks without manual intervention.
        if ($group->status === 'SEMPRO_DONE') {
            if ($this->workflowService->isSemproComplete($group)) {
                return $this->tryTransition($group, 'PDC2_ACTIVE');
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

        // EXPO_REGISTERED → EXPO_DONE once every member's expo document
        // is APPROVED + every member's self-evaluation is submitted.
        // EXPO scores come only from member self-evaluations; there is no
        // examiner evaluation for EXPO, so no schedule state is required.
        if ($group->status === 'EXPO_REGISTERED') {
            if ($this->workflowService->isExpoComplete($group)) {
                return $this->tryTransition($group, 'EXPO_DONE');
            }
        }

        // EXPO_DONE → PDC2_COMPLETED once final TA readiness is met (expo
        // documents + self-evaluations from every member, NILAI_DOSEN and
        // MILESTONE complete, peer review completed). This closes the gap
        // that left groups stranded at EXPO_DONE with no path to
        // TaSubmission (which requires exactly PDC2_COMPLETED).
        if ($group->status === 'EXPO_DONE') {
            if ($this->workflowService->isExpoComplete($group)
                && $this->workflowService->isFinalReadyForTaIndividual($group)) {
                return $this->tryTransition($group, 'PDC2_COMPLETED');
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
