<?php

namespace Modules\Capstone\Groups;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\WorkflowService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\StudentTitleAccess;

class GroupPresenter
{
    public function __construct(
        protected WorkflowService $workflowService,
    ) {}

    // ======================================================================
    // Status Resolution
    // ======================================================================

    /**
     * Resolve human-readable status label for student view.
     */
    public function resolveStatusLabel(Group $group): string
    {
        if ($group->status === 'READY_FOR_BIDDING' && $group->title_id) {
            return 'Ready for Finalization';
        }

        return $this->resolveAdminStatusLabel($group->status);
    }

    /**
     * Resolve human-readable status label for admin view.
     */
    public function resolveAdminStatusLabel(string $status): string
    {
        return match ($status) {
            'FORMING' => 'Incomplete Group',
            'FORMING_SOLO' => 'Solo Seeker',
            'READY_FOR_BIDDING' => 'Ready for Bidding',
            'WAITING_SUPERVISOR_APPROVAL' => 'Waiting Supervisor Approval',
            'TITLE_APPROVED' => 'Title Approved',
            'READY_FOR_FINALIZATION' => 'Ready for Finalization',
            'KELOMPOK_FINAL' => 'Kelompok Final',
            'PDC1_ACTIVE' => 'PDC1 Active',
            'PDC2_ACTIVE' => 'PDC2 Active',
            default => str_replace('_', ' ', $status),
        };
    }

    /**
     * Resolve group name for admin view.
     */
    public function resolveAdminGroupName(Group $group): string
    {
        $name = trim((string) ($group->name ?? ''));

        if ($name !== '') {
            return $name;
        }

        return $group->code ?? "Kelompok #{$group->id}";
    }

    /**
     * Build canonical group payload with status label and allowed actions.
     */
    public function buildCanonicalGroupPayload(Group $group, User $user): array
    {
        $groupArray = $group->toArray();

        // Surface an approved student proposal as the display title while
        // title_id is still null (admin finalization has not run yet).
        // Governance is preserved: title_id itself is never written here.
        if (empty($groupArray['title_id']) && empty($groupArray['title'])) {
            $approvedProposal = Title::where('proposed_by_group_id', $group->id)
                ->where('title_source', 'STUDENT')
                ->where('supervisor_approval_status', 'APPROVED')
                ->with('proposedSupervisor')
                ->first();
            if ($approvedProposal) {
                $groupArray['title'] = [
                    'id' => $approvedProposal->id,
                    'title' => $approvedProposal->title,
                    'description' => $approvedProposal->description,
                    'lecturer' => $approvedProposal->proposedSupervisor,
                    'title_source' => 'STUDENT',
                ];
            }
        }

        $groupArray['status_label'] = $this->resolveStatusLabel($group);
        $groupArray['allowed_actions'] = $this->resolveAllowedActions($group, $user);

        return $groupArray;
    }

    /**
     * Resolve allowed actions based on group state, membership, and period.
     */
    public function resolveAllowedActions(Group $group, User $user): array
    {
        $period = $group->period;
        $memberCount = $group->members()->count();
        $minSize = $period?->min_group_size ?? 3;
        $maxSize = $period?->max_group_size ?? 4;
        $isLocked = ! $period || ! $period->is_active || $period->is_finalized || ! in_array($group->status, StudentTitleAccess::RECRUITABLE_STATUSES, true);
        $isLeader = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->where('group_id', $group->id)
            ->where('is_leader', true)
            ->exists();

        $hasAcceptedBid = $group->bids()
            ->where('lecturer_recommendation', 'ACCEPT')
            ->exists();

        $hasApprovedProposal = Title::where('proposed_by_group_id', $group->id)
            ->where('title_source', 'STUDENT')
            ->where('supervisor_approval_status', 'APPROVED')
            ->exists();

        $canMarkReady = $isLeader
            && in_array($group->status, ['FORMING_SOLO', 'READY_FOR_BIDDING', 'TITLE_APPROVED'])
            && $period?->is_active && ! $period->is_finalized
            && $memberCount >= $minSize
            && $memberCount <= $maxSize
            && ($hasAcceptedBid || $hasApprovedProposal);

        return [
            'can_add_member' => $isLeader && ! $isLocked && $memberCount < $maxSize,
            'can_remove_member' => $isLeader && ! $isLocked && $memberCount > 1,
            'can_leave_group' => ! $isLeader && ! $isLocked,
            'can_delete_group' => $isLeader && ! $isLocked
                && in_array($group->status, ['FORMING', 'FORMING_SOLO', 'READY_FOR_BIDDING', 'TITLE_APPROVED'])
                && $memberCount <= 1,
            'can_mark_ready_for_finalization' => $canMarkReady,
            'can_cancel_ready_for_finalization' => $isLeader
                && $group->status === 'READY_FOR_FINALIZATION'
                && (bool) ($period?->is_active) && ! $period->is_finalized,
        ];
    }

    // ======================================================================
    // Query Helpers
    // ======================================================================

    /**
     * Resolve user's group data for the index endpoint.
     */
    public function getUserGroupData(User $user, ?int $periodId): array
    {
        $period = $this->resolveUserGroupPeriod($user->id, $periodId);

        $membership = GroupMember::where('student_id', $user->id)
            ->where('period_id', $period?->id)
            ->whereHas('group', fn ($q) => $q->whereNotIn('status', ['CLOSED']))
            ->first();

        if (! $membership || ! $period) {
            return ['group' => null];
        }

        $group = Group::with([
            'members.student', 'title.lecturer', 'period', 'bids.title',
            'supervisorProposals.supervisor1', 'supervisorProposals.supervisor2',
            'supervisions.supervisor', 'supervisor1', 'supervisor2',
        ])->find($membership->group_id);

        return ['group' => $this->buildCanonicalGroupPayload($group, $user)];
    }

    /**
     * Resolve the period for the user's group context (with registration fallback).
     */
    public function resolveUserGroupPeriod(int $userId, ?int $periodId): ?Period
    {
        $period = null;

        if ($periodId) {
            $period = Period::find($periodId);
        }

        if (! $period) {
            $registration = PeriodRegistration::where('user_id', $userId)
                ->where('status', PeriodRegistration::STATUS_APPROVED)
                ->first();
            if ($registration) {
                $period = Period::find($registration->period_id);
            }
        }

        if (! $period) {
            $period = Period::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return $period;
    }

    /**
     * Transform a group for the admin list endpoint.
     */
    public function transformGroupForAdminList(Group $group): array
    {
        $groupArray = $group->toArray();
        $groupArray['name'] = $this->resolveAdminGroupName($group);
        $groupArray['status_label'] = $this->resolveAdminStatusLabel($group->status);
        $groupArray['allowed_actions'] = [
            'can_manage_finalization' => in_array($group->status, ['READY_FOR_FINALIZATION', 'KELOMPOK_FINAL', 'TITLE_APPROVED', 'READY_FOR_BIDDING'], true)
                && ! ($group->period?->is_finalized),
            'reason' => $group->period?->is_finalized ? 'PERIOD_FINALIZED' : null,
        ];

        return $groupArray;
    }

    /**
     * Transform a group for the progress endpoint.
     */
    public function transformGroupForProgress(Group $group, $documents, $periodRequirements): array
    {
        $progressData = null;
        if ($documents->isNotEmpty() || $periodRequirements->isNotEmpty()) {
            try {
                $progressData = $this->workflowService->getWorkflowData($group, $documents, $periodRequirements);
            } catch (\Exception $e) {
                Log::error('Failed to get workflow data', ['group_id' => $group->id, 'error' => $e->getMessage()]);
            }
        }

        $statusOrder = GroupStateMachine::ALL_STATUSES;
        $statusIndex = array_search($group->status, $statusOrder, true);
        $progressPercentage = $statusIndex !== false
            ? min(round((($statusIndex + 1) / count($statusOrder)) * 100), 100)
            : 0;

        return [
            'id' => $group->id,
            'name' => null,
            'status' => $group->status,
            'period_id' => $group->period_id,
            'period' => $group->period ? [
                'id' => $group->period->id,
                'name' => $group->period->name,
                'is_active' => $group->period->is_active,
            ] : null,
            'title' => $group->title ? [
                'id' => $group->title->id,
                'title' => $group->title->title,
            ] : null,
            'supervisor1' => $group->supervisor1 ? [
                'id' => $group->supervisor1->id,
                'name' => $group->supervisor1->name,
            ] : null,
            'supervisor2' => $group->supervisor2 ? [
                'id' => $group->supervisor2->id,
                'name' => $group->supervisor2->name,
            ] : null,
            'members' => $group->members?->map(fn ($m) => [
                'id' => $m->id,
                'student' => [
                    'id' => $m->student?->id,
                    'name' => $m->student?->name,
                    'nim' => $m->student?->nim,
                ],
            ])->toArray() ?? [],
            'members_count' => $group->members?->count() ?? 0,
            'progress' => $progressData,
            'progress_percentage' => $progressPercentage,
        ];
    }

    /**
     * Enrich supervised groups with supervisor role data.
     *
     * @param  Collection  $groups
     */
    public function enrichSupervisedGroups($groups, Lecturer $lecturer): Collection
    {
        return $groups->map(function ($group) use ($lecturer) {
            $supervision = $group->supervisions->firstWhere('supervisor_id', $lecturer->id);
            $dosbing1 = $group->supervisions->firstWhere('role', 'SUPERVISOR_1');
            $dosbing2 = $group->supervisions->firstWhere('role', 'SUPERVISOR_2');

            $group->dosbing_1_name = $dosbing1?->supervisor?->name ?? $group->supervisor1?->name;
            $group->dosbing_2_name = $dosbing2?->supervisor?->name ?? $group->supervisor2?->name;
            $group->is_dosbing_1 = $supervision?->role === 'SUPERVISOR_1'
                || (int) $group->supervisor_1_id === $lecturer->id;
            $group->is_dosbing_2 = $supervision?->role === 'SUPERVISOR_2'
                || (int) $group->supervisor_2_id === $lecturer->id;

            return $group;
        });
    }
}
