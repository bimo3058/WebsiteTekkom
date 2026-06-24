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

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Selamat datang, {{ $user->name }} 👋</h1>
            <p class="mp-page-sub">Kelola dan pantau pengajuan peminjaman ruangan Anda disini.</p>
        </div>
        <div>
            <a href="{{ route('eoffice.peminjaman.user.booking') }}" class="mp-btn primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Pinjam Ruangan Sekarang
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 24px;">
        <div class="mp-card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
            <div
                style="width: 48px; height: 48px; border-radius: 12px; background: #EEF2FF; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>
            <div>
                <div style="font-size: 13px; color: #6B7280; font-weight: 500;">Peminjaman Aktif</div>
                <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ $activeBookingsCount }}</div>
            </div>
        </div>

        <div class="mp-card"
            style="padding: 20px; background: linear-gradient(135deg, #0B266E 0%, #1a3c94 100%); color: white; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 13px; opacity: 0.8; font-weight: 500;">Perlu Ruangan Segera?</div>
                <div style="font-size: 16px; font-weight: 600; margin-top: 4px;">Cek Kalender Ketersediaan</div>
            </div>
            <a href="{{ route('eoffice.peminjaman.user.kalender') }}"
                style="background: rgba(255,255,255,0.2); border-radius: 8px; padding: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <div style="margin-top: 32px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 16px;">Aktivitas Peminjaman Terbaru
        </h3>
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
                                        <span class="mp-badge bg-green">Disetujui</span>
                                    @elseif($booking->status == 'menunggu')
                                        <span class="mp-badge bg-yellow">Menunggu</span>
                                    @elseif($booking->status == 'ditolak')
                                        <span class="mp-badge bg-red">Ditolak</span>
                                    @else
                                        <span class="mp-badge" style="background:#F3F4F6; color:#374151;">Dibatalkan</span>
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