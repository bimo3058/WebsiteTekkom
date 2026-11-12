<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Exceptions\ScheduleConflictException;
use Modules\Capstone\Http\Requests\Admin\AssignExaminersRequest;
use Modules\Capstone\Http\Requests\Admin\StoreTaDefenseRequest;
use Modules\Capstone\Http\Requests\Admin\UpdateTaDefenseRequest;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\TaRegistration;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Services\SchedulingService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\PeriodGate;

class TaDefenseScheduleController extends Controller
{
    use ApiResponseTrait;

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
        $periodIds = PeriodGate::activeAndFinalizedIds();
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

        try {
            $schedule = $this->schedulingService->scheduleTaDefense($request->validated(), (int) $user->id);

            return $this->createdResponse([
                'message' => 'TA defense scheduled successfully',
                'data' => $schedule,
            ]);
        } catch (ScheduleConflictException $e) {
            return $this->errorResponse($e->getMessage(), 400, $e->conflicts);
        } catch (DomainRuleException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create schedule: '.$e->getMessage(), 500);
        }
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

        try {
            $schedule = $this->schedulingService->updateTaDefenseSchedule((int) $id, $validated);

            return $this->successResponse($schedule, 'Schedule updated successfully');
        } catch (ScheduleConflictException $e) {
            return $this->errorResponse($e->getMessage(), 400, $e->conflicts);
        } catch (AuthorizationException $e) {
            return $this->errorResponse($e->getMessage(), 403);
        } catch (DomainRuleException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update schedule: '.$e->getMessage(), 500);
        }
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

        try {
            $this->schedulingService->cancelTaDefenseSchedule((int) $id, (int) $user->id);

            return $this->successResponse(null, 'Schedule cancelled successfully. Students are now eligible for rescheduling.');
        } catch (DomainRuleException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
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
        // Full `students` select (not `students:id`): App\Student auto-loads
        // `user` via user_id, so a column-constrained load 500s here.
        $activeDefenseStudentIds = TaDefenseSchedule::with('students')->where(fn ($q) => $q->whereIn('student_id', $memberIds)->orWhereHas('students', fn ($q) => $q->whereIn('students.id', $memberIds)))
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
