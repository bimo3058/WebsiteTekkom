<?php

namespace Modules\Capstone\Http\Controllers;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Concerns\ResolvesActivePeriods;
use Modules\Capstone\Http\Requests\Admin\AssignExaminersRequest;
use Modules\Capstone\Http\Requests\Admin\StoreTaDefenseRequest;
use Modules\Capstone\Http\Requests\Admin\UpdateTaDefenseRequest;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\TaDefenseEvaluation;
use Modules\Capstone\Models\TaDefenseExaminer;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaRegistration;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Services\SchedulingService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\EOffice\Models\Ruangan;

class TaDefenseScheduleController extends Controller
{
    use ApiResponseTrait, ResolvesActivePeriods;

    protected $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    /**
     * List all TA defense schedules for admin
     * Cross-period: Fetches schedules from all active and finalized periods by default.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (CapstoneActor::role($user) !== 'admin') {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $with = ['students', 'group.title', 'group.period', 'examiner1', 'examiner2'];
        if ($this->hasPeriodColumn()) {
            $with[] = 'period';
        }

        // Get active and finalized period IDs for cross-period fetching
        $periodIds = $this->getActiveAndFinalizedPeriodIds();
        $hasPeriodColumn = $this->hasPeriodColumn();

        $query = TaDefenseSchedule::with($with)
            ->whereHas('group', function ($q) use ($periodIds) {
                // Filter by active/finalized periods by default
                $q->whereIn('period_id', $periodIds);
            })
            ->orderBy('date', 'asc');

        // Handle period_id filter including 'all' (all removes the period filter for viewing historical data)
        if ($request->has('period_id')) {
            if ($request->period_id === 'all') {
                // Remove the whereHas constraint by requerying without it
                $query = TaDefenseSchedule::with($with)
                    ->orderBy('date', 'asc');
            } elseif ($hasPeriodColumn) {
                $query->where('period_id', $request->period_id);
            } else {
                $query->whereHas('group', function ($q) use ($request) {
                    $q->where('period_id', $request->period_id);
                });
            }
        }

        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Cancelled rows stay in the DB (audit trail) but are hidden by
            // default; pass ?status=CANCELLED explicitly to list them.
            $query->where('status', '!=', 'CANCELLED');
        }

        $schedules = $query->paginate($request->per_page ?? 1000);

        // Transform schedules to ensure students data is properly formatted
        // Build response manually to ensure all relationships are properly included
        $transformedSchedules = $schedules->getCollection()->map(function ($schedule) {
            // Ensure students relationship is loaded
            if (! $schedule->relationLoaded('students')) {
                $schedule->load('students');
            }

            // Build students array directly from relationship
            $students = $schedule->students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim,
                    'email' => $student->email,
                ];
            })->toArray();

            return [
                'id' => $schedule->id,
                'student_id' => $schedule->student_id,
                'student' => $students[0] ?? null,
                'students' => $students,
                'group_id' => $schedule->group_id,
                'group' => $schedule->group ? [
                    'id' => $schedule->group->id,
                    'name' => $schedule->group->name ?? null,
                    'code' => $schedule->group->code ?? null,
                    'title' => $schedule->group->title ? [
                        'id' => $schedule->group->title->id,
                        'title' => $schedule->group->title->title,
                    ] : null,
                    'period' => $schedule->group->period ? [
                        'id' => $schedule->group->period->id,
                        'name' => $schedule->group->period->name,
                    ] : null,
                ] : null,
                'period' => $schedule->period ?? null,
                'period_id' => $schedule->period_id ?? null,
                'date' => $schedule->date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'room' => $schedule->room,
                'location_id' => $schedule->location_id,
                'status' => $schedule->status,
                'notes' => $schedule->notes,
                'evaluation_deadline' => $schedule->evaluation_deadline,
                'examiner_1_id' => $schedule->examiner_1_id,
                'examiner_2_id' => $schedule->examiner_2_id,
                'examiner1' => $schedule->examiner1 ? ['id' => $schedule->examiner1->id, 'name' => $schedule->examiner1->name] : null,
                'examiner2' => $schedule->examiner2 ? ['id' => $schedule->examiner2->id, 'name' => $schedule->examiner2->name] : null,
            ];
        })->toArray();

        return $this->envelopeResponse($transformedSchedules, [
            'current_page' => $schedules->currentPage(),
            'last_page' => $schedules->lastPage(),
            'total' => $schedules->total(),
        ]);
    }

    /**
     * Create new TA defense schedule with multi-student support
     */
    public function store(StoreTaDefenseRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (CapstoneActor::role($user) !== 'admin') {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $request) {
            $studentIds = $validated['student_ids'];
            $group = Group::with(['supervisors', 'period', 'members'])->whereKey($validated['group_id'])->lockForUpdate()->firstOrFail();
            if (isset($validated['period_id']) && (int) $validated['period_id'] !== $group->period_id) {
                return $this->errorResponse('The selected group belongs to another period', 400);
            }

            // Validate all students are from the same group
            $groupMemberIds = $group->members->pluck('student_id')->toArray();
            $invalidStudents = array_diff($studentIds, $groupMemberIds);

            if (! empty($invalidStudents)) {
                return $this->errorResponse('All selected students must be from the same group', 400);
            }

            // Validate all students have TA_DOCUMENTS_APPROVED status
            $validSubmissions = TaSubmission::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('status', 'TA_DOCUMENTS_APPROVED')
                ->pluck('student_id')
                ->toArray();

            $invalidStatusStudents = array_diff($studentIds, $validSubmissions);
            if (! empty($invalidStatusStudents)) {
                return $this->errorResponse('All selected students must have TA_DOCUMENTS_APPROVED status', 400);
            }

            // Validate all students have an APPROVED sidang TA registration
            $approvedRegistrations = TaRegistration::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('status', TaRegistration::STATUS_APPROVED)
                ->pluck('student_id')
                ->toArray();

            $unapprovedStudents = array_diff($studentIds, $approvedRegistrations);
            if (! empty($unapprovedStudents)) {
                return $this->errorResponse('All selected students must have an approved sidang TA registration', 400);
            }

            // Check for existing scheduled defenses for these students
            $existingScheduled = TaDefenseSchedule::where(fn ($q) => $q->whereIn('student_id', $studentIds)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $studentIds)))
                ->whereIn('status', ['SCHEDULED', 'DONE'])
                ->exists();

            if ($existingScheduled) {
                return $this->errorResponse('One or more selected students already have a scheduled or completed defense', 400);
            }

            // Validate examiners are not supervisors
            $supervisorIds = array_unique(array_merge($group->supervisors->pluck('id')->all(), array_filter([$group->supervisor_1_id, $group->supervisor_2_id])));

            if (in_array($validated['examiner_1_id'], $supervisorIds)) {
                return $this->errorResponse('Examiner 1 cannot be a supervisor of this group', 400);
            }

            if (in_array($validated['examiner_2_id'], $supervisorIds)) {
                return $this->errorResponse('Examiner 2 cannot be a supervisor of this group', 400);
            }

            // Validate examiners are dosen
            $examiner1 = Lecturer::find($validated['examiner_1_id']);
            $examiner2 = Lecturer::find($validated['examiner_2_id']);

            if (! $examiner1) {
                return $this->errorResponse('Examiner 1 must be a dosen', 400);
            }

            if (! $examiner2) {
                return $this->errorResponse('Examiner 2 must be a dosen', 400);
            }

            // Validate scheduling conflicts
            $examinerIds = [$validated['examiner_1_id'], $validated['examiner_2_id']];

            // Determine room for conflict checking — single source: EOffice.
            $ruangan = Ruangan::findOrFail($validated['eoffice_ruangan_id']);
            $room = $ruangan->nama;

            $conflicts = $this->schedulingService->validateScheduleConflicts(
                $examinerIds,
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $room,
                null,
                null,
                null,
                null,
                $ruangan->id
            );

            if (! empty($conflicts)) {
                return $this->errorResponse('Scheduling conflicts detected.', 400, $conflicts);
            }

            // Auto-calculate evaluation deadline
            $evaluationDeadline = date('Y-m-d H:i:s', strtotime($validated['date'].' +2 days'));

            DB::beginTransaction();
            try {
                $payload = [
                    'group_id' => $validated['group_id'],
                    'student_id' => $studentIds[0], // Backward compatibility
                    'examiner_1_id' => $validated['examiner_1_id'],
                    'examiner_2_id' => $validated['examiner_2_id'],
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'room' => $room,
                    'eoffice_ruangan_id' => $ruangan->id,
                    'status' => 'SCHEDULED',
                    'evaluation_deadline' => $evaluationDeadline,
                    'notes' => $validated['notes'] ?? null,
                ];

                if ($this->hasPeriodColumn()) {
                    $payload['period_id'] = $validated['period_id'] ?? $group->period_id;
                }

                $schedule = TaDefenseSchedule::create($payload);

                // Attach all students to pivot table
                $schedule->students()->attach($studentIds);

                // Update or create TA submissions for all scheduled students
                foreach ($studentIds as $studentId) {
                    TaSubmission::updateOrCreate(
                        ['student_id' => $studentId, 'group_id' => $group->id],
                        [
                            'status' => 'TA_READY_FOR_SIDANG',
                            'group_id' => $group->id,
                            'period_id' => $payload['period_id'] ?? $group->period_id,
                        ]
                    );
                }

                // Create evaluation records
                $this->schedulingService->createTaDefenseEvaluations($schedule, $studentIds);

                // Reload schedule with students relationship for notification
                $schedule->load('students');

                // Notify all students and examiners
                $this->notifyTaDefenseScheduled($schedule, $studentIds);

                DB::commit();

                return $this->createdResponse([
                    'message' => 'TA defense scheduled successfully',
                    'data' => $schedule->load(['students', 'group.title', 'group.period', 'examiner1', 'examiner2']),
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Failed to create TA defense schedule', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                ]);

                return $this->errorResponse('Failed to create schedule: '.$e->getMessage(), 500);
            }
        });
    }

    /**
     * Get TA defense schedule details
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $with = ['students', 'group.title', 'group.period', 'examiner1', 'examiner2', 'evaluations'];
        if ($this->hasPeriodColumn()) {
            $with[] = 'period';
        }

        $schedule = TaDefenseSchedule::with($with)
            ->findOrFail($id);

        // Check authorization - allow if user is admin, one of the students, examiner, or supervisor
        $studentIds = $schedule->students->pluck('id')->toArray();

        $isAdmin = CapstoneActor::role($user) === 'admin';
        $studentId = $user->student?->id;
        $lecturerId = $user->lecturer?->id;
        if (! $isAdmin &&
            ! in_array($studentId, $studentIds, true) &&
            $lecturerId !== $schedule->examiner_1_id &&
            $lecturerId !== $schedule->examiner_2_id &&
            ! $schedule->group->supervisors->contains('id', $lecturerId)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        return $this->successResponse($schedule);
    }

    /**
     * Update TA defense schedule with multi-student support
     */
    public function update(UpdateTaDefenseRequest $request, $id): JsonResponse
    {
        return $this->updateSchedule($request, $request->validated(), $id);
    }

    private function updateSchedule(Request $request, array $validated, $id): JsonResponse
    {
        if (CapstoneActor::role($request->user()) !== 'admin') {
            return $this->unauthorizedResponse('Unauthorized');
        }
        if (($validated['status'] ?? null) === 'CANCELLED') {
            return $this->cancel($id);
        }

        return DB::transaction(function () use ($validated, $id) {
            $initial = TaDefenseSchedule::findOrFail($id);
            $group = Group::with(['members', 'supervisors'])->whereKey($initial->group_id)->lockForUpdate()->firstOrFail();
            $schedule = TaDefenseSchedule::with('students')->whereKey($id)->lockForUpdate()->firstOrFail();
            if ($schedule->status !== 'SCHEDULED' && ! in_array($schedule->status, ['PENDING', 'PENDING_APPROVAL'], true)) {
                return $this->errorResponse('Cannot update a completed or cancelled schedule', 400);
            }
            $currentIds = $schedule->students->pluck('id')->all() ?: array_filter([$schedule->student_id]);
            $studentIds = array_values(array_unique(array_map('intval', $validated['student_ids'] ?? $currentIds)));
            if (array_diff($currentIds, $studentIds)) {
                return $this->errorResponse('Already selected students cannot be removed', 400);
            }
            if (array_diff($studentIds, $group->members->pluck('student_id')->all())) {
                return $this->errorResponse('All selected students must be from the same group', 400);
            }
            $added = array_values(array_diff($studentIds, $currentIds));
            $ready = TaSubmission::where('group_id', $group->id)->whereIn('student_id', $added)->where('status', 'TA_DOCUMENTS_APPROVED')->pluck('student_id')->all();
            if (array_diff($added, $ready)) {
                return $this->errorResponse('All selected students must have TA_DOCUMENTS_APPROVED status', 400);
            }
            $registered = TaRegistration::where('group_id', $group->id)->whereIn('student_id', $added)->where('status', TaRegistration::STATUS_APPROVED)->pluck('student_id')->all();
            if (array_diff($added, $registered)) {
                return $this->errorResponse('All selected students must have an approved sidang TA registration', 400);
            }
            $hasOtherDefense = TaDefenseSchedule::whereKeyNot($id)->whereIn('status', ['SCHEDULED', 'DONE', 'COMPLETED'])
                ->where(fn ($q) => $q->whereIn('student_id', $added)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $added)))->exists();
            if ($hasOtherDefense) {
                return $this->errorResponse('A selected student already has an active defense', 400);
            }
            $examinerIds = [(int) ($validated['examiner_1_id'] ?? $schedule->examiner_1_id), (int) ($validated['examiner_2_id'] ?? $schedule->examiner_2_id)];
            $supervisorIds = array_unique(array_merge($group->supervisors->pluck('id')->all(), array_filter([$group->supervisor_1_id, $group->supervisor_2_id])));
            if ($examinerIds[0] === $examinerIds[1] || count(array_filter($examinerIds)) !== 2) {
                return $this->errorResponse('Choose two different examiners', 400);
            }
            if (array_intersect($examinerIds, $supervisorIds)) {
                return $this->errorResponse('Examiners cannot be supervisors of this group', 400);
            }
            $examinersChanged = $examinerIds !== [(int) $schedule->examiner_1_id, (int) $schedule->examiner_2_id];
            if (($examinersChanged || $added) && TaDefenseEvaluation::where('schedule_id', $id)->where('status', '!=', 'PENDING')->exists()) {
                return $this->errorResponse('Examiners and students are locked after evaluation submission', 403);
            }
            $payload = collect($validated)->only(['date', 'start_time', 'end_time', 'eoffice_ruangan_id', 'notes', 'status', 'examiner_1_id', 'examiner_2_id'])->all();
            unset($payload['room'], $payload['location_id']);
            if (! empty($payload['eoffice_ruangan_id'])) {
                $payload['room'] = Ruangan::findOrFail($payload['eoffice_ruangan_id'])->nama;
            }
            $eofficeId = isset($payload['eoffice_ruangan_id']) ? (int) $payload['eoffice_ruangan_id'] : ($schedule->getAttributes()['eoffice_ruangan_id'] ?? null);
            $conflicts = $this->schedulingService->validateScheduleConflicts($examinerIds, $payload['date'] ?? $schedule->date->format('Y-m-d'), $payload['start_time'] ?? $schedule->start_time, $payload['end_time'] ?? $schedule->end_time, $payload['room'] ?? $schedule->room, null, $schedule->id, null, ($schedule->getAttributes()['eoffice_peminjaman_id'] ?? null), $eofficeId ? (int) $eofficeId : null);
            if ($conflicts) {
                return $this->errorResponse('Scheduling conflicts detected.', 400, $conflicts);
            }
            $schedule->update($payload);
            if ($added) {
                $schedule->students()->syncWithoutDetaching($added);
                TaSubmission::where('group_id', $group->id)->whereIn('student_id', $added)->update(['status' => 'TA_READY_FOR_SIDANG']);
            }
            if ($examinersChanged) {
                TaDefenseEvaluation::where('schedule_id', $id)->where('status', 'PENDING')->delete();
                TaDefenseExaminer::where('schedule_id', $id)->delete();
                $this->schedulingService->createTaDefenseEvaluations($schedule->fresh(), $studentIds);
            }
            $this->notifyDefenseUpdated($schedule, $studentIds);

            return $this->successResponse($schedule->fresh()->load(['students', 'group.title', 'group.period', 'examiner1', 'examiner2']), 'Schedule updated successfully');
        });
    }

    public function mySchedule(): JsonResponse
    {
        $user = Auth::user();

        if (! in_array('mahasiswa', CapstoneActor::roles($user), true)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $schedules = TaDefenseSchedule::with(['examiner1', 'examiner2', 'students'])
            ->where(function ($query) use ($user) {
                $studentId = CapstoneActor::student($user)->id;
                $query->where('student_id', $studentId)->orWhereHas('students', fn ($q) => $q->where('students.id', $studentId));
            })
            ->whereIn('status', ['SCHEDULED', 'DONE'])
            ->orderBy('date', 'desc')
            ->get();

        return $this->successResponse($schedules);
    }

    /**
     * Get schedules for examiner
     */
    public function examinerSchedules(): JsonResponse
    {
        $user = Auth::user();

        if (! in_array('dosen', CapstoneActor::roles($user), true)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $schedules = TaDefenseSchedule::with(['students', 'group'])
            ->where(function ($query) use ($user) {
                $lecturerId = CapstoneActor::lecturer($user)->id;
                $query->where('examiner_1_id', $lecturerId)
                    ->orWhere('examiner_2_id', $lecturerId);
            })
            ->whereIn('status', ['SCHEDULED', 'DONE'])
            ->orderBy('date', 'asc')
            ->get();

        // Transform schedules to ensure students data is properly formatted
        $transformedSchedules = $schedules->map(function ($schedule) {
            $data = $schedule->toArray();

            // Ensure students array is properly formatted
            if (isset($data['students']) && is_array($data['students'])) {
                $data['students'] = collect($data['students'])->map(function ($student) {
                    return [
                        'id' => $student['id'] ?? null,
                        'name' => $student['name'] ?? 'Unknown',
                        'nim' => $student['nim'] ?? null,
                        'email' => $student['email'] ?? null,
                    ];
                })->toArray();
            } else {
                $data['students'] = [];
            }

            // Add single student property for backward compatibility (first student)
            $data['student'] = $data['students'][0] ?? null;

            return $data;
        })->toArray();

        return $this->successResponse($transformedSchedules);
    }

    /**
     * Cancel TA defense schedule
     */
    public function cancel($id): JsonResponse
    {
        $user = Auth::user();

        if (CapstoneActor::role($user) !== 'admin') {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $schedule = TaDefenseSchedule::with(['students', 'group'])->findOrFail($id);

        if ($schedule->status === 'DONE') {
            return $this->errorResponse('Cannot cancel completed schedule', 400);
        }

        $studentIds = $schedule->students->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            $schedule->update(['status' => 'CANCELLED']);

            // Revert status for all students
            TaSubmission::whereIn('student_id', $studentIds)
                ->where('status', 'TA_READY_FOR_SIDANG')
                ->update(['status' => 'TA_DOCUMENTS_APPROVED']);

            // Clean up pending evaluations
            TaDefenseEvaluation::where('schedule_id', $schedule->id)
                ->where('status', 'PENDING')
                ->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'TA_DEFENSE_CANCELLED',
                'target_type' => 'TaDefenseSchedule',
                'target_id' => $schedule->id,
                'payload' => ['group_id' => $schedule->group_id],
            ]);

            // Notify all students of cancellation
            $this->notifyDefenseCancelled($schedule, $studentIds);

            DB::commit();

            return $this->successResponse(null, 'Schedule cancelled successfully. Students are now eligible for rescheduling.');

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Failed to cancel schedule: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get students eligible for TA defense scheduling.
     * Returns ALL students with readiness status and lock information.
     */
    public function eligibleStudents(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (CapstoneActor::role($user) !== 'admin') {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $periodId = $request->input('period_id');
        $currentScheduleId = $request->input('current_schedule_id'); // For edit mode

        // Get groups with ALL their members
        $groupQuery = Group::with(['members.student', 'supervisors', 'supervisor1', 'supervisor2'])
            ->whereHas('members');

        // Handle period_id filter including 'all'
        if ($periodId && $periodId !== 'all') {
            $groupQuery->where('period_id', $periodId);
        }

        if ($request->has('group_id')) {
            $groupQuery->where('id', $request->group_id);
        }

        $groups = $groupQuery->get();

        // Get all submissions for these groups' members
        $memberIds = $groups->flatMap(function ($group) {
            return $group->members->pluck('student_id');
        })->unique();

        $submissions = TaSubmission::whereIn('student_id', $memberIds)
            ->latest('id')->get()->unique(fn ($submission) => $submission->student_id.':'.$submission->group_id)
            ->keyBy(fn ($submission) => $submission->student_id.':'.$submission->group_id);

        // Get students already in active defenses (SCHEDULED or DONE)
        $activeDefenseStudentIds = TaDefenseSchedule::with('students:id')->where(fn ($q) => $q->whereIn('student_id', $memberIds)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $memberIds)))
            ->whereIn('status', ['SCHEDULED', 'DONE'])
            ->when($currentScheduleId, function ($q) use ($currentScheduleId) {
                // Exclude current schedule when editing
                $q->where('id', '!=', $currentScheduleId);
            })
            ->get()->flatMap(fn ($schedule) => $schedule->students->pluck('id')->push($schedule->student_id))->filter()->unique()->values()->all();

        // Get students in current schedule (for edit mode)
        $currentScheduleStudentIds = [];
        if ($currentScheduleId) {
            $currentScheduleStudentIds = TaDefenseSchedule::find($currentScheduleId)
                ?->students()
                ?->pluck('students.id')
                ?->toArray() ?? [];
        }

        $approvedRegistrationIds = TaRegistration::whereIn('student_id', $memberIds)
            ->where('status', TaRegistration::STATUS_APPROVED)
            ->pluck('student_id')
            ->unique()
            ->values()
            ->all();

        // Transform groups with ALL members
        $result = $groups->map(function ($group) use ($submissions, $activeDefenseStudentIds, $currentScheduleStudentIds, $approvedRegistrationIds) {
            return [
                'id' => $group->id,
                'name' => $group->code ?? 'Group #'.$group->id,
                'code' => $group->code ?? null,
                'supervisors' => $group->supervisors->concat([$group->supervisor1, $group->supervisor2])->filter()->unique('id')->values()->map(function ($sv) {
                    return [
                        'id' => $sv->id,
                        'name' => $sv->name,
                        'pivot' => [
                            'role' => $sv->pivot->role ?? null,
                        ],
                    ];
                }),
                'members' => $group->members->map(function ($member) use ($group, $submissions, $activeDefenseStudentIds, $currentScheduleStudentIds, $approvedRegistrationIds) {
                    $studentId = $member->student_id;
                    $submission = $submissions->get($studentId.':'.$group->id);
                    $hasActiveDefense = in_array($studentId, $activeDefenseStudentIds);
                    $isInCurrentSchedule = in_array($studentId, $currentScheduleStudentIds);

                    // Student is ready if:
                    // 1. Has TA_DOCUMENTS_APPROVED status
                    // 2. Has an APPROVED sidang TA registration
                    // 3. Is NOT already in another active defense
                    // 4. OR is already in current schedule (edit mode)
                    $isReadyForSidang = ($submission && $submission->status === 'TA_DOCUMENTS_APPROVED' && in_array($studentId, $approvedRegistrationIds) && ! $hasActiveDefense) || $isInCurrentSchedule;

                    return [
                        'student' => [
                            'id' => $member->student->id,
                            'name' => $member->student->name,
                            'nim' => $member->student->nim ?? null,
                        ],
                        'is_leader' => $member->is_leader,
                        'is_ready_for_sidang' => $isReadyForSidang,
                        'is_already_selected' => $isInCurrentSchedule,
                        'status_text' => $submission ? $submission->status : 'NO_SUBMISSION',
                        'has_active_defense' => $hasActiveDefense,
                    ];
                })->values(),
            ];
        })->values();

        return $this->successResponse($result);
    }

    /**
     * Notify students and examiners when defense is scheduled
     */
    private function notifyTaDefenseScheduled($schedule, array $studentIds): void
    {
        $studentNames = $schedule->students->pluck('name')->join(', ');

        // Notify all students
        foreach ($studentIds as $studentId) {
            Notification::create([
                'user_id' => Student::find($studentId)?->user_id,
                'type' => 'TA_DEFENSE_SCHEDULED',
                'title' => 'Jadwal Sidang TA',
                'message' => "Anda dijadwalkan sidang TA pada {$schedule->date} pukul {$schedule->start_time} di {$schedule->room}",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }

        // Notify examiners
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            Notification::create([
                'user_id' => Lecturer::find($examinerId)?->user_id,
                'type' => 'TA_DEFENSE_EXAMINER_ASSIGNED',
                'title' => 'Penugasan Penguji Sidang TA',
                'message' => "Anda ditugaskan sebagai penguji sidang TA untuk mahasiswa: {$studentNames}",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify students when they are added to an existing defense
     */
    private function notifyStudentsAddedToDefense($schedule, array $addedStudentIds): void
    {
        foreach ($addedStudentIds as $studentId) {
            Notification::create([
                'user_id' => Student::find($studentId)?->user_id,
                'type' => 'TA_DEFENSE_SCHEDULED',
                'title' => 'Ditambahkan ke Jadwal Sidang TA',
                'message' => "Anda ditambahkan ke jadwal sidang TA pada {$schedule->date} pukul {$schedule->start_time} di {$schedule->room}",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all students when defense is updated
     */
    private function notifyDefenseUpdated($schedule, array $studentIds): void
    {
        foreach ($studentIds as $studentId) {
            Notification::create([
                'user_id' => Student::find($studentId)?->user_id,
                'type' => 'TA_DEFENSE_UPDATED',
                'title' => 'Jadwal Sidang TA Diperbarui',
                'message' => "Jadwal sidang TA Anda telah diperbarui. Tanggal: {$schedule->date}, Waktu: {$schedule->start_time}, Ruang: {$schedule->room}",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }

        // Notify examiners
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            Notification::create([
                'user_id' => Lecturer::find($examinerId)?->user_id,
                'type' => 'TA_DEFENSE_UPDATED',
                'title' => 'Jadwal Sidang TA Diperbarui',
                'message' => "Jadwal sidang TA yang Anda uji telah diperbarui. Tanggal: {$schedule->date}, Waktu: {$schedule->start_time}, Ruang: {$schedule->room}",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify all students when defense is cancelled
     */
    private function notifyDefenseCancelled($schedule, array $studentIds): void
    {
        foreach ($studentIds as $studentId) {
            Notification::create([
                'user_id' => Student::find($studentId)?->user_id,
                'type' => 'TA_DEFENSE_CANCELLED',
                'title' => 'Jadwal Sidang TA Dibatalkan',
                'message' => "Jadwal sidang TA Anda pada {$schedule->date} telah dibatalkan. Anda dapat menjadwalkan ulang.",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }

        // Notify examiners
        foreach ([$schedule->examiner_1_id, $schedule->examiner_2_id] as $examinerId) {
            Notification::create([
                'user_id' => Lecturer::find($examinerId)?->user_id,
                'type' => 'TA_DEFENSE_CANCELLED',
                'title' => 'Jadwal Sidang TA Dibatalkan',
                'message' => "Jadwal sidang TA yang Anda uji pada {$schedule->date} telah dibatalkan. Anda dapat mengabaikan penugasan ini.",
                'related_type' => 'TaDefenseSchedule',
                'related_id' => $schedule->id,
                'is_read' => false,
            ]);
        }
    }

    private function hasPeriodColumn(): bool
    {
        return Schema::hasColumn('capstone_ta_defense_schedules', 'period_id');
    }

    /**
     * Assign examiners to an existing TA defense schedule (admin only).
     * Validates that examiners are not supervisors and checks for conflicts.
     */
    public function assignExaminers(AssignExaminersRequest $request, $id): JsonResponse
    {
        return $this->updateSchedule($request, $request->validated(), $id);
    }
}
