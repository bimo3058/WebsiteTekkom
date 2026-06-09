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
    <span style="font-size:12px;color:#666D80;">{{ $semesterLabel }}</span>
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
        <div class="mp-stat-label">Asprak Pending</div>
        <div class="mp-stat-value">{{ $totalAsprakPending ?? 0 }}</div>
        <div class="mp-stat-sub">perlu review</div>
    </div>
</div>

{{-- Section: Data Pengguna --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Data Pengguna</span>
    <span class="sec-rule"></span>
</div>

{{-- Tabel Dosen & Mahasiswa --}}
<div class="flex gap-[14px]" style="flex-shrink:0;">

    {{-- Tabel Dosen --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Dosen Terdaftar</span>
            <span class="mp-badge neutral sm">{{ $totalDosen ?? 0 }}</span>
            <div class="right">
                <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua →</a>
            </div>
        </div>
        <div class="mp-card-body">
            <div style="display:flex;align-items:center;padding:10px 18px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <div class="mp-th flex-1">Nama Dosen</div>
                <div class="mp-th" style="width:130px;">NIP</div>
                <div class="mp-th" style="width:80px;text-align:center;">Praktikum</div>
            </div>
            <div style="overflow-y:auto;max-height:220px;">
                @forelse($dosenTerbaru ?? [] as $d)
                <div class="mp-tr" style="display:flex;align-items:center;padding:10px 18px;">
                    <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                        <div class="mp-av sky">{{ strtoupper(substr($d['name'], 0, 1)) }}{{ strtoupper(substr($d['name'], strpos($d['name'].' ',' ')+1, 1)) }}</div>
                        <div class="min-w-0">
                            <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">{{ $d['name'] }}</div>
                            <div style="font-size:11px;color:#666D80;" class="truncate">{{ $d['email'] }}</div>
                        </div>
                    </div>
                    <div style="width:130px;font-size:11px;color:#666D80;font-family:ui-monospace,monospace;">{{ $d['employee_number'] }}</div>
                    <div style="width:80px;text-align:center;">
                        <span class="mp-badge navy sm">{{ $d['jumlah_praktikum'] }} mk</span>
                    </div>
                </div>
                @empty
                <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada dosen terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Mahasiswa --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Mahasiswa Terdaftar</span>
            <span class="mp-badge neutral sm">{{ $totalMahasiswa ?? 0 }}</span>
        </div>
        <div class="mp-card-body">
            <div style="display:flex;align-items:center;padding:10px 18px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <div class="mp-th flex-1">Nama Mahasiswa</div>
                <div class="mp-th" style="width:100px;">NIM</div>
                <div class="mp-th" style="width:70px;text-align:center;">Angkatan</div>
                <div class="mp-th" style="width:70px;text-align:center;">Ikut</div>
            </div>
            <div style="overflow-y:auto;max-height:220px;">
                @forelse($mahasiswaTerbaru ?? [] as $m)
                <div class="mp-tr" style="display:flex;align-items:center;padding:10px 18px;">
                    <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-2">
                        <div class="mp-av yellow">{{ strtoupper(substr($m['name'], 0, 1)) }}{{ strtoupper(substr($m['name'], strpos($m['name'].' ',' ')+1, 1)) }}</div>
                        <div class="min-w-0">
                            <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">{{ $m['name'] }}</div>
                            <div style="font-size:11px;color:#666D80;" class="truncate">{{ $m['email'] }}</div>
                        </div>
                    </div>
                    <div style="width:100px;font-size:11px;color:#666D80;font-family:ui-monospace,monospace;">{{ $m['student_number'] }}</div>
                    <div style="width:70px;font-size:11px;color:#666D80;text-align:center;">{{ $m['cohort_year'] }}</div>
                    <div style="width:70px;text-align:center;">
                        <span class="mp-badge sky sm">{{ $m['jumlah_praktikum'] }}</span>
                    </div>
                </div>
                @empty
                <div style="padding:32px;text-align:center;font-size:13px;color:#808897;">Belum ada mahasiswa terdaftar.</div>
                @endforelse
            </div>
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
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Tabel Praktikum --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <div>
                <span class="mp-card-title">Daftar Praktikum</span>
            </div>
            <div class="right">
                <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua →</a>
            </div>
        </div>
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
            <div class="mp-th" style="width:100px;">Kode</div>
            <div class="mp-th flex-1">Nama Praktikum</div>
            <div class="mp-th" style="width:160px;">Dosen Pengampu</div>
            <div class="mp-th" style="width:80px;text-align:center;">Praktikan</div>
            <div class="mp-th" style="width:90px;text-align:center;">Status</div>
        </div>
        <div style="overflow-y:auto;flex:1;">
            @forelse($praktikums ?? [] as $p)
            <div class="mp-tr" style="display:flex;align-items:center;padding:12px 20px;cursor:pointer;transition:background .12s;"
                 onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''"
                 onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.show', $p->id) }}'">
                <div style="width:100px;font-size:12px;font-weight:700;color:#0B266E;font-family:ui-monospace,monospace;">{{ $p->kode ?? '—' }}</div>
                <div class="flex-1 truncate pr-3" style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $p->nama }}</div>
                <div style="width:160px;font-size:12px;color:#666D80;" class="truncate">{{ $p->dosen?->name ?? '—' }}</div>
                <div style="width:80px;text-align:center;font-size:14px;font-weight:700;color:#0D0D12;">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                <div style="width:90px;text-align:center;">
                    @if($p->status === 'aktif')
                        <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                    @else
                        <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada data praktikum.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Pendaftaran Pending --}}
    <div class="mp-card min-w-0 flex-1">
        <div class="mp-card-header" style="flex-shrink:0;">
            <div>
                <span class="mp-card-title">Pendaftaran Pending</span>
            </div>
            @if($totalAsprakPending ?? 0)
            <span class="mp-badge warning sm"><span class="dot"></span>{{ $totalAsprakPending }} menunggu</span>
            @endif
            <div class="right">
                <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua →</a>
            </div>
        </div>
        <div style="overflow-y:auto;flex:1;">
            @forelse($pendaftaranTerbaru ?? [] as $pend)
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;padding:12px 18px;">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="mp-av green">{{ strtoupper(substr($pend->user?->name ?? 'A', 0, 1)) }}{{ strtoupper(substr($pend->user?->name ?? 'A', strpos(($pend->user?->name ?? 'A').' ',' ')+1, 1)) }}</div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">{{ $pend->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#666D80;" class="truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}">
                        @csrf
                        <button type="submit" class="mp-btn ghost sm" title="Setujui">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="mp-btn destructive sm" title="Tolak">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada pendaftaran pending.</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

</x-eoffice::manajemen-praktikum.layout>
