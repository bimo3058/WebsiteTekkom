<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Modul">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">{{ $praktikum?->nama ?? 'Belum ada praktikum aktif' }} · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">
    Anda belum ditugaskan sebagai koordinator praktikum aktif.
</div>
@else

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Tambah & Kelola Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">
    {{-- Form Tambah Modul --}}
    <div class="mp-card flex-shrink-0" style="padding:20px;">
        <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Tambah Modul</div>
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

    {{-- Daftar Modul --}}
    <div class="mp-card min-h-0">
        <div class="mp-card-header">
            <span class="mp-card-title">Daftar Modul</span>
            <div class="right">
                <span class="mp-badge neutral sm">{{ $moduls->count() }} modul</span>
            </div>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="mp-table">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>

                        <th class="mp-th text-left" style="padding:10px 16px;">Asisten Praktikum</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Konten</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($moduls as $m)
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:#0D0D12;">{{ $m->urutan }}. {{ $m->nama }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $m->jadwal_minggu ?? 'Jadwal belum diisi' }}</div>
                        </td>

                        <td style="padding:12px 16px;font-size:12px;color:#666D80;">{{ $m->modulAsprak->pluck('asprak.user.name')->filter()->join(', ') ?: '-' }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:#666D80;">{{ $m->materi->count() }} materi, {{ $m->tugas->count() }} tugas</td>
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
                    <tr>
                        <td colspan="5">
                            <div style="padding:48px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
