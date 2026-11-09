<?php

namespace Modules\Capstone\Services;

use Modules\Capstone\Models\Group;

class ExpoEligibilityService
{
    /**
     * Check if a group is eligible for expo scheduling.
     * Centralized in service (not controller) for reuse from scheduler and other contexts.
     *
     * Eligibility: group.status == PDC2_READY_FOR_EXPO AND at least one
     * APPROVED TA draft document (from the documents/workflow page).
     * A capstone_ta_submissions row is NOT required.
     */
    public function isEligible(Group $group): bool
    {
        if ($group->status !== 'PDC2_READY_FOR_EXPO') {
            return false;
        }

        return $group->hasApprovedTaDraftDocument();
    }
}
