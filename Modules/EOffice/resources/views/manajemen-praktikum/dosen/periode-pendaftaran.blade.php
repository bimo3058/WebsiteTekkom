<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pendaftaran Koordinator">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran Koordinator</h1>
            <span class="mp-badge info sm"><span class="dot"></span>Dosen</span>
        </div>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran Koordinator Praktikum untuk matkul yang Anda ampu · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(session('success'))
<div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif

{{-- ── PILIH PRAKTIKUM ──────────────────────────────────────────────────────── --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="flex-shrink:0;">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum yang Anda Ampu</span>
    </div>
    <div style="padding:24px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">
            Anda belum mengampu praktikum aktif. Hubungi Admin untuk assign praktikum ke Anda.
        </div>
        @else
        <form method="GET" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.index') }}" id="form-praktikum">
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                Pilih Praktikum <span style="color:#DF1C41;">*</span>
            </label>
            <select name="praktikum_id" onchange="document.getElementById('form-praktikum').submit()"
                    class="mp-input mp-select w-full">
                @foreach($praktikumList as $p)
                <option value="{{ $p->id }}" {{ $praktikumId == $p->id ? 'selected' : '' }}>
                    {{ $p->nama }}
                    @if($p->matkul) — [{{ $p->matkul->kode }}] {{ $p->matkul->nama }} @endif
                    · Sem {{ $p->semester }} {{ $p->tahun_ajaran }}
                </option>
                @endforeach
            </select>
        </form>

        @if($praktikumDipilih)
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;">
            <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;">{{ $praktikumDipilih->nama }}</div>
            @if($praktikumDipilih->matkul)
            <span style="font-size:11px;color:#666D80;">{{ $praktikumDipilih->matkul->kode }} · Sem {{ $praktikumDipilih->semester }}</span>
            @endif
            @if($praktikumDipilih->koordinator)
            <span style="font-size:11px;color:#666D80;">Koor: {{ $praktikumDipilih->koordinator->name }}</span>
            @else
            <span class="mp-badge warning sm">Belum Ada Koordinator</span>
            @endif
        </div>
        @endif
        @endif
    </div>
</div>

@if($praktikumDipilih)

{{-- ── BUKA PERIODE BARU ────────────────────────────────────────────────────── --}}
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Periode Pendaftaran Koordinator</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="flex-shrink:0;">
    <div class="mp-card-header">
        <span class="mp-card-title">Form Pembukaan Periode</span>
    </div>
    <div style="padding:24px;">
        @php
            $periodeAktif = $periodeList->firstWhere('is_aktif', true);
        @endphp

        @if($periodeAktif && $periodeAktif->isSedangBuka())
        <div class="mp-alert warning" style="margin-bottom:16px;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;">Periode Koordinator Sedang Dibuka</div>
            <div style="font-size:12px;color:#956321;margin-top:4px;">
                <strong>{{ $periodeAktif->nama }}</strong>
                @if($periodeAktif->ditutup_pada)
                · Ditutup otomatis: {{ $periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                @else
                · Tanpa batas waktu
                @endif
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.tutup', $periodeAktif->id) }}"
                  style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran koor sekarang?')">
                @csrf
                <button type="submit" class="mp-btn error sm">🔒 Tutup Periode Sekarang</button>
            </form>
        </div>
        @endif

        <form method="POST" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.store') }}">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikumDipilih->id }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Nama Periode <span style="color:#666D80;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" class="mp-input w-full"
                           placeholder="cth. Pendaftaran Koordinator Gasal 2025/2026"
                           value="{{ old('nama') }}">
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <div style="font-size:12px;color:#666D80;padding:8px;background:#F6F8FA;border-radius:8px;width:100%;">
                        💡 Membuka periode ini akan otomatis menutup periode koor aktif sebelumnya untuk praktikum ini.
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Dibuka Pada
                    </label>
                    <input type="datetime-local" name="dibuka_pada" class="mp-input w-full"
                           value="{{ old('dibuka_pada', now()->format('Y-m-d\TH:i')) }}">
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan untuk langsung dibuka saat ini.</p>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Ditutup Pada <span style="color:#666D80;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="datetime-local" name="ditutup_pada" class="mp-input w-full"
                           value="{{ old('ditutup_pada') }}">
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan jika tidak ada batas waktu otomatis.</p>
                </div>
            </div>

            <button type="submit" class="mp-btn primary md">
                📢 Buka Periode Pendaftaran Koordinator
            </button>
        </form>
    </div>
</div>

{{-- ── RIWAYAT PERIODE ─────────────────────────────────────────────────────── --}}
@if($periodeList->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode Pendaftaran Koordinator</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="flex-shrink:0;">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Periode — {{ $praktikumDipilih->nama }}</span>
        <span class="mp-badge neutral sm">{{ $periodeList->count() }} periode</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:680px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Periode</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Dibuka</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Ditutup</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:130px;">Dibuka Oleh</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:130px;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodeList as $p)
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:12px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);">{{ $p->nama }}</td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                        {{ $p->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                    </td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                        {{ $p->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas' }}
                    </td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">{{ $p->dibukaOleh?->name ?? '—' }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        @if($p->isSedangBuka())
                            <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                        @elseif($p->is_aktif)
                            <span class="mp-badge warning sm"><span class="dot"></span>Aktif (Belum Buka)</span>
                        @else
                            <span class="mp-badge neutral sm">Ditutup</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;">
                            @if($p->is_aktif)
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.tutup', $p->id) }}"
                                  onsubmit="return confirm('Tutup periode ini?')">
                                @csrf
                                <button type="submit" class="mp-btn destructive sm" style="padding:5px 10px; font-size:11px;">Tutup</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.periode-pendaftaran.destroy', $p->id) }}"
                                  onsubmit="return confirm('Hapus periode ini? Data tidak dapat dikembalikan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="mp-btn secondary sm" style="padding:5px 10px; font-size:11px;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── RIWAYAT PENGUMUMAN ───────────────────────────────────────────────────── --}}
@if($riwayatPengumuman->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Pengumuman Sistem</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card" style="flex-shrink:0;">
    <div class="mp-card-header">
        <span class="mp-card-title">Log Pengumuman Otomatis</span>
        <span class="mp-badge neutral sm">{{ $riwayatPengumuman->count() }} entri</span>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:500px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Judul</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">Tipe</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Waktu</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">Visibilitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatPengumuman as $pg)
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:12px 16px; font-size:13px; color:var(--c-fg, #0D0D12);">{{ $pg->judul }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        @if($pg->tipe_sistem === 'buka')
                            <span class="mp-badge success sm">Buka</span>
                        @elseif($pg->tipe_sistem === 'tutup')
                            <span class="mp-badge error sm">Tutup</span>
                        @else
                            <span class="mp-badge neutral sm">{{ $pg->tipe_sistem }}</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">{{ $pg->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</td>
                    <td style="padding:12px 16px; text-align:center;">
                        @if($pg->trashed())
                            <span class="mp-badge neutral sm" title="Soft deleted — tidak tampil di user">Disembunyikan</span>
                        @else
                            <span class="mp-badge success sm">Tampil</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif {{-- end if praktikumDipilih --}}

</x-eoffice::manajemen-praktikum.layout>
