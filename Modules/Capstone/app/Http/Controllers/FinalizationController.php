<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Capstone\Exceptions\UnprocessableFinalizationException;
use Modules\Capstone\Finalization\FinalizationSupport;
use Modules\Capstone\Http\Requests\Admin\Finalization\AddToExistingGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\AllocateGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\AllocateStudentProposedRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\AssignTitleRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\AutoFixRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\BatchSetSupervisorRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\CancelKelompokFinalRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\CreateManualGroupRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\DashboardRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ExecuteFinalizationRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ExportRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\FinalizePeriodFlagRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\FinalizePeriodRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ForceReadyRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\PromoteToReadyRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\ReopenPeriodRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\RollbackFinalizationRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\SetSupervisorRequest;
use Modules\Capstone\Http\Requests\Admin\Finalization\SimulateRequest;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Services\FinalizationService;
use Modules\Capstone\Services\GroupAutoFixService;
use Modules\Capstone\Services\SupervisorLoadService;

class FinalizationController extends Controller
{
    protected FinalizationService $finalizationService;

    protected BiddingService $biddingService;

    protected SupervisorLoadService $supervisorLoadService;

    protected GroupAutoFixService $groupAutoFixService;

    protected FinalizationSupport $support;

    public function __construct(
        FinalizationService $finalizationService,
        BiddingService $biddingService,
        SupervisorLoadService $supervisorLoadService,
        GroupAutoFixService $groupAutoFixService,
        FinalizationSupport $support
    ) {
        $this->finalizationService = $finalizationService;
        $this->biddingService = $biddingService;
        $this->supervisorLoadService = $supervisorLoadService;
        $this->groupAutoFixService = $groupAutoFixService;
        $this->support = $support;
    }

    /**
     * Resolve which period to use.
     * V4: accept explicit period_id; fallback to single active period.
     */
    private function resolvePeriod(Request $request): Period
    {
        if ($request->filled('period_id')) {
            return Period::findOrFail($request->input('period_id'));
        }

        $activePeriods = Period::where('is_active', true)->get();

        if ($activePeriods->count() === 0) {
            abort(400, 'No active period found.');
        }

        if ($activePeriods->count() > 1) {
            abort(400, 'Multiple active periods exist. Please specify period_id.');
        }

        return $activePeriods->first();
    }

    /**
     * Standard contract envelope: {success, message, data}.
     */
    private function ok(mixed $data, string $message = 'OK'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Title-centric finalization dashboard.
     */
    public function index(Request $request)
    {
        $period = $this->resolvePeriod($request);

        // 2-LEVEL GOVERNANCE: Only show titles ready for admin finalization
        $titles = Title::with([
            'lecturer',
            'bids' => function ($q) use ($period) {
                $q->where('lecturer_recommendation', 'ACCEPT')
                    ->whereHas('group', function ($gq) use ($period) {
                        $gq->where('period_id', $period->id);
                    })
                    ->with(['group.members.student', 'proposedSupervisor1', 'proposedSupervisor2'])
                    ->orderBy('priority');
            },
        ])
            ->where(function ($q) use ($period) {
                $q->where(function ($sub) {
                    $sub->where('title_source', 'STUDENT')
                        ->where('supervisor_approval_status', 'APPROVED');
                })
                    ->orWhereHas('bids', function ($bq) use ($period) {
                        $bq->where('lecturer_recommendation', 'ACCEPT')
                            ->whereHas('group', function ($gq) use ($period) {
                                $gq->where('period_id', $period->id);
                            });
                    });
            })
            ->get();

        $titles->each(function ($title) {
            $title->current_allocations = Group::where('title_id', $title->id)
                ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
                ->count();
            $title->remaining_quota = $title->quota - $title->current_allocations;
        });

        return response()->json([
            'success' => true,
            'message' => 'Finalization overview loaded.',
            'data' => $titles,
            'period' => $period,
            'is_locked' => $period->isBiddingLocked(),
        ]);
    }

    /**
     * Supervisor load dashboard.
     */
    public function dosenLoad(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $loadData = $this->finalizationService->getSupervisorLoad(
            $period->id,
            $period->supervisorLoadLimit(8)
        );

        return response()->json([
            'success' => true,
            'message' => 'Supervisor load loaded.',
            'data' => $loadData,
        ]);
    }

    /**
     * Allocate a group via bid acceptance.
     */
    public function allocate(AllocateGroupRequest $request)
    {

        try {
            $result = $this->finalizationService->allocateGroup(
                $request->bid_id,
                $request->supervisor_1_id,
                $request->supervisor_2_id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Group allocated successfully.',
                'data' => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Allocation failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Allocate a student-proposed title.
     */
    public function allocateStudentProposed(AllocateStudentProposedRequest $request)
    {

        try {
            $result = $this->finalizationService->allocateStudentProposed(
                $request->group_id,
                $request->title_id,
                $request->supervisor_1_id,
                $request->supervisor_2_id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Student-proposed title allocated successfully.',
                'data' => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Allocation failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Batch finalize all eligible groups in a period.
     * V4: Atomic transaction — all-or-nothing.
     */
    public function finalizePeriod(FinalizePeriodRequest $request)
    {

        $period = Period::findOrFail($request->period_id);

        // ⚠ Guard: don't finalize archived/inactive period
        if (! $period->is_active) {
            return response()->json(['success' => false, 'message' => 'Cannot finalize an inactive period.'], 400);
        }

        try {
            $result = $this->finalizationService->finalizePeriod(
                $period->id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Period finalized successfully.',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Batch finalization failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Manually lock bidding.
     * Policy: bidding may only be locked after the period is finalized.
     */
    public function lock(Request $request)
    {
        $period = $this->resolvePeriod($request);

        if (! $period->is_finalized) {
            abort(400, 'Bidding hanya dapat dikunci setelah periode difinalisasi.');
        }

        $this->biddingService->lockBidding($period);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'BIDDING_LOCK',
            'target_type' => 'Period',
            'target_id' => $period->id,
            'payload' => ['locked_at' => now()->toISOString()],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidding locked successfully.',
            'data' => ['period' => $period->fresh()],
        ]);
    }

    /**
     * Manually unlock bidding (e.g. bidding was locked before finalization
     * under the old policy, or admin needs to reopen bidding).
     */
    public function unlock(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $this->biddingService->unlockBidding($period);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'BIDDING_UNLOCK',
            'target_type' => 'Period',
            'target_id' => $period->id,
            'payload' => ['unlocked_at' => now()->toISOString()],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidding unlocked successfully.',
            'data' => ['period' => $period->fresh()],
        ]);
    }

    /**
     * Group-centric dashboard: GET /admin/finalization/dashboard
     * Params: period_id, tab (ready|final|others), sub_tab (no_group|no_title|not_ready),
     * search, supervisor_status, member_count, page, per_page.
     */
    public function dashboard(DashboardRequest $request)
    {
        $period = $this->resolvePeriod($request);
        $tab = $request->input('tab', 'ready');
        $subTab = $request->input('sub_tab', 'no_group');
        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);

        $stats = $this->support->buildStats($period);
        $flow = $this->support->buildFlow($period, $tab, $subTab, $stats);

        if ($tab === 'others' && $subTab === 'no_group') {
            $data = $this->support->paginateUngroupedStudents($period, $request);
        } else {
            $data = $this->support->paginateGroups($period, $request, $tab, $subTab);
        }

        return $this->ok([
            'period' => $period,
            'tab' => $tab,
            'sub_tab' => $subTab,
            'stats' => $stats,
            'flow' => $flow,
            'data' => $data,
        ], 'Dashboard loaded.');
    }

    /**
     * Lecturer load list: GET /admin/finalization/lecturers
     */
    public function lecturers(Request $request)
    {
        $period = $this->resolvePeriod($request);
        $maxLoad = $period->supervisorLoadLimit(8);

        $lecturers = Lecturer::with('user')->get()->map(function (Lecturer $lecturer) use ($period, $maxLoad) {
            $load = $this->supervisorLoadService->getLoad($lecturer->id, $period->id);

            return [
                'id' => $lecturer->id,
                'name' => $lecturer->name,
                'email' => $lecturer->email,
                'nip' => $lecturer->employee_number,
                'current_load' => $load['current_load'],
                'max_load' => $load['max_load'] ?? $maxLoad,
                'remaining_capacity' => $load['remaining'] ?? max(0, $maxLoad - $load['current_load']),
                'is_overloaded' => $load['is_overloaded'],
            ];
        })->values();

        return $this->ok([
            'period' => $period,
            'lecturers' => $lecturers,
        ], 'Lecturers loaded.');
    }

    /**
     * Dry-run of execute: GET /admin/finalization/simulate
     */
    public function simulate(SimulateRequest $request)
    {
        $period = Period::findOrFail($request->input('period_id'));

        $groups = Group::with(['members', 'title'])
            ->where('period_id', $period->id)
            ->where('status', 'READY_FOR_FINALIZATION')
            ->get();

        $wouldFinalize = [];
        $skipped = [];

        foreach ($groups as $group) {
            $reason = $this->support->checkReadyForExecute($group);
            if ($reason === null) {
                $wouldFinalize[] = ['group_id' => $group->id, 'code' => $group->code];
            } else {
                $skipped[] = ['group_id' => $group->id, 'code' => $group->code, 'reason' => $reason];
            }
        }

        return $this->ok([
            'period' => $period,
            'would_finalize_count' => count($wouldFinalize),
            'would_finalize' => $wouldFinalize,
            'skipped_count' => count($skipped),
            'skipped' => $skipped,
        ], 'Simulation completed.');
    }

    /**
     * Set supervisors for one group: POST /admin/finalization/set-supervisor
     */
    public function setSupervisor(SetSupervisorRequest $request)
    {
        $markFinal = $request->boolean('mark_final');

        try {
            $group = $this->finalizationService->setGroupSupervisors(
                (int) $request->input('group_id'),
                (int) $request->input('supervisor_1_id'),
                $request->input('supervisor_2_id') !== null ? (int) $request->input('supervisor_2_id') : null,
                $request->user()->id,
                $request->input('notes'),
                $markFinal,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], $markFinal ? 'Kelompok berhasil ditandai sebagai Kelompok Final.' : 'Supervisor assigned.');
    }

    /**
     * Batch set supervisors: POST /admin/finalization/batch-set-supervisor
     */
    public function batchSetSupervisor(BatchSetSupervisorRequest $request)
    {
        $result = $this->finalizationService->batchSetGroupSupervisors(
            $request->input('group_ids'),
            (int) $request->input('supervisor_1_id'),
            $request->input('supervisor_2_id') !== null ? (int) $request->input('supervisor_2_id') : null,
            $request->user()->id,
            $request->input('notes'),
        );

        $message = count($result['success']) > 0
            ? 'Supervisor assigned to '.count($result['success']).' group(s).'
            : 'Failed to assign supervisors.';

        return $this->ok([
            'message' => $message,
            'results' => ['success' => $result['success'], 'failed' => $result['failed']],
            'success_count' => count($result['success']),
            'failed_count' => count($result['failed']),
        ], $message);
    }

    /**
     * Execute finalization: POST /admin/finalization/execute
     * Transitions all valid READY_FOR_FINALIZATION groups to KELOMPOK_FINAL
     * and marks the period finalized.
     */
    public function execute(ExecuteFinalizationRequest $request)
    {
        try {
            $result = $this->finalizationService->executeFinalization(
                (int) $request->input('period_id'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        $finalized = $result['finalized_count'];

        return $this->ok([
            'message' => "{$finalized} group(s) finalized successfully.",
            'finalized_count' => $result['finalized_count'],
            'skipped_count' => $result['skipped_count'],
            'skipped' => $result['skipped'],
            'period' => $result['period'],
        ], "{$finalized} group(s) finalized successfully.");
    }

    /**
     * Finalize the period flag when groups were already marked KELOMPOK_FINAL
     * one-by-one (mark_final) so `execute` has no READY_FOR_FINALIZATION rows
     * left to process. Groups already past finalization (PDC1_ACTIVE, e.g.
     * after a reopen that ran before the revert logic existed) are accepted
     * as-is. Optionally activates PDC1 for the finalized groups.
     *
     * POST /admin/finalization/finalize-period-flag
     */
    public function finalizePeriodFlag(FinalizePeriodFlagRequest $request)
    {
        $activatePdc1 = $request->boolean('activate_pdc1');

        try {
            $result = $this->finalizationService->flagFinalizePeriod(
                (int) $request->input('period_id'),
                $activatePdc1,
                $request->user()->id,
            );
        } catch (UnprocessableFinalizationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'ineligible' => $e->details['ineligible']], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok([
            'period' => $result['period'],
            'finalized_groups' => $result['finalized_groups'],
            'activated_pdc1' => $result['activated_pdc1'],
            'already_pdc1' => $result['already_active'],
        ], $activatePdc1 ? 'Periode difinalisasi dan '.count($result['activated_pdc1']).' kelompok diaktifkan ke PDC1.' : 'Periode difinalisasi.');
    }

    /**
     * Rollback finalization: POST /admin/finalization/rollback
     */
    public function rollback(RollbackFinalizationRequest $request)
    {
        try {
            $result = $this->finalizationService->rollbackFinalization(
                (int) $request->input('period_id'),
                $request->input('group_ids', []),
                (string) $request->input('reason'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        $count = $result['rolled_back_count'];

        return $this->ok([
            'message' => "{$count} group(s) rolled back successfully.",
            'rolled_back_count' => $count,
        ], "{$count} group(s) rolled back successfully.");
    }

    /**
     * Cancel a single Kelompok Final: POST /admin/finalization/cancel-kelompok-final
     */
    public function cancelKelompokFinal(CancelKelompokFinalRequest $request)
    {
        try {
            $group = $this->finalizationService->cancelKelompokFinal(
                (int) $request->input('period_id'),
                (int) $request->input('group_id'),
                $request->input('reason'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok([
            'message' => 'Kelompok Final dibatalkan.',
            'group' => $this->support->groupPayload($group->fresh()),
            'old_status' => 'KELOMPOK_FINAL',
            'new_status' => 'READY_FOR_FINALIZATION',
        ], 'Kelompok Final dibatalkan.');
    }

    /**
     * Assign a title to a group: POST /admin/finalization/assign-title
     */
    public function assignTitle(AssignTitleRequest $request)
    {
        try {
            $group = $this->finalizationService->assignGroupTitle(
                (int) $request->input('group_id'),
                (int) $request->input('title_id'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], 'Judul berhasil ditetapkan.');
    }

    /**
     * Titles with remaining quota: GET /admin/finalization/available-titles
     */
    public function availableTitles(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $titles = Title::with('lecturer')
            ->where(function ($q) use ($period) {
                // Dosen (LECTURER) titles are cross-period: visible in every period
                // while global quota remains, including legacy rows with period_id set.
                // STUDENT titles stay period-locked (approved ones only).
                $q->where(function ($q) {
                    $q->where('title_source', 'LECTURER')->orWhereNull('title_source');
                })->orWhere(function ($q) use ($period) {
                    $q->where('title_source', 'STUDENT')
                        ->where('supervisor_approval_status', 'APPROVED')
                        ->where('period_id', $period->id);
                });
            })
            ->get()
            ->map(function (Title $title) {
                $allocated = Group::where('title_id', $title->id)
                    ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
                    ->count();

                return [
                    'id' => $title->id,
                    'title' => $title->title,
                    'description' => $title->description,
                    'quota' => $title->quota,
                    'allocated' => $allocated,
                    'remaining_quota' => max(0, (int) $title->quota - $allocated),
                    'title_source' => $title->title_source,
                    'lecturer' => $title->lecturer ? ['id' => $title->lecturer->id, 'name' => $title->lecturer->name] : null,
                ];
            })
            ->filter(fn ($t) => $t['remaining_quota'] > 0)
            ->values();

        return $this->ok(['titles' => $titles], 'Available titles loaded.');
    }

    /**
     * Groups that can still accept members: GET /admin/finalization/available-groups
     */
    public function availableGroups(Request $request)
    {
        $period = $this->resolvePeriod($request);
        $maxSize = (int) ($period->max_group_size ?? 4);

        $groups = Group::with(['members.student', 'title'])
            ->where('period_id', $period->id)
            ->whereIn('status', ['FORMING', 'FORMING_SOLO', 'READY_FOR_BIDDING'])
            ->withCount('members')
            ->whereRaw('(SELECT COUNT(*) FROM capstone_group_members WHERE capstone_group_members.group_id = capstone_groups.id AND capstone_group_members.deleted_at IS NULL) < ?', [$maxSize])
            ->get()
            ->map(fn (Group $group) => $this->support->groupPayload($group));

        return $this->ok(['groups' => $groups->values()], 'Available groups loaded.');
    }

    /**
     * Promote a group to Ready for Finalization: POST /admin/finalization/promote-to-ready
     */
    public function promoteToReady(PromoteToReadyRequest $request)
    {
        try {
            $group = $this->finalizationService->promoteGroupToReady(
                (int) $request->input('group_id'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], 'Grup berhasil dipromosikan ke Ready for Finalization.');
    }

    /**
     * Create a manual group: POST /admin/finalization/create-manual-group
     */
    public function createManualGroup(CreateManualGroupRequest $request)
    {
        try {
            $group = $this->finalizationService->createManualGroup(
                (int) $request->input('period_id'),
                $request->input('student_ids'),
                (string) $request->input('option'),
                $request->input('title_id') !== null ? (int) $request->input('title_id') : null,
                $request->input('new_title'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], 'Grup berhasil dibuat.');
    }

    /**
     * Add students to an existing group: POST /admin/finalization/add-to-existing-group
     */
    public function addToExistingGroup(AddToExistingGroupRequest $request)
    {
        try {
            $group = $this->finalizationService->addStudentsToGroup(
                (int) $request->input('group_id'),
                $request->input('student_ids'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], 'Anggota berhasil ditambahkan ke grup.');
    }

    /**
     * Reopen a finalized period: POST /admin/finalization/reopen
     */
    public function reopen(ReopenPeriodRequest $request)
    {
        try {
            $result = $this->finalizationService->reopenPeriod(
                (int) $request->input('period_id'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['period' => $result['period'], 'reverted_group_ids' => $result['reverted_group_ids'], 'left_post_final' => $result['left_post_final']], 'Periode dibuka kembali.');
    }

    /**
     * Export finalization report: GET /admin/finalization/export?period_id=&format=excel|pdf
     */
    public function export(ExportRequest $request)
    {
        $period = Period::findOrFail($request->input('period_id'));

        $groups = Group::with(['members.student.user', 'title', 'supervisor1.user', 'supervisor2.user'])
            ->where('period_id', $period->id)
            ->orderBy('id')
            ->get();

        $filename = "finalisasi_periode_{$period->id}_".now()->format('Y-m-d');

        if ($request->input('format') === 'excel') {
            $lines = ['Kelompok;Status;Judul;Anggota;Pembimbing 1;Pembimbing 2'];
            foreach ($groups as $group) {
                $members = $group->members->map(fn ($m) => $m->student?->name.' ('.$m->student?->student_number.')')->implode(' | ');
                $lines[] = implode(';', [
                    $group->code ?? "Kelompok #{$group->id}",
                    $group->status,
                    '"'.str_replace('"', '""', (string) ($group->title?->title ?? '-')).'"',
                    '"'.str_replace('"', '""', $members).'"',
                    $group->supervisor1?->name ?? '-',
                    $group->supervisor2?->name ?? '-',
                ]);
            }

            return response(implode("\n", $lines), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ]);
        }

        $rows = $groups->map(fn (Group $group) => '<tr><td>'.e($group->code ?? "Kelompok #{$group->id}").'</td><td>'.e($group->status).'</td><td>'.e($group->title?->title ?? '-').'</td><td>'.e($group->supervisor1?->name ?? '-').'</td></tr>')->implode('');
        $html = '<html><body><h1>Laporan Finalisasi — '.e($period->name).'</h1><table border="1" cellpadding="6"><tr><th>Kelompok</th><th>Status</th><th>Judul</th><th>Pembimbing 1</th></tr>'.$rows.'</table></body></html>';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
        ]);
    }

    /**
     * Auto-fix group readiness: POST /admin/finalization/auto-fix
     */
    public function autoFix(AutoFixRequest $request)
    {
        $mode = $request->input('mode', 'safe');
        $adminId = $request->user()->id;

        if ($request->filled('group_id')) {
            $group = Group::findOrFail($request->input('group_id'));
            $result = $this->groupAutoFixService->fixGroupReadiness($group, $mode, $adminId);

            return $this->ok($result, $result['message']);
        }

        $period = $this->resolvePeriod($request);
        $result = $this->groupAutoFixService->fixPeriodGroupsReadiness($period->id, $mode, $adminId);

        return $this->ok($result, "Auto-fix selesai: {$result['fixed']} diperbaiki, {$result['failed']} gagal.");
    }

    /**
     * Force a group to Ready for Finalization: POST /admin/finalization/force-ready
     */
    public function forceReady(ForceReadyRequest $request)
    {
        try {
            $group = $this->finalizationService->forceGroupReady(
                (int) $request->input('group_id'),
                $request->input('reason'),
                $request->user()->id,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->support->groupPayload($group->fresh())], 'Grup dipaksa ke Ready for Finalization.');
    }
}
