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
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
</div>

{{-- Main Scrollable Container --}}
<div style="display:flex;flex-direction:column;gap:16px;min-height:0;flex:1;overflow-y:auto;padding-right:8px;">

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
<div class="mp-card" style="padding:24px;margin-bottom:4px;"
     onmouseover="this.style.boxShadow='0 4px 16px rgba(11,38,110,.08)';this.style.borderColor='#B7C2DE';"
     onmouseout="this.style.boxShadow='';this.style.borderColor='#DFE1E7';"
     x-data="{ open: false }">
    
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;">
        {{-- Left Content --}}
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
                <div style="font-size:15px;font-weight:700;color:#0D0D12;">{{ $tugas->judul }}</div>
                @if($lewat)
                <span class="mp-badge neutral sm">Berakhir</span>
                @else
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                @endif
            </div>
            
            <div style="font-size:13px;color:#666D80;margin-bottom:10px;">
                📚 Modul: <span style="font-weight:600;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</span>
                @if($dl)
                <br>⏰ Deadline: <span style="font-weight:600;color:{{ $lewat ? '#999' : '#353849' }};">{{ $dl->locale('id')->format('d M Y, H:i') }}</span>
                @endif
            </div>

            @if($tugas->deskripsi)
            <div style="font-size:12px;color:#666D80;line-height:1.5;margin-top:10px;">{{ Str::limit($tugas->deskripsi, 150) }}</div>
            @endif
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:12px;flex-shrink:0;">
            <div style="text-align:center;padding:12px;border-radius:8px;background:#F9FAFB;border:1px solid #E4E6EB;">
                <div style="font-size:18px;font-weight:700;color:#0D0D12;">{{ $totalKumpul }}</div>
                <div style="font-size:10px;font-weight:600;color:#666D80;margin-top:4px;">Dikumpul</div>
            </div>
            <div style="text-align:center;padding:12px;border-radius:8px;background:#FFFBF0;border:1px solid #FFE8C8;">
                <div style="font-size:18px;font-weight:700;color:#D39C3D;">{{ max(0, $belumDicek) }}</div>
                <div style="font-size:10px;font-weight:600;color:#854F0B;margin-top:4px;">Menunggu</div>
            </div>
            <div style="text-align:center;padding:12px;border-radius:8px;background:#FFF3F3;border:1px solid #FFD9E1;">
                <div style="font-size:18px;font-weight:700;color:#DF1C41;">{{ $revisi }}</div>
                <div style="font-size:10px;font-weight:600;color:#A51330;margin-top:4px;">Revisi</div>
            </div>
            <div style="text-align:center;padding:12px;border-radius:8px;background:#E8EEFF;border:1px solid #D4DBFF;">
                <div style="font-size:18px;font-weight:700;color:#0B266E;">{{ $acc }}</div>
                <div style="font-size:10px;font-weight:600;color:#185FA5;margin-top:4px;">ACC</div>
            </div>
        </div>
    </div>

    {{-- Pengumpulan Section --}}
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid #DFE1E7;">
        <button @click="open = !open" type="button" style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6366F1;cursor:pointer;padding:8px 0;border:none;background:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" :class="open ? 'rotate-90' : ''" style="transition:transform .2s;">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
            <span x-text="open ? 'Sembunyikan Pengumpulan' : 'Lihat Pengumpulan (' + {{ $totalKumpul }} + ')'"></span>
        </button>

        <div x-show="open" x-transition style="margin-top:16px;">
            @php $pengumpulanList = $tugas->pengumpulan ?? collect(); @endphp
            @if($pengumpulanList->isEmpty())
            <div style="padding:40px;text-align:center;background:#F9FAFB;border-radius:8px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada yang mengumpulkan.</div>
            </div>
            @else
            <div style="border:1px solid #DFE1E7;border-radius:10px;overflow:hidden;">
                {{-- Header --}}
                <div style="display:flex;align-items:center;padding:12px 16px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;gap:12px;">
                    <div style="flex:1;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Mahasiswa</div>
                    <div style="width:120px;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Status</div>
                    <div style="width:140px;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Waktu Kumpul</div>
                    <div style="width:70px;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">File</div>
                    <div style="width:70px;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Nilai</div>
                    <div style="width:240px;font-size:12px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;">Aksi</div>
                </div>

                {{-- Rows --}}
                @foreach($pengumpulanList as $peng)
                @php $st = $peng->status_pengumpulan ?? 'belum_dicek'; @endphp
                <div style="display:flex;align-items:center;padding:14px 16px;border-bottom:1px solid #EEF0F5;gap:12px;transition:background .1s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                    <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0;">
                        <div class="mp-av yellow" style="width:36px;height:36px;">{{ strtoupper(substr($peng->daftarPraktikan?->user?->name ?? 'M', 0, 2)) }}</div>
                        <div style="min-width:0;">
                            <div style="font-size:12px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                            @if($peng->is_revision)
                            <span class="mp-badge navy sm" style="font-size:10px;margin-top:2px;">Revisi ke-{{ $peng->is_revision }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="width:120px;">
                        @if($st === 'acc')
                        <span class="mp-badge success sm"><span class="dot"></span>ACC</span>
                        @elseif($st === 'revisi')
                        <span class="mp-badge error sm"><span class="dot"></span>Revisi</span>
                        @else
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        @endif
                    </div>
                    <div style="width:140px;font-size:12px;color:#666D80;">{{ $peng->created_at?->locale('id')->format('d M, H:i') }}</div>
                    <div style="width:70px;">
                        @if($peng->file_path)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($peng->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;">Unduh</a>
                        @else
                        <span style="font-size:12px;color:#999;">—</span>
                        @endif
                    </div>
                    <div style="width:70px;">
                        @if($peng->nilai !== null)
                        <span style="font-size:14px;font-weight:700;color:{{ $peng->nilai >= 75 ? '#0B266E' : ($peng->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $peng->nilai }}</span>
                        @else
                        <span style="font-size:12px;color:#999;">—</span>
                        @endif
                    </div>
                    <div style="width:240px;display:flex;gap:6px;align-items:center;">
                        @if($st !== 'acc')
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $peng->id) }}" style="display:flex;gap:4px;align-items:center;">
                            @csrf
                            <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0-100" value="{{ $peng->nilai ?? '' }}" class="mp-input" style="width:70px;font-size:12px;padding:6px 8px;">
                            <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;">ACC</button>
                        </form>
                        @endif
                        @if($st !== 'revisi' && $st !== 'acc')
                        <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $peng->id) }}" x-data="{ catatan: '' }">
                            @csrf
                            <input type="hidden" name="catatan_revisi" :value="catatan">
                            <button type="button" @click="catatan = prompt('Catatan revisi:'); if(catatan) $el.closest('form').submit()" class="mp-btn secondary sm" style="white-space:nowrap;">Revisi</button>
                        </form>
                        @endif
                        @if($st === 'acc')
                        <span style="font-size:11px;color:#0B266E;font-weight:700;">✓ Selesai</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
    <div style="text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada tugas. Buat tugas baru sekarang!</div>
    </div>
</div>
@endforelse

</div>
{{-- End Scrollable Container --}}

</x-eoffice::manajemen-praktikum.layout>