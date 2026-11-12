<?php

namespace Modules\Capstone\Services;

use InvalidArgumentException;
use Modules\Capstone\Models\Group;

class GroupStateMachine
{
    /**
     * All valid state transitions: from => [to, to, ...]
     */
    const TRANSITIONS = [
        'FORMING_SOLO' => ['FORMING', 'READY_FOR_BIDDING', 'TITLE_APPROVED', 'READY_FOR_FINALIZATION'],
        'WAITING_SUPERVISOR_APPROVAL' => ['FORMING', 'READY_FOR_BIDDING', 'TITLE_APPROVED'],
        'TITLE_APPROVED' => ['FORMING', 'READY_FOR_BIDDING', 'READY_FOR_FINALIZATION'],
        'READY_FOR_FINALIZATION' => ['TITLE_APPROVED', 'READY_FOR_BIDDING', 'KELOMPOK_FINAL'],
        'FORMING' => ['READY_FOR_BIDDING', 'TITLE_APPROVED'],
        'READY_FOR_BIDDING' => ['READY_FOR_FINALIZATION', 'KELOMPOK_FINAL', 'FORMING'], // FORMING if members drop below min
        'KELOMPOK_FINAL' => ['PDC1_ACTIVE'],
        'PDC1_ACTIVE' => ['READY_FOR_SEMPRO', 'KELOMPOK_FINAL'], // KELOMPOK_FINAL on period reopen
        'READY_FOR_SEMPRO' => ['SEMPRO_DONE', 'PDC1_ACTIVE'], // PDC1_ACTIVE on sempro fail
        'SEMPRO_DONE' => ['PDC2_ACTIVE'],
        'PDC2_ACTIVE' => ['PDC2_READY_FOR_EXPO', 'TA_DRAFT'],
        'TA_DRAFT' => ['PDC2_READY_FOR_EXPO'],
        'PDC2_READY_FOR_EXPO' => ['EXPO_REGISTERED'],
        'EXPO_REGISTERED' => ['EXPO_DONE', 'PDC2_ACTIVE', 'PDC2_READY_FOR_EXPO'], // Withdrawal returns to ready; failure returns to PDC2.
        'EXPO_DONE' => ['PDC2_COMPLETED'],
        'PDC2_COMPLETED' => ['CLOSED'],
        'CLOSED' => [],
    ];

    /**
     * All valid statuses.
     */
    const ALL_STATUSES = [
        'FORMING_SOLO',
        'FORMING',
        'WAITING_SUPERVISOR_APPROVAL',
        'READY_FOR_BIDDING',
        'TITLE_APPROVED',
        'READY_FOR_FINALIZATION',
        'KELOMPOK_FINAL',
        'PDC1_ACTIVE',
        'READY_FOR_SEMPRO',
        'SEMPRO_DONE',
        'PDC2_ACTIVE',
        'TA_DRAFT',
        'PDC2_READY_FOR_EXPO',
        'EXPO_REGISTERED',
        'EXPO_DONE',
        'PDC2_COMPLETED',
        'READY_FOR_TA_INDIVIDUAL',
        'TA_IN_PROGRESS',
        'CLOSED',
        'DISSOLVED',
    ];

    /**
     * Check if a transition is valid.
     */
    public function canTransition(string $from, string $to): bool
    {
        if (! isset(self::TRANSITIONS[$from])) {
            return false;
        }

        return in_array($to, self::TRANSITIONS[$from]);
    }

    /**
     * Transition a group to a new status.
     *
     * @throws InvalidArgumentException if transition is invalid.
     */
    public function transition(Group $group, string $newStatus): void
    {
        $currentStatus = $group->status;

        if (! $this->canTransition($currentStatus, $newStatus)) {
            throw new InvalidArgumentException(
                "Invalid group state transition: {$currentStatus} → {$newStatus}. ".
                "Allowed transitions from {$currentStatus}: ".
                implode(', ', self::TRANSITIONS[$currentStatus] ?? [])
            );
        }

        $group->status = $newStatus;
        $group->save();
    }

    /**
     * Get available transitions for a group.
     */
    public function getAvailableTransitions(Group $group): array
    {
        return self::TRANSITIONS[$group->status] ?? [];
    }

    /**
     * Get the integer order of a status for comparison.
     */
    public function statusOrder(string $status): int
    {
        $index = array_search($status, self::ALL_STATUSES);

        return $index !== false ? $index : -1;
    }

    /**
     * Check if a group's status is at least a given status.
     */
    public function isAtLeast(Group $group, string $minStatus): bool
    {
        return $this->statusOrder($group->status) >= $this->statusOrder($minStatus);
    }

    /**
     * Whether a group reads as solo: the `is_solo` flag and the `FORMING_SOLO`
     * status mean the same thing. Single home so formation, bidding, and
     * readiness agree on it. (`group_mode === 'INDIVIDUAL'` only affects the
     * minimum-member count and stays at those two call sites.)
     */
    public static function isSolo(Group $group): bool
    {
        $attributes = $group->getAttributes();

        return (bool) ($attributes['is_solo'] ?? false)
            || ($group->status === 'FORMING_SOLO');
    }

    /**
     * Whether a status is sticky: lifecycle states at/after title approval
     * are never recomputed from member counts, only the four formation
     * states are.
     */
    public static function isLifecycleSticky(string $status): bool
    {
        return ! in_array($status, ['FORMING', 'FORMING_SOLO', 'READY_FOR_BIDDING', 'WAITING_SUPERVISOR_APPROVAL'], true);
    }
}
