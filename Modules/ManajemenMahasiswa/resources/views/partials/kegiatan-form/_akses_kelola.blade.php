{{--
    Bagian "Akses Kelola" — siapa saja, selain pemilik, yang boleh mengubah
    kegiatan ini. Dipakai bersama:
      • Rencana Proker (create & edit) dan Pelaksanaan (edit), lewat _fields.blade.php
      • Laporan & Arsip (create & edit), yang punya form sendiri

    Hanya dirender untuk pemilik kegiatan & override (KegiatanPolicy::aturAkses).
    Pengelola biasa tidak melihatnya, dan server mengabaikan input ini darinya
    (PengelolaKegiatanService::sync).

    Variabel dari PengelolaKegiatanService::dataForm(): $bolehAturAkses,
    $calonPengelola, $pengelolaTerpilih, $namaPembuat.

    Kotak pilihnya memakai komponen Alpine mkPilihBanyak (_pilih-assets.blade.php),
    sama dengan Dosen Pendamping & Panitia. Tampilannya meminjam kelas .panitia-*
    yang tersedia di kedua jenis form.
--}}
@if($bolehAturAkses ?? false)
@include('manajemenmahasiswa::partials.kegiatan-form._pilih-assets')
@php
    // Pengelola yang sudah tidak memegang role pengurus tidak ada di daftar calon,
    // jadi ikut terlepas saat form ini disimpan — route pun sudah menolaknya.
    $opsiPengelola = $calonPengelola->map(fn ($calon) => [
        'id'         => $calon['id'],
        'nama'       => $calon['nama'],
        'sub'        => $calon['role'],
        'bisa_hapus' => (bool) $calon['bisa_hapus'],
        'cari'       => \Illuminate\Support\Str::lower($calon['nama']),
    ])->values();
@endphp
<div class="form-card mk-pilih" x-data="mkPilihBanyak(@js($opsiPengelola), @js(array_values($pengelolaTerpilih)))">
    <div class="form-card-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Akses Kelola</div>

    <input type="hidden" name="akses_kelola_dikirim" value="1">

    <p style="font-size: 13px; color: var(--c-fg-muted); margin-bottom: 14px;">
        Pembuat: <strong style="color: var(--c-fg-sec);">{{ $namaPembuat }}</strong>.
        Admin dan Ketua Himpunan otomatis bisa mengelola semua kegiatan.
        Tambahkan pengurus lain yang boleh ikut mengubah kegiatan ini.
    </p>

    <label class="form-label-custom" for="pengelolaSearchInput">
        Pengelola
        <span style="color: var(--c-fg-muted); font-weight: 400;">(opsional)</span>
    </label>
    <div class="panitia-select-wrapper" id="pengelolaSelectWrapper" @click.outside="open = false">
        <div class="panitia-chips-container" @click="$refs.cari.focus()">
            <template x-for="p in daftarTerpilih" :key="p.id">
                <span class="panitia-chip">
                    <span x-text="p.nama"></span>
                    <button type="button" class="panitia-chip-remove" title="Hapus pengelola"
                            :aria-label="'Hapus pengelola ' + p.nama" @click.stop="hapus(p.id)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </span>
            </template>
            <input type="text" class="panitia-search-input" id="pengelolaSearchInput" x-ref="cari"
                   :placeholder="terpilih.length ? 'Tambah pengurus lain...' : 'Cari pengurus...'"
                   autocomplete="off"
                   x-model="query"
                   @focus="open = true" @click="open = true" @input="open = true"
                   @keydown.enter.prevent="pilihPertama()"
                   @keydown.escape.prevent="open = false" @keydown.tab="open = false">
            <svg class="mk-pilih-caret" :class="{ 'is-open': open }" width="12" height="12" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>

        <div class="panitia-dropdown" role="listbox" x-show="open" style="display: none;"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <template x-for="p in hasil" :key="p.id">
                <div class="panitia-option" role="option" :class="{ selected: dipilih(p.id) }"
                     :aria-selected="dipilih(p.id)" @click="pilih(p)">
                    <div>
                        <span x-text="p.nama"></span>
                        <div class="sub-text" x-text="p.sub"></div>
                    </div>
                    <span class="check-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            </template>
            <div class="panitia-empty" x-show="hasil.length === 0">Tidak ada pengurus yang cocok</div>
        </div>
    </div>
    <div class="checkbox-hint">Haknya mengikuti jabatan: Ketua Bidang/Unit yang ditambahkan langsung bisa mengedit sekaligus menghapus, Staff Himpunan hanya bisa mengedit.</div>

    {{-- Baris hak per pengelola + hidden input pengelola_ids[] --}}
    <div id="pengelolaRolesContainer" class="mt-3 d-flex flex-column gap-2">
        <template x-for="p in daftarTerpilih" :key="'hak-' + p.id">
            <div class="d-flex align-items-center gap-3 p-2 border rounded bg-light">
                <div style="flex: 1; font-size: 13px; font-weight: 600; color: var(--c-fg-sec);">
                    <span x-text="p.nama"></span>
                    <div style="font-size: 11px; font-weight: 500; color: var(--c-fg-muted);" x-text="p.sub"></div>
                </div>
                {{-- Bukan pilihan, melainkan keterangan: hak hapus melekat pada jabatan.
                     Yang menegakkannya KegiatanPolicy::delete, bukan kiriman form ini. --}}
                <span style="flex: none; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 999px; border: 1px solid; white-space: nowrap;"
                      :style="p.bisa_hapus
                          ? { background: 'var(--c-error-subtle)', color: 'var(--c-error)', borderColor: 'var(--c-error-subtle)' }
                          : { background: 'var(--c-bg)', color: 'var(--c-fg-sec)', borderColor: 'var(--c-border)' }"
                      :title="p.bisa_hapus
                          ? 'Ketua Bidang/Unit yang ditambahkan otomatis bisa mengedit sekaligus menghapus kegiatan ini'
                          : 'Staff Himpunan hanya bisa mengedit, tidak bisa menghapus'"
                      x-text="p.bisa_hapus ? 'Edit & hapus' : 'Boleh edit'"></span>
                <input type="hidden" name="pengelola_ids[]" :value="p.id">
            </div>
        </template>
    </div>
</div>
@endif
