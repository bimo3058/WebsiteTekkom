<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Koordinator">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pendaftaran Koordinator Praktikum</h1>
        <p class="mp-page-sub">Final approval setelah dosen menyetujui pendaftar</p>
    </div>
</div>

{{-- Flow info --}}
<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Mahasiswa mendaftar → Dosen pengampu review & setujui → Admin melakukan final approval → Role <code>koor_prak</code> di-assign otomatis.
    Anda hanya bisa approve jika dosen sudah menyetujui.
</div>

{{-- Filter --}}
<div class="flex gap-2 flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..."
               class="mp-input" style="width:200px;">
        <select name="status_dosen" class="mp-input mp-select">
            <option value="">Semua Status Dosen</option>
            <option value="menunggu"  {{ request('status_dosen')=='menunggu'  ? 'selected' : '' }}>Menunggu Review Dosen</option>
            <option value="disetujui" {{ request('status_dosen')=='disetujui' ? 'selected' : '' }}>Disetujui Dosen (Siap Approve)</option>
            <option value="ditolak"   {{ request('status_dosen')=='ditolak'   ? 'selected' : '' }}>Ditolak Dosen</option>
        </select>
        <select name="status" class="mp-input mp-select">
            <option value="">Semua Status Admin</option>
            <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <th class="mp-th text-left" style="padding:10px 16px;">Status Dosen</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Status Admin</th>
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
                    <td style="padding:12px 16px;font-weight:700;color:var(--c-fg);">{{ number_format($p->ipk, 2) }}</td>
                    <td style="padding:12px 16px;">
                        @if($p->status_dosen === 'disetujui')
                        <span class="mp-badge success sm">✓ Disetujui Dosen</span>
                        @elseif($p->status_dosen === 'ditolak')
                        <span class="mp-badge error sm">✗ Ditolak Dosen</span>
                        @else
                        <span class="mp-badge warning sm">⏳ Menunggu Dosen</span>
                        @endif
                        @if($p->catatan_dosen)
                        <div style="font-size:10px;color:var(--c-fg-muted);margin-top:2px;">{{ Str::limit($p->catatan_dosen, 40) }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'approved')
                        <span class="mp-badge success sm">Approved</span>
                        @elseif($p->status === 'rejected')
                        <span class="mp-badge error sm">Rejected</span>
                        @else
                        <span class="mp-badge neutral sm">Pending</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'pending' && $p->status_dosen === 'disetujui')
                        <div class="flex gap-2" x-data="{}">
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.approve', $p->id) }}">
                                @csrf
                                <button type="submit" class="mp-btn ghost sm">Final Approve</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        @elseif($p->status === 'pending' && $p->status_dosen === 'menunggu')
                        <span style="font-size:11px;color:var(--c-fg-placeholder);">Menunggu dosen review</span>
                        @elseif($p->status !== 'pending')
                        <span style="font-size:11px;color:var(--c-fg-placeholder);">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Tidak ada data pendaftaran.</td>
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
