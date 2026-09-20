<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Services\BiddingService;
use Modules\Capstone\Services\FinalizationService;
use Modules\Capstone\Services\GroupAutoFixService;
use Modules\Capstone\Services\GroupService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\SupervisorLoadService;

class FinalizationController extends Controller
{
    protected FinalizationService $finalizationService;

    protected BiddingService $biddingService;

    protected GroupService $groupService;

    protected SupervisorLoadService $supervisorLoadService;

    protected GroupAutoFixService $groupAutoFixService;

    protected GroupStateMachine $stateMachine;

    public function __construct(
        FinalizationService $finalizationService,
        BiddingService $biddingService,
        GroupService $groupService,
        SupervisorLoadService $supervisorLoadService,
        GroupAutoFixService $groupAutoFixService,
        GroupStateMachine $stateMachine
    ) {
        $this->finalizationService = $finalizationService;
        $this->biddingService = $biddingService;
        $this->groupService = $groupService;
        $this->supervisorLoadService = $supervisorLoadService;
        $this->groupAutoFixService = $groupAutoFixService;
        $this->stateMachine = $stateMachine;
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
    public function allocate(Request $request)
    {
        $request->validate([
            'bid_id' => 'required|exists:capstone_bids,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
        ]);

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
    public function allocateStudentProposed(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'title_id' => 'required|exists:capstone_titles,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
        ]);

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
    public function finalizePeriod(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

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

    // ======================================================================
    // Dashboard contract (admin/finalization/*)
    // ======================================================================

    public const READY_STATUSES = ['READY_FOR_FINALIZATION'];

    public const FINAL_STATUSES = ['KELOMPOK_FINAL'];

    public const POST_FINALIZATION_STATUSES = ['PDC1_ACTIVE', 'READY_FOR_SEMPRO', 'SEMPRO_DONE', 'PDC2_ACTIVE', 'PDC2_READY_FOR_EXPO', 'EXPO_REGISTERED', 'EXPO_DONE', 'PDC2_COMPLETED', 'CLOSED'];

    public const NOT_READY_STATUSES = ['FORMING', 'FORMING_SOLO', 'WAITING_SUPERVISOR_APPROVAL', 'READY_FOR_BIDDING', 'TITLE_PROPOSED', 'TITLE_APPROVED'];

    /**
     * Group-centric dashboard: GET /admin/finalization/dashboard
     * Params: period_id, tab (ready|final|others), sub_tab (no_group|no_title|not_ready),
     * search, supervisor_status, member_count, page, per_page.
     */
    public function dashboard(Request $request)
    {
        $request->validate([
            'tab' => 'nullable|in:ready,final,post,others',
            'sub_tab' => 'nullable|in:no_group,no_title,not_ready',
            'search' => 'nullable|string|max:255',
            'supervisor_status' => 'nullable|in:all,missing_sv1,missing_sv2,complete',
            'member_count' => 'nullable|in:all,under_min,in_range,over_max',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $period = $this->resolvePeriod($request);
        $tab = $request->input('tab', 'ready');
        $subTab = $request->input('sub_tab', 'no_group');
        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);

        $stats = $this->buildStats($period);
        $flow = $this->buildFlow($period, $tab, $subTab, $stats);

        if ($tab === 'others' && $subTab === 'no_group') {
            $data = $this->paginateUngroupedStudents($period, $request);
        } else {
            $data = $this->paginateGroups($period, $request, $tab, $subTab);
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
    public function simulate(Request $request)
    {
        $request->validate(['period_id' => 'required|exists:capstone_periods,id']);
        $period = Period::findOrFail($request->input('period_id'));

        $groups = Group::with(['members', 'title'])
            ->where('period_id', $period->id)
            ->where('status', 'READY_FOR_FINALIZATION')
            ->get();

        $wouldFinalize = [];
        $skipped = [];

        foreach ($groups as $group) {
            $reason = $this->checkReadyForExecute($group);
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
    public function setSupervisor(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
            'notes' => 'nullable|string|max:1000',
            'mark_final' => 'nullable|boolean',
        ]);

        $group = Group::findOrFail($request->input('group_id'));

        try {
            $group = $this->applySupervisors(
                $group,
                (int) $request->input('supervisor_1_id'),
                $request->input('supervisor_2_id') !== null ? (int) $request->input('supervisor_2_id') : null,
                $request->user()->id,
                $request->input('notes')
            );

            // Mark as Kelompok Final: READY_FOR_FINALIZATION → KELOMPOK_FINAL.
            // Requires title + member count in range + SV1 + SV2.
            if ($request->boolean('mark_final')) {
                $group->loadMissing(['period', 'title', 'members']);
                $reason = $this->checkReadyForExecute($group->fresh());
                if ($reason !== null) {
                    throw new \InvalidArgumentException('Cannot mark Kelompok Final: '.$reason);
                }
                $oldStatus = $group->status;
                $this->stateMachine->transition($group, 'KELOMPOK_FINAL');
                $group->finalized_at = now();
                $group->finalized_by = $request->user()->id;
                $group->save();

                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FINALIZATION_MARK_KELOMPOK_FINAL',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['old_status' => $oldStatus, 'new_status' => 'KELOMPOK_FINAL'],
                ]);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->groupPayload($group->fresh())], $request->boolean('mark_final') ? 'Kelompok berhasil ditandai sebagai Kelompok Final.' : 'Supervisor assigned.');
    }

    /**
     * Batch set supervisors: POST /admin/finalization/batch-set-supervisor
     */
    public function batchSetSupervisor(Request $request)
    {
        $request->validate([
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'integer|exists:capstone_groups,id',
            'supervisor_1_id' => 'required|exists:lecturers,id',
            'supervisor_2_id' => 'nullable|exists:lecturers,id|different:supervisor_1_id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $success = [];
        $failed = [];

        foreach ($request->input('group_ids') as $groupId) {
            try {
                $group = Group::findOrFail($groupId);
                $this->applySupervisors(
                    $group,
                    (int) $request->input('supervisor_1_id'),
                    $request->input('supervisor_2_id') !== null ? (int) $request->input('supervisor_2_id') : null,
                    $request->user()->id,
                    $request->input('notes')
                );
                $success[] = ['group_id' => $group->id, 'name' => $group->code ?? "Kelompok #{$group->id}"];
            } catch (\Exception $e) {
                $failed[] = ['group_id' => (int) $groupId, 'reason' => $e->getMessage()];
            }
        }

        $message = count($success) > 0
            ? 'Supervisor assigned to '.count($success).' group(s).'
            : 'Failed to assign supervisors.';

        return $this->ok([
            'message' => $message,
            'results' => ['success' => $success, 'failed' => $failed],
            'success_count' => count($success),
            'failed_count' => count($failed),
        ], $message);
    }

    /**
     * Execute finalization: POST /admin/finalization/execute
     * Transitions all valid READY_FOR_FINALIZATION groups to KELOMPOK_FINAL
     * and marks the period finalized.
     */
    public function execute(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'confirmation' => 'required|accepted',
        ]);

        $period = Period::findOrFail($request->input('period_id'));

        if (! $period->is_active) {
            return response()->json(['success' => false, 'message' => 'Cannot execute finalization on an inactive period.'], 400);
        }

        if ($period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Period is already finalized.'], 400);
        }

        $groups = Group::where('period_id', $period->id)
            ->where('status', 'READY_FOR_FINALIZATION')
            ->get();

        if ($groups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No groups ready for finalization.'], 400);
        }

        $finalized = 0;
        $skipped = [];

        DB::transaction(function () use ($groups, $request, $period, &$finalized, &$skipped) {
            foreach ($groups as $group) {
                $reason = $this->checkReadyForExecute($group);
                if ($reason !== null) {
                    $skipped[] = ['group_id' => $group->id, 'reason' => $reason];

                    continue;
                }

                $oldStatus = $group->status;
                $this->stateMachine->transition($group, 'KELOMPOK_FINAL');
                $group->finalized_at = now();
                $group->finalized_by = $request->user()->id;
                $group->save();

                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FINALIZATION_EXECUTE',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['old_status' => $oldStatus, 'new_status' => 'KELOMPOK_FINAL', 'period_id' => $period->id],
                ]);

                $finalized++;
            }

            if ($finalized === 0) {
                throw new \InvalidArgumentException('No eligible groups could be finalized. '.($skipped[0]['reason'] ?? ''));
            }

            $period->is_finalized = true;
            $period->save();
        });

        return $this->ok([
            'message' => "{$finalized} group(s) finalized successfully.",
            'finalized_count' => $finalized,
            'skipped_count' => count($skipped),
            'skipped' => $skipped,
            'period' => $period->fresh(),
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
    public function finalizePeriodFlag(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'activate_pdc1' => 'nullable|boolean',
            'confirmation' => 'required|accepted',
        ]);

        $period = Period::findOrFail($request->input('period_id'));

        if (! $period->is_active) {
            return response()->json(['success' => false, 'message' => 'Cannot finalize an inactive period.'], 400);
        }

        if ($period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Period is already finalized.'], 400);
        }

        $base = Group::where('period_id', $period->id);

        $readyLeft = (clone $base)->whereIn('status', self::READY_STATUSES)->count();
        if ($readyLeft > 0) {
            return response()->json(['success' => false, 'message' => "Masih ada {$readyLeft} kelompok berstatus siap finalisasi."], 400);
        }

        $notReadyLeft = (clone $base)->whereIn('status', self::NOT_READY_STATUSES)->count();
        if ($notReadyLeft > 0) {
            return response()->json(['success' => false, 'message' => "Masih ada {$notReadyLeft} kelompok yang belum siap finalisasi."], 400);
        }

        $finalGroups = (clone $base)->whereIn('status', ['KELOMPOK_FINAL', 'PDC1_ACTIVE'])->with(['period', 'title', 'members'])->get();
        if ($finalGroups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada kelompok final atau pasca-final pada periode ini.'], 400);
        }

        $ineligible = [];
        foreach ($finalGroups as $group) {
            $reason = $this->checkReadyForExecute($group);
            if ($reason !== null) {
                $ineligible[] = ['group_id' => $group->id, 'code' => $group->code, 'reason' => $reason];
            }
        }
        if (! empty($ineligible)) {
            return response()->json(['success' => false, 'message' => 'Ada kelompok final yang tidak memenuhi syarat.', 'ineligible' => $ineligible], 422);
        }

        $activatePdc1 = $request->boolean('activate_pdc1');
        $activated = [];
        $alreadyActive = [];

        DB::transaction(function () use ($period, $request, $finalGroups, $activatePdc1, &$activated, &$alreadyActive) {
            $locked = Period::lockForUpdate()->findOrFail($period->id);
            if ($locked->is_finalized) {
                throw new \InvalidArgumentException('Period is already finalized.');
            }
            $locked->is_finalized = true;
            $locked->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
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
                        'user_id' => $request->user()->id,
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

        return $this->ok([
            'period' => $period->fresh(),
            'finalized_groups' => $finalGroups->pluck('id')->all(),
            'activated_pdc1' => $activated,
            'already_pdc1' => $alreadyActive,
        ], $activatePdc1 ? 'Periode difinalisasi dan '.count($activated).' kelompok diaktifkan ke PDC1.' : 'Periode difinalisasi.');
    }

    /**
     * Rollback finalization: POST /admin/finalization/rollback
     */
    public function rollback(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'integer|exists:capstone_groups,id',
            'reason' => 'required|string|max:1000',
        ]);

        $period = Period::findOrFail($request->input('period_id'));
        $groupIds = $request->input('group_ids', []);

        $query = Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL');
        if (! empty($groupIds)) {
            $query->whereIn('id', $groupIds);
        }
        $groups = $query->get();

        if ($groups->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No finalized groups to roll back.'], 400);
        }

        $count = 0;
        DB::transaction(function () use ($groups, $request, $period, &$count) {
            foreach ($groups as $group) {
                $oldStatus = $group->status;
                $group->status = 'READY_FOR_FINALIZATION';
                $group->finalized_at = null;
                $group->finalized_by = null;
                $group->save();

                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FINALIZATION_ROLLBACK',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['old_status' => $oldStatus, 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $request->input('reason'), 'period_id' => $period->id],
                ]);

                $count++;
            }

            if (Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL')->count() === 0) {
                $period->is_finalized = false;
                $period->save();
            }
        });

        return $this->ok([
            'message' => "{$count} group(s) rolled back successfully.",
            'rolled_back_count' => $count,
        ], "{$count} group(s) rolled back successfully.");
    }

    /**
     * Cancel a single Kelompok Final: POST /admin/finalization/cancel-kelompok-final
     */
    public function cancelKelompokFinal(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_id' => 'required|exists:capstone_groups,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $group = Group::where('period_id', $request->input('period_id'))->findOrFail($request->input('group_id'));

        if ($group->status !== 'KELOMPOK_FINAL') {
            return response()->json(['success' => false, 'message' => 'Group is not in KELOMPOK_FINAL status.'], 400);
        }

        DB::transaction(function () use ($group, $request) {
            $group->status = 'READY_FOR_FINALIZATION';
            $group->finalized_at = null;
            $group->finalized_by = null;
            $group->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'FINALIZATION_CANCEL_KELOMPOK_FINAL',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['old_status' => 'KELOMPOK_FINAL', 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $request->input('reason')],
            ]);

            $period = $group->period;
            if ($period && Group::where('period_id', $period->id)->where('status', 'KELOMPOK_FINAL')->count() === 0) {
                $period->is_finalized = false;
                $period->save();
            }
        });

        return $this->ok([
            'message' => 'Kelompok Final dibatalkan.',
            'group' => $this->groupPayload($group->fresh()),
            'old_status' => 'KELOMPOK_FINAL',
            'new_status' => 'READY_FOR_FINALIZATION',
        ], 'Kelompok Final dibatalkan.');
    }

    /**
     * Assign a title to a group: POST /admin/finalization/assign-title
     */
    public function assignTitle(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'title_id' => 'required|exists:capstone_titles,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $title = Title::findOrFail($request->input('title_id'));

        // Title assignment is for groups that are not yet ready/finalized.
        // Siap Finalisasi only sets supervisors — it must not (re)assign titles.
        if (in_array($group->status, array_merge(self::READY_STATUSES, self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES), true)) {
            return response()->json(['success' => false, 'message' => 'Cannot assign title: group is already ready for finalization or finalized.'], 400);
        }

        if ($group->period && $group->period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Cannot assign title: period is finalized.'], 400);
        }

        if ($title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            return response()->json(['success' => false, 'message' => 'Cannot assign: student title has not been approved by the supervisor.'], 400);
        }

        // STUDENT titles are period-locked; LECTURER titles are cross-period.
        if ($title->title_source === 'STUDENT' && $title->period_id && (int) $title->period_id !== (int) $group->period_id) {
            return response()->json(['success' => false, 'message' => 'Title belongs to another period.'], 400);
        }

        $allocated = Group::where('title_id', $title->id)
            ->where('id', '!=', $group->id)
            ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
            ->count();

        if ($allocated >= (int) $title->quota) {
            return response()->json(['success' => false, 'message' => 'Title quota is full.'], 400);
        }

        DB::transaction(function () use ($group, $title, $request) {
            $group->assignTitleFromFinalization($title->id);
            $group->save();

            if ($group->status === 'FORMING' && $this->stateMachine->canTransition('FORMING', 'TITLE_APPROVED')) {
                $this->stateMachine->transition($group, 'TITLE_APPROVED');
            }

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'FINALIZATION_ASSIGN_TITLE',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['title_id' => $title->id],
            ]);
        });

        return $this->ok(['group' => $this->groupPayload($group->fresh())], 'Judul berhasil ditetapkan.');
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
            ->map(fn (Group $group) => $this->groupPayload($group));

        return $this->ok(['groups' => $groups->values()], 'Available groups loaded.');
    }

    /**
     * Promote a group to Ready for Finalization: POST /admin/finalization/promote-to-ready
     */
    public function promoteToReady(Request $request)
    {
        $request->validate(['group_id' => 'required|exists:capstone_groups,id']);

        $group = Group::with(['members', 'title'])->findOrFail($request->input('group_id'));

        if ($group->period && $group->period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Cannot promote: period is finalized.'], 400);
        }

        if ($group->status === 'READY_FOR_FINALIZATION') {
            return response()->json(['success' => false, 'message' => 'Group is already ready for finalization.'], 400);
        }

        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = $group->members->count();

        if ($memberCount < $minSize || $memberCount > $maxSize) {
            return response()->json(['success' => false, 'message' => "Cannot promote: group has {$memberCount} members, allowed range is {$minSize}–{$maxSize}."], 400);
        }

        // A group can only be marked ready when it has a concrete title assigned.
        // (An ACCEPT bid alone is not enough — assign the title first via assign-title.)
        if ($group->title_id === null) {
            return response()->json(['success' => false, 'message' => 'Cannot promote: group has no title. Assign a title first.'], 400);
        }

        $title = $group->title;
        if ($title && $title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            return response()->json(['success' => false, 'message' => 'Cannot promote: student title has not been approved by the supervisor.'], 400);
        }

        try {
            DB::transaction(function () use ($group, $request) {
                if ($group->status === 'FORMING') {
                    $this->stateMachine->transition($group, 'TITLE_APPROVED');
                    $group->refresh();
                }
                $this->stateMachine->transition($group, 'READY_FOR_FINALIZATION');

                AuditLog::create([
                    'user_id' => $request->user()->id,
                    'action' => 'FINALIZATION_PROMOTE_TO_READY',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['new_status' => 'READY_FOR_FINALIZATION'],
                ]);
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return $this->ok(['group' => $this->groupPayload($group->fresh())], 'Grup berhasil dipromosikan ke Ready for Finalization.');
    }

    /**
     * Create a manual group: POST /admin/finalization/create-manual-group
     */
    public function createManualGroup(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
            'period_id' => 'required|exists:capstone_periods,id',
            'option' => 'required|in:no_title,assign_title,add_title',
            'title_id' => 'required_if:option,assign_title|nullable|exists:capstone_titles,id',
            'new_title.title' => 'required_if:option,add_title|nullable|string|max:255',
            'new_title.description' => 'nullable|string',
            'new_title.specializations' => 'nullable|array',
            'new_title.lecturer_id' => 'required_if:option,add_title|nullable|exists:lecturers,id',
        ]);

        $period = Period::findOrFail($request->input('period_id'));

        if (! $period->is_active || $period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Cannot create group: period is not active or already finalized.'], 400);
        }

        $students = $this->resolveStudents($request->input('student_ids'));

        if (count($students) !== count(array_unique($request->input('student_ids')))) {
            return response()->json(['success' => false, 'message' => 'One or more students were not found.'], 400);
        }

        $maxSize = (int) ($period->max_group_size ?? 4);
        if (count($students) > $maxSize) {
            return response()->json(['success' => false, 'message' => "Too many members: maximum is {$maxSize}."], 400);
        }

        foreach ($students as $student) {
            if ($this->hasMembershipInPeriod((int) $student->id, $period->id)) {
                return response()->json(['success' => false, 'message' => "Student {$student->name} is already grouped in this period."], 400);
            }
        }

        try {
            $group = DB::transaction(function () use ($students, $period, $request) {
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

                if ($request->input('option') === 'assign_title') {
                    $title = Title::findOrFail($request->input('title_id'));
                    $this->assertTitleAssignable($title, $group->id);
                    $group->assignTitleFromFinalization($title->id);
                    $group->save();
                    if ($this->stateMachine->canTransition($group->status, 'TITLE_APPROVED')) {
                        $this->stateMachine->transition($group, 'TITLE_APPROVED');
                    }
                }

                if ($request->input('option') === 'add_title') {
                    $payload = $request->input('new_title');
                    $title = Title::create([
                        'lecturer_id' => $payload['lecturer_id'],
                        'title' => $payload['title'],
                        'description' => $payload['description'] ?? null,
                        'specializations' => $payload['specializations'] ?? [],
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
                    'user_id' => $request->user()->id,
                    'action' => 'FINALIZATION_CREATE_MANUAL_GROUP',
                    'target_type' => 'Group',
                    'target_id' => $group->id,
                    'payload' => ['period_id' => $period->id, 'option' => $request->input('option'), 'member_count' => count($students)],
                ]);

                return $group;
            });
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        // Sync state with the student flow: a group meeting the min size
        // should read READY_FOR_BIDDING, not stay stuck at FORMING.
        if (in_array($group->status, ['FORMING', 'FORMING_SOLO'], true)) {
            try {
                $this->groupService->evaluateGroupReadiness($group->fresh());
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $this->ok(['group' => $this->groupPayload($group->fresh())], 'Grup berhasil dibuat.');
    }

    /**
     * Add students to an existing group: POST /admin/finalization/add-to-existing-group
     */
    public function addToExistingGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'integer',
        ]);

        $group = Group::with('period')->findOrFail($request->input('group_id'));
        $period = $group->period;

        if (! $period || ! $period->is_active || $period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Cannot add members: period is not active or already finalized.'], 400);
        }

        if (in_array($group->status, array_merge(self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES), true)) {
            return response()->json(['success' => false, 'message' => 'Cannot add members to a finalized group.'], 400);
        }

        $students = $this->resolveStudents($request->input('student_ids'));

        if (count($students) !== count(array_unique($request->input('student_ids')))) {
            return response()->json(['success' => false, 'message' => 'One or more students were not found.'], 400);
        }

        $maxSize = (int) ($period->max_group_size ?? 4);
        $currentCount = GroupMember::where('group_id', $group->id)->count();

        if ($currentCount + count($students) > $maxSize) {
            return response()->json(['success' => false, 'message' => "Group would exceed maximum size of {$maxSize}."], 400);
        }

        foreach ($students as $student) {
            if ($this->hasMembershipInPeriod((int) $student->id, $period->id)) {
                return response()->json(['success' => false, 'message' => "Student {$student->name} is already grouped in this period."], 400);
            }
        }

        DB::transaction(function () use ($students, $group, $period, $request) {
            foreach ($students as $student) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'student_id' => $student->id,
                    'is_leader' => false,
                    'period_id' => $period->id,
                ]);
            }

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'FINALIZATION_ADD_TO_EXISTING_GROUP',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['added_count' => count($students)],
            ]);
        });

        // A top-up can push the group over the min size: promote
        // FORMING -> READY_FOR_BIDDING like the student flow does.
        $group->refresh();
        if (in_array($group->status, ['FORMING', 'FORMING_SOLO'], true)) {
            try {
                $this->groupService->evaluateGroupReadiness($group);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $this->ok(['group' => $this->groupPayload($group->fresh())], 'Anggota berhasil ditambahkan ke grup.');
    }

    /**
     * Reopen a finalized period: POST /admin/finalization/reopen
     */
    public function reopen(Request $request)
    {
        $request->validate(['period_id' => 'required|exists:capstone_periods,id']);

        $period = Period::findOrFail($request->input('period_id'));

        $reverted = [];
        $leftPostFinal = [];

        DB::transaction(function () use ($period, $request, &$reverted, &$leftPostFinal) {
            $locked = Period::lockForUpdate()->findOrFail($period->id);
            $locked->is_finalized = false;
            $locked->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
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
                ->whereIn('status', array_merge(self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES))
                ->lockForUpdate()
                ->get();

            foreach ($groups as $group) {
                if ($group->status === 'PDC1_ACTIVE') {
                    $group->status = 'KELOMPOK_FINAL';
                    $group->save();
                    $reverted[] = $group->id;

                    AuditLog::create([
                        'user_id' => $request->user()->id,
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

        return $this->ok(['period' => $period->fresh(), 'reverted_group_ids' => $reverted, 'left_post_final' => $leftPostFinal], 'Periode dibuka kembali.');
    }

    /**
     * Export finalization report: GET /admin/finalization/export?period_id=&format=excel|pdf
     */
    public function export(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'format' => 'required|in:excel,pdf',
        ]);

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
    public function autoFix(Request $request)
    {
        $request->validate([
            'mode' => 'nullable|in:safe,aggressive',
            'group_id' => 'nullable|exists:capstone_groups,id',
            'period_id' => 'nullable|exists:capstone_periods,id',
        ]);

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
    public function forceReady(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $group = Group::with('period')->findOrFail($request->input('group_id'));

        if ($group->period && $group->period->is_finalized) {
            return response()->json(['success' => false, 'message' => 'Cannot force ready: period is finalized.'], 400);
        }

        if (in_array($group->status, array_merge(self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES), true)) {
            return response()->json(['success' => false, 'message' => 'Group is already past finalization.'], 400);
        }

        // Same gates as promote-to-ready: concrete title + member count in range.
        $group->loadMissing(['members', 'title']);
        $period = $group->period;
        $minSize = $group->group_mode === 'INDIVIDUAL' ? 1 : (int) ($period?->min_group_size ?? 3);
        $maxSize = (int) ($period?->max_group_size ?? 4);
        $memberCount = $group->members->count();

        if ($memberCount < $minSize || $memberCount > $maxSize) {
            return response()->json(['success' => false, 'message' => "Cannot force ready: group has {$memberCount} members, allowed range is {$minSize}–{$maxSize}."], 400);
        }

        if ($group->title_id === null) {
            return response()->json(['success' => false, 'message' => 'Cannot force ready: group has no title. Assign a title first.'], 400);
        }

        $title = $group->title;
        if ($title && $title->title_source === 'STUDENT' && $title->supervisor_approval_status !== 'APPROVED') {
            return response()->json(['success' => false, 'message' => 'Cannot force ready: student title has not been approved by the supervisor.'], 400);
        }

        $oldStatus = $group->status;

        DB::transaction(function () use ($group, $oldStatus, $request) {
            $group->status = 'READY_FOR_FINALIZATION';
            $group->save();

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'FINALIZATION_FORCE_READY',
                'target_type' => 'Group',
                'target_id' => $group->id,
                'payload' => ['old_status' => $oldStatus, 'new_status' => 'READY_FOR_FINALIZATION', 'reason' => $request->input('reason')],
            ]);
        });

        return $this->ok(['group' => $this->groupPayload($group->fresh())], 'Grup dipaksa ke Ready for Finalization.');
    }

    // ======================================================================
    // Internal helpers
    // ======================================================================

    private function buildStats(Period $period): array
    {
        $base = Group::where('period_id', $period->id);

        $ready = (clone $base)->whereIn('status', self::READY_STATUSES)->count();
        $final = (clone $base)->whereIn('status', self::FINAL_STATUSES)->count();
        $pdc1 = (clone $base)->where('status', 'PDC1_ACTIVE')->count();
        $noTitle = (clone $base)->whereNull('title_id')->count();
        $notReady = (clone $base)->whereIn('status', self::NOT_READY_STATUSES)->count();
        $postBreakdown = (clone $base)->whereIn('status', self::POST_FINALIZATION_STATUSES)
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

    private function buildFlow(Period $period, string $tab, string $subTab, array $stats): array
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

    private function groupPayload(Group $group): array
    {
        $group->loadMissing(['title.lecturer', 'members.student.user', 'period', 'supervisor1.user', 'supervisor2.user', 'supervisions.supervisor.user']);

        $payload = $this->groupService->transformGroupForAdminList($group);
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

    private function resolveFinalizationActions(Group $group): array
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
                'reason' => in_array($group->status, array_merge(self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES), true) ? 'POST_FINALIZATION' : null,
            ],
        };
    }

    private function baseGroupQuery(Period $period, Request $request)
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

    private function paginateGroups(Period $period, Request $request, string $tab, string $subTab)
    {
        $query = $this->baseGroupQuery($period, $request);

        if ($tab === 'ready') {
            $query->whereIn('status', self::READY_STATUSES);
        } elseif ($tab === 'final') {
            $query->whereIn('status', self::FINAL_STATUSES);
        } elseif ($tab === 'post') {
            $query->whereIn('status', self::POST_FINALIZATION_STATUSES);
        } elseif ($subTab === 'no_title') {
            $query->whereNull('title_id');
        } elseif ($subTab === 'not_ready') {
            $query->whereIn('status', self::NOT_READY_STATUSES);
        }

        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);
        $paginator = $query->latest()->paginate($perPage);
        $paginator->getCollection()->transform(fn (Group $group) => $this->groupPayload($group));

        return $paginator;
    }

    private function ungroupedStudentsQuery(Period $period)
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

    private function paginateUngroupedStudents(Period $period, Request $request)
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
    private function checkReadyForExecute(Group $group): ?string
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
    private function applySupervisors(Group $group, int $supervisor1Id, ?int $supervisor2Id, int $adminId, ?string $notes): Group
    {
        if ($group->period && $group->period->is_finalized) {
            throw new \InvalidArgumentException('Cannot set supervisor: period is finalized.');
        }

        if (in_array($group->status, array_merge(self::FINAL_STATUSES, self::POST_FINALIZATION_STATUSES), true)) {
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
    private function resolveStudents(array $ids): array
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

    private function hasMembershipInPeriod(int $studentId, int $periodId): bool
    {
        return GroupMember::where('student_id', $studentId)
            ->where('period_id', $periodId)
            ->exists();
    }

    private function assertTitleAssignable(Title $title, int $excludingGroupId): void
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
