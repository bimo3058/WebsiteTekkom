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
            'file_berkas' => 'nullable|file|mimes:pdf|max:2048'
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
            // Upload ke Cloud Supabase
            $filePath = app(\App\Services\SupabaseStorage::class)->upload($request->file('file_berkas'), 'eo_mr_berkas', 'eoffice');
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

        // Pengecekan Bentrok Jadwal Peminjaman (Global Checks)
        $isConflict = Peminjaman::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal_pinjam', $request->tanggal_pinjam)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($query) use ($request) {
                // Logika Overlap: Waktu yang diajukan bertabrakan dengan rentang jam sistem
                $query->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                });
            })
            ->exists();

        // Pengecekan Bentrok: Jadwal Internal (Akademik, Kuliah, Rapat)
        $isInternalConflict = \Modules\EOffice\Models\MrJadwalInternal::where('ruangan_id', $request->ruangan_id)
            ->where(function ($query) use ($request, $dayOfWeek) {
                $query->where(function ($q) use ($dayOfWeek, $request) {
                    $q->where('tipe_jadwal', 'rutin')
                        ->where('hari', $dayOfWeek)
                        ->where(function ($tq) use ($request) {
                            $tq->whereNull('tgl_mulai_efektif')
                                ->orWhere('tgl_mulai_efektif', '<=', $request->tanggal_pinjam);
                        })
                        ->where(function ($tq) use ($request) {
                            $tq->whereNull('tgl_selesai_efektif')
                                ->orWhere('tgl_selesai_efektif', '>=', $request->tanggal_pinjam);
                        });
                })->orWhere(function ($q) use ($request) {
                    $q->where('tipe_jadwal', 'spesifik')->where('tanggal_spesifik', $request->tanggal_pinjam);
                });
            })
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        // Kebijakan Baru: SELURUH Role (Dosen & Mahasiswa) MUTLAK ditolak jika menabrak jadwal yang sudah ada!
        if ($isConflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Bentrok' => 'Mohon maaf, Ruangan tersebut telah lebih dulu dipesan atau sedang dalam proses antrean (menunggu persetujuan) pada rentang jam tersebut.']);
        }
        if ($isInternalConflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Sistem Internal' => 'Ruangan terblokir secara otomatis. Terbentrok dengan Jadwal ' . $isInternalConflict->kategori . ': ' . $isInternalConflict->keterangan]);
        }

        // Arsitektur Status Logika Akhir (VIP Shortcut untuk Dosen HANYA pada ruang yang 100% kosong)
        $isDosen = auth()->user()->hasRole('dosen');
        $statusAkhir = $isDosen ? 'disetujui' : 'menunggu';

        Peminjaman::create([
            'user_id' => auth()->id(),
            'ruangan_id' => $request->ruangan_id,
            'nomor_telepon' => $request->nomor_telepon,
            'tujuan' => $request->tujuan,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'berkas_pendukung' => $filePath,
            'status' => $statusAkhir,
            'created_by' => auth()->id()
        ]);

        $feedbackMsg = $statusAkhir == 'disetujui'
            ? 'Pengajuan peminjaman ruangan Anda telah berhasil diproses dan disetujui secara otomatis oleh sistem.'
            : 'Form Booking Ruangan berhasil diajukan dan masuk ke daftar tunggu persetujuan Admin.';

        return redirect()->route('eoffice.peminjaman.user.saya')
            ->with('success', $feedbackMsg);
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

        // Handle Room Filter & Sensible Default
        $selectedRoomId = $request->get('ruangan_id');
        $selectedKategori = $request->get('kategori');
        
        $allowedCategories = ['Kelas', 'Laboratorium', 'Sidang', 'Aula', 'Fasilitas Umum'];
        $allRuangansQuery = Ruangan::where('is_active', true)
            ->whereIn('kategori', $allowedCategories)
            ->orderBy('nama');

        // List semua ruangan (yang boleh dipinjam) untuk dropdown filter
        $allRuangansDaftar = $allRuangansQuery->get();
        $kategoriList = $allRuangansDaftar->pluck('kategori')->filter()->unique()->values();

        // Sensible Default: Jika tidak ada ruangan/kategori yang dipilih, default ke Kelas
        if (!$selectedRoomId && !$selectedKategori) {
            $selectedKategori = 'Kelas';
        }

        if ($selectedRoomId) {
            $ruangans = $allRuangansDaftar->where('id', $selectedRoomId)->values();
        } elseif ($selectedKategori && $selectedKategori !== 'Semua Kategori') {
            $ruangans = $allRuangansDaftar->where('kategori', $selectedKategori)->values();
        } else {
            $ruangans = $allRuangansDaftar;
        }

        // Fetch bookings for the week range
        $bookingsRaw = Peminjaman::with('user:id,name')
            ->whereBetween('tanggal_pinjam', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->get(['id', 'user_id', 'ruangan_id', 'tanggal_pinjam', 'jam_mulai', 'jam_selesai', 'status', 'tujuan']);

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
        $nim = $user->student->student_number ?? $user->lecturer->employee_number ?? explode('@', $user->email)[0];
        $phone = ''; // User model currently may not have phone natively unless it does, we can leave blank.

        $internalSchedules = \Modules\EOffice\Models\MrJadwalInternal::all();

        return view('eoffice::manajemen-ruangan.user.kalender.index', compact(
            'ruangans',
            'allRuangansDaftar',
            'selectedRoomId',
            'kategoriList',
            'selectedKategori',
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
            $peminjaman->update([
                'status' => 'dibatalkan',
                'waktu_approval' => now()
            ]);
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
            ->latest('updated_at')
            ->paginate(request('per_page', 10))->appends(request()->query());
        return view('eoffice::manajemen-ruangan.user.riwayat.index', compact('riwayats'));
    }

    /**
     * Mark a specific notification as read and redirect to its URL
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $peminjamanId = $notification->data['peminjaman_id'] ?? null;
        $url = $notification->data['url'] ?? route('eoffice.peminjaman.user.riwayat');

        if ($peminjamanId) {
            $peminjaman = \Modules\EOffice\Models\Peminjaman::find($peminjamanId);
            if ($peminjaman) {
                if ($peminjaman->status == 'disetujui') {
                    $now = now();
                    $date = $now->format('Y-m-d');
                    $time = $now->format('H:i:s');
                    
                    $isPast = ($peminjaman->tanggal_pinjam < $date) || ($peminjaman->tanggal_pinjam == $date && $peminjaman->jam_selesai <= $time);
                    
                    if ($isPast) {
                        $url = route('eoffice.peminjaman.user.riwayat');
                    } else {
                        $url = route('eoffice.peminjaman.user.saya');
                    }
                } else {
                    // Ditolak / Dibatalkan
                    $url = route('eoffice.peminjaman.user.riwayat');
                }
            }
        }

        return redirect()->to($url);
    }

    /**
     * Get the current unread notification count
     */
    public function getUnreadCount()
    {
        $count = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
        return response()->json(['count' => $count]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }
}
