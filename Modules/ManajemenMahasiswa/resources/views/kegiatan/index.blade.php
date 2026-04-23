<x-manajemenmahasiswa::layouts.mahasiswa>

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
        padding: 7px 18px;
        border-radius: 20px;
        border: 1.5px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none !important;
        display: inline-block;
    }
    .filter-chip:hover {
        border-color: #818cf8;
        color: #4f46e5;
        background: #eef2ff;
    }
    .filter-chip.active {
        background: #4f46e5;
        color: #ffffff !important;
        border-color: #4f46e5;
    }
    .filter-select-custom {
        padding: 7px 16px;
        border-radius: 20px;
        border: 1.5px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: all 0.2s;
        height: 38px;
    }
    .filter-select-custom:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
        color: #9ca3af;
        font-size: 14px;
    }
    .search-input {
        background-color: #f3f4f6;
        border: none;
        border-radius: 8px;
        height: 42px;
        padding-left: 36px;
        font-size: 13px;
        font-weight: 500;
        width: 100%;
    }
    .search-input:focus {
        background-color: #ffffff;
        box-shadow: 0 0 0 2px #e0e7ff;
        outline: none;
    }

    /* ── Kegiatan Cards ── */
    .kegiatan-card {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        border: 1px solid #f3f4f6;
    }
    .kegiatan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px rgba(79, 70, 229, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.06);
        border-color: #c7d2fe;
    }
    .kegiatan-card-image {
        width: 100%;
        height: 170px;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
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
        background: #eef2ff;
        color: #4f46e5;
    }
    .badge-status {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .badge-status.akan_datang { background: #fef3c7; color: #d97706; }
    .badge-status.berlangsung { background: #dbeafe; color: #2563eb; }
    .badge-status.selesai { background: #dcfce7; color: #16a34a; }

    .kegiatan-card-title {
        font-weight: 700;
        font-size: 15px;
        color: #1f2937;
        margin-bottom: 6px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .kegiatan-card-desc {
        font-size: 13px;
        color: #6b7280;
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
        color: #9ca3af;
        font-weight: 500;
        padding-top: 10px;
        border-top: 1px solid #f3f4f6;
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
        color: #9ca3af;
    }
    .empty-state .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    .empty-state h5 {
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .empty-state p {
        font-size: 14px;
        color: #9ca3af;
    }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Manajemen Kegiatan</h3>
        <p class="text-dark fw-bold mb-0" style="font-size: 14px;">Daftar kegiatan terbaru dari berbagai bidang kepengurusan</p>
    </div>
    @if($isAdmin)
        <a href="{{ route('manajemenmahasiswa.kegiatan.create') }}"
           class="btn d-flex align-items-center gap-2"
           style="background: #4f46e5; color: #fff; font-weight: 600; font-size: 14px; padding: 10px 20px; border-radius: 10px; transition: all 0.2s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Kegiatan
        </a>
    @endif
</div>

<!-- Search & Filter Area (matching forum layout) -->
<form method="GET" action="{{ route('manajemenmahasiswa.kegiatan.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" name="search" class="form-control search-input w-100"
                   placeholder="Cari kegiatan..." value="{{ request('search') }}">
        </div>

        <div class="d-flex gap-3">
            <select name="tahun" class="form-select border-1 filter-select-custom"
                    style="min-width: 160px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Tahun</option>
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Bidang Filter Chips -->
    <div class="filter-section">
        <a href="{{ route('manajemenmahasiswa.kegiatan.index', request()->except(['bidang', 'page'])) }}"
           class="filter-chip {{ !request('bidang') || request('bidang') == 'semua' ? 'active' : '' }}">
            Semua
        </a>
        <a href="{{ route('manajemenmahasiswa.kegiatan.index', array_merge(request()->except('page'), ['bidang' => 'prodi'])) }}"
           class="filter-chip {{ request('bidang') == 'prodi' ? 'active' : '' }}"
           style="{{ request('bidang') == 'prodi' ? 'background: #7c3aed; border-color: #7c3aed;' : '' }}">
            Prodi
        </a>
        @foreach($bidangList as $bidang)
            <a href="{{ route('manajemenmahasiswa.kegiatan.index', array_merge(request()->except('page'), ['bidang' => $bidang->id])) }}"
               class="filter-chip {{ request('bidang') == $bidang->id ? 'active' : '' }}">
                {{ $bidang->nama_bidang }}
            </a>
        @endforeach
    </div>
</form>

<!-- Kegiatan Cards Grid -->
@if($kegiatan->count() > 0)
    <div class="row g-4">
        @foreach($kegiatan as $item)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('manajemenmahasiswa.kegiatan.show', $item->id) }}" class="kegiatan-card">
                    <!-- Image -->
                    <div class="kegiatan-card-image">
                        @if($item->banner)
                            <img src="{{ $item->banner_url }}" alt="{{ $item->judul }}">
                        @else
                            <span class="placeholder-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg></span>
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
                                <span class="badge-bidang" style="background: #f3e8ff; color: #7c3aed;">Prodi</span>
                            @endif
                            @if($item->kategoris && $item->kategoris->count() > 0)
                                @foreach($item->kategoris as $kat)
                                    <span class="badge-bidang" style="background: #fef3c7; color: #92400e;">{{ $kat->nama_kategori }}</span>
                                @endforeach
                            @endif
                            @if($item->status)
                                <span class="badge-status {{ $item->status }}">{{ $item->status_label }}</span>
                            @endif
                        </div>

                        <!-- Title -->
                        <div class="kegiatan-card-title">{{ $item->judul }}</div>

                        <!-- Description -->
                        <div class="kegiatan-card-desc">
                            {{ Str::limit(strip_tags($item->deskripsi), 100) }}
                        </div>

                        <!-- Meta -->
                        <div class="kegiatan-card-meta">
                            <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg> {{ $item->tanggal_mulai->translatedFormat('d M Y') }}</span>
                            @if($item->lokasi)
                                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> {{ Str::limit($item->lokasi, 20) }}</span>
                            @endif
                            @if($item->ketuaPelaksana && $item->ketuaPelaksana->user)
                                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> {{ Str::limit($item->ketuaPelaksana->user->name, 18) }}</span>
                            @elseif($item->penanggung_jawab)
                                <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> {{ Str::limit($item->penanggung_jawab, 18) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($kegiatan->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $kegiatan->withQueryString()->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8V21H3V8"></path><path d="M23 3H1v5h22V3z"></path><path d="M10 12h4"></path></svg></div>
        <h5>Belum ada kegiatan</h5>
        <p>Kegiatan yang tersedia akan muncul di sini</p>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-manajemenmahasiswa::layouts.mahasiswa>

