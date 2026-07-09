<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Admin — Manajemen Praktikum">
@php
    $name      = auth()->user()->name;
    $firstName = explode(' ', $name)[0];
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
@endphp

{{-- ═══════════════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════════════ --}}
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
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/></svg>
            Kelola Praktikum
        </a>
        <a href="{{ route('eoffice.manprak.admin.periode-pendaftaran.index') }}" class="mp-btn primary md" style="text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Periode Pendaftaran
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     WELCOME BANNER
═══════════════════════════════════════════════ --}}
<div style="background:linear-gradient(120deg,#0B266E 0%,#1a3a8f 100%); border-radius:14px; padding:20px 24px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
    <div>
        <div style="font-size:18px; font-weight:700; color:#fff; letter-spacing:-.02em;">Selamat Datang, {{ $name }}!</div>
        <div style="font-size:12px; color:rgba(255,255,255,.65); margin-top:3px;">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}
        </div>
    </div>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <div style="background:rgba(255,255,255,.12); border-radius:10px; padding:10px 16px; text-align:center; min-width:72px;">
            <div style="font-size:22px; font-weight:700; color:#fff;">{{ $totalPraktikumAktif ?? 0 }}</div>
            <div style="font-size:10px; color:rgba(255,255,255,.65); margin-top:2px;">Praktikum Aktif</div>
        </div>
        <div style="background:rgba(255,255,255,.12); border-radius:10px; padding:10px 16px; text-align:center; min-width:72px;">
            <div style="font-size:22px; font-weight:700; color:#fff;">{{ ($totalAsprakPending ?? 0) + ($totalKoorPending ?? 0) }}</div>
            <div style="font-size:10px; color:rgba(255,255,255,.65); margin-top:2px;">Perlu Tindakan</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     STAT CARDS
═══════════════════════════════════════════════ --}}
<div class="mp-stats-grid cols-4" style="flex-shrink:0;">

    {{-- Praktikum Aktif --}}
    <div class="mp-stat">
        <div class="mp-stat-icon navy">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/>
            </svg>
        </div>
        <div class="mp-stat-label">Praktikum Aktif</div>
        <div class="mp-stat-value">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub">semester ini</div>
    </div>

    {{-- Total Dosen --}}
    <div class="mp-stat">
        <div class="mp-stat-icon sky">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8"/>
            </svg>
        </div>
        <div class="mp-stat-label">Total Dosen</div>
        <div class="mp-stat-value">{{ $totalDosen ?? 0 }}</div>
        <div class="mp-stat-sub">dari tabel lecturers</div>
    </div>

    {{-- Total Mahasiswa --}}
    <div class="mp-stat">
        <div class="mp-stat-icon yellow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
        </div>
        <div class="mp-stat-label">Total Mahasiswa</div>
        <div class="mp-stat-value">{{ $totalMahasiswa ?? 0 }}</div>
        <div class="mp-stat-sub">terdaftar di sistem</div>
    </div>

    {{-- Pendaftaran Pending --}}
    <div class="mp-stat">
        <div class="mp-stat-icon red">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div class="mp-stat-label">Pendaftaran Pending</div>
        <div class="mp-stat-value">{{ ($totalAsprakPending ?? 0) + ($totalKoorPending ?? 0) }}</div>
        <div class="mp-stat-sub">perlu ditinjau</div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     2×2 PANEL GRID  (mirip wireframe)
═══════════════════════════════════════════════ --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; flex-shrink:0;">

    {{-- ── Panel 1: Daftar Dosen ─────────────────────────────────── --}}
    <div style="background:#fff; border:1px solid var(--c-border,#DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        {{-- Panel Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:13px 16px; border-bottom:1px solid var(--c-border,#DFE1E7); background:#FAFAFA;">
            <div style="font-size:14px; font-weight:700; color:var(--c-fg,#0D0D12);">Daftar Dosen</div>
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}"
               style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; background:#0B266E; color:#fff; text-decoration:none;">Lihat</a>
        </div>
        {{-- Table Head --}}
        <div style="display:grid; grid-template-columns:1fr 1fr 130px 100px; padding:8px 14px; border-bottom:1px solid #F3F4F6; background:#FAFAFA;">
            <div style="font-size:10px; font-weight:600; color:#A4ABB8; text-transform:uppercase; letter-spacing:.04em;">Dosen</div>
            <div style="font-size:10px; font-weight:600; color:#A4ABB8; text-transform:uppercase; letter-spacing:.04em;">Email</div>
            <div style="font-size:10px; font-weight:600; color:#A4ABB8; text-transform:uppercase; letter-spacing:.04em;">NIP</div>
            <div style="font-size:10px; font-weight:600; color:#A4ABB8; text-transform:uppercase; letter-spacing:.04em; text-align:center;">Praktikum Diampu</div>
        </div>
        {{-- Rows --}}
        @forelse($dosenTerbaru ?? [] as $d)
        @php
            $parts   = explode(' ', $d['name'] ?? 'D');
            $ini     = strtoupper(substr($parts[0] ?? 'D', 0, 1) . substr($parts[1] ?? $parts[0], 0, 1));
            $colors  = ['sky','navy','green','yellow','violet'];
            $col     = $colors[crc32($d['email'] ?? '') % count($colors)];
        @endphp
        <div style="display:grid; grid-template-columns:1fr 1fr 130px 100px; padding:10px 14px; border-bottom:1px solid #F8F9FB; transition:background .12s;"
             onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
            <div style="display:flex; align-items:center; gap:8px; min-width:0;">
                <div class="mp-av {{ $col }}" style="width:26px; height:26px; font-size:10px; flex-shrink:0;">{{ $ini }}</div>
                <div style="font-size:12px; font-weight:600; color:var(--c-fg,#0D0D12); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $d['name'] }}</div>
            </div>
            <div style="font-size:11px; color:#666D80; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; align-self:center;">{{ $d['email'] }}</div>
            <div style="font-size:11px; font-family:monospace; color:#353849; align-self:center;">{{ $d['employee_number'] }}</div>
            <div style="text-align:center; align-self:center;">
                <span class="mp-badge primary sm">{{ $d['jumlah_praktikum'] }} Praktikum</span>
            </div>
        </div>
        @empty
        <div style="padding:32px; text-align:center; font-size:12px; color:#A4ABB8;">Belum ada data dosen.</div>
        @endforelse
    </div>

    {{-- ── Panel 2: Daftar Praktikum ────────────────────────────── --}}
    <div style="background:#fff; border:1px solid var(--c-border,#DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:13px 16px; border-bottom:1px solid var(--c-border,#DFE1E7); background:#FAFAFA;">
            <div style="font-size:14px; font-weight:700; color:var(--c-fg,#0D0D12);">Daftar Praktikum</div>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}"
               style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; background:#0B266E; color:#fff; text-decoration:none;">Lihat</a>
        </div>
        @forelse($praktikums ?? [] as $p)
        <div style="padding:11px 16px; border-bottom:1px solid #F8F9FB; display:flex; align-items:center; gap:10px; transition:background .12s;"
             onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
            <div style="width:32px; height:32px; border-radius:8px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="1.8" stroke-linecap="round">
                    <path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/>
                </svg>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:600; color:var(--c-fg,#0D0D12); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    {{ $p->nama }}
                    @if($p->kode) <span style="font-size:10px; font-family:monospace; color:#0B266E; background:rgba(11,38,110,0.08); padding:1px 5px; border-radius:3px; margin-left:4px;">{{ $p->kode }}</span> @endif
                </div>
                <div style="font-size:11px; color:#A4ABB8; margin-top:1px;">
                    {{ $p->semester }} {{ $p->tahun_ajaran }}
                    @if($p->dosen) · {{ $p->dosen->name }} @endif
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                <span style="font-size:11px; color:#666D80;">{{ $p->daftar_praktikan_count ?? 0 }} praktikan</span>
                @if($p->status === 'aktif')
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                @else
                <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                @endif
            </div>
        </div>
        @empty
        <div style="padding:32px; text-align:center; font-size:12px; color:#A4ABB8;">Belum ada praktikum terdaftar.</div>
        @endforelse
    </div>

    {{-- ── Panel 3: Pendaftaran Koordinator ───────────────────────────── --}}
    <div style="background:#fff; border:1px solid var(--c-border,#DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:13px 16px; border-bottom:1px solid var(--c-border,#DFE1E7); background:#FAFAFA;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="font-size:14px; font-weight:700; color:var(--c-fg,#0D0D12);">Pendaftaran Koor</div>
                @if(($totalKoorPending ?? 0) > 0)
                <span style="font-size:10px; font-weight:700; background:#D39C3D; color:#fff; border-radius:999px; padding:1px 7px;">
                    {{ $totalKoorPending ?? 0 }}
                </span>
                @endif
            </div>
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-koor.index') }}"
               style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; background:#0B266E; color:#fff; text-decoration:none;">Lihat</a>
        </div>
        @forelse($pendaftaranKoorTerbaru ?? [] as $pend)
        @php
            $isPrak = $pend->praktikum?->nama ?? '—';
            $tipe   = 'Koor';
        @endphp
        <div style="padding:11px 16px; border-bottom:1px solid #F8F9FB; display:flex; align-items:center; gap:10px; transition:background .12s;"
             onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
            <div style="width:32px; height:32px; border-radius:8px; background:#FFFBF0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="1.8" stroke-linecap="round">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:12px; font-weight:600; color:var(--c-fg,#0D0D12); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    Pendaftaran {{ $tipe }} · {{ $isPrak }}
                </div>
                <div style="font-size:11px; color:#A4ABB8; margin-top:1px;">
                    {{ $pend->user?->name ?? '—' }} · {{ $pend->created_at?->diffForHumans() }}
                </div>
            </div>
            <span class="mp-badge warning sm"><span class="dot"></span>Pending</span>
        </div>
        @empty
        <div style="padding:32px; text-align:center;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 8px;display:block;"><circle cx="12" cy="12" r="10"/><path d="M8 12l2.5 2.5L16 9"/></svg>
            <div style="font-size:12px; color:#A4ABB8;">Tidak ada pendaftaran koordinator yang menunggu.</div>
        </div>
        @endforelse
    </div>

    {{-- ── Panel 4: Pendaftaran Asprak ───────────────────────────── --}}
    <div style="background:#fff; border:1px solid var(--c-border,#DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:13px 16px; border-bottom:1px solid var(--c-border,#DFE1E7); background:#FAFAFA;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="font-size:14px; font-weight:700; color:var(--c-fg,#0D0D12);">Pendaftaran Asprak</div>
                @if(($totalAsprakPending ?? 0) > 0)
                <span style="font-size:10px; font-weight:700; background:#DF1C41; color:#fff; border-radius:999px; padding:1px 7px;">
                    {{ $totalAsprakPending ?? 0 }}
                </span>
                @endif
            </div>
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}"
               style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:6px; background:#0B266E; color:#fff; text-decoration:none;">Lihat</a>
        </div>
        @forelse($pendaftaranTerbaru ?? [] as $pend)
        @php
            $isPrak = $pend->praktikum?->nama ?? '—';
            $tipe   = 'Asprak';
        @endphp
        <div style="padding:11px 16px; border-bottom:1px solid #F8F9FB; display:flex; align-items:center; gap:10px; transition:background .12s;"
             onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
            <div style="width:32px; height:32px; border-radius:8px; background:#FADAE1; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="1.8" stroke-linecap="round">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:12px; font-weight:600; color:var(--c-fg,#0D0D12); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    Pendaftaran {{ $tipe }} · {{ $isPrak }}
                </div>
                <div style="font-size:11px; color:#A4ABB8; margin-top:1px;">
                    {{ $pend->user?->name ?? '—' }} · {{ $pend->created_at?->diffForHumans() }}
                </div>
            </div>
            <span class="mp-badge warning sm"><span class="dot"></span>Pending</span>
        </div>
        @empty
        <div style="padding:32px; text-align:center;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 8px;display:block;"><circle cx="12" cy="12" r="10"/><path d="M8 12l2.5 2.5L16 9"/></svg>
            <div style="font-size:12px; color:#A4ABB8;">Tidak ada pendaftaran asprak yang perlu ditinjau.</div>
        </div>
        @endforelse
    </div>

</div>

</x-eoffice::manajemen-praktikum.layout>