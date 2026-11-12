<?php

namespace Modules\Capstone\Finalization;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Groups\GroupPresenter;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\SupervisorLoadService;

class FinalizationSupport
{
    public const READY_STATUSES = ['READY_FOR_FINALIZATION'];

    public const FINAL_STATUSES = ['KELOMPOK_FINAL'];

    public const POST_FINALIZATION_STATUSES = ['PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE', 'PDC2_ACTIVE', 'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED', 'CLOSED', 'READY_FOR_TA_INDIVIDUAL', 'TA_IN_PROGRESS', 'DISSOLVED'];

    /**
     * Pre-finalization group statuses. Canonical order lives in
     * GroupStateMachine::ALL_STATUSES; `TITLE_PROPOSED` is intentionally
     * absent (no transition produces it, nothing writes it).
     */
    public const NOT_READY_STATUSES = ['FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL', 'READY_FOR_BIDDING', 'TITLE_APPROVED'];

    public function __construct(
        protected GroupPresenter $presenter,
        protected SupervisorLoadService $supervisorLoadService
    ) {}

    public function buildStats(Period $period): array
    {
        $base = Group::where('period_id', $period->id);

        $ready = (clone $base)->whereIn('status', FinalizationSupport::READY_STATUSES)->count();
        $final = (clone $base)->whereIn('status', FinalizationSupport::FINAL_STATUSES)->count();
        $pdc1 = (clone $base)->where('status', 'PDC1_ACTIVE')->count();
        $noTitle = (clone $base)->whereNull('title_id')->count();
        $notReady = (clone $base)->whereIn('status', FinalizationSupport::NOT_READY_STATUSES)->count();
        $postBreakdown = (clone $base)->whereIn('status', FinalizationSupport::POST_FINALIZATION_STATUSES)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $noGroup = $this->ungroupedStudentsQuery($period)->count();

        $canModify = (bool) $period->is_active && ! (bool) $period->is_finalized;

        return [
            'total_ready' => $ready,
            'total_kelompok_final' => $final,
            'total_pdc1_active' => $pdc1,
            'total_no_group' => $noGroup,
            'total_no_title' => $noTitle,
            'total_not_ready' => $notReady,
            'can_finalize' => $canModify && $ready > 0,
            'can_reopen_finalization' => (bool) $period->is_finalized,
            'total_post_finalization' => array_sum($postBreakdown),
            'post_finalization_breakdown' => $postBreakdown,
        ];
    }

    public function buildFlow(Period $period, string $tab, string $subTab, array $stats): array
    {
        $blockers = [];
        $canModify = (bool) $period->is_active && ! (bool) $period->is_finalized;
        $reason = null;

        if (! $period->is_active) {
            $reason = 'Periode tidak aktif.';
            $blockers[] = ['type' => 'PERIOD_INACTIVE', 'message' => 'Periode tidak aktif.', 'severity' => 'error'];
        }

        if ((bool) $period->is_finalized) {
            $reason = 'Periode sudah difinalisasi.';
            $blockers[] = ['type' => 'PERIOD_FINALIZED', 'message' => 'Periode sudah difinalisasi. Batalkan finalisasi untuk mengubah data.', 'severity' => 'error', 'action' => 'reopen'];
        }

        if ($canModify && $stats['total_ready'] === 0) {
            $leftoverGroups = ($stats['total_no_title'] ?? 0) + ($stats['total_not_ready'] ?? 0);
            $finalPool = ($stats['total_kelompok_final'] ?? 0) + ($stats['total_pdc1_active'] ?? 0);
            if ($finalPool > 0 && $leftoverGroups === 0) {
                $blockers[] = ['type' => 'ALL_GROUPS_FINAL', 'message' => 'Semua kelompok sudah final atau pasca-final. Klik Finalisasi Periode untuk mengunci periode.', 'severity' => 'info', 'action' => 'period_flag'];
            } else {
                $blockers[] = ['type' => 'NO_READY_GROUPS', 'message' => 'Belum ada kelompok yang siap difinalisasi.', 'severity' => 'warning'];
            }
        }

        if (Group::where('period_id', $period->id)->count() === 0) {
            $blockers[] = ['type' => 'NO_GROUPS_IN_PERIOD', 'message' => 'Periode ini belum memiliki grup. Pastikan periode yang dipilih benar.', 'severity' => 'warning'];
        }

        $prerequisites = [
            [
                'type' => 'GROUP_SIZE',
                'label' => 'Ukuran kelompok',
                'configured' => $period->min_group_size !== null && $period->max_group_size !== null,
                'severity' => ($period->min_group_size !== null && $period->max_group_size !== null) ? 'success' : 'error',
                'message' => "Min {$period->min_group_size}, maks {$period->max_group_size}",
                'configure_url' => '',
                'edit_url' => '',
            ],
            [
                'type' => 'SUPERVISOR_LOAD',
                'label' => 'Beban pembimbing',
                'configured' => $period->supervisorLoadLimit(8) > 0,
                'severity' => 'success',
                'message' => 'Maks '.$period->supervisorLoadLimit(8).' kelompok per dosen',
                'configure_url' => '',
                'edit_url' => '',
            ],
        ];

        return [
            'can_modify' => $canModify,
            'can_execute_finalization' => $canModify && $stats['total_ready'] > 0,
            'reason' => $reason,
            'tab' => $tab,
            'sub_tab' => $subTab,
            'blockers' => $blockers,
            'prerequisites' => $prerequisites,
        ];
    }

    public function groupPayload(Group $group): array
    {
        $group->loadMissing(['title.lecturer', 'members.student.user', 'period', 'supervisor1.user', 'supervisor2.user', 'supervisions.supervisor.user']);

        $payload = $this->presenter->transformGroupForAdminList($group);
        $payload['allowed_actions'] = $this->resolveFinalizationActions($group);
        $payload['suggested_supervisor_1_id'] = null;
        $payload['suggested_supervisor_1_name'] = null;

        // SV1 default: existing SV1 → title owner (for student titles the
        // lecturer_id is the proposed supervisor) → accepted bid proposal.
        if ($group->supervisor_1_id) {
            $payload['suggested_supervisor_1_id'] = (int) $group->supervisor_1_id;
            $payload['suggested_supervisor_1_name'] = $group->supervisor1?->name;
        } elseif ($group->title?->lecturer_id) {
            $payload['suggested_supervisor_1_id'] = (int) $group->title->lecturer_id;
            $payload['suggested_supervisor_1_name'] = $group->title->lecturer?->name;
        } else {
            $acceptedBid = Bid::where('group_id', $group->id)
                ->where('lecturer_recommendation', 'ACCEPT')
                ->first();
            if ($acceptedBid?->proposed_supervisor_1_id) {
                $payload['suggested_supervisor_1_id'] = (int) $acceptedBid->proposed_supervisor_1_id;
                $payload['suggested_supervisor_1_name'] = Lecturer::find($payload['suggested_supervisor_1_id'])?->name;
            }
        }

        return $payload;
    }

    public function resolveFinalizationActions(Group $group): array
    {
        $finalized = (bool) $group->period?->is_finalized;

        if ($finalized) {
            return [
                'can_set_supervisor' => false,
                'can_mark_kelompok_final' => false,
                'can_cancel_kelompok_final' => false,
                'can_assign_title' => false,
                'can_promote_to_ready_for_finalization' => false,
                'reason' => 'PERIOD_FINALIZED',
            ];
        }

        return match ($group->status) {
            'READY_FOR_FINALIZATION' => [
                'can_set_supervisor' => true,
                'can_mark_kelompok_final' => true,
                'can_cancel_kelompok_final' => false,
                'can_assign_title' => false,
                'can_promote_to_ready_for_finalization' => false,
                'reason' => null,
            ],
            'KELOMPOK_FINAL' => [
                'can_set_supervisor' => false,
                'can_mark_kelompok_final' => false,
                'can_cancel_kelompok_final' => true,
                'can_assign_title' => false,
                'can_promote_to_ready_for_finalization' => false,
                'reason' => null,
            ],
            'TITLE_APPROVED', 'READY_FOR_BIDDING' => [
                'can_set_supervisor' => false,
                'can_mark_kelompok_final' => false,
                'can_cancel_kelompok_final' => false,
                'can_assign_title' => true,
                'can_promote_to_ready_for_finalization' => true,
                'reason' => null,
            ],
            default => [
                'can_set_supervisor' => false,
                'can_mark_kelompok_final' => false,
                'can_cancel_kelompok_final' => false,
                'can_assign_title' => in_array($group->status, ['FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL'], true),
                'can_promote_to_ready_for_finalization' => false,
                'reason' => in_array($group->status, array_merge(FinalizationSupport::FINAL_STATUSES, FinalizationSupport::POST_FINALIZATION_STATUSES), true) ? 'POST_FINALIZATION' : null,
            ],
        };
    }

    public function baseGroupQuery(Period $period, Request $request)
    {
        $query = Group::with(['title.lecturer', 'members.student.user', 'period', 'supervisor1.user', 'supervisor2.user', 'supervisions.supervisor.user'])
            ->where('period_id', $period->id)
            ->withCount('members');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('title', fn ($t) => $t->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('members.student', function ($s) use ($search) {
                        $s->where('student_number', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                    });
            });
        }

        $supervisorStatus = $request->input('supervisor_status', 'all');
        if ($supervisorStatus === 'missing_sv1') {
            $query->whereNull('supervisor_1_id');
        } elseif ($supervisorStatus === 'missing_sv2') {
            $query->whereNull('supervisor_2_id');
        } elseif ($supervisorStatus === 'complete') {
            $query->whereNotNull('supervisor_1_id')->whereNotNull('supervisor_2_id');
        }

        $minSize = (int) ($period->min_group_size ?? 3);
        $maxSize = (int) ($period->max_group_size ?? 4);
        $memberCount = $request->input('member_count', 'all');
        $countSql = '(SELECT COUNT(*) FROM capstone_group_members WHERE capstone_group_members.group_id = capstone_groups.id AND capstone_group_members.deleted_at IS NULL)';
        if ($memberCount === 'under_min') {
            $query->whereRaw("{$countSql} < ?", [$minSize]);
        } elseif ($memberCount === 'over_max') {
            $query->whereRaw("{$countSql} > ?", [$maxSize]);
        } elseif ($memberCount === 'in_range') {
            $query->whereRaw("{$countSql} BETWEEN ? AND ?", [$minSize, $maxSize]);
        }

        return $query;
    }

    public function paginateGroups(Period $period, Request $request, string $tab, string $subTab)
    {
        $query = $this->baseGroupQuery($period, $request);

        if ($tab === 'ready') {
            $query->whereIn('status', FinalizationSupport::READY_STATUSES);
        } elseif ($tab === 'final') {
            $query->whereIn('status', FinalizationSupport::FINAL_STATUSES);
        } elseif ($tab === 'post') {
            $query->whereIn('status', FinalizationSupport::POST_FINALIZATION_STATUSES);
        } elseif ($subTab === 'no_title') {
            $query->whereNull('title_id');
        } elseif ($subTab === 'not_ready') {
            $query->whereIn('status', FinalizationSupport::NOT_READY_STATUSES);
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $paginator = $query->latest()->paginate($perPage);
        $paginator->getCollection()->transform(fn (Group $group) => $this->groupPayload($group));

        return $paginator;
    }

    public function ungroupedStudentsQuery(Period $period)
    {
        // GroupMember.student_id references students.id (FK + belongsTo relation).
        $groupedStudentIds = GroupMember::where('period_id', $period->id)->pluck('student_id');

        // Only approved registrations count as registered. Statuses are
        // normalized to uppercase (PENDING / APPROVED / REJECTED / FLAGGED).
        $registeredIds = PeriodRegistration::where('period_id', $period->id)
            ->where('status', PeriodRegistration::STATUS_APPROVED)
            ->pluck('user_id');

        // No fallback to "all students": without registrations the honest
        // answer is zero, not the whole student table (and not students
        // grouped in other periods).
        return Student::with('user')
            ->whereNotIn('id', $groupedStudentIds)
            ->whereIn('id', $registeredIds->isNotEmpty() ? $registeredIds : [-1]);
    }

    public function paginateUngroupedStudents(Period $period, Request $request)
    {
        $query = $this->ungroupedStudentsQuery($period);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);

        return $query->orderBy('id')->paginate($perPage);
    }

    /**
     * Validate a ready group can be executed. Returns reason string or null when valid.
     */
    public function checkReadyForExecute(Group $group): ?string
    {
        $group->loadMissing(['period', 'title', 'members']);

        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = $group->members->count();

        if ($memberCount < $minSize) {
            return "Hanya {$memberCount} anggota, minimal {$minSize}.";
        }

        if ($memberCount > $maxSize) {
            return "{$memberCount} anggota melebihi maksimal {$maxSize}.";
        }

        if ($group->title_id === null) {
            return 'Belum memiliki judul.';
        }

        if ($group->supervisor_1_id === null) {
            return 'Belum memiliki pembimbing 1.';
        }

        if ($group->supervisor_2_id === null) {
            return 'Belum memiliki pembimbing 2.';
        }

        return null;
    }

    /**
     * Replace a group's supervisors (supervisions table is source of truth,
     * supervisor_1_id/2_id are cache fields).
     */
    public function applySupervisors(Group $group, int $supervisor1Id, ?int $supervisor2Id, int $adminId, ?string $notes): Group
    {
        if ($group->period && $group->period->is_finalized) {
            throw new \InvalidArgumentException('Cannot set supervisor: period is finalized.');
        }

        if (in_array($group->status, array_merge(FinalizationSupport::FINAL_STATUSES, FinalizationSupport::POST_FINALIZATION_STATUSES), true)) {
            throw new \InvalidArgumentException('Cannot set supervisor: group is already finalized.');
        }

        // Supervisors are only set on groups that are ready for finalization.
        if ($group->status !== 'READY_FOR_FINALIZATION') {
            throw new \InvalidArgumentException('Cannot set supervisor: group is not ready for finalization.');
        }

        return DB::transaction(function () use ($group, $supervisor1Id, $supervisor2Id, $adminId, $notes) {
            $group->loadMissing('period');

            if ((int) $group->supervisor_1_id !== $supervisor1Id) {
                $check = $this->supervisorLoadService->validateAssignment($supervisor1Id, $group->period_id);
                if (! $check['valid']) {
                    throw new \InvalidArgumentException($check['message']);
                }
            }

            if ($supervisor2Id !== null && (int) $group->supervisor_2_id !== $supervisor2Id) {
                $check = $this->supervisorLoadService->validateAssignment($supervisor2Id, $group->period_id);
                if (! $check['valid']) {
                    throw new \InvalidArgumentException($check['message']);
                }
            }

            Supervision::where('group_id', $group->id)->delete();

            Supervision::create([
                'group_id' => $group->id,
                'supervisor_id' => $supervisor1Id,
                'role' => 'SUPERVISOR_1',
                'assigned_by' => $adminId,
            ]);
            $group->supervisor_1_id = $supervisor1Id;

            if ($supervisor2Id !== null) {
                Supervision::create([
                    'group_id' => $group->id,
                    'supervisor_id' => $supervisor2Id,
                    'role' => 'SUPERVISOR_2',
                    'assigned_by' => $adminId,
                ]);
                $group->supervisor_2_id = $supervisor2Id;
            } else {
                $group->supervisor_2_id = null;
            }

            $group->save();

            AuditLog::create([
                'user_id' => $adminId,
                'action' => 'FINALIZATION_SET_SUPERVISOR',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['supervisor_1_id' => $supervisor1Id, 'supervisor_2_id' => $supervisor2Id, 'notes' => $notes],
            ]);

            return $group;
        });
    }

    /**
     * Resolve student IDs (accepts students.id or users.id) to Student models.
     */
    public function resolveStudents(array $ids): array
    {
        $students = [];
        foreach ($ids as $id) {
            $student = Student::find($id) ?? Student::where('user_id', $id)->first();
            if ($student) {
                $students[] = $student;
            }
        }

        return $students;
    }

    public function hasMembershipInPeriod(int $studentId, int $periodId): bool
    {
        return GroupMember::where('student_id', $studentId)
            ->where('period_id', $periodId)
            ->exists();
    }

    public function assertTitleAssignable(Title $title, int $excludingGroupId): void
    {
        if ($title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            throw new \InvalidArgumentException('Cannot assign: student title has not been approved by the supervisor.');
        }

        $allocated = Group::where('title_id', $title->id)
            ->where('id', '!=', $excludingGroupId)
            ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
            ->count();

        if ($allocated >= (int) $title->quota) {
            throw new \InvalidArgumentException('Title quota is full.');
        }
    }
}
