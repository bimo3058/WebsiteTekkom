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
            'bukaAkhirPekan'
        ));
    }

    /**
     * Jalur Tol: Express Booking Bypass 
     * Injects either a direct 'Disetujui' booking or a 'Jadwal Internal Spesifik' schedule.
     */
    public function expressBooking(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tipe_aksi' => 'required|in:internal,dosen',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan' => 'required|string',
            'nim' => 'required_if:tipe_aksi,dosen',
            'kategori' => 'required_if:tipe_aksi,internal|string',
            'mata_kuliah' => 'nullable|string|max:255',
            'kode_mk' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:50',
            'sks' => 'nullable|integer',
            'kuota' => 'nullable|integer',
            'pengampu' => 'nullable|string|max:255'
        ]);

        if ($request->tipe_aksi === 'internal') {
            // Path A: Register an Ad-Hoc block (Maintenance / Internal Academic)
            MrJadwalInternal::create([
                'ruangan_id' => $request->ruangan_id,
                'tipe_jadwal' => 'spesifik',
                'kategori' => $request->kategori,
                'tanggal_spesifik' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'keterangan' => $request->keterangan,
                'mata_kuliah' => $request->mata_kuliah,
                'kode_mk' => $request->kode_mk,
                'kelas' => $request->kelas,
                'sks' => $request->sks,
                'kuota' => $request->kuota,
                'pengampu' => $request->pengampu
            ]);

            return redirect()->back()->with('success', 'Jadwal Internal Express berhasil diblokir ke ruangan.');
        } else {
            // Resolve User by NIM/NIP or Email first
            $targetUser = \App\Models\User::where('external_id', $request->nim)
                ->orWhere('email', $request->nim)
                ->first();

            if (!$targetUser) {
                return redirect()->back()->with('error', 'Peminjam dengan identitas tersebut tidak ditemukan di sistem.');
            }

            Peminjaman::create([
                'user_id' => $targetUser->id,
                'ruangan_id' => $request->ruangan_id,
                'tanggal_pinjam' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'tujuan' => $request->keterangan,
                'status' => 'disetujui',
                'created_by' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'Booking Ekspres berhasil dimasukkan atas nama ' . $targetUser->name . ' dan otomatis Disetujui.');
        }
    }

    /**
     * API Endpoint for Autocomplete JSON search.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->query('q');
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $searchTerm = '%' . strtolower($query) . '%';

        $users = \App\Models\User::whereRaw('LOWER(name) LIKE ?', [$searchTerm])
            ->orWhereRaw('LOWER(email) LIKE ?', [$searchTerm])
            ->orWhereRaw('LOWER(external_id) LIKE ?', [$searchTerm])
            ->take(5)
            ->get(['id', 'name', 'email', 'external_id']);

        return response()->json($users);
    }
}
