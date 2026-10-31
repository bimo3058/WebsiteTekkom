{{--
    Pencarian + panel filter (Bidang & Tahun) daftar kegiatan — dipakai bersama oleh
    Rencana Proker, Pelaksanaan Kegiatan, dan Laporan & Arsip. Panelnya sama dengan
    panel "Advanced Filters" tabel Audit Log global (partials/filter-popover, wajib
    di-include halaman pemakai). Filter Kategori sengaja tidak ada: pilihannya
    (Kegiatan Himpunan / Kegiatan Prodi) sudah terwakili Bidang — "Prodi" di sana
    mencakup kegiatan tanpa bidang atau berkategori Prodi.

    Variabel: $action (URL daftar), $placeholder, $bidangList, $tahunList,
    $adaTanpaTahun (opsi "Belum ada tanggal" hanya dirender bila memang ada isinya),
    $periodeList (Kegiatan::daftarPeriode). Periode Kepengurusan (S-62) mengikuti masa
    jabatan kabinet HIMASKOM, bukan tahun kalender, dan berlaku juga untuk kegiatan
    Prodi (S-64). Daftar kabinet & tanggal mulainya di Support\PeriodeKepengurusan.
    Opsional: $tahapList (Kegiatan::TAHAP_FILTER_LABELS) — hanya Rencana Proker,
    memunculkan filter Tahap (Di Rencana Proker / Di Pelaksanaan Kegiatan / Di Laporan &
    Arsip). Tanpa opsi "Semua Tahap": saat kosong tampil petunjuk abu yang tak bisa
    dipilih, dan filter dikosongkan lewat Reset (29 Okt 2026).
--}}
@php
    $tahapList         = $tahapList ?? null;
    $filterBidangAktif = request()->filled('bidang') && request('bidang') !== 'semua';
    $filterTahunAktif  = request()->filled('tahun') && request('tahun') !== 'semua';
    $filterTahapAktif  = $tahapList && request()->filled('status_rencana') && array_key_exists(request('status_rencana'), $tahapList);
    $filterPeriodeAktif = \Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan::ada(request('periode'));
    $adaFilterPanel    = $filterBidangAktif || $filterPeriodeAktif || $filterTahunAktif || $filterTahapAktif;
    $adaFilterApaPun   = $adaFilterPanel || request()->filled('search');
    $tanpaTahun        = \Modules\ManajemenMahasiswa\Models\Kegiatan::FILTER_TANPA_TAHUN;
@endphp
<form method="GET" action="{{ $action }}" id="filterForm">
    {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
    @if(request()->filled('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
    @endif
    <div class="mk-kegiatan-filter-row">
        <div class="mk-kegiatan-search w-100">
            <span class="mk-kegiatan-search__icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="mk-kegiatan-search__input"
                   placeholder="{{ $placeholder }}" value="{{ request('search') }}">
        </div>
        <div class="mk-kegiatan-filter-controls">
            <div class="filter-pop" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($adaFilterPanel)
                        <span class="filter-pop-dot"></span>
                    @endif
                </button>

                <div class="filter-pop-backdrop" x-show="filterOpen" x-cloak style="display: none;"
                     @click="filterOpen = false"></div>

                <div class="filter-pop-panel" x-show="filterOpen" x-cloak style="display: none;"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    <p class="filter-pop-title">Advanced Filters</p>

                    <div class="filter-pop-fields">
                        <div>
                            <label class="filter-pop-label" for="filterBidang">Bidang</label>
                            <x-manajemenmahasiswa::ui.select name="bidang" id="filterBidang">
                                <option value="semua">Semua Bidang</option>
                                <option value="prodi" {{ request('bidang') === 'prodi' ? 'selected' : '' }}>Prodi</option>
                                @foreach($bidangList as $bidang)
                                    <option value="{{ $bidang->id }}" {{ request('bidang') == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama_bidang }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterPeriode">Periode Kepengurusan</label>
                            <x-manajemenmahasiswa::ui.select name="periode" id="filterPeriode">
                                <option value="semua">Semua Periode</option>
                                @foreach($periodeList as $p)
                                    <option value="{{ $p }}" {{ (string) request('periode') === (string) $p ? 'selected' : '' }}>{{ \Modules\ManajemenMahasiswa\Support\PeriodeKepengurusan::label($p) }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterTahun">Tahun</label>
                            <x-manajemenmahasiswa::ui.select name="tahun" id="filterTahun">
                                <option value="semua">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                                {{-- Kegiatan tanpa tanggal sama sekali: tahunnya tidak diketahui, jadi
                                     tanpa opsi ini ia cuma muncul di "Semua Tahun" dan sulit ditemukan. --}}
                                @if($adaTanpaTahun)
                                    <option value="{{ $tanpaTahun }}" {{ request('tahun') === $tanpaTahun ? 'selected' : '' }}>Belum ada tanggal</option>
                                @endif
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        @if($tahapList)
                            <div>
                                <label class="filter-pop-label" for="filterTahap">Tahap Rencana</label>
                                <x-manajemenmahasiswa::ui.select name="status_rencana" id="filterTahap">
                                    <option value="" disabled hidden {{ $filterTahapAktif ? '' : 'selected' }}>Pilih tahap</option>
                                    @foreach($tahapList as $kunci => $label)
                                        <option value="{{ $kunci }}" {{ request('status_rencana') === $kunci ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </x-manajemenmahasiswa::ui.select>
                            </div>
                        @endif

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilterApaPun)
                                <a href="{{ $action }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
