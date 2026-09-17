<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Concerns\RequiresActivePeriod;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SoloTitleController extends Controller
{
    use ApiResponseTrait, RequiresActivePeriod;

    /**
     * List solo seeker titles in marketplace.
     * These are student-proposed titles that have been APPROVED and are open for recruitment.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Respect period_id like BursaIdeController, fallback to active period
        $periodId = $request->query('period_id');
        $period = $periodId
            ? Period::find($periodId)
            : Period::where('is_active', true)->first();
        if (! $period) {
            $period = Period::where('is_active', true)->first();
        }

        // Get current user's group (if any)
        $currentGroupId = null;
        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)->first();
        if ($membership) {
            $currentGroupId = $membership->group_id;
        }

        // Get solo titles that are APPROVED and available for bidding.
        // Returned as full Title models (same shape as BursaIdeController)
        // so the marketplace Student Ideas tab can render them directly.
        $query = Title::with(['proposedByGroup.members.student', 'proposedByGroup.period', 'proposedSupervisor'])
            ->where('title_source', 'STUDENT')
            ->where('supervisor_approval_status', 'APPROVED')
            ->where('status', 'open')
            ->whereHas('proposedByGroup', function ($q) {
                $q->where('is_solo', true);
            });

        if ($period) {
            $query->where('period_id', $period->id);
        }

        $maxSize = $period?->max_group_size ?? 4;

        $soloTitles = $query->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($title) use ($currentGroupId, $maxSize) {
                if (! $title->proposedByGroup) {
                    return false;
                }
                // Only show titles with available slots and not already bid
                if ($maxSize - $title->proposedByGroup->members->count() <= 0) {
                    return false;
                }
                if ($currentGroupId && \Modules\Capstone\Models\Bid::where('group_id', $currentGroupId)
                    ->where('title_id', $title->id)
                    ->exists()) {
                    return false;
                }

                return true;
            })
            ->values();

        return $this->successResponse($soloTitles);
    }

    /**
     * List incoming PENDING bids on the current leader's own solo title(s).
     * This is the owner inbox: without it the solo seeker cannot discover
     * bid_id values needed by acceptBidder()/rejectBidder().
     */
    public function incomingBids(Request $request)
    {
        $user = Auth::user();
        $studentId = CapstoneActor::student($user)->id;

        $ownGroupIds = GroupMember::where('student_id', $studentId)
            ->where('is_leader', true)
            ->whereHas('group', fn ($q) => $q->where('is_solo', true))
            ->pluck('group_id');

        if ($ownGroupIds->isEmpty()) {
            return $this->successResponse([]);
        }

        $bids = \Modules\Capstone\Models\Bid::with(['group.members.student', 'title'])
            ->where('status', 'PENDING')
            ->whereHas('title', fn ($q) => $q->where('title_source', 'STUDENT')
                ->where('supervisor_approval_status', 'APPROVED')
                ->whereIn('proposed_by_group_id', $ownGroupIds))
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->successResponse($bids);
    }

    /**
     * Submit bid to a solo seeker's title.
     * This allows a group to apply to join/merge with the solo seeker's group.
     */
    public function store(Request $request, $titleId)
    {
        $user = Auth::user();
        $student = CapstoneActor::student($user);

        // 1. Check title exists and is a solo title
        $title = Title::find($titleId);

        if (! $title) {
            return $this->notFoundResponse('Judul tidak ditemukan.');
        }

        if ($title->title_source !== 'STUDENT' || $title->supervisor_approval_status !== 'APPROVED') {
            return $this->errorResponse('Judul ini tidak menerima bidder.', 400);
        }

        $soloGroup = $title->proposedByGroup;
        $soloGroup?->loadMissing('period');

        if (! $soloGroup || ! $soloGroup->is_solo) {
            return $this->errorResponse('Judul ini bukan dari solo seeker.', 400);
        }

        // 2. Resolve bidder group. Group leaders bid with their group;
        // ghost students (no group at all) get a temporary 1-person group
        // vessel so they can join the solo seeker as individuals.
        $membership = GroupMember::where('student_id', $student->id)
            ->where('is_leader', true)
            ->first();

        $isGhost = false;
        if (! $membership) {
            $anyMembership = GroupMember::where('student_id', $student->id)->first();
            if ($anyMembership) {
                return $this->unauthorizedResponse('Hanya pemimpin kelompok yang dapat mengajukan bid.');
            }
            $group = Group::create([
                'period_id' => $soloGroup->period_id,
                'status' => 'FORMING',
                'group_mode' => 'GROUP',
                'has_existing_group' => false,
                'is_solo' => false,
            ]);
            GroupMember::create([
                'group_id' => $group->id,
                'student_id' => $student->id,
                'is_leader' => true,
                'period_id' => $soloGroup->period_id,
            ]);
            $group->load('period', 'members');
            $isGhost = true;
        } else {
            $group = Group::with('period', 'members')->find($membership->group_id);
        }

        $this->ensurePeriodIsActive($group);

        $period = $group->period;

        // 3. Check merge quota - total members after merge should not exceed max
        $soloMembers = $soloGroup->members()->count();
        $bidderMembers = $group->members()->count();
        $totalAfterMerge = $soloMembers + $bidderMembers;
        $maxSize = $period->max_group_size ?? 4;

        if ($totalAfterMerge > $maxSize) {
            return $this->errorResponse("Total anggota setelah merge ({$totalAfterMerge}) akan melebihi batas maksimal ({$maxSize}). Kurangi anggota kelompok Anda atau cari judul lain.", 400);
        }

        // 4. Check group has enough members for the bid itself (not for merge).
        // Skipped for ghost individuals joining as a single person.
        $minSize = $period->min_group_size ?? 3;
        if (! $isGhost && $bidderMembers < $minSize) {
            return $this->errorResponse("Kelompok Anda memiliki {$bidderMembers} anggota. Minimal {$minSize} anggota diperlukan untuk mengajukan bid.", 400);
        }

        // 5. Check group preference quota (3-slot limit)
        $bidCount = $group->bids()->count();
        $proposalCount = Title::where('proposed_by_group_id', $group->id)
            ->where('title_source', 'STUDENT')
            ->whereIn('supervisor_approval_status', ['PENDING', 'APPROVED'])
            ->count();

        if (($bidCount + $proposalCount) >= 3) {
            return $this->errorResponse('Grup Anda sudah mencapai batas 3 judul aktif (bid + proposal).', 400);
        }

        // 6. Check not bidding to own title
        if ($group->id === $soloGroup->id) {
            return $this->errorResponse('Anda tidak dapat bid pada judul kelompok sendiri.', 400);
        }

        // 7. Create bid
        // For solo titles, we store it differently - as a special type of bid
        // that indicates intent to join/merge
        $bid = \Modules\Capstone\Models\Bid::create([
            'group_id' => $group->id,
            'title_id' => $title->id,
            'priority' => 1, // Default for solo title bids
            'status' => 'PENDING',
            'proposed_supervisor_1_id' => $title->proposed_supervisor_id,
            'proposed_supervisor_2_id' => null,
        ]);

        // Create notification for solo seeker leader
        $soloLeader = $soloGroup->members()->where('is_leader', true)->first();
        if ($soloLeader) {
            \Modules\Capstone\Models\Notification::create([
                'user_id' => $soloLeader->student->user_id,
                'type' => 'BID_TO_SOLO_TITLE',
                'title' => 'Permintaan Join ke Judul Anda',
                'message' => $isGhost
                    ? "{$student->name} ingin bergabung ke kelompok Anda melalui judul '{$title->title}'. Terima atau tolak permintaan mereka."
                    : "Kelompok {$group->id} mengajukan bid pada judul Anda '{$title->title}'. Terima atau tolak permintaan mereka.",
                'related_type' => 'Bid',
                'related_id' => $bid->id,
            ]);
        }

        return $this->createdResponse([
            'message' => 'Bid berhasil dikirim. Pemilik judul akan menerima notifikasi.',
            'data' => $bid->load(['title', 'group.members.student']),
        ]);
    }

    /**
     * Accept a bidder - solo seeker accepts and merges groups.
     * Only the solo group leader can do this.
     */
    public function acceptBidder(Request $request, $titleId)
    {
        $user = Auth::user();
        $bidId = $request->input('bid_id');

        // 1. Must be leader of solo group
        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->where('is_leader', true)
            ->first();

        if (! $membership) {
            return $this->unauthorizedResponse('Hanya pemimpin kelompok yang dapat menerima bidder.');
        }

        $soloGroup = Group::with('period', 'members')->find($membership->group_id);

        $this->ensurePeriodIsActive($soloGroup);

        if (! $soloGroup->is_solo) {
            return $this->errorResponse('Anda bukan kelompok solo seeker.', 400);
        }

        // 2. Verify ownership of the title
        $title = Title::find($titleId);
        if (! $title || $title->proposed_by_group_id !== $soloGroup->id) {
            return $this->notFoundResponse('Judul tidak ditemukan atau bukan milik Anda.');
        }

        // 3. Get the bid
        $bid = \Modules\Capstone\Models\Bid::with('group.members')->find($bidId);
        if (! $bid || (int) $bid->title_id !== (int) $titleId) {
            return $this->notFoundResponse('Bid tidak ditemukan.');
        }

        $bidderGroup = $bid->group;

        // 4. Verify merge quota
        $period = $soloGroup->period;
        $maxSize = $period->max_group_size ?? 4;
        $totalMembers = $soloGroup->members()->count() + $bidderGroup->members()->count();

        if ($totalMembers > $maxSize) {
            return $this->errorResponse("Total anggota melebihi batas maximal ({$maxSize}).", 400);
        }

        // 5. Execute merge
        DB::beginTransaction();
        try {
            // Move all members from bidder group to solo group
            foreach ($bidderGroup->members as $member) {
                $member->update(['group_id' => $soloGroup->id]);
            }

            // Update solo group - no longer solo; status follows member count
            $memberCount = $soloGroup->members()->count();
            $minSize = $soloGroup->period->min_group_size ?? 3;
            $soloGroup->update([
                'is_solo' => false,
                'status' => $memberCount >= $minSize ? 'READY_FOR_BIDDING' : 'FORMING',
            ]);

            // Update the bid status
            $bid->update(['status' => 'ACCEPTED']);

            // Delete the bidder group
            $bidderGroup->delete();

            // NOTE: title_id is intentionally NOT set here. Title assignment
            // is admin-finalization-owned (FinalizationService::allocateStudentProposed).
            // The approved proposal is surfaced as the display title via
            // GroupService::buildCanonicalGroupPayload() until then.

            // Notify bidder group members
            foreach ($bid->group->members as $member) {
                \Modules\Capstone\Models\Notification::create([
                    'user_id' => $member->student->user_id,
                    'type' => 'BID_ACCEPTED',
                    'title' => 'Bid Diterima',
                    'message' => "Permintaan join Anda pada judul '{$title->title}' telah diterima. Anda sekarang bergabung dengan kelompok baru.",
                    'related_type' => 'Group',
                    'related_id' => $soloGroup->id,
                ]);
            }

            DB::commit();

            return $this->successResponse([
                'message' => 'Bidder berhasil digabungkan ke kelompok Anda.',
                'group' => $soloGroup->fresh(['members.student', 'period'])->load('title'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Gagal menggabungkan bidder: '.$e->getMessage(), 500);
        }
    }

    /**
     * Reject a bidder - solo seeker rejects the application.
     */
    public function rejectBidder(Request $request, $titleId)
    {
        $user = Auth::user();
        $bidId = $request->input('bid_id');

        // 1. Must be leader of solo group
        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->where('is_leader', true)
            ->first();

        if (! $membership) {
            return $this->unauthorizedResponse('Hanya pemimpin kelompok yang dapat menolak bidder.');
        }

        $soloGroup = Group::find($membership->group_id);

        $this->ensurePeriodIsActive($soloGroup);

        if (! $soloGroup->is_solo) {
            return $this->errorResponse('Anda bukan kelompok solo seeker.', 400);
        }

        // 2. Verify ownership
        $title = Title::find($titleId);
        if (! $title || $title->proposed_by_group_id !== $soloGroup->id) {
            return $this->notFoundResponse('Judul tidak ditemukan atau bukan milik Anda.');
        }

        // 3. Get and reject the bid
        $bid = \Modules\Capstone\Models\Bid::find($bidId);
        if (! $bid || (int) $bid->title_id !== (int) $titleId) {
            return $this->notFoundResponse('Bid tidak ditemukan.');
        }

        $bid->update(['status' => 'REJECTED']);

        // Notify bidder
        foreach ($bid->group->members as $member) {
            \Modules\Capstone\Models\Notification::create([
                'user_id' => $member->student->user_id,
                'type' => 'BID_REJECTED',
                'title' => 'Bid Ditolak',
                'message' => "Permintaan join Anda pada judul '{$title->title}' telah ditolak.",
                'related_type' => 'Bid',
                'related_id' => $bid->id,
            ]);
        }

        return $this->successResponse(null, 'Bid berhasil ditolak.');
    }
}
