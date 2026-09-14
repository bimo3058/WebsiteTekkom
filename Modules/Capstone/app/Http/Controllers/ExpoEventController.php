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
            ->withCount('registrations');

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
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:200',
            'is_published' => 'boolean',
        ]);

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
            'room' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1|max:200',
        ]);

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
            ->withCount('registrations')
            ->orderBy('date')
            ->get();

        // Append registration status for this group
        $events->each(function ($event) use ($group) {
            $event->is_registered = $event->registrations()
                ->where('group_id', $group->id)
                ->exists();
        });

        return response()->json($events);
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
