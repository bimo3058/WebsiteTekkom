<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\Evaluation;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Capstone\Support\CapstoneActor;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('dosen')) {
            $lecturerId = CapstoneActor::lecturer($user)->id;
            // Return evaluations made by this dosen? Or for groups supervised?
            // Since evaluations are usually per student per phase
            // Let's allow filtering by group_id
            if ($request->has('group_id')) {
                $allowed = Group::whereKey($request->group_id)
                    ->whereHas('supervisions', fn ($query) => $query->where('supervisor_id', $lecturerId))
                    ->exists();
                abort_unless($allowed, 403, 'Anda bukan dosen pembimbing kelompok ini.');
                return response()->json(['data' => Evaluation::where('group_id', $request->group_id)->where('evaluator_id', $lecturerId)->with('student')->get()]);
            }
            return response()->json(['data' => []]);
        }

        if ($user->hasRole('mahasiswa')) {
            return response()->json(['data' => Evaluation::where('student_id', CapstoneActor::student($user)->id)->get()]);
        }

        return response()->json(['data' => []]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lecturerId = CapstoneActor::lecturer(Auth::user())->id;

        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
            'student_id' => 'required|exists:students,id',
            'type' => ['required', Rule::in(['bimbingan', 'proposal', 'skripsi'])], // Using lowercase to match migration or enum? Migration said string.
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        abort_unless(
            Group::whereKey($request->group_id)
                ->whereHas('supervisions', fn ($query) => $query->where('supervisor_id', $lecturerId))
                ->whereHas('members', fn ($query) => $query->where('student_id', $request->student_id))
                ->exists(),
            403,
            'Mahasiswa tidak berada pada kelompok bimbingan Anda.'
        );

        $evaluation = Evaluation::updateOrCreate(
            [
                'group_id' => $request->group_id,
                'student_id' => $request->student_id,
                'type' => $request->type,
            ],
            [
                'evaluator_id' => $lecturerId,
                'score' => $request->score,
                'feedback' => $request->feedback,
            ]
        );

        return response()->json(['message' => 'Evaluation saved', 'data' => $evaluation]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
