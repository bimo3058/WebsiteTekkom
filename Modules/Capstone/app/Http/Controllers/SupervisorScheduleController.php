<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Evaluations\SupervisorEvaluationPresenter;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Schedule;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Support\CapstoneActor;

class SupervisorScheduleController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of groups where current dosen is supervisor with evaluation status
     */
    public function groups(Request $request): JsonResponse
    {
        $user = CapstoneActor::lecturer(Auth::user());
        $periodId = $request->input('period_id');
        $type = $request->input('type');

        // Get groups where current user is supervisor
        $supervisions = Supervision::where('supervisor_id', $user->id)
            ->when($periodId, fn ($q) => $q->whereHas('group', fn ($gq) => $gq->where('period_id', $periodId)))
            ->with(['group.period', 'group.members.student'])
            ->get();

        $groups = $supervisions->map(function ($supervision) use ($user, $type) {
            $group = $supervision->group;

            // Use canonical schedule/evaluation generator to keep group and schedule views consistent.
            $groupSchedules = app(SupervisorEvaluationPresenter::class)->getGroupSchedulesDetailed($group, $supervision, $user->id);

            // Apply type filter if provided (before building evaluation cards)
            if ($type) {
                $groupSchedules = array_filter($groupSchedules, fn ($s) => $s['evaluation_type'] === $type);
            }

            // Get one card per evaluation type
            $evaluations = [];
            foreach ($groupSchedules as $schedule) {
                $evalType = $schedule['evaluation_type'];
                if (! isset($evaluations[$evalType])) {
                    $editableStatuses = GroupLifecycleService::EDITABLE_STATUSES[$evalType] ?? [];
                    $evaluations[$evalType] = [
                        'schedule_id' => $schedule['schedule_id'],
                        'schedule_type' => $schedule['schedule_type'],
                        'date' => $schedule['date'],
                        'room' => $schedule['room'],
                        'deadline' => $schedule['deadline'],
                        'status' => strtolower((string) $schedule['status']),
                        // Schedule entries may carry a stricter editability
                        // rule (e.g. EXPO review is only actionable while the
                        // submission is pending).
                        'editable' => $schedule['editable'] ?? in_array($group->status, $editableStatuses, true),
                        'evaluation_type' => $evalType,
                    ];
                }
            }

            return [
                'id' => $group->id,
                'name' => $group->name ?? $group->code ?? 'Group '.$group->id,
                'code' => $group->code,
                'status' => $group->status,
                'period' => [
                    'id' => $group->period->id,
                    'name' => $group->period->name,
                ],
                'supervisor_role' => $supervision->role, // SUPERVISOR_1 or SUPERVISOR_2
                'members' => $group->members->map(fn ($m) => [
                    'id' => $m->student->id,
                    'name' => $m->student->name,
                    'nim' => $m->student->nim,
                    'is_leader' => $m->is_leader,
                ]),
                'evaluations' => $evaluations,
            ];
        });

        // Filter groups that have evaluations (only groups with matching evaluation types if type filter is applied)
        $groupsWithEvaluations = $groups->filter(fn ($g) => count($g['evaluations']) > 0)->values();

        return $this->successResponse($groupsWithEvaluations);
    }

    /**
     * Get count of pending evaluations for current supervisor
     */
    public function pendingCount(): JsonResponse
    {
        $user = CapstoneActor::lecturer(Auth::user());

        // Get groups where current user is supervisor
        $supervisions = Supervision::where('supervisor_id', $user->id)
            ->with('group')
            ->get();

        $pendingCount = 0;

        foreach ($supervisions as $supervision) {
            try {
                $group = $supervision->group;

                if (! $group) {
                    continue;
                }

                $groupSchedules = app(SupervisorEvaluationPresenter::class)->getGroupSchedulesDetailed($group, $supervision, $user->id);
                $pendingTypes = [];

                foreach ($groupSchedules as $schedule) {
                    $evalType = (string) $schedule['evaluation_type'];
                    $status = strtoupper((string) $schedule['status']);
                    if (in_array($status, ['PENDING', 'PARTIAL'], true) && ! in_array($evalType, $pendingTypes, true)) {
                        $pendingTypes[] = $evalType;
                    }
                }

                $pendingCount += count($pendingTypes);
            } catch (\Exception $e) {
                Log::error('Error in pendingCount for supervision', [
                    'supervision_id' => $supervision->id,
                    'group_id' => $supervision->group_id,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return $this->successResponse(['count' => $pendingCount]);
    }

    /**
     * Get schedules where current user is supervisor with evaluation info
     * Returns schedule-based view (similar to examiner)
     */
    public function schedules(Request $request): JsonResponse
    {
        $user = CapstoneActor::lecturer(Auth::user());
        $periodId = $request->input('period_id');
        $type = $request->input('type');

        // Get groups where current user is supervisor
        $supervisionsQuery = Supervision::where('supervisor_id', $user->id)
            ->with(['group.period', 'group.members.student']);

        if ($periodId) {
            $supervisionsQuery->whereHas('group', function ($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });
        }

        $supervisions = $supervisionsQuery->get();

        $schedules = [];

        foreach ($supervisions as $supervision) {
            $group = $supervision->group;

            // Skip if group doesn't exist
            if (! $group) {
                continue;
            }

            // Get detailed schedules for this group
            $groupSchedules = app(SupervisorEvaluationPresenter::class)->getGroupSchedulesDetailed($group, $supervision, $user->id);

            // Apply type filter if provided
            if ($type) {
                $groupSchedules = array_filter($groupSchedules, fn ($s) => $s['evaluation_type'] === $type);
            }

            $schedules = array_merge($schedules, $groupSchedules);
        }

        // Sort by date and start time (undated cards go last)
        usort($schedules, function ($a, $b) {
            $aDate = $a['date'] ? strtotime($a['date']) : PHP_INT_MAX;
            $bDate = $b['date'] ? strtotime($b['date']) : PHP_INT_MAX;
            $dateCompare = $aDate - $bDate;
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return strcmp((string) $a['start_time'], (string) $b['start_time']);
        });

        return $this->successResponse($schedules);
    }
}
