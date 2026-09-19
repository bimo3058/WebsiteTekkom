<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')

<style>
    .filter-chip { padding:7px 16px;border-radius:8px;border:1px solid var(--c-border);background:var(--c-surface);color:var(--c-fg-muted);font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;text-decoration:none !important;display:inline-block; }
    .filter-chip:hover { border-color:var(--c-primary);color:var(--c-primary);background:var(--c-primary-subtle); }
    .filter-chip.active { background:var(--c-primary);color:var(--c-surface) !important;border-color:var(--c-primary); }
    .filter-select-custom { padding:0 14px;border-radius:8px;border:1px solid var(--c-border);background:var(--c-surface);color:var(--c-fg-sec);font-size:13px;font-weight:600;outline:none;height:38px;transition:all 0.15s; }
    .filter-select-custom:focus { border-color:var(--c-primary);box-shadow:0 0 0 3px var(--c-primary-subtle); }
    .search-wrapper { position:relative;flex-grow:1; }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--c-fg-muted); }
    .search-input { background:var(--c-surface);border:1px solid var(--c-border);border-radius:8px;height:38px;padding-left:36px;font-size:13px;width:100%;color:var(--c-fg-sec); }
    .search-input:focus { background:var(--c-surface);border-color:var(--c-primary);box-shadow:0 0 0 3px var(--c-primary-subtle);outline:none; }
    .filter-section { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;align-items:center; }

    /* Cards */
    .pelaksanaan-card { background:var(--c-surface);border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,0.04);transition:all 0.2s;text-decoration:none !important;display:flex;flex-direction:column;border:1px solid var(--c-border); }
    .pelaksanaan-card:hover { transform:translateY(-3px);box-shadow:0 12px 24px -4px var(--c-primary-shadow);border-color:var(--c-primary-border); }
    .card-banner { width:100%;aspect-ratio:16/9;background:linear-gradient(135deg,var(--c-primary-subtle),var(--c-primary-shadow));display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative; }
    .card-banner img { width:100%;height:100%;object-fit:cover; }
    .card-body { padding:16px 18px 18px;display:flex;flex-direction:column;flex:1; }
    .badge-bidang { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:var(--c-primary-subtle);color:var(--c-primary); }
    .card-title { font-weight:700;font-size:15px;color:var(--c-fg);margin:8px 0 10px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    .card-meta { display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:var(--c-fg-muted);font-weight:500;padding-top:10px;border-top:1px solid var(--c-surface-muted);margin-top:auto; }
    .card-meta span { display:inline-flex;align-items:center;gap:4px; }

    /* Status */
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700; }
    .status-disetujui { background:var(--c-primary-subtle);color:var(--c-primary); }
    .status-berlangsung { background:var(--c-sky-subtle);color:var(--c-sky); }
    .status-selesai { background:var(--c-surface-muted);color:var(--c-fg-sec); }
    .empty-state { text-align:center;padding:50px 20px;color:var(--c-fg-muted); }
    .empty-state h5 { color:var(--c-fg-muted);font-weight:600;margin-bottom:4px; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:var(--c-success-subtle);color:var(--c-success);font-weight:500;font-size:14px;">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:var(--c-fg);letter-spacing:-.02em;">Pelaksanaan Kegiatan</h3>
        <p class="mb-0" style="font-size:.82rem;color:var(--c-fg-muted);font-weight:500;">Proker yang sudah disetujui — lengkapi data pelaksanaan di sini</p>
    </div>
</div>


{{-- Filter --}}
<form method="GET" action="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" id="filterForm">
    {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
    @if(request()->filled('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
    @endif
    <div class="mk-kegiatan-filter-row">
        <div class="mk-kegiatan-search w-100">
            <span class="mk-kegiatan-search__icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="mk-kegiatan-search__input"
                   placeholder="Cari judul atau deskripsi kegiatan..." value="{{ request('search') }}">
        </div>
        {{-- Bidang & Tahun dikumpulkan dalam satu panel, sama dengan panel "Advanced
             Filters" tabel Audit Log global (partials/filter-popover). Bidang dulu berupa
             deretan chip di bawah pencarian; kini jadi dropdown di panel ini, sekaligus
             menutup celah lama: mengganti Tahun tidak lagi mengembalikan Bidang ke "Semua". --}}
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
                            <select name="bidang" id="filterBidang" class="filter-pop-select">
                                <option value="semua">Semua Bidang</option>
                                <option value="prodi" {{ request('bidang') === 'prodi' ? 'selected' : '' }}>Prodi</option>
                                @foreach($bidangList as $bidang)
                                    <option value="{{ $bidang->id }}" {{ request('bidang') == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama_bidang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterTahun">Tahun</label>
                            <select name="tahun" id="filterTahun" class="filter-pop-select">
                                <option value="semua">Semua Tahun</option>
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ request('tahun')==$t?'selected':'' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilterApaPun)
                                <a href="{{ route('manajemenmahasiswa.pelaksanaan.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@if($pelaksanaanList->count() > 0)
    <div class="row g-4">
        @foreach($pelaksanaanList as $item)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $item->id) }}" class="pelaksanaan-card">
                    <div class="card-banner">
                        @if($item->banner)
                            <img src="{{ $item->banner_url }}" alt="{{ $item->judul }}">
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary-border)" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @if($item->bidangs && $item->bidangs->count() > 0)
                                @foreach($item->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @else
                                <span class="badge-bidang" style="background:var(--c-primary-subtle);color:var(--c-primary);">Prodi</span>
                            @endif
                        </div>
                        <div class="card-title">{{ $item->judul }}</div>

                        <div class="card-meta">
                            <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> {{ $item->tanggal_mulai ? $item->tanggal_mulai->translatedFormat('d M Y') : 'Belum ditentukan' }}</span>
                            @if($item->jam_mulai)
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $item->jam_mulai_formatted }}{{ $item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : '' }} WIB</span>
                            @endif
                            @if($item->lokasi)
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> {{ $item->lokasi }}</span>
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
        'paginator'      => $pelaksanaanList,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#127939;</div>
        <h5>Belum ada proker yang siap dilaksanakan</h5>
        <p>Proker yang sudah disetujui admin akan muncul di sini</p>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="mk-kegiatan-btn mk-kegiatan-btn--primary mk-kegiatan-btn--form mt-2">
            Lihat Rencana Proker
        </a>
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
