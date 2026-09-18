<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Praktikan">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Praktikan</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Pilih mata kuliah praktikum, unggah IRS, lalu tunggu verifikasi Koordinator Praktikum.</p>
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

<div class="mp-card flex-shrink-0" style="margin-bottom:24px;">
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:18px;">
        <div style="padding:14px;border-radius:10px;background:#EEF2FF;">
            <div style="font-size:11px;color:#666D80;">Langkah 1</div>
            <div style="font-size:13px;font-weight:600;color:#0B266E;margin-top:4px;">Pilih praktikum</div>
        </div>
        <div style="padding:14px;border-radius:10px;background:#FFF7E6;">
            <div style="font-size:11px;color:#666D80;">Langkah 2</div>
            <div style="font-size:13px;font-weight:600;color:#956321;margin-top:4px;">Unggah IRS</div>
        </div>
        <div style="padding:14px;border-radius:10px;background:#ECFDF3;">
            <div style="font-size:11px;color:#666D80;">Langkah 3</div>
            <div style="font-size:13px;font-weight:600;color:#287F6E;margin-top:4px;">Tunggu verifikasi</div>
        </div>
    </div>
    <div style="border-top:1px solid #DFE1E7;padding:14px 18px;font-size:12px;color:#666D80;">
        Pastikan IRS mencantumkan mata kuliah praktikum yang dipilih. Format yang diterima: PDF, JPG, atau PNG; maksimal 5 MB.
    </div>
</div>

@if($praktikumBuka->isEmpty())
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:260px;">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 14px;display:block;"><path d="M6 2h9l3 3v17H6z"/><path d="M14 2v4h4M9 12h6M9 16h6"/></svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum ada pendaftaran yang dibuka</div>
        <div style="font-size:13px;color:#666D80;">Admin belum membuka periode pendaftaran praktikan untuk mata kuliah mana pun.</div>
    </div>
</div>
@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Mata Kuliah Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:18px;">
        <form method="GET" action="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.index') }}">
            @php
                $praktikumOptions = $praktikumBuka->map(function ($p) {
                    $matkul = $p->matkul
                        ? trim(($p->matkul->kode ? $p->matkul->kode . ' · ' : '') . $p->matkul->nama)
                        : $p->nama;
                    return [
                        'value' => (string) $p->id,
                        'label' => $matkul . " · {$p->semester} {$p->tahun_ajaran}",
                    ];
                })->values()->all();
            @endphp
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Mata Kuliah / Praktikum</label>
            <x-eoffice::manajemen-praktikum.ui.select
                name="praktikum_id"
                :options="$praktikumOptions"
                :selected="(string) request('praktikum_id', $praktikumBuka->first()?->id)"
                placeholder="Pilih Mata Kuliah..."
                onChange="$event.target.form.submit()"
                minWidth="320px"
            />
        </form>
    </div>
</div>

@php
    $selectedPraktikumId = request('praktikum_id', $praktikumBuka->first()?->id);
    $selectedPraktikum = $praktikumBuka->firstWhere('id', $selectedPraktikumId);
    $pendaftaranTerakhir = $selectedPraktikum
        ? $riwayat->firstWhere('praktikum_id', $selectedPraktikum->id)
        : null;
    $periode = $selectedPraktikum ? ($periodeByPraktikum[$selectedPraktikum->id] ?? null) : null;
@endphp

@if($selectedPraktikum)
<div style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(300px,.9fr);gap:16px;margin-top:24px;">
    <div>
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Detail Praktikum</span>
            <span class="sec-rule"></span>
        </div>
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                <div style="font-size:11px;color:#666D80;text-transform:uppercase;letter-spacing:.04em;">Mata Kuliah</div>
                <div style="font-size:18px;font-weight:700;color:#0D0D12;margin-top:4px;">
                    {{ $selectedPraktikum->matkul?->kode ?? $selectedPraktikum->kode ?? '—' }}
                </div>
                <div style="font-size:14px;color:#353849;margin-top:2px;">{{ $selectedPraktikum->matkul?->nama ?? $selectedPraktikum->nama }}</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;">
                    <span class="mp-badge neutral sm">{{ $selectedPraktikum->semester }} · {{ $selectedPraktikum->tahun_ajaran }}</span>
                    @if($periode?->ditutup_pada)
                    <span class="mp-badge success sm">Ditutup {{ $periode->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</span>
                    @endif
                </div>
                <div style="border-top:1px solid #DFE1E7;margin-top:18px;padding-top:14px;font-size:12px;color:#666D80;">
                    <div><strong style="color:#353849;">Praktikum:</strong> {{ $selectedPraktikum->nama }}</div>
                    <div style="margin-top:5px;"><strong style="color:#353849;">Dosen:</strong> {{ $selectedPraktikum->dosens->pluck('name')->join(', ') ?: 'Belum ditentukan' }}</div>
                    <div style="margin-top:5px;"><strong style="color:#353849;">Koordinator:</strong> {{ $selectedPraktikum->koordinator?->name ?? 'Belum ditentukan' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Status Pendaftaran</span>
            <span class="sec-rule"></span>
        </div>
        <div class="mp-card flex-shrink-0">
            <div style="padding:20px;">
                @if($pendaftaranTerakhir?->status === 'approved')
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                <div style="font-size:13px;font-weight:600;color:#287F6E;margin-top:10px;">Anda resmi terdaftar sebagai praktikan.</div>
                <div style="font-size:12px;color:#666D80;margin-top:5px;">Silakan buka Dashboard Praktikum untuk melihat modul, tugas, dan nilai.</div>
                @elseif($pendaftaranTerakhir?->status === 'pending')
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu verifikasi</span>
                <div style="font-size:12px;color:#666D80;margin-top:10px;">Koordinator sedang memeriksa IRS Anda.</div>
                @elseif($pendaftaranTerakhir?->status === 'rejected')
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                <div style="font-size:12px;color:#666D80;margin-top:10px;">{{ $pendaftaranTerakhir->alasan_penolakan ?: 'Berkas IRS belum memenuhi persyaratan.' }}</div>
                <div style="font-size:11px;color:#808897;margin-top:8px;">Anda dapat mengirim pendaftaran baru dengan IRS yang sesuai.</div>
                @else
                <span class="mp-badge neutral sm">Belum mendaftar</span>
                <div style="font-size:12px;color:#666D80;margin-top:10px;">Lengkapi formulir di bawah untuk mengajukan pendaftaran.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$pendaftaranTerakhir || $pendaftaranTerakhir->status === 'rejected')
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Kirim Pendaftaran</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header"><span class="mp-card-title">Unggah IRS</span></div>
    <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.store') }}" enctype="multipart/form-data" style="padding:20px;">
        @csrf
        <input type="hidden" name="praktikum_id" value="{{ $selectedPraktikum->id }}">
        <div style="padding:14px 16px;border-radius:10px;background:#F6F8FA;margin-bottom:16px;font-size:12px;color:#666D80;">
            <strong style="color:#353849;">Yang perlu disiapkan:</strong> Cetak IRS aktif yang mencantumkan mata kuliah praktikum ini.
        </div>
        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Berkas IRS <span style="color:#DF1C41;">*</span></label>
        <input type="file" name="irs" accept=".pdf,.jpg,.jpeg,.png" required class="mp-input" style="width:100%;">
        <div style="font-size:11px;color:#808897;margin-top:5px;">PDF/JPG/PNG, maksimal 5 MB.</div>
        <button type="submit" class="mp-btn primary md" style="margin-top:18px;" onclick="this.disabled=true;this.innerHTML='Mengirim...';">Kirim Pendaftaran</button>
    </form>
</div>
@endif
@endif

@if($riwayat->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Pendaftaran</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="overflow-x-auto">
        <table class="mp-table">
            <thead><tr><th>Praktikum</th><th>Dikirim</th><th>Status</th><th>Catatan</th></tr></thead>
            <tbody>
                @foreach($riwayat as $item)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $item->praktikum?->matkul?->nama ?? $item->praktikum?->nama ?? '—' }}</div>
                        <div style="font-size:11px;color:#808897;">{{ $item->praktikum?->semester }} · {{ $item->praktikum?->tahun_ajaran }}</div>
                    </td>
                    <td style="font-size:12px;">{{ $item->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</td>
                    <td>
                        @if($item->status === 'approved')
                        <span class="mp-badge success sm">Disetujui</span>
                        @elseif($item->status === 'rejected')
                        <span class="mp-badge error sm">Ditolak</span>
                        @else
                        <span class="mp-badge warning sm">Menunggu</span>
                        @endif
                    </td>
                    <td style="font-size:11px;color:#666D80;">{{ $item->alasan_penolakan ?: ($item->status === 'approved' ? 'IRS terverifikasi.' : 'Sedang diperiksa koordinator.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

@if($activePraktikum && $classmates->isNotEmpty())
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Teman Satu Praktikum</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="overflow-x-auto">
        <table class="mp-table">
            <thead><tr><th>Nama</th><th>Kelompok</th><th>Shift</th></tr></thead>
            <tbody>
                @foreach($classmates as $classmate)
                <tr>
                    <td style="font-weight:600;">{{ $classmate->user?->name ?? '—' }}</td>
                    <td>{{ $classmate->kelompok ?: 'Belum dibagi' }}</td>
                    <td>{{ $classmate->shift ?: 'Belum dibagi' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
