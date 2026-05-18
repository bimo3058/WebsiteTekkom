<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Tugas">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Tugas Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub">Buat tugas dan nilai pengumpulan mahasiswa · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}" class="mp-btn primary md" style="text-decoration:none;display:flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
</div>

{{-- Section header --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Tugas</span>
    <span class="sec-rule"></span>
</div>

@forelse($tugasList ?? [] as $tugas)
@php
    $dl    = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
    $lewat = $dl && now()->gt($dl);
    $totalKumpul = $tugas->pengumpulan_count ?? 0;
    $acc         = $tugas->pengumpulan_acc_count ?? 0;
    $revisi      = $tugas->pengumpulan_revisi_count ?? 0;
    $belumDicek  = $totalKumpul - $acc - $revisi;
@endphp
<div class="mp-card flex-shrink-0" style="padding:20px;"
     onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
     onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''"
     x-data="{ open: false }">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $tugas->judul }}</div>
                @if($lewat)
                <span class="mp-badge neutral sm">Berakhir</span>
                @else
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                @endif
            </div>
            <div style="font-size:12px;color:#666D80;">
                Modul: <span style="font-weight:600;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</span>
                @if($dl)
                · Deadline: <span style="font-weight:600;color:{{ $lewat ? '#666D80' : '#353849' }};">{{ $dl->format('d M Y, H:i') }}</span>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:20px;flex-shrink:0;">
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#0D0D12;">{{ $totalKumpul }}</div>
                <div style="font-size:10px;color:#666D80;">Dikumpul</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#D39C3D;">{{ max(0,$belumDicek) }}</div>
                <div style="font-size:10px;color:#666D80;">Belum Dicek</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#DF1C41;">{{ $revisi }}</div>
                <div style="font-size:10px;color:#666D80;">Revisi</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:16px;font-weight:700;color:#0B266E;">{{ $acc }}</div>
                <div style="font-size:10px;color:#666D80;">ACC</div>
            </div>
            <button @click="open = !open" class="mp-btn secondary sm" x-text="open ? 'Tutup' : 'Lihat Pengumpulan'"></button>
        </div>
    </div>

    {{-- Pengumpulan Table --}}
    <div x-show="open" x-transition style="margin-top:16px;border-top:1px solid #DFE1E7;padding-top:16px;">
        @php $pengumpulanList = $tugas->pengumpulan ?? collect(); @endphp
        @if($pengumpulanList->isEmpty())
        <div style="padding:48px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada yang mengumpulkan.</div>
        </div>
        @else
        <div style="border:1px solid #DFE1E7;border-radius:10px;overflow:hidden;">
            <div style="display:flex;align-items:center;padding:8px 16px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <div class="mp-th flex-1">Mahasiswa</div>
                <div class="mp-th" style="width:100px;">Status</div>
                <div class="mp-th" style="width:110px;">Waktu Kumpul</div>
                <div class="mp-th" style="width:65px;">File</div>
                <div class="mp-th" style="width:65px;">Nilai</div>
                <div class="mp-th" style="width:200px;">Aksi</div>
            </div>
            @foreach($pengumpulanList as $peng)
            @php $st = $peng->status_pengumpulan ?? 'belum_dicek'; @endphp
            <div class="mp-tr" style="display:flex;align-items:center;padding:10px 16px;border-bottom:1px solid #DFE1E7;">
                <div style="flex:1;display:flex;align-items:center;gap:8px;min-width:0;padding-right:8px;">
                    <div class="mp-av yellow">{{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}</div>
                    <div style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                    @if($peng->is_revision)
                    <span class="mp-badge navy sm">Revisi ke-{{ $peng->is_revision }}</span>
                    @endif
                </div>
                <div style="width:100px;">
                    @if($st === 'acc')
                    <span class="mp-badge success sm"><span class="dot"></span>ACC</span>
                    @elseif($st === 'revisi')
                    <span class="mp-badge error sm"><span class="dot"></span>Revisi</span>
                    @else
                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                    @endif
                </div>
                <div style="width:110px;font-size:11px;color:#666D80;">{{ $peng->created_at?->format('d M, H:i') }}</div>
                <div style="width:65px;">
                    @if($peng->file_path)
                    <a href="{{ Storage::url($peng->file_path) }}" target="_blank"
                       style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;">Unduh</a>
                    @else
                    <span style="font-size:11px;color:#808897;">—</span>
                    @endif
                </div>
                <div style="width:65px;">
                    @if($peng->nilai !== null)
                    <span style="font-size:14px;font-weight:700;color:{{ $peng->nilai >= 75 ? '#0B266E' : ($peng->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $peng->nilai }}</span>
                    @else
                    <span style="font-size:12px;color:#808897;">—</span>
                    @endif
                </div>
                <div style="display:flex;gap:6px;align-items:center;width:200px;">
                    @if($st !== 'acc')
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $peng->id) }}" style="display:flex;gap:4px;align-items:center;">
                        @csrf
                        <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0–100"
                               value="{{ $peng->nilai ?? '' }}"
                               class="mp-input" style="width:68px;font-size:12px;padding:4px 8px;">
                        <button type="submit" class="mp-btn ghost sm">ACC</button>
                    </form>
                    @endif
                    @if($st !== 'revisi' && $st !== 'acc')
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $peng->id) }}" x-data="{ catatan: '' }">
                        @csrf
                        <input type="hidden" name="catatan_revisi" :value="catatan">
                        <button type="button"
                                @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()"
                                class="mp-btn secondary sm">Revisi</button>
                    </form>
                    @endif
                    @if($st === 'acc')
                    <span style="font-size:11px;color:#0B266E;font-weight:600;">Selesai</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@empty
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada tugas. Buat tugas baru!</div>
    </div>
</div>
@endforelse

</x-eoffice::manajemen-praktikum.layout>
