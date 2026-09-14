<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\{Group, Title, Notification};
use Modules\Capstone\Support\StudentTitleAccess;

class StudentProposalController extends Controller
{
    use ApiResponseTrait;

    public function lecturers()
    {
        return $this->successResponse(Lecturer::with('user')->whereHas('user.roles', fn($q)=>$q->where('name','dosen'))->get()->sortBy('name')->values());
    }

    public function myProposal(Request $request)
    {
        $member = StudentTitleAccess::membership($request->user());
        return $this->successResponse([
            'proposals'=>$member ? StudentTitleAccess::proposals($member->group)->with('proposedSupervisor')->latest()->get() : [],
            'flow'=>StudentTitleAccess::proposalFlow($member),
        ]);
    }

    public function store(Request $request) { return $this->save($request); }
    public function update(Request $request) { return $this->save($request, true); }

    private function save(Request $request, bool $editing = false)
    {
        $data = $request->validate([
            'title_id'=>$editing ? 'required|integer|exists:capstone_titles,id' : 'prohibited',
            'title'=>'required|string|max:255','description'=>'required|string',
            'problem_statement'=>'required|string','scope'=>'required|string',
            'specializations'=>'sometimes|array','specializations.*'=>'string|in:Software,Embedded,Network,Multimedia,AI,Blockchain',
            'proposed_supervisor_id'=>'required|integer|exists:lecturers,id',
        ]);
        return DB::transaction(function () use ($request,$data,$editing) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader, 403, 'Only the group leader can manage proposals.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            $member->setRelation('group',$group);
            $title = $editing ? StudentTitleAccess::proposals($group)->lockForUpdate()->findOrFail($data['title_id']) : null;
            if ($title) abort_unless(in_array($title->supervisor_approval_status,['PENDING','REJECTED','UNDER_REVIEW']),403,'Approved proposals cannot be edited.');
            $flow = StudentTitleAccess::proposalFlow($member, $title?->id);
            abort_unless($flow['can_create_proposal'],403,$flow['reason'] ?? 'Proposal locked.');
            $supervisor = Lecturer::whereKey($data['proposed_supervisor_id'])->whereHas('user.roles',fn($q)=>$q->where('name','dosen'))->first();
            abort_unless($supervisor,422,'Selected supervisor must be a lecturer.');
            $attributes = collect($data)->except('title_id')->all();
            $attributes['lecturer_id']=$supervisor->id;
            $attributes['rejection_reason']=null;
            if (!$title || $title->supervisor_approval_status==='REJECTED') $attributes['supervisor_approval_status']='PENDING';
            if ($title) $title->update($attributes);
            else $title = Title::create([...$attributes,'quota'=>1,'status'=>'open','title_source'=>'STUDENT','proposed_by_group_id'=>$group->id,'period_id'=>$group->period_id]);
            $group->update(['has_active_proposal'=>true]);
            Notification::create(['user_id'=>$supervisor->user_id,'type'=>$editing?'PROPOSAL_UPDATED':'PROPOSAL_SUBMITTED','title'=>$editing?'Updated Title Proposal':'New Title Proposal','message'=>'Proposal from group #'.$group->id.': '.$title->title,'related_type'=>'Title','related_id'=>$title->id]);
            return $this->successResponse(['title'=>$title->load('proposedSupervisor')], 'Proposal saved.', $editing?200:201);
        });
    }

    public function destroy(Request $request, int $id)
    {
        return DB::transaction(function () use ($request,$id) {
            $member = StudentTitleAccess::membership($request->user());
            abort_unless($member && $member->is_leader,403,'Only the group leader can cancel proposals.');
            $group = Group::with('period')->lockForUpdate()->findOrFail($member->group_id);
            abort_if(StudentTitleAccess::periodReason($group)!==null,403,'Period is closed.');
            abort_if($group->title_id || !in_array($group->status,['FORMING','FORMING_SOLO','PENDING','REJECTED','READY_FOR_BIDDING','WAITING_SUPERVISOR_APPROVAL']),403,'Group is locked.');
            $title = StudentTitleAccess::proposals($group)->lockForUpdate()->findOrFail($id);
            abort_unless(in_array($title->supervisor_approval_status,['PENDING','REJECTED','UNDER_REVIEW']),403,'Approved proposals cannot be cancelled.');
            $title->delete();
            $group->update(['has_active_proposal'=>StudentTitleAccess::proposals($group)->whereIn('supervisor_approval_status',['PENDING','UNDER_REVIEW','APPROVED'])->exists()]);
            return $this->successResponse(null,'Proposal dibatalkan.');
        });
    }
}
