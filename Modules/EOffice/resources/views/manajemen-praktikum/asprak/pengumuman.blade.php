<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Asprak">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Pengumuman</h1>
        <p class="mp-page-sub">Buat pengumuman untuk praktikum yang Anda ampu</p>
    </div>
</div>

@if(!$asprak)
<div class="mp-alert warning flex-shrink-0">Status asprak Anda belum aktif.</div>
@else
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.store') }}" class="flex flex-col gap-3">
        @csrf
        <input type="hidden" name="praktikum_id" value="{{ $asprak->praktikum_id }}">
        <input name="judul" required placeholder="Judul pengumuman" class="mp-input w-full">
        <textarea name="konten" rows="4" required placeholder="Isi pengumuman" class="mp-input w-full"></textarea>
        <label class="flex items-center gap-2 text-[13px] font-semibold" style="color:var(--c-fg-sub);">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" class="accent-[#40C4AA]" checked>
            Publikasikan
        </label>
        <button class="mp-btn success md self-start">Buat Pengumuman</button>
    </form>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Daftar Pengumuman</span>
    </div>
    @forelse($pengumumans as $p)
    <div class="mp-tr" style="display:flex;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid var(--c-border-light);">
        <div>
            <div style="font-weight:600;font-size:14px;color:var(--c-fg);">{{ $p->judul }}</div>
            <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $p->user?->name }} — {{ $p->created_at?->format('d M Y H:i') }}</div>
            <div style="font-size:13px;color:var(--c-fg-sub);margin-top:8px;">{{ $p->konten }}</div>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.asprak.pengumuman.destroy', $p->id) }}" onsubmit="return confirm('Hapus pengumuman ini?')" class="flex-shrink-0">
            @csrf @method('DELETE')
            <button class="mp-btn destructive sm">Hapus</button>
        </form>
    </div>
    @empty
    <div style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada pengumuman.</div>
    @endforelse
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
