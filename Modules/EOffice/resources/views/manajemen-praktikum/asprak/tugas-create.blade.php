<x-eoffice::manajemen-praktikum.layout pageTitle="Buat Tugas">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Buat Tugas Baru</h1>
        <p class="mp-page-sub">Pilih modul yang Anda ampu lalu isi detail tugas</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

<div class="mp-card flex-shrink-0" style="max-width:720px;padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.tugas.store') }}" class="flex flex-col gap-4">
        @csrf
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Modul</label>
            <select name="modul_id" required class="mp-input mp-select w-full">
                <option value="">Pilih modul</option>
                @foreach($moduls as $m)
                <option value="{{ $m->id }}">{{ $m->nama }} - {{ $m->praktikum?->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Judul Tugas</label>
            <input name="judul" required class="mp-input w-full">
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Deskripsi</label>
            <textarea name="deskripsi" rows="6" class="mp-input w-full"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Deadline</label>
                <input type="datetime-local" name="deadline" class="mp-input w-full">
            </div>
            <label class="flex items-center gap-2 text-[13px] font-semibold mt-6" style="color:var(--c-fg-sub);">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="accent-[#40C4AA]" checked>
                Publikasikan
            </label>
        </div>
        <button class="mp-btn success md self-start">Simpan Tugas</button>
    </form>
</div>

</x-eoffice::manajemen-praktikum.layout>
