<x-eoffice::manajemen-ruangan.layout pageTitle="Dashboard Mahasiswa">

    @php
        $user = auth()->user();
        $recentBookings = \Modules\EOffice\Models\Peminjaman::with('ruangan')
            ->where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get();

        $activeBookingsCount = \Modules\EOffice\Models\Peminjaman::where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->count();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-8">
        <div class="min-w-0">
            <h1 class="text-[22px] font-bold text-gray-900 tracking-tight flex items-center gap-2">
                Selamat datang, <span class="truncate block max-w-[200px] sm:max-w-xs">{{ array_pad(explode(' ', $user->name), 1, 'Mahasiswa')[0] }}</span> 👋
            </h1>
            <p class="text-[13px] text-gray-500 mt-1.5">Kelola dan pantau pengajuan peminjaman ruangan Anda disini.</p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-[11px] rounded-xl transition-all shadow-sm hover:shadow-md w-full sm:w-auto">
                Pinjam Ruang
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div class="bg-white rounded-[16px] border border-gray-200 p-5 flex items-center gap-5 shadow-sm hover:shadow-md transition-all">
            <div class="w-[52px] h-[52px] flex-shrink-0 rounded-[14px] bg-primary-50 flex items-center justify-center text-primary-500 border border-primary-100">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
            </div>
            <div>
                <div class="text-[13px] font-semibold text-gray-500 mb-0.5 tracking-wide">Peminjaman Aktif</div>
                <div class="text-3xl font-black text-gray-900 leading-none">{{ $activeBookingsCount }}</div>
            </div>
        </div>

        <div class="relative bg-gradient-to-br from-[#0B266E] to-[#1a3c94] rounded-[16px] border border-primary-500 p-5 flex items-center overflow-hidden shadow-sm hover:shadow-md transition-all group cursor-pointer" onclick="window.location.href='{{ route('eoffice.peminjaman.user.kalender') }}'">
            <div class="absolute top-0 right-0 -mt-6 -mr-6 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-6 -ml-6 w-24 h-24 bg-primary-400 opacity-20 rounded-full blur-xl group-hover:scale-125 transition-transform duration-700"></div>
            
            <div class="relative z-10 w-full flex items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="text-primary-200 font-medium text-[13px] mb-1">Perlu Ruangan Segera?</div>
                    <div class="text-white text-[16px] font-bold tracking-tight leading-snug">Cek Kalender Ketersediaan</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 text-white backdrop-blur-sm group-hover:bg-white/20 group-hover:scale-105 transition-all flex-shrink-0 shadow-inner">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-base font-bold text-gray-900 mb-4">Aktivitas Peminjaman Terbaru</h3>
        <div class="mp-card">
            @if($recentBookings->count() > 0)
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>RUANGAN</th>
                            <th>TANGGAL & WAKTU</th>
                            <th>TUJUAN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                            <tr class="mp-tr">
                                <td style="font-weight: 600; color: #111827;">
                                    {{ $booking->ruangan->nama }}
                                </td>
                                <td>
                                    <div style="color: #111827; font-weight: 500;">
                                        {{ $booking->tanggal_pinjam?->translatedFormat('d M Y') }}</div>
                                    <div style="color: #6B7280; font-size: 12px;">
                                        {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}</div>
                                </td>
                                <td style="color: #4B5563;">{{ Str::limit($booking->tujuan, 30) }}</td>
                                <td>
                                    @if($booking->status == 'disetujui')
                                        <span class="px-3.5 py-1 rounded-full text-[13px] font-medium bg-emerald-50 text-emerald-600 inline-flex items-center whitespace-nowrap">Disetujui</span>
                                    @elseif($booking->status == 'menunggu')
                                        <span class="px-3.5 py-1 rounded-full text-[13px] font-medium bg-amber-50 text-amber-600 inline-flex items-center whitespace-nowrap">Menunggu</span>
                                    @elseif($booking->status == 'ditolak')
                                        <span class="px-3.5 py-1 rounded-full text-[13px] font-medium bg-red-50 text-red-600 inline-flex items-center whitespace-nowrap">Ditolak</span>
                                    @else
                                        <span class="px-3.5 py-1 rounded-full text-[13px] font-medium bg-gray-100 text-gray-600 inline-flex items-center whitespace-nowrap">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 40px; text-align: center;">
                    <div
                        style="width: 48px; height: 48px; border-radius: 24px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"
                            stroke-linecap="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <h3 style="font-size: 14px; font-weight: 600; color: #111827;">Belum ada riwayat</h3>
                    <p style="font-size: 13px; color: #6B7280; margin-top: 4px;">Anda belum pernah mengajukan peminjaman
                        ruangan.</p>
                    <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="mp-btn secondary"
                        style="margin-top: 16px;">Cari Ruangan</a>
                </div>
            @endif
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>