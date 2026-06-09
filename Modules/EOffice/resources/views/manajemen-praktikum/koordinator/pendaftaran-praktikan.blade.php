<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Praktikan — IRS">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Verifikasi Pendaftaran Praktikan</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Review Cetak IRS mahasiswa sebelum mereka dapat bergabung ke kelas · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if($periodeAktif)
<div class="mp-alert success flex-shrink-0">
    <strong>Pendaftaran Praktikan Sedang Buka:</strong> {{ $periodeAktif->nama }}
    @if($periodeAktif->ditutup_pada)
    · Ditutup: {{ $periodeAktif->ditutup_pada->format('d M Y H:i') }}
    @endif
</div>
@else
<div class="mp-alert warning flex-shrink-0">
    <strong>Periode pendaftaran praktikan sedang tutup.</strong> Mahasiswa baru bisa mengirim IRS saat Admin membuka periode jenis Praktikan.
</div>
@endif

<div class="mp-alert warning flex-shrink-0">
    <strong>Alur:</strong> Mahasiswa unggah IRS &rarr; Anda setujui/tolak &rarr; jika disetujui role <code>praktikan</code> aktif &amp; mahasiswa memasukkan <strong>kode praktikum</strong> di dashboard untuk masuk kelas.
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Praktikan</span>
    <span class="sec-rule"></span>
</div>

{{-- Filter --}}
<div class="flex gap-2 flex-wrap flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..."
               class="mp-input" style="width:180px;">
        <select name="status" class="mp-input mp-select">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="mp-table">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Berkas IRS</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-[10px]">
                            <div class="mp-av yellow">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                                <div style="font-size:10px;color:#808897;">{{ $p->created_at?->format('d M Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->irs_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($p->irs_path) }}" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;" class="hover:underline">Lihat berkas</a>
                        @else
                        <span style="font-size:11px;color:#808897;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'approved')
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                        @elseif($p->status === 'rejected')
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <div style="font-size:10px;color:#666D80;margin-top:4px;max-width:200px;">{{ \Illuminate\Support\Str::limit($p->alasan_penolakan ?? '', 80) }}</div>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'pending')
                        <div class="flex flex-col gap-2 items-start">
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-praktikan.approve', $p->id) }}">
                                @csrf
                                <button type="submit" class="mp-btn primary sm">Setujui IRS</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-praktikan.reject-irs-default', $p->id) }}"
                                  onsubmit="return confirm('Tolak dengan alasan standar (belum ambil IRS praktikum)?');">
                                @csrf
                                <button type="submit" class="mp-btn secondary sm">Tolak (IRS tidak valid)</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-praktikan.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan (opsional):'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">Tolak manual…</button>
                            </form>
                        </div>
                        @else
                        <span style="font-size:11px;color:#808897;">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pendaftaran praktikan.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pendaftaran->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
