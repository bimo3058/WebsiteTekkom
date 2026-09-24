<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\Location;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Services\EofficeAvailabilityService;
use Modules\Capstone\Support\CapstoneActor;
use Modules\EOffice\Models\Ruangan;

class LocationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of all locations.
     */
    public function index()
    {
        $locations = Location::orderBy('name')->get();

        return $this->successResponse($locations);
    }

    /**
     * Display active locations only.
     */
    public function active()
    {
        $locations = Location::active()->orderBy('name')->get();

        return $this->successResponse($locations);
    }

    /**
     * Display offline locations only.
     */
    public function offline()
    {
        $locations = Location::offline()->active()->orderBy('name')->get();

        return $this->successResponse($locations);
    }

    public function physical()
    {
        return $this->offline();
    }

    /**
     * Display online/virtual locations only.
     */
    public function online()
    {
        $locations = Location::online()->active()->orderBy('name')->get();

        return $this->successResponse($locations);
    }

    /**
     * Store a newly created location (admin only).
     */
    public function store(Request $request)
    {
        if (! in_array('admin', CapstoneActor::roles(Auth::user()), true)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:capstone_locations,name',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|string|in:online',
            'description' => 'nullable|string|max:1000',
            'eoffice_ruangan_id' => 'prohibited',
        ]);

        $location = Location::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'type' => 'online',
            'description' => $request->description,
            'eoffice_ruangan_id' => null,
            'is_active' => true,
        ]);

        return $this->createdResponse([
            'message' => 'Location created successfully',
            'data' => $location,
        ]);
    }

    /**
     * Display the specified location.
     */
    public function show($id)
    {
        $location = Location::findOrFail($id);

        return $this->successResponse($location);
    }

    /**
     * Update the specified location (admin only).
     */
    public function update(Request $request, $id)
    {
        if (! in_array('admin', CapstoneActor::roles(Auth::user()), true)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $location = Location::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:capstone_locations,name,'.$id,
            'capacity' => 'nullable|integer|min:1',
            'type' => 'sometimes|string|in:online',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'eoffice_ruangan_id' => 'prohibited',
        ]);

        $data = $request->all();
        $data['type'] = 'online';
        $data['eoffice_ruangan_id'] = null;
        $location->update($data);

        return $this->successResponse($location, 'Location updated successfully');
    }

    /**
     * Remove the specified location (admin only).
     * Constraint: Location must be inactive before deletion.
     */
    public function destroy($id)
    {
        if (! in_array('admin', CapstoneActor::roles(Auth::user()), true)) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $location = Location::findOrFail($id);

        // Constraint: Must be inactive first
        if ($location->is_active) {
            return $this->errorResponse('Location must be deactivated before it can be deleted. Please set is_active to false first.', 422);
        }

        $location->delete();

        return $this->successResponse(null, 'Location deleted successfully');
    }

    /**
     * Get available EOffice rooms for a specific date/time range.
     * EOffice is the single source of rooms; Capstone no longer keeps
     * its own offline room list.
     */
    public function available(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_schedule_id' => 'nullable|integer',
            'exclude_seminar_id' => 'nullable|integer',
            'exclude_ta_defense_id' => 'nullable|integer',
            'exclude_expo_id' => 'nullable|integer',
        ]);

        $date = $request->date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;

        $eoffice = app(EofficeAvailabilityService::class);
        $busyIds = $eoffice->busyEofficeIds($date, $startTime, $endTime);

        // Rooms taken by other Capstone schedules in the same slot.
        $capstoneBusyIds = [];
        if (Schema::hasColumn('capstone_seminar_schedules', 'eoffice_ruangan_id')) {
            $capstoneBusyIds = array_merge($capstoneBusyIds, SeminarSchedule::where('date', $date)
                ->where('status', '!=', 'CANCELLED')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($request->exclude_seminar_id, fn ($q) => $q->where('id', '!=', $request->exclude_seminar_id))
                ->whereNotNull('eoffice_ruangan_id')
                ->pluck('eoffice_ruangan_id')->all());
        }
        if (Schema::hasColumn('capstone_ta_defense_schedules', 'eoffice_ruangan_id')) {
            $capstoneBusyIds = array_merge($capstoneBusyIds, TaDefenseSchedule::where('date', $date)
                ->where('status', '!=', 'CANCELLED')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($request->exclude_ta_defense_id, fn ($q) => $q->where('id', '!=', $request->exclude_ta_defense_id))
                ->whereNotNull('eoffice_ruangan_id')
                ->pluck('eoffice_ruangan_id')->all());
        }
        if (Schema::hasColumn('capstone_expo_events', 'eoffice_ruangan_id')) {
            $capstoneBusyIds = array_merge($capstoneBusyIds, ExpoEvent::where('date', $date)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->when($request->exclude_expo_id, fn ($q) => $q->where('id', '!=', $request->exclude_expo_id))
                ->whereNotNull('eoffice_ruangan_id')
                ->pluck('eoffice_ruangan_id')->all());
        }

        $busyIds = array_values(array_unique(array_merge($busyIds, $capstoneBusyIds)));

        $rooms = Ruangan::orderBy('nama')
            ->get(['id', 'nama', 'lokasi', 'lantai', 'kapasitas'])
            ->map(fn ($room) => [
                'id' => $room->id,
                'nama' => $room->nama,
                'lokasi' => $room->lokasi,
                'lantai' => $room->lantai,
                'kapasitas' => $room->kapasitas,
                'available' => ! in_array($room->id, $busyIds),
            ]);

        return $this->envelopeResponse($rooms->where('available')->values()->all(), [
            'busy_eoffice_ids' => $busyIds,
        ]);
    }

    /**
     * List EOffice rooms (view-only single source of rooms).
     * Used by the locations page and every schedule room picker,
     * including the dosen BIMBINGAN form — hence admin + dosen.
     */
    public function eofficeRooms()
    {
        if (empty(array_intersect(['admin', 'dosen'], CapstoneActor::roles(Auth::user())))) {
            return $this->unauthorizedResponse('Unauthorized');
        }

        $today = now()->format('Y-m-d');
        $rooms = Ruangan::orderBy('nama')
            ->withCount(['peminjamans as upcoming_bookings_count' => fn ($q) => $q
                ->where('status', 'disetujui')
                ->whereDate('tanggal_pinjam', '>=', $today)])
            ->get(['id', 'nama', 'lokasi', 'lantai', 'kapasitas', 'fasilitas', 'is_active'])
            ->map(fn ($room) => [
                'id' => $room->id,
                'nama' => $room->nama,
                'lokasi' => $room->lokasi,
                'lantai' => $room->lantai,
                'kapasitas' => $room->kapasitas,
                'fasilitas' => $room->fasilitas,
                'is_active' => (bool) $room->is_active,
                'upcoming_bookings_count' => (int) $room->upcoming_bookings_count,
            ]);

        return $this->successResponse($rooms);
    }
}
