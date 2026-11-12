<?php

namespace Modules\Capstone\Services;

use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Capstone\Exceptions\UnprocessableFinalizationException;
use Modules\Capstone\Finalization\FinalizationSupport;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\Title;

class FinalizationService
{
    protected GroupStateMachine $stateMachine;

    protected FinalizationSupport $support;

    protected GroupService $groupService;

    public function __construct(
        GroupStateMachine $stateMachine,
        FinalizationSupport $support,
        GroupService $groupService,
    ) {
        $this->stateMachine = $stateMachine;
        $this->support = $support;
        $this->groupService = $groupService;
    }

    /**
     * Enforce period min/max group size before any finalization.
     * Undersized groups (member kicked/left mid-bidding) keep their approved
     * title but must refill to minimum; oversized groups must trim to maximum.
     */
    private function assertGroupSize(Group $group): void
    {
        if (! $group->relationLoaded('period')) {
            $group->load('period');
        }
        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        if ($memberCount < $minSize) {
            throw new InvalidArgumentException("Cannot finalize: group has {$memberCount} members, minimum is {$minSize}. Add members until the minimum is reached (approved title is retained).");
        }
        if ($memberCount > $maxSize) {
            throw new InvalidArgumentException("Cannot finalize: group has {$memberCount} members, maximum is {$maxSize}. Remove members until the maximum is reached.");
        }
    }

    /**
     * Allocate a group to a title via bidding resolution.
     * Single atomic transaction: quota lock, bid resolution, supervisor assignment, state transitions.
     *
     * @param  int  $bidId  The winning bid to accept
     * @param  int  $supervisor1Id  Supervisor 1 user ID
     * @param  int|null  $supervisor2Id  Supervisor 2 user ID (optional)
     * @param  int  $adminId  The admin performing the allocation
     */
    public function allocateGroup(int $bidId, int $supervisor1Id, ?int $supervisor2Id, int $adminId): array
    {
        return DB::transaction(function () use ($bidId, $supervisor1Id, $supervisor2Id, $adminId) {
            // 1. Load the bid with group
            $bid = Bid::with('group.period')->findOrFail($bidId);
            $group = $bid->group;
            $titleId = $bid->title_id;

            // 2. Row lock on title for quota concurrency safety
            $title = Title::where('id', $titleId)->lockForUpdate()->first();
            if (! $title) {
                throw new InvalidArgumentException('Title not found.');
            }

            // GOVERNANCE: If title is student-proposed, it must be approved by supervisor
            if ($title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
                throw new InvalidArgumentException('Cannot finalize: title has not been approved by the supervisor.');
            }

            // STUDENT titles are period-locked; LECTURER titles are cross-period.
            if ($title->title_source === 'STUDENT' && $title->period_id && (int) $title->period_id !== (int) $group->period_id) {
                throw new InvalidArgumentException('Title belongs to another period.');
            }

            // GOVERNANCE: Strict 2-level — bid must be recommended ACCEPT by lecturer
            if ($bid->lecturer_recommendation !== 'ACCEPT') {
                throw new InvalidArgumentException('Bid must be recommended ACCEPT by lecturer before admin can allocate.');
            }

            // 3. Validate quota not exceeded
            $currentAllocations = Group::where('title_id', $titleId)
                ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
                ->count();

            if ($currentAllocations >= $title->quota) {
                throw new InvalidArgumentException('Title quota is full.');
            }

            // 3b. Validate group size (kicked/left member or oversized merge
            // must refill/trim first; approved title is retained).
            $this->assertGroupSize($group);

            // 4. Accept winning bid, reject all others for this title
            $bid->update(['status' => 'ACCEPTED']);

            Bid::where('title_id', $titleId)
                ->where('id', '!=', $bidId)
                ->update(['status' => 'REJECTED']);

            // 5. Reject other bids from this group (for other titles)
            Bid::where('group_id', $group->id)
                ->where('id', '!=', $bidId)
                ->update(['status' => 'REJECTED']);

            // 6. Assign title to group (via explicit method — title_id not in $fillable)
            $group->assignTitleFromFinalization($titleId);
            $group->assignTypeFromFinalization('BIDDING');
            $group->save();

            // 7. Transition to KELOMPOK_FINAL
            $this->stateMachine->transition($group, 'KELOMPOK_FINAL');

            // 8. Create supervision records (source of truth)
            Supervision::create([
                'group_id' => $group->id,
                'supervisor_id' => $supervisor1Id,
                'role' => 'SUPERVISOR_1',
                'assigned_by' => $adminId,
            ]);

            // 9. Update group cache fields
            $group->supervisor_1_id = $supervisor1Id;

            if ($supervisor2Id) {
                Supervision::create([
                    'group_id' => $group->id,
                    'supervisor_id' => $supervisor2Id,
                    'role' => 'SUPERVISOR_2',
                    'assigned_by' => $adminId,
                ]);
                $group->supervisor_2_id = $supervisor2Id;
            }
            $group->save();

            // 10. Transition to PDC1_ACTIVE
            $this->stateMachine->transition($group, 'PDC1_ACTIVE');

            // 11. Audit log
            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_ALLOCATE',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => [
                    'bid_id' => $bidId,
                    'title_id' => $titleId,
                    'supervisor_1_id' => $supervisor1Id,
                    'supervisor_2_id' => $supervisor2Id,
                ],
            ]);

            return [
                'group' => $group->fresh()->load(['title', 'members.student', 'supervisions.supervisor']),
                'bid' => $bid->fresh(),
            ];
        });
    }

    /**
     * Allocate a group with a student-proposed title (same transactional pattern).
     */
    public function allocateStudentProposed(int $groupId, int $titleId, int $supervisor1Id, ?int $supervisor2Id, int $adminId): array
    {
        return DB::transaction(function () use ($groupId, $titleId, $supervisor1Id, $supervisor2Id, $adminId) {
            $group = Group::findOrFail($groupId);

            // Size gate applies to student-proposed allocations as well.
            $this->assertGroupSize($group);

            // GOVERNANCE: Ensure title is approved by supervisor before admin finalization
            $title = Title::findOrFail($titleId);
            if ($title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
                throw new InvalidArgumentException('Cannot finalize: title has not been approved by the supervisor.');
            }

            // STUDENT titles are period-locked; LECTURER titles are cross-period.
            if ($title->title_source === 'STUDENT' && $title->period_id && (int) $title->period_id !== (int) $group->period_id) {
                throw new InvalidArgumentException('Title belongs to another period.');
            }

            // Assign title (via explicit method — title_id not in $fillable)
            $group->assignTitleFromFinalization($titleId);
            $group->assignTypeFromFinalization('STUDENT_PROPOSED');
            $group->save();

            // Transition
            $this->stateMachine->transition($group, 'KELOMPOK_FINAL');

            // Create supervisions
            Supervision::create([
                'group_id' => $group->id,
                'supervisor_id' => $supervisor1Id,
                'role' => 'SUPERVISOR_1',
                'assigned_by' => $adminId,
            ]);
            $group->supervisor_1_id = $supervisor1Id;

            if ($supervisor2Id) {
                Supervision::create([
                    'group_id' => $group->id,
                    'supervisor_id' => $supervisor2Id,
                    'role' => 'SUPERVISOR_2',
                    'assigned_by' => $adminId,
                ]);
                $group->supervisor_2_id = $supervisor2Id;
            }
            $group->save();

            // Transition to PDC1_ACTIVE
            $this->stateMachine->transition($group, 'PDC1_ACTIVE');

            // Audit
            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_ALLOCATE_STUDENT_PROPOSED',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => [
                    'title_id' => $titleId,
                    'supervisor_1_id' => $supervisor1Id,
                    'supervisor_2_id' => $supervisor2Id,
                ],
            ]);

            return [
                'group' => $group->fresh()->load(['title', 'members.student', 'supervisions.supervisor']),
            ];
        });
    }

    /**
     * Get supervisor load dashboard data for a period.
     */
    public function getSupervisorLoad(int $periodId, int $maxLoad): array
    {
        $lecturers = Lecturer::whereHas('user.roles', fn ($query) => $query->where('name', 'dosen'))->get();

        $loadData = [];
        foreach ($lecturers as $lecturer) {
            $currentLoad = Supervision::where('supervisor_id', $lecturer->id)
                ->whereHas('group', fn ($query) => $query->where('period_id', $periodId))
                ->count();
            $loadData[] = [
                'lecturer' => $lecturer,
                'current_load' => $currentLoad,
                'max_load' => $maxLoad,
                'is_overloaded' => $currentLoad >= $maxLoad,
            ];
        }

        return $loadData;
    }

    /**
     * V4: Batch finalize all eligible groups in a period.
     * ⚠ Atomic transaction — all-or-nothing.
     */
    public function finalizePeriod(int $periodId, int $adminId): array
    {
        return DB::transaction(function () use ($periodId, $adminId) {
            // Lock the period row to prevent concurrent finalization
            $period = Period::lockForUpdate()->findOrFail($periodId);

            // Fetch all ACCEPT bids for this period, grouped by title, ordered by priority
            $acceptBids = Bid::where('lecturer_recommendation', 'ACCEPT')
                ->whereHas('group', function ($q) use ($periodId) {
                    $q->where('period_id', $periodId)
                        ->where('status', 'READY_FOR_BIDDING');
                })
                ->with(['group', 'proposedSupervisor1', 'proposedSupervisor2'])
                ->orderBy('priority')
                ->get();

            $allocated = [];
            $skipped = [];
            $titleQuotaUsed = [];

            foreach ($acceptBids as $bid) {
                $titleId = $bid->title_id;
                $group = $bid->group;

                // Skip if group already allocated (by a higher-priority bid)
                if ($group->status !== 'READY_FOR_BIDDING') {
                    continue;
                }

                // Size gate: undersized/oversized groups keep their bids but
                // are skipped until refill/trim.
                try {
                    $this->assertGroupSize($group);
                } catch (InvalidArgumentException $e) {
                    $skipped[] = ['bid_id' => $bid->id, 'reason' => 'GROUP_SIZE_INVALID: '.$e->getMessage(), 'group_id' => $group->id];

                    continue;
                }

                // Check quota
                if (! isset($titleQuotaUsed[$titleId])) {
                    $titleQuotaUsed[$titleId] = Group::where('title_id', $titleId)
                        ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
                        ->count();
                }

                $title = Title::find($titleId);
                if (! $title || $titleQuotaUsed[$titleId] >= $title->quota) {
                    $skipped[] = ['bid_id' => $bid->id, 'reason' => 'Quota full', 'group_id' => $group->id];

                    continue;
                }

                // Allocate: accept bid, reject others
                $bid->update(['status' => 'ACCEPTED']);
                Bid::where('group_id', $group->id)->where('id', '!=', $bid->id)->update(['status' => 'REJECTED']);

                // Assign title
                $group->assignTitleFromFinalization($titleId);
                $group->assignTypeFromFinalization('BIDDING');
                $group->save();

                // Transition READY_FOR_BIDDING → KELOMPOK_FINAL
                $this->stateMachine->transition($group, 'KELOMPOK_FINAL');

                // Assign supervisors
                $sup1Id = $bid->proposed_supervisor_1_id ?? $title->lecturer_id;
                $sup2Id = $bid->proposed_supervisor_2_id;

                Supervision::create([
                    'group_id' => $group->id,
                    'supervisor_id' => $sup1Id,
                    'role' => 'SUPERVISOR_1',
                    'assigned_by' => $adminId,
                ]);
                $group->supervisor_1_id = $sup1Id;

                if ($sup2Id) {
                    Supervision::create([
                        'group_id' => $group->id,
                        'supervisor_id' => $sup2Id,
                        'role' => 'SUPERVISOR_2',
                        'assigned_by' => $adminId,
                    ]);
                    $group->supervisor_2_id = $sup2Id;
                }
                $group->save();

                // Transition KELOMPOK_FINAL → PDC1_ACTIVE
                $this->stateMachine->transition($group, 'PDC1_ACTIVE');

                $titleQuotaUsed[$titleId]++;
                $allocated[] = [
                    'group_id' => $group->id,
                    'title_id' => $titleId,
                    'bid_id' => $bid->id,
                ];
            }

            // Audit
            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'BATCH_FINALIZATION',
                'target_type' => 'Period',
                'target_id' => $periodId,
                'payload' => [
                    'allocated_count' => count($allocated),
                    'skipped_count' => count($skipped),
                ],
            ]);

            return [
                'allocated' => $allocated,
                'skipped' => $skipped,
                'total_allocated' => count($allocated),
                'total_skipped' => count($skipped),
            ];
        });
    }

    /**
     * Execute finalization: transition all valid READY_FOR_FINALIZATION
     * groups to KELOMPOK_FINAL and mark the period finalized.
     *
     * @return array{finalized_count: int, skipped_count: int, skipped: array<int, array{group_id: int, reason: string}>, period: Period}
     */
    public function executeFinalization(int $periodId, int $adminId): array
    {
        $period = Period::findOrFail($periodId);

        if (! $period->is_active) {
            throw new InvalidArgumentException('Cannot execute finalization on an inactive period.');
        }

        if ($period->is_finalized) {
            throw new InvalidArgumentException('Period is already finalized.');
        }

        $groups = Group::where('period_id', $period->id)
            ->where('status', 'READY_FOR_FINALIZATION')
            ->get();

        if ($groups->isEmpty()) {
            throw new InvalidArgumentException('No groups ready for finalization.');
        }

        $finalized = 0;
        $skipped = [];

        DB::transaction(function () use ($groups, $period, $adminId, &$finalized, &$skipped) {
            foreach ($groups as $group) {
                $reason = $this->support->checkReadyForExecute($group);
                if ($reason !== null) {
                    $skipped[] = ['group_id' => $group->id, 'reason' => $reason];

                    continue;
                }

                $oldStatus = $group->status;
                $this->stateMachine->transition($group, 'KELOMPOK_FINAL');
                $group->finalized_at = now();
                $group->finalized_by = $adminId;
                $group->save();

                AuditLog::create([
                    'user_id' => $adminId,
                    'action' => 'FINALIZATION_EXECUTE',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['old_status' => $oldStatus, 'new_status' => 'KELOMPOK_FINAL', 'period_id' => $period->id],
                ]);

                $finalized++;
            }

            if ($finalized === 0) {
                throw new InvalidArgumentException('No eligible groups could be finalized. '.($skipped[0]['reason'] ?? ''));
            }

            $period->is_finalized = true;
            $period->save();
        });

        return [
            'finalized_count' => $finalized,
            'skipped_count' => count($skipped),
            'skipped' => $skipped,
            'period' => $period->fresh(),
        ];
    }

    /**
     * Finalize the period flag when groups were already marked KELOMPOK_FINAL
     * one-by-one so `execute` has no READY_FOR_FINALIZATION rows left.
     *
     * @return array{period: Period, finalized_groups: array<int>, activated_pdc1: array<int>, already_active: array<int>}
     */
    public function flagFinalizePeriod(int $periodId, bool $activatePdc1, int $adminId): array
    {
        $period = Period::findOrFail($periodId);

        if (! $period->is_active) {
            throw new InvalidArgumentException('Cannot finalize an inactive period.');
        }

        if ($period->is_finalized) {
            throw new InvalidArgumentException('Period is already finalized.');
        }

        $base = Group::where('period_id', $period->id);

        $readyLeft = (clone $base)->whereIn('status', FinalizationSupport::READY_STATUSES)->count();
        if ($readyLeft > 0) {
            throw new InvalidArgumentException("Masih ada {$readyLeft} kelompok berstatus siap finalisasi.");
        }

        $notReadyLeft = (clone $base)->whereIn('status', FinalizationSupport::NOT_READY_STATUSES)->count();
        if ($notReadyLeft > 0) {
            throw new InvalidArgumentException("Masih ada {$notReadyLeft} kelompok yang belum siap finalisasi.");
        }

        $finalGroups = (clone $base)->whereIn('status', ['KELOMPOK_FINAL', 'PDC1_ACTIVE'])->with(['period', 'title', 'members'])->get();
        if ($finalGroups->isEmpty()) {
            throw new InvalidArgumentException('Tidak ada kelompok final atau pasca-final pada periode ini.');
        }

        $ineligible = [];
        foreach ($finalGroups as $group) {
            $reason = $this->support->checkReadyForExecute($group);
            if ($reason !== null) {
                $ineligible[] = ['group_id' => $group->id, 'code' => $group->code, 'reason' => $reason];
            }
        }
        if (! empty($ineligible)) {
            throw new UnprocessableFinalizationException('Ada kelompok final yang tidak memenuhi syarat.', ['ineligible' => $ineligible]);
        }

        $activated = [];
        $alreadyActive = [];

        DB::transaction(function () use ($period, $finalGroups, $activatePdc1, $adminId, &$activated, &$alreadyActive) {
            $locked = Period::lockForUpdate()->findOrFail($period->id);
            if ($locked->is_finalized) {
                throw new InvalidArgumentException('Period is already finalized.');
            }
            $locked->is_finalized = true;
            $locked->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'PERIOD_MANUAL_FINALIZED',
                'target_type' => 'Period',
                'target_id' => $locked->id,
                'payload' => [
                    'period_id' => $locked->id,
                    'final_group_ids' => $finalGroups->where('status', 'KELOMPOK_FINAL')->pluck('id')->all(),
                    'pdc1_group_ids' => $finalGroups->where('status', 'PDC1_ACTIVE')->pluck('id')->all(),
                    'activate_pdc1' => $activatePdc1,
                ],
            ]);

            if ($activatePdc1) {
                foreach ($finalGroups as $group) {
                    if ($group->status === 'PDC1_ACTIVE') {
                        $alreadyActive[] = $group->id;

                        continue;
                    }

                    $this->stateMachine->transition($group->fresh(), 'PDC1_ACTIVE');

                    AuditLog::create([
                        'user_id' => $adminId,
                        'action' => 'GROUP_ACTIVATED_PDC1',
                        'target_type' => 'Group',
                        'target_id' => $group->id,
                        'payload' => ['old_status' => 'KELOMPOK_FINAL', 'new_status' => 'PDC1_ACTIVE', 'period_id' => $locked->id],
                    ]);

                    $activated[] = $group->id;
                }
            } else {
                $alreadyActive = $finalGroups->where('status', 'PDC1_ACTIVE')->pluck('id')->all();
            }
        });

        return [
            'period' => $period->fresh(),
            'finalized_groups' => $finalGroups->pluck('id')->all(),
            'activated_pdc1' => $activated,
            'already_active' => $alreadyActive,
        ];
    }

    /**
     * Roll finalized groups back to READY_FOR_FINALIZATION.
     *
     * @param  array<int>  $groupIds  Empty means all KELOMPOK_FINAL groups.
     * @return array{rolled_back_count: int}
     */
    public function rollbackFinalization(int $periodId, array $groupIds, string $reason, int $adminId): array
    {
        $period = Period::findOrFail($periodId);

        $query = Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL');
        if (! empty($groupIds)) {
            $query->whereIn('id', $groupIds);
        }
        $groups = $query->get();

        if ($groups->isEmpty()) {
            throw new InvalidArgumentException('No finalized groups to roll back.');
        }

        $count = 0;
        DB::transaction(function () use ($groups, $period, $reason, $adminId, &$count) {
            foreach ($groups as $group) {
                $oldStatus = $group->status;
                $group->status = 'READY_FOR_FINALIZATION';
                $group->finalized_at = null;
                $group->finalized_by = null;
                $group->save();

                AuditLog::create([
                    'user_id' => $adminId,
                    'action' => 'FINALIZATION_ROLLBACK',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['old_status' => $oldStatus, 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $reason, 'period_id' => $period->id],
                ]);

                $count++;
            }

            if (Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL')->count() === 0) {
                $period->is_finalized = false;
                $period->save();
            }
        });

        return ['rolled_back_count' => $count];
    }

    /**
     * Cancel a single Kelompok Final back to READY_FOR_FINALIZATION.
     */
    public function cancelKelompokFinal(int $periodId, int $groupId, ?string $reason, int $adminId): Group
    {
        $group = Group::where('period_id', $periodId)->findOrFail($groupId);

        if ($group->status !== 'KELOMPOK_FINAL') {
            throw new InvalidArgumentException('Group is not in KELOMPOK_FINAL status.');
        }

        DB::transaction(function () use ($group, $reason, $adminId) {
            $group->status = 'READY_FOR_FINALIZATION';
            $group->finalized_at = null;
            $group->finalized_by = null;
            $group->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_CANCEL_KELOMPOK_FINAL',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['old_status' => 'KELOMPOK_FINAL', 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $reason],
            ]);

            $period = $group->period;
            if ($period && Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL')->count() === 0) {
                $period->is_finalized = false;
                $period->save();
            }
        });

        return $group;
    }

    /**
     * Assign a title to a group that is not yet ready/finalized.
     * Siap Finalisasi only sets supervisors — it must not (re)assign titles.
     */
    public function assignGroupTitle(int $groupId, int $titleId, int $adminId): Group
    {
        $group = Group::findOrFail($groupId);
        $title = Title::findOrFail($titleId);

        if (in_array($group->status, array_merge(FinalizationSupport::READY_STATUSES, FinalizationSupport::FINAL_STATUSES, FinalizationSupport::POST_FINALIZATION_STATUSES), true)) {
            throw new InvalidArgumentException('Cannot assign title: group is already ready for finalization or finalized.');
        }

        if ($group->period && $group->period->is_finalized) {
            throw new InvalidArgumentException('Cannot assign title: period is finalized.');
        }

        if ($title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            throw new InvalidArgumentException('Cannot assign: student title has not been approved by the supervisor.');
        }

        if ($title->title_source === 'STUDENT' && $title->period_id && (int) $title->period_id !== (int) $group->period_id) {
            throw new InvalidArgumentException('Title belongs to another period.');
        }

        $allocated = Group::where('title_id', $title->id)
            ->where('id', '!=', $group->id)
            ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
            ->count();

        if ($allocated >= (int) $title->quota) {
            throw new InvalidArgumentException('Title quota is full.');
        }

        DB::transaction(function () use ($group, $title, $adminId) {
            $group->assignTitleFromFinalization($title->id);
            $group->save();

            if ($group->status === 'FORMING' && $this->stateMachine->canTransition('FORMING', 'TITLE_APPROVED')) {
                $this->stateMachine->transition($group, 'TITLE_APPROVED');
            }

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_ASSIGN_TITLE',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['title_id' => $title->id],
            ]);
        });

        return $group;
    }

    /**
     * Promote a group to Ready for Finalization.
     */
    public function promoteGroupToReady(int $groupId, int $adminId): Group
    {
        $group = Group::with(['members', 'title'])->findOrFail($groupId);

        if ($group->period && $group->period->is_finalized) {
            throw new InvalidArgumentException('Cannot promote: period is finalized.');
        }

        if ($group->status === 'READY_FOR_FINALIZATION') {
            throw new InvalidArgumentException('Group is already ready for finalization.');
        }

        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = $group->members->count();

        if ($memberCount < $minSize || $memberCount > $maxSize) {
            throw new InvalidArgumentException("Cannot promote: group has {$memberCount} members, allowed range is {$minSize}–{$maxSize}.");
        }

        // A group can only be marked ready with a concrete title assigned.
        // An ACCEPT bid alone is not enough — assign the title first.
        if ($group->title_id === null) {
            throw new InvalidArgumentException('Cannot promote: group has no title. Assign a title first.');
        }

        $title = $group->title;
        if ($title && $title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            throw new InvalidArgumentException('Cannot promote: student title has not been approved by the supervisor.');
        }

        DB::transaction(function () use ($group, $adminId) {
            if ($group->status === 'FORMING') {
                $this->stateMachine->transition($group, 'TITLE_APPROVED');
                $group->refresh();
            }
            $this->stateMachine->transition($group, 'READY_FOR_FINALIZATION');

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_PROMOTE_TO_READY',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['new_status' => 'READY_FOR_FINALIZATION'],
            ]);
        });

        return $group;
    }

    /**
     * Reopen a finalized period: PDC1_ACTIVE groups revert to KELOMPOK_FINAL,
     * deeper post-finalization groups are left untouched and reported.
     *
     * @return array{period: Period, reverted_group_ids: array<int>, left_post_final: array<int, array{group_id: int, code: string, status: string}>}
     */
    public function reopenPeriod(int $periodId, int $adminId): array
    {
        $period = Period::findOrFail($periodId);

        $reverted = [];
        $leftPostFinal = [];

        DB::transaction(function () use ($period, $adminId, &$reverted, &$leftPostFinal) {
            $locked = Period::lockForUpdate()->findOrFail($period->id);
            $locked->is_finalized = false;
            $locked->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_REOPEN',
                'target_type' => 'Period',
                'target_id' => $locked->id,
                'payload' => [],
            ]);

            // Return groups that never progressed past PDC1 activation to
            // KELOMPOK_FINAL so the period can be finalized again. Groups
            // deeper into post-finalization (sempro, PDC2, expo, ...) are
            // left untouched and reported instead.
            $groups = Group::where('period_id', $locked->id)
                ->whereIn('status', array_merge(FinalizationSupport::FINAL_STATUSES, FinalizationSupport::POST_FINALIZATION_STATUSES))
                ->lockForUpdate()
                ->get();

            foreach ($groups as $group) {
                if ($group->status === 'PDC1_ACTIVE') {
                    $group->status = 'KELOMPOK_FINAL';
                    $group->save();
                    $reverted[] = $group->id;

                    AuditLog::create([
                        'user_id' => $adminId,
                        'action' => 'FINALIZATION_REOPEN_GROUP',
                        'target_type' => 'Group',
                        'target_id' => $group->id,
                        'payload' => ['old_status' => 'PDC1_ACTIVE', 'new_status' => 'KELOMPOK_FINAL', 'period_id' => $locked->id],
                    ]);
                } elseif ($group->status !== 'KELOMPOK_FINAL') {
                    $leftPostFinal[] = ['group_id' => $group->id, 'code' => $group->code, 'status' => $group->status];
                }
            }
        });

        return [
            'period' => $period->fresh(),
            'reverted_group_ids' => $reverted,
            'left_post_final' => $leftPostFinal,
        ];
    }

    /**
     * Force a TITLE_APPROVED group to Ready for Finalization.
     * Same gates as promote-to-ready: concrete title + member count in range.
     */
    public function forceGroupReady(int $groupId, ?string $reason, int $adminId): Group
    {
        $group = Group::with('period')->findOrFail($groupId);

        if ($group->period && $group->period->is_finalized) {
            throw new InvalidArgumentException('Cannot force ready: period is finalized.');
        }

        if ($group->status !== 'TITLE_APPROVED') {
            throw new InvalidArgumentException('Cannot force ready: only groups with Title Approved status can be forced to Ready for Finalization.');
        }

        $group->loadMissing(['members', 'title']);
        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = $group->members->count();

        if ($memberCount < $minSize || $memberCount > $maxSize) {
            throw new InvalidArgumentException("Cannot force ready: group has {$memberCount} members, allowed range is {$minSize}–{$maxSize}.");
        }

        if ($group->title_id === null) {
            throw new InvalidArgumentException('Cannot force ready: group has no title. Assign a title first.');
        }

        $title = $group->title;
        if ($title && $title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            throw new InvalidArgumentException('Cannot force ready: student title has not been approved by the supervisor.');
        }

        $oldStatus = $group->status;

        DB::transaction(function () use ($group, $oldStatus, $reason, $adminId) {
            $group->status = 'READY_FOR_FINALIZATION';
            $group->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_FORCE_READY',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['old_status' => $oldStatus, 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $reason],
            ]);
        });

        return $group;
    }

    /**
     * Create a manual group with the given students.
     *
     * @param  array<int>  $studentIds
     * @param  array{title?: string, description?: string|null, specializations?: array<mixed>, lecturer_id?: int}|null  $newTitle
     */
    public function createManualGroup(int $periodId, array $studentIds, string $option, ?int $titleId, ?array $newTitle, int $adminId): Group
    {
        $period = Period::findOrFail($periodId);

        if (! $period->is_active || $period->is_finalized) {
            throw new InvalidArgumentException('Cannot create group: period is not active or already finalized.');
        }

        $students = $this->support->resolveStudents($studentIds);

        if (count($students) !== count(array_unique($studentIds))) {
            throw new InvalidArgumentException('One or more students were not found.');
        }

        $maxSize = (int) ($period->max_group_size ?? 4);
        if (count($students) > $maxSize) {
            throw new InvalidArgumentException("Too many members: maximum is {$maxSize}.");
        }

        foreach ($students as $student) {
            if ($this->support->hasMembershipInPeriod((int) $student->id, $period->id)) {
                throw new InvalidArgumentException("Student {$student->name} is already grouped in this period.");
            }
        }

        $group = DB::transaction(function () use ($students, $period, $option, $titleId, $newTitle, $adminId) {
            $group = Group::create([
                'period_id' => $period->id,
                'status' => count($students) === 1 ? 'FORMING_SOLO' : 'FORMING',
                'group_mode' => 'GROUP',
                'has_existing_group' => false,
            ]);

            foreach (array_values($students) as $index => $student) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'student_id' => $student->id,
                    'is_leader' => $index === 0,
                    'period_id' => $period->id,
                ]);
            }

            if ($option === 'assign_title') {
                $title = Title::findOrFail($titleId);
                $this->support->assertTitleAssignable($title, $group->id);
                $group->assignTitleFromFinalization($title->id);
                $group->save();
                if ($this->stateMachine->canTransition($group->status, 'TITLE_APPROVED')) {
                    $this->stateMachine->transition($group, 'TITLE_APPROVED');
                }
            }

            if ($option === 'add_title') {
                $title = Title::create([
                    'lecturer_id' => $newTitle['lecturer_id'],
                    'title' => $newTitle['title'],
                    'description' => $newTitle['description'] ?? null,
                    'specializations' => $newTitle['specializations'] ?? [],
                    'quota' => 1,
                    'title_source' => 'LECTURER',
                    'approved_by_admin' => true,
                    'period_id' => $period->id,
                ]);
                $group->assignTitleFromFinalization($title->id);
                $group->save();
                if ($this->stateMachine->canTransition($group->status, 'TITLE_APPROVED')) {
                    $this->stateMachine->transition($group, 'TITLE_APPROVED');
                }
            }

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_CREATE_MANUAL_GROUP',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['period_id' => $period->id, 'option' => $option, 'member_count' => count($students)],
            ]);

            return $group;
        });

        // Sync state with the student flow: a group meeting the min size
        // should read READY_FOR_BIDDING, not stay stuck at FORMING.
        if (in_array($group->status, ['FORMING', 'FORMING_SOLO'], true)) {
            try {
                $this->groupService->evaluateGroupReadiness($group->fresh());
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $group;
    }

    /**
     * Add students to an existing group.
     *
     * @param  array<int>  $studentIds
     */
    public function addStudentsToGroup(int $groupId, array $studentIds, int $adminId): Group
    {
        $group = Group::with('period')->findOrFail($groupId);
        $period = $group->period;

        if (! $period || ! $period->is_active || $period->is_finalized) {
            throw new InvalidArgumentException('Cannot add members: period is not active or already finalized.');
        }

        if (in_array($group->status, array_merge(FinalizationSupport::FINAL_STATUSES, FinalizationSupport::POST_FINALIZATION_STATUSES), true)) {
            throw new InvalidArgumentException('Cannot add members to a finalized group.');
        }

        $students = $this->support->resolveStudents($studentIds);

        if (count($students) !== count(array_unique($studentIds))) {
            throw new InvalidArgumentException('One or more students were not found.');
        }

        $maxSize = (int) ($period->max_group_size ?? 4);
        $currentCount = GroupMember::where('group_id', $group->id)->count();

        if ($currentCount + count($students) > $maxSize) {
            throw new InvalidArgumentException("Group would exceed maximum size of {$maxSize}.");
        }

        foreach ($students as $student) {
            if ($this->support->hasMembershipInPeriod((int) $student->id, $period->id)) {
                throw new InvalidArgumentException("Student {$student->name} is already grouped in this period.");
            }
        }

        DB::transaction(function () use ($students, $group, $period, $adminId) {
            foreach ($students as $student) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'student_id' => $student->id,
                    'is_leader' => false,
                    'period_id' => $period->id,
                ]);
            }

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_ADD_TO_EXISTING_GROUP',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['added_count' => count($students)],
            ]);
        });

        $group->refresh();
        // A top-up can push the group over the min size: promote
        // FORMING -> READY_FOR_BIDDING like the student flow does.
        if (in_array($group->status, ['FORMING', 'FORMING_SOLO'], true)) {
            try {
                $this->groupService->evaluateGroupReadiness($group);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $group;
    }

    /**
     * Set a group's supervisors, optionally marking it Kelompok Final
     * (READY_FOR_FINALIZATION → KELOMPOK_FINAL, requires title + member
     * count in range + SV1 + SV2).
     */
    public function setGroupSupervisors(int $groupId, int $supervisor1Id, ?int $supervisor2Id, int $adminId, ?string $notes, bool $markFinal): Group
    {
        $group = Group::findOrFail($groupId);

        $group = $this->support->applySupervisors($group, $supervisor1Id, $supervisor2Id, $adminId, $notes);

        if ($markFinal) {
            $group->loadMissing(['period', 'title', 'members']);
            $reason = $this->support->checkReadyForExecute($group->fresh());
            if ($reason !== null) {
                throw new InvalidArgumentException('Cannot mark Kelompok Final: '.$reason);
            }
            $oldStatus = $group->status;
            $this->stateMachine->transition($group, 'KELOMPOK_FINAL');
            $group->finalized_at = now();
            $group->finalized_by = $adminId;
            $group->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_MARK_KELOMPOK_FINAL',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['old_status' => $oldStatus, 'new_status' => 'KELOMPOK_FINAL'],
            ]);
        }

        return $group;
    }

    /**
     * Assign one supervisor pair to many groups, collecting per-group results.
     *
     * @param  array<int>  $groupIds
     * @return array{success: array<int, array{group_id: int, name: string}>, failed: array<int, array{group_id: int, reason: string}>}
     */
    public function batchSetGroupSupervisors(array $groupIds, int $supervisor1Id, ?int $supervisor2Id, int $adminId, ?string $notes): array
    {
        $success = [];
        $failed = [];

        foreach ($groupIds as $groupId) {
            try {
                $group = Group::findOrFail($groupId);
                $this->support->applySupervisors($group, $supervisor1Id, $supervisor2Id, $adminId, $notes);
                $success[] = ['group_id' => $group->id, 'name' => $group->code ?? "Kelompok #{$group->id}"];
            } catch (\Exception $e) {
                $failed[] = ['group_id' => (int) $groupId, 'reason' => $e->getMessage()];
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }
}
