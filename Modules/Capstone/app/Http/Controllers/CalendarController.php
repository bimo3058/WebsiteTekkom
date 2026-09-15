<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Support\CapstoneActor;

/** One calendar feed, scoped before loading related academic identities. */
class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $role = CapstoneActor::role($request->user(), $request->attributes->get('capstone_role'));
        abort_unless(in_array($role, ['admin', 'dosen', 'mahasiswa'], true), 403);
        $request->validate(['period_id'=>'nullable|integer|min:1']);
        $seminars = SeminarSchedule::query();
        $defenses = TaDefenseSchedule::query();

        if ($role === 'dosen') {
            $lecturerId = CapstoneActor::lecturer($request->user())->id;
            $seminars->where(fn (Builder $q) => $q
                ->whereHas('group', fn (Builder $g) => $g->supervisedBy($lecturerId))
                ->orWhere('examiner_1_id', $lecturerId)->orWhere('examiner_2_id', $lecturerId));
            $defenses->where(fn (Builder $q) => $q
                ->whereHas('group', fn (Builder $g) => $g->supervisedBy($lecturerId))
                ->orWhere('examiner_1_id', $lecturerId)->orWhere('examiner_2_id', $lecturerId)
                ->orWhereHas('examiners', fn (Builder $e) => $e->where('examiner_id', $lecturerId)));
        } elseif ($role === 'mahasiswa') {
            $studentId = CapstoneActor::student($request->user())->id;
            $seminars->whereHas('group', fn (Builder $q) => $q->whereNotIn('status', ['REJECTED', 'DISSOLVED'])
                ->whereHas('members', fn (Builder $m) => $m->where('student_id', $studentId)));
            // A group member must not see another student's individual defense.
            $defenses->where(fn (Builder $q) => $q->where('student_id', $studentId)
                ->orWhereHas('students', fn (Builder $s) => $s->where('students.id', $studentId)));
        }

        foreach ([$seminars, $defenses] as $query) {
            $query->when($request->filled('period_id'), fn (Builder $q) => $q
                ->whereHas('group', fn (Builder $g) => $g->where('period_id', $request->integer('period_id'))));
            $query->with(['group.title', 'group.period', 'group.members.student.user', 'examiner1.user', 'examiner2.user']);
        }
        $seminarEvents = $seminars->orderBy('date')->orderBy('start_time')->get()->map(fn ($s) => [
            ...$s->toArray(), 'date'=>$s->date->format('Y-m-d'),
            'id'=>$s->type === 'BIMBINGAN' ? 'bim_'.$s->id : $s->id,
            'period_name'=>$s->group?->period?->name,
        ]);
        $defenseEvents = $defenses->with(['student.user', 'students.user', 'examiners.examiner.user', 'location'])
            ->orderBy('date')->orderBy('start_time')->get()->map(fn ($s) => [
                ...$s->toArray(), 'id'=>'ta_'.$s->id, 'type'=>'TA_DEFENSE',
                'date'=>$s->date->format('Y-m-d'), 'period_name'=>$s->group?->period?->name,
                'student_name'=>$s->students->isNotEmpty() ? $s->students->pluck('name')->join(', ') : $s->student?->name,
                'examiners'=>$s->examiners->map(fn ($e) => ['name'=>$e->examiner?->name, 'role'=>$e->role]),
                'room'=>$s->room ?: $s->location?->name,
            ]);

        return response()->json(['data'=>$seminarEvents->concat($defenseEvents)->sortBy([
            ['date', 'asc'], ['start_time', 'asc'],
        ])->values()]);
    }
}
