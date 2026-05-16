<x-eoffice::manajemen-praktikum.layout pageTitle="Edit Periode Pendaftaran">

{{-- Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Edit Periode Pendaftaran</h1>
        <p class="mp-page-sub">Atur jadwal buka/tutup pendaftaran dan status aktifnya</p>
    </div>
    <div class="mp-page-actions">
        <a href="{{ route('eoffice.manprak.admin.periode-pendaftaran.index', ['matkul_id' => $periode->praktikum?->matkul_id, 'praktikum_id' => $periode->praktikum_id]) }}"
           class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>

@php
    $statusLabel = $periode->isSedangBuka()
        ? ['label' => 'Sedang Buka', 'variant' => 'success']
        : ($periode->is_aktif ? ['label' => 'Terjadwal / Lewat Waktu', 'variant' => 'warning'] : ['label' => 'Ditutup', 'variant' => 'neutral']);
@endphp

<div class="grid grid-cols-[1fr_320px] gap-[14px] flex-1 min-h-0">
    <div class="mp-card">
        <div class="mp-card-header">
            <div>
                <div style="font-weight:700;font-size:15px;color:var(--c-fg);">{{ $periode->nama }}</div>
                <div style="font-size:12px;color:var(--c-fg-muted);margin-top:2px;">{{ $periode->praktikum?->nama ?? '-' }}</div>
            </div>
            <span class="mp-badge {{ $statusLabel['variant'] }} sm">{{ $statusLabel['label'] }}</span>
        </div>
        <div class="mp-card-body" style="padding:24px;">
            <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.update', $periode->id) }}" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Praktikum</label>
                        <select name="praktikum_id" required class="mp-input mp-select w-full">
                            @foreach($praktikumList as $praktikum)
                            <option value="{{ $praktikum->id }}" {{ $periode->praktikum_id === $praktikum->id ? 'selected' : '' }}>
                                {{ $praktikum->nama }}
                                @if($praktikum->matkul) - {{ $praktikum->matkul->kode }} @endif
                                @if($praktikum->dosen) - {{ $praktikum->dosen->name }} @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jenis</label>
                        <select name="jenis" required class="mp-input mp-select w-full">
                            <option value="koor" {{ $periode->jenis === 'koor' ? 'selected' : '' }}>Koordinator Praktikum</option>
                            <option value="asprak" {{ $periode->jenis === 'asprak' ? 'selected' : '' }}>Asisten Praktikum</option>
                            <option value="praktikan" {{ $periode->jenis === 'praktikan' ? 'selected' : '' }}>Praktikan (IRS)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Periode</label>
                    <input name="nama" value="{{ old('nama', $periode->nama) }}" required class="mp-input w-full">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dibuka Pada</label>
                        <input type="datetime-local" name="dibuka_pada"
                               value="{{ old('dibuka_pada', $periode->dibuka_pada?->format('Y-m-d\TH:i')) }}"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Ditutup Pada</label>
                        <input type="datetime-local" name="ditutup_pada"
                               value="{{ old('ditutup_pada', $periode->ditutup_pada?->format('Y-m-d\TH:i')) }}"
                               class="mp-input w-full">
                    </div>
                </div>

                <label class="flex items-center gap-2" style="font-size:13px;font-weight:600;color:var(--c-fg-sub);">
                    <input type="hidden" name="is_aktif" value="0">
                    <input type="checkbox" name="is_aktif" value="1" class="accent-[#0B266E]" {{ old('is_aktif', $periode->is_aktif) ? 'checked' : '' }}>
                    Periode aktif
                </label>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="submit" class="mp-btn primary md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex flex-col gap-[14px]">
        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Aksi Cepat</span>
            </div>
            <div class="mp-card-body" style="padding:20px;">
                <div style="font-size:12px;color:var(--c-fg-muted);margin-bottom:16px;">Tutup manual akan menyembunyikan pengumuman pembukaan dan menghapus notifikasi pembukaan dari dashboard global.</div>
                @if($periode->is_aktif)
                <form method="POST" action="{{ route('eoffice.manprak.admin.periode-pendaftaran.tutup', $periode->id) }}">
                    @csrf
                    <button type="submit" class="mp-btn warning md w-full">Tutup Sekarang</button>
                </form>
                @else
                <div style="padding:10px;border-radius:9px;background:var(--c-bg-sub);color:var(--c-fg-muted);font-size:13px;font-weight:600;text-align:center;">Periode sudah ditutup</div>
                @endif
            </div>
        </div>

        <div class="mp-card flex-shrink-0">
            <div class="mp-card-header">
                <span class="mp-card-title">Ringkasan</span>
            </div>
            <div class="mp-card-body" style="padding:20px;">
                <div class="space-y-2" style="font-size:12px;color:var(--c-fg-muted);">
                    <div>Dibuka oleh: <span style="font-weight:600;color:var(--c-fg-sub);">{{ $periode->dibukaOleh?->name ?? '-' }}</span></div>
                    <div>Dibuat: <span style="font-weight:600;color:var(--c-fg-sub);">{{ $periode->created_at?->format('d M Y H:i') }}</span></div>
                    <div>Diubah: <span style="font-weight:600;color:var(--c-fg-sub);">{{ $periode->updated_at?->format('d M Y H:i') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
