<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Modul">

<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Kelola Modul</h1>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum terdaftar di praktikum manapun' }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai Asisten Praktikum aktif.</div>
@else

{{-- Form Buat Modul Baru --}}
<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div class="mp-card-title" style="margin-bottom:16px;">Buat Modul Baru</div>
    <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.store') }}" class="grid grid-cols-2 gap-3">
        @csrf
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Nama Modul <span class="text-red-500">*</span></label>
            <input name="nama" value="{{ old('nama') }}" required class="mp-input w-full" placeholder="cth. Modul 1 — Pengenalan">
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Jadwal / Minggu</label>
            <input name="jadwal_minggu" value="{{ old('jadwal_minggu') }}" class="mp-input w-full" placeholder="cth. Minggu 1 / Senin 08.00–10.00">
        </div>
        <div>
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Urutan <span class="text-red-500">*</span></label>
            <input type="number" name="urutan" value="{{ old('urutan', $moduls->count() + 1) }}" min="1" required class="mp-input w-full">
        </div>
        <div class="row-span-2 flex flex-col">
            <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="mp-input w-full flex-1" placeholder="Deskripsi singkat modul ini">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="col-span-2 flex justify-end">
            <button class="mp-btn primary md">+ Tambah Modul</button>
        </div>
    </form>
</div>

{{-- Daftar Modul --}}
<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Modul Praktikum</span>
        <span style="font-size:12px;color:var(--c-fg-muted);">{{ $moduls->count() }} modul</span>
    </div>

    @forelse($moduls as $modul)
    @php $isMine = $assignedModulIds->contains($modul->id); @endphp
    <div class="mp-tr" style="display:flex;align-items:flex-start;gap:16px;padding:16px 20px;border-bottom:1px solid var(--c-border-light);">
        {{-- Urutan --}}
        <div class="flex items-center justify-center flex-shrink-0"
             style="width:36px;height:36px;border-radius:50%;font-size:13px;font-weight:700;
                    background:{{ $isMine ? 'var(--c-bg-sub)' : '#F0F1F4' }};
                    color:{{ $isMine ? 'var(--c-primary)' : 'var(--c-fg-muted)' }};">
            {{ $modul->urutan }}
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span style="font-size:14px;font-weight:600;color:var(--c-fg);">{{ $modul->nama }}</span>
                @if($isMine)
                <span class="mp-badge success sm">Diampu Anda</span>
                @endif
                @if($modul->kode_modul)
                <span class="mp-badge primary sm" style="font-family:monospace;">{{ $modul->kode_modul }}</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $modul->jadwal_minggu ?? '—' }}</div>
            @if($modul->deskripsi)
            <div style="font-size:12px;color:var(--c-fg-sub);margin-top:4px;" class="line-clamp-2">{{ $modul->deskripsi }}</div>
            @endif
            <div class="flex gap-3 mt-2" style="font-size:11px;color:var(--c-fg-muted);">
                <span>{{ $modul->materi->count() }} materi</span>
                <span>{{ $modul->tugas->count() }} tugas</span>
                <span>{{ $modul->modulAsprak->count() }} asprak</span>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('eoffice.manprak.asprak.modul.show', $modul->id) }}" class="mp-btn primary sm" style="text-decoration:none;">Detail</a>

            <button onclick="openEdit({{ $modul->id }}, '{{ addslashes($modul->nama) }}', {{ $modul->urutan }}, '{{ addslashes($modul->jadwal_minggu ?? '') }}', '{{ addslashes($modul->deskripsi ?? '') }}')"
                    class="mp-btn secondary sm">Edit</button>

            <form method="POST" action="{{ route('eoffice.manprak.asprak.modul.destroy', $modul->id) }}"
                  onsubmit="return confirm('Hapus modul {{ addslashes($modul->nama) }}? Materi dan tugas di dalamnya juga akan dihapus.')">
                @csrf @method('DELETE')
                <button class="mp-btn destructive sm">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div style="padding:48px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">
        Belum ada modul. Buat modul pertama di atas.
    </div>
    @endforelse
</div>

{{-- Modal Edit Modul --}}
<div id="modal-edit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-[480px] mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <div style="font-weight:700;font-size:15px;color:var(--c-fg);">Edit Modul</div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    style="color:var(--c-fg-muted);font-size:18px;line-height:1;border:none;background:transparent;cursor:pointer;">✕</button>
        </div>
        <form id="form-edit" method="POST" class="flex flex-col gap-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Nama Modul</label>
                <input id="edit-nama" name="nama" required class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Urutan</label>
                    <input id="edit-urutan" type="number" name="urutan" min="1" required class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Jadwal</label>
                    <input id="edit-jadwal" name="jadwal_minggu" class="mp-input w-full">
                </div>
            </div>
            <div>
                <label class="block text-[12px] font-semibold mb-1" style="color:var(--c-fg-sub);">Deskripsi</label>
                <textarea id="edit-deskripsi" name="deskripsi" rows="3" class="mp-input w-full"></textarea>
            </div>
            <div class="flex gap-2 justify-end mt-1">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button class="mp-btn primary md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, nama, urutan, jadwal, deskripsi) {
    document.getElementById('form-edit').action = '/eoffice/manprak/asprak/modul/' + id;
    document.getElementById('edit-nama').value    = nama;
    document.getElementById('edit-urutan').value  = urutan;
    document.getElementById('edit-jadwal').value  = jadwal;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

@endif

</x-eoffice::manajemen-praktikum.layout>
