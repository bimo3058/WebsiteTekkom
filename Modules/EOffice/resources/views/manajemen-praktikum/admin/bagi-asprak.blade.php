<x-eoffice::manajemen-praktikum.layout pageTitle="Bagi Asprak ke Modul">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Bagi Asprak ke Modul</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Tugaskan asisten praktikum ke modul-modul yang tersedia</div>
    </div>
</div>

{{-- Pilih Praktikum --}}
<div class="flex-shrink-0">
    <form method="GET" class="flex gap-2 items-center">
        <select name="praktikum_id" onchange="this.form.submit()"
            class="text-[13px] border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] bg-white text-[#0D0D12] focus:outline-none focus:ring-2 focus:ring-[#0B266E]">
            @foreach($praktikums as $prak)
            <option value="{{ $prak->id }}" {{ $prak->id == $praktikumId ? 'selected' : '' }}>
                {{ $prak->nama }} ({{ $prak->semester }} {{ $prak->tahun_ajaran }})
            </option>
            @endforeach
        </select>
    </form>
</div>

@if(session('success'))
<div class="flex-shrink-0 px-4 py-3 rounded-[10px] bg-[#DDF2EE] text-[#174E43] text-[13px] font-medium">
    {{ session('success') }}
</div>
@endif

@if($praktikums->isEmpty())
<div class="flex-1 flex items-center justify-center text-[14px] text-[#666D80]">
    Tidak ada praktikum aktif saat ini.
</div>
@else

<div class="flex gap-4 flex-1 min-h-0">

    {{-- Daftar Modul --}}
    <div class="flex flex-col flex-1 bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-h-0">
        <div class="px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
            <div class="text-[12px] font-semibold text-[#666D80] uppercase tracking-[.06em]">
                Modul Praktikum
                @if($selectedPraktikum)
                — <span class="normal-case font-normal">{{ $selectedPraktikum->nama }}</span>
                @endif
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($moduls as $modul)
            <div class="px-5 py-4 border-b border-[#F8F9FB] last:border-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-[13px] font-semibold text-[#0D0D12]">
                            {{ $modul->urutan }}. {{ $modul->nama }}
                        </div>
                        @if($modul->deskripsi)
                        <div class="text-[11px] text-[#666D80] mt-[2px]">{{ $modul->deskripsi }}</div>
                        @endif

                        {{-- Asprak yang sudah ditugaskan --}}
                        @if($modul->modulAsprak->isNotEmpty())
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($modul->modulAsprak as $ma)
                            <span class="text-[11px] font-medium px-2 py-[2px] rounded-full bg-[#EEF1FB] text-[#3C518B]">
                                {{ $ma->asprak?->user?->name ?? '—' }}
                            </span>
                            @endforeach
                        </div>
                        @else
                        <div class="text-[11px] text-[#A4ABB8] mt-1 italic">Belum ada asprak ditugaskan</div>
                        @endif
                    </div>

                    {{-- Form tambah asprak ke modul ini --}}
                    @if($aspraks->isNotEmpty())
                    <form method="POST" action="{{ route('eoffice.manprak.admin.bagi-asprak.store') }}"
                          class="flex gap-2 items-center flex-shrink-0">
                        @csrf
                        <input type="hidden" name="modul_id" value="{{ $modul->id }}">
                        <select name="asprak_id"
                            class="text-[12px] border border-[#DFE1E7] rounded-[7px] px-2 py-[5px] bg-white focus:outline-none focus:ring-1 focus:ring-[#0B266E]">
                            @foreach($aspraks as $asprak)
                            <option value="{{ $asprak->id }}">{{ $asprak->user?->name ?? '—' }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="text-[11px] font-semibold px-3 py-[5px] rounded-[7px] bg-[#0B266E] text-white border-none cursor-pointer hover:bg-[#0a1f5a]">
                            Tugaskan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-14 text-center text-[13px] text-[#666D80]">
                Belum ada modul untuk praktikum ini.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Daftar Asprak di Praktikum ini --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)]" style="width:260px;">
        <div class="px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
            <div class="text-[12px] font-semibold text-[#666D80] uppercase tracking-[.06em]">
                Asprak Terdaftar
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($aspraks as $asprak)
            <div class="flex items-center gap-3 px-4 py-3 border-b border-[#F8F9FB] last:border-0">
                <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E);">
                    {{ strtoupper(substr($asprak->user?->name ?? 'A', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-[12px] font-semibold text-[#0D0D12] truncate">{{ $asprak->user?->name ?? '—' }}</div>
                    <div class="text-[11px] text-[#666D80]">{{ $asprak->user?->email ?? '—' }}</div>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-[12px] text-[#A4ABB8]">Belum ada asprak.</div>
            @endforelse
        </div>
    </div>

</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
