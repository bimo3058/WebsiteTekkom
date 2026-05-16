<x-eoffice::manajemen-praktikum.layout pageTitle="Tugas Praktikum">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Tugas Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Lihat dan kumpulkan tugas praktikum Anda</div>
    </div>
</div>

@if($tugasList->isEmpty())
<div class="flex-1 bg-white border border-[#DFE1E7] rounded-[14px] flex items-center justify-center">
    <div class="text-center text-[#A4ABB8]">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <div class="text-[14px] font-medium">Belum ada tugas.</div>
    </div>
</div>
@else
<div class="flex flex-col gap-3 flex-1">
    @foreach($tugasList as $tugas)
    @php
        $dl = \Carbon\Carbon::parse($tugas->deadline);
        $lewat = now()->gt($dl);
        $sisa  = now()->diffInDays($dl, false);
        $pengumpulan = $tugas->pengumpulan; // relasi hasOne untuk user ini
        $sudahKumpul = !is_null($pengumpulan);
    @endphp
    <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]" x-data="{ open: false }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <div class="text-[14px] font-bold text-[#0D0D12]">{{ $tugas->judul }}</div>
                    {{-- Status badge --}}
                    @if($sudahKumpul)
                        @if($pengumpulan->nilai)
                        <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Dinilai: {{ $pengumpulan->nilai }}</span>
                        @elseif($pengumpulan->is_revision)
                        <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#FADAE1] text-[#7C1028]">Perlu Revisi</span>
                        @else
                        <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F9ECCB] text-[#7C5309]">Dikumpul — Menunggu Penilaian</span>
                        @endif
                    @elseif($lewat)
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#FADAE1] text-[#7C1028]">Terlambat</span>
                    @elseif($sisa <= 2)
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#FADAE1] text-[#7C1028]">Segera!</span>
                    @else
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0F1F4] text-[#666D80]">Belum Dikumpul</span>
                    @endif
                </div>
                <div class="text-[12px] text-[#666D80]">
                    Modul: <span class="font-semibold text-[#353849]">{{ $tugas->modul?->nama ?? '—' }}</span>
                    · Deadline: <span class="font-semibold {{ $lewat ? 'text-[#DF1C41]' : 'text-[#353849]' }}">{{ $dl->format('d M Y, H:i') }}</span>
                    @if(!$lewat) <span class="text-[#A4ABB8]">({{ $sisa }} hari lagi)</span> @endif
                </div>
                @if(!empty($tugas->deskripsi))
                <div class="text-[12px] text-[#666D80] mt-1 line-clamp-2">{{ $tugas->deskripsi }}</div>
                @endif
            </div>
            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                @if(!$sudahKumpul && !$lewat)
                <button @click="open = !open"
                        class="text-[12px] font-semibold px-4 py-[7px] rounded-[8px] bg-[#0B266E] text-white border-none cursor-pointer hover:bg-[#0a1f5c]">
                    Upload
                </button>
                @elseif($sudahKumpul && $pengumpulan?->is_revision)
                <button @click="open = !open"
                        class="text-[12px] font-semibold px-4 py-[7px] rounded-[8px] bg-[#D39C3D] text-white border-none cursor-pointer hover:bg-[#b88530]">
                    Revisi
                </button>
                @endif
            </div>
        </div>

        {{-- Upload Form --}}
        <div x-show="open" x-transition class="mt-4 pt-4 border-t border-[#F0F1F4]">
            <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.kumpul', $tugas->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">File Tugas <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required
                               class="w-full text-[13px] border border-[#DFE1E7] rounded-[8px] px-3 py-[8px] focus:outline-none focus:border-[#0B266E]">
                        <div class="text-[11px] text-[#A4ABB8] mt-1">Format: PDF, DOCX, ZIP (maks. 10MB)</div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Catatan (opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan untuk asisten..."
                                  class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[8px] text-[13px] focus:outline-none focus:border-[#0B266E] resize-none"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="open = false"
                                class="px-4 py-[7px] rounded-[8px] border border-[#DFE1E7] text-[13px] text-[#353849] bg-white cursor-pointer hover:bg-[#F6F8FA]">Batal</button>
                        <button type="submit"
                                class="px-4 py-[7px] rounded-[8px] bg-[#0B266E] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0a1f5c]">Kumpulkan</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Revisi note --}}
        @if($sudahKumpul && $pengumpulan?->catatan_revisi)
        <div class="mt-3 bg-[#FADAE1] border border-[#DF1C41] rounded-[8px] px-3 py-2">
            <div class="text-[11px] font-bold text-[#7C1028] mb-[2px]">Catatan Revisi:</div>
            <div class="text-[12px] text-[#7C1028]">{{ $pengumpulan->catatan_revisi }}</div>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
