<x-eoffice::manajemen-praktikum.layout pageTitle="Materi Modul">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Materi Modul</h1>
        <p class="mp-page-sub">Unggah dan kelola materi untuk modul yang Anda ampu</p>
    </div>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.store') }}" enctype="multipart/form-data" class="grid grid-cols-4 gap-3 items-end">
        @csrf
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Modul</label>
            <select name="modul_id" required class="mp-input mp-select w-full">
                <option value="">Pilih modul</option>
                @foreach($modulsForSelect as $m)
                <option value="{{ $m->id }}">{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Judul</label>
            <input name="judul" required class="mp-input w-full">
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">File</label>
            <input type="file" name="file" class="mp-input w-full">
        </div>
        <button class="mp-btn success md">Upload</button>
        <textarea name="deskripsi" rows="2" class="mp-input col-span-4" placeholder="Deskripsi singkat"></textarea>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Materi</span>
    </div>
    @forelse($materis as $materi)
    <div class="mp-tr" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 20px;border-bottom:1px solid var(--c-border-light);">
        <div>
            <div style="font-weight:600;font-size:13px;color:var(--c-fg);">{{ $materi->judul }}</div>
            <div style="font-size:11px;color:var(--c-fg-muted);">{{ $materi->modul?->nama }} — {{ $materi->created_at?->format('d M Y H:i') }}</div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $materi->deskripsi }}</div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            @if($materi->file_path)
            <a href="{{ Storage::url($materi->file_path) }}" target="_blank" class="mp-btn primary sm" style="text-decoration:none;">Unduh</a>
            @endif
            <form method="POST" action="{{ route('eoffice.manprak.asprak.materi.destroy', $materi->id) }}" onsubmit="return confirm('Hapus materi ini?')">
                @csrf @method('DELETE')
                <button class="mp-btn destructive sm">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada materi.</div>
    @endforelse
</div>

</x-eoffice::manajemen-praktikum.layout>
