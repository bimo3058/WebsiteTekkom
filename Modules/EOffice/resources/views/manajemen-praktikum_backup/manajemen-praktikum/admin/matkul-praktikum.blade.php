<x-eoffice::manajemen-praktikum.layout pageTitle="Mata Kuliah Praktikum">

{{-- ════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════ --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Mata Kuliah Praktikum</h1>
        <p class="mp-page-sub">Daftar mata kuliah yang memiliki komponen praktikum</p>
    </div>
</div>

{{-- ════════════════════════════════════════════
     CARD TABEL TERINTEGRASI
════════════════════════════════════════════ --}}
<div id="daftar-matkul" class="mp-card mt-2">

    {{-- Toolbar: Search + Filter + Button Tambah --}}
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex flex-wrap gap-3 items-center justify-between">
        <form id="filter-matkul-form" method="GET" class="flex flex-wrap gap-3 items-center flex-1">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode atau nama mata kuliah..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>

            {{-- Dropdown Semester --}}
            @php
                $semesterOptions = [['value' => '', 'label' => 'Semua Semester']];
                for($s = 1; $s <= 8; $s++) {
                    $semesterOptions[] = ['value' => (string)$s, 'label' => "Semester $s"];
                }
            @endphp
            <x-eoffice::manajemen-praktikum.ui.select 
                name="semester"
                :options="$semesterOptions"
                :selected="request('semester', '')"
                placeholder="Semua Semester"
                onChange="$event.target.form.submit()"
                minWidth="160px"
            />

            @if(request()->hasAny(['search','semester']))
            <a href="{{ route('eoffice.manprak.admin.matkul-praktikum.index') }}"
               class="mp-btn secondary md px-4" style="height:35px;">Reset</a>
            @endif
        </form>

        {{-- Tambah Matkul Button --}}
        <div class="flex-shrink-0 border-l border-[#DFE1E7] pl-4">
            <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
                    class="mp-btn primary md" style="height:35px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Mata Kuliah
            </button>
        </div>
    </div>

    {{-- Kolom Header Tabel --}}
    <div class="mp-card-body p-0">
        <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 120px 80px 1fr 60px 90px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Kode Mata Kuliah</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Semester</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Mata Kuliah</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">SKS</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Aksi</div>
        </div>

        {{-- Rows --}}
        @php $sortedItems = $matkulList->getCollection()->sortBy(['semester', 'kode']); @endphp

        @if($matkulList->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 gap-4">
            <div class="w-14 h-14 rounded-full bg-[#F6F8FA] flex items-center justify-center">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-[14px] font-semibold text-[#353849] mb-1">Belum ada mata kuliah</div>
                <div class="text-[12px] text-[#A4ABB8]">
                    @if(request()->hasAny(['search','semester']))
                        Tidak ada hasil untuk filter yang dipilih
                    @else
                        Klik "Tambah Mata Kuliah" untuk memulai
                    @endif
                </div>
            </div>
        </div>
        @else

        @foreach($sortedItems as $mk)
        <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 120px 80px 1fr 60px 90px;">

            {{-- Kode Mata Kuliah --}}
            <div>
                <span class="text-[12px] font-mono font-bold text-[#0B266E]">{{ $mk->kode }}</span>
            </div>

            {{-- Semester --}}
            <div class="text-[13px] font-semibold text-[#353849]">{{ $mk->semester }}</div>

            {{-- Nama --}}
            <div class="min-w-0">
                <div class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $mk->nama }}</div>
            </div>

            {{-- SKS --}}
            <div class="text-[13px] font-medium text-[#353849] text-center">
                {{ $mk->sks }} SKS
            </div>

            {{-- Aksi --}}
            <div class="flex items-center justify-center gap-1.5">
                <button onclick="openEdit({{ $mk->id }}, '{{ addslashes($mk->kode) }}', '{{ addslashes($mk->nama) }}', {{ $mk->sks }}, {{ $mk->semester ?? 'null' }})"
                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-[#DFE1E7] bg-white text-[#666D80] hover:bg-[#F6F8FA] hover:text-[#0B266E] transition-colors"
                        title="Edit Mata Kuliah">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                <form method="POST" action="{{ route('eoffice.manprak.admin.matkul-praktikum.destroy', $mk->id) }}"
                      onsubmit="return confirm('Hapus mata kuliah {{ addslashes($mk->nama) }}?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center justify-center w-8 h-8 rounded-lg border border-[#FADAE1] bg-[#FFF5F5] text-[#DF1C41] hover:bg-[#FEE2E2] transition-colors"
                            title="Hapus Mata Kuliah">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        @endif

    </div>{{-- /mp-card-body --}}

    {{-- Pagination --}}
    @if($matkulList->hasPages())
    <div class="p-4 border-t border-[#DFE1E7]">{{ $matkulList->links() }}</div>
    @endif

</div>

{{-- ════════════════════════════════════════════
     MODAL TAMBAH
════════════════════════════════════════════ --}}
<div id="modal-tambah" onclick="if(event.target === this) this.classList.add('hidden')" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl overflow-visible">
        <div class="px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[15px] text-[#0D0D12]">Tambah Mata Kuliah Praktikum</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.matkul-praktikum.store') }}" class="px-6 py-5 flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" name="kode" required placeholder="Contoh: TSK1624107" maxlength="20"
                       class="mp-input w-full" style="font-family:monospace;">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Praktikum Pemrograman Dasar"
                       class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">SKS <span class="text-red-500">*</span></label>
                    <input type="number" name="sks" required min="1" max="6" placeholder="1"
                           class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1.5">Semester <span class="text-[#DF1C41]">*</span></label>
                    <div x-data="{ open: false, selected: '' }" class="relative w-full">
                        <input type="hidden" name="semester" x-model="selected" required>
                        <button type="button" @click="open = !open" 
                                class="mp-input w-full flex items-center justify-between bg-white text-left text-[13px] h-[36px] px-3 border rounded-[8px] focus:outline-none transition-colors"
                                :class="open ? 'border-[#0B266E] shadow-[0_0_0_3px_rgba(11,38,110,0.1)]' : 'border-[#DFE1E7]'">
                            <span x-text="selected ? 'Semester ' + selected : '— Pilih Semester —'" :class="selected ? 'text-[#0D0D12]' : 'text-[#666D80]'"></span>
                            <svg class="w-4 h-4 text-[#666D80] transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none;" 
                             class="absolute z-10 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-lg shadow-[0_4px_12px_rgba(0,0,0,0.08)] py-1.5 max-h-48 overflow-y-auto">
                            @for($s = 1; $s <= 8; $s++)
                                <label class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-[#F6F8FA] cursor-pointer text-[13px] text-[#353849]">
                                    <input type="radio" value="{{ $s }}" x-model="selected" @change="open = false" class="hidden">
                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-colors flex-shrink-0" :class="selected == '{{ $s }}' ? 'border-[#0B266E]' : 'border-[#DFE1E7]'">
                                        <div class="w-2 h-2 rounded-full bg-[#0B266E]" x-show="selected == '{{ $s }}'" style="display: none;"></div>
                                    </div>
                                    <span>Semester {{ $s }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button type="submit" class="mp-btn primary md">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════
     MODAL EDIT
════════════════════════════════════════════ --}}
<div id="modal-edit" onclick="if(event.target === this) this.classList.add('hidden')" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl overflow-visible">
        <div class="px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[15px] text-[#0D0D12]">Edit Mata Kuliah Praktikum</div>
        </div>
        <form id="form-edit" method="POST" action="" class="px-6 py-5 flex flex-col gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" id="edit-kode" name="kode" required maxlength="20"
                       class="mp-input w-full" style="font-family:monospace;">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nama" name="nama" required class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">SKS <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-sks" name="sks" required min="1" max="6" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1.5">Semester <span class="text-[#DF1C41]">*</span></label>
                    <div x-data="{ open: false, selected: '' }" 
                         @set-semester.window="selected = $event.detail"
                         class="relative w-full">
                        <input type="hidden" name="semester" x-model="selected" required>
                        <button type="button" @click="open = !open" 
                                class="mp-input w-full flex items-center justify-between bg-white text-left text-[13px] h-[36px] px-3 border rounded-[8px] focus:outline-none transition-colors"
                                :class="open ? 'border-[#0B266E] shadow-[0_0_0_3px_rgba(11,38,110,0.1)]' : 'border-[#DFE1E7]'">
                            <span x-text="selected ? 'Semester ' + selected : '— Pilih Semester —'" :class="selected ? 'text-[#0D0D12]' : 'text-[#666D80]'"></span>
                            <svg class="w-4 h-4 text-[#666D80] transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" style="display: none;" 
                             class="absolute z-10 w-full mt-1.5 bg-white border border-[#DFE1E7] rounded-lg shadow-[0_4px_12px_rgba(0,0,0,0.08)] py-1.5 max-h-48 overflow-y-auto">
                            @for($s = 1; $s <= 8; $s++)
                                <label class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-[#F6F8FA] cursor-pointer text-[13px] text-[#353849]">
                                    <input type="radio" value="{{ $s }}" x-model="selected" @change="open = false" class="hidden">
                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-colors flex-shrink-0" :class="selected == '{{ $s }}' ? 'border-[#0B266E]' : 'border-[#DFE1E7]'">
                                        <div class="w-2 h-2 rounded-full bg-[#0B266E]" x-show="selected == '{{ $s }}'" style="display: none;"></div>
                                    </div>
                                    <span>Semester {{ $s }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button type="submit" class="mp-btn primary md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, kode, nama, sks, semester) {
    const base = '{{ url("eoffice/manprak/admin/matkul-praktikum") }}';
    document.getElementById('form-edit').action = base + '/' + id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-sks').value = sks;
    
    window.dispatchEvent(new CustomEvent('set-semester', { detail: semester }));
    
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

</x-eoffice::manajemen-praktikum.layout>
