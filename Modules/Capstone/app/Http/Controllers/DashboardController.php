<?php
namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\Title;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Document;

class DashboardController extends Controller
{
    public function admin()
    {
        return response()->json([
            'total_users'     => User::count(),
            'total_students'  => Student::count(),
            'total_lecturers' => Lecturer::count(),
            'active_periods'  => Period::where('is_active', true)->get(),
        ]);
    }

    public function dosen()
    {
        $user     = Auth::user();
        $lecturer = $user->lecturer;

        if (!$lecturer) {
            return response()->json(['message' => 'Data dosen tidak ditemukan.'], 403);
        }

        $titles = Title::where('lecturer_id', $lecturer->id)->get();

        $activeGroups = Group::whereIn('title_id', $titles->pluck('id'))
            ->where('status', 'APPROVED')
            ->count();

        $pendingProposals = Title::where('proposed_supervisor_id', $lecturer->id)
            ->where('title_source', 'STUDENT')
            ->where('supervisor_approval_status', 'PENDING')
            ->count();

        return response()->json([
            'total_titles'     => $titles->count(),
            'active_groups'    => $activeGroups,
            'pending_bimbingan' => 0,
            'pending_proposals' => $pendingProposals,
        ]);
    }

    public function mahasiswa()
    {
        $user    = Auth::user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 403);
        }

        $groupMember = GroupMember::with(['group.title', 'group.period'])
            ->where('student_id', $student->id)
            ->whereHas('group', fn($q) => $q->where('status', '!=', 'REJECTED'))
            ->first();

        $group = $groupMember?->group;

        $documents = $group
            ? Document::where('group_id', $group->id)->get()
            : collect([]);

        $phases = ['PDC1', 'SEMPRO', 'PDC2', 'TA', 'SIDANG', 'EXPO'];
        $steps  = [];
        foreach ($phases as $phase) {
            $steps[$phase] = $documents
                ->where('phase', $phase)
                ->where('status', 'APPROVED')
                ->isNotEmpty();
        }

        $isGraduated = $group
            && $group->status === 'APPROVED'
            && collect($steps)->every(fn($v) => $v === true);

        $pendingProposal = null;
        if ($group) {
            $pendingProposal = Title::where('proposed_by_group_id', $group->id)
                ->where('title_source', 'STUDENT')
                ->whereIn('supervisor_approval_status', ['PENDING', 'REJECTED'])
                ->with('proposedSupervisor')
                ->latest()
                ->first();
        }

        return response()->json([
            'has_group'        => (bool) $group,
            'group_status'     => $group?->status,
            'title'            => $group?->title?->title,
            'group_period'     => $group?->period,
            'active_periods'   => Period::where('is_active', true)->get(),
            'steps'            => $steps,
            'is_graduated'     => $isGraduated,
            'pending_proposal' => $pendingProposal,
        ]);
    }
}