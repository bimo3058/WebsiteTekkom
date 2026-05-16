<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Mahasiswa — Manajemen Praktikum">
@php $name = auth()->user()->name; @endphp

{{-- Banner: Belum terdaftar di kelas → daftar IRS dulu, lalu kode --}}
@if($belumTerdaftar)

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Selamat Datang, {{ $name }}!</h1>
            <span class="mp-badge warning sm">Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Ikuti langkah di bawah untuk bergabung ke kelas praktikum.</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.index') }}" class="mp-btn primary md" style="text-decoration:none;">Daftar &amp; Unggah IRS</a>
    </div>
</div>

<div class="mp-alert info flex-shrink-0">
    <strong>(1)</strong> Unggah Cetak IRS saat periode pendaftaran dibuka.
    <strong>(2)</strong> Setelah Koordinator menyetujui, masukkan <strong>kode praktikum</strong> dari Koordinator di Dashboard.
</div>

@if(isset($siapGabung) && $siapGabung->isNotEmpty())
<div class="mp-card flex-shrink-0" style="padding:20px;border-color:#D39C3D;">
    <div class="mp-card-title" style="margin-bottom:4px;">IRS sudah disetujui — tinggal gabung kelas</div>
    <div style="font-size:12px;color:var(--c-fg-muted);margin-bottom:16px;">Klik tombol "Bergabung" di bawah untuk langsung masuk ke kelas praktikum.</div>
    <div class="flex flex-col gap-3">
        @foreach($siapGabung as $sg)
        <div class="flex items-center justify-between gap-4 p-3 rounded-[10px]" style="border:1px solid var(--c-border);">
            <div class="min-w-0">
                <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $sg->praktikum?->nama }}</div>
                @if($sg->praktikum?->kode)
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">
                    Kode: <span style="font-family:monospace;font-weight:700;color:var(--c-primary);">{{ $sg->praktikum->kode }}</span>
                </div>
                @else
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Kode belum tersedia — hubungi Koordinator</div>
                @endif
            </div>
            @if($sg->praktikum?->kode)
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.masuk') }}" class="flex-shrink-0">
                @csrf
                <input type="hidden" name="kode" value="{{ $sg->praktikum->kode }}">
                <button type="submit" class="mp-btn primary md">Bergabung →</button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    {{-- Fallback manual untuk edge case (kode dari WhatsApp dll) --}}
    <details class="mt-4">
        <summary style="font-size:12px;color:var(--c-fg-muted);cursor:pointer;user-select:none;">Punya kode lain dari Koordinator?</summary>
        <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.masuk') }}" class="flex gap-2 mt-3 max-w-md">
            @csrf
            <input type="text" name="kode" placeholder="Masukkan kode praktikum..."
                   class="mp-input flex-1" value="{{ old('kode') }}">
            <button type="submit" class="mp-btn primary md">Gabung</button>
        </form>
        @error('kode')<div style="font-size:12px;color:#DF1C41;margin-top:6px;">{{ $message }}</div>@enderror
    </details>
</div>
@endif

@if(isset($pendaftaranPraktikanTerbaru) && $pendaftaranPraktikanTerbaru->isNotEmpty())
<div class="mp-card flex-shrink-0" style="padding:16px 20px;">
    <div style="font-weight:700;font-size:13px;color:var(--c-fg);margin-bottom:8px;">Status pendaftaran IRS</div>
    <ul class="flex flex-col gap-2">
        @foreach($pendaftaranPraktikanTerbaru as $pp)
        <li class="flex justify-between gap-2 flex-wrap">
            <span style="font-size:13px;color:var(--c-fg-sub);">{{ $pp->praktikum?->nama }}</span>
            @if($pp->status === 'pending')
            <span class="mp-badge warning sm">Menunggu koor</span>
            @elseif($pp->status === 'approved')
            <span class="mp-badge success sm">Disetujui</span>
            @else
            <span class="mp-badge error sm">Ditolak</span>
            @endif
        </li>
        @endforeach
    </ul>
    <a href="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.index') }}"
       style="font-size:12px;font-weight:600;color:var(--c-primary);margin-top:8px;display:inline-block;">Kelola pendaftaran →</a>
</div>
@endif

<div class="mp-card flex-shrink-0" style="padding:32px;text-align:center;">
    <div style="font-size:13px;color:var(--c-fg-placeholder);">Anda belum masuk ke kelas praktikum manapun.<br>Ikuti langkah di atas atau hubungi Koordinator jika kode belum tersedia.</div>
</div>

@else

{{-- Welcome Banner (sudah terdaftar) --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
            <h1 class="mp-page-title">Halo, {{ $name }}!</h1>
            <span class="mp-badge warning sm">Mahasiswa</span>
        </div>
        <p class="mp-page-sub">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            @if($terdaftarDi) · {{ $terdaftarDi->nama }} @endif
        </p>
    </div>
    @php
        $pct = $absensiStat['total'] > 0 ? round($absensiStat['hadir'] / $absensiStat['total'] * 100) : 0;
    @endphp
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Kehadiran</div>
            <div style="font-size:22px;font-weight:700;color:{{ $pct >= 75 ? '#40C4AA' : '#DF1C41' }};line-height:1;">{{ $pct }}%</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;color:var(--c-fg-muted);margin-bottom:2px;">Tugas Pending</div>
            <div style="font-size:22px;font-weight:700;color:var(--c-fg);line-height:1;">{{ count($tugasMendatang) }}</div>
        </div>
    </div>
</div>

@if(isset($siapGabung) && $siapGabung->isNotEmpty())
<div class="mp-alert warning flex-shrink-0">
    <div style="font-weight:700;margin-bottom:8px;">Anda disetujui untuk praktikum lain — langsung bergabung</div>
    <div class="flex flex-col gap-2">
        @foreach($siapGabung as $sg)
        <div class="flex items-center justify-between gap-3 p-3 bg-white rounded-[8px]" style="border:1px solid var(--c-border);">
            <div class="min-w-0">
                <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $sg->praktikum?->nama }}</div>
                @if($sg->praktikum?->kode)
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Kode: <span style="font-family:monospace;font-weight:700;color:var(--c-primary);">{{ $sg->praktikum->kode }}</span></div>
                @else
                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">Kode belum tersedia — hubungi Koordinator</div>
                @endif
            </div>
            @if($sg->praktikum?->kode)
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.masuk') }}" class="flex-shrink-0">
                @csrf
                <input type="hidden" name="kode" value="{{ $sg->praktikum->kode }}">
                <button type="submit" class="mp-btn primary sm">Bergabung →</button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="flex flex-wrap gap-2 flex-shrink-0">
    <a href="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.index') }}"
       class="mp-btn ghost sm" style="text-decoration:none;">+ Daftar praktikum lain (IRS)</a>
</div>

{{-- Info Praktikum --}}
@if($terdaftarDi)
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div class="flex items-start justify-between">
        <div>
            <div style="font-size:11px;font-weight:600;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Praktikum Aktif</div>
            <div style="font-size:18px;font-weight:700;color:var(--c-fg);">{{ $terdaftarDi->nama }}</div>
            <div class="flex items-center gap-3 mt-2 flex-wrap">
                <span style="font-size:12px;color:var(--c-fg-muted);">Kode: <span style="font-weight:600;color:var(--c-primary);">{{ $terdaftarDi->kode ?? '—' }}</span></span>
                <span style="font-size:12px;color:var(--c-fg-muted);">Dosen: <span style="font-weight:600;color:var(--c-fg);">{{ $terdaftarDi->dosen?->name ?? '—' }}</span></span>
                @if($terdaftarDi->koordinator)
                <span style="font-size:12px;color:var(--c-fg-muted);">Koor: <span style="font-weight:600;color:var(--c-fg);">{{ $terdaftarDi->koordinator->name }}</span></span>
                @endif
            </div>
        </div>
        <span class="mp-badge success sm">Aktif</span>
    </div>
</div>
@endif

{{-- Progress Kehadiran --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div style="font-weight:700;font-size:14px;color:var(--c-fg);">Statistik Kehadiran</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $absensiStat['hadir'] }} hadir dari {{ $absensiStat['total'] }} sesi</div>
        </div>
        <div style="font-size:24px;font-weight:700;color:{{ $pct >= 75 ? '#40C4AA' : '#DF1C41' }};">{{ $pct }}%</div>
    </div>
    <div style="width:100%;background:#F0F1F4;border-radius:999px;height:8px;">
        <div style="height:8px;border-radius:999px;width:{{ $pct }}%;background:{{ $pct >= 75 ? 'linear-gradient(90deg,#40C4AA,#0D6B55)' : 'linear-gradient(90deg,#DF1C41,#f87171)' }};"></div>
    </div>
    @if($pct < 75)
    <div style="margin-top:8px;font-size:11px;color:#DF1C41;font-weight:500;">Kehadiran di bawah 75% — risiko tidak lulus praktikum</div>
    @endif
</div>

{{-- Bottom Grid: Tugas + Nilai + Pengumuman --}}
<div class="flex gap-[14px] flex-1 min-h-0 mb-1">

    {{-- Tugas Mendatang --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Tugas Mendatang</span>
            <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}" class="mp-btn secondary sm" style="text-decoration:none;">Lihat Semua</a>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($tugasMendatang as $t)
            @php
                $dl = \Carbon\Carbon::parse($t['deadline']);
                $sisa = now()->diffInDays($dl, false);
                $sudah = $t['sudah_kumpul'];
            @endphp
            <div class="mp-tr" style="padding:11px 20px;border-bottom:1px solid var(--c-border-light);">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div style="font-size:13px;font-weight:600;color:var(--c-fg);" class="truncate">{{ $t['judul'] }}</div>
                        <div style="font-size:11px;margin-top:2px;color:{{ $sisa <= 2 && !$sudah ? '#DF1C41' : 'var(--c-fg-muted)' }};">
                            Deadline: {{ $dl->format('d M Y, H:i') }}
                        </div>
                    </div>
                    @if($sudah)
                    <span class="mp-badge success sm flex-shrink-0">Dikumpul</span>
                    @else
                    <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
                       class="mp-badge warning sm flex-shrink-0" style="text-decoration:none;">Kumpul</a>
                    @endif
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Tidak ada tugas mendatang.</div>
            @endforelse
        </div>
    </div>

    {{-- Nilai --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Nilai Praktikum</span>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($nilaiList as $n)
            @php
                $nilai = $n->nilai_akhir ?? null;
                $nilaiColor = !$nilai ? 'var(--c-fg-muted)' : ($nilai >= 75 ? '#40C4AA' : ($nilai >= 50 ? '#D39C3D' : '#DF1C41'));
            @endphp
            <div class="mp-tr flex items-center justify-between" style="padding:11px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-size:13px;font-weight:500;color:var(--c-fg);">{{ $n->modul ?? 'Modul' }}</div>
                <span style="font-size:16px;font-weight:700;color:{{ $nilaiColor }};">{{ $nilai ?? '—' }}</span>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Nilai belum tersedia.</div>
            @endforelse
        </div>
    </div>

    {{-- Pengumuman --}}
    <div class="mp-card flex-1 min-w-0">
        <div class="mp-card-header" style="flex-shrink:0;">
            <span class="mp-card-title">Pengumuman</span>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($pengumuman as $p)
            <div class="mp-tr" style="padding:11px 20px;border-bottom:1px solid var(--c-border-light);">
                <div style="font-size:13px;font-weight:600;color:var(--c-fg);">{{ $p->judul }}</div>
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;" class="line-clamp-2">{{ $p->konten }}</div>
                <div style="font-size:11px;color:var(--c-fg-placeholder);margin-top:4px;">{{ $p->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;font-size:13px;color:var(--c-fg-muted);">Belum ada pengumuman.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Status Pendaftaran Asprak --}}
@if($statusAsprak)
<div class="mp-card flex-shrink-0" style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Status Pendaftaran Asisten Praktikum</div>
        <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $statusAsprak->praktikum?->nama ?? '—' }}</div>
    </div>
    @if($statusAsprak->status === 'pending')
    <span class="mp-badge warning sm">Menunggu Review</span>
    @elseif($statusAsprak->status === 'approved')
    <span class="mp-badge success sm">Diterima</span>
    @else
    <span class="mp-badge error sm">Ditolak</span>
    @endif
</div>
@else
<div class="mp-card flex-shrink-0" style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Tertarik jadi Asisten Praktikum?</div>
        <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">Daftarkan diri Anda sebagai calon asprak semester ini.</div>
    </div>
    <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}" class="mp-btn primary md" style="text-decoration:none;">Daftar Sekarang</a>
</div>
@endif

@endif {{-- end if belumTerdaftar --}}

</x-eoffice::manajemen-praktikum.layout>
