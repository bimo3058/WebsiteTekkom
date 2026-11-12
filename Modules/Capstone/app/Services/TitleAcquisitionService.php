<?php

namespace Modules\Capstone\Services;

use App\Models\User;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Support\StudentTitleAccess;

class TitleAcquisitionService
{
    public function leaderMembership(User $user, string $action): GroupMember
    {
        $member = StudentTitleAccess::membership($user);
        $this->requireLeader($member, $action);

        return $member;
    }

    public function requireLeader(?GroupMember $member, string $action): void
    {
        abort_unless($member && $member->is_leader, 403, "Only the group leader can {$action}.");
    }

    public function lockedGroup(GroupMember $member): Group
    {
        $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
        $member->setRelation('group', $group);

        return $group;
    }

    public function requireBidAction(GroupMember $member, string $key, ?string $fallback = null): void
    {
        $flow = StudentTitleAccess::bidFlow($member);
        abort_unless($flow[$key] ?? false, 403, $fallback ?? $flow['reason'] ?? 'Bidding locked.');
    }

    public function requireProposalAction(GroupMember $member, ?int $exceptTitle = null): void
    {
        $flow = StudentTitleAccess::proposalFlow($member, $exceptTitle);
        abort_unless($flow['can_create_proposal'], 403, $flow['reason'] ?? 'Proposal locked.');
    }
}
