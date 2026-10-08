<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Services\ExpoService;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;

class ExpoEventController extends Controller
{
    protected ExpoService $expoService;

    public function __construct(ExpoService $expoService)
    {
        $this->expoService = $expoService;
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Admin CRUD
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function index(Request $request)
    {
        $query = ExpoEvent::with(['period', 'creator'])
            ->withCount(['registrations' => fn ($query) => $query->where('status', 'REGISTERED')]);

        if ($request->has('period_id')) {
            $query->where('period_id', $request->period_id);
        }

        return response()->json($query->orderBy('date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'eoffice_ruangan_id' => 'required|integer|exists:eo_mr_ruangans,id',
            'capacity' => 'required|integer|min:1|max:200',
            'is_published' => 'boolean',
        ]);

        // Rooms come from EOffice only; `room` is a display snapshot.
        $ruangan = \Modules\EOffice\Models\Ruangan::findOrFail($validated['eoffice_ruangan_id']);
        $conflict = app(\Modules\Capstone\Services\EofficeAvailabilityService::class)->checkByEofficeId(
            $ruangan->id, $validated['date'], $validated['start_time'], $validated['end_time']
        );
        if ($conflict) {
            return response()->json(['message' => $conflict['message']], 422);
        }

        $validated['room'] = $ruangan->nama;
        $validated['created_by'] = $request->user()->id;

        $event = ExpoEvent::create($validated);

        return response()->json($event->load(['period', 'creator']), 201);
    }

    public function show(ExpoEvent $expoEvent)
    {
        return response()->json(
            $expoEvent->load(['period', 'creator', 'registrations.group.members.student'])
        );
    }

    public function update(Request $request, ExpoEvent $expoEvent)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'eoffice_ruangan_id' => 'sometimes|integer|exists:eo_mr_ruangans,id',
            'capacity' => 'sometimes|integer|min:1|max:200',
        ]);

        unset($validated['room']);
        $eofficeId = $validated['eoffice_ruangan_id'] ?? $expoEvent->getAttributes()['eoffice_ruangan_id'] ?? null;
        if (! empty($validated['eoffice_ruangan_id'])) {
            $validated['room'] = \Modules\EOffice\Models\Ruangan::findOrFail($validated['eoffice_ruangan_id'])->nama;
        }
        if ($eofficeId) {
            $conflict = app(\Modules\Capstone\Services\EofficeAvailabilityService::class)->checkByEofficeId(
                (int) $eofficeId,
                $validated['date'] ?? $expoEvent->date->format('Y-m-d'),
                $validated['start_time'] ?? $expoEvent->start_time,
                $validated['end_time'] ?? $expoEvent->end_time,
                $expoEvent->getAttributes()['eoffice_peminjaman_id'] ?? null
            );
            if ($conflict) {
                return response()->json(['message' => $conflict['message']], 422);
            }
        }

        $expoEvent->update($validated);

        return response()->json($expoEvent->fresh()->load(['period', 'creator']));
    }

    public function destroy(ExpoEvent $expoEvent)
    {
        if ($expoEvent->registrations()->exists()) {
            return response()->json(['message' => 'Cannot delete event with active registrations.'], 400);
        }

        $expoEvent->delete(); // soft delete
        return response()->json(['message' => 'Event deleted.']);
    }

    /**
     * Toggle published status.
     */
    public function publish(ExpoEvent $expoEvent)
    {
        $expoEvent->update(['is_published' => !$expoEvent->is_published]);

        return response()->json([
            'message' => $expoEvent->is_published ? 'Event published.' : 'Event unpublished.',
            'data' => $expoEvent->fresh(),
        ]);
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Mahasiswa: View + Register
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    /**
     * List published expo events for the student's period.
     */
    public function studentEvents(Request $request)
    {
        $user = $request->user();
        $group = GroupMember::where('student_id', CapstoneActor::student($user)->id)
            ->first()?->group;

        if (!$group) {
            return response()->json([]);
        }

        $events = ExpoEvent::where('period_id', $group->period_id)
            ->where('is_published', true)
            ->withCount(['registrations'=>fn($q)=>$q->where('status','REGISTERED')])
            ->withExists(['registrations as is_registered'=>fn($q)=>$q->where('status','REGISTERED')->where('group_id',$group->id)])
            ->orderBy('date')
            ->get();

        $hasDraft=\Modules\Capstone\Models\TaSubmission::where('group_id',$group->id)->exists();
        $events->each(function ($event) use ($group,$hasDraft) {
            $event->registration_reason = $group->status !== 'PDC2_READY_FOR_EXPO' ? 'Group must be ready for Expo.' : (!$hasDraft ? 'At least 1 member must submit a TA draft.' : null);
            $event->can_register = !$event->is_registered && !$event->registration_reason && $event->registrations_count < $event->capacity;
        });

        return response()->json($events);
    }

    public function withdraw(Request $request, ExpoEvent $expoEvent)
    {
        $member=GroupMember::where('student_id',CapstoneActor::student($request->user())->id)->firstOrFail();
        try {
            $this->expoService->withdrawGroupFromEvent($expoEvent->id,$member->group_id,$request->user()->id);
            return response()->json(['message'=>'Successfully withdrawn from expo.']);
        } catch (\InvalidArgumentException $e) {return response()->json(['message'=>$e->getMessage()],403);}
    }

    /**
     * Register the student's group for an expo event.
     */
    public function register(Request $request, ExpoEvent $expoEvent)
    {
        $user = $request->user();
        $groupMember = GroupMember::where('student_id', CapstoneActor::student($user)->id)->first();

        if (!$groupMember) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        try {
            $registration = $this->expoService->registerGroupToEvent(
                $expoEvent->id,
                $groupMember->group_id,
                $user->id
            );

            return response()->json([
                'message' => 'Successfully registered for expo event.',
                'data' => $registration,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
