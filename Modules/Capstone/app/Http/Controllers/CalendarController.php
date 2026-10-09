<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Capstone\Models\ExpoEvent;
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
        $request->validate(['period_id' => 'nullable|integer|min:1']);
        // Cancelled rows stay in the DB (audit trail) but must never render:
        // dosen/mahasiswa learn about cancellations via notifications only.
        $seminars = SeminarSchedule::query()->where('status', '!=', 'CANCELLED');
        $defenses = TaDefenseSchedule::query()->where('status', '!=', 'CANCELLED');

        if ($role === 'dosen') {
            $lecturerId = CapstoneActor::lecturer($request->user())->id;
            // Slot-split visibility: SEMPRO belongs to supervisor 2, TA
            // defense to supervisor 1 (visibility only; duties unchanged).
            // EXPO is global read-only: every schedule, every period.
            // Examiner slots always stay visible regardless of slot.
            $seminars->where(fn (Builder $q) => $q
                ->where(fn (Builder $sempro) => $sempro
                    ->where('type', 'SEMPRO')
                    ->whereHas('group', fn (Builder $g) => $g->supervisedByInSlot($lecturerId, 'SUPERVISOR_2')))
                ->orWhere(fn (Builder $expo) => $expo->where('type', 'EXPO'))
                ->orWhere(fn (Builder $other) => $other
                    ->whereNotIn('type', ['SEMPRO', 'EXPO'])
                    ->whereHas('group', fn (Builder $g) => $g->supervisedBy($lecturerId)))
                ->orWhere('examiner_1_id', $lecturerId)->orWhere('examiner_2_id', $lecturerId));
            $defenses->where(fn (Builder $q) => $q
                ->whereHas('group', fn (Builder $g) => $g->supervisedByInSlot($lecturerId, 'SUPERVISOR_1'))
                ->orWhere('examiner_1_id', $lecturerId)->orWhere('examiner_2_id', $lecturerId)
                ->orWhereHas('examiners', fn (Builder $e) => $e->where('examiner_id', $lecturerId)));
        } elseif ($role === 'mahasiswa') {
            $studentId = CapstoneActor::student($request->user())->id;
            // Own group's schedules plus the global read-only EXPO feed
            // (all groups, all periods) so cross-period expos are visible.
            // A group member must not see another student's individual defense.
            $seminars->where(fn (Builder $q) => $q
                ->whereHas('group', fn (Builder $g) => $g->whereNotIn('status', ['REJECTED', 'DISSOLVED'])
                    ->whereHas('members', fn (Builder $m) => $m->where('student_id', $studentId)))
                ->orWhere(fn (Builder $expo) => $expo->where('type', 'EXPO')));
            // A group member must not see another student's individual defense.
            $defenses->where(fn (Builder $q) => $q->where('student_id', $studentId)
                ->orWhereHas('students', fn (Builder $s) => $s->where('students.id', $studentId)));
        }

        // EXPO is cross-period: an explicit period filter never hides it.
        $seminars->when($request->filled('period_id'), fn (Builder $q) => $q
            ->where(fn (Builder $w) => $w
                ->whereHas('group', fn (Builder $g) => $g->where('period_id', $request->integer('period_id')))
                ->orWhere('type', 'EXPO')));
        $defenses->when($request->filled('period_id'), fn (Builder $q) => $q
            ->whereHas('group', fn (Builder $g) => $g->where('period_id', $request->integer('period_id'))));
        foreach ([$seminars, $defenses] as $query) {
            $query->with(['group.title', 'group.period', 'group.members.student.user', 'examiner1.user', 'examiner2.user']);
        }
        $seminarEvents = $seminars->orderBy('date')->orderBy('start_time')->get()->map(fn ($s) => [
            ...$s->toArray(), 'date' => $s->date->format('Y-m-d'),
            'id' => $s->type === 'BIMBINGAN' ? 'bim_'.$s->id : $s->id,
            'period_name' => $s->group?->period?->name,
        ]);
        $defenseEvents = $defenses->with(['student.user', 'students.user', 'examiners.examiner.user', 'location'])
            ->orderBy('date')->orderBy('start_time')->get()->map(fn ($s) => [
                ...$s->toArray(), 'id' => 'ta_'.$s->id, 'type' => 'TA_DEFENSE',
                'date' => $s->date->format('Y-m-d'), 'period_name' => $s->group?->period?->name,
                'student_name' => $s->students->isNotEmpty() ? $s->students->pluck('name')->join(', ') : $s->student?->name,
                'examiners' => $s->examiners->map(fn ($e) => ['name' => $e->examiner?->name, 'role' => $e->role]),
                'room' => $s->room ?: $s->location?->name,
            ]);

        // Published expo masters are announcements, not per-group rows: they
        // exist even with zero registrations, so they are appended for
        // every role. No period filter: all masters. With a period filter:
        // globals (period_id NULL) plus that period's own events. Status
        // PUBLISHED sits outside the approvable PENDING* set and the rows
        // are not BIMBINGAN, so the UI treats them as read-only.
        $expoMasters = ExpoEvent::with('period')->where('is_published', true)
            ->when($request->filled('period_id'), fn (Builder $q) => $q
                ->where(fn (Builder $w) => $w->whereNull('period_id')->orWhere('period_id', $request->integer('period_id'))))
            ->withCount(['registrations' => fn ($q) => $q->where('status', 'REGISTERED')])
            ->orderBy('date')->orderBy('start_time')->get()->map(fn ($e) => [
                'id' => 'expo_event_'.$e->id, 'type' => 'EXPO', 'status' => 'PUBLISHED',
                'name' => $e->name, 'date' => $e->date->format('Y-m-d'),
                'start_time' => $e->start_time, 'end_time' => $e->end_time,
                'room' => $e->room, 'capacity' => $e->capacity,
                'registrations_count' => $e->registrations_count,
                'period_id' => $e->period_id, 'period_name' => $e->period?->name ?? 'Semua periode',
                'is_master' => true,
            ]);

        return response()->json(['data' => $seminarEvents->concat($defenseEvents)->concat($expoMasters)->sortBy([
            ['date', 'asc'], ['start_time', 'asc'],
        ])->values()]);
    }
}
