<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Modul">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">
    Anda belum ditugaskan sebagai koordinator praktikum aktif.
</div>
@else
<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div style="font-weight:700;font-size:14px;color:var(--c-fg);margin-bottom:16px;">Tambah Modul</div>
        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.store') }}" class="flex flex-col gap-3">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul</label>
                <input name="nama" required class="mp-input w-full" placeholder="Modul 1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan</label>
                <input type="number" name="urutan" min="1" value="{{ ($moduls->max('urutan') ?? 0) + 1 }}" required class="mp-input w-full">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jadwal / Minggu</label>
                <input name="jadwal_minggu" class="mp-input w-full" placeholder="Minggu ke-1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none"></textarea>
            </div>
            <button class="mp-btn primary md w-full">Simpan Modul</button>
        </form>
    </div>

    <div class="mp-card min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Daftar Modul</span>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full" style="font-size:13px;">
                <thead style="background:#FAFBFC;border-bottom:1px solid var(--c-border);">
                    <tr>
                        <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Kode</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Asprak</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Konten</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($moduls as $m)
                    <tr class="mp-tr" style="border-bottom:1px solid var(--c-border-light);">
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:var(--c-fg);">{{ $m->urutan }}. {{ $m->nama }}</div>
                            <div style="font-size:11px;color:var(--c-fg-muted);">{{ $m->jadwal_minggu ?? 'Jadwal belum diisi' }}</div>
                        </td>
                        <td style="padding:12px 16px;color:var(--c-fg-sub);">
                            <div style="font-family:monospace;font-size:12px;font-weight:600;">{{ $m->kode_modul ?? '-' }}</div>
                            <form method="POST" action="{{ route('eoffice.manprak.koor.modul.generate-kode', $m->id) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="mp-btn ghost sm" style="font-size:11px;">
                                    {{ $m->kode_modul ? 'Refresh Kode' : 'Generate Kode' }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;color:var(--c-fg-muted);">{{ $m->modulAsprak->pluck('asprak.user.name')->filter()->join(', ') ?: '-' }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:var(--c-fg-muted);">{{ $m->materi->count() }} materi, {{ $m->tugas->count() }} tugas</td>
                        <td style="padding:12px 16px;">
                            <div class="flex gap-2">
                                <a href="{{ route('eoffice.manprak.koor.modul.show', $m->id) }}" class="mp-btn primary sm" style="text-decoration:none;">Detail</a>
                                <form method="POST" action="{{ route('eoffice.manprak.koor.modul.destroy', $m->id) }}" onsubmit="return confirm('Hapus modul ini?')">
                                    @csrf @method('DELETE')
                                    <button class="mp-btn destructive sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:40px;text-align:center;font-size:13px;color:var(--c-fg-placeholder);">Belum ada modul.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
