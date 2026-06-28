<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\MrJadwalInternal;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\TanggalLibur;
use Modules\EOffice\Models\Pengaturan;
use Carbon\Carbon;

class KalenderController extends Controller
{
    /**
     * Display the Global Calendar merging both Student and Internal Bookings.
     */
    public function index(Request $request)
    {
        Peminjaman::autoExpirePending();
        $allRuangansDaftar = Ruangan::where('is_active', true)->orderBy('nama', 'asc')->get();
        $selectedRoomId = $request->get('ruangan_id');

        if ($selectedRoomId) {
            $ruangans = $allRuangansDaftar->where('id', $selectedRoomId)->values();
        } else {
            $ruangans = $allRuangansDaftar;
        }

        $mode = $request->get('mode', 'week');
        $today = \Carbon\Carbon::today();

        $weekStart = $request->get('week_start')
            ? \Carbon\Carbon::parse($request->get('week_start'))
            : $today->copy();
        $weekEnd = $weekStart->copy()->addDays(6);

        $monthDate = $request->get('month')
            ? \Carbon\Carbon::parse($request->get('month') . '-01')
            : $today->copy()->startOfMonth();

        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $bookingsRaw = Peminjaman::with(['user', 'ruangan'])
            ->whereBetween('tanggal_pinjam', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->get();

        $monthBookings = Peminjaman::whereBetween('tanggal_pinjam', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->selectRaw('tanggal_pinjam, count(*) as total')
            ->groupBy('tanggal_pinjam')
            ->pluck('total', 'tanggal_pinjam');

        $holidays = TanggalLibur::whereBetween('tanggal', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->pluck('keterangan', 'tanggal')->toArray();
        $jamBuka = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';
        $bukaAkhirPekan = filter_var(Pengaturan::where('key', 'buka_akhir_pekan')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);
        $batasHMinBooking = (int) (Pengaturan::where('key', 'batas_h_min_booking')->value('value') ?? 0);

        $internalSchedules = MrJadwalInternal::with('ruangan')->get();

        return view('eoffice::manajemen-ruangan.admin.kalender.index', compact(
            'ruangans',
            'allRuangansDaftar',
            'selectedRoomId',
            'bookingsRaw',
            'internalSchedules',
            'weekStart',
            'weekEnd',
            'mode',
            'today',
            'monthDate',
            'monthStart',
            'monthEnd',
            'monthBookings',
            'holidays',
            'jamBuka',
            'jamTutup',
            'bukaAkhirPekan',
            'batasHMinBooking'
        ));
    }
}
