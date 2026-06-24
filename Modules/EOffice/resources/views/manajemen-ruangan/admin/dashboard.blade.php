<x-eoffice::manajemen-ruangan.layout pageTitle="Dashboard Admin">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Dashboard Admin Ruangan</h1>
            <p class="mp-page-sub">Pantau aktivitas peminjaman, jumlah ruangan terpakai, dan tiket approval hari ini.
            </p>
        </div>
    </div>

    <div class="mp-stats-grid cols-3" style="margin-top: 20px;">
        <div class="mp-stat">
            <div class="mp-stat-icon sky"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 19.5V4.5C4 3.11929 5.11929 2 6.5 2H20V22H6.5C5.11929 22 4 20.8807 4 19.5Z" />
                </svg></div>
            <div class="mp-stat-label">Total Ruangan Aktif</div>
            <div class="mp-stat-value">{{ number_format($totalRuangan ?? 0) }}</div>
            <div class="mp-stat-sub">Siap dipinjam</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon yellow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg></div>
            <div class="mp-stat-label">Menunggu Approval</div>
            <div class="mp-stat-value">0</div>
            <div class="mp-stat-sub">Butuh tindakan admin</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon violet"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
                </svg></div>
            <div class="mp-stat-label">Total Pengguna</div>
            <div class="mp-stat-value">{{ number_format($totalUser ?? 0) }}</div>
            <div class="mp-stat-sub">Terdaftar di sistem</div>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-header">
            <h3 class="mp-card-title">Ruangan Baru Ditambahkan</h3>
            <div class="right">
                <a href="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" class="mp-btn secondary sm">Lihat
                    Semua</a>
            </div>
        </div>
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>NAMA RUANG</th>
                            <th>LOKASI</th>
                            <th>KAPASITAS</th>
                            <th>JADWAL TERPAKAI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRuangan as $r)
                            <tr class="mp-tr">
                                <td style="font-weight: 600;">{{ $r->nama }}</td>
                                <td>{{ $r->lokasi }} <span style="font-size:11px; color:#A4ABB8;">(Lt.
                                        {{ $r->lantai ?? '-' }})</span></td>
                                <td>{{ $r->kapasitas }} Orang</td>
                                <td><span style="font-size:12px; color:#A4ABB8;">Segera Hadir...</span></td>
                                <td>
                                    @if($r->is_active)
                                        <span class="mp-badge success sm">Aktif</span>
                                    @else
                                        <span class="mp-badge sm" style="background:#FADAE1; color:#710E21;">Non-aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 30px; color: #666D80;">
                                    Belum ada data ruangan yang ditambahkan.<br>
                                    <a href="{{ route('eoffice.peminjaman.admin.ruangan.create') }}"
                                        style="color:#0B266E; font-weight:600; text-decoration:none; margin-top:8px; display:inline-block;">+
                                        Tambah Ruangan</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>