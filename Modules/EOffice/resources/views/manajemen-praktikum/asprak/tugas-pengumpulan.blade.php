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

{{-- Info Section --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;margin-bottom:24px;">
    @php
        $dl = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
        $lewat = $dl && now()->gt($dl);
    @endphp
    <div class="mp-card" style="padding:16px;border:1px solid #DFE1E7;">
        <div style="font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Deadline</div>
        <div style="font-size:14px;font-weight:700;color:{{ $lewat ? '#999' : '#0D0D12' }};">
            @if($dl)
                {{ $dl->locale('id')->format('d M Y, H:i') }}
                @if($lewat)
                    <span style="font-size:11px;color:#666D80;display:block;margin-top:4px;">(Berakhir)</span>
                @endif
            @else
                <span style="color:#999;">Tidak ada deadline</span>
            @endif
        </div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #E8F5E9;background:linear-gradient(135deg, #F1F8F5 0%, #E8F5E9 100%);">
        <div style="font-size:11px;font-weight:700;color:#0F6E56;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Sudah Dikumpul</div>
        <div style="font-size:18px;font-weight:700;color:#0F6E56;">{{ $pengumpulan->count() }}</div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #FFE0B2;background:linear-gradient(135deg, #FFF8E8 0%, #FFF3E0 100%);">
        <div style="font-size:11px;font-weight:700;color:#854F0B;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Menunggu Penilaian</div>
        <div style="font-size:18px;font-weight:700;color:#D39C3D;">{{ $pengumpulan->where('status_pengumpulan', '!=', 'acc')->where('status_pengumpulan', '!=', 'revisi')->count() }}</div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #D4DBFF;background:linear-gradient(135deg, #F0F4FF 0%, #E8EEFF 100%);">
        <div style="font-size:11px;font-weight:700;color:#185FA5;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Sudah Dinilai</div>
        <div style="font-size:18px;font-weight:700;color:#0B266E;">{{ $pengumpulan->where('nilai', '!=', null)->count() }}</div>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumpulan</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm">{{ $pengumpulan->count() }} pengumpulan</span>
</div>

@if($pengumpulan->count() > 0)
<div class="mp-card">
    <div class="mp-card-header">
        <span class="mp-card-title">Pengumpulan Tugas</span>
        <div class="right">
            <span style="font-size:12px;color:#666D80;">{{ $pengumpulan->count() }} mahasiswa mengumpulkan</span>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Mahasiswa</th>
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Waktu Kumpul</th>
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">File</th>
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Nilai</th>
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($pengumpulan as $p)
                @php $st = $p->status_pengumpulan ?? 'belum_dicek'; @endphp
                <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                            <div class="mp-av yellow" style="width:40px;height:40px;">{{ strtoupper(substr($p->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}</div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#0D0D12;">{{ $p->daftarPraktikan?->user?->name ?? '—' }}</div>
                                <div style="font-size:11px;color:#666D80;margin-top:2px;">{{ $p->created_at?->locale('id')->format('d M Y H:i') }}</div>
                                @if($p->catatan)
                                <div style="font-size:12px;color:#666D80;margin-top:4px;font-style:italic;">💬 {{ $p->catatan }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px;">
                        @if($st === 'acc')
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                        @elseif($st === 'revisi')
                        <span class="mp-badge error sm"><span class="dot"></span>Revisi</span>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;font-size:12px;color:#666D80;">{{ $p->created_at?->locale('id')->format('d M, H:i') }}</td>
                    <td style="padding:14px 16px;">
                        @if($p->file_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Unduh
                        </a>
                        @else
                        <span style="font-size:12px;color:#999;">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;">
                        @if($p->nilai !== null)
                        <span style="font-size:14px;font-weight:700;color:{{ $p->nilai >= 75 ? '#0B266E' : ($p->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $p->nilai }}</span>
                        @else
                        <span style="font-size:12px;color:#999;">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                            @if($st !== 'acc')
                            <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $p->id) }}" style="display:flex;gap:4px;align-items:center;">
                                @csrf
                                <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0–100" value="{{ $p->nilai ?? '' }}" class="mp-input" style="width:70px;font-size:12px;padding:6px 8px;">
                                <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;">ACC</button>
                            </form>
                            @endif
                            @if($st !== 'revisi' && $st !== 'acc')
                            <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $p->id) }}" x-data="{ catatan: '' }">
                                @csrf
                                <input type="hidden" name="catatan_revisi" :value="catatan">
                                <button type="button" @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()" class="mp-btn secondary sm" style="white-space:nowrap;">Revisi</button>
                            </form>
                            @endif
                            @if($st === 'acc')
                            <span style="font-size:11px;color:#0B266E;font-weight:700;">✓ Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada pengumpulan.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
    <div style="text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada pengumpulan.</div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>