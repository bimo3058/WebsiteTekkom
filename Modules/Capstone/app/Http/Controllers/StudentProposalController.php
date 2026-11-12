<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Http\Requests\Title\StoreStudentProposalRequest;
use Modules\Capstone\Http\Requests\Title\UpdateStudentProposalRequest;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\TitleAcquisitionService;
use Modules\Capstone\Support\StudentTitleAccess;

class StudentProposalController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected TitleAcquisitionService $acquisition) {}

    public function lecturers()
    {
        return $this->successResponse(Lecturer::with('user')->whereHas('user.roles', fn ($q) => $q->where('name', 'dosen'))->get()->sortBy('name')->values());
    }

    public function myProposal(Request $request)
    {
        $member = StudentTitleAccess::membership($request->user());

        return $this->successResponse([
            'proposals' => $member ? StudentTitleAccess::proposals($member->group)->with('proposedSupervisor')->latest()->get() : [],
            'flow' => StudentTitleAccess::proposalFlow($member),
        ]);
    }

    public function store(StoreStudentProposalRequest $request)
    {
        return $this->save($request);
    }

    public function update(UpdateStudentProposalRequest $request)
    {
        return $this->save($request, true);
    }

    private function save(FormRequest $request, bool $editing = false)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $editing) {
            $member = $this->acquisition->leaderMembership($request->user(), 'manage proposals');
            $group = $this->acquisition->lockedGroup($member);
            $title = $editing ? StudentTitleAccess::proposals($group)->lockForUpdate()->findOrFail($data['title_id']) : null;
            if ($title) {
                abort_unless(in_array($title->supervisor_approval_status, ['PENDING', 'REJECTED', 'UNDER_REVIEW']), 403, 'Approved proposals cannot be edited.');
            }
            $this->acquisition->requireProposalAction($member, $title?->id);
            $supervisor = Lecturer::whereKey($data['proposed_supervisor_id'])->whereHas('user.roles', fn ($q) => $q->where('name', 'dosen'))->first();
            abort_unless($supervisor, 422, 'Selected supervisor must be a lecturer.');
            $attributes = collect($data)->except('title_id')->all();
            $attributes['lecturer_id'] = $supervisor->id;
            $attributes['rejection_reason'] = null;
            if (! $title || $title->supervisor_approval_status === 'REJECTED') {
                $attributes['supervisor_approval_status'] = 'PENDING';
            }
            if ($title) {
                $title->update($attributes);
            } else {
                $title = Title::create([...$attributes, 'quota' => 1, 'status' => 'open', 'title_source' => 'STUDENT', 'proposed_by_group_id' => $group->id, 'period_id' => $group->period_id]);
            }
            $group->update(['has_active_proposal' => true]);
            Notification::create(['user_id' => $supervisor->user_id, 'type' => $editing ? 'PROPOSAL_UPDATED' : 'PROPOSAL_SUBMITTED', 'title' => $editing ? 'Updated Title Proposal' : 'New Title Proposal', 'message' => 'Proposal from group #'.$group->id.': '.$title->title, 'related_type' => 'Title', 'related_id' => $title->id]);

            return $this->successResponse(['title' => $title->load('proposedSupervisor')], 'Proposal saved.', $editing ? 200 : 201);
        });
    }

    public function destroy(Request $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $member = $this->acquisition->leaderMembership($request->user(), 'cancel proposals');
            $group = $this->acquisition->lockedGroup($member);
            abort_if(StudentTitleAccess::periodReason($group) !== null, 403, 'Period is closed.');
            abort_if($group->title_id || ! in_array($group->status, StudentTitleAccess::PROPOSABLE_STATUSES, true), 403, 'Group is locked.');
            $title = StudentTitleAccess::proposals($group)->lockForUpdate()->findOrFail($id);
            abort_unless(in_array($title->supervisor_approval_status, ['PENDING', 'REJECTED', 'UNDER_REVIEW']), 403, 'Approved proposals cannot be cancelled.');
            $title->delete();
            $group->update(['has_active_proposal' => StudentTitleAccess::proposals($group)->whereIn('supervisor_approval_status', ['PENDING', 'UNDER_REVIEW', 'APPROVED'])->exists()]);

            return $this->successResponse(null, 'Proposal dibatalkan.');
        });
    }
}
