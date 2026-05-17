<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Praktikan — IRS">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Verifikasi Pendaftaran Praktikan</h1>
        <p class="mp-page-sub">Review Cetak IRS mahasiswa sebelum mereka dapat bergabung ke kelas dengan kode praktikum.</p>
    </div>
</div>

@if($periodeAktif)
<div class="mp-alert success flex-shrink-0">
    <strong>● Pendaftaran Praktikan Sedang Buka:</strong> {{ $periodeAktif->nama }}
    @if($periodeAktif->ditutup_pada)
    · Ditutup: {{ $periodeAktif->ditutup_pada->format('d M Y H:i') }}
    @endif
</div>
@else
<div class="mp-alert warning flex-shrink-0">
    <strong>⏸ Periode pendaftaran praktikan sedang tutup.</strong> Mahasiswa baru bisa mengirim IRS saat Admin membuka periode jenis Praktikan.
</div>
@endif

<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Mahasiswa unggah IRS → Anda setujui/tolak → jika disetujui role <code>praktikan</code> aktif & mahasiswa memasukkan <strong>kode praktikum</strong> di dashboard untuk masuk kelas.
</div>

<div class="flex gap-2 flex-wrap flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..."
               class="mp-input" style="width:180px;">
        <select name="status" class="mp-input mp-select">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">IRS</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)
                <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600;color:var(--c-fg);">{{ $p->user?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--c-fg-muted);">{{ $p->user?->email }}</div>
                        <div style="font-size:10px;color:var(--c-fg-placeholder);">{{ $p->created_at?->format('d M Y') }}</div>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->irs_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($p->irs_path) }}" target="_blank"
                           style="font-size:11px;font-weight:600;color:var(--c-primary);text-decoration:none;" class="hover:underline">Lihat berkas</a>
                        @else
                        <span style="font-size:11px;color:var(--c-fg-placeholder);">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'approved')
                        <span class="mp-badge success sm">Disetujui</span>
                        @elseif($p->status === 'rejected')
                        <span class="mp-badge error sm">Ditolak</span>
                        <div style="font-size:10px;color:var(--c-fg-muted);margin-top:4px;max-width:200px;">{{ \Illuminate\Support\Str::limit($p->alasan_penolakan ?? '', 80) }}</div>
                        @else
                        <span class="mp-badge warning sm">Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'pending')
                        <div class="flex flex-col gap-2 items-start">
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-praktikan.approve', $p->id) }}">
                                @csrf
                                <button type="submit" class="mp-btn ghost sm">Setujui IRS</button>
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
                        <span style="font-size:11px;color:var(--c-fg-placeholder);">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada pendaftaran praktikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pendaftaran->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--c-border);flex-shrink:0;">{{ $pendaftaran->links() }}</div>
    @endif
</div>

</x-eoffice::manajemen-praktikum.layout>
