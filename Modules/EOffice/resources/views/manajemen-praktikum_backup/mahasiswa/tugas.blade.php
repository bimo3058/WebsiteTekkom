<x-eoffice::manajemen-praktikum.layout pageTitle="Tugas Praktikum">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Tugas Praktikum</h1>
        <p class="mp-page-sub">
            Lihat dan kumpulkan tugas praktikum Anda
            @if($daftarPraktikan) · {{ $daftarPraktikan->praktikum?->nama }} @endif
        </p>
    </div>
</div>

{{-- Switcher praktikum jika ikut lebih dari 1 --}}
@if(isset($semuaPraktikan) && $semuaPraktikan->count() > 1)
<div style="display:flex;flex-wrap:wrap;gap:8px;" class="flex-shrink-0">
    @foreach($semuaPraktikan as $dp)
    <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}?praktikum_id={{ $dp->praktikum_id }}"
       class="{{ $dp->praktikum_id === $daftarPraktikan?->praktikum_id ? 'mp-btn primary sm' : 'mp-btn secondary sm' }}"
       style="text-decoration:none;">
        {{ $dp->praktikum?->nama ?? 'Praktikum' }}
    </a>
    @endforeach
</div>
@endif

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Tugas</span>
    <span class="sec-rule"></span>
</div>

@if($tugasList->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;">
    <svg class="mx-auto mb-3" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada tugas yang diberikan.</div>
</div>
@else
<div class="flex flex-col gap-3 flex-1">
    @foreach($tugasList as $tugas)
    @php
        $dl             = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
        $dlAcc          = $tugas->deadline_acc ? \Carbon\Carbon::parse($tugas->deadline_acc) : null;
        $lewat          = $dl && now()->gt($dl);
        $lewatAcc       = $dlAcc && now()->gt($dlAcc);
        
        $sisa           = $dl ? now()->diffInDays($dl, false) : null;
        $sisaAcc        = $dlAcc ? now()->diffInDays($dlAcc, false) : null;
        
        $pengumpulan    = $tugas->pengumpulan;
        $sudahKumpul    = !is_null($pengumpulan);
        $statusTugas    = $tugas->status_tugas ?? ($sudahKumpul ? $pengumpulan->status_pengumpulan : 'belum_dikumpul');
        
        // Cek keterlambatan pengumpulan pertama
        $firstSubmission = $sudahKumpul && $pengumpulan->riwayat ? $pengumpulan->riwayat->sortBy('created_at')->first() : null;
        $isLate          = $firstSubmission && $dl && \Carbon\Carbon::parse($firstSubmission->created_at)->gt($dl);
        
        // Batas pengumpulan mutlak
        $tenggatMutlak  = $dlAcc ?? $dl;
        $lewatMutlak    = $tenggatMutlak && now()->gt($tenggatMutlak);
        
        $deadlineColor  = ($lewat || ($sisa !== null && $sisa <= 2)) ? '#DF1C41' : '#666D80';
    @endphp
    <div class="mp-card flex-shrink-0" style="padding:20px;" x-data="{ open: false }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $tugas->judul }}</div>
                    {{-- Status badge berdasarkan status_pengumpulan --}}
                    @if($statusTugas === 'acc')
                    <span class="mp-badge success sm"><span class="dot"></span>ACC{{ $pengumpulan?->nilai ? ' — Nilai: '.$pengumpulan->nilai : '' }}</span>
                    @elseif($statusTugas === 'revisi')
                    <span class="mp-badge error sm"><span class="dot"></span>Perlu Revisi</span>
                    @elseif($statusTugas === 'belum_dicek')
                    <span class="mp-badge warning sm"><span class="dot"></span>Dikumpul — Menunggu Penilaian</span>
                    @elseif($lewatMutlak)
                    <span class="mp-badge error sm"><span class="dot"></span>Waktu Habis</span>
                    @elseif($lewat)
                    <span class="mp-badge warning sm"><span class="dot"></span>Tenggat AC Terlewat (Masih Bisa Kumpul)</span>
                    @elseif($sisa !== null && $sisa <= 2)
                    <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                    @else
                    <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                    @endif

                    @if($isLate)
                    <span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41;">Terlambat (Lewat Deadline AC)</span>
                    @endif
                </div>
                <div style="font-size:12px;color:#666D80;">
                    Modul: <span style="font-weight:600;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</span>
                    @if($dl)
                    · Deadline AC: <span style="font-weight:600;color:{{ $deadlineColor }};">{{ $dl->format('d M Y, H:i') }}</span>
                    @endif
                    @if($dlAcc)
                    · Deadline ACC: <span style="font-weight:600;color:{{ $lewatAcc ? '#DF1C41' : '#353849' }};">{{ $dlAcc->format('d M Y, H:i') }}</span>
                    @endif
                </div>
                @if(!empty($tugas->deskripsi))
                <div style="font-size:12px;color:#666D80;margin-top:4px;" class="line-clamp-2">{{ $tugas->deskripsi }}</div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 flex-shrink-0">
                @if(!$sudahKumpul && !$lewatMutlak)
                <button @click="open = !open" class="mp-btn primary sm">Upload</button>
                @elseif($statusTugas === 'revisi' && !$lewatMutlak)
                <button @click="open = !open" class="mp-btn warning sm">Kirim Ulang</button>
                @endif
            </div>
        </div>

        {{-- File yang Dikumpulkan & Riwayat --}}
        @if($sudahKumpul && $pengumpulan?->file_path)
        <div class="mt-3 p-3 rounded-[8px]" style="background:#F4F6F8;border:1px solid #DFE1E7;">
            <div style="font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;display:flex;align-items:center;justify-content:space-between;">
                <span>File yang Dikumpulkan:</span>
                <span style="font-size:10px;font-weight:500;color:#666D80;">Update terakhir: {{ \Carbon\Carbon::parse($pengumpulan->updated_at)->locale('id')->format('d M Y, H:i') }}</span>
            </div>
            
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_path, 'eoffice') }}" target="_blank" style="font-size:12px;font-weight:700;color:#0B266E;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Unduh File Terbaru
                </a>

                {{-- Dropdown Riwayat File --}}
                @if($pengumpulan->riwayat && $pengumpulan->riwayat->isNotEmpty())
                <div x-data="{ openRiwayat: false }" style="position:relative;">
                    <button type="button" @click="openRiwayat = !openRiwayat" class="mp-btn secondary sm" style="font-size:11px;padding:4px 8px;display:inline-flex;align-items:center;gap:2px;">
                        Riwayat Berkas ({{ $pengumpulan->riwayat->count() }})
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;right:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:240px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                        @foreach($pengumpulan->riwayat as $index => $r)
                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice') }}" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;text-align:left;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-weight:700;color:#0B266E;">#{{ $pengumpulan->riwayat->count() - $index }} {{ $r->is_revision ? 'Revisi' : 'Pertama' }}</span>
                                <span style="font-size:9px;color:#888;">{{ $r->created_at->format('H:i') }}</span>
                            </div>
                            @if($r->catatan)
                            <span style="font-size:10px;color:#666D80;margin-top:2px;font-style:italic;" class="truncate">💬 {{ $r->catatan }}</span>
                            @endif
                            <span style="font-size:9px;color:#A4ABB8;margin-top:2px;">{{ $r->created_at->locale('id')->format('d M Y') }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Catatan Revisi dari Asprak --}}
        @if($sudahKumpul && ($pengumpulan?->catatan_revisi || $pengumpulan?->file_revisi_asprak))
        <div class="mt-3 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;">
            <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asprak:</div>
            @if($pengumpulan->catatan_revisi)
            <div style="font-size:12px;color:#7C1028;margin-bottom:6px;">{{ $pengumpulan->catatan_revisi }}</div>
            @endif
            @if($pengumpulan->file_revisi_asprak)
            <div style="margin-top:6px;">
                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_revisi_asprak, 'eoffice') }}" target="_blank" style="font-size:11px;font-weight:700;color:#95122B;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Unduh File Lampiran Revisi
                </a>
            </div>
            @endif
        </div>
        @endif

        {{-- Upload / Kirim Ulang Form --}}
        <div x-show="open" x-transition class="mt-4 pt-4" style="border-top:1px solid #ECEFF3;">
            @if($statusTugas === 'revisi')
            <div style="font-size:12px;font-weight:600;color:#D39C3D;margin-bottom:12px;">Kirim ulang file perbaikan:</div>
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $tugas->id) }}" enctype="multipart/form-data">
                @csrf
            @else
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kumpul', $tugas->id) }}" enctype="multipart/form-data">
                @csrf
            @endif
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">File Tugas <span style="color:#DF1C41;">*</span></label>
                        <input type="file" name="file" required class="mp-input w-full">
                        <div style="font-size:11px;color:#666D80;margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</div>
                    </div>
                    <div>
                        <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">Catatan (opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan untuk asisten..."
                                  class="mp-input w-full" style="resize:none;"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="open = false" class="mp-btn secondary md">Batal</button>
                        <button type="submit" class="mp-btn primary md">
                            {{ $statusTugas === 'revisi' ? 'Kirim Perbaikan' : 'Kumpulkan' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
