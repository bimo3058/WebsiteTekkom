<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Tugas">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Kelola Tugas Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Buat tugas dan nilai pengumpulan mahasiswa</div>
    </div>
    <a href="{{ route('eoffice.manprak.asprak.tugas.create') }}"
       class="flex items-center gap-2 px-4 py-[9px] rounded-[9px] bg-[#40C4AA] text-white text-[13px] font-semibold no-underline hover:bg-[#32a896]">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat Tugas Baru
    </a>
</div>

@forelse($tugasList ?? [] as $tugas)
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <div class="text-[14px] font-bold text-[#0D0D12]">{{ $tugas->judul }}</div>
                @php
                    $dl = \Carbon\Carbon::parse($tugas->deadline);
                    $lewat = now()->gt($dl);
                @endphp
                @if($lewat)
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F0F1F4] text-[#666D80]">Berakhir</span>
                @else
                <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Aktif</span>
                @endif
            </div>
            <div class="text-[12px] text-[#666D80]">
                Modul: <span class="font-semibold text-[#353849]">{{ $tugas->modul?->nama ?? '—' }}</span>
                · Deadline: <span class="font-semibold {{ $lewat ? 'text-[#666D80]' : 'text-[#353849]' }}">{{ $dl->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="text-center">
                <div class="text-[16px] font-bold text-[#0D0D12]">{{ $tugas->pengumpulan_count ?? 0 }}</div>
                <div class="text-[10px] text-[#666D80]">Dikumpul</div>
            </div>
            <div class="text-center">
                <div class="text-[16px] font-bold text-[#D39C3D]">{{ $tugas->pending_nilai_count ?? 0 }}</div>
                <div class="text-[10px] text-[#666D80]">Belum Dinilai</div>
            </div>
            <button @click="open = !open"
                    class="text-[12px] font-semibold px-4 py-[7px] rounded-[8px] border border-[#DFE1E7] text-[#353849] bg-white cursor-pointer hover:bg-[#F6F8FA]"
                    x-text="open ? 'Tutup' : 'Lihat Pengumpulan'">
            </button>
        </div>
    </div>

    {{-- Pengumpulan Table --}}
    <div x-show="open" x-transition class="mt-4 border-t border-[#F0F1F4] pt-4">
        @php $pengumpulanList = $tugas->pengumpulan ?? collect(); @endphp
        @if($pengumpulanList->isEmpty())
        <div class="py-5 text-center text-[13px] text-[#A4ABB8]">Belum ada yang mengumpulkan.</div>
        @else
        <div class="rounded-[10px] border border-[#DFE1E7] overflow-hidden">
            <div class="flex px-4 py-[8px] bg-[#FAFBFC] border-b border-[#DFE1E7]">
                <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-wider">Mahasiswa</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-wider" style="width:110px;">Waktu Kumpul</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-wider" style="width:80px;">File</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-wider" style="width:80px;">Nilai</div>
                <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-wider" style="width:160px;">Aksi</div>
            </div>
            @foreach($pengumpulanList as $peng)
            <div class="flex items-center px-4 py-[10px] border-b border-[#F8F9FB] last:border-0 hover:bg-[#FAFAFC]">
                <div class="flex-1 flex items-center gap-2 min-w-0 pr-2">
                    <div class="text-[13px] font-medium text-[#0D0D12] truncate">{{ $peng->daftarPraktikan?->user?->name ?? '—' }}</div>
                </div>
                <div class="text-[11px] text-[#666D80]" style="width:110px;">{{ $peng->created_at?->format('d M, H:i') }}</div>
                <div style="width:80px;">
                    @if($peng->file_path)
                    <a href="{{ Storage::url($peng->file_path) }}" target="_blank"
                       class="text-[11px] font-semibold text-[#0B266E] no-underline hover:underline">Unduh</a>
                    @else
                    <span class="text-[11px] text-[#A4ABB8]">—</span>
                    @endif
                </div>
                <div style="width:80px;">
                    @if($peng->nilai !== null)
                    <span class="text-[14px] font-bold" style="color:{{ $peng->nilai >= 75 ? '#40C4AA' : ($peng->nilai >= 50 ? '#D39C3D' : '#DF1C41') }};">{{ $peng->nilai }}</span>
                    @else
                    <span class="text-[12px] text-[#A4ABB8]">—</span>
                    @endif
                </div>
                <div class="flex gap-1" style="width:160px;">
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai', $peng->id) }}" class="flex gap-1">
                        @csrf
                        <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0-100"
                               value="{{ $peng->nilai ?? '' }}"
                               class="w-[70px] border border-[#DFE1E7] rounded-[6px] px-2 py-[4px] text-[12px] focus:outline-none focus:border-[#40C4AA]">
                        <button type="submit"
                                class="text-[11px] font-semibold px-2 py-[4px] rounded-[6px] bg-[#DDF2EE] text-[#174E43] border-none cursor-pointer hover:bg-[#c0e8e0]">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.revisi', $peng->id) }}">
                        @csrf
                        <button type="submit"
                                class="text-[11px] font-semibold px-2 py-[4px] rounded-[6px] bg-[#F9ECCB] text-[#7C5309] border-none cursor-pointer hover:bg-[#f0e0b0]">Revisi</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@empty
<div class="flex-1 bg-white border border-[#DFE1E7] rounded-[14px] flex items-center justify-center min-h-[200px]">
    <div class="text-center text-[#A4ABB8]">
        <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <div class="text-[14px] font-medium">Belum ada tugas. Buat tugas baru!</div>
    </div>
</div>
@endforelse

</x-eoffice::manajemen-praktikum.layout>
