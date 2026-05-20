<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Admin — Manajemen Praktikum">
@php
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
    $name = auth()->user()->name;
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
@endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Dashboard Admin</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Selamat datang, {{ $firstName }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            Kelola Praktikum
        </a>
        <a href="{{ route('eoffice.manprak.admin.periode-pendaftaran.index') }}" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Periode Pendaftaran
        </a>
    </div>
</div>

{{-- Section: Ringkasan --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Ringkasan Semester</span>
    <span class="sec-rule"></span>
    <span style="font-size:12px;color:var(--c-fg-muted, #666D80);">{{ $semesterLabel }}</span>
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
        <div class="mp-stat-label">Praktikum Aktif</div>
        <div class="mp-stat-value">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub">semester ini</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="mp-stat-label">Total Mahasiswa</div>
        <div class="mp-stat-value">{{ $totalMahasiswa ?? 0 }}</div>
        <div class="mp-stat-sub">terdaftar di sistem</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8"/></svg>
        </div>
        <div class="mp-stat-label">Total Dosen</div>
        <div class="mp-stat-value">{{ $totalDosen ?? 0 }}</div>
        <div class="mp-stat-sub">dari tabel lecturers</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-icon red">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zM12 6v6l4 2"/></svg>
        </div>
        <div class="mp-stat-label">Total Praktikan</div>
        <div class="mp-stat-value">{{ $totalPraktikan ?? 0 }}</div>
        <div class="mp-stat-sub">terdaftar di praktikum</div>
    </div>
</div>

{{-- Section: Data Pengguna --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Data Pengguna</span>
    <span class="sec-rule"></span>
</div>

{{-- Tabel Dosen & Mahasiswa --}}
<div class="flex gap-[14px]" style="flex-shrink:0; margin-bottom: 4px;">

    {{-- Tabel Dosen --}}
    <div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-width:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
            <div style="display:flex; align-items:center; gap:8px;">
                <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Dosen Terdaftar</h2>
                <span class="mp-badge neutral sm">{{ $totalDosen ?? 0 }}</span>
            </div>
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="mp-btn secondary sm" style="text-decoration:none; font-size:11px; padding:4px 8px;">Lihat Semua →</a>
        </div>
        <div style="overflow-x:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; min-width:320px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                        <th style="padding:10px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Dosen</th>
                        <th style="padding:10px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:120px;">NIP</th>
                        <th style="padding:10px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">Praktikum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosenTerbaru ?? [] as $d)
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:10px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                <div class="mp-av sky" style="width:28px; height:28px; font-size:10px;">{{ strtoupper(substr($d['name'], 0, 1)) }}{{ strtoupper(substr($d['name'], strpos($d['name'].' ',' ')+1, 1)) }}</div>
                                <div style="min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--c-fg, #0D0D12);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $d['name'] }}</div>
                                    <div style="font-size:11px;color:var(--c-fg-muted, #666D80);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $d['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:10px 16px; font-size:11px; color:var(--c-fg-muted, #666D80); font-family:monospace;">{{ $d['employee_number'] }}</td>
                        <td style="padding:10px 16px; text-align:center;">
                            <span class="mp-badge navy sm" style="font-weight:600;">{{ $d['jumlah_praktikum'] }} mk</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-muted, #808897);">Belum ada dosen terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Mahasiswa --}}
    <div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-width:0;">
        <div style="display:flex; align-items:center; gap:8px; padding:12px 16px; border-bottom:1px solid var(--c-border, #DFE1E7); height: 43px;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Mahasiswa Terdaftar</h2>
            <span class="mp-badge neutral sm">{{ $totalMahasiswa ?? 0 }}</span>
        </div>
        <div style="overflow-x:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; min-width:340px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                        <th style="padding:10px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Mahasiswa</th>
                        <th style="padding:10px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">NIM</th>
                        <th style="padding:10px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:65px;">Angkatan</th>
                        <th style="padding:10px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:55px;">Ikut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswaTerbaru ?? [] as $m)
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:10px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                <div class="mp-av yellow" style="width:28px; height:28px; font-size:10px;">{{ strtoupper(substr($m['name'], 0, 1)) }}{{ strtoupper(substr($m['name'], strpos($m['name'].' ',' ')+1, 1)) }}</div>
                                <div style="min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--c-fg, #0D0D12);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $m['name'] }}</div>
                                    <div style="font-size:11px;color:var(--c-fg-muted, #666D80);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $m['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:10px 16px; font-size:11px; color:var(--c-fg-muted, #666D80); font-family:monospace;">{{ $m['student_number'] }}</td>
                        <td style="padding:10px 16px; text-align:center; font-size:12px; color:var(--c-fg-muted, #666D80);">{{ $m['cohort_year'] }}</td>
                        <td style="padding:10px 16px; text-align:center;">
                            <span class="mp-badge sky sm" style="font-weight:700;">{{ $m['jumlah_praktikum'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-muted, #808897);">Belum ada mahasiswa terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Section: Praktikum & Pendaftaran --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikum &amp; Pendaftaran</span>
    <span class="sec-rule"></span>
    @if(($totalAsprakPending??0) + ($totalKoorPending??0) > 0)
    <span class="mp-badge error sm">
        <span class="dot"></span>{{ ($totalAsprakPending??0) + ($totalKoorPending??0) }} perlu tindakan
    </span>
    @endif
</div>

{{-- Bottom: Tabel Praktikum + Pendaftaran Pending --}}
<div class="flex gap-[14px]">

    {{-- Tabel Praktikum --}}
    <div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:2; min-width:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border, #DFE1E7); flex-shrink:0;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Daftar Praktikum Terbaru</h2>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary sm" style="text-decoration:none; font-size:11px; padding:4px 8px;">Lihat Semua →</a>
        </div>
        <div style="overflow-x:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; min-width:460px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">Kode</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Praktikum</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:150px;">Dosen Pengampu</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:75px;">Peserta</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:85px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($praktikums ?? [] as $p)
                    <tr style="border-bottom:1px solid #F3F4F6;transition:background .12s;cursor:pointer;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'" onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.show', $p->id) }}'">
                        <td style="padding:12px 16px;font-size:12px;font-weight:700;color:#0B266E;font-family:monospace;">{{ $p->kode ?? '—' }}</td>
                        <td style="padding:12px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);" class="truncate" title="{{ $p->nama }}">{{ $p->nama }}</td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);" class="truncate" title="{{ $p->dosen?->name ?? '—' }}">{{ $p->dosen?->name ?? '—' }}</td>
                        <td style="padding:12px 16px; text-align:center; font-size:13px; font-weight:700; color:var(--c-fg, #0D0D12);">{{ $p->daftar_praktikan_count ?? 0 }}</td>
                        <td style="padding:12px 16px; text-align:center;">
                            @if($p->status === 'aktif')
                                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                            @else
                                <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:40px;text-align:center;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            <div style="font-size:12px;color:var(--c-fg-muted, #666D80);">Belum ada data praktikum.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pendaftaran Pending --}}
    <div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-width:0;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border, #DFE1E7); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:6px;">
                <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Pendaftaran Pending</h2>
                @if($totalAsprakPending ?? 0)
                <span class="mp-badge warning sm"><span class="dot"></span>{{ $totalAsprakPending }}</span>
                @endif
            </div>
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}" class="mp-btn secondary sm" style="text-decoration:none; font-size:11px; padding:4px 8px;">Lihat Semua →</a>
        </div>
        <div style="overflow-x:auto; flex:1;">
            <table style="width:100%; border-collapse:collapse; min-width:260px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Calon Asprak</th>
                        <th style="padding:11px 16px; text-align:right; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:95px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaranTerbaru ?? [] as $pend)
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                        <td style="padding:11px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                <div class="mp-av green" style="width:28px; height:28px; font-size:10px;">{{ strtoupper(substr($pend->user?->name ?? 'A', 0, 1)) }}{{ strtoupper(substr($pend->user?->name ?? 'A', strpos(($pend->user?->name ?? 'A').' ',' ')+1, 1)) }}</div>
                                <div style="min-width:0;">
                                    <div style="font-size:13px;font-weight:600;color:var(--c-fg, #0D0D12);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pend->user?->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--c-fg-muted, #666D80);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $pend->praktikum?->nama ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:11px 16px; text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="mp-btn ghost sm" style="padding:5px 7px;" title="Setujui">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mp-btn destructive sm" style="padding:5px 7px;" title="Tolak">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="padding:40px;text-align:center;font-size:12px;color:var(--c-fg-muted, #666D80);">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
                            Tidak ada pendaftaran pending.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</x-eoffice::manajemen-praktikum.layout>