<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')

<style>
    /* ── Filter Bar ── */
    .filter-section {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }
    .filter-chip {
        padding: 7px 16px;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: var(--c-surface);
        color: var(--c-fg-muted);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none !important;
        display: inline-block;
    }
    .filter-chip:hover {
        border-color: var(--c-primary);
        color: var(--c-primary);
        background: var(--c-primary-subtle);
    }
    .filter-chip.active {
        background: var(--c-primary);
        color: var(--c-surface) !important;
        border-color: var(--c-primary);
    }
    .filter-select-custom {
        padding: 0 14px;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: var(--c-surface);
        color: var(--c-fg-sec);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: all 0.15s;
        height: 38px;
    }
    .filter-select-custom:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
    }

    /* ── Search Bar (matching forum) ── */
    .search-wrapper {
        position: relative;
        flex-grow: 1;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--c-fg-muted);
        font-size: 14px;
    }
    .search-input {
        background-color: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 8px;
        height: 38px;
        padding-left: 36px;
        font-size: 13px;
        font-weight: 500;
        width: 100%;
        color: var(--c-fg-sec);
    }
    .search-input:focus {
        background-color: var(--c-surface);
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
        outline: none;
    }

    /* ── Kegiatan Cards ── */
    .kegiatan-card {
        background: var(--c-surface);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--c-border);
    }
    .kegiatan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px var(--c-primary-shadow);
        border-color: var(--c-primary-border);
    }
    .kegiatan-card-image {
        width: 100%;
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, var(--c-primary-subtle) 0%, var(--c-primary-shadow) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .kegiatan-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .kegiatan-card-image .placeholder-icon {
        font-size: 40px;
        opacity: 0.4;
    }
    .kegiatan-card-body {
        padding: 16px 18px 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .kegiatan-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .badge-bidang {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }

    .kegiatan-card-title {
        font-weight: 700;
        font-size: 15px;
        color: var(--c-fg);
        margin-bottom: 6px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .kegiatan-card-desc {
        font-size: 13px;
        color: var(--c-fg-muted);
        line-height: 1.5;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }
    .kegiatan-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: var(--c-fg-muted);
        font-weight: 500;
        padding-top: 10px;
        border-top: 1px solid var(--c-surface-muted);
    }
    .kegiatan-card-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: var(--c-fg-muted);
    }
    .empty-state .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    .empty-state h5 {
        color: var(--c-fg-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .empty-state p {
        font-size: 14px;
        color: var(--c-fg-muted);
    }
</style>

<!-- Page Header -->
<x-manajemenmahasiswa::ui.page-header bordered
    title="Manajemen Kegiatan"
    subtitle="Daftar kegiatan terbaru dari berbagai bidang kepengurusan">
    <x-slot:actions>
        @if($canTambahKegiatan)
            <a href="{{ route('manajemenmahasiswa.kegiatan.create') }}"
               class="mk-btn mk-btn--primary mk-btn--sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Kegiatan
            </a>
        @endif
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Search & Filter Area (matching forum layout) -->
<form method="GET" action="{{ route('manajemenmahasiswa.kegiatan.index') }}" id="filterForm">
    {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
    @if(request()->filled('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
    @endif
    <div class="mk-kegiatan-filter-row">
        <div class="mk-kegiatan-search w-100">
            <span class="mk-kegiatan-search__icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" name="search" class="mk-kegiatan-search__input"
                   placeholder="Cari judul atau deskripsi kegiatan..." value="{{ request('search') }}">
        </div>

        {{-- Bidang & Tahun dikumpulkan dalam satu panel, sama dengan panel "Advanced
             Filters" tabel Audit Log global (partials/filter-popover). Bidang dulu berupa
             deretan chip di bawah pencarian; kini jadi dropdown di panel ini, jadi ikut
             terbawa bersama Tahun tanpa hidden input tambahan. Filter Kategori sengaja
             tidak ada: pilihannya (Kegiatan Himpunan / Kegiatan Prodi) sudah terwakili
             Bidang — "Prodi" di sana mencakup kegiatan tanpa bidang atau berkategori Prodi.
             Badge kategori di kartu tetap tampil sebagai informasi. --}}
        @php
            $filterBidangAktif = request()->filled('bidang') && request('bidang') !== 'semua';
            $filterTahunAktif  = request()->filled('tahun') && request('tahun') !== 'semua';
            $adaFilterApaPun   = $filterBidangAktif || $filterTahunAktif
                || request()->filled('search');
        @endphp
        <div class="mk-kegiatan-filter-controls">
            <div class="filter-pop filter-pop--md" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($filterBidangAktif || $filterTahunAktif)
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
                                    <option value="{{ $bidang->id }}" {{ request('bidang') == $bidang->id ? 'selected' : '' }}>
                                        {{ $bidang->nama_bidang }}
                                    </option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterTahun">Tahun</label>
                            <x-manajemenmahasiswa::ui.select name="tahun" id="filterTahun">
                                <option value="semua">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                        {{ $t }}
                                    </option>
                                @endforeach
                                {{-- Kegiatan tanpa tanggal sama sekali: tahunnya tidak diketahui, jadi
                                     tanpa opsi ini ia cuma muncul di "Semua Tahun" dan sulit ditemukan. --}}
                                @if($adaTanpaTahun)
                                    <option value="{{ \Modules\ManajemenMahasiswa\Models\Kegiatan::FILTER_TANPA_TAHUN }}"
                                        {{ request('tahun') === \Modules\ManajemenMahasiswa\Models\Kegiatan::FILTER_TANPA_TAHUN ? 'selected' : '' }}>
                                        Belum ada tanggal
                                    </option>
                                @endif
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilterApaPun)
                                <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Kegiatan Cards Grid -->
@if($kegiatan->count() > 0)
    <div class="row g-4">
        @foreach($kegiatan as $item)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <a href="{{ route('manajemenmahasiswa.kegiatan.show', $item->id) }}" class="kegiatan-card">
                    <!-- Image -->
                    <div class="kegiatan-card-image">
                        @if($item->banner)
                            <img src="{{ $item->banner_url }}" alt="{{ $item->judul }}">
                        @else
                            <span class="placeholder-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary-border)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1 2-2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
                        @endif
                    </div>

                    <!-- Body -->
                    <div class="kegiatan-card-body">
                        <!-- Badges -->
                        <div class="kegiatan-badges">
                            @if($item->bidangs && $item->bidangs->count() > 0)
                                @foreach($item->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @elseif($item->bidang)
                                <span class="badge-bidang">{{ $item->bidang->nama_bidang }}</span>
                            @else
                                <span class="badge-bidang" style="background: var(--c-primary-subtle); color: var(--c-primary);">Prodi</span>
                            @endif
                            @if($item->kategoris && $item->kategoris->count() > 0)
                                @foreach($item->kategoris as $kat)
                                    <span class="badge-bidang">{{ $kat->nama_kategori }}</span>
                                @endforeach
                            @endif
                        </div>

                        <!-- Title -->
                        <div class="kegiatan-card-title">{{ $item->judul }}</div>

                        <!-- Description -->
                        <div class="kegiatan-card-desc">
                            {{ Str::limit(html_entity_decode(strip_tags($item->deskripsi)), 100) }}
                        </div>

                        <!-- Meta -->
                        <div class="kegiatan-card-meta">
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg> {{ $item->tanggal_mulai ? $item->tanggal_mulai->translatedFormat('d M Y') : 'Belum ditentukan' }}</span>
                            @if($item->lokasi)
                                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> {{ Str::limit($item->lokasi, 20) }}</span>
                            @endif
                            @if($item->jam_mulai)
                                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> {{ $item->jam_mulai_formatted }}{{ $item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : '' }} WIB</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Footer: Per page + Showing X to Y of Z results + nomor halaman (partial bersama).
         Daftar ini berupa grid kartu, jadi pilihannya kelipatan 6 (PerPage::KARTU). --}}
    @include('manajemenmahasiswa::partials.table-footer', [
        'paginator'      => $kegiatan,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
        <h5>Belum ada kegiatan</h5>
        <p>Kegiatan yang tersedia akan muncul di sini</p>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>
