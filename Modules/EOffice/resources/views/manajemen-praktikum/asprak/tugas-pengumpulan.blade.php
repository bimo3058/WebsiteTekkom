<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumpulan Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">{{ $tugas->judul }}</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">{{ $tugas->modul?->nama }} · {{ $tugas->modul?->praktikum?->nama }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumpulan</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumpulan->count() }} pengumpulan</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Pengumpulan</span>
        <div class="right">
            <span style="font-size:12px;color:#666D80;">{{ $pengumpulan->count() }} mahasiswa mengumpulkan</span>
        </div>
    </div>

    <table class="mp-table">
        <thead>
            <tr style="background:#F9FAFB;">
                <th style="padding:10px 16px;text-align:left;">Mahasiswa</th>
                <th style="padding:10px 16px;text-align:left;">Status</th>
                <th style="padding:10px 16px;text-align:left;">File</th>
                <th style="padding:10px 16px;text-align:left;">Nilai</th>
                <th style="padding:10px 16px;text-align:left;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($pengumpulan as $p)
            @php $st = $p->status_pengumpulan ?? 'belum_dicek'; @endphp
            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                <td style="padding:12px 16px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="mp-av yellow">{{ strtoupper(substr($p->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}</div>
                        <div>
                            <div style="font-weight:600;color:#0D0D12;">{{ $p->daftarPraktikan?->user?->name ?? '-' }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $p->created_at?->format('d M Y H:i') }}</div>
                            @if($p->catatan)
                            <div style="font-size:12px;color:#666D80;margin-top:2px;">{{ $p->catatan }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td style="padding:12px 16px;">
                    @if($st === 'acc')
                    <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                    @elseif($st === 'revisi')
                    <span class="mp-badge error sm"><span class="dot"></span>Revisi</span>
                    @else
                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                    @endif
                </td>
                <td style="padding:12px 16px;">
                    @if($p->file_path)
                    <a href="{{ Storage::url($p->file_path) }}" target="_blank"
                       style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;">Unduh</a>
                    @else
                    <span style="font-size:12px;color:#808897;">—</span>
                    @endif
                </td>
                <td style="padding:12px 16px;font-weight:700;color:#0D0D12;">
                    @if($p->nilai !== null)
                    <span style="color:{{ $p->nilai >= 75 ? '#0B266E' : ($p->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $p->nilai }}</span>
                    @else
                    <span style="color:#808897;">—</span>
                    @endif
                </td>
                <td style="padding:12px 16px;">
                    <div style="display:flex;gap:8px;align-items:center;">
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $p->id) }}" style="display:flex;gap:4px;">
                            @csrf
                            <input type="number" name="nilai" min="0" max="100" value="{{ $p->nilai }}"
                                   class="mp-input" style="width:72px;font-size:12px;padding:4px 8px;" placeholder="0–100">
                            <button class="mp-btn ghost sm">ACC</button>
                        </form>
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $p->id) }}" x-data="{ catatan: '' }">
                            @csrf
                            <input type="hidden" name="catatan_revisi" :value="catatan">
                            <button type="button"
                                    @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()"
                                    class="mp-btn secondary sm">Revisi</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pengumpulan.</div>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

</x-eoffice::manajemen-praktikum.layout>
