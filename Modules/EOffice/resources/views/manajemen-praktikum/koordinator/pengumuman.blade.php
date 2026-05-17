<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pengumuman Praktikum</h1>
        <p class="mp-page-sub">Buat dan kelola pengumuman untuk praktikan</p>
    </div>
    <div class="mp-page-actions">
        <button onclick="document.getElementById('modalCreate').classList.remove('hidden')" class="mp-btn primary md">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Pengumuman
        </button>
    </div>
</div>

<div class="flex flex-col gap-3 flex-1">
    @forelse($pengumuman ?? [] as $p)
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <div style="font-size:14px;font-weight:700;color:var(--c-fg);">{{ $p->judul }}</div>
                    @if($p->is_published)
                    <span class="mp-badge success sm">Dipublikasikan</span>
                    @else
                    <span class="mp-badge warning sm">Draft</span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--c-fg-muted);">{{ $p->created_at?->format('d M Y, H:i') }}</div>
                <div style="font-size:13px;color:var(--c-fg-sub);margin-top:8px;line-height:1.6;">{{ $p->konten }}</div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <form method="POST" action="{{ route('eoffice.manprak.koor.pengumuman.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus pengumuman ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="mp-btn destructive sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="mp-card flex-1 flex items-center justify-center" style="min-height:200px;">
        <div style="text-align:center;color:var(--c-fg-placeholder);">
            <svg class="mx-auto mb-3 w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
            <div style="font-size:14px;font-weight:500;">Belum ada pengumuman.</div>
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
                    <input type="text" name="judul" required placeholder="Judul pengumuman..." class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Isi Pengumuman <span class="text-red-500">*</span></label>
                    <textarea name="konten" rows="5" required placeholder="Tulis isi pengumuman di sini..."
                              class="mp-input w-full resize-none"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" checked class="accent-[#0B266E]">
                    <span style="font-size:12px;font-weight:600;color:var(--c-fg-sub);">Publikasikan langsung</span>
                </label>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="mp-btn secondary md flex-1">Batal</button>
                <button type="submit" class="mp-btn primary md flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
