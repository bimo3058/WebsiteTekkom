<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\ExpoEligibilityService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\SchedulingService;

class ExpoController extends Controller
{
    protected GroupStateMachine $stateMachine;

    protected SchedulingService $schedulingService;

    protected ExpoEligibilityService $eligibilityService;

    public function __construct(
        GroupStateMachine $stateMachine,
        SchedulingService $schedulingService,
        ExpoEligibilityService $eligibilityService
    ) {
        $this->stateMachine = $stateMachine;
        $this->schedulingService = $schedulingService;
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * List EXPO schedules (admin).
     */
    public function index()
    {
        $schedules = SeminarSchedule::with(['group.title', 'examiner1', 'examiner2', 'evaluations.examiner'])
            ->where('type', 'EXPO')
            ->where('status', '!=', 'CANCELLED')
            ->orderByDesc('date')
            ->get();

        return response()->json(['data' => $schedules]);
    }

    /**
     * Cancel an EXPO schedule (admin).
     *
     * Mirrors the SEMPRO cancel guards. The group is moved back to
     * PDC2_READY_FOR_EXPO (canTransition-guarded) so it can re-register.
     */
    public function cancel(Request $request, int $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $schedule = SeminarSchedule::where('type', 'EXPO')->lockForUpdate()->findOrFail($id);
            abort_unless(in_array($schedule->status, ['SCHEDULED', 'PENDING_APPROVAL'], true), 422, 'Jadwal tidak dapat dibatalkan.');
            abort_if($schedule->evaluations()->where('status', '!=', 'PENDING')->exists(), 422, 'Jadwal yang sudah dinilai tidak dapat dibatalkan.');
            $schedule->update(['status' => 'CANCELLED']);

            $group = Group::lockForUpdate()->find($schedule->group_id);
            if ($group && $group->status === 'EXPO_REGISTERED'
                && $this->stateMachine->canTransition($group->status, 'PDC2_READY_FOR_EXPO')) {
                $this->stateMachine->transition($group, 'PDC2_READY_FOR_EXPO');
            }

            AuditLog::create(['user_id' => $request->user()->id, 'action' => 'EXPO_CANCELLED', 'target_type' => 'SeminarSchedule', 'target_id' => $id, 'payload' => ['group_id' => $schedule->group_id]]);

            $this->notifyExpoCancelled($schedule);

            return response()->json(['message' => 'Jadwal dibatalkan.']);
        });
    }

    /**
     * Notify group members and examiners that an EXPO schedule was cancelled.
     * The CANCELLED row is hidden from student/dosen cards; this notification
     * is the visible trace for both roles.
     */
    private function notifyExpoCancelled(SeminarSchedule $schedule): void
    {
        $notificationService = app(NotificationService::class);
        $group = Group::find($schedule->group_id);
        $groupName = $group?->name ?? $group?->code ?? 'Group '.$schedule->group_id;

        if ($group) {
            $studentIds = $group->members()->with('student')->get()->pluck('student.user_id')->filter()->all();
            $notificationService->sendToMany(
                $studentIds,
                'EXPO_CANCELLED',
                'Jadwal EXPO Dibatalkan',
                "Jadwal EXPO {$groupName} pada {$schedule->date} telah dibatalkan oleh admin.",
                'SeminarSchedule',
                $schedule->id
            );
        }

        $notificationService->sendToMany(
            Lecturer::whereIn('id', [$schedule->examiner_1_id, $schedule->examiner_2_id])->pluck('user_id')->filter()->all(),
            'EXPO_CANCELLED',
            'Jadwal EXPO Dibatalkan',
            "Jadwal EXPO {$groupName} pada {$schedule->date} yang Anda uji telah dibatalkan oleh admin.",
            'SeminarSchedule',
            $schedule->id
        );
    }

    /**
     * Reject a student-submitted EXPO schedule request (admin).
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        $schedule = SeminarSchedule::where('id', $id)
            ->where('type', 'EXPO')
            ->where('status', 'PENDING_APPROVAL')
            ->firstOrFail();

        $schedule->update([
            'status' => 'CANCELLED',
            'rejection_reason' => $request->rejection_reason,
        ]);

        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'EXPO_REJECTED', 'target_type' => 'SeminarSchedule', 'target_id' => $schedule->id, 'payload' => ['group_id' => $schedule->group_id]]);

        // Notify students
        $notificationService = app(NotificationService::class);
        $group = Group::find($schedule->group_id);
        if ($group) {
            $studentIds = $group->members()->with('student')->get()->pluck('student.user_id')->filter()->all();
            $notificationService->sendToMany(
                $studentIds,
                'SCHEDULE_REJECTED',
                'EXPO Schedule Rejected',
                "Your EXPO schedule request was rejected. Reason: {$request->rejection_reason}",
                'seminar_schedules',
                $schedule->id
            );
        }

        return response()->json(['message' => 'EXPO schedule request rejected.', 'data' => $schedule]);
    }
}
