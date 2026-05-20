<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pendaftaran Asprak">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran Asisten Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran Asisten Praktikum untuk praktikum yang Anda koordinir · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
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

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum yang Anda Koordinir</span>
    </div>
    <div style="padding:24px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">
            Anda belum di-assign sebagai koordinator pada praktikum aktif manapun.
            Hubungi Dosen pengampu untuk melakukan penugasan.
        </div>
        @else
        <form method="GET" action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.index') }}" id="form-praktikum">
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
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#ECFDF3;">
            <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;">{{ $praktikumDipilih->nama }}</div>
            @if($praktikumDipilih->matkul)
            <span style="font-size:11px;color:#666D80;">{{ $praktikumDipilih->matkul->kode }} · Sem {{ $praktikumDipilih->semester }}</span>
            @endif
            @if($praktikumDipilih->dosen)
            <span style="font-size:11px;color:#666D80;">Dosen: {{ $praktikumDipilih->dosen->name }}</span>
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
    <span class="sec-title">Buka Periode Pendaftaran Asisten Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Form Pembukaan Periode</span>
    </div>
    <div style="padding:24px;">
        @php
            $periodeAktif = $periodeList->firstWhere('is_aktif', true);
        @endphp

        @if($periodeAktif && $periodeAktif->isSedangBuka())
        <div class="mp-alert warning" style="margin-bottom:16px;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;">Periode Asprak Sedang Dibuka</div>
            <div style="font-size:12px;color:#956321;margin-top:4px;">
                <strong>{{ $periodeAktif->nama }}</strong>
                @if($periodeAktif->ditutup_pada)
                · Ditutup otomatis: {{ $periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                @else
                · Tanpa batas waktu
                @endif
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $periodeAktif->id) }}"
                  style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran asprak sekarang?')">
                @csrf
                <button type="submit" class="mp-btn error sm">🔒 Tutup Periode Sekarang</button>
            </form>
        </div>
        @endif

        <form method="POST" action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.store') }}">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikumDipilih->id }}">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Nama Periode <span style="color:#666D80;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" class="mp-input w-full"
                           placeholder="cth. Pendaftaran Asprak Gasal 2025/2026"
                           value="{{ old('nama') }}">
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <div style="font-size:12px;color:#666D80;padding:8px;background:#F6F8FA;border-radius:8px;width:100%;">
                        💡 Hanya mahasiswa yang terlihat di menu Pendaftaran Asprak ketika periode ini aktif.
                        Membuka periode baru akan otomatis menutup periode aktif sebelumnya.
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
                📢 Buka Periode Pendaftaran Asisten Praktikum
            </button>
        </form>
    </div>
</div>

{{-- ── RIWAYAT PERIODE ─────────────────────────────────────────────────────── --}}
@if($periodeList->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode Pendaftaran Asprak</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Periode — {{ $praktikumDipilih->nama }}</span>
        <span class="mp-badge neutral sm">{{ $periodeList->count() }} periode</span>
    </div>
    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Nama Periode</th>
                    <th>Dibuka</th>
                    <th>Ditutup</th>
                    <th>Dibuka Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodeList as $p)
                <tr>
                    <td style="font-weight:600;">{{ $p->nama }}</td>
                    <td style="font-size:12px;">
                        {{ $p->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $p->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas' }}
                    </td>
                    <td style="font-size:12px;">{{ $p->dibukaOleh?->name ?? '—' }}</td>
                    <td>
                        @if($p->isSedangBuka())
                            <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                        @elseif($p->is_aktif)
                            <span class="mp-badge warning sm"><span class="dot"></span>Aktif (Belum Buka)</span>
                        @else
                            <span class="mp-badge neutral sm">Ditutup</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if($p->is_aktif)
                            <form method="POST" action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $p->id) }}"
                                  onsubmit="return confirm('Tutup periode ini?')">
                                @csrf
                                <button type="submit" class="mp-btn error xs">Tutup</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('eoffice.manprak.koordinator.periode-pendaftaran.destroy', $p->id) }}"
                                  onsubmit="return confirm('Hapus periode ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="mp-btn neutral xs">Hapus</button>
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

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Log Pengumuman Otomatis</span>
        <span class="mp-badge neutral sm">{{ $riwayatPengumuman->count() }} entri</span>
    </div>
    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Waktu</th>
                    <th>Visibilitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatPengumuman as $pg)
                <tr>
                    <td style="font-size:13px;">{{ $pg->judul }}</td>
                    <td>
                        @if($pg->tipe_sistem === 'buka')
                            <span class="mp-badge success sm">Buka</span>
                        @elseif($pg->tipe_sistem === 'tutup')
                            <span class="mp-badge error sm">Tutup</span>
                        @else
                            <span class="mp-badge neutral sm">{{ $pg->tipe_sistem }}</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $pg->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</td>
                    <td>
                        @if($pg->trashed())
                            <span class="mp-badge neutral sm">Disembunyikan</span>
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
