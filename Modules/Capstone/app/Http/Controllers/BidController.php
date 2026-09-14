<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use App\Models\Lecturer;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    protected BiddingService $biddingService;

    public function __construct(BiddingService $biddingService)
    {
        $this->biddingService = $biddingService;
    }

    /**
     * List bids for the current student's group.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)->first();

        if (!$membership) {
            return response()->json(['data' => []]);
        }

        $bids = Bid::with(['title.lecturer', 'proposedSupervisor1', 'proposedSupervisor2'])
            ->where('group_id', $membership->group_id)
            ->orderBy('priority')
            ->get();

        return response()->json(['data' => $bids]);
    }

    /**
     * Submit a bid on a title (group leader only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_id' => 'required|exists:capstone_titles,id',
            'priority' => 'required|integer|min:1',
            'proposed_supervisor_1_id' => 'required|exists:lecturers,id',
            'proposed_supervisor_2_id' => 'nullable|exists:lecturers,id|different:proposed_supervisor_1_id',
        ]);

        $user = $request->user();

        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->first();

        if (!$membership || !$membership->is_leader) {
            return response()->json(['message' => 'Only the group leader can submit bids.'], 403);
        }

        // Validate supervisors are dosen
        $sup1 = Lecturer::whereKey($request->proposed_supervisor_1_id)
            ->whereHas('user.roles', fn ($query) => $query->where('name', 'dosen'))
            ->first();
        if (! $sup1) {
            return response()->json(['message' => 'Proposed supervisor 1 must be a dosen.'], 400);
        }
        if ($request->proposed_supervisor_2_id) {
            $sup2 = Lecturer::whereKey($request->proposed_supervisor_2_id)
                ->whereHas('user.roles', fn ($query) => $query->where('name', 'dosen'))
                ->first();
            if (! $sup2) {
                return response()->json(['message' => 'Proposed supervisor 2 must be a dosen.'], 400);
            }
        }

        $group = Group::with('period')->find($membership->group_id);

        // Status check
        if ($group->status !== 'READY_FOR_BIDDING') {
            return response()->json(['message' => 'Group must be in READY_FOR_BIDDING status to bid.'], 400);
        }

        // Window check
        if ($this->biddingService->isBiddingLocked($group->period)) {
            return response()->json(['message' => 'Bidding is locked.'], 400);
        }

        if (!$this->biddingService->isWindowOpen($group->period)) {
            return response()->json(['message' => 'Bidding window is not open yet.'], 400);
        }

        // Combined limit: bids + student proposals <= 3
        $bidCount = Bid::where('group_id', $group->id)->count();
        $proposalCount = Title::where('proposed_by_group_id', $group->id)
            ->where('title_source', 'STUDENT')
            ->whereIn('supervisor_approval_status', ['PENDING', 'APPROVED'])
            ->count();

        if (($bidCount + $proposalCount) >= 3) {
            return response()->json(['message' => 'Maximum 3 titles allowed (bids + proposals combined).'], 400);
        }

        // DB unique constraints will enforce (group_id, priority) and (group_id, title_id)
        try {
            $bid = Bid::create([
                'group_id' => $group->id,
                'title_id' => $request->title_id,
                'priority' => $request->priority,
                'status' => 'PENDING',
                'proposed_supervisor_1_id' => $request->proposed_supervisor_1_id,
                'proposed_supervisor_2_id' => $request->proposed_supervisor_2_id,
            ]);

            return response()->json([
                'message' => 'Bid submitted successfully.',
                'data' => $bid->load(['title.lecturer', 'proposedSupervisor1', 'proposedSupervisor2']),
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed') || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json(['message' => 'Duplicate bid: you already have a bid with this priority or for this title.'], 400);
            }
            throw $e;
        }
    }

    /**
     * Delete a bid (group leader only, before lock).
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $membership = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->first();

        if (!$membership || !$membership->is_leader) {
            return response()->json(['message' => 'Only the group leader can delete bids.'], 403);
        }

        $bid = Bid::where('id', $id)
            ->where('group_id', $membership->group_id)
            ->firstOrFail();

        $group = Group::with('period')->find($membership->group_id);

        if ($this->biddingService->isBiddingLocked($group->period)) {
            return response()->json(['message' => 'Bidding is locked. Cannot delete bids.'], 400);
        }

        $bid->delete();
        return response()->json(['message' => 'Bid deleted successfully.']);
    }

    /**
     * List bids on the lecturer's titles.
     */
    public function lecturerBids(Request $request)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        $bids = Bid::with(['group.members.student', 'title', 'proposedSupervisor1', 'proposedSupervisor2'])
            ->whereHas('title', function ($q) use ($lecturerId) {
                $q->where('lecturer_id', $lecturerId);
            })
            ->orderBy('title_id')
            ->orderBy('priority')
            ->get();

        return response()->json(['data' => $bids]);
    }

    /**
     * Lecturer recommendation on a bid (ACCEPT/REJECT â€” advisory only).
     */
    public function recommend(Request $request, $id)
    {
        $request->validate([
            'recommendation' => 'required|in:ACCEPT,REJECT',
        ]);

        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        $bid = Bid::with('title')->findOrFail($id);

        // Verify lecturer owns the title
        if ($bid->title->lecturer_id !== $lecturerId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check lock
        $group = Group::with('period')->find($bid->group_id);
        if ($this->biddingService->isBiddingLocked($group->period)) {
            return response()->json(['message' => 'Bidding is locked. Cannot change recommendation.'], 400);
        }

        $bid->update(['lecturer_recommendation' => $request->recommendation]);

        return response()->json([
            'message' => 'Recommendation submitted.',
            'data' => $bid,
        ]);
    }
}
