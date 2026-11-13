{{--
    Anggaran per sumber dana (S-80, usulan Pak Maman L-41) — dipakai Rencana Proker,
    Pelaksanaan, serta Tambah/Edit Laporan & Arsip. Menggantikan kolom tunggal
    "Anggaran (Rp)"; totalnya dihitung otomatis dan disimpan ke kolom `anggaran`.

    Variabel: $kegiatan (Kegiatan|null), $tandaWajib (bool, tanda *),
    $realisasi (bool — teks bantu "yang terpakai" untuk Pelaksanaan/Arsip).
--}}
@php
    $kegiatan   = $kegiatan ?? null;
    $tandaWajib = $tandaWajib ?? false;
    $realisasi  = $realisasi ?? false;
    $sd = \Modules\ManajemenMahasiswa\Support\SumberDana::class;
    $nilai = collect($sd::KOLOM)->mapWithKeys(fn ($l, $k) => [$k => $sd::nilaiInput($kegiatan, $k)])->all();
    // Kegiatan lama: anggaran tersimpan tanpa rincian.
    $anggaranLama = $kegiatan && $kegiatan->exists && $kegiatan->anggaran !== null && !$sd::terisi($kegiatan)
        ? (float) $kegiatan->anggaran : null;
@endphp
<div class="mb-1" x-data="mkSumberDana(@js($nilai))">
    <label class="form-label-custom">
        Anggaran per Sumber Dana (Rp) @if($tandaWajib)<span class="required">*</span>@endif
    </label>
    <div class="row g-2">
        @foreach($sd::KOLOM as $kolom => $label)
            <div class="col-12 col-md-4">
                <label class="mk-sd-label" for="sd_{{ $kolom }}">{{ $label }}</label>
                {{-- step="any": lihat catatan di form lama — step="1000" diam-diam menolak 750500 --}}
                <input type="number" name="{{ $kolom }}" id="sd_{{ $kolom }}" class="form-control form-control-custom"
                       x-model="nilai.{{ $kolom }}" min="0" max="9999999999999" step="any" inputmode="numeric"
                       placeholder="0">
            </div>
        @endforeach
    </div>
    <div class="mk-sd-total">
        <span>Total anggaran {{ $realisasi ? 'terpakai' : '' }}</span>
        <strong x-text="total()"></strong>
    </div>
    <div class="mk-sd-hint">
        {{ $realisasi ? 'Isi sesuai LPJ.' : 'Isi sesuai bagian "Sumber Dana" di proposal.' }}
        Kosongkan sumber yang tidak dipakai; isi 0 bila kegiatan tanpa biaya.
        @if($anggaranLama !== null)
            <br><span style="color: var(--c-warning, #b45309);">Sebelumnya tercatat Rp {{ number_format($anggaranLama, 0, ',', '.') }} tanpa rincian sumber dana.</span>
        @endif
    </div>
</div>

@once
<style>
    .mk-sd-label { display: block; font-size: 12px; font-weight: 500; color: var(--c-fg-sec, #475467); margin-bottom: 4px; }
    .mk-sd-total { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; padding: 10px 12px;
        background: var(--c-surface-subtle, #F8F9FB); border: 1px solid var(--c-border, #DFE1E7); border-radius: 8px; font-size: 13px; color: var(--c-fg-sec, #475467); }
    .mk-sd-total strong { font-size: 15px; color: var(--c-primary, #0B266E); }
    .mk-sd-hint { font-size: 11px; color: var(--c-fg-muted); margin-top: 6px; font-weight: 500; line-height: 1.5; }
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mkSumberDana', (nilai) => ({
            nilai: nilai,
            total() {
                const isi = Object.values(this.nilai).filter(v => v !== null && v !== '');
                if (!isi.length) return '—';
                const jumlah = isi.reduce((a, v) => a + (Number(v) || 0), 0);
                return 'Rp ' + jumlah.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            },
        }));
    });
</script>
@endonce
