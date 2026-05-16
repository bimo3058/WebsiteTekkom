<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Praktikum">

<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Pengumuman Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Buat dan kelola pengumuman untuk praktikan</div>
    </div>
    <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-[9px] rounded-[9px] bg-[#106A97] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0e5a80]">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat Pengumuman
    </button>
</div>

<div class="flex flex-col gap-3 flex-1">
    @forelse($pengumuman ?? [] as $p)
    <div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <div class="text-[14px] font-bold text-[#0D0D12]">{{ $p->judul }}</div>
                    @if($p->is_published)
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#DDF2EE] text-[#174E43]">Dipublikasikan</span>
                    @else
                    <span class="text-[11px] font-semibold px-2 py-[2px] rounded-full bg-[#F9ECCB] text-[#7C5309]">Draft</span>
                    @endif
                </div>
                <div class="text-[12px] text-[#666D80]">{{ $p->created_at?->format('d M Y, H:i') }}</div>
                <div class="text-[13px] text-[#353849] mt-2 leading-[1.6]">{{ $p->konten }}</div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <form method="POST" action="{{ route('eoffice.manprak.koor.pengumuman.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus pengumuman ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-1 text-[12px] font-medium px-3 py-[6px] rounded-[7px] bg-[#FADAE1] text-[#7C1028] border-none cursor-pointer hover:bg-[#f5b8c5]">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="flex-1 bg-white border border-[#DFE1E7] rounded-[14px] flex items-center justify-center min-h-[200px]">
        <div class="text-center text-[#A4ABB8]">
            <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
            <div class="text-[14px] font-medium">Belum ada pengumuman.</div>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Create --}}
<div id="modalCreate" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Buat Pengumuman Baru</div>
            <button onclick="document.getElementById('modalCreate').classList.add('hidden')"
                    class="text-[#666D80] text-xl bg-transparent border-none cursor-pointer hover:text-[#0D0D12]">×</button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.koor.pengumuman.store') }}" class="p-6">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Judul pengumuman..."
                           class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#106A97]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                    <textarea name="konten" rows="5" required placeholder="Tulis isi pengumuman di sini..."
                              class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#106A97] resize-none"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" checked class="accent-[#106A97]">
                    <span class="text-[12px] font-semibold text-[#353849]">Publikasikan langsung</span>
                </label>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="flex-1 py-[9px] rounded-[9px] border border-[#DFE1E7] text-[13px] font-medium text-[#353849] bg-white cursor-pointer hover:bg-[#F6F8FA]">Batal</button>
                <button type="submit"
                        class="flex-1 py-[9px] rounded-[9px] bg-[#106A97] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0e5a80]">Simpan</button>
            </div>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
