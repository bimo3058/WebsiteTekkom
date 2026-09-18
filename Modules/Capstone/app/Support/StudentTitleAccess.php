<?php

namespace Modules\Capstone\Support;

use App\Models\User;
use Modules\Capstone\Models\{Group, GroupMember, Bid, Title};

/** Shared CTMS prerequisites for Blade data and student mutations. */
final class StudentTitleAccess
{
    public static function membership(User $user): ?GroupMember
    {
        return GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->whereHas('group', fn($q)=>$q->whereNotIn('status', ['CLOSED','DISSOLVED']))
            ->with('group.period')->first();
    }

    public static function proposals(Group $group)
    {
        return Title::where('proposed_by_group_id', $group->id)->where('title_source', 'STUDENT');
    }

    public static function periodReason(Group $group): ?string
    {
        if (!$group->period || !$group->period->is_active) return 'NO_ACTIVE_PERIOD';
        if ($group->period->is_finalized) return 'PERIOD_FINALIZED';
        return null;
    }

    public static function proposalFlow(?GroupMember $member, ?int $exceptTitle = null): array
    {
        $group = $member?->group;
        $reason = !$group ? 'NO_GROUP' : (!$member->is_leader ? 'LEADER_ONLY' : self::periodReason($group));
        if (!$reason && $group->title_id) $reason = 'TITLE_ALREADY_ASSIGNED';
        if (!$reason && !$group->is_solo && $group->members()->count() < ($group->period->min_group_size ?? 3)) $reason = 'INSUFFICIENT_MEMBERS';
        if (!$reason && !in_array($group->status, ['PENDING','READY_FOR_BIDDING','REJECTED','FORMING','FORMING_SOLO','WAITING_SUPERVISOR_APPROVAL'])) $reason = 'INVALID_GROUP_STATUS';
        $proposals = $group ? self::proposals($group)->when($exceptTitle, fn($q)=>$q->where('id','!=',$exceptTitle)) : null;
        if (!$reason && (clone $proposals)->whereIn('supervisor_approval_status',['PENDING','UNDER_REVIEW'])->exists()) $reason = 'PENDING_PROPOSAL_EXISTS';
        if (!$reason && !$group->is_solo && $group->bids()->where(fn($q)=>$q->where('status','PENDING')->orWhere('lecturer_recommendation','ACCEPT'))->exists()) $reason = 'ACTIVE_BID_EXISTS';
        if (!$reason) {
            $count = $group->bids()->where(fn($q)=>$q->whereNull('lecturer_recommendation')->orWhere('lecturer_recommendation','ACCEPT'))->count();
            $count += (clone $proposals)->whereIn('supervisor_approval_status',['PENDING','UNDER_REVIEW','APPROVED'])->count();
            if ($count >= 3) $reason = 'TITLE_LIMIT_REACHED';
        }
        return ['can_create_proposal'=>$reason===null, 'reason'=>$reason];
    }

    public static function bidFlow(?GroupMember $member): array
    {
        $group = $member?->group;
        $reason = !$group ? 'NO_GROUP' : (!$member->is_leader ? 'LEADER_ONLY' : self::periodReason($group));
        $isSolo = (bool) ($group?->is_solo || $group?->status === 'FORMING_SOLO');
        $memberCount = $group ? $group->members()->count() : 0;
        $minSize = $group?->period->min_group_size ?? 3;
        if (!$reason && $isSolo && $memberCount < $minSize) $reason = 'INSUFFICIENT_MEMBERS';
        if (!$reason && !$isSolo && !in_array($group->status, ['FORMING','READY_FOR_BIDDING','WAITING_SUPERVISOR_APPROVAL'])) $reason = 'INVALID_GROUP_STATUS';
        if (!$reason && $isSolo && !in_array($group->status, ['FORMING_SOLO','FORMING','READY_FOR_BIDDING','WAITING_SUPERVISOR_APPROVAL'])) $reason = 'INVALID_GROUP_STATUS';
        if (!$reason && $group->title_id) $reason = 'TITLE_ALREADY_ASSIGNED';
        if (!$reason && $group->period->isBiddingLocked()) $reason = 'BIDDING_LOCKED';
        $canManage = $reason===null;
        if (!$reason && !$group->period->isBiddingOpen()) $reason = 'BIDDING_WINDOW_CLOSED';
        if (!$reason && $group->members()->count()<($group->period->min_group_size??3)) $reason='INSUFFICIENT_MEMBERS';
        if (!$reason && self::proposals($group)->whereIn('supervisor_approval_status',['PENDING','UNDER_REVIEW','APPROVED'])->exists()) $reason='ACTIVE_PROPOSAL_EXISTS';
        if (!$reason && $group->bids()->where('status','!=','REJECTED')->count()>=3) $reason='TITLE_LIMIT_REACHED';
        return ['can_submit_bid'=>$reason===null,'can_reorder_bid'=>$canManage,'can_delete_bid'=>$canManage,'reason'=>$reason];
    }
}
