<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Ruangan;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Pengaturan;
use Modules\EOffice\Models\TanggalLibur;

class UserPeminjamanController extends Controller
{
    // Feature 2 & 3: Catalog & Interactive Calendar Booking
    public function booking(Request $request)
    {
        Peminjaman::autoExpirePending();

        $ruangans = Ruangan::where('is_active', true)
            ->with([
                'fotos',
                'peminjamans' => function ($q) {
                    $q->where('tanggal_pinjam', now()->format('Y-m-d'))
                        ->whereIn('status', ['menunggu', 'disetujui']);
                }
            ])
            ->orderBy('nama')
            ->get();

        $pendingCount = Peminjaman::where('user_id', auth()->id())
            ->whereIn('status', ['menunggu'])
            ->count();

        return view('eoffice::manajemen-ruangan.user.booking.index', compact('ruangans', 'pendingCount'));
    }

    // Room Detail Page
    public function showRuangan($id)
    {
        $room = Ruangan::where('is_active', true)->with('fotos')->findOrFail($id);
        $fasilitas = is_array($room->fasilitas) ? $room->fasilitas : (json_decode($room->fasilitas, true) ?? []);

        // Upcoming bookings for this room (next 7 days)
        $upcomingBookings = Peminjaman::where('ruangan_id', $id)
            ->whereBetween('tanggal_pinjam', [\Carbon\Carbon::today()->format('Y-m-d'), \Carbon\Carbon::today()->addDays(6)->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->orderBy('tanggal_pinjam')
            ->orderBy('jam_mulai')
            ->get();

        $kalenderUrl = route('eoffice.peminjaman.user.kalender', ['ruangan_id' => $id]);

        return view('eoffice::manajemen-ruangan.user.booking.detail', compact('room', 'fasilitas', 'upcomingBookings', 'kalenderUrl'));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'nomor_telepon' => 'required|string',
            'tujuan' => 'required|string|max:500',
            'tanggal_pinjam' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'file_berkas' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        // Interceptor: Cek status blacklist / Banned account 
        $blacklist = \Modules\EOffice\Models\MrBlacklist::where('user_id', auth()->id())->first();
        if ($blacklist) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Suspend' => 'Akun Anda sedang ditangguhkan dari layanan peminjaman ruangan. Alasan: ' . ($blacklist->alasan ?? 'Pelanggaran ketentuan')]);
        }

        $filePath = null;
        if ($request->hasFile('file_berkas')) {
            $filePath = $request->file('file_berkas')->store('eo_mr_berkas', 'public');
        }

        // Cek hari libur
        $isHoliday = TanggalLibur::where('tanggal', $request->tanggal_pinjam)->exists();
        if ($isHoliday) {
            return redirect()->back()->withErrors('Tidak dapat melakukan peminjaman. Tanggal yang Anda pilih adalah hari libur.');
        }

        // Cek akhir pekan (Sabtu/Minggu)
        $dayOfWeek = \Carbon\Carbon::parse($request->tanggal_pinjam)->format('N');
        $bukaAkhirPekan = filter_var(Pengaturan::where('key', 'buka_akhir_pekan')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$bukaAkhirPekan && ($dayOfWeek == 6 || $dayOfWeek == 7)) {
            return redirect()->back()->withErrors('Peminjaman tidak tersedia pada akhir pekan (Sabtu/Minggu).');
        }

        // Cek jam operasional
        $jamBuka = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';
        if ($request->jam_mulai < $jamBuka || $request->jam_selesai > $jamTutup) {
            return redirect()->back()->withErrors("Jam peminjaman harus berada di dalam jam operasional ({$jamBuka} - {$jamTutup}).");
        }

        // Cek batas minimum H- booking
        $batasHMinBooking = (int) (Pengaturan::where('key', 'batas_h_min_booking')->value('value') ?? 0);
        $minDate = \Carbon\Carbon::today()->addDays($batasHMinBooking);
        if (\Carbon\Carbon::parse($request->tanggal_pinjam)->startOfDay()->lt($minDate)) {
            return redirect()->back()->withErrors("Peminjaman gagal! Pengajuan harus dilakukan sekurang-kurangnya H-{$batasHMinBooking} dari tanggal pemakaian.");
        }

        // Pengecekan Bentrok Jadwal Peminjaman
        $isConflict = Peminjaman::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal_pinjam', $request->tanggal_pinjam)
            ->where('status', 'disetujui') // Hanya memblokir jam yang SUDAH PASTI dipakai (disetujui)
            ->where(function ($query) use ($request) {
                // Logika Overlap: Waktu yang diajukan bertabrakan dengan rentang jam sistem
                $query->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                });
            })
            ->exists();

        if ($isConflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Bentrok' => 'Mohon maaf, Ruangan tersebut telah lebih dulu dipesan dan DISETUJUI oleh pihak lain pada rentang jam tersebut.']);
        }

        Peminjaman::create([
            'user_id' => auth()->id(),
            'ruangan_id' => $request->ruangan_id,
            'nomor_telepon' => $request->nomor_telepon,
            'tujuan' => $request->tujuan,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'berkas_pendukung' => $filePath,
            'status' => 'menunggu'
        ]);

        return redirect()->route('eoffice.peminjaman.user.saya')
            ->with('success', 'Form Booking Ruangan berhasil diajukan dan masuk ke daftar tunggu persetujuan.');
    }

    public function kalender(Request $request)
    {
        Peminjaman::autoExpirePending();

        $mode = $request->get('mode', 'week'); // 'week' or 'month'
        $today = \Carbon\Carbon::today();

        // --- Week Mode ---
        $weekStart = $request->get('week_start')
            ? \Carbon\Carbon::parse($request->get('week_start'))
            : $today->copy();
        $weekEnd = $weekStart->copy()->addDays(6);

        // --- Month Mode ---
        $monthDate = $request->get('month')
            ? \Carbon\Carbon::parse($request->get('month') . '-01')
            : $today->copy()->startOfMonth();

        // Handle Room Filter
        $selectedRoomId = $request->get('ruangan_id');
        $allRuangansQuery = Ruangan::where('is_active', true)->orderBy('nama');

        if ($selectedRoomId) {
            $ruangans = $allRuangansQuery->where('id', $selectedRoomId)->get();
        } else {
            $ruangans = $allRuangansQuery->get();
        }

        // List semua ruangan untuk dropdown filter
        $allRuangansDaftar = Ruangan::where('is_active', true)->orderBy('nama')->get();

        // Fetch bookings for the week range
        $bookingsRaw = Peminjaman::whereBetween('tanggal_pinjam', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->get(['ruangan_id', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'status']);

        // For month heatmap - count bookings per day
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();
        $monthBookings = Peminjaman::whereBetween('tanggal_pinjam', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->selectRaw('tanggal_pinjam, count(*) as total')
            ->groupBy('tanggal_pinjam')
            ->pluck('total', 'tanggal_pinjam');

        // Ambil Data Hari Libur dan Jam Operasional
        $holidays = TanggalLibur::whereBetween('tanggal', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->pluck('keterangan', 'tanggal')->toArray();
        $jamBuka = Pengaturan::where('key', 'jam_buka')->value('value') ?? '08:00';
        $jamTutup = Pengaturan::where('key', 'jam_tutup')->value('value') ?? '16:00';
        $bukaAkhirPekan = filter_var(Pengaturan::where('key', 'buka_akhir_pekan')->value('value') ?? false, FILTER_VALIDATE_BOOLEAN);
        $batasHMinBooking = (int) (Pengaturan::where('key', 'batas_h_min_booking')->value('value') ?? 0);

        $user = auth()->user();
        $nim = explode('@', $user->email)[0];
        $phone = ''; // User model currently may not have phone natively unless it does, we can leave blank.

        return view('eoffice::manajemen-ruangan.user.kalender.index', compact(
            'ruangans',
            'allRuangansDaftar',
            'selectedRoomId',
            'bookingsRaw',
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
            'batasHMinBooking',
            'user',
            'nim',
            'phone'
        ));
    }

    // Feature 4: Peminjaman Saya
    public function saya()
    {
        Peminjaman::autoExpirePending();

        $now = \Carbon\Carbon::now();
        $dateToday = $now->copy()->format('Y-m-d');
        $timeNow = $now->copy()->format('H:i:s');

        $peminjamans = Peminjaman::with('ruangan')
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($dateToday, $timeNow) {
                $q->where('status', 'menunggu')
                    ->orWhere(function ($subQ) use ($dateToday, $timeNow) {
                        $subQ->where('status', 'disetujui')
                            ->where(function ($timeQ) use ($dateToday, $timeNow) {
                                $timeQ->where('tanggal_pinjam', '>', $dateToday)
                                    ->orWhere(function ($dayQ) use ($dateToday, $timeNow) {
                                        $dayQ->where('tanggal_pinjam', '=', $dateToday)
                                            ->where('jam_selesai', '>', $timeNow);
                                    });
                            });
                    });
            })
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('eoffice::manajemen-ruangan.user.peminjaman.index', compact('peminjamans'));
    }

    public function batalkanBooking($id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($peminjaman->status == 'menunggu' || $peminjaman->status == 'disetujui') {
            $peminjaman->update(['status' => 'dibatalkan']);
            return redirect()->back()->with('success', 'Peminjaman berhasil dibatalkan secara mandiri.');
        }

        return redirect()->back()->withErrors('Aksi tidak diijinkan atau status sudah tidak bisa dibatalkan.');
    }

    // Feature 5: Riwayat Peminjaman (Finished/Rejected/Canceled)
    // Feature 5: Riwayat Peminjaman (Finished/Rejected/Canceled)
    public function riwayat()
    {
        Peminjaman::autoExpirePending();

        $now = \Carbon\Carbon::now();
        $dateToday = $now->copy()->format('Y-m-d');
        $timeNow = $now->copy()->format('H:i:s');

        $riwayats = Peminjaman::with('ruangan')
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($dateToday, $timeNow) {
                // Yang ditolak/dibatalkan
                $q->whereIn('status', ['ditolak', 'dibatalkan'])
                    // PLUS yang Disetujui tapi waktunya sudah SELESAI
                    ->orWhere(function ($subQ) use ($dateToday, $timeNow) {
                    $subQ->where('status', 'disetujui')
                        ->where(function ($timeQ) use ($dateToday, $timeNow) {
                            $timeQ->where('tanggal_pinjam', '<', $dateToday)
                                ->orWhere(function ($dayQ) use ($dateToday, $timeNow) {
                                    $dayQ->where('tanggal_pinjam', '=', $dateToday)
                                        ->where('jam_selesai', '<=', $timeNow);
                                });
                        });
                });
            })
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();
        return view('eoffice::manajemen-ruangan.user.riwayat.index', compact('riwayats'));
    }
}
