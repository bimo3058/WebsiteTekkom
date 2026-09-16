<x-eoffice::manajemen-ruangan.layout pageTitle="Dashboard Peminjaman">

    @php
        $user = auth()->user();
        $now = \Carbon\Carbon::now();
        $dateToday = $now->copy()->format('Y-m-d');
        $timeNow = $now->copy()->format('H:i:s');

        // Statistik
        $pendingCount = \Modules\EOffice\Models\Peminjaman::where('user_id', $user->id)
            ->where('status', 'menunggu')->count();

        $approvedCount = \Modules\EOffice\Models\Peminjaman::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($dateToday, $timeNow) {
                $q->where('tanggal_pinjam', '>', $dateToday)
                    ->orWhere(function ($sq) use ($dateToday, $timeNow) {
                        $sq->where('tanggal_pinjam', '=', $dateToday)
                            ->where('jam_selesai', '>', $timeNow);
                    });
            })->count();

        $rejectedCount = \Modules\EOffice\Models\Peminjaman::where('user_id', $user->id)
            ->where('status', 'ditolak')->count();

        // Jadwal Terdekat
        $upcomingBookings = \Modules\EOffice\Models\Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($dateToday, $timeNow) {
                $q->where('tanggal_pinjam', '>', $dateToday)
                    ->orWhere(function ($sq) use ($dateToday, $timeNow) {
                        $sq->where('tanggal_pinjam', '=', $dateToday)
                            ->where('jam_selesai', '>', $timeNow);
                    });
            })
            ->orderBy('tanggal_pinjam', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(3)
            ->get();

        // Pengajuan Terakhir
        $recentBookings = \Modules\EOffice\Models\Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-8">
        <div class="min-w-0">
            <h1 class="text-[22px] font-bold text-gray-900 tracking-tight flex items-center gap-2">
                Selamat datang, <span
                    class="truncate block max-w-[200px] sm:max-w-xs">{{ array_pad(explode(' ', $user->name), 1, 'Mahasiswa')[0] }}</span>
            </h1>
            <p class="text-[13px] text-gray-500 mt-1.5">Kelola dan pantau pengajuan peminjaman ruangan Anda di sini.</p>
        </div>
        <div class="flex flex-shrink-0 gap-3">
            <a href="{{ route('eoffice.peminjaman.user.kalender') }}"
                class="inline-flex items-center justify-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-[13px] font-semibold px-4 py-[11px] rounded-xl transition-all shadow-sm">
                Lihat Kalender
            </a>
            <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-[11px] rounded-xl transition-all shadow-sm">
                + Pinjam Ruangan
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div
            class="bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div
                class="w-12 h-12 flex-shrink-0 rounded-[12px] bg-amber-50 flex items-center justify-center text-amber-500 border border-amber-100">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-[13px] font-semibold text-gray-500 mb-0.5 tracking-wide">Menunggu</div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $pendingCount }}</div>
            </div>
        </div>

        <div
            class="bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div
                class="w-12 h-12 flex-shrink-0 rounded-[12px] bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div>
                <div class="text-[13px] font-semibold text-gray-500 mb-0.5 tracking-wide">Akan Datang (Disetujui)</div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $approvedCount }}</div>
            </div>
        </div>

        <div
            class="bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-all">
            <div
                class="w-12 h-12 flex-shrink-0 rounded-[12px] bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div>
                <div class="text-[13px] font-semibold text-gray-500 mb-0.5 tracking-wide">Ditolak</div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $rejectedCount }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Jadwal Terdekat --}}
        <div class="lg:col-span-1 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Jadwal Terdekat</h3>
                <a href="{{ route('eoffice.peminjaman.user.saya') }}"
                    class="text-[12px] font-semibold text-[#0B266E] hover:underline bg-blue-50 px-3 py-1 rounded-full">Lihat
                    Semua</a>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($upcomingBookings as $booking)
                    <div
                        class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-all border-l-4 border-l-emerald-500">
                        <div class="text-[11px] font-bold text-emerald-600 mb-1 flex items-center gap-1.5">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ $booking->tanggal_pinjam?->translatedFormat('d M Y') }}
                        </div>
                        <h4 class="font-bold text-gray-900 text-[14px] leading-tight mb-1">{{ $booking->ruangan->nama }}
                        </h4>
                        <div class="text-[12px] text-gray-500 mb-3 flex items-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                        </div>
                        <div
                            class="text-[12px] font-medium text-gray-700 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100 line-clamp-2">
                            {{ $booking->tujuan }}
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white rounded-xl border border-dashed border-gray-300 p-6 flex flex-col items-center justify-center text-center">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div class="text-[13px] font-semibold text-gray-700">Tidak ada jadwal</div>
                        <div class="text-[12px] text-gray-500 mt-0.5">Anda belum memiliki jadwal ruangan yang disetujui
                            dalam waktu dekat.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Kanan: Pengajuan Terakhir --}}
        <div class="lg:col-span-2 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Pengajuan Terakhir</h3>
                <a href="{{ route('eoffice.peminjaman.user.riwayat') }}"
                    class="text-[12px] font-semibold text-[#0B266E] hover:underline bg-blue-50 px-3 py-1 rounded-full">Riwayat
                    Lengkap</a>
            </div>

            <div class="mp-card flex-1">
                @if($recentBookings->count() > 0)
                    <div class="mp-table-wrap">
                        <table class="mp-table">
                            <thead>
                                <tr>
                                    <th>RUANGAN & TANGGAL</th>
                                    <th>TUJUAN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                    <tr class="mp-tr">
                                        <td>
                                            <div class="truncate max-w-[100px] sm:max-w-[130px]"
                                                style="font-weight: 600; color: #111827; font-size:13px;"
                                                title="{{ $booking->ruangan->nama }}">
                                                {{ $booking->ruangan->nama }}
                                            </div>
                                            <div style="color: #6B7280; font-size: 11px; margin-top:2px;">
                                                {{ $booking->tanggal_pinjam?->translatedFormat('d M Y') }} •
                                                {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-[12px] text-gray-600 line-clamp-2 max-w-[200px]"
                                                title="{{ $booking->tujuan }}">
                                                {{ Str::limit($booking->tujuan, 40) }}
                                            </div>
                                            @if($booking->status === 'ditolak' && $booking->alasan_penolakan)
                                                <div class="text-[11px] font-medium text-rose-600 mt-1 flex items-start gap-1">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                    </svg>
                                                    <span class="line-clamp-1"
                                                        title="{{ $booking->alasan_penolakan }}">{{ $booking->alasan_penolakan }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                                if ($booking->status === 'disetujui')
                                                    $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                                elseif ($booking->status === 'ditolak')
                                                    $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                                elseif ($booking->status === 'menunggu')
                                                    $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                                elseif ($booking->status === 'selesai')
                                                    $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];
                                                elseif ($booking->status === 'dibatalkan')
                                                    $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                            @endphp
                                            <span
                                                style="font-size:10px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 10px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                                {{ $booking->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                        <div
                            class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-3 border border-gray-100">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h3 class="text-[14px] font-semibold text-gray-900 mb-1">Belum ada riwayat pengajuan</h3>
                        <p class="text-[13px] text-gray-500 mb-4 max-w-sm">Anda belum pernah mengajukan peminjaman ruangan
                            apa pun sejauh ini.</p>
                        <a href="{{ route('eoffice.peminjaman.user.booking') }}"
                            class="inline-flex items-center justify-center bg-gray-900 hover:bg-gray-800 text-white text-[12px] font-semibold px-4 py-2 rounded-lg transition-colors">
                            Katalog Ruangan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>