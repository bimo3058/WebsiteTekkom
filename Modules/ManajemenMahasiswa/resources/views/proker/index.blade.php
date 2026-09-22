<x-manajemenmahasiswa::layouts.mahasiswa>
@include('manajemenmahasiswa::partials.card-frame')

@include('manajemenmahasiswa::partials.kegiatan-theme')
@include('manajemenmahasiswa::partials.filter-popover')

<style>
    /* ── Status Badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 50px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
    }
    .status-draft     { background: var(--c-surface-muted); color: var(--c-fg-muted); }
    .status-diajukan  { background: var(--c-warning-subtle); color: var(--c-warning); }
    .status-disetujui { background: var(--c-primary-subtle); color: var(--c-primary); }
    .status-ditolak   { background: var(--c-error-subtle); color: var(--c-error); }



    /* ── Filter Bar ── */
    .filter-section { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; align-items: center; }
    .filter-chip {
        padding: 7px 16px; border-radius: 8px; border: 1px solid var(--c-border);
        background: var(--c-surface); color: var(--c-fg-muted); font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s; text-decoration: none !important;
        display: inline-block;
    }
    .filter-chip:hover { border-color: var(--c-primary); color: var(--c-primary); background: var(--c-primary-subtle); }
    .filter-chip.active { background: var(--c-primary); color: var(--c-surface) !important; border-color: var(--c-primary); }

    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--c-fg-muted); }
    .search-input {
        background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 8px;
        height: 38px; padding-left: 36px; font-size: 13px; width: 100%; color: var(--c-fg-sec);
    }
    .search-input:focus { background: var(--c-surface); border-color: var(--c-primary); box-shadow: 0 0 0 3px var(--c-primary-subtle); outline: none; }

    /* ── Proker Cards ── */
    .proker-card {
        background: var(--c-surface); border-radius: 12px; overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.2s ease; text-decoration: none !important;
        display: flex; flex-direction: column; border: 1px solid var(--c-border);
    }
    .proker-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px var(--c-primary-shadow);
        border-color: var(--c-primary-border);
    }
    .proker-card-image {
        width: 100%; aspect-ratio: 16/9;
        background: linear-gradient(135deg, var(--c-primary-subtle) 0%, var(--c-primary-shadow) 100%);
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .proker-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .proker-card-body { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; }
    .badge-bidang {
        font-size: 11px; font-weight: 700; padding: 3px 10px;
        border-radius: 50px; background: var(--c-primary-subtle); color: var(--c-primary);
    }
    .proker-card-title {
        font-weight: 700; font-size: 15px; color: var(--c-fg);
        margin: 8px 0 6px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    .proker-card-meta {
        display: flex; flex-wrap: wrap; gap: 10px;
        font-size: 12px; color: var(--c-fg-muted); font-weight: 500;
        padding-top: 10px; border-top: 1px solid var(--c-surface-muted); margin-top: auto;
    }
    .proker-card-meta span { display: inline-flex; align-items: center; gap: 4px; }
    .empty-state { text-align: center; padding: 50px 20px; color: var(--c-fg-muted); }
    .empty-state h5 { color: var(--c-fg-muted); font-weight: 600; margin-bottom: 4px; }
</style>

{{-- Band judul selebar kotak (pola sama dengan Direktori Mahasiswa / SITKOM) --}}
<div class="mm-frame-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
    <div>
        <h1 style="font-size:22px;font-weight:700;color:var(--c-fg);letter-spacing:-.02em;line-height:1.2;margin:0;">Rencana Program Kerja</h1>
        <p style="font-size:12px;color:var(--c-fg-muted);margin:3px 0 0;">Daftar rencana proker dari 8 bidang himpunan &amp; prodi</p>
    </div>
    @if($canManage)
        <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="mk-kegiatan-btn mk-kegiatan-btn--primary mk-kegiatan-btn--compact d-flex align-items-center gap-2">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Proker
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:var(--c-success-subtle);color:var(--c-success);font-weight:500;font-size:14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:var(--c-error-subtle);color:var(--c-error);font-weight:500;font-size:14px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- Filter --}}
<form method="GET" action="{{ route('manajemenmahasiswa.proker.index') }}" id="filterForm">
    {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
    @if(request()->filled('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
    @endif
    <div class="mk-kegiatan-filter-row">
        <div class="mk-kegiatan-search w-100">
            <span class="mk-kegiatan-search__icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="mk-kegiatan-search__input"
                   placeholder="Cari judul atau deskripsi rencana proker..." value="{{ request('search') }}">
        </div>
        {{-- Bidang memakai panel yang sama dengan panel "Advanced Filters" tabel Audit Log
             global (partials/filter-popover). Dulu berupa deretan chip di bawah pencarian. --}}
        @php
            $filterBidangAktif = request()->filled('bidang') && request('bidang') !== 'semua';
            $adaFilterApaPun   = $filterBidangAktif || request()->filled('search');
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
                    @if($filterBidangAktif)
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

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($adaFilterApaPun)
                                <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@if($prokerList->count() > 0)
    <div class="row g-4">
        @foreach($prokerList as $proker)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <a href="{{ route('manajemenmahasiswa.proker.show', $proker->id) }}" class="proker-card">
                    <div class="proker-card-image">
                        @if($proker->banner)
                            <img src="{{ $proker->banner_url }}" alt="{{ $proker->judul }}">
                        @else
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary-border)" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        @endif
                    </div>
                    <div class="proker-card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="status-badge status-{{ $proker->status }}">{{ $proker->status_label }}</span>
                            @php
                                $hasProdi = $proker->kategoris && $proker->kategoris->contains(fn($k) => stripos($k->nama_kategori, 'Prodi') !== false);
                                $hasBidang = $proker->bidangs && $proker->bidangs->count() > 0;
                            @endphp
                            @if($hasProdi || !$hasBidang)
                                <span class="badge-bidang" style="background:var(--c-primary-subtle);color:var(--c-primary);">Prodi</span>
                            @endif
                            @if($hasBidang)
                                @foreach($proker->bidangs as $b)
                                    <span class="badge-bidang">{{ $b->nama_bidang }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="proker-card-title">{{ $proker->judul }}</div>
                        @if($proker->deskripsi)
                            <div style="font-size:12px;color:var(--c-fg-muted);line-height:1.55;margin-bottom:8px;
                                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $proker->deskripsi }}
                            </div>
                        @endif
                        <div class="proker-card-meta">
                            @if($proker->tanggal_mulai)
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                    {{ $proker->tanggal_mulai->translatedFormat('d M Y') }}
                                </span>
                            @endif
                            @if($proker->tahun)<span>{{ $proker->tahun }}</span>@endif
                            @if($proker->ketuaPelaksana && $proker->ketuaPelaksana->user)
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ Str::limit($proker->ketuaPelaksana->user->name, 18) }}
                                </span>
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
        'paginator'      => $prokerList,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
        'standalone'     => true,
    ])
@else
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#128203;</div>
        <h5>Belum ada rencana proker</h5>
        <p>Rencana program kerja yang dibuat akan muncul di sini</p>
        @if($canManage)
            <a href="{{ route('manajemenmahasiswa.proker.create') }}" class="mk-kegiatan-btn mk-kegiatan-btn--primary mk-kegiatan-btn--form mt-2">+ Buat Proker Pertama</a>
        @endif
    </div>
@endif

</x-manajemenmahasiswa::layouts.mahasiswa>
