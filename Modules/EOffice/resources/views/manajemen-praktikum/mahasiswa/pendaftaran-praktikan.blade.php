<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Praktikan">

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Praktikan</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Unggah Cetak IRS untuk diverifikasi Koordinator · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Alur Pendaftaran</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-alert warning flex-shrink-0">
    <strong>Alur:</strong> Pilih praktikum yang sedang membuka pendaftaran → unggah PDF/JPG IRS → tunggu persetujuan koordinator → masukkan <strong>kode kelas</strong> di dashboard mahasiswa.
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikum Tersedia</span>
    <span class="sec-rule"></span>
</div>

@if($praktikumBuka->isEmpty())
<div class="mp-card flex-shrink-0">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Saat ini tidak ada praktikum yang membuka pendaftaran praktikan.</div>
        <div style="font-size:12px;color:#808897;margin-top:6px;">Silakan cek lagi setelah Admin membuka periode pendaftaran jenis Praktikan.</div>
    </div>
</div>
@else
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum dengan periode terbuka</span>
    </div>
    @foreach($praktikumBuka as $pk)
    @php $periode = $periodeByPraktikum[$pk->id] ?? null; @endphp
    <div class="mp-tr" style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid #DFE1E7;">
        <div>
            <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $pk->nama }}</div>
            <div style="font-size:12px;color:#666D80;margin-top:4px;">
                @if($pk->matkul)
                <span style="font-family:monospace;font-size:11px;background:#F6F8FA;padding:1px 6px;border-radius:4px;border:1px solid #DFE1E7;">{{ $pk->matkul->kode }}</span> ·
                @endif
                {{ $pk->semester }} {{ $pk->tahun_ajaran }}
                @if($periode?->nama) · {{ $periode->nama }} @endif
            </div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.store') }}"
              enctype="multipart/form-data" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $pk->id }}">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#353849;margin-bottom:4px;">Cetak IRS (PDF/JPG)</label>
                <input type="file" name="irs" required accept=".pdf,.jpg,.jpeg,.png" class="mp-input" style="max-width:220px;">
            </div>
            <button type="submit" class="mp-btn primary md">Kirim</button>
        </form>
    </div>
    @endforeach
</div>
@endif

@if($riwayat->isNotEmpty())
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Pengajuan</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat pengajuan</span>
    </div>
    <table class="mp-table">
        <thead>
            <tr style="background:#F9FAFB;">
                <th>Praktikum</th>
                <th>Tanggal Pengajuan</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $r)
            <tr class="mp-tr">
                <td>
                    <div style="font-weight:600;font-size:13px;color:#0D0D12;">{{ $r->praktikum?->nama ?? '—' }}</div>
                </td>
                <td>
                    <div style="font-size:12px;color:#666D80;">{{ $r->created_at?->format('d M Y H:i') }}</div>
                </td>
                <td>
                    @if($r->status === 'approved')
                    <span class="mp-badge navy sm"><span class="dot"></span>Disetujui</span>
                    @elseif($r->status === 'rejected')
                    <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                    @else
                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                    @endif
                </td>
                <td>
                    @if($r->status === 'approved')
                    <span style="font-size:11px;color:#666D80;">Gunakan kode di dashboard</span>
                    @elseif($r->status === 'rejected' && $r->alasan_penolakan)
                    <span style="font-size:11px;color:#666D80;max-width:280px;display:block;">{{ $r->alasan_penolakan }}</span>
                    @else
                    <span style="font-size:11px;color:#808897;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="text-align:center;" class="flex-shrink-0">
    <a href="{{ route('eoffice.manprak.mahasiswa.dashboard') }}"
       class="mp-btn ghost sm" style="text-decoration:none;">← Kembali ke dashboard</a>
</div>

</x-eoffice::manajemen-praktikum.layout>
