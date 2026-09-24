<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\SeminarEvaluation;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Modules\Capstone\Services\ExpoEligibilityService;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Services\NotificationService;
use Modules\Capstone\Services\SchedulingService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\EOffice\Models\Ruangan;

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
     * Schedule an EXPO for a group (admin only).
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'eoffice_ruangan_id' => 'required|integer|exists:eo_mr_ruangans,id',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ]);

        $group = Group::findOrFail($request->group_id);

        if ($group->status !== 'PDC2_READY_FOR_EXPO') {
            return response()->json(['message' => 'Group must be in PDC2_READY_FOR_EXPO status.'], 400);
        }

        // Check TA eligibility
        if (! $this->eligibilityService->isEligible($group)) {
            return response()->json(['message' => 'Group does not meet Expo TA eligibility requirements.'], 400);
        }

        // Check existing EXPO schedule
        $existing = SeminarSchedule::where('group_id', $group->id)
            ->where('type', 'EXPO')
            ->where('status', '!=', 'CANCELLED')
            ->first();
        if ($existing) {
            return response()->json(['message' => 'Group already has an EXPO schedule.'], 400);
        }

        // Double-booking & room conflict check (rooms come from EOffice only).
        $ruangan = Ruangan::findOrFail($request->eoffice_ruangan_id);
        $conflicts = $this->schedulingService->validateScheduleConflicts(
            [$request->examiner_1_id, $request->examiner_2_id],
            $request->date,
            $request->start_time,
            $request->end_time,
            $ruangan->nama,
            null,
            null,
            null,
            null,
            $ruangan->id
        );

        if (! empty($conflicts)) {
            return response()->json(['message' => 'Scheduling conflicts detected.', 'conflicts' => $conflicts], 400);
        }

        $schedule = SeminarSchedule::create([
            'group_id' => $group->id,
            'type' => 'EXPO',
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $ruangan->nama,
            'eoffice_ruangan_id' => $ruangan->id,
            'examiner_1_id' => $request->examiner_1_id,
            'examiner_2_id' => $request->examiner_2_id,
            'status' => 'SCHEDULED',
        ]);

        $this->schedulingService->autoGenerateSeminarEvaluations($schedule);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'EXPO_SCHEDULED',
            'target_type' => 'SeminarSchedule',
            'target_id' => $schedule->id,
            'payload' => ['group_id' => $group->id],
        ]);

        // Send notifications
        $notificationService = app(NotificationService::class);
        $studentIds = $group->members()->with('student')->get()->pluck('student.user_id')->filter()->all();
        $notificationService->sendToMany(
            $studentIds,
            'SCHEDULE_APPROVED',
            'EXPO Scheduled',
            "Your EXPO schedule has been set for {$schedule->date} at {$schedule->start_time}.",
            'seminar_schedules',
            $schedule->id
        );
        $notificationService->sendToMany(
            Lecturer::whereIn('id', [$schedule->examiner_1_id, $schedule->examiner_2_id])->pluck('user_id')->all(),
            'SCHEDULE_APPROVED',
            'You are assigned as an examiner',
            "You have been assigned as an examiner for an EXPO on {$schedule->date} at {$schedule->start_time}.",
            'seminar_schedules',
            $schedule->id
        );

        return response()->json([
            'message' => 'EXPO scheduled.',
            'data' => $schedule->load(['examiner1', 'examiner2', 'evaluations']),
        ]);
    }

    /**
     * Submit EXPO evaluation (per-examiner, transactional).
     */
    public function evaluate(Request $request, $scheduleId)
    {
        $request->validate([
            'rubric_json' => 'required|array',
            'score' => 'required|numeric|min:0|max:100',
            'result' => 'required|in:PASS,FAIL',
        ]);

        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        $evaluation = SeminarEvaluation::where('schedule_id', $scheduleId)
            ->where('examiner_id', $lecturerId)
            ->first();

        if (! $evaluation) {
            return response()->json(['message' => 'You are not assigned as examiner for this schedule.'], 403);
        }

        try {
            $result = $this->schedulingService->submitSeminarEvaluation(
                $evaluation->id,
                $request->rubric_json,
                $request->score,
                $request->result,
                $user->id
            );

            return response()->json([
                'message' => $result['all_submitted']
                    ? "All evaluations submitted. EXPO result: {$result['result']}"
                    : ($result['updated'] ?? false
                        ? 'Evaluation updated.'
                        : 'Evaluation submitted. Waiting for other examiner.'),
                'data' => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Approve a student-submitted EXPO schedule request (admin).
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'eoffice_ruangan_id' => 'nullable|integer|exists:eo_mr_ruangans,id',
            'room' => 'nullable|string|max:255',
            'examiner_1_id' => 'required|exists:lecturers,id',
            'examiner_2_id' => 'required|exists:lecturers,id|different:examiner_1_id',
        ]);

        $schedule = SeminarSchedule::where('id', $id)
            ->where('type', 'EXPO')
            ->where('status', 'PENDING_APPROVAL')
            ->firstOrFail();

        $group = Group::findOrFail($schedule->group_id);

        // Double guard: examiner constraints
        $examinerIds = [$request->examiner_1_id, $request->examiner_2_id];
        $constraintError = $this->schedulingService->validateExaminerConstraints($group, $examinerIds);
        if ($constraintError) {
            return response()->json(['message' => $constraintError], 400);
        }

        // Conflict check (authoritative). Rooms come from EOffice only (legacy room names resolved).
        $eofficeId = $request->eoffice_ruangan_id
            ? (int) $request->eoffice_ruangan_id
            : app(EofficeAvailabilityService::class)->resolveEofficeId($request->room ?? $schedule->room);
        if (! $eofficeId) {
            return response()->json(['message' => 'Ruangan tidak dikenali di EOffice. Pilih ruangan EOffice yang valid.'], 422);
        }
        $ruangan = Ruangan::findOrFail($eofficeId);
        $conflicts = $this->schedulingService->validateScheduleConflicts(
            $examinerIds,
            $request->date,
            $request->start_time,
            $request->end_time,
            $ruangan->nama,
            $schedule->id,
            null,
            null,
            null,
            $ruangan->id
        );

        if (! empty($conflicts)) {
            return response()->json(['message' => 'Scheduling conflicts detected.', 'conflicts' => $conflicts], 400);
        }

        $schedule->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $ruangan->nama,
            'eoffice_ruangan_id' => $ruangan->id,
            'examiner_1_id' => $request->examiner_1_id,
            'examiner_2_id' => $request->examiner_2_id,
            'status' => 'SCHEDULED',
        ]);
        $this->schedulingService->autoGenerateSeminarEvaluations($schedule);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'EXPO_APPROVED',
            'target_type' => 'SeminarSchedule',
            'target_id' => $schedule->id,
            'payload' => ['group_id' => $group->id],
        ]);

        // Send notifications
        $notificationService = app(NotificationService::class);
        $studentIds = $group->members()->with('student')->get()->pluck('student.user_id')->filter()->all();
        $notificationService->sendToMany(
            $studentIds,
            'SCHEDULE_APPROVED',
            'EXPO Schedule Approved',
            "Your EXPO schedule request for {$schedule->date} at {$schedule->start_time} has been approved.",
            'seminar_schedules',
            $schedule->id
        );
        $notificationService->sendToMany(
            Lecturer::whereIn('id', [$schedule->examiner_1_id, $schedule->examiner_2_id])->pluck('user_id')->all(),
            'SCHEDULE_APPROVED',
            'You are assigned as an examiner',
            "You have been assigned as an examiner for an EXPO on {$schedule->date} at {$schedule->start_time}.",
            'seminar_schedules',
            $schedule->id
        );

        return response()->json([
            'message' => 'EXPO schedule approved.',
            'data' => $schedule->load(['examiner1', 'examiner2', 'evaluations']),
        ]);
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
