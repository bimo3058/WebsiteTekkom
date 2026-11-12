<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Exceptions\GroupActionException;
use Modules\Capstone\Groups\GroupPresenter;
use Modules\Capstone\Http\Requests\Group\AddMemberRequest;
use Modules\Capstone\Http\Requests\Group\AssignSupervisor2Request;
use Modules\Capstone\Http\Requests\Group\ChangeReadyRequest;
use Modules\Capstone\Http\Requests\Group\CreateStudentGroupRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Support\CapstoneActor;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroupController extends Controller
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly GroupPresenter $presenter,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;
        $membership = GroupMember::where('student_id', $studentId)
            ->whereHas('group', function ($q) {
                $q->whereNotIn('status', ['CLOSED']);
            })
            ->first();

        if (! $membership) {
            return response()->json(['group' => null]);
        }

        $group = Group::with([
            'members.student',
            'title.lecturer',
            'period',
            'bids.title',
            'supervisorProposals.supervisor1',
            'supervisorProposals.supervisor2',
            'supervisions.supervisor',
            'supervisor1',
            'supervisor2',
        ])->find($membership->group_id);

        return response()->json(['group' => $this->presenter->buildCanonicalGroupPayload($group, $user)]);
    }

    public function listGroups(Request $request)
    {
        $role = CapstoneActor::role(
            $request->user(),
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );
        $query = Group::with([
            'title',
            'members.student',
            'period',
            'supervisor1',
            'supervisor2',
            'supervisions.supervisor',
        ]);

        $lecturer = null;
        if ($role === 'dosen') {
            $lecturer = CapstoneActor::lecturer($request->user());
            $query->supervisedBy($lecturer->id);
        }

        if ($request->filled('period_id') && $request->input('period_id') !== 'all') {
            $query->where('period_id', $request->integer('period_id'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($groups) use ($search) {
                $groups->where('code', 'like', "%{$search}%")
                    ->orWhereHas('title', fn ($titles) => $titles->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('members.student', function ($students) use ($search) {
                        $students->where('student_number', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($users) => $users
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%"));
                    });
            });
        }

        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $fetchAll = $request->string('per_page')->toString() === 'all';
        $groups = $fetchAll
            ? $query->latest()->get()
            : $query->latest()->paginate($perPage);
        $collection = $fetchAll ? $groups : $groups->getCollection();
        $data = $role === 'admin'
            ? $collection->map(fn (Group $group) => $this->presenter->transformGroupForAdminList($group))
            : $this->presenter->enrichSupervisedGroups($collection, $lecturer);

        return response()->json([
            'data' => $data->values(),
            'pagination' => [
                'current_page' => $fetchAll ? 1 : $groups->currentPage(),
                'last_page' => $fetchAll ? 1 : $groups->lastPage(),
                'per_page' => $fetchAll ? $collection->count() : $groups->perPage(),
                'total' => $fetchAll ? $collection->count() : $groups->total(),
            ],
        ]);
    }

    public function show(Request $request, Group $group)
    {
        $role = CapstoneActor::role(
            $request->user(),
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'dosen') {
            $lecturer = CapstoneActor::lecturer($request->user());
            abort_unless($group->isSupervisedBy($lecturer->id), 403, 'Anda bukan dosen pembimbing kelompok ini.');
        }

        abort_unless(in_array($role, ['admin', 'dosen'], true), 403);

        $group->load([
            'title.lecturer',
            'members.student',
            'period',
            'supervisor1',
            'supervisor2',
            'supervisions.supervisor',
            'documents',
            'schedules',
        ]);

        $requirements = PhaseDocumentRequirement::where('period_id', $group->period_id)->get();
        $progress = $this->presenter->transformGroupForProgress($group, $group->documents, $requirements);
        $group->setAttribute('workflow', $progress['progress']);
        $group->setAttribute('progress_percentage', $progress['progress_percentage']);
        $group->setAttribute('flagged_members', GroupMember::withTrashed()
            ->with('student')
            ->where('group_id', $group->id)
            ->where('status', 'flagged')
            ->whereNotNull('deleted_at')
            ->get());

        return response()->json(['data' => $group]);
    }

    /**
     * Create a new group. Student becomes leader. Status = FORMING.
     */
    public function store(CreateStudentGroupRequest $request)
    {
        return $this->createStudentGroup($request, false);
    }

    public function storeSolo(CreateStudentGroupRequest $request)
    {
        return $this->createStudentGroup($request, true);
    }

    private function createStudentGroup(CreateStudentGroupRequest $request, bool $solo)
    {
        $data = $request->validated();
        $student = CapstoneActor::student($request->user());

        // abort_* guards bubble identically through the handler.
        $group = $this->groupService->createStudentGroupFromRequest($student, (int) $data['period_id'], $solo);

        return response()->json(['group' => $group, 'message' => 'Group created successfully.'], 201);
    }

    public function markReadyForFinalization(ChangeReadyRequest $request)
    {
        return $this->changeReady($request, false);
    }

    public function cancelReadyForFinalization(ChangeReadyRequest $request)
    {
        return $this->changeReady($request, true);
    }

    private function changeReady(ChangeReadyRequest $request, bool $cancel)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $cancel) {
            $group = Group::with('period')->lockForUpdate()->findOrFail($data['group_id']);
            $actions = $this->presenter->resolveAllowedActions($group, $request->user());
            abort_unless($actions[$cancel ? 'can_cancel_ready_for_finalization' : 'can_mark_ready_for_finalization'], 403, 'Group prerequisites are not satisfied or you are not its leader.');
            $group = $this->groupService->transitionReadyState($group, $cancel);

            return response()->json(['group' => $group, 'message' => 'Group status updated.']);
        });
    }

    public function deleteGroup(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $membership = GroupMember::where('student_id', $studentId)
            ->whereHas('group', function ($q) {
                $q->whereNotIn('status', ['CLOSED']);
            })
            ->first();

        if (! $membership) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        if (! $membership->is_leader) {
            return response()->json(['message' => 'Only the group leader can delete the group.'], 403);
        }

        $group = Group::find($membership->group_id);
        abort_unless($this->presenter->resolveAllowedActions($group, $user)['can_delete_group'], 403, 'Group action is locked.');

        try {
            $this->groupService->deleteGroupCascade($group);

            return response()->json(['message' => 'Group deleted successfully.']);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete group: '.$e->getMessage()], 500);
        }
    }

    /**
     * Add a member to the current user's group (leader only).
     */
    public function addMember(AddMemberRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $leaderMembership = GroupMember::where('student_id', $studentId)->first();
        if (! $leaderMembership) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        if (! $leaderMembership->is_leader) {
            return response()->json(['message' => 'Only the group leader can add members.'], 403);
        }

        $group = Group::with('period')->find($leaderMembership->group_id);
        abort_unless($this->presenter->resolveAllowedActions($group, $user)['can_add_member'], 403, 'Group action is locked.');

        try {
            $group = $this->groupService->sendMemberInvitation($group, $user, $validated['email']);

            return response()->json([
                'message' => 'Invitation sent successfully',
                'group' => $group,
            ]);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send invitation: '.$e->getMessage()], 500);
        }
    }

    /**
     * Accept a group invitation.
     */
    public function acceptInvite(Request $request, $id)
    {
        try {
            $this->groupService->acceptInvitationAsStudent($request->user(), (int) $id);

            return response()->json(['message' => 'Invitation accepted.']);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to accept invitation: '.$e->getMessage()], 500);
        }
    }

    /**
     * Reject a group invitation.
     */
    public function rejectInvite(Request $request, $id)
    {
        try {
            $this->groupService->rejectInvitationAsStudent($request->user(), (int) $id);

            return response()->json(['message' => 'Invitation rejected.']);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        }
    }

    /**
     * Remove a member from the group (leader only).
     */
    public function removeMember(Request $request, $memberId)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $leaderMembership = GroupMember::where('student_id', $studentId)->first();
        if (! $leaderMembership) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        if (! $leaderMembership->is_leader) {
            return response()->json(['message' => 'Only the group leader can remove members.'], 403);
        }

        $member = GroupMember::where('id', $memberId)
            ->where('group_id', $leaderMembership->group_id)
            ->first();

        if (! $member) {
            return response()->json(['message' => 'Member not found in your group.'], 404);
        }

        if ($member->student_id === $studentId) {
            return response()->json(['message' => 'You cannot remove yourself.'], 400);
        }

        $group = Group::with('period')->find($leaderMembership->group_id);
        abort_unless($this->presenter->resolveAllowedActions($group, $user)['can_remove_member'], 403, 'Group action is locked.');

        try {
            $result = $this->groupService->removeMemberCascade($group, $member, (int) $user->id);

            if ($result['deleted']) {
                return response()->json(['message' => 'Member removed. Group had no remaining members and was deleted.', 'group' => null, 'group_deleted' => true]);
            }

            return response()->json(['message' => 'Member removed', 'group' => $result['group'], 'membership_shrink' => $result['shrink']]);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to remove member: '.$e->getMessage()], 500);
        }
    }

    /**
     * Leave the group (non-leader only).
     */
    public function leaveGroup(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $membership = GroupMember::where('student_id', $studentId)->first();
        if (! $membership) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        if ($membership->is_leader) {
            return response()->json(['message' => 'Group leaders cannot leave the group. Delete the group instead.'], 400);
        }

        $group = Group::with('period')->find($membership->group_id);
        abort_unless($this->presenter->resolveAllowedActions($group, $user)['can_leave_group'], 403, 'Group action is locked.');

        try {
            $result = $this->groupService->leaveGroupCascade($group, $membership, (int) $user->id);

            if ($result['deleted']) {
                return response()->json(['message' => 'You have left the group. Group had no remaining members and was deleted.', 'group_deleted' => true]);
            }

            return response()->json(['message' => 'You have left the group.', 'membership_shrink' => $result['shrink']]);
        } catch (HttpException $e) {
            throw $e;
        } catch (GroupActionException $e) {
            return response()->json(['message' => $e->getMessage()], $e->httpCode());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to leave group: '.$e->getMessage()], 500);
        }
    }

    public function supervisedGroups(Request $request)
    {
        $lecturer = CapstoneActor::lecturer($request->user());
        $groups = Group::with([
            'title',
            'members.student',
            'period',
            'supervisor1',
            'supervisor2',
            'supervisions.supervisor',
            'documents' => fn ($documents) => $documents->latest(),
        ])
            ->supervisedBy($lecturer->id)
            ->when(
                $request->filled('period_id') && $request->input('period_id') !== 'all',
                fn ($query) => $query->where('period_id', $request->integer('period_id'))
            )
            ->latest()
            ->get();

        return response()->json([
            'data' => $this->presenter->enrichSupervisedGroups($groups, $lecturer)->values(),
        ]);
    }

    public function supervisedStudents(Request $request)
    {
        $lecturer = CapstoneActor::lecturer($request->user());
        $memberships = GroupMember::with(['student.user', 'group.title', 'group.period'])
            ->whereHas('group', function ($groups) use ($lecturer, $request) {
                $groups->supervisedBy($lecturer->id)
                    ->when(
                        $request->filled('period_id') && $request->input('period_id') !== 'all',
                        fn ($query) => $query->where('period_id', $request->integer('period_id'))
                    );
            })
            ->get();

        $students = $memberships->groupBy('student_id')->map(function ($studentMemberships) {
            $student = $studentMemberships->first()->student;

            return [
                'id' => $student->id,
                'user_id' => $student->user_id,
                'name' => $student->name,
                'email' => $student->email,
                'nim' => $student->student_number,
                'cohort_year' => $student->cohort_year,
                'groups' => $studentMemberships->map(fn (GroupMember $membership) => [
                    'id' => $membership->group->id,
                    'code' => $membership->group->code,
                    'status' => $membership->group->status,
                    'title' => $membership->group->title?->title,
                    'period' => $membership->group->period?->name,
                    'is_leader' => $membership->is_leader,
                ])->values(),
            ];
        })->values();

        return response()->json(['data' => $students]);
    }

    public function pendingGroups(Request $request)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;
        $groups = Group::with(['title', 'members.student'])
            ->whereHas('title', function ($query) use ($lecturerId) {
                $query->where('lecturer_id', $lecturerId);
            })
            ->where('status', 'READY_FOR_BIDDING')
            ->get();

        return response()->json(['data' => $groups]);
    }

    /**
     * [Admin] Assign Supervisor 2 (Dosbing 2) to a group. Admin exclusive.
     */
    public function assignSupervisor2(AssignSupervisor2Request $request, Group $group)
    {
        $validated = $request->validated();

        try {
            $group = $this->groupService->assignSupervisor2ByLecturerId(
                $group,
                (int) $validated['supervisor_2_id'],
                (int) $request->user()->id
            );

            return response()->json([
                'message' => 'Supervisor 2 assigned successfully.',
                'group' => $group,
            ]);
        } catch (DomainRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed: '.$e->getMessage()], 500);
        }
    }
}
