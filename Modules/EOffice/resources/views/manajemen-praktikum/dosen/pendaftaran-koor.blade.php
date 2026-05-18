<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Koordinator">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
        <p class="mp-page-sub">Review pendaftar koor sesuai praktikum yang Anda ampu</p>
    </div>
</div>

{{-- Info Alert --}}
<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Anda mereview data mahasiswa (IPK, motivasi) → Setujui/Tolak → Jika disetujui, masuk ke antrian Admin untuk final approval.
</div>

{{-- Section: Filter --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter Pendaftaran</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Pencarian & Filter</span>
    </div>
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..."
                   class="mp-input" style="width:200px;">
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
            <button type="submit" class="mp-btn primary sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
        </form>
    </div>
</div>

{{-- Section: Daftar Pendaftar --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm">{{ $pendaftaran->total() }} pendaftar</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaran as $p)
                @php
                    $namaParts = explode(' ', $p->user?->name ?? 'UN');
                    $initials = strtoupper(substr($namaParts[0] ?? 'U', 0, 1) . substr($namaParts[1] ?? $namaParts[0] ?? 'N', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet','red'];
                    $avColor = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                    $ipkOk = ($p->ipk ?? 0) >= 3.0;
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av {{ $avColor }}">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;">{{ $p->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#353849;">{{ $p->praktikum?->nama ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="font-weight:700;color:{{ $ipkOk ? '#40C4AA' : '#DF1C41' }};">{{ number_format($p->ipk ?? 0, 2) }}</span>
                    </td>
                    <td style="padding:12px 16px;color:#666D80;max-width:200px;">
                        <div class="line-clamp-2" style="font-size:12px;">{{ $p->motivasi ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($p->status_dosen === 'disetujui')
                        <div>
                            <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                            <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
                        </div>
                        @elseif($p->status_dosen === 'ditolak')
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status_dosen === 'menunggu')
                        <div class="flex gap-2" x-data="{ catatan: '' }">
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id) }}">
                                @csrf
                                <input type="hidden" name="catatan_dosen" value="">
                                <button type="submit" class="mp-btn ghost sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @else
                        <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
                        </svg>
                        <div style="font-size:13px;color:#666D80;">Tidak ada pendaftaran untuk praktikum Anda.</div>
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
