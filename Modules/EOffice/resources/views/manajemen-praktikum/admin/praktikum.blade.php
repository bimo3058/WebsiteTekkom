<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikum">

{{-- Header --}}
<div class="flex items-center justify-between flex-shrink-0">
    <div>
        <div class="text-[20px] font-bold text-[#0D0D12]">Daftar Praktikum</div>
        <div class="text-[12px] text-[#666D80] mt-[2px]">Kelola semua praktikum terdaftar</div>
    </div>
    <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-[9px] rounded-[9px] bg-[#0B266E] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0a1f5c]">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Praktikum
    </button>
</div>

{{-- Filter Bar --}}
<div class="flex gap-3 flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode..."
               class="flex-1 border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] text-[13px] focus:outline-none focus:border-[#0B266E]">
        <select name="status" class="border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] text-[13px] focus:outline-none focus:border-[#0B266E]">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status')==='aktif'?'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif'?'selected':'' }}>Nonaktif</option>
        </select>
        <select name="semester" class="border border-[#DFE1E7] rounded-[8px] px-3 py-[7px] text-[13px] focus:outline-none focus:border-[#0B266E]">
            <option value="">Semua Semester</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
        </select>
        <button type="submit" class="px-4 py-[7px] rounded-[8px] bg-[#0B266E] text-white text-[13px] font-medium border-none cursor-pointer">Filter</button>
        <a href="{{ route('eoffice.manprak.admin.praktikum.index') }}" class="px-4 py-[7px] rounded-[8px] border border-[#DFE1E7] text-[13px] text-[#666D80] no-underline hover:bg-[#F6F8FA]">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-1 min-h-0">
    {{-- Thead --}}
    <div class="flex px-5 py-[10px] bg-[#FAFBFC] border-b border-[#DFE1E7] flex-shrink-0">
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:100px;">Kode</div>
        <div class="flex-1 text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]">Nama Praktikum</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:160px;">Dosen Pengampu</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:140px;">Koordinator</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Praktikan</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:90px;">Status</div>
        <div class="text-[11px] font-semibold text-[#666D80] uppercase tracking-[.06em]" style="width:80px;">Aksi</div>
    </div>

    {{-- Rows --}}
    <div class="overflow-y-auto flex-1">
        @forelse($praktikums ?? [] as $p)
        <div class="flex items-center px-5 py-[12px] border-b border-[#F8F9FB] hover:bg-[#FAFAFC] last:border-0">
            <div class="text-[12px] font-bold tracking-wider" style="width:100px; color:#0B266E;">{{ $p->kode ?? '—' }}</div>
            <div class="flex-1 pr-3">
                <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $p->nama }}</div>
                <div class="text-[11px] text-[#666D80]">{{ $p->tahun_ajaran }} / Sem. {{ $p->semester }}</div>
            </div>
            <div class="text-[12px] text-[#353849] truncate" style="width:160px;">{{ $p->dosen?->name ?? '—' }}</div>
            <div class="text-[12px] text-[#353849] truncate" style="width:140px;">
                @if($p->koordinator)
                    {{ $p->koordinator->name }}
                @else
                    <span class="text-[#DF1C41] text-[11px] font-semibold">Belum ditunjuk</span>
                @endif
            </div>
            <div class="text-[13px] font-semibold text-[#0D0D12] text-center" style="width:80px;">{{ $p->daftar_praktikan_count ?? 0 }}</div>
            <div style="width:90px;">
                @if($p->status === 'aktif')
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#DDF2EE] text-[#174E43]">
                    <span class="w-[5px] h-[5px] rounded-full bg-[#40C4AA]"></span>Aktif
                </span>
                @else
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-[9px] py-[3px] rounded-full bg-[#F0F1F4] text-[#666D80]">
                    <span class="w-[5px] h-[5px] rounded-full bg-[#A4ABB8]"></span>Nonaktif
                </span>
                @endif
            </div>
            <div class="flex gap-1" style="width:80px;">
                <a href="{{ route('eoffice.manprak.admin.praktikum.edit', $p->id) }}"
                   class="flex items-center justify-center w-[28px] h-[28px] rounded-[6px] border border-[#DFE1E7] bg-white no-underline hover:bg-[#F6F8FA]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.destroy', $p->id) }}"
                      onsubmit="return confirm('Hapus praktikum {{ $p->nama }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="flex items-center justify-center w-[28px] h-[28px] rounded-[6px] border border-[#FADAE1] bg-[#FADAE1] cursor-pointer hover:bg-[#f5b8c5]">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="py-14 text-center text-[13px] text-[#666D80]">Belum ada data praktikum.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(isset($praktikums) && method_exists($praktikums, 'links'))
    <div class="px-5 py-3 border-t border-[#DFE1E7] flex-shrink-0">
        {{ $praktikums->links() }}
    </div>
    @endif
</div>

{{-- Modal Create --}}
<div id="modalCreate" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Praktikum Baru</div>
            <button onclick="document.getElementById('modalCreate').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#F6F8FA] border-none bg-transparent cursor-pointer text-[#666D80] text-lg">×</button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.store') }}" class="p-6">
            @csrf
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Praktikum <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Praktikum Algoritma dan Pemrograman"
                           class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun_ajaran" required placeholder="2025" value="{{ now()->year }}"
                               class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester <span class="text-red-500">*</span></label>
                        <select name="semester" required class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap" selected>Genap</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dosen Pengampu</label>
                    <select name="dosen_id" class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                        <option value="">— Pilih Dosen —</option>
                        @foreach($dosenList ?? [] as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat praktikum..."
                              class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E] resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Status</label>
                    <select name="status" class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="flex-1 py-[9px] rounded-[9px] border border-[#DFE1E7] text-[13px] font-medium text-[#353849] bg-white cursor-pointer hover:bg-[#F6F8FA]">Batal</button>
                <button type="submit"
                        class="flex-1 py-[9px] rounded-[9px] bg-[#0B266E] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0a1f5c]">Simpan</button>
            </div>
        </form>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
