<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi Praktikum">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Absensi Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Catat kehadiran mahasiswa per sesi modul</div>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-[12px] text-[#666D80]">Sesi:</span>
        <input type="date" id="tglFilter" value="{{ date('Y-m-d') }}"
               class="border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] text-[13px] focus:outline-none focus:border-[#40C4AA]">
    </div>
</div>

{{-- Pilih Modul --}}
<div class="flex gap-3 flex-shrink-0">
    @forelse($modulDiampu ?? [] as $m)
    <a href="{{ route('eoffice.manprak.asprak.absensi.show', $m->id) }}"
       class="flex items-center gap-2 px-4 py-[9px] rounded-[10px] border no-underline transition-all
              {{ request()->route('modulId') == $m->id
                 ? 'bg-[#40C4AA] border-[#40C4AA] text-white'
                 : 'bg-white border-[#DFE1E7] text-[#353849] hover:border-[#40C4AA]' }}">
        <span class="text-[13px] font-semibold">{{ $m->nama }}</span>
    </a>
    @empty
    <div class="text-[13px] text-[#A4ABB8]">Belum ada modul.</div>
    @endforelse
</div>

@if(isset($modul) && isset($praktikan))
{{-- Form Absensi --}}
<div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
        <div>
            <div class="font-bold text-[15px] text-[#0D0D12]">{{ $modul->nama }}</div>
            <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $praktikan->count() }} mahasiswa terdaftar</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.asprak.absensi.store', $modul->id) }}" id="absensiForm">
            @csrf
            <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
            <button type="submit"
                    class="px-4 py-[8px] rounded-[9px] bg-[#40C4AA] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#32a896]">
                Simpan Absensi
            </button>
        </form>
    </div>

    {{-- Thead --}}
    <div class="flex px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:50px;">No</div>
        <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama Mahasiswa</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:120px;">NIM</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em] text-center" style="width:80px;">Hadir</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em] text-center" style="width:80px;">Izin</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em] text-center" style="width:80px;">Tidak Hadir</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:150px;">Keterangan</div>
    </div>

    {{-- Rows --}}
    <div class="overflow-y-auto flex-1">
        @foreach($praktikan as $i => $prak)
        @php $existing = $absensiHariIni[$prak->id] ?? null; @endphp
        <div class="flex items-center px-5 py-[11px] border-b border-[#F8F9FB] last:border-0 hover:bg-[#FAFAFC]">
            <div class="text-[12px] text-[#666D80]" style="width:50px;">{{ $i + 1 }}</div>
            <div class="flex-1 flex items-center gap-[10px] pr-3 min-w-0">
                <div class="w-[28px] h-[28px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#0D6B55,#40C4AA);">
                    {{ strtoupper(substr($prak->user?->name ?? 'M', 0, 2)) }}
                </div>
                <span class="text-[13px] font-medium text-[#0D0D12] truncate">{{ $prak->user?->name }}</span>
            </div>
            <div class="text-[12px] text-[#666D80]" style="width:120px;">{{ $prak->user?->student_number ?? '—' }}</div>
            {{-- Radio buttons --}}
            @foreach(['hadir','izin','tidak_hadir'] as $status)
            <div class="flex justify-center" style="width:80px;">
                <input type="radio" form="absensiForm"
                       name="absensi[{{ $prak->id }}][status]"
                       value="{{ $status }}"
                       {{ ($existing?->status ?? 'hadir') === $status ? 'checked' : '' }}
                       class="w-4 h-4 cursor-pointer accent-[#40C4AA]">
            </div>
            @endforeach
            <div style="width:150px;">
                <input type="text" form="absensiForm"
                       name="absensi[{{ $prak->id }}][keterangan]"
                       value="{{ $existing?->keterangan ?? '' }}"
                       placeholder="Opsional..."
                       class="w-full border border-[#DFE1E7] rounded-[6px] px-2 py-[4px] text-[12px] focus:outline-none focus:border-[#40C4AA]">
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="flex-1 bg-white border border-[#DFE1E7] rounded-[14px] flex items-center justify-center">
    <div class="text-center text-[#A4ABB8]">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
        <div class="text-[14px] font-medium">Pilih modul di atas untuk mulai mengisi absensi</div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
