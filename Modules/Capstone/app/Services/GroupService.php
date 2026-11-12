<?php

namespace Modules\Capstone\Services;

use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Capstone\Concerns\RequiresActivePeriod;
use Modules\Capstone\Exceptions\ConflictRuleException;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Exceptions\GroupActionException;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupInvitation;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\JoinRequest;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Models\TitleApprovalAudit;
use Modules\Capstone\Support\CapstoneActor;
use Throwable;

class GroupService
{
    use RequiresActivePeriod;

    // Status Constants
    public const STATUS_FORMING = 'FORMING';

    public const STATUS_FORMING_SOLO = 'FORMING_SOLO';

    public const STATUS_WAITING_SUPERVISOR_APPROVAL = 'WAITING_SUPERVISOR_APPROVAL';

    public const STATUS_READY_FOR_TITLE_BIDDING = 'READY_FOR_BIDDING';

    public const STATUS_KELOMPOK_FINAL = 'KELOMPOK_FINAL';

    public const STATUS_DISSOLVED = 'DISSOLVED';

    protected GroupStateMachine $stateMachine;

    protected NotificationService $notificationService;

    public function __construct(GroupStateMachine $stateMachine, NotificationService $notificationService)
    {
        $this->stateMachine = $stateMachine;
        $this->notificationService = $notificationService;
    }

    /**
     * Enterprise Entry Point: Handle a student joining a group.
     * Guaranteed atomic and concurrency-safe.
     */
    public function handleJoinGroup(Student $student, Group $targetGroup): void
    {
        $startTime = microtime(true);

        // 1. Unified Logging Context (Tracing)
        Log::shareContext([
            'request_id' => Str::uuid(),
            'student_id' => $student->id,
            'group_id' => $targetGroup->id,
        ]);

        Log::info('group.join.attempt');

        // SAFETY CHECK: Locked groups cannot accept new members
        if ($this->stateMachine->isAtLeast($targetGroup, 'READY_FOR_FINALIZATION')) {
            throw new DomainRuleException('Kelompok sudah terkunci (Ready for Finalization) dan tidak menerima anggota baru.');
        }

        $this->ensurePeriodIsActive($targetGroup);

        // 2. Pre-validation & Idempotency (Fast Path)
        $this->validateJoinRequest($student, $targetGroup);

        $isAlreadyMember = GroupMember::where('student_id', $student->id)
            ->where('group_id', $targetGroup->id)
            ->exists();

        if ($isAlreadyMember) {
            throw new ConflictRuleException('Anda sudah terdaftar di kelompok ini. Hubungi ketua kelompok jika ada masalah.');
        }

        // 3. Selective Retry with Exponential Backoff
        try {
            retry(3, function () use ($student, $targetGroup) {
                DB::transaction(function () use ($student, $targetGroup) {
                    // A. Global Lock Order (Student -> Group -> Pivot)
                    $lockedStudent = Student::where('id', $student->id)->lockForUpdate()->firstOrFail();
                    $lockedGroup = Group::where('id', $targetGroup->id)->lockForUpdate()->first();

                    // Re-check idempotency inside transaction (Hard Guarantee)
                    if (GroupMember::where('student_id', $lockedStudent->id)->where('group_id', $lockedGroup->id)->exists()) {
                        throw new ConflictRuleException('Anda sudah terdaftar di kelompok ini. Hubungi ketua kelompok jika ada masalah.');
                    }

                    // B. Validate Capacity (Source Team + Target Team)
                    $sourceMembership = GroupMember::where('student_id', $lockedStudent->id)
                        ->where('period_id', $lockedGroup->period_id)
                        ->first();

                    $sourceMemberCount = $sourceMembership ? GroupMember::where('group_id', $sourceMembership->group_id)->count() : 1;
                    $targetMemberCount = GroupMember::where('group_id', $lockedGroup->id)->count();

                    $maxMembers = $lockedGroup->period->max_group_size ?? 4;
                    if (($sourceMemberCount + $targetMemberCount) > $maxMembers) {
                        throw new DomainRuleException('Kelompok penuh. Total anggota akan melebihi batas maksimal ('.($maxMembers).' orang). Kurangi anggota yang dibawa atau cari kelompok lain.');
                    }

                    // C. Migrate & Attach
                    $this->migrateStudentAndTeam($lockedStudent, $lockedGroup);
                    $this->attachToGroup($lockedStudent, $lockedGroup);
                    $this->evaluateGroupReadiness($lockedGroup);
                });
            }, fn ($attempt) => 100 * $attempt, function ($e) {
                return $e instanceof QueryException;
            });

            $durationMs = (microtime(true) - $startTime) * 1000;
            DB::afterCommit(fn () => Log::info('group.join.success', ['duration_ms' => round($durationMs, 2)]));

        } catch (Throwable $e) {
            Log::error('group.join.failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate if a student can join a target group.
     */
    public function validateJoinRequest(Student $student, Group $group): void
    {
        if (! $student->user?->hasRole('mahasiswa')) {
            throw new DomainRuleException('Hanya mahasiswa yang dapat bergabung ke dalam kelompok. Login sebagai mahasiswa terlebih dahulu.');
        }

        if ($group->period->is_finalized) {
            throw new DomainRuleException('Periode pendaftaran ini sudah ditutup. Hubungi admin jika ada kebutuhan khusus.');
        }

        if (! $group->period->is_active) {
            throw new DomainRuleException('Periode tidak aktif. Operasi tidak diizinkan.');
        }

        if ($this->stateMachine->isAtLeast($group, self::STATUS_KELOMPOK_FINAL)) {
            throw new DomainRuleException('Kelompok sudah difinalisasi dan tidak menerima anggota baru. Hubungi admin jika ada kebutuhan khusus.');
        }

        // 4. Already In Group (Idempotency)
        $existingMembership = GroupMember::where('student_id', $student->id)
            ->where('period_id', $group->period_id)
            ->first();

        if ($existingMembership && $existingMembership->group_id === $group->id) {
            return; // Idempotent success
        }

        if ($existingMembership && ! in_array($existingMembership->group->status, [self::STATUS_FORMING, self::STATUS_FORMING_SOLO, self::STATUS_WAITING_SUPERVISOR_APPROVAL])) {
            throw new DomainRuleException('Anda sudah terdaftar di kelompok lain pada periode ini. Keluar dari kelompok lama terlebih dahulu.');
        }

        // If they have a "FORMING" group but it's not a solo group (more than 1 member), they can't leave easily by joining another
        if ($existingMembership && $existingMembership->group->status === self::STATUS_FORMING) {
            $memberCount = GroupMember::where('group_id', $existingMembership->group_id)->count();
            if ($memberCount > 1 && ! $existingMembership->is_leader) {
                throw new DomainRuleException('Hanya ketua kelompok yang dapat memindahkan seluruh anggota ke kelompok lain.');
            }
        }
    }

    /**
     * Migrate a student (and their entire team if they are the leader) to a new group.
     */
    private function migrateStudentAndTeam(Student $student, Group $targetGroup): void
    {
        $membership = GroupMember::where('student_id', $student->id)
            ->where('period_id', $targetGroup->period_id)
            ->first();

        if (! $membership) {
            return;
        }

        $sourceGroup = Group::with('members')->find($membership->group_id);
        if (! $sourceGroup) {
            return;
        }

        // Cleanup Data (Source Group)
        $this->cleanupSoloGroupData($sourceGroup, $student);

        $members = $sourceGroup->members;

        foreach ($members as $m) {
            if ($m->student_id === $student->id) {
                // Requester: will be attached in next step of handleJoinGroup
                $m->delete();
            } else {
                // Team members: Bulk move to target group
                $m->update([
                    'group_id' => $targetGroup->id,
                    'is_leader' => false, // Hard rule: Target idea owner remains leader
                ]);
            }
        }

        // Delete Source Group
        $sourceGroup->delete();

        Log::info('group.migration.merged', [
            'student_id' => $student->id,
            'source_group_id' => $sourceGroup->id,
            'target_group_id' => $targetGroup->id,
            'member_count' => $members->count(),
        ]);
    }

    private function cleanupSoloGroupData(Group $group, Student $student): void
    {
        // Cancel Associated Titles
        $titles = Title::where('proposed_by_group_id', $group->id)->get();
        foreach ($titles as $title) {
            /** @var Title $title */
            $this->cancelAssociatedTitle($title, $student);
        }

        // Remove Bids & Proposals
        $group->bids()->delete();
        $group->supervisorProposals()->delete();

        // Invalidate Join Requests
        JoinRequest::where('group_id', $group->id)->update(['status' => 'INVALIDATED']);
    }

    private function cancelAssociatedTitle(Title $title, Student $student): void
    {
        $oldStatus = $title->supervisor_approval_status;
        $title->update([
            'proposed_by_group_id' => null,
            'supervisor_approval_status' => 'CANCELED',
        ]);

        // Notify Lecturer (Isolate side-effect via afterCommit)
        if ($title->proposed_supervisor_id && $oldStatus === 'UNDER_REVIEW') {
            DB::afterCommit(function () use ($title, $student) {
                $lecturerUserId = Lecturer::find($title->proposed_supervisor_id)?->user_id;
                if (! $lecturerUserId) {
                    return;
                }

                $this->notificationService->send(
                    $lecturerUserId,
                    'PROPOSAL_CANCELED',
                    'Solo Proposal Canceled',
                    "Proposal dari {$student->name} dibatalkan karena yang bersangkutan bergabung ke grup lain.",
                    'titles',
                    $title->id
                );
            });
        }
    }

    /**
     * Link student to the target group.
     */
    private function attachToGroup(Student $student, Group $group): void
    {
        GroupMember::create([
            'group_id' => $group->id,
            'student_id' => $student->id,
            'is_leader' => false,
            'period_id' => $group->period_id,
        ]);

        // Cleanup other pending invites/requests for this student
        JoinRequest::where('requester_id', $student->id)
            ->where('status', 'PENDING')
            ->update(['status' => 'INVALIDATED']);

        GroupInvitation::where('student_id', $student->id)
            ->where('status', 'PENDING')
            ->whereHas('group', fn ($q) => $q->where('period_id', $group->period_id))
            ->update(['status' => 'REJECTED']);

        Log::info('group.attach.success', ['student_id' => $student->id, 'group_id' => $group->id]);
    }

    /**
     * Auto-transition a newly created FORMING group to READY_FOR_BIDDING
     * when the member count reaches the period minimum.
     *
     * Why a separate method from evaluateGroupReadiness(): this is the
     * new-group fast path (min fallback 2, INDIVIDUAL mode needs 1, no
     * quota/title checks). evaluateGroupReadiness() is the general
     * re-evaluation path (min fallback 3, quota + title guards, demotion).
     * Do not merge them; the fallbacks and guards differ on purpose.
     */
    public function transitionNewGroupToReadyIfEligible(Group $group, Period $period): void
    {
        if ($group->status !== 'FORMING') {
            return;
        }

        $memberCount = GroupMember::where('group_id', $group->id)->count();

        // INDIVIDUAL mode: 1 member is enough
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : ($period->min_group_size ?? 2);

        if ($memberCount >= $minSize) {
            $this->stateMachine->transition($group, 'READY_FOR_BIDDING');
        }
    }

    /**
     * Create a student group (or solo group). Student becomes leader.
     *
     * Moved verbatim from GroupController (request-scoped orchestration:
     * registration approval, locks, creation, readiness). Actor resolution
     * (CapstoneActor) and input validation stay in the controller; abort_*
     * guards bubble exactly as before — the controller rethrows HttpException.
     */
    public function createStudentGroupFromRequest(Student $student, int $periodId, bool $solo): Group
    {
        return DB::transaction(function () use ($student, $periodId, $solo) {
            Student::whereKey($student->id)->lockForUpdate()->firstOrFail();
            $period = Period::lockForUpdate()->findOrFail($periodId);
            abort_unless($period->isRegistrationOpen(), 403, 'Period is closed.');
            abort_unless(PeriodRegistration::where('user_id', $student->id)->where('period_id', $period->id)->where('status', PeriodRegistration::STATUS_APPROVED)->exists(), 403, 'Your join request for this period must be approved by admin first.');
            abort_if(GroupMember::where('student_id', $student->id)->whereHas('group', fn ($q) => $q->whereNotIn('status', ['CLOSED', 'DISSOLVED']))->exists(), 422, 'You already have an active group.');
            $group = Group::create(['period_id' => $period->id, 'status' => $solo ? 'FORMING_SOLO' : 'FORMING', 'group_mode' => 'GROUP', 'has_existing_group' => false, 'is_solo' => $solo]);
            GroupMember::create(['group_id' => $group->id, 'student_id' => $student->id, 'is_leader' => true, 'period_id' => $period->id]);
            if (! $solo) {
                $this->transitionNewGroupToReadyIfEligible($group, $period);
            }

            return $group->load('members.student', 'period');
        });
    }

    /**
     * Delete a group with full cascade (leader flow).
     *
     * Membership/leader/presenter gates stay in the controller (actor +
     * action-matrix authorization); the finalized guard and the cascade
     * live here. The finalized branch is unreachable behind the presenter
     * lock and stays as defense-in-depth.
     */
    public function deleteGroupCascade(Group $group): void
    {
        // Only allow deletion before KELOMPOK_FINAL
        if ($this->stateMachine->isAtLeast($group, 'KELOMPOK_FINAL')) {
            throw new GroupActionException('Cannot delete group after finalization.', 400);
        }

        DB::transaction(function () use ($group) {
            // Delete any student proposals tied to this group
            Title::where('proposed_by_group_id', $group->id)
                ->where('title_source', 'STUDENT')
                ->delete();

            // Delete bids
            $group->bids()->delete();

            // Delete supervisor proposals
            $group->supervisorProposals()->delete();

            // Delete all members
            GroupMember::where('group_id', $group->id)->delete();

            // Delete the group
            $group->delete();
        });
    }

    /**
     * Apply a ready-state transition with member notifications (endpoint orchestration).
     *
     * The presenter action-matrix gate stays in the controller (actor +
     * action-matrix authorization, abort_unless bubbles); target selection,
     * transition, and notifications live here. No new guards.
     */
    public function transitionReadyState(Group $group, bool $cancel): Group
    {
        return DB::transaction(function () use ($group, $cancel) {
            $target = 'READY_FOR_FINALIZATION';
            if ($cancel) {
                $target = Title::where('proposed_by_group_id', $group->id)->where('title_source', 'STUDENT')->where('supervisor_approval_status', 'APPROVED')->exists() ? 'TITLE_APPROVED' : 'READY_FOR_BIDDING';
            }
            $this->stateMachine->transition($group, $target);
            $group->load('members.student');
            foreach ($group->members as $member) {
                $this->notificationService->send($member->student->user_id, $cancel ? 'GROUP_FINALIZATION_CANCELLED' : 'GROUP_READY_FOR_FINALIZATION', $cancel ? 'Finalization Cancelled' : 'Ready for Finalization', $cancel ? 'Status siap finalisasi kelompok dibatalkan.' : 'Kelompok siap untuk finalisasi admin.', 'Group', $group->id);
            }

            return $group;
        });
    }

    /**
     * Invite a student to the leader's group (endpoint orchestration).
     *
     * Membership/leader/group-load/presenter gates stay in the controller;
     * the finalized/capacity/lookup guards and the invitation transaction
     * live here. The finalized and full-group branches are unreachable
     * behind the presenter lock and stay as defense-in-depth.
     */
    public function sendMemberInvitation(Group $group, User $leader, string $email): Group
    {
        $studentId = CapstoneActor::student($leader)->id;

        // Only allow adding members before KELOMPOK_FINAL
        if ($this->stateMachine->isAtLeast($group, 'KELOMPOK_FINAL')) {
            throw new GroupActionException('Cannot add members after finalization.', 400);
        }

        // Member limit from period config
        $maxMembers = $group->period->max_group_size ?? 4;
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        if ($memberCount >= $maxMembers) {
            throw new GroupActionException("Group is full. Maximum {$maxMembers} members allowed.", 400);
        }

        // Find the student to add
        $studentUser = User::with('student')
            ->where('email', $email)
            ->whereHas('roles', fn ($query) => $query->where('name', 'mahasiswa'))
            ->first();
        $student = $studentUser?->student;
        if (! $student) {
            throw new GroupActionException('Student not found.', 404);
        }

        if ($studentUser->id === $leader->id) {
            throw new GroupActionException('You are already in this group.', 400);
        }

        // V4: Check if student is already in a group for THIS period
        $inPeriodGroup = GroupMember::where('student_id', $student->id)
            ->where('period_id', $group->period_id)
            ->exists();
        if ($inPeriodGroup) {
            throw new GroupActionException('This student is already in a group for this period.', 400);
        }

        // Check if there's already a pending invitation
        $existingInvite = GroupInvitation::where('group_id', $group->id)
            ->where('student_id', $student->id)
            ->where('status', 'PENDING')
            ->exists();
        if ($existingInvite) {
            throw new GroupActionException('An invitation is already pending for this student.', 400);
        }

        return DB::transaction(function () use ($group, $studentId, $student, $studentUser, $leader) {
            $invitation = GroupInvitation::updateOrCreate(
                ['group_id' => $group->id, 'student_id' => $student->id],
                ['inviter_id' => $studentId, 'status' => 'PENDING']
            );

            // Send notification to student
            $this->notificationService->send(
                $studentUser->id,
                'GROUP_INVITATION',
                'Group Invitation',
                "{$leader->name} invited you to join their capstone group.",
                'group_invitations',
                $invitation->id
            );

            return $group->fresh()->load('members.student');
        });
    }

    /**
     * Strict State Transition: Evaluate if group is ready for bidding.
     */
    public function evaluateGroupReadiness(Group $group): void
    {
        $this->ensurePeriodIsActive($group);

        // IMPORTANT: Refresh to get latest status from DB (avoid stale data from eager loading)
        $group->refresh();

        // Guard: Skip if already finalized or later (covers DISSOLVED via
        // status order), or has approved title (solo seeker waiting for
        // leader to finalize).
        if ($this->stateMachine->isAtLeast($group, 'READY_FOR_FINALIZATION') || $group->status === 'TITLE_APPROVED') {
            return;
        }

        if ($this->canBecomeReady($group)) {
            $this->transitionToReady($group);
        } else {
            // Case: group was READY but member left -> demote back to FORMING or FORMING_SOLO based on is_solo flag
            $memberCount = GroupMember::where('group_id', $group->id)->count();
            $revertStatus = ($memberCount === 1 && $group->is_solo) ? self::STATUS_FORMING_SOLO : self::STATUS_FORMING;
            $group->update(['status' => $revertStatus]);
        }
    }

    private function canBecomeReady(Group $group): bool
    {
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : ($period->min_group_size ?? 3);

        if ($memberCount < $minSize) {
            return false;
        }

        // SECURITY FIX: For solo seeker groups, verify title is still APPROVED
        // Prevent transition to READY if title has been withdrawn
        if ($group->is_solo && $group->title_id) {
            $title = Title::find($group->title_id);
            if (! $title || $title->supervisor_approval_status !== 'APPROVED') {
                return false; // Title not approved, cannot become ready
            }
        }

        // Atomic Validation: Check Quota if title exists
        $preApprovedTitle = Title::where('proposed_by_group_id', $group->id)
            ->where('supervisor_approval_status', 'UNDER_REVIEW')
            ->first();

        if ($preApprovedTitle) {
            $lecturerId = $preApprovedTitle->proposed_supervisor_id;
            $maxLoad = $period->supervisorLoadLimit(8);

            $currentLoad = Supervision::where('supervisor_id', $lecturerId)
                ->whereHas('group', fn ($q) => $q->where('period_id', $period->id))
                ->count();

            $approvedProposals = Title::where('proposed_supervisor_id', $lecturerId)
                ->where('supervisor_approval_status', 'APPROVED')
                ->whereHas('proposedByGroup', fn ($q) => $q->where('period_id', $period->id))
                ->count();

            if (($currentLoad + $approvedProposals) >= $maxLoad) {
                return false; // Quota full, cannot become READY with this title
            }
        }

        return true;
    }

    private function transitionToReady(Group $group): void
    {
        // Guard: Skip if already at target status (avoid same-state transition error)
        if ($group->status === self::STATUS_READY_FOR_TITLE_BIDDING) {
            Log::info('group.readiness.skipped', ['group_id' => $group->id, 'reason' => 'already_ready']);

            return;
        }

        $preApprovedTitle = Title::where('proposed_by_group_id', $group->id)
            ->where('supervisor_approval_status', 'UNDER_REVIEW')
            ->first();

        if ($preApprovedTitle) {
            $preApprovedTitle->update(['supervisor_approval_status' => 'APPROVED']);
            // Assignment remains an admin finalization operation.
            $this->stateMachine->transition($group, 'TITLE_APPROVED');

            return;
        }

        $this->stateMachine->transition($group, self::STATUS_READY_FOR_TITLE_BIDDING);

        Log::info('group.readiness.transitioned', ['group_id' => $group->id, 'status' => self::STATUS_READY_FOR_TITLE_BIDDING]);
    }

    /**
     * Size limits for a group (single source of truth for min/max fallbacks).
     *
     * @return array{0:int,1:int} [minSize, maxSize]
     */
    public function groupSizeLimits(Group $group): array
    {
        if (! $group->relationLoaded('period')) {
            $group->load('period');
        }
        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);

        return [$minSize, $maxSize];
    }

    /**
     * Throw when a group is outside the period min/max size for finalization.
     */
    public function assertGroupSizeForFinalization(Group $group): void
    {
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        [$minSize, $maxSize] = $this->groupSizeLimits($group);
        if ($memberCount < $minSize || $memberCount > $maxSize) {
            throw new DomainRuleException("Jumlah anggota harus antara {$minSize}-{$maxSize} orang (saat ini: {$memberCount}). Tambah/kurangi anggota hingga memenuhi batas sebelum finalisasi.");
        }
    }

    /**
     * Handle a membership shrink (kick/leave) for bidding groups.
     *
     * Must be called inside the caller's DB transaction, after the member row
     * was deleted. It never deletes ACCEPT/REJECT lecturer decisions:
     * - count < min: hard-delete PENDING bids with no lecturer decision
     *   (deleted rows never block rebidding), compact remaining priorities,
     *   demote bidding statuses back to FORMING, and notify remaining members
     *   that an approved title is retained but mark-ready is blocked until refill.
     * - count > max: bids/titles untouched, notify remaining members to trim.
     * Locked statuses (READY_FOR_FINALIZATION and above, TITLE_APPROVED,
     * DISSOLVED) are never demoted here.
     *
     * @return array{member_count:int,min_size:int,max_size:int,undersized:bool,oversized:bool,cancelled_bids:int,has_accepted_bid:bool}
     */
    public function handleMembershipShrink(Group $group): array
    {
        $group->refresh();
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        [$minSize, $maxSize] = $this->groupSizeLimits($group);
        $undersized = $memberCount < $minSize;
        $oversized = $memberCount > $maxSize;
        $cancelled = 0;
        $hasAcceptedBid = Bid::where('group_id', $group->id)
            ->where('lecturer_recommendation', 'ACCEPT')
            ->exists();

        if ($undersized) {
            $pendingIds = Bid::where('group_id', $group->id)
                ->where('status', 'PENDING')
                ->whereNull('lecturer_recommendation')
                ->pluck('id');
            $cancelled = $pendingIds->count();
            if ($cancelled > 0) {
                Bid::whereIn('id', $pendingIds)->delete();
                $remaining = Bid::where('group_id', $group->id)->orderBy('priority')->get();
                foreach ($remaining as $index => $bid) {
                    if ((int) $bid->priority !== $index + 1) {
                        $bid->update(['priority' => $index + 1]);
                    }
                }
            }

            if (in_array($group->status, ['READY_FOR_BIDDING', 'WAITING_SUPERVISOR_APPROVAL'], true)) {
                $revertStatus = ($group->is_solo && $memberCount === 1)
                    ? self::STATUS_FORMING_SOLO
                    : self::STATUS_FORMING;
                $group->update(['status' => $revertStatus]);
            }

            $userIds = GroupMember::where('group_id', $group->id)
                ->with('student')
                ->get()
                ->map(fn ($member) => $member->student?->user_id)
                ->filter()
                ->values()
                ->all();
            if (! empty($userIds)) {
                $message = $hasAcceptedBid
                    ? "Anggota keluar/dikeluarkan sehingga tersisa {$memberCount}/{$minSize}. Judul yang disetujui dosen tetap dipertahankan, tetapi kelompok belum bisa mark-ready. Tambah anggota hingga minimum."
                    : "Anggota keluar/dikeluarkan sehingga tersisa {$memberCount}/{$minSize}. Bid PENDING yang dihapus otomatis: {$cancelled}. Tambah anggota hingga minimum untuk bidding.";
                $this->notificationService->sendToMany(
                    $userIds,
                    'GROUP_SIZE_BELOW_MINIMUM',
                    'Ukuran Kelompok Di Bawah Minimum',
                    $message,
                    'Group',
                    $group->id
                );
            }

            Log::info('group.membership.shrink_below_min', [
                'group_id' => $group->id,
                'member_count' => $memberCount,
                'min_size' => $minSize,
                'cancelled_bids' => $cancelled,
                'accepted_bid_retained' => $hasAcceptedBid,
            ]);
        } elseif ($oversized) {
            $userIds = GroupMember::where('group_id', $group->id)
                ->with('student')
                ->get()
                ->map(fn ($member) => $member->student?->user_id)
                ->filter()
                ->values()
                ->all();
            if (! empty($userIds)) {
                $this->notificationService->sendToMany(
                    $userIds,
                    'GROUP_SIZE_ABOVE_MAXIMUM',
                    'Ukuran Kelompok Melebihi Maksimum',
                    "Jumlah anggota saat ini {$memberCount} melebihi batas maksimum {$maxSize}. Judul/bid tetap dipertahankan, tetapi finalisasi diblokir. Kurangi anggota hingga maksimum.",
                    'Group',
                    $group->id
                );
            }

            Log::info('group.membership.above_max', [
                'group_id' => $group->id,
                'member_count' => $memberCount,
                'max_size' => $maxSize,
            ]);
        }

        return [
            'member_count' => $memberCount,
            'min_size' => $minSize,
            'max_size' => $maxSize,
            'undersized' => $undersized,
            'oversized' => $oversized,
            'cancelled_bids' => $cancelled,
            'has_accepted_bid' => $hasAcceptedBid,
        ];
    }

    /**
     * Destroy a group that has no remaining active members.
     *
     * Must be called inside the caller's DB transaction, after the last
     * member row was removed. Applies to groups of ANY status (including
     * finalized ones): an empty group is deleted, never left behind as an
     * orphan row. Most relations cascade at the DB level
     * (cascadeOnDelete); STUDENT titles and restrictOnDelete rows are
     * removed explicitly first. An AuditLog entry is written BEFORE the
     * delete so the trail survives the row removal.
     *
     * Active members = GroupMember rows with deleted_at IS NULL
     * (soft-deleted/flagged rows do not count).
     *
     * @param  Group|int  $groupOrId  Group instance or ID
     * @param  string  $reason  member_removed|member_left|member_flagged|merge|purge
     * @param  int|null  $triggeredBy  Acting user ID for the audit payload
     * @return bool True when the group was deleted
     */
    public function destroyIfEmpty(Group|int $groupOrId, string $reason, ?int $triggeredBy = null): bool
    {
        $groupId = $groupOrId instanceof Group ? $groupOrId->id : (int) $groupOrId;

        $group = Group::where('id', $groupId)->lockForUpdate()->first();
        if (! $group) {
            return false;
        }

        $remainingMembers = GroupMember::where('group_id', $group->id)->count();
        if ($remainingMembers > 0) {
            return false;
        }

        $statusBefore = $group->status;
        $periodId = $group->period_id;

        AuditLog::create([
            'user_id' => $triggeredBy,
            'action' => 'GROUP_AUTO_DELETED',
            'target_type' => Group::class,
            'target_id' => $group->id,
            'payload' => [
                'reason' => $reason,
                'status_before' => $statusBefore,
                'period_id' => $periodId,
            ],
        ]);

        // STUDENT proposals owned by the group are removed (matches the
        // manual deleteGroup semantics); other titles detach via nullOnDelete.
        Title::where('proposed_by_group_id', $group->id)
            ->where('title_source', 'STUDENT')
            ->delete();

        // restrictOnDelete table — must go before the group row.
        DB::table('capstone_expo_self_evaluations')->where('group_id', $group->id)->delete();

        // Everything else (members incl. soft-deleted rows, bids,
        // supervisions, documents, schedules, evaluations, invitations,
        // join requests, …) cascades at the DB level.
        $group->delete();

        Log::info('group.lifecycle.auto_deleted', [
            'group_id' => $groupId,
            'reason' => $reason,
            'status_before' => $statusBefore,
            'period_id' => $periodId,
        ]);

        return true;
    }

    /**
     * Mark a group as dissolved (audit trail preserved).
     *
     * @deprecated Empty groups are now hard-deleted via destroyIfEmpty().
     * Kept for backward compatibility; delegates to destroyIfEmpty().
     */
    private function archiveGroup(Group $group): void
    {
        if ($this->destroyIfEmpty($group, 'legacy_archive')) {
            return;
        }
        $group->update(['status' => self::STATUS_DISSOLVED]);
        Log::info('group.lifecycle.dissolved', ['group_id' => $group->id]);
    }

    // ======================================================================
    // Group CRUD
    // ======================================================================

    /**
     * Admin force-delete a group in ANY status.
     *
     * This is the only admin group-deletion path: the older
     * FORMING/inactive-only twin had zero callers (verified Stream 1) and
     * was removed. The mandatory reason is stored in the audit log payload
     * and shown to affected students.
     *
     * @return int Number of affected students
     */
    public function adminForceDeleteGroup(Group $group, User $admin, string $reason): int
    {
        $reason = trim($reason);
        if (mb_strlen($reason) < 10) {
            throw new DomainRuleException('Alasan penghapusan wajib diisi (minimal 10 karakter).');
        }

        $affectedStudents = $group->members()->pluck('student_id')->toArray();
        $statusBefore = $group->status;
        $periodId = $group->period_id;
        $period = $group->period;
        // Notifications target users.id; members carry student-profile ids.
        $notifyUserIds = $group->members()->with('student')->get()
            ->pluck('student.user_id')->filter()->unique()->values()->all();

        DB::transaction(function () use ($group, $admin, $reason, $statusBefore, $periodId, $period, $affectedStudents, $notifyUserIds) {
            Title::where('proposed_by_group_id', $group->id)
                ->where('title_source', 'STUDENT')
                ->delete();

            $group->bids()->delete();
            $group->supervisorProposals()->delete();
            $group->supervisions()->delete();
            $group->taSubmissions()->delete();
            $group->documents()->delete();
            $group->evaluations()->delete();
            $group->schedules()->delete();
            $group->seminarSchedules()->delete();
            $group->taDefenseSchedules()->delete();
            TitleApprovalAudit::where('affected_group_id', $group->id)->delete();
            $group->members()->delete();
            GroupInvitation::where('group_id', $group->id)->delete();
            JoinRequest::where('group_id', $group->id)->delete();

            // restrictOnDelete table — must go before the group row.
            DB::table('capstone_expo_self_evaluations')->where('group_id', $group->id)->delete();

            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'GROUP_DELETED_BY_ADMIN',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => [
                    'group_id' => $group->id,
                    'period_id' => $periodId,
                    'status_before' => $statusBefore,
                    'reason' => $reason,
                    'affected_students' => $affectedStudents,
                ],
            ]);

            $group->delete();

            foreach ($notifyUserIds as $userId) {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'GROUP_DELETED_BY_ADMIN',
                    'title' => 'Kelompok Dihapus oleh Admin',
                    'message' => 'Kelompok Anda telah dihapus oleh admin. Alasan: '.$reason,
                    'related_type' => 'Period',
                    'related_id' => $period?->id,
                ]);
            }
        });

        Log::info('group.lifecycle.admin_deleted', [
            'group_id' => $group->id,
            'admin_id' => $admin->id,
            'status_before' => $statusBefore,
            'period_id' => $periodId,
            'affected_students' => count($affectedStudents),
        ]);

        return count($affectedStudents);
    }

    /**
     * Accept a group invitation as the invited student (endpoint orchestration).
     *
     * Moved verbatim from GroupController. Kept separate from
     * validateInvitationAcceptance()/handleJoinGroup(): those serve the
     * join-request flow with different guards and merge semantics, while
     * this serves the invitation-accept flow (capacity check, cancel other
     * invites, mark notification read). Lookup failures report 404 envelopes.
     */
    public function acceptInvitationAsStudent(User $user, int $invitationId): void
    {
        $studentId = CapstoneActor::student($user)->id;
        $invitation = GroupInvitation::with('inviter.user')->where('id', $invitationId)
            ->where('student_id', $studentId)
            ->where('status', 'PENDING')
            ->first();

        if (! $invitation) {
            throw new GroupActionException('Invitation not found or already processed.', 404);
        }

        $group = Group::with('period')->find($invitation->group_id);

        if (! $group || $group->status === 'CLOSED') {
            throw new GroupActionException('Group is no longer available.', 400);
        }

        if ($this->stateMachine->isAtLeast($group, 'KELOMPOK_FINAL')) {
            throw new GroupActionException('Group is finalized, cannot join now.', 400);
        }

        $inPeriodGroup = GroupMember::where('student_id', $studentId)
            ->where('period_id', $group->period_id)
            ->exists();

        if ($inPeriodGroup) {
            throw new GroupActionException('You are already in a group for this period.', 400);
        }

        $maxMembers = $group->period->max_group_size ?? 4;
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        if ($memberCount >= $maxMembers) {
            throw new GroupActionException('Group is already full.', 400);
        }

        DB::transaction(function () use ($invitation, $group, $studentId, $user) {
            $invitation->update(['status' => 'ACCEPTED']);

            GroupMember::create([
                'group_id' => $group->id,
                'student_id' => $studentId,
                'is_leader' => DB::raw('false'),
                'period_id' => $group->period_id,
            ]);

            $this->transitionNewGroupToReadyIfEligible($group, $group->period);

            $this->notificationService->send(
                $invitation->inviter->user_id,
                'INVITE_ACCEPTED',
                'Invitation Accepted',
                "{$user->name} has joined your group.",
                'groups',
                $group->id
            );

            // Mark invitation notification as read
            Notification::where('user_id', $user->id)
                ->where('type', 'GROUP_INVITATION')
                ->where('related_id', $invitation->id)
                ->update(['is_read' => DB::raw('true')]);

            // Cancel any other pending invitations for this student in this period
            GroupInvitation::where('student_id', $studentId)
                ->where('status', 'PENDING')
                ->whereHas('group', function ($q) use ($group) {
                    $q->where('period_id', $group->period_id);
                })
                ->update(['status' => 'REJECTED']);
        });
    }

    /**
     * Reject a group invitation as the invited student (endpoint orchestration).
     *
     * Moved verbatim from GroupController. Kept separate from
     * rejectInvitation(): that twin notifies inviter_id (profile id) with a
     * boolean flag, while this flow notifies inviter->user_id (app id) with
     * a raw expression — different recipients and payloads.
     */
    public function rejectInvitationAsStudent(User $user, int $invitationId): void
    {
        $studentId = CapstoneActor::student($user)->id;
        $invitation = GroupInvitation::with('inviter.user')->where('id', $invitationId)
            ->where('student_id', $studentId)
            ->where('status', 'PENDING')
            ->first();

        if (! $invitation) {
            throw new GroupActionException('Invitation not found or already processed.', 404);
        }

        $invitation->update(['status' => 'REJECTED']);

        $this->notificationService->send(
            $invitation->inviter->user_id,
            'INVITE_REJECTED',
            'Invitation Rejected',
            "{$user->name} declined your group invitation.",
            'groups',
            $invitation->group_id
        );

        // Mark invitation notification as read
        Notification::where('user_id', $user->id)
            ->where('type', 'GROUP_INVITATION')
            ->where('related_id', $invitation->id)
            ->update(['is_read' => DB::raw('true')]);
    }

    /**
     * Remove a member from the group (leader flow cascade).
     *
     * Membership/leader/member-lookup/self/presenter gates stay in the
     * controller (actor + action-matrix authorization); the finalized
     * guard and the delete/shrink orchestration live here.
     *
     * @return array{deleted: bool, group: ?Group, shrink: ?array}
     */
    public function removeMemberCascade(Group $group, GroupMember $member, int $triggeredBy): array
    {
        // After KELOMPOK_FINAL: require admin approval (handled separately)
        if ($this->stateMachine->isAtLeast($group, 'KELOMPOK_FINAL')) {
            throw new GroupActionException('Cannot remove members after finalization. Request admin approval.', 400);
        }

        $result = DB::transaction(function () use ($group, $member, $triggeredBy) {
            $member->delete();

            // Empty group (no remaining active members) is hard-deleted,
            // never left behind as an orphan row.
            $groupId = $group->id;
            if (GroupMember::where('group_id', $groupId)->count() === 0) {
                $this->destroyIfEmpty($groupId, 'member_removed', $triggeredBy);

                return ['deleted' => true, 'group' => null, 'shrink' => null];
            }

            // Shrink policy: auto-cancel PENDING bids below min (ACCEPT kept,
            // title retained), demote bidding statuses, notify members.
            $shrink = $this->handleMembershipShrink($group);

            return ['deleted' => false, 'group' => Group::with('members.student')->find($group->id), 'shrink' => $shrink];
        });

        return $result;
    }

    /**
     * Leave a group (non-leader flow cascade).
     *
     * Membership/leader/presenter gates stay in the controller; the
     * finalized guard and the leave/shrink orchestration live here.
     *
     * @return array{deleted: bool, shrink: ?array}
     */
    public function leaveGroupCascade(Group $group, GroupMember $membership, int $triggeredBy): array
    {
        if ($this->stateMachine->isAtLeast($group, 'KELOMPOK_FINAL')) {
            throw new GroupActionException('Group is finalized, you cannot leave.', 400);
        }

        return DB::transaction(function () use ($group, $membership, $triggeredBy) {
            $groupId = $membership->group_id;
            $membership->delete();

            // Empty group (no remaining active members) is hard-deleted,
            // never left behind as an orphan row.
            if (GroupMember::where('group_id', $groupId)->count() === 0) {
                $this->destroyIfEmpty($groupId, 'member_left', $triggeredBy);

                return ['deleted' => true, 'shrink' => null];
            }

            $shrink = $this->handleMembershipShrink($group);

            return ['deleted' => false, 'shrink' => $shrink];
        });
    }

    // ======================================================================
    // Supervisor Management
    // ======================================================================

    /**
     * Assign Supervisor 2 looked up by lecturers.id (admin group endpoint).
     *
     * Why the only assignSupervisor2* method: the admin endpoint addresses
     * supervisors by Lecturer row id with a dosen-role check. The older
     * User-id twin had zero callers (verified Stream 1) and was removed.
     * Guards throw DomainRuleException with the same messages the endpoint
     * returns as 400 JSON; callers map the exception to the response envelope.
     */
    public function assignSupervisor2ByLecturerId(Group $group, int $lecturerId, int $assignedBy): Group
    {
        $supervisor = Lecturer::whereKey($lecturerId)
            ->whereHas('user.roles', fn ($query) => $query->where('name', 'dosen'))
            ->first();

        if (! $supervisor) {
            throw new DomainRuleException('Supervisor 2 must be a lecturer.');
        }

        // Check not same as Supervisor 1
        if ($group->supervisor_1_id && (int) $group->supervisor_1_id === (int) $supervisor->id) {
            throw new DomainRuleException('Supervisor 2 cannot be the same as Supervisor 1.');
        }

        DB::transaction(function () use ($group, $supervisor, $assignedBy) {
            Supervision::updateOrCreate(
                ['group_id' => $group->id, 'role' => 'SUPERVISOR_2'],
                [
                    'supervisor_id' => $supervisor->id,
                    'assigned_by' => $assignedBy,
                ]
            );

            $group->update(['supervisor_2_id' => $supervisor->id]);
        });

        return $group->fresh()->load(['supervisor1', 'supervisor2', 'supervisions.supervisor']);
    }

    // ======================================================================
    // Query Helpers
    // ======================================================================

    /**
     * Validate invitation acceptance and prepare membership context.
     */
    public function validateInvitationAcceptance(GroupInvitation $invitation, User $user): array
    {
        $group = Group::with('period')->find($invitation->group_id);

        $this->ensurePeriodIsActive($group);

        if (! $group || $group->status === 'CLOSED') {
            throw new DomainRuleException('Kelompok tidak tersedia lagi.');
        }

        if ($group->period && $group->period->is_finalized) {
            throw new DomainRuleException('Pendaftaran untuk periode ini sudah ditutup.');
        }

        if ($this->stateMachine->isAtLeast($group, 'READY_FOR_FINALIZATION')) {
            throw new DomainRuleException('Kelompok sudah terkunci (Ready for Finalization) dan tidak menerima anggota baru.');
        }

        if ($group->is_solo && $group->title_id) {
            $title = Title::find($group->title_id);
            if (! $title || $title->supervisor_approval_status !== 'APPROVED') {
                throw new DomainRuleException('Judul kelompok ini telah dibatalkan oleh dosen. Tidak dapat bergabung.');
            }
        }

        $inStandardGroup = GroupMember::where('student_id', $user->id)
            ->where('period_id', $group->period_id)
            ->whereHas('group', fn ($q) => $q->whereNotIn('status', ['FORMING', 'WAITING_SUPERVISOR_APPROVAL']))
            ->exists();

        if ($inStandardGroup) {
            throw new DomainRuleException('Anda sudah terdaftar di kelompok lain pada periode ini. Keluar dari kelompok lama terlebih dahulu.');
        }

        $maxMembers = $group->period->max_group_size ?? 4;
        $memberCount = GroupMember::where('group_id', $group->id)->count();
        if ($memberCount >= $maxMembers) {
            throw new DomainRuleException('Kelompok sudah penuh. Cari kelompok lain atau buat kelompok baru.');
        }

        $isApproved = PeriodRegistration::where('user_id', $user->id)
            ->where('period_id', $group->period_id)
            ->where('status', PeriodRegistration::STATUS_APPROVED)
            ->exists();

        if (! $isApproved) {
            throw new DomainRuleException('Permintaan gabung periode Anda belum disetujui admin. Tunggu persetujuan sebelum bergabung kelompok.');
        }

        return ['group' => $group, 'auto_registered' => false];
    }
}
