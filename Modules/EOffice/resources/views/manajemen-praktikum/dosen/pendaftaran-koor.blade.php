<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Koordinator">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
        <p class="mp-page-sub">Review pendaftar koor sesuai praktikum yang Anda ampu</p>
    </div>
</div>

<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Anda mereview data mahasiswa (IPK, motivasi) → Setujui/Tolak → Jika disetujui, masuk ke antrian Admin untuk final approval.
</div>

{{-- Filter --}}
<div class="flex gap-2 flex-wrap flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..."
               class="mp-input" style="width:180px;">
        <select name="praktikum_id" class="mp-input mp-select">
            <option value="">Semua Praktikum</option>
            @foreach($praktikumList as $pr)
            <option value="{{ $pr->id }}" {{ request('praktikum_id') == $pr->id ? 'selected' : '' }}>{{ $pr->nama }}</option>
            @endforeach
        </select>
        <select name="status_dosen" class="mp-input mp-select">
            <option value="">Semua Status</option>
            <option value="menunggu"  {{ request('status_dosen')=='menunggu'  ? 'selected' : '' }}>Menunggu Review</option>
            <option value="disetujui" {{ request('status_dosen')=='disetujui' ? 'selected' : '' }}>Sudah Disetujui</option>
            <option value="ditolak"   {{ request('status_dosen')=='ditolak'   ? 'selected' : '' }}>Ditolak</option>
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
                    <th class="mp-th text-left" style="padding:10px 16px;">Praktikum</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
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
                    </td>
                    <td style="padding:12px 16px;color:var(--c-fg-sub);">{{ $p->praktikum?->nama ?? '—' }}</td>
                    <td style="padding:12px 16px;font-weight:700;color:{{ ($p->ipk ?? 0) >= 3.0 ? '#40C4AA' : '#DF1C41' }};">
                        {{ number_format($p->ipk ?? 0, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:var(--c-fg-muted);max-width:200px;">
                        <div class="line-clamp-2" style="font-size:12px;">{{ $p->motivasi ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status_dosen === 'disetujui')
                        <span class="mp-badge success sm">Disetujui</span>
                        <div style="font-size:10px;color:var(--c-fg-muted);margin-top:2px;">Menunggu Admin</div>
                        @elseif($p->status_dosen === 'ditolak')
                        <span class="mp-badge error sm">Ditolak</span>
                        @else
                        <span class="mp-badge warning sm">Menunggu Review</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status_dosen === 'menunggu')
                        <div class="flex gap-2" x-data="{ catatan: '' }">
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id) }}">
                                @csrf
                                <input type="hidden" name="catatan_dosen" value="">
                                <button type="submit" class="mp-btn ghost sm">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        @else
                        <span style="font-size:11px;color:var(--c-fg-placeholder);">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Tidak ada pendaftaran untuk praktikum Anda.</td>
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
