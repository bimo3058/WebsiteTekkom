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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
        <a href="{{ route('eoffice.peminjaman.user.saya') }}"
            class="block bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-gray-300 hover:-translate-y-0.5 transition-all cursor-pointer">
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
        </a>

        <a href="{{ route('eoffice.peminjaman.user.saya') }}"
            class="block bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-gray-300 hover:-translate-y-0.5 transition-all cursor-pointer">
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
                <div class="text-[13px] font-semibold text-gray-500 mb-0.5 tracking-wide">Jadwal Aktif</div>
                <div class="text-2xl font-black text-gray-900 leading-none">{{ $approvedCount }}</div>
            </div>
        </a>

        <a href="{{ route('eoffice.peminjaman.user.riwayat') }}"
            class="block bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md hover:border-gray-300 hover:-translate-y-0.5 transition-all cursor-pointer">
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
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Kolom Kiri: Jadwal Terdekat --}}
        <div class="bg-white rounded-xl border border-gray-200 flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[14px] font-bold text-gray-900">Jadwal Terdekat</h3>
                <a href="{{ route('eoffice.peminjaman.user.saya') }}"
                    class="text-[12px] font-medium text-[#0B266E] hover:underline flex items-center gap-1">
                    Lihat Semua &rarr;
                </a>
            </div>

            {{-- Table Header --}}
            <div
                class="px-5 py-2.5 border-b border-gray-100 flex justify-between items-center text-[12px] font-medium text-gray-500 bg-gray-50/50">
                <div class="flex-1">Ruangan & Tujuan</div>
                <div class="w-32 text-right">Waktu</div>
            </div>

            {{-- List --}}
            <div class="flex-1 overflow-y-auto">
                @forelse($upcomingBookings as $booking)
                    <div
                        class="px-5 py-3.5 border-b border-gray-100 last:border-b-0 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                        <div class="min-w-0 flex-1 pr-4">
                            <h4 class="text-[13px] font-medium text-[#111827] leading-tight mb-0.5 truncate"
                                title="{{ $booking->ruangan->nama }}">
                                {{ $booking->ruangan->nama }}
                            </h4>
                            <div class="text-[11px] text-gray-500 line-clamp-1" title="{{ $booking->tujuan }}">
                                {{ $booking->tujuan }}
                            </div>
                        </div>
                        <div class="flex-shrink-0 w-32 text-right">
                            <div class="text-[13px] font-medium text-[#111827]">
                                {{ $booking->tanggal_pinjam?->translatedFormat('d M') }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 flex flex-col items-center justify-center text-center">
                        <div class="text-[13px] font-medium text-gray-500 max-w-[240px]">
                            Jadwal ruangan yang disetujui dalam waktu dekat belum tersedia
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Kolom Kanan: Pengajuan Terakhir --}}
        <div class="bg-white rounded-xl border border-gray-200 flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[14px] font-bold text-gray-900">Pengajuan Terakhir</h3>
                <a href="{{ route('eoffice.peminjaman.user.riwayat') }}"
                    class="text-[12px] font-medium text-[#0B266E] hover:underline flex items-center gap-1">
                    Riwayat Lengkap &rarr;
                </a>
            </div>

            {{-- Table --}}
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50">
                            <th class="px-5 py-2.5 text-[12px] font-medium text-gray-500 whitespace-nowrap">Ruangan &
                                Tanggal</th>
                            <th class="px-5 py-2.5 text-[12px] font-medium text-gray-500 whitespace-nowrap">Tujuan</th>
                            <th class="px-5 py-2.5 text-[12px] font-medium text-gray-500 whitespace-nowrap text-left">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5 align-top">
                                    <div class="text-[13px] font-medium text-[#111827] mb-0.5 truncate max-w-[150px]"
                                        title="{{ $booking->ruangan->nama }}">
                                        {{ $booking->ruangan->nama }}
                                    </div>
                                    <div class="text-[11px] text-gray-500">
                                        {{ $booking->tanggal_pinjam?->translatedFormat('d M Y') }}<br>
                                        {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 align-top text-[11px] text-gray-500">
                                    <div class="line-clamp-2 max-w-[120px]" title="{{ $booking->tujuan }}">
                                        {{ $booking->tujuan }}
                                    </div>
                                    @if($booking->status === 'ditolak' && $booking->alasan_penolakan)
                                        <div class="text-[10px] font-medium text-rose-600 mt-1 flex items-start gap-1">
                                            <svg class="w-3 h-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
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
                                <td class="px-5 py-3.5 align-top text-left">
                                    @php
                                        $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                        if (strtolower($booking->status) === 'disetujui')
                                            $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                        elseif (strtolower($booking->status) === 'ditolak')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                        elseif (strtolower($booking->status) === 'menunggu')
                                            $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                        elseif (strtolower($booking->status) === 'selesai')
                                            $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];
                                        elseif (strtolower($booking->status) === 'dibatalkan')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                    @endphp
                                    <span
                                        style="font-size:10px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 10px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-10 h-10 flex items-center justify-center text-gray-300 mb-3">
                                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                                <polyline points="10 9 9 9 8 9"></polyline>
                                            </svg>
                                        </div>
                                        <div class="text-[13px] font-medium text-gray-500 max-w-[240px] mx-auto">
                                            Riwayat pengajuan peminjaman ruangan belum tersedia
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>