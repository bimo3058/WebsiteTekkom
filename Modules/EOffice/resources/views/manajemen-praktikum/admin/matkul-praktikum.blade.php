<x-eoffice::manajemen-praktikum.layout pageTitle="Mata Kuliah Praktikum">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Mata Kuliah Praktikum</h1>
        <p class="mp-page-sub">Daftar mata kuliah yang memiliki komponen praktikum — Kurikulum 2024 S1 Teknik Komputer UNDIP</p>
    </div>
    <div class="mp-page-actions">
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="mp-btn primary md">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Matkul
        </button>
    </div>
</div>

{{-- Filter & search --}}
<div class="flex gap-2 flex-wrap items-center flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama..."
               class="mp-input" style="width:220px;">
        <select name="semester" class="mp-input mp-select">
            <option value="">Semua Semester</option>
            @for($s = 1; $s <= 8; $s++)
            <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
            @endfor
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
        @if(request()->hasAny(['search','semester']))
        <a href="{{ route('eoffice.manprak.admin.matkul-praktikum.index') }}" class="mp-btn secondary sm">Reset</a>
        @endif
    </form>
    <div class="ml-auto" style="font-size:12px;color:var(--c-fg-muted);">Total: <strong>{{ $matkulList->total() }}</strong> matkul</div>
</div>

{{-- Tabel per semester --}}
@php
    $grouped = $matkulList->getCollection()->groupBy('semester');
@endphp

@foreach($grouped->sortKeys() as $sem => $items)
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <div class="flex items-center gap-3">
            <span class="mp-badge primary sm">Semester {{ $sem }}</span>
            <span style="font-size:12px;font-weight:600;color:var(--c-fg);">{{ $items->count() }} mata kuliah</span>
        </div>
    </div>
    <table class="w-full" style="font-size:13px;">
        <thead>
            <tr style="border-bottom:1px solid var(--c-border-light);">
                <th class="mp-th text-left" style="padding:8px 20px;width:160px;">Kode MK</th>
                <th class="mp-th text-left" style="padding:8px 20px;">Nama Mata Kuliah</th>
                <th class="mp-th text-left" style="padding:8px 20px;width:60px;">SKS</th>
                <th class="mp-th text-left" style="padding:8px 20px;width:120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $mk)
            <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                <td style="padding:12px 20px;">
                    <span class="mp-badge primary sm" style="font-family:monospace;">{{ $mk->kode }}</span>
                </td>
                <td style="padding:12px 20px;font-weight:500;color:var(--c-fg);">{{ $mk->nama }}</td>
                <td style="padding:12px 20px;text-align:center;font-weight:700;color:var(--c-fg-sub);">{{ $mk->sks }}</td>
                <td style="padding:12px 20px;">
                    <div class="flex gap-2">
                        <button onclick="openEdit({{ $mk->id }}, '{{ addslashes($mk->kode) }}', '{{ addslashes($mk->nama) }}', {{ $mk->sks }}, {{ $mk->semester ?? 'null' }})"
                                class="mp-btn secondary sm">Edit</button>
                        <form method="POST" action="{{ route('eoffice.manprak.admin.matkul-praktikum.destroy', $mk->id) }}"
                              onsubmit="return confirm('Hapus {{ addslashes($mk->nama) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mp-btn destructive sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

@if($matkulList->isEmpty())
<div class="mp-card flex-shrink-0" style="padding:48px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">
    Belum ada mata kuliah praktikum.
</div>
@endif

@if($matkulList->hasPages())
<div class="flex-shrink-0">{{ $matkulList->links() }}</div>
@endif

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex items-center justify-between">
            <div class="font-bold text-[15px] text-[#0D0D12]">Tambah Mata Kuliah Praktikum</div>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="text-[#A4ABB8] hover:text-[#353849] bg-transparent border-none cursor-pointer text-[20px] leading-none">×</button>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.matkul-praktikum.store') }}" class="px-6 py-5 flex flex-col gap-4">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode MK <span class="text-red-500">*</span></label>
                <input type="text" name="kode" required placeholder="cth: TSK1624107" maxlength="20"
                       class="mp-input w-full" style="font-family:monospace;">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="cth: Praktikum Pemrograman Dasar"
                       class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">SKS <span class="text-red-500">*</span></label>
                    <input type="number" name="sks" required min="1" max="6" placeholder="1"
                           class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester</label>
                    <select name="semester" class="mp-input mp-select w-full">
                        <option value="">— Pilih —</option>
                        @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}">Semester {{ $s }}</option>
                        @endfor
                    </select>
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

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex items-center justify-between">
            <div class="font-bold text-[15px] text-[#0D0D12]">Edit Mata Kuliah Praktikum</div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-[#A4ABB8] hover:text-[#353849] bg-transparent border-none cursor-pointer text-[20px] leading-none">×</button>
        </div>
        <form id="form-edit" method="POST" action="" class="px-6 py-5 flex flex-col gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode MK <span class="text-red-500">*</span></label>
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
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester</label>
                    <select id="edit-semester" name="semester" class="mp-input mp-select w-full">
                        <option value="">— Pilih —</option>
                        @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}">Semester {{ $s }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button type="submit" class="mp-btn primary md">Perbarui</button>
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
    const sel = document.getElementById('edit-semester');
    for (let o of sel.options) o.selected = (parseInt(o.value) === semester);
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

</x-eoffice::manajemen-praktikum.layout>
