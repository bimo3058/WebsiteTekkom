<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Koordinator">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Koordinator Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Final approval setelah dosen menyetujui pendaftar · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

{{-- Section title --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Alur Persetujuan Koordinator</span>
    <span class="sec-rule"></span>
</div>

{{-- Flow info --}}
<div class="mp-alert warning flex-shrink-0">
    <strong>Alur:</strong> Mahasiswa mendaftar &rarr; Dosen pengampu review &amp; setujui &rarr; Admin melakukan final approval &rarr; Role <code>koor_prak</code> di-assign otomatis.
    Anda hanya bisa approve jika dosen sudah menyetujui.
</div>

{{-- Filter --}}
<div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;">
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
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
            <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>Menunggu</option>
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div style="overflow-x:auto;flex:1;">
        <table class="mp-table">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th style="padding:10px 16px;text-align:left;">Mahasiswa</th>
                    <th style="padding:10px 16px;text-align:left;">Praktikum</th>
                    <th style="padding:10px 16px;text-align:left;">IPK</th>
                    <th style="padding:10px 16px;text-align:left;">Status Dosen</th>
                    <th style="padding:10px 16px;text-align:left;">Status Admin</th>
                    <th style="padding:10px 16px;text-align:left;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)
                <tr class="mp-tr">
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="mp-av violet">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 1)) }}{{ strtoupper(substr($p->user?->name ?? 'M', strpos(($p->user?->name ?? 'M').' ', ' ')+1, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;font-size:13px;">{{ $p->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#353849;font-size:13px;">{{ $p->praktikum?->nama ?? '—' }}</td>
                    <td style="padding:12px 16px;font-weight:700;color:#0D0D12;font-size:13px;">{{ number_format($p->ipk, 2) }}</td>
                    <td style="padding:12px 16px;">
                        @if($p->status_dosen === 'disetujui')
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui Dosen</span>
                        @elseif($p->status_dosen === 'ditolak')
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak Dosen</span>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Dosen</span>
                        @endif
                        @if($p->catatan_dosen)
                        <div style="font-size:10px;color:#666D80;margin-top:2px;">{{ Str::limit($p->catatan_dosen, 40) }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'approved')
                        <span class="mp-badge navy sm"><span class="dot"></span>Disetujui</span>
                        @elseif($p->status === 'rejected')
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        @else
                        <span class="mp-badge neutral sm">Menunggu</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status === 'pending' && $p->status_dosen === 'disetujui')
                        <div style="display:flex;gap:8px;" x-data="{}">
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.approve', $p->id) }}">
                                @csrf
                                <button type="submit" class="mp-btn primary sm">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.admin.pendaftaran-koor.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Tolak</button>
                            </form>
                        </div>
                        @elseif($p->status === 'pending' && $p->status_dosen === 'menunggu')
                        <span style="font-size:11px;color:#808897;font-style:italic;">Menunggu dosen review</span>
                        @elseif($p->status !== 'pending')
                        <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada data pendaftaran.</div>
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
