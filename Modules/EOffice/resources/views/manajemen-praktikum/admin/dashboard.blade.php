<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Admin — Manajemen Praktikum">
@php
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
    $name = auth()->user()->name;
@endphp

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Dashboard Admin</h1>
            <span class="mp-badge error sm">Admin</span>
        </div>
        <p class="mp-page-sub">Selamat datang, {{ $name }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}</p>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Perlu Tindakan</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ ($totalAsprakPending??0) + ($totalKoorPending??0) }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Praktikum Aktif</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ $totalPraktikumAktif ?? 0 }}</div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="mp-stats-grid cols-4">
    <div class="mp-stat">
        <div class="mp-stat-label">Praktikum Aktif</div>
        <div class="mp-stat-value">{{ $totalPraktikumAktif ?? 0 }}</div>
        <div class="mp-stat-sub">semester ini</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Mahasiswa</div>
        <div class="mp-stat-value">{{ $totalMahasiswa ?? 0 }}</div>
        <div class="mp-stat-sub">terdaftar di sistem</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Total Dosen</div>
        <div class="mp-stat-value">{{ $totalDosen ?? 0 }}</div>
        <div class="mp-stat-sub">dari tabel lecturers</div>
    </div>
    <div class="mp-stat">
        <div class="mp-stat-label">Asprak Pending</div>
        <div class="mp-stat-value">{{ $totalAsprakPending ?? 0 }}</div>
        <div class="mp-stat-sub">perlu review</div>
    </div>
</div>

{{-- Tabel Dosen & Mahasiswa --}}
<div class="flex gap-[14px]" style="flex-shrink:0;">

    {{-- Tabel Dosen --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Dosen Terdaftar · {{ $totalDosen ?? 0 }}</span>
            <a href="{{ route('eoffice.manprak.admin.dosen.index') }}" class="mp-btn secondary sm">Lihat Semua</a>
        </div>
        <div class="mp-card-body">
            <div style="display:flex;align-items:center;padding:8px 16px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <div class="mp-th flex-1">Nama</div>
                <div class="mp-th" style="width:110px;">NIP</div>
                <div class="mp-th" style="width:80px;">Praktikum</div>
            </div>
            <div style="overflow-y:auto;max-height:220px;">
                @forelse($dosenTerbaru ?? [] as $d)
                <div class="mp-tr" style="display:flex;align-items:center;padding:10px 16px;">
                    <div class="flex-1 flex items-center gap-[8px] min-w-0 pr-2">
                        <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#7B2FBE,#9B59B6);">
                            {{ strtoupper(substr($d['name'], 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $d['name'] }}</div>
                            <div style="font-size:10px;color:var(--c-fg-muted);" class="truncate">{{ $d['email'] }}</div>
                        </div>
                    </div>
                    <div style="width:110px;font-size:11px;color:var(--c-fg-muted);font-family:monospace;">{{ $d['employee_number'] }}</div>
                    <div style="width:80px;text-align:center;">
                        <span class="mp-badge neutral sm">{{ $d['jumlah_praktikum'] }} mk</span>
                    </div>
                </div>
                @empty
                <div style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Belum ada dosen terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Mahasiswa --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Mahasiswa Terdaftar · {{ $totalMahasiswa ?? 0 }}</span>
        </div>
        <div class="mp-card-body">
            <div style="display:flex;align-items:center;padding:8px 16px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <div class="mp-th flex-1">Nama</div>
                <div class="mp-th" style="width:90px;">NIM</div>
                <div class="mp-th" style="width:60px;">Angkatan</div>
                <div class="mp-th" style="width:80px;">Ikut</div>
            </div>
            <div style="overflow-y:auto;max-height:220px;">
                @forelse($mahasiswaTerbaru ?? [] as $m)
                <div class="mp-tr" style="display:flex;align-items:center;padding:10px 16px;">
                    <div class="flex-1 flex items-center gap-[8px] min-w-0 pr-2">
                        <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#106A97,#3C9DBE);">
                            {{ strtoupper(substr($m['name'], 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div style="font-size:12px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $m['name'] }}</div>
                            <div style="font-size:10px;color:var(--c-fg-muted);" class="truncate">{{ $m['email'] }}</div>
                        </div>
                    </div>
                    <div style="width:90px;font-size:11px;color:var(--c-fg-muted);font-family:monospace;">{{ $m['student_number'] }}</div>
                    <div style="width:60px;font-size:11px;color:var(--c-fg-muted);text-align:center;">{{ $m['cohort_year'] }}</div>
                    <div style="width:80px;text-align:center;">
                        <span class="mp-badge sky sm">{{ $m['jumlah_praktikum'] }} ikut</span>
                    </div>
                </div>
                @empty
                <div style="padding:32px;text-align:center;font-size:12px;color:var(--c-fg-placeholder);">Belum ada mahasiswa terdaftar.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- Bottom: Tabel Praktikum + Pendaftaran Pending --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Tabel Praktikum --}}
    <div class="mp-card min-w-0" style="flex:2;">
        <div class="mp-card-header" style="flex-shrink:0;">
            <div>
                <span class="mp-card-title">Daftar Praktikum</span>
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $semesterLabel }}</div>
            </div>
            <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="mp-btn secondary sm">Lihat Semua</a>
        </div>
        <div class="mp-card-body" style="flex-shrink:0;">
            <div style="display:flex;align-items:center;padding:8px 20px;background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <div class="mp-th" style="width:90px;">Kode</div>
                <div class="mp-th flex-1">Nama Praktikum</div>
                <div class="mp-th" style="width:150px;">Dosen</div>
                <div class="mp-th" style="width:75px;">Praktikan</div>
                <div class="mp-th" style="width:80px;">Status</div>
            </div>
        </div>
        <div style="overflow-y:auto;flex:1;">
            @forelse($praktikums ?? [] as $p)
            <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;cursor:pointer;"
                 onclick="window.location='{{ route('eoffice.manprak.admin.praktikum.show', $p->id) }}'">
                <div style="width:90px;font-size:12px;font-weight:700;color:#0B266E;font-family:monospace;">{{ $p->kode ?? '—' }}</div>
                <div class="flex-1 truncate pr-3" style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $p->nama }}</div>
                <div style="width:150px;font-size:12px;color:var(--c-fg-muted);" class="truncate">{{ $p->dosen?->name ?? '—' }}</div>
                <div style="width:75px;font-size:13px;font-weight:600;color:var(--c-fg);">{{ $p->daftar_praktikan_count ?? 0 }}</div>
                <div style="width:80px;">
                    @if($p->status === 'aktif')
                        <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                    @else
                        <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada data praktikum.</div>
            @endforelse
        </div>
    </div>

    {{-- Pendaftaran Pending --}}
    <div class="mp-card min-w-0 flex-1">
        <div class="mp-card-header" style="flex-shrink:0;">
            <div>
                <span class="mp-card-title">Pendaftaran Pending</span>
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Perlu persetujuan</div>
            </div>
            <a href="{{ route('eoffice.manprak.admin.pendaftaran-asprak.index') }}" class="mp-btn secondary sm">Lihat Semua</a>
        </div>
        <div style="overflow-y:auto;flex:1;">
            @forelse($pendaftaranTerbaru ?? [] as $pend)
            <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;padding:11px 16px;">
                <div class="flex items-center gap-[10px] min-w-0">
                    <div class="w-[32px] h-[32px] rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                        {{ strtoupper(substr($pend->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $pend->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);" class="truncate">{{ $pend->praktikum?->nama ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id) }}">
                        @csrf
                        <button type="submit" class="mp-btn ghost sm">✓</button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="mp-btn destructive sm">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Tidak ada pendaftaran pending.</div>
            @endforelse
        </div>
    </div>

</div>

</x-eoffice::manajemen-praktikum.layout>
