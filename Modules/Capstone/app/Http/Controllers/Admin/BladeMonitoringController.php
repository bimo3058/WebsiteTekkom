<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Models\StudentPeerReviewStatus;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\StudentFlagService;
use Modules\Capstone\Support\BladeFeatureAccess;

class BladeMonitoringController extends Controller
{
    public function progress(Request $request, GroupService $service)
    {
        $request->validate(['period_id' => 'nullable|integer|exists:capstone_periods,id']);
        $groups = Group::with(['period', 'title', 'members.student', 'supervisor1', 'supervisor2', 'documents'])
            ->when($request->filled('period_id'), fn ($q) => $q->where('period_id', $request->integer('period_id')))->latest()->get();
        $requirements = PhaseDocumentRequirement::whereIn('period_id', $groups->pluck('period_id')->unique())->get()->groupBy('period_id');

        return response()->json(['data' => $groups->map(fn ($group) => $service->transformGroupForProgress($group, $group->documents, $requirements->get($group->period_id, collect())))]);
    }

    public function peerReviews(Request $request)
    {
        $request->validate(['period_id' => 'nullable|integer|exists:capstone_periods,id']);
        $groups = Group::with(['period', 'members.student.user'])
            ->whereIn('status', BladeFeatureAccess::PEER_REVIEW_STATUSES)
            ->when($request->filled('period_id'), fn ($q) => $q->where('period_id', $request->integer('period_id')))->get();
        $states = StudentPeerReviewStatus::whereIn('group_id', $groups->modelKeys())->get()->groupBy('group_id');

        return response()->json(['data' => $groups->map(function ($group) use ($states) {
            $groupStates = $states->get($group->id, collect())->keyBy('student_id');
            $members = $group->members->map(function ($member) use ($groupStates) {
                $state = $groupStates->get($member->student_id);

                return ['student_id' => $member->student_id, 'student_name' => $member->student?->user?->name,
                    'student_nim' => $member->student?->student_number, 'has_completed' => (bool) $state?->has_completed_peer_review,
                    'ta_status' => $state?->ta_status ?? 'TA_BLOCKED'];
            });
            $completed = $members->where('has_completed', true)->count();

            return ['group_id' => $group->id, 'group_code' => $group->code ?? 'Group '.$group->id, 'period_name' => $group->period?->name,
                'total_members' => $members->count(), 'completed_count' => $completed,
                'completion_percentage' => $members->count() ? round($completed / $members->count() * 100) : 0, 'members' => $members];
        })]);
    }

    public function remind(Group $group, NotificationService $notifications)
    {
        abort_unless(in_array($group->status, BladeFeatureAccess::PEER_REVIEW_STATUSES, true), 422, 'Peer review belum dibuka untuk kelompok ini.');
        $completed = StudentPeerReviewStatus::where('group_id', $group->id)->where('has_completed_peer_review', true)->pluck('student_id');
        $users = $group->members()->whereNotIn('student_id', $completed)->with('student')->get()
            ->pluck('student.user_id')->filter()->unique()->all();
        $notifications->sendToMany($users, 'PEER_REVIEW_REMINDER', 'Pengingat Peer Review', 'Lengkapi peer review kelompok Anda.', 'Group', $group->id);

        return response()->json(['message' => 'Pengingat dikirim.', 'count' => count($users)]);
    }

    public function message(Group $group, Request $request, NotificationService $notifications)
    {
        $data = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'message' => 'required|string|max:1000',
        ]);
        $memberUserIds = $group->members()->with('student')->get()
            ->pluck('student.user_id')->filter()->unique()->all();
        $targets = array_values(array_intersect($data['user_ids'], $memberUserIds));
        abort_if(empty($targets), 422, 'Pilih minimal satu anggota kelompok.');
        $notifications->sendToMany($targets, 'ADMIN_MESSAGE', 'Pesan dari Admin', $data['message'], 'Group', $group->id);

        return response()->json(['message' => 'Pesan dikirim.', 'count' => count($targets)]);
    }

    public function flagMember(Group $group, int $memberId, Request $request, StudentFlagService $flags)
    {
        $data = $request->validate(['reason' => 'required|string|max:1000']);
        $member = GroupMember::with('student')->where('group_id', $group->id)->findOrFail($memberId);
        abort_unless($member->student, 422, 'Data mahasiswa tidak ditemukan.');
        $period = $group->period ?? Period::findOrFail($group->period_id);
        try {
            $flags->flagStudent($period, $member->student, $request->user(), $data['reason']);
        } catch (DomainRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Mahasiswa di-flag dari periode.']);
    }

    public function unflagMember(Group $group, int $memberId, Request $request, StudentFlagService $flags)
    {
        $member = GroupMember::withTrashed()->with('student')
            ->where('group_id', $group->id)->findOrFail($memberId);
        abort_unless($member->student, 422, 'Data mahasiswa tidak ditemukan.');
        $period = $group->period ?? Period::findOrFail($group->period_id);
        try {
            $flags->unflagStudent($period, $member->student, $request->user());
        } catch (DomainRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Mahasiswa dikembalikan ke status aktif.']);
    }

    /**
     * Admin force-delete a group in any status.
     * Requires a mandatory reason (min 10 chars) recorded in the audit log.
     */
    public function destroy(Group $group, Request $request, GroupService $groups)
    {
        $data = $request->validate(['reason' => 'required|string|min:10|max:1000']);
        try {
            $count = $groups->adminForceDeleteGroup($group, $request->user(), $data['reason']);
        } catch (DomainRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Kelompok dihapus permanen.', 'affected_students' => $count]);
    }
}
