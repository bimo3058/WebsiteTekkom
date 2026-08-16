<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Schedule;
use Modules\Capstone\Support\CapstoneActor;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        // Admin can only see SEMPRO, SIDANG, EXPO schedules
        if ($role === 'admin') {
            return response()->json([
                'data' => Schedule::with('group.title.lecturer', 'group.members.student')
                    ->whereIn('type', ['SEMPRO', 'SIDANG', 'EXPO'])
                    ->orderBy('date', 'asc')->get(),
            ]);
        }

        // Dosen can only see BIMBINGAN schedules for their own groups
        if ($role === 'dosen') {
            $lecturerId = CapstoneActor::lecturer($user)->id;
            $groupIds = Group::supervisedBy($lecturerId)->pluck('id');

            return response()->json([
                'data' => Schedule::whereIn('group_id', $groupIds)
                    ->where('type', 'BIMBINGAN')
                    ->with('group.title.lecturer', 'group.members.student')
                    ->orderBy('date', 'asc')->get(),
            ]);
        }

        // Mahasiswa can only see their own group's schedule (exclude rejected groups)
        if ($role === 'mahasiswa') {
            $groupMember = GroupMember::where('student_id', CapstoneActor::student($user)->id)
                ->whereHas('group', function ($q) {
                    $q->where('status', '!=', 'REJECTED');
                })
                ->first();
            if (! $groupMember) {
                return response()->json(['data' => []]);
            }

            return response()->json([
                'data' => Schedule::where('group_id', $groupMember->group_id)
                    ->with('group.title.lecturer', 'group.members.student')
                    ->orderBy('date', 'asc')->get(),
            ]);
        }

        return response()->json(['data' => []]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'mahasiswa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Dosen can only create BIMBINGAN, Admin can only create SEMPRO/SIDANG/EXPO
        $allowedTypes = $role === 'dosen'
            ? ['BIMBINGAN']
            : ['SEMPRO', 'SIDANG', 'EXPO'];

        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'type' => ['required', 'string', 'in:'.implode(',', $allowedTypes)],
            'date' => 'required|date',
            'room' => 'required|string',
            'mode' => 'nullable|string|in:online,offline',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($role === 'dosen') {
            abort_unless(
                Group::whereKey($request->group_id)
                    ->supervisedBy(CapstoneActor::lecturer($user)->id)
                    ->exists(),
                403,
                'Anda bukan dosen pembimbing kelompok ini.'
            );
        }

        $schedule = Schedule::create($request->all());

        return response()->json(['message' => 'Schedule created successfully', 'data' => $schedule], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'mahasiswa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $allowedTypes = $role === 'dosen'
            ? ['BIMBINGAN']
            : ['SEMPRO', 'SIDANG', 'EXPO'];

        $request->validate([
            'group_id' => 'exists:capstone_groups,id',
            'type' => ['string', 'in:'.implode(',', $allowedTypes)],
            'date' => 'date',
            'room' => 'string',
            'mode' => 'nullable|string|in:online,offline',
            'notes' => 'nullable|string|max:1000',
        ]);

        $schedule = Schedule::findOrFail($id);

        if ($role === 'dosen') {
            $targetGroupId = (int) $request->input('group_id', $schedule->group_id);
            abort_unless(
                Group::whereKey($targetGroupId)
                    ->supervisedBy(CapstoneActor::lecturer($user)->id)
                    ->exists(),
                403,
                'Anda bukan dosen pembimbing kelompok ini.'
            );
        }
        $schedule->update($request->all());

        return response()->json(['message' => 'Schedule updated successfully', 'data' => $schedule]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = Auth::user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );
        if ($role === 'mahasiswa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $schedule = Schedule::findOrFail($id);
        if ($role === 'dosen') {
            abort_unless(
                Group::whereKey($schedule->group_id)
                    ->supervisedBy(CapstoneActor::lecturer($user)->id)
                    ->exists(),
                403,
                'Anda bukan dosen pembimbing kelompok ini.'
            );
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
