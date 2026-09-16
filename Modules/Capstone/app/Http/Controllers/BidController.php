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
use Modules\Capstone\Support\StudentTitleAccess;
use Illuminate\Support\Facades\DB;
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
        $member = StudentTitleAccess::membership($request->user());
        $bids = $member ? Bid::with(['title.lecturer','proposedSupervisor1','proposedSupervisor2'])->where('group_id',$member->group_id)->orderBy('priority')->get() : [];
        return response()->json(['data'=>$bids,'flow'=>StudentTitleAccess::bidFlow($member)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title_id'=>'required|integer|exists:capstone_titles,id',
            'priority'=>'sometimes|integer|min:1|max:3',
            'proposed_supervisor_1_id'=>'required|integer|exists:lecturers,id',
            'proposed_supervisor_2_id'=>'nullable|integer|exists:lecturers,id|different:proposed_supervisor_1_id',
        ]);
        return DB::transaction(function () use ($request,$data) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader,403,'Only the group leader can submit bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group',$group);
            $flow = StudentTitleAccess::bidFlow($member);
            abort_unless($flow['can_submit_bid'],403,$flow['reason'] ?? 'Bidding locked.');
            $title = Title::lockForUpdate()->findOrFail($data['title_id']);
            abort_if($title->title_source==='STUDENT' || $title->status!=='open',422,'Title is not open for bidding.');
            abort_if($title->period_id && (int)$title->period_id!==(int)$group->period_id,422,'Title belongs to another period.');
            abort_if($title->groups()->where('status','!=','REJECTED')->count()>=$title->quota,422,'Title quota is full.');
            abort_if($group->bids()->where('title_id',$title->id)->exists(),422,'You already bid on this title.');
            foreach (['proposed_supervisor_1_id','proposed_supervisor_2_id'] as $key) {
                if (empty($data[$key])) continue;
                abort_unless(Lecturer::whereKey($data[$key])->whereHas('user.roles',fn($q)=>$q->where('name','dosen'))->exists(),422,'Supervisor must be a lecturer.');
            }
            $used = $group->bids()->pluck('priority')->map(fn($n)=>(int)$n)->all();
            $priority = $data['priority'] ?? collect([1,2,3])->first(fn($n)=>!in_array($n,$used,true));
            abort_if(in_array($priority,$used,true),422,'Priority is already used.');
            $bid = Bid::create([...$data,'group_id'=>$group->id,'priority'=>$priority,'status'=>'PENDING']);
            return response()->json(['data'=>$bid->load(['title.lecturer','proposedSupervisor1','proposedSupervisor2']),'message'=>'Bid submitted successfully.'],201);
        });
    }

    public function destroy(Request $request, $id)
    {
        return DB::transaction(function () use ($request,$id) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader,403,'Only the group leader can delete bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group',$group);
            abort_unless(StudentTitleAccess::bidFlow($member)['can_delete_bid'],403,'Bidding is locked.');
            $bid = $group->bids()->lockForUpdate()->findOrFail($id);
            abort_unless($bid->status==='PENDING',403,'Only pending bids can be deleted.');
            $bid->delete();
            // Compact ascending priorities; each lower slot is already vacant.
            foreach ($group->bids()->orderBy('priority')->get() as $index=>$remaining) $remaining->update(['priority'=>$index+1]);
            return response()->json(['message'=>'Bid deleted successfully.']);
        });
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['bids'=>'required|array|min:1|max:3','bids.*.id'=>'required|integer|distinct','bids.*.priority'=>'required|integer|distinct|min:1|max:3']);
        return DB::transaction(function () use ($request,$data) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader,403,'Only the group leader can reorder bids.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group',$group);
            abort_unless(StudentTitleAccess::bidFlow($member)['can_reorder_bid'],403,'Bidding is locked.');
            $bids = $group->bids()->lockForUpdate()->get();
            $ids = collect($data['bids'])->pluck('id')->sort()->values()->all();
            abort_unless($bids->pluck('id')->sort()->values()->all()===$ids,422,'Submit every bid in your own group exactly once.');
            abort_unless(collect($data['bids'])->pluck('priority')->sort()->values()->all()===range(1,$bids->count()),422,'Priorities must be consecutive.');
            // A positive temporary range avoids the immediate PostgreSQL unique constraint.
            $offset = (int)$bids->max('priority')+count($bids)+1;
            foreach ($bids as $index=>$bid) $bid->update(['priority'=>$offset+$index]);
            foreach ($data['bids'] as $item) $bids->firstWhere('id',$item['id'])->update(['priority'=>$item['priority']]);
            return response()->json(['message'=>'Urutan prioritas berhasil disimpan.']);
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
