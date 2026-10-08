<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Bid;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Models\Title;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        // Keep the same permission boundary as /admin/groups. Dashboard cards
        // need counts and five labels, not every member and supervisor profile.
        $canViewGroups = $request->user()->can('capstone.groups.view');
        $periodId = $request->input('period_id');
        $scopedPeriod = $periodId && $periodId !== 'all' ? (int) $periodId : null;

        $groupCounts = $canViewGroups
            ? Group::query()
                ->when($scopedPeriod, fn ($query) => $query->where('period_id', $scopedPeriod))
                ->selectRaw('COUNT(*) AS total')
                ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) AS pending', ['READY_FOR_FINALIZATION'])
                ->first()
            : null;

        $pendingFinalization = (int) ($groupCounts?->pending ?? 0);
        $pendingTitles = Title::where('title_source', 'STUDENT')
            ->where('supervisor_approval_status', 'PENDING')
            ->when($scopedPeriod, fn ($query) => $query->whereHas('proposedByGroup', fn ($groups) => $groups->where('period_id', $scopedPeriod)))
            ->count();
        $pendingDocuments = Document::where('status', 'SUBMITTED')
            ->when($scopedPeriod, fn ($query) => $query->whereHas('group', fn ($groups) => $groups->where('period_id', $scopedPeriod)))
            ->count();
        $pendingJoinRequests = PeriodRegistration::where('status', PeriodRegistration::STATUS_PENDING)
            ->when($scopedPeriod, fn ($query) => $query->where('period_id', $scopedPeriod))
            ->count();

        $activePeriods = Period::where('is_active', true)->get();

        return response()->json([
            'total_users' => User::count(),
            'total_students' => Student::count(),
            'total_lecturers' => Lecturer::count(),
            'active_periods' => $activePeriods,
            'active_periods_count' => $activePeriods->count(),
            'periods' => Period::orderByDesc('created_at')->get(['id', 'name', 'is_active']),
            'total_periods' => Period::count(),
            'total_groups' => (int) ($groupCounts?->total ?? 0),
            'pending_finalization' => $pendingFinalization,
            'pending_title_approvals' => $pendingTitles,
            'pending_documents' => $pendingDocuments,
            'pending_join_requests' => $pendingJoinRequests,
            'pending_approval' => $pendingFinalization + $pendingTitles + $pendingDocuments + $pendingJoinRequests,
            'pending_breakdown' => [
                'finalization' => $pendingFinalization,
                'titles' => $pendingTitles,
                'documents' => $pendingDocuments,
                'join_requests' => $pendingJoinRequests,
            ],
            'selected_period_id' => $periodId ?: 'all',
            'recent_groups' => $canViewGroups
                ? Group::query()->latest()->orderByDesc('id')->limit(5)->get(['id', 'code', 'status'])
                : [],
        ]);
    }

    public function dosen(Request $request)
    {
        $user = Auth::user();
        $lecturer = $user->lecturer;

        if (! $lecturer) {
            return response()->json(['message' => 'Data dosen tidak ditemukan.'], 403);
        }

        $totalTitles = Title::where('lecturer_id', $lecturer->id)->count();

        $periodId = $request->input('period_id');
        $activeGroups = Group::supervisedBy($lecturer->id)
            ->whereNotIn('status', ['FORMING', 'CLOSED', 'DISSOLVED', 'REJECTED'])
            ->when($periodId && $periodId !== 'all', fn ($query) => $query->where('period_id', $periodId))
            ->count();

        $pendingProposals = Title::where('proposed_supervisor_id', $lecturer->id)
            ->where('title_source', 'STUDENT')
            ->where('supervisor_approval_status', 'PENDING')
            ->count();

        $pendingBids = Bid::whereHas('title', fn ($query) => $query->where('lecturer_id', $lecturer->id))
            ->whereNull('lecturer_recommendation')
            ->when($periodId && $periodId !== 'all', fn ($query) => $query->whereHas('group', fn ($groups) => $groups->where('period_id', $periodId)))
            ->count();

        $upcomingSeminars = SeminarSchedule::where('date', '>=', today())
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->where(fn ($query) => $query
                ->whereHas('group', fn ($groups) => $groups->supervisedBy($lecturer->id))
                ->orWhere('examiner_1_id', $lecturer->id)->orWhere('examiner_2_id', $lecturer->id))
            ->when($periodId && $periodId !== 'all', fn ($query) => $query->whereHas('group', fn ($groups) => $groups->where('period_id', $periodId)))
            ->count();

        $upcomingDefenses = TaDefenseSchedule::where('date', '>=', today())
            ->whereNotIn('status', ['CANCELLED', 'REJECTED'])
            ->where(fn ($query) => $query
                ->whereHas('group', fn ($groups) => $groups->supervisedBy($lecturer->id))
                ->orWhere('examiner_1_id', $lecturer->id)->orWhere('examiner_2_id', $lecturer->id)
                ->orWhereHas('examiners', fn ($examiners) => $examiners->where('examiner_id', $lecturer->id)))
            ->when($periodId && $periodId !== 'all', fn ($query) => $query->where('period_id', $periodId))
            ->count();

        $titles = Title::where('lecturer_id', $lecturer->id)
            ->when($periodId && $periodId !== 'all', fn ($query) => $query->where('period_id', $periodId))
            ->get(['id', 'quota']);
        $titlesAvailable = 0;
        foreach ($titles as $title) {
            $allocations = Group::where('title_id', $title->id)
                ->whereNotIn('status', ['FORMING', 'READY_FOR_BIDDING', 'CLOSED'])
                ->count();
            if ($title->quota - $allocations > 0) {
                $titlesAvailable++;
            }
        }

        $activitySeries = AuditLog::where('user_id', $user->id)
            ->where('created_at', '>=', today()->subDays(30))
            ->selectRaw('DATE(created_at) AS day, COUNT(*) AS count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get();

        return response()->json([
            'total_titles' => $totalTitles,
            'active_groups' => $activeGroups,
            'pending_bimbingan' => 0,
            'pending_proposals' => $pendingProposals,
            'pending_bids' => $pendingBids,
            'upcoming_schedules' => $upcomingSeminars + $upcomingDefenses,
            'titles_available' => $titlesAvailable,
            'titles_full' => $titles->count() - $titlesAvailable,
            'activity_series' => $activitySeries,
            'available_periods' => Period::orderByDesc('created_at')->get(['id', 'name', 'is_active']),
            'selected_period_id' => $periodId ?: 'all',
        ]);
    }

    public function mahasiswa()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 403);
        }

        $groupMember = GroupMember::with(['group.title', 'group.period'])
            ->where('student_id', $student->id)
            ->whereHas('group', fn ($q) => $q->where('status', '!=', 'REJECTED'))
            ->first();

        $group = $groupMember?->group;

        $phases = ['PDC1', 'SEMPRO', 'PDC2', 'TA', 'SIDANG', 'EXPO'];
        $approvedPhases = $group
            ? Document::where('group_id', $group->id)
                ->where('status', 'APPROVED')
                ->whereIn('phase', $phases)
                ->distinct()
                ->pluck('phase')
            : collect();

        $steps = [];
        foreach ($phases as $phase) {
            $steps[$phase] = $approvedPhases->contains($phase);
        }

        $isGraduated = $group
            && $group->status === 'APPROVED'
            && collect($steps)->every(fn ($v) => $v === true);

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
            'has_group' => (bool) $group,
            'group_status' => $group?->status,
            'title' => $group?->title?->title,
            'group_period' => $group?->period,
            'active_periods' => Period::where('is_active', true)->get(),
            'steps' => $steps,
            'is_graduated' => $isGraduated,
            'pending_proposal' => $pendingProposal,
        ]);
    }
}
