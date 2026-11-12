<?php

namespace Modules\Capstone\Support;

use App\Models\User;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\GroupStateMachine;

/** Shared CTMS prerequisites for Blade data and student mutations. */
final class StudentTitleAccess
{
    /**
     * Group statuses that may still propose a student title. These are group
     * lifecycle states only — title-approval states (`PENDING`, `REJECTED`,
     * `UNDER_REVIEW`) live on `titles.supervisor_approval_status` and are
     * checked separately in proposalFlow/bidFlow, never here.
     */
    public const PROPOSABLE_STATUSES = ['READY_FOR_BIDDING', 'FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL'];

    public const BIDDABLE_STATUSES = ['FORMING', 'READY_FOR_BIDDING', 'WAITING_SUPERVISOR_APPROVAL'];

    public const BIDDABLE_SOLO_STATUSES = ['FORMING_SOLO', 'FORMING', 'READY_FOR_BIDDING', 'WAITING_SUPERVISOR_APPROVAL'];

    /**
     * Group statuses whose approved titles stay open for recruitment.
     * Covers solo seekers AND merged (ex-solo) groups that fell below
     * minimum members. Locked/finalized groups are excluded.
     */
    public const RECRUITABLE_STATUSES = ['FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL', 'TITLE_APPROVED', 'READY_FOR_BIDDING'];

    /**
     * Solo Seeker statuses — FORMING_SOLO and FORMING transition to
     * WAITING_SUPERVISOR_APPROVAL after proposing a title, then back to
     * FORMING/FORMING_SOLO after Pre-Approval. TITLE_APPROVED is the status
     * after supervisor approves - title is open for recruitment.
     */
    public const SOLO_STATUSES = ['FORMING_SOLO', 'FORMING', 'WAITING_SUPERVISOR_APPROVAL', 'TITLE_APPROVED'];

    public static function membership(User $user): ?GroupMember
    {
        return GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->whereHas('group', fn ($q) => $q->whereNotIn('status', ['CLOSED', 'DISSOLVED']))
            ->with('group.period')->first();
    }

    public static function proposals(Group $group)
    {
        return Title::where('proposed_by_group_id', $group->id)->where('title_source', 'STUDENT');
    }

    public static function periodReason(Group $group): ?string
    {
        if (! $group->relationLoaded('period')) {
            $group->loadMissing('period');
        }

        return PeriodGate::reason($group->period);
    }

    public static function proposalFlow(?GroupMember $member, ?int $exceptTitle = null): array
    {
        $group = $member?->group;
        $reason = ! $group ? 'NO_GROUP' : (! $member->is_leader ? 'LEADER_ONLY' : self::periodReason($group));
        if (! $reason && $group->title_id) {
            $reason = 'TITLE_ALREADY_ASSIGNED';
        }
        if (! $reason && ! $group->is_solo && $group->members()->count() < ($group->period->min_group_size ?? 3)) {
            $reason = 'INSUFFICIENT_MEMBERS';
        }
        if (! $reason && ! in_array($group->status, self::PROPOSABLE_STATUSES, true)) {
            $reason = 'INVALID_GROUP_STATUS';
        }
        $proposals = $group ? self::proposals($group)->when($exceptTitle, fn ($q) => $q->where('id', '!=', $exceptTitle)) : null;
        if (! $reason && (clone $proposals)->whereIn('supervisor_approval_status', ['PENDING', 'UNDER_REVIEW'])->exists()) {
            $reason = 'PENDING_PROPOSAL_EXISTS';
        }
        if (! $reason && ! $group->is_solo && $group->bids()->where(fn ($q) => $q->where('status', 'PENDING')->orWhere('lecturer_recommendation', 'ACCEPT'))->exists()) {
            $reason = 'ACTIVE_BID_EXISTS';
        }
        if (! $reason) {
            $count = $group->bids()->where(fn ($q) => $q->whereNull('lecturer_recommendation')->orWhere('lecturer_recommendation', 'ACCEPT'))->count();
            $count += (clone $proposals)->whereIn('supervisor_approval_status', ['PENDING', 'UNDER_REVIEW', 'APPROVED'])->count();
            if ($count >= 3) {
                $reason = 'TITLE_LIMIT_REACHED';
            }
        }

        return ['can_create_proposal' => $reason === null, 'reason' => $reason];
    }

    public static function bidFlow(?GroupMember $member): array
    {
        $group = $member?->group;
        $reason = ! $group ? 'NO_GROUP' : (! $member->is_leader ? 'LEADER_ONLY' : self::periodReason($group));
        $isSolo = $group !== null && GroupStateMachine::isSolo($group);
        $memberCount = $group ? $group->members()->count() : 0;
        $minSize = $group?->period->min_group_size ?? 3;
        if (! $reason && $isSolo && $memberCount < $minSize) {
            $reason = 'INSUFFICIENT_MEMBERS';
        }
        if (! $reason && ! $isSolo && ! in_array($group->status, self::BIDDABLE_STATUSES, true)) {
            $reason = 'INVALID_GROUP_STATUS';
        }
        if (! $reason && $isSolo && ! in_array($group->status, self::BIDDABLE_SOLO_STATUSES, true)) {
            $reason = 'INVALID_GROUP_STATUS';
        }
        if (! $reason && $group->title_id) {
            $reason = 'TITLE_ALREADY_ASSIGNED';
        }
        if (! $reason && $group->period->isBiddingLocked()) {
            $reason = 'BIDDING_LOCKED';
        }
        $canManage = $reason === null;
        if (! $reason && ! $group->period->isBiddingOpen()) {
            $reason = 'BIDDING_WINDOW_CLOSED';
        }
        if (! $reason && $group->members()->count() < ($group->period->min_group_size ?? 3)) {
            $reason = 'INSUFFICIENT_MEMBERS';
        }
        if (! $reason && self::proposals($group)->whereIn('supervisor_approval_status', ['PENDING', 'UNDER_REVIEW', 'APPROVED'])->exists()) {
            $reason = 'ACTIVE_PROPOSAL_EXISTS';
        }
        if (! $reason && $group->bids()->where('status', '!=', 'REJECTED')->count() >= 3) {
            $reason = 'TITLE_LIMIT_REACHED';
        }

        return ['can_submit_bid' => $reason === null, 'can_reorder_bid' => $canManage, 'can_delete_bid' => $canManage, 'reason' => $reason];
    }
}
