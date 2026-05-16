<x-eoffice::manajemen-praktikum.layout pageTitle="Modul Praktikum{{ $praktikum ? ' — '.$praktikum->nama : '' }}">

{{-- Pilih Praktikum --}}
@if(!$praktikum)
<div class="bg-white rounded-[14px] border border-[#DFE1E7] px-6 py-10 text-center flex-shrink-0">
    <div class="text-[32px] mb-3">📚</div>
    <div class="text-[15px] font-semibold text-[#353849] mb-1">Pilih Praktikum</div>
    <div class="text-[13px] text-[#666D80] mb-5">Pilih praktikum yang ingin dilihat modulnya.</div>
    @if($praktikums->isEmpty())
        <div class="text-[13px] text-[#A4ABB8]">Kamu belum mengampu praktikum apapun.</div>
    @else
        <div class="flex flex-col gap-2 max-w-sm mx-auto">
            @foreach($praktikums as $p)
            <a href="{{ route('eoffice.manprak.dosen.modul.index', $p->id) }}"
               class="flex items-center justify-between px-4 py-3 rounded-[10px] border border-[#DFE1E7] bg-[#F6F8FA] hover:bg-[#F0E6FA] hover:border-[#9B59B6] no-underline transition-colors">
                <span class="text-[13px] font-medium text-[#0D0D12]">{{ $p->nama }}</span>
                <span class="text-[11px] text-[#666D80]">{{ $p->semester }} {{ $p->tahun_ajaran }}</span>
            </a>
            @endforeach
        </div>
    @endif
</div>
@else

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Daftar Modul</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $praktikum->nama }} · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</div>
    </div>
    <a href="{{ route('eoffice.manprak.dosen.modul.index', '0') }}"
       class="flex items-center gap-2 px-4 py-2 rounded-[10px] text-[13px] font-semibold text-[#353849] bg-white border border-[#DFE1E7] no-underline hover:bg-[#F6F8FA] transition-colors">
        Ganti Praktikum
    </a>
</div>

@if($modulList->isEmpty())
<div class="bg-white rounded-[14px] border border-[#DFE1E7] px-6 py-12 text-center">
    <div class="text-[32px] mb-3">📭</div>
    <div class="text-[15px] font-semibold text-[#353849]">Belum ada modul</div>
    <div class="text-[13px] text-[#666D80] mt-1">Modul dibuat oleh koordinator atau asisten praktikum.</div>
</div>
@else
<div class="flex flex-col gap-3">
    @foreach($modulList as $item)
    @php $modul = $item['modul']; @endphp
    <div class="bg-white rounded-[14px] border border-[#DFE1E7] px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold px-2 py-[2px] rounded-full bg-[#F0E6FA] text-[#6B21A8]">Modul {{ $modul->urutan }}</span>
                    <div class="text-[14px] font-semibold text-[#0D0D12] truncate">{{ $modul->nama }}</div>
                </div>
                @if($modul->deskripsi)
                <div class="text-[12px] text-[#666D80] mt-1 line-clamp-2">{{ $modul->deskripsi }}</div>
                @endif
                @if($item['asprak']->isNotEmpty())
                <div class="flex items-center gap-1 mt-2 flex-wrap">
                    <span class="text-[10px] text-[#A4ABB8]">Asprak:</span>
                    @foreach($item['asprak'] as $nama)
                    <span class="text-[10px] font-medium px-2 py-[1px] rounded-full bg-[#DDF2EE] text-[#174E43]">{{ $nama }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex gap-3 flex-shrink-0">
                <div class="text-center">
                    <div class="text-[16px] font-bold text-[#6B21A8]">{{ $item['jumlah_materi'] }}</div>
                    <div class="text-[10px] text-[#A4ABB8]">Materi</div>
                </div>
                <div class="text-center">
                    <div class="text-[16px] font-bold text-[#6B21A8]">{{ $item['jumlah_tugas'] }}</div>
                    <div class="text-[10px] text-[#A4ABB8]">Tugas</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endif

</x-eoffice::manajemen-praktikum.layout>