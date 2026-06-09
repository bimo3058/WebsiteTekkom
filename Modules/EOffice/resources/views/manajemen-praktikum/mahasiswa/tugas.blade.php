<x-eoffice::manajemen-praktikum.layout pageTitle="Tugas Praktikum">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Tugas Praktikum</h1>
        <p class="mp-page-sub">Lihat dan kumpulkan tugas praktikum Anda</p>
    </div>
</div>

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
        $dl          = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
        $lewat       = $dl && now()->gt($dl);
        $sisa        = $dl ? now()->diffInDays($dl, false) : null;
        $pengumpulan = $tugas->pengumpulan;
        $sudahKumpul = !is_null($pengumpulan);
        $statusTugas = $tugas->status_tugas ?? ($sudahKumpul ? $pengumpulan->status_pengumpulan : 'belum_dikumpul');
        $deadlineColor = ($lewat || ($sisa !== null && $sisa <= 2)) ? '#DF1C41' : '#666D80';
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
                    @elseif($lewat)
                    <span class="mp-badge error sm"><span class="dot"></span>Terlambat</span>
                    @elseif($sisa !== null && $sisa <= 2)
                    <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                    @else
                    <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                    @endif
                </div>
                <div style="font-size:12px;color:#666D80;">
                    Modul: <span style="font-weight:600;color:#353849;">{{ $tugas->modul?->nama ?? '—' }}</span>
                    @if($dl)
                    · Deadline: <span style="font-weight:600;color:{{ $deadlineColor }};">{{ $dl->format('d M Y, H:i') }}</span>
                    @if($sisa !== null && !$lewat)
                    <span style="color:{{ $sisa <= 2 ? '#DF1C41' : '#666D80' }};">({{ $sisa }} hari lagi)</span>
                    @endif
                    @endif
                </div>
                @if(!empty($tugas->deskripsi))
                <div style="font-size:12px;color:#666D80;margin-top:4px;" class="line-clamp-2">{{ $tugas->deskripsi }}</div>
                @endif
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 flex-shrink-0">
                @if(!$sudahKumpul && !$lewat)
                <button @click="open = !open" class="mp-btn primary sm">Upload</button>
                @elseif($statusTugas === 'revisi')
                <button @click="open = !open" class="mp-btn warning sm">Kirim Ulang</button>
                @endif
            </div>
        </div>

        {{-- Catatan Revisi dari Asprak --}}
        @if($sudahKumpul && $pengumpulan?->catatan_revisi)
        <div class="mt-3 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;">
            <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asprak:</div>
            <div style="font-size:12px;color:#7C1028;">{{ $pengumpulan->catatan_revisi }}</div>
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
