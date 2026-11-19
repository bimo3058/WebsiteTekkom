<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Cerc;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Modules\EOffice\Models\{AsprakPraktikum, Praktikum, PracticumBookingMeeting};

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->hasAnyRole(['superadmin', 'koordinator_cerc']), 403);
        $data = $request->validate(['q' => 'nullable|string|max:100', 'filter' => 'nullable|in:without_coordinator,with_assistants', 'per_page' => 'nullable|integer|in:5,10,15', 'schedule_per_page' => 'nullable|integer|in:5,10,15']);
        $active = Praktikum::where('is_active', true);
        $assignments = AsprakPraktikum::where('role', 'asprak')->whereHas('user')
            ->whereHas('praktikum', fn ($q) => $q->where('is_active', true));
        $stats = [
            'practicums' => (clone $active)->count(),
            'without_coordinator' => (clone $active)->whereDoesntHave('koordinator')->count(),
            'assistants' => (clone $assignments)->distinct()->count('user_id'),
            'pending' => 0,
        ];
        $query = (clone $active)->with('koordinator')->addSelect(['assistants_count' => AsprakPraktikum::selectRaw('COUNT(DISTINCT user_id)')
            ->whereColumn('praktikum_id', 'eo_prak_praktikum.id')->where('role', 'asprak')->whereHas('user')]);
        if (!empty($data['q'])) $query->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($data['q']).'%']);
        if (($data['filter'] ?? '') === 'without_coordinator') $query->whereDoesntHave('koordinator');
        if (($data['filter'] ?? '') === 'with_assistants') $query->whereHas('asistenPraktikum', fn ($q) => $q->whereHas('user'));
        $praktikums = $query->withExists('koordinator')->orderBy('koordinator_exists')
            ->orderBy('nama')->orderBy('id')->paginate((int) ($data['per_page'] ?? 5))->withQueryString();

        $from = today(); $until = $from->copy()->addDays(6);
        $schedules = new LengthAwarePaginator([], 0, (int) ($data['schedule_per_page'] ?? 5), 1, ['path' => $request->url(), 'pageName' => 'schedule_page']);
        $attention = ['rejected' => 0, 'cancelled' => 0];
        if (Schema::hasTable('eo_prak_booking_meetings') && Schema::hasTable('eo_prak_booking_requests') && Schema::hasTable('eo_mr_peminjamans')
            && Schema::hasColumn('eo_mr_peminjamans', 'source') && Schema::hasColumn('eo_mr_peminjamans', 'source_id')) {
            $sessions = PracticumBookingMeeting::whereHas('praktikum', fn ($q) => $q->where('is_active', true));
            $futureSessions = (clone $sessions)->where(fn ($q) => $q->whereDate('date', '>', $from)->orWhere(fn ($q) => $q->whereDate('date', $from)->whereTime('ends_at', '>', now()->format('H:i:s'))));
            $stats['pending'] = (clone $futureSessions)->whereHas('booking', fn ($q) => $q->where('source', 'praktikum')->where('status', 'menunggu'))->count();
            foreach (['rejected' => 'ditolak', 'cancelled' => 'dibatalkan'] as $key => $status) {
                $attention[$key] = (clone $futureSessions)->whereHas('booking', fn ($q) => $q->where('source', 'praktikum')->where('status', $status))->count();
            }
            $schedules = (clone $sessions)->with(['praktikum', 'room'])->official()
                ->whereBetween('date', [$from->toDateString(), $until->toDateString()])
                ->where(fn ($q) => $q->whereDate('date', '>', $from)->orWhere(fn ($q) => $q->whereDate('date', $from)->whereTime('ends_at', '>', now()->format('H:i:s'))))
                ->orderBy('date')->orderBy('starts_at')->orderBy('id')->paginate((int) ($data['schedule_per_page'] ?? 5), ['*'], 'schedule_page')->withQueryString();
        }
        return view('eoffice::manajemen-praktikum.cerc.dashboard', compact('stats', 'praktikums', 'schedules', 'attention', 'from', 'until'));
    }
}
