<x-eoffice::manajemen-praktikum.layout pageTitle="Seleksi Asprak">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Seleksi Asisten Praktikum</h1>
            <span class="mp-badge" style="background:#D0D6E9;color:#5D6DA2;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#5D6DA2;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Review dan seleksi calon asisten praktikum untuk praktikum yang Anda koordinatori · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if($periodeAktif)
<div class="mp-alert success flex-shrink-0">
    <strong>Pendaftaran Sedang Buka:</strong> {{ $periodeAktif->nama }}
    @if($periodeAktif->ditutup_pada)
    · Ditutup: {{ $periodeAktif->ditutup_pada->format('d M Y H:i') }}
    @endif
</div>
@else
<div class="mp-alert warning flex-shrink-0">
    <strong>Periode pendaftaran sedang tutup.</strong> Hubungi Admin untuk membuka periode pendaftaran baru.
</div>
@endif

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Asisten Praktikum</span>
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
            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Diterima</option>
            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <select name="sort" class="mp-input mp-select">
            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="ipk_tertinggi" {{ request('sort') == 'ipk_tertinggi' ? 'selected' : '' }}>IPK Tertinggi</option>
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
                    <th class="mp-th text-left" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">CV / Transkrip</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Jadwal</th>
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
                    <td style="padding:12px 16px;font-weight:700;color:{{ ($p->ipk ?? 0) >= 3.0 ? '#16a34a' : '#DF1C41' }};">
                        {{ number_format($p->ipk ?? 0, 2) }}
                    </td>
                    <td style="padding:12px 16px;color:#666D80;max-width:180px;">
                        <div class="line-clamp-2" style="font-size:12px;">{{ $p->motivasi ?? '—' }}</div>
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->cv_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->cv_path, 'eoffice') }}" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;" class="hover:underline">CV</a>
                        @endif
                        @if($p->transkrip_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice') }}" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;" class="hover:underline">Transkrip</a>
                        @endif
                        @if($p->berkas_cerc_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, 'eoffice') }}" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;margin-top:2px;" class="hover:underline">CERC</a>
                        @endif
                        @if(!$p->cv_path && !$p->transkrip_path && !$p->berkas_cerc_path)
                        <span style="font-size:11px;color:#808897;">—</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;font-size:11px;color:#666D80;">
                        {{ collect($p->jadwal ?? [])->join(', ') ?: '—' }}
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status_koor === 'disetujui')
                        <div>
                            <span class="mp-badge success sm"><span class="dot"></span>Disetujui Koordinator</span>
                            <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
                        </div>
                        @elseif($p->status_koor === 'ditolak' || $p->status === 'rejected')
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;">
                        @if($p->status_koor === 'menunggu')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.approve', $p->id) }}">
                                @csrf
                                <button type="submit" class="mp-btn primary sm">Terima</button>
                            </form>
                            <form method="POST" action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.reject', $p->id) }}" x-data="{ alasan: '' }">
                                @csrf
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        @else
                        <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pendaftar asprak.</div>
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
