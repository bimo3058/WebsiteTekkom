<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\StudentTitleAccess;

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
        $member = StudentTitleAccess::membership($request->user());
        $bids = $member ? Bid::with(['title.lecturer', 'proposedSupervisor1', 'proposedSupervisor2'])->where('group_id', $member->group_id)->orderBy('priority')->get() : [];

        return response()->json(['data' => $bids, 'flow' => StudentTitleAccess::bidFlow($member)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_id' => 'required|integer|exists:capstone_titles,id',
            'priority' => 'sometimes|integer|min:1|max:3',
        ]);

        return DB::transaction(function () use ($request, $data) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader, 403, 'Only the group leader can submit bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group', $group);
            $flow = StudentTitleAccess::bidFlow($member);
            abort_unless($flow['can_submit_bid'], 403, $flow['reason'] ?? 'Bidding locked.');
            $title = Title::lockForUpdate()->findOrFail($data['title_id']);
            abort_if($title->title_source === 'STUDENT' || $title->status !== 'open', 422, 'Title is not open for bidding.');
            // Dosen (LECTURER) titles are cross-period: period_id is ignored for them,
            // including legacy rows created with a period_id. STUDENT titles stay period-locked.
            abort_if($title->title_source !== 'LECTURER' && $title->period_id && (int) $title->period_id !== (int) $group->period_id, 422, 'Title belongs to another period.');
            abort_if($title->groups()->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])->count() >= $title->quota, 422, 'Title quota is full.');
            abort_if($group->bids()->where('title_id', $title->id)->where('status', '!=', 'REJECTED')->exists(), 422, 'You already bid on this title.');
            // Students do not propose supervisors when bidding on lecturer titles;
            // supervisors are assigned at finalization (balancing). Ignore any
            // supervisor fields sent by older clients.
            $used = $group->bids()->where('status', 'PENDING')->pluck('priority')->map(fn ($n) => (int) $n)->all();
            $priority = $data['priority'] ?? collect([1, 2, 3])->first(fn ($n) => ! in_array($n, $used, true));
            abort_if(in_array($priority, $used, true), 422, 'Priority is already used.');
            $bid = Bid::create(['title_id' => $data['title_id'], 'group_id' => $group->id, 'priority' => $priority, 'status' => 'PENDING']);

            return response()->json(['data' => $bid->load(['title.lecturer', 'proposedSupervisor1', 'proposedSupervisor2']), 'message' => 'Bid submitted successfully.'], 201);
        });
    }

    public function destroy(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader, 403, 'Only the group leader can delete bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group', $group);
            abort_unless(StudentTitleAccess::bidFlow($member)['can_delete_bid'], 403, 'Bidding is locked.');
            $bid = $group->bids()->lockForUpdate()->findOrFail($id);
            abort_unless(in_array($bid->status, ['PENDING', 'REJECTED'], true), 403, 'Only pending or rejected bids can be deleted.');
            $bid->delete();
            // Compact ascending priorities of remaining active bids; each lower slot is already vacant.
            foreach ($group->bids()->where('status', 'PENDING')->orderBy('priority')->get() as $index => $remaining) {
                $remaining->update(['priority' => $index + 1]);
            }

            return response()->json(['message' => 'Bid deleted successfully.']);
        });
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['bids' => 'required|array|min:1|max:3', 'bids.*.id' => 'required|integer|distinct', 'bids.*.priority' => 'required|integer|distinct|min:1|max:3']);

        return DB::transaction(function () use ($request, $data) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader, 403, 'Only the group leader can reorder bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group', $group);
            abort_unless(StudentTitleAccess::bidFlow($member)['can_reorder_bid'], 403, 'Bidding is locked.');
            // Only active (PENDING) bids participate in priority ordering; rejected bids live in history.
            $bids = $group->bids()->where('status', 'PENDING')->lockForUpdate()->get();
            $ids = collect($data['bids'])->pluck('id')->sort()->values()->all();
            abort_unless($bids->pluck('id')->sort()->values()->all() === $ids, 422, 'Submit every bid in your own group exactly once.');
            abort_unless(collect($data['bids'])->pluck('priority')->sort()->values()->all() === range(1, $bids->count()), 422, 'Priorities must be consecutive.');
            // A positive temporary range avoids the immediate PostgreSQL unique constraint.
            $offset = (int) $bids->max('priority') + count($bids) + 1;
            foreach ($bids as $index => $bid) {
                $bid->update(['priority' => $offset + $index]);
            }
            foreach ($data['bids'] as $item) {
                $bids->firstWhere('id', $item['id'])->update(['priority' => $item['priority']]);
            }

            return response()->json(['message' => 'Urutan prioritas berhasil disimpan.']);
        });
    }

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
     * Lecturer recommendation on a bid.
     * REJECT is a hard reject: bid moves to status REJECTED (kept in history),
     * its slot is freed automatically, members are notified and the action is logged.
     * CANCEL clears a previous recommendation (undo accept/reject) back to PENDING.
     */
    public function recommend(Request $request, $id)
    {
        $request->validate([
            'recommendation' => 'required|in:ACCEPT,REJECT,CANCEL',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $user = $request->user();
            $lecturerId = CapstoneActor::lecturer($user)->id;

            $bid = Bid::with('title')->lockForUpdate()->findOrFail($id);

            // Verify lecturer owns the title
            if ($bid->title->lecturer_id !== $lecturerId) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Check lock
            $group = Group::with(['period', 'members.student'])->lockForUpdate()->find($bid->group_id);
            if ($this->biddingService->isBiddingLocked($group->period)) {
                return response()->json(['message' => 'Bidding is locked. Cannot change recommendation.'], 400);
            }

            $recommendation = $request->recommendation;

            if ($recommendation === 'REJECT') {
                // Idempotent: already hard-rejected, nothing more to do.
                if ($bid->status === 'REJECTED' && $bid->lecturer_recommendation === 'REJECT') {
                    return response()->json([
                        'message' => 'Recommendation submitted.',
                        'data' => $bid,
                    ]);
                }

                $bid->update(['lecturer_recommendation' => 'REJECT', 'status' => 'REJECTED']);

                // Compact priorities of remaining active bids so slots stay consecutive.
                foreach ($group->bids()->where('status', 'PENDING')->orderBy('priority')->get() as $index => $remaining) {
                    if ((int) $remaining->priority !== $index + 1) {
                        $remaining->update(['priority' => $index + 1]);
                    }
                }

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'BID_REJECTED',
                    'target_type' => 'Bid',
                    'target_id' => $bid->id,
                    'payload' => [
                        'group_id' => $bid->group_id,
                        'title_id' => $bid->title_id,
                        'title' => $bid->title->title ?? null,
                        'lecturer_id' => $lecturerId,
                        'recommendation' => 'REJECT',
                    ],
                ]);

                $titleName = $bid->title->title ?? ('Judul #'.$bid->title_id);
                foreach ($group->members as $gm) {
                    $studentUserId = $gm->student->user_id ?? null;
                    if (! $studentUserId) {
                        continue;
                    }
                    Notification::create([
                        'user_id' => $studentUserId,
                        'type' => 'BID_REJECTED',
                        'title' => 'Bid Ditolak',
                        'message' => "Bid Anda pada judul '{$titleName}' ditolak oleh dosen. Slot bidding Anda telah dibebaskan, silakan bid judul lain.",
                        'related_type' => 'Bid',
                        'related_id' => $bid->id,
                    ]);
                }

                return response()->json([
                    'message' => 'Recommendation submitted.',
                    'data' => $bid->fresh(),
                ]);
            }

            if ($recommendation === 'CANCEL') {
                $wasRejected = $bid->status === 'REJECTED';
                $nextPriority = null;
                if ($wasRejected) {
                    $activeCount = $group->bids()->where('status', 'PENDING')->count();
                    abort_if($activeCount >= 3, 422, 'Active bid slots are full.');
                    $nextPriority = (int) ($group->bids()->where('status', 'PENDING')->max('priority') ?? 0) + 1;
                }
                $bid->update(array_filter([
                    'lecturer_recommendation' => null,
                    'status' => 'PENDING',
                    'priority' => $nextPriority,
                ], fn ($v) => $v !== null));

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'BID_RECOMMENDATION_CANCELLED',
                    'target_type' => 'Bid',
                    'target_id' => $bid->id,
                    'payload' => ['group_id' => $bid->group_id, 'title_id' => $bid->title_id],
                ]);

                return response()->json([
                    'message' => 'Recommendation cancelled.',
                    'data' => $bid->fresh(),
                ]);
            }

            // ACCEPT: advisory only, status stays PENDING until koordinator allocates.
            // If reactivating a previously rejected bid, assign a free priority slot.
            if ($bid->status === 'REJECTED') {
                $activeCount = $group->bids()->where('status', 'PENDING')->count();
                abort_if($activeCount >= 3, 422, 'Active bid slots are full.');
                $nextPriority = (int) ($group->bids()->where('status', 'PENDING')->max('priority') ?? 0) + 1;
                $bid->update(['lecturer_recommendation' => 'ACCEPT', 'status' => 'PENDING', 'priority' => $nextPriority]);
            } else {
                $bid->update(['lecturer_recommendation' => 'ACCEPT']);
            }

            return response()->json([
                'message' => 'Recommendation submitted.',
                'data' => $bid->fresh(),
            ]);
        });
    }
}
