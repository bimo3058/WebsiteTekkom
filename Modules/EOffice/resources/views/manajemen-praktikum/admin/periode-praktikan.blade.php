<x-eoffice::manajemen-praktikum.layout pageTitle="Periode Pendaftaran Praktikan">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran Praktikan</h1>
            <span class="mp-badge info sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Buka atau tutup pendaftaran mahasiswa untuk setiap mata kuliah praktikum.</p>
    </div>
</div>

@if(session('success'))
<div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">
    <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum Aktif</span>
    </div>
    <div style="padding:24px;">
        @if($praktikumList->isEmpty())
        <div class="mp-alert warning">Belum ada praktikum aktif. Buat atau aktifkan praktikum terlebih dahulu.</div>
        @else
        <form method="GET" action="{{ route('eoffice.manprak.admin.periode-praktikan.index') }}">
            @php
                $praktikumOptions = $praktikumList->map(function ($p) {
                    $matkul = $p->matkul
                        ? trim(($p->matkul->kode ? $p->matkul->kode . ' · ' : '') . $p->matkul->nama)
                        : $p->nama;
                    return [
                        'value' => (string) $p->id,
                        'label' => $matkul . " · {$p->semester} {$p->tahun_ajaran}",
                    ];
                })->values()->all();
            @endphp
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Mata Kuliah / Praktikum</label>
            <x-eoffice::manajemen-praktikum.ui.select
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string) $praktikumId"
                placeholder="Pilih Praktikum..."
                onChange="$event.target.form.submit()"
                minWidth="280px"
            />
        </form>

        @if($praktikumDipilih)
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;font-size:13px;font-weight:600;color:#0D0D12;">{{ $praktikumDipilih->nama }}</div>
            @if($praktikumDipilih->matkul)
            <span style="font-size:11px;color:#666D80;">{{ $praktikumDipilih->matkul->kode }} · {{ $praktikumDipilih->matkul->nama }}</span>
            @endif
            <span style="font-size:11px;color:#666D80;">{{ $praktikumDipilih->semester }} · {{ $praktikumDipilih->tahun_ajaran }}</span>
        </div>
        @endif
        @endif
    </div>
</div>

@if($praktikumDipilih)
@php $periodeAktif = $periodeList->firstWhere('is_aktif', true); @endphp

<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Pendaftaran Praktikan</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">{{ $praktikumDipilih->nama }}</span>
    </div>
    <div style="padding:24px;">
        @if($periodeAktif && $periodeAktif->isSedangBuka())
        <div class="mp-alert success" style="margin-bottom:16px;">
            <div style="font-weight:600;">Pendaftaran sedang dibuka</div>
            <div style="font-size:12px;margin-top:4px;">
                {{ $periodeAktif->nama }}
                @if($periodeAktif->ditutup_pada)
                · ditutup {{ $periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                @endif
            </div>
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-praktikan.tutup', $periodeAktif->id) }}" style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran praktikan sekarang?')">
                @csrf
                <button type="submit" class="mp-btn error sm">Tutup Periode</button>
            </form>
        </div>
        @elseif($periodeAktif)
        <div class="mp-alert warning" style="margin-bottom:16px;">Periode sudah dijadwalkan dan akan dibuka pada {{ $periodeAktif->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'sekarang' }}.</div>
        @endif

        <form method="POST" action="{{ route('eoffice.manprak.admin.periode-praktikan.store') }}">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikumDipilih->id }}">
            <div class="mp-form-grid" style="margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Nama Periode <span style="font-weight:400;color:#666D80;">(opsional)</span></label>
                    <input type="text" name="nama" class="mp-input w-full" value="{{ old('nama') }}" placeholder="Pendaftaran Praktikan Semester Ganjil">
                </div>
                <div style="font-size:12px;color:#666D80;padding:10px;background:#F6F8FA;border-radius:8px;align-self:end;">
                    Membuka periode baru otomatis menutup periode praktikan sebelumnya untuk mata kuliah ini.
                </div>
            </div>
            <div class="mp-form-grid" style="margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Dibuka Pada</label>
                    <input type="datetime-local" name="dibuka_pada" class="mp-input w-full" value="{{ old('dibuka_pada', now()->format('Y-m-d\TH:i')) }}">
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan untuk langsung dibuka.</p>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                    <input type="datetime-local" name="ditutup_pada" class="mp-input w-full" value="{{ old('ditutup_pada') }}" required>
                </div>
            </div>
            <button type="submit" class="mp-btn primary md">Buka Pendaftaran Praktikan</button>
        </form>
    </div>
</div>

@if($periodeList->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="overflow-x-auto">
        <table class="mp-table">
            <thead><tr><th>Nama</th><th>Dibuka</th><th>Ditutup</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($periodeList as $periode)
                <tr>
                    <td style="font-weight:600;">{{ $periode->nama }}</td>
                    <td style="font-size:12px;">{{ $periode->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Sekarang' }}</td>
                    <td style="font-size:12px;">{{ $periode->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas' }}</td>
                    <td>
                        @if($periode->isSedangBuka())
                        <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                        @elseif($periode->is_aktif)
                        <span class="mp-badge warning sm"><span class="dot"></span>Terjadwal</span>
                        @else
                        <span class="mp-badge neutral sm">Ditutup</span>
                        @endif
                    </td>
                    <td>
                        @if($periode->is_aktif)
                        <form method="POST" action="{{ route('eoffice.manprak.admin.periode-praktikan.tutup', $periode->id) }}" onsubmit="return confirm('Tutup periode ini?')">
                            @csrf
                            <button type="submit" class="mp-btn error xs">Tutup</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('eoffice.manprak.admin.periode-praktikan.destroy', $periode->id) }}" onsubmit="return confirm('Hapus riwayat periode ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mp-btn neutral xs">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

</x-eoffice::manajemen-praktikum.layout>
