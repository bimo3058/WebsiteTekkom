<x-eoffice::manajemen-ruangan.layout pageTitle="Dashboard Admin">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Dashboard Admin Ruangan</h1>
            <p class="mp-page-sub">Pantau aktivitas peminjaman, jumlah ruangan terpakai, dan tiket approval hari ini.
            </p>
        </div>
    </div>
    <style>
        .mp-stat.clickable {
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: block;
        }

        .mp-stat.clickable:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border-color: #d1d5db;
        }
    </style>

    <div class="mp-stats-grid cols-3" style="margin-top: 20px;">
        <a href="{{ route('eoffice.peminjaman.admin.ruangan.index') }}" class="mp-stat clickable">
            <div class="mp-stat-icon sky"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M4 19.5V4.5C4 3.11929 5.11929 2 6.5 2H20V22H6.5C5.11929 22 4 20.8807 4 19.5Z" />
                </svg></div>
            <div class="mp-stat-label">Total Ruangan Aktif</div>
            <div class="mp-stat-value">{{ number_format($totalRuangan ?? 0) }}</div>
            <div class="mp-stat-sub">Siap dipinjam</div>
        </a>
        <a href="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" class="mp-stat clickable">
            <div class="mp-stat-icon yellow"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg></div>
            <div class="mp-stat-label">Menunggu Approval</div>
            <div class="mp-stat-value" {!! ($pendingApproval ?? 0) > 0 ? 'style="color: #D97706;"' : '' !!}>
                {{ number_format($pendingApproval ?? 0) }}
            </div>
            <div class="mp-stat-sub">Butuh tindakan admin</div>
        </a>
        <div class="mp-stat">
            <div class="mp-stat-icon violet"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg></div>
            <div class="mp-stat-label">Digunakan Hari Ini</div>
            <div class="mp-stat-value">{{ number_format($dipakaiHariIni ?? 0) }}</div>
            <div class="mp-stat-sub">Jadwal disetujui hari ini</div>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-header">
            <h3 class="mp-card-title">Jadwal Terdekat & Antrean Peminjaman</h3>
            <div class="right">
                <a href="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" class="mp-btn secondary sm">Kelola
                    Jadwal</a>
            </div>
        </div>
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; text-transform:uppercase;">Peminjam</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; text-transform:uppercase;">Ruangan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; text-transform:uppercase;">Kegiatan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; text-transform:uppercase;">Waktu</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; text-transform:uppercase;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingActivities as $act)
                            <tr class="mp-tr">
                                <td style="font-weight: 600;" title="{{ $act->user->name ?? 'Pegawai / Mahasiswa' }}">
                                    @php
                                        $fullName = $act->user->name ?? 'Pegawai / Mahasiswa';
                                        $isDosen = $act->user ? $act->user->hasRole('dosen') : false;
                                        
                                        $displayName = $fullName;
                                        if (!$isDosen) {
                                            $nameParts = explode(' ', $fullName);
                                            if (count($nameParts) > 2) {
                                                $displayName = $nameParts[0] . ' ' . $nameParts[1];
                                                for ($i = 2; $i < count($nameParts); $i++) {
                                                    $displayName .= ' ' . strtoupper(substr($nameParts[$i], 0, 1)) . '.';
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($isDosen)
                                        <div style="max-width: 160px;" class="truncate">{{ $fullName }}</div>
                                    @else
                                        <div>{{ $displayName }}</div>
                                    @endif
                                </td>
                                <td style="max-width: 260px;" class="truncate" title="{{ $act->ruangan->nama ?? '-' }}">
                                    {{ $act->ruangan->nama ?? '-' }}
                                </td>
                                <td style="max-width: 260px;" class="truncate" title="{{ $act->tujuan }}">
                                    {{ $act->tujuan }}
                                </td>
                                <td>
                                    @php 
                                                                            $tgl = \Carbon\Carbon::parse($act->tanggal_pinjam);
                                        $strTgl = $tgl->isToday() ? 'Hari ini' : ($tgl->isTomorrow() ? 'Besok' : $tgl->translatedFormat('d M Y'));
                                    @endphp
                                    <div style="font-size:12px; color:#1F2937; font-weight:600;">
                                        {{ $strTgl }}
                                    </div>
                                    <div style="font-size:11px; color:#6B7280; margin-top:2px;">
                                        Jam {{ \Carbon\Carbon::parse($act->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($act->jam_selesai)->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                        if ($act->status === 'disetujui')
                                            $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                        elseif ($act->status === 'ditolak')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                        elseif ($act->status === 'menunggu')
                                            $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                        elseif ($act->status === 'selesai')
                                            $st = ['bg' => '#F1E9FF', 'color' => '#5E53F4', 'border' => '#D1BFFF'];
                                        elseif ($act->status === 'dibatalkan')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                    @endphp
                                    <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                        {{ $act->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 30px; color: #666D80;">
                                    Belum ada aktivitas peminjaman terdekat atau antrean baru.<br>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-eoffice::manajemen-ruangan.layout>