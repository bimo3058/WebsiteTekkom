<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikan — IRS">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pendaftaran Praktikan</h1>
        <p class="mp-page-sub">Unggah Cetak IRS untuk diverifikasi Koordinator. Setelah disetujui, gunakan kode praktikum di dashboard untuk bergabung ke kelas.</p>
    </div>
</div>

<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Pilih praktikum yang sedang membuka pendaftaran → unggah PDF/JPG IRS → tunggu persetujuan koor → masukkan <strong>kode kelas</strong> di dashboard mahasiswa.
</div>

@if($praktikumBuka->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:32px;text-align:center;">
    <div style="font-size:13px;color:var(--c-fg-placeholder);">
        Saat ini tidak ada praktikum yang membuka pendaftaran praktikan.<br>
        Silakan cek lagi setelah Admin membuka periode pendaftaran jenis Praktikan.
    </div>
</div>
@else
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum dengan periode terbuka</span>
    </div>
    @foreach($praktikumBuka as $pk)
    @php $periode = $periodeByPraktikum[$pk->id] ?? null; @endphp
    <div class="mp-tr" style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid var(--c-border-light);">
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--c-fg);">{{ $pk->nama }}</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:4px;">
                @if($pk->matkul)<span style="font-family:monospace;font-size:11px;background:var(--c-bg-sub);padding:1px 6px;border-radius:4px;">{{ $pk->matkul->kode }}</span> · @endif
                {{ $pk->semester }} {{ $pk->tahun_ajaran }}
                @if($periode?->nama) · {{ $periode->nama }} @endif
            </div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.store') }}"
              enctype="multipart/form-data" class="flex flex-wrap gap-2 items-end">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $pk->id }}">
            <div>
                <label class="block text-[11px] font-semibold mb-1" style="color:var(--c-fg-sub);">Cetak IRS (PDF/JPG)</label>
                <input type="file" name="irs" required accept=".pdf,.jpg,.jpeg,.png" class="mp-input" style="max-width:220px;">
            </div>
            <button type="submit" class="mp-btn primary md">Kirim</button>
        </form>
    </div>
    @endforeach
</div>
@endif

@if($riwayat->isNotEmpty())
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat pengajuan</span>
    </div>
    @foreach($riwayat as $r)
    <div class="mp-tr" style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
        <div>
            <div style="font-weight:600;font-size:13px;color:var(--c-fg);">{{ $r->praktikum?->nama ?? '—' }}</div>
            <div style="font-size:11px;color:var(--c-fg-muted);">{{ $r->created_at?->format('d M Y H:i') }}</div>
        </div>
        <div style="text-align:right;">
            @if($r->status === 'approved')
            <span class="mp-badge success sm">Disetujui</span>
            <div style="font-size:11px;color:var(--c-fg-muted);margin-top:4px;">Gunakan kode di dashboard</div>
            @elseif($r->status === 'rejected')
            <span class="mp-badge error sm">Ditolak</span>
            <div style="font-size:11px;color:var(--c-fg-muted);margin-top:4px;max-width:280px;">{{ $r->alasan_penolakan }}</div>
            @else
            <span class="mp-badge warning sm">Menunggu</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="text-center flex-shrink-0">
    <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
       class="mp-btn ghost sm" style="text-decoration:none;">← Kembali ke dashboard</a>
</div>

</x-eoffice::manajemen-praktikum.layout>
