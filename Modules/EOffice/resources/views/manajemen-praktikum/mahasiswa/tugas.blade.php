<x-eoffice::manajemen-praktikum.layout pageTitle="Tugas Praktikum">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Tugas Praktikum</h1>
        <p class="mp-page-sub">Lihat dan kumpulkan tugas praktikum Anda</p>
    </div>
</div>

@if($tugasList->isEmpty())
<div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
    <div style="text-align:center;color:var(--c-fg-placeholder);">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <div style="font-size:14px;font-weight:500;">Belum ada tugas.</div>
    </div>
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
    @endphp
    <div class="mp-card flex-shrink-0" style="padding:20px;" x-data="{ open: false }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <div style="font-size:14px;font-weight:700;color:var(--c-fg);">{{ $tugas->judul }}</div>
                    {{-- Status badge berdasarkan status_pengumpulan --}}
                    @if($statusTugas === 'acc')
                    <span class="mp-badge success sm">
                        ACC{{ $pengumpulan?->nilai ? ' — Nilai: '.$pengumpulan->nilai : '' }}
                    </span>
                    @elseif($statusTugas === 'revisi')
                    <span class="mp-badge error sm">Perlu Revisi</span>
                    @elseif($statusTugas === 'belum_dicek')
                    <span class="mp-badge warning sm">Dikumpul — Menunggu Penilaian</span>
                    @elseif($lewat)
                    <span class="mp-badge error sm">Terlambat</span>
                    @elseif($sisa !== null && $sisa <= 2)
                    <span class="mp-badge warning sm">Segera!</span>
                    @else
                    <span class="mp-badge neutral sm">Belum Dikumpul</span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--c-fg-muted);">
                    Modul: <span style="font-weight:600;color:var(--c-fg-sub);">{{ $tugas->modul?->nama ?? '—' }}</span>
                    @if($dl)
                    · Deadline: <span style="font-weight:600;color:{{ $lewat ? '#DF1C41' : 'var(--c-fg-sub)' }};">{{ $dl->format('d M Y, H:i') }}</span>
                    @if($sisa !== null && !$lewat) <span style="color:var(--c-fg-muted);">({{ $sisa }} hari lagi)</span> @endif
                    @endif
                </div>
                @if(!empty($tugas->deskripsi))
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:4px;" class="line-clamp-2">{{ $tugas->deskripsi }}</div>
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

        {{-- Upload / Kirim Ulang Form --}}
        <div x-show="open" x-transition class="mt-4 pt-4" style="border-top:1px solid var(--c-border-light);">
            @if($statusTugas === 'revisi')
            {{-- Form kirim ulang setelah revisi --}}
            <div style="font-size:12px;font-weight:600;color:#D39C3D;margin-bottom:12px;">Kirim ulang file perbaikan:</div>
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $tugas->id) }}" enctype="multipart/form-data">
                @csrf
            @else
            {{-- Form kumpul pertama kali --}}
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kumpul', $tugas->id) }}" enctype="multipart/form-data">
                @csrf
            @endif
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">File Tugas <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required class="mp-input w-full">
                        <div style="font-size:11px;color:var(--c-fg-muted);margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Catatan (opsional)</label>
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

        {{-- Catatan Revisi dari Asprak --}}
        @if($sudahKumpul && $pengumpulan?->catatan_revisi)
        <div class="mt-3 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;">
            <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asprak:</div>
            <div style="font-size:12px;color:#7C1028;">{{ $pengumpulan->catatan_revisi }}</div>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
