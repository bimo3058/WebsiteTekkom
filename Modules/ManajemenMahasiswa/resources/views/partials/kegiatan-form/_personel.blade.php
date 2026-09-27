{{--
    Isi kartu "Personel Kegiatan": Ketua Pelaksana, Dosen Pendamping, dan Panitia beserta
    jabatannya. Dipakai bersama oleh semua form kegiatan:
      • Rencana Proker (create & edit) dan Pelaksanaan (edit), lewat _fields.blade.php
      • Laporan & Arsip (create & edit), yang punya form sendiri
    Dulu ketiga kotak ini ditulis ulang di dua tempat dengan JavaScript manual;
    kini satu partial Alpine (lihat _pilih-assets.blade.php).

    Variabel include:
      $mahasiswaList      koleksi Student (opsi Ketua & Panitia)
      $dosenList          koleksi Lecturer (opsi Dosen Pendamping)
      $ketuaId            id ketua terpilih — sudah memperhitungkan old()
      $dosenTerpilih      koleksi Lecturer terpilih — sudah memperhitungkan old()
      $panitiaTerpilih    koleksi Student terpilih — sudah memperhitungkan old()
      $panitiaPeranLama   old('panitia_peran', []); jabatan dari database dibaca
                          lewat pivot bila tidak ada isian terakhir
--}}
@include('manajemenmahasiswa::partials.kegiatan-form._pilih-assets')

@php
    $opsiMahasiswa = $mahasiswaList->map(fn ($mhs) => [
        'id'   => $mhs->id,
        'nama' => $mhs->user->name ?? 'N/A',
        'sub'  => 'NIM: ' . $mhs->student_number . ' • Angkatan ' . $mhs->cohort_year,
        'cari' => \Illuminate\Support\Str::lower(($mhs->user->name ?? '') . ' ' . $mhs->student_number),
    ])->values();

    $opsiDosen = $dosenList->map(fn ($dosen) => [
        'id'   => $dosen->id,
        'nama' => $dosen->user->name ?? 'N/A',
        'sub'  => 'NIP: ' . $dosen->employee_number,
        'cari' => \Illuminate\Support\Str::lower(($dosen->user->name ?? '') . ' ' . $dosen->employee_number),
    ])->values();

    $panitiaPeranLama = $panitiaPeranLama ?? [];
    $panitiaPeranAwal = [];
    foreach ($panitiaTerpilih as $pan) {
        $panitiaPeranAwal[$pan->id] = $panitiaPeranLama[$pan->id] ?? $pan->pivot->peran ?? '';
    }
@endphp

<div class="row g-3 mb-3">
    {{-- ── Ketua Pelaksana (pilih satu) ── --}}
    <div class="col-md-6 mk-pilih" x-data="mkPilihSatu(@js($opsiMahasiswa), @js((string) ($ketuaId ?? '')))">
        <label class="form-label-custom" for="ketuaPelaksanaSearch">Ketua Pelaksana</label>
        <div class="search-select-wrapper" @click.outside="tutup()">
            <input type="hidden" name="ketua_pelaksana_id" id="ketuaPelaksanaId" :value="terpilih">
            <input type="text" class="form-control form-control-custom" id="ketuaPelaksanaSearch"
                   placeholder="Cari nama mahasiswa..."
                   autocomplete="off"
                   role="combobox" aria-controls="ketuaPelaksanaDropdown" :aria-expanded="open"
                   x-model="query"
                   @focus="buka()" @click="buka()" @input="buka()"
                   @keydown.enter.prevent="pilihPertama()"
                   @keydown.escape.prevent="tutup()" @keydown.tab="tutup()">
            <svg class="mk-pilih-caret" :class="{ 'is-open': open }" width="12" height="12" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>

            <div class="search-select-dropdown" id="ketuaPelaksanaDropdown" role="listbox"
                 x-show="open" style="display: none;"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <template x-for="o in hasil" :key="o.id">
                    <div class="search-select-option" role="option"
                         :class="{ 'is-selected': String(o.id) === terpilih }"
                         :aria-selected="String(o.id) === terpilih"
                         @click="pilih(o)">
                        <span x-text="o.nama"></span>
                        <div class="sub-text" x-text="o.sub"></div>
                    </div>
                </template>
                <div class="panitia-empty" x-show="hasil.length === 0">Tidak ada mahasiswa yang cocok</div>
            </div>
        </div>
    </div>

    {{-- ── Dosen Pendamping (pilih banyak) ── --}}
    <div class="col-md-6 mk-pilih" x-data="mkPilihBanyak(@js($opsiDosen), @js($dosenTerpilih->pluck('id')->values()))">
        <label class="form-label-custom" for="dosenSearchInput">
            Dosen Pendamping <span style="color: var(--c-fg-muted); font-weight: 400;">(opsional)</span>
            <span class="panitia-count-badge" x-show="terpilih.length" x-text="terpilih.length + ' dipilih'" style="display: none;"></span>
        </label>
        <div class="panitia-select-wrapper" id="dosenSelectWrapper" @click.outside="open = false">
            <div class="panitia-chips-container" @click="$refs.cari.focus()">
                <template x-for="o in daftarTerpilih" :key="o.id">
                    <span class="panitia-chip">
                        <span x-text="o.nama"></span>
                        <button type="button" class="panitia-chip-remove" title="Hapus dosen"
                                :aria-label="'Hapus dosen ' + o.nama" @click.stop="hapus(o.id)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </span>
                </template>
                <input type="text" class="panitia-search-input" id="dosenSearchInput" x-ref="cari"
                       :placeholder="terpilih.length ? 'Tambah dosen lain...' : 'Cari dan tambah dosen pendamping...'"
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
                <template x-for="o in hasil" :key="o.id">
                    <div class="panitia-option" role="option" :class="{ selected: dipilih(o.id) }"
                         :aria-selected="dipilih(o.id)" @click="pilih(o)">
                        <div>
                            <span x-text="o.nama"></span>
                            <div class="sub-text" x-text="o.sub"></div>
                        </div>
                        <span class="check-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                    </div>
                </template>
                <div class="panitia-empty" x-show="hasil.length === 0">Tidak ada dosen yang cocok</div>
            </div>

            <template x-for="id in terpilih" :key="'dosen-' + id">
                <input type="hidden" name="dosen_pendamping_ids[]" :value="id">
            </template>
        </div>
        <div class="checkbox-hint">Bisa lebih dari satu. Ketik nama atau NIP untuk mencari.</div>
    </div>
</div>

{{-- ── Panitia Kegiatan (pilih banyak + jabatan) ── --}}
<div class="mb-1 mk-pilih" x-data="mkPilihBanyak(@js($opsiMahasiswa), @js($panitiaTerpilih->pluck('id')->values()))">
    <label class="form-label-custom" for="panitiaSearchInput">
        Panitia Kegiatan
        <span style="color: var(--c-fg-muted); font-weight: 400;">(opsional)</span>
        <span class="panitia-count-badge" x-show="terpilih.length" x-text="terpilih.length + ' dipilih'" style="display: none;"></span>
    </label>
    <div class="panitia-select-wrapper" id="panitiaSelectWrapper" @click.outside="open = false">
        <div class="panitia-chips-container" @click="$refs.cari.focus()">
            <template x-for="o in daftarTerpilih" :key="o.id">
                <span class="panitia-chip">
                    <span x-text="o.nama"></span>
                    <button type="button" class="panitia-chip-remove" title="Hapus panitia"
                            :aria-label="'Hapus panitia ' + o.nama" @click.stop="hapus(o.id)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </span>
            </template>
            <input type="text" class="panitia-search-input" id="panitiaSearchInput" x-ref="cari"
                   :placeholder="terpilih.length ? 'Tambah lebih banyak...' : 'Cari dan tambah panitia...'"
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
            <template x-for="o in hasil" :key="o.id">
                <div class="panitia-option" role="option" :class="{ selected: dipilih(o.id) }"
                     :aria-selected="dipilih(o.id)" @click="pilih(o)">
                    <div>
                        <span x-text="o.nama"></span>
                        <div class="sub-text" x-text="o.sub"></div>
                    </div>
                    <span class="check-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                </div>
            </template>
            <div class="panitia-empty" x-show="hasil.length === 0">Tidak ada mahasiswa yang cocok</div>
        </div>

        <template x-for="id in terpilih" :key="'panitia-' + id">
            <input type="hidden" name="panitia_ids[]" :value="id">
        </template>
    </div>
    <div class="checkbox-hint">Pilih satu atau lebih mahasiswa sebagai panitia. Ketik nama untuk mencari.</div>

    {{-- Jabatan tiap panitia. `peran` disimpan per id, jadi jabatan yang sudah diketik
         tidak hilang walau panitianya sempat dilepas lalu dipilih lagi. --}}
    <div id="panitiaRolesContainer" class="mt-3 d-flex flex-column gap-2" x-data="{ peran: @js((object) $panitiaPeranAwal) }">
        <template x-for="o in daftarTerpilih" :key="'peran-' + o.id">
            <div class="d-flex align-items-center gap-3 p-2 border rounded bg-light">
                <div style="flex: 1; font-size: 13px; font-weight: 600; color: var(--c-fg-sec);" x-text="o.nama"></div>
                <div style="flex: 2;">
                    <input type="text" class="form-control form-control-sm"
                           :name="'panitia_peran[' + o.id + ']'"
                           placeholder="Masukkan Jabatan (misal: Sekretaris, Bendahara, dll)"
                           x-model="peran[o.id]">
                </div>
            </div>
        </template>
    </div>
</div>
