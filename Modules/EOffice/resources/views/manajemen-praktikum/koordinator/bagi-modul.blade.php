<x-eoffice::manajemen-praktikum.layout pageTitle="Pembagian Modul ke Asisten">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Pembagian Modul ke Asisten Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Tentukan asisten mana yang mengelola modul mana</div>
    </div>
</div>

<div class="flex gap-[14px] flex-1 min-h-0">

    {{-- Panel Kiri: Daftar Asprak --}}
    <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0" style="width:280px; flex-shrink:0;">
        <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
            <div class="font-bold text-[14px] text-[#0D0D12]">Asisten Praktikum</div>
            <div class="text-[12px] text-[#666D80] mt-[2px]">{{ $asistenList->count() }} asprak terdaftar</div>
        </div>
        <div class="overflow-y-auto flex-1">
            @forelse($asistenList ?? [] as $a)
            <div class="px-5 py-[10px] border-b border-[#F8F9FB] last:border-0">
                <div class="flex items-center gap-[10px]">
                    <div class="w-[30px] h-[30px] rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#1a6691,#40C4AA);">
                        {{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[12px] font-semibold text-[#0D0D12] truncate">{{ $a->user?->name ?? '—' }}</div>
                        <div class="text-[10px] text-[#666D80]">{{ $a->modulAsprak->count() }} modul</div>
                    </div>
                </div>
                @if($a->modulAsprak->isNotEmpty())
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($a->modulAsprak as $ma)
                    <span class="text-[10px] font-semibold px-2 py-[2px] rounded-full bg-[#D1F0F9] text-[#106A97]">{{ $ma->modul?->nama }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="py-8 text-center text-[12px] text-[#A4ABB8]">Belum ada asprak.</div>
            @endforelse
        </div>
    </div>

    {{-- Panel Kanan: Form Bagi Modul --}}
    <div class="flex flex-col gap-[14px] flex-1 min-w-0 overflow-y-auto">

        {{-- Form Assign --}}
        <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
            <div class="font-bold text-[14px] text-[#0D0D12] mb-4">Assign Asprak ke Modul</div>
            <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <select name="asprak_id" required class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#106A97]">
                            <option value="">— Pilih Asprak —</option>
                            @foreach($asistenList ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->user?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <select name="modul_id" required class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#106A97]">
                            <option value="">— Pilih Modul —</option>
                            @foreach($modulList ?? [] as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit"
                        class="px-5 py-[9px] rounded-[9px] bg-[#106A97] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0e5a80]">Assign Modul</button>
            </form>
        </div>

        {{-- Tabel Distribusi Saat Ini --}}
        <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
            <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                <div class="font-bold text-[14px] text-[#0D0D12]">Distribusi Saat Ini</div>
            </div>
            <div class="flex px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
                <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Modul</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:180px;">Asisten Praktikum</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Aksi</div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($distribusiList ?? [] as $d)
                <div class="flex items-center px-5 py-[11px] border-b border-[#F8F9FB] last:border-0">
                    <div class="flex-1">
                        <div class="text-[13px] font-medium text-[#0D0D12]">{{ $d->modul?->nama ?? '—' }}</div>
                    </div>
                    <div class="text-[12px] text-[#353849]" style="width:180px;">{{ $d->asprak?->user?->name ?? '—' }}</div>
                    <div style="width:80px;">
                        <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.destroy', $d->id) }}"
                              onsubmit="return confirm('Hapus distribusi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[11px] font-semibold px-2 py-[4px] rounded-[6px] bg-[#FADAE1] text-[#7C1028] border-none cursor-pointer hover:bg-[#f5b8c5]">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-[13px] text-[#666D80]">Belum ada distribusi modul.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
