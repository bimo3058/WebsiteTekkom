<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')

<style>
    /* ── Filter Bar ── */
    .filter-section {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }
    .filter-select-custom {
        padding: 0 14px;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: #ffffff;
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

    /* ── Search Bar ── */
    .search-wrapper {
        position: relative;
        flex-grow: 1;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--c-fg-placeholder);
    }
    .search-input {
        background-color: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        height: 38px;
        padding-left: 36px;
        font-size: 13px;
        font-weight: 500;
        width: 100%;
        color: var(--c-fg);
    }
    .search-input:focus {
        background-color: #ffffff;
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px var(--c-primary-subtle);
        outline: none;
    }

    /* ── Table ── */
    /* Latar head, hover baris, dan garis antarbaris mengikuti tabel User Management global */
    .mhs-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .mhs-table thead th {
        background: #FAFAFA;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 700;
        color: var(--c-fg-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--c-border);
        white-space: nowrap;
    }
    .mhs-table tbody tr {
        transition: background 0.15s;
    }
    .mhs-table tbody tr:hover {
        background: #FAFAFA;
    }
    .mhs-table tbody td {
        padding: 14px 16px;
        font-size: 14px;
        color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    /* Avatar inisial netral, sama dengan komponen user-avatar global */
    .mhs-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--c-grey-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--c-fg-muted);
        font-size: 14px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid var(--c-border);
    }
    .mhs-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Warna tiap status karir ada di partials/palette */
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
        white-space: nowrap;
    }

    .btn-action {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-action-view {
        background: var(--c-primary-subtle);
        color: var(--c-primary);
    }
    .btn-action-view:hover {
        background: rgba(11, 38, 110, 0.12);
        color: var(--c-primary-hover);
    }
    /* Padding dikurangi 1px untuk menampung border, supaya tingginya sama dengan tombol Detail */
    .btn-action-edit {
        background: #ffffff;
        border: 1px solid var(--c-border);
        padding: 5px 13px;
        color: var(--c-fg-sec);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-action-edit:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
        color: var(--c-fg);
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--c-fg-muted);
    }
    .empty-state h5 {
        color: var(--c-fg-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }

    /* ── Stat Cards ── */
    /* Border, bayangan, dan efek hover sama dengan kartu statistik dashboard global */
    .stat-card {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-card);
        transition: all 0.2s;
    }
    .stat-card:hover {
        border-color: var(--c-primary-border);
        box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--c-fg);
        line-height: 1;
    }
    .stat-label {
        font-size: 12px;
        color: var(--c-fg-muted);
        font-weight: 500;
    }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:var(--c-fg);letter-spacing:-.02em;">Direktori Alumni</h3>
        <p class="mb-0" style="font-size:.82rem;color:var(--c-fg-muted);font-weight:500;">Daftar dan profil karir seluruh lulusan program studi</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4 row-cols-2 row-cols-md-4">
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $totalAlumni }}</div>
                <div class="stat-label" style="font-size: 11px;">Total Alumni</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $bekerja + $wirausaha }}</div>
                <div class="stat-label" style="font-size: 11px;">Bekerja / Wirausaha</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $studiLanjut }}</div>
                <div class="stat-label" style="font-size: 11px;">Studi Lanjut</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-neutral">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $belumTerdata }}</div>
                <div class="stat-label" style="font-size: 11px;">Belum Terdata</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" name="search" class="form-control search-input w-100"
                   placeholder="Cari nama, NIM, atau instansi alumni..." value="{{ request('search') }}">
        </div>
        <div class="d-flex gap-3">
            <select name="tahun_lulus" class="form-select border-1 filter-select-custom"
                    style="min-width: 160px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Tahun Lulus</option>
                @foreach($tahunLulusList as $tl)
                    <option value="{{ $tl }}" {{ request('tahun_lulus') == $tl ? 'selected' : '' }}>
                        Lulus {{ $tl }}
                    </option>
                @endforeach
            </select>
            <select name="status_karir" class="form-select border-1 filter-select-custom"
                    style="min-width: 140px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Status</option>
                @foreach($statusKarirOptions as $val => $label)
                    <option value="{{ $val }}" {{ request('status_karir') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="bidang_industri" class="form-select border-1 filter-select-custom"
                    style="min-width: 150px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Industri</option>
                @foreach($bidangIndustriOptions as $val => $label)
                    <option value="{{ $val }}" {{ request('bidang_industri') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

<!-- Alumni Table -->
@if($alumni->count() > 0)
    <div style="overflow-x: auto; border-radius: 12px; border: 1px solid var(--c-border);">
        <table class="mhs-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Alumni</th>
                    <th>NIM</th>
                    <th>Tahun Lulus</th>
                    <th>Karir / Instansi</th>
                    <th>Status</th>
                    <th style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumni as $index => $alm)
                    <tr>
                        <td style="color: var(--c-fg-muted); font-weight: 500;">{{ $alumni->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="mhs-avatar">
                                    @if($alm->user && $alm->user->avatar_url)
                                        <img src="{{ $alm->user->avatar_url }}" alt="{{ $alm->user->name }}">
                                    @else
                                        {{ strtoupper(substr($alm->user->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--c-fg);">{{ $alm->user->name ?? 'Tanpa Nama' }}</div>
                                    @if($alm->user && $alm->user->email)
                                        <div style="font-size: 12px; color: var(--c-fg-muted);">{{ $alm->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600; font-family: monospace; color: var(--c-primary);">{{ $alm->nim }}</td>
                        <td>
                            <span style="font-weight: 600;">{{ $alm->tahun_lulus }}</span>
                            <div style="font-size: 11px; color: var(--c-fg-muted);">Angk. {{ $alm->angkatan }}</div>
                        </td>
                        <td>
                            @if($alm->perusahaan)
                                <div style="font-weight: 600; color: var(--c-fg);">{{ Str::limit($alm->perusahaan, 28) }}</div>
                                @if($alm->jabatan)
                                    <div style="font-size: 12px; color: var(--c-fg-muted);">{{ Str::limit($alm->jabatan, 28) }}</div>
                                @endif
                            @else
                                <span style="color: var(--c-fg-placeholder); font-style: italic;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $alm->status_karir ?? 'belum_terdata' }}">
                                {{ $alm->status_karir_label }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alm->id) }}"
                                   class="btn-action btn-action-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                                @if($isAdmin)
                                    <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alm->id) }}"
                                       class="btn-action btn-action-edit">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($alumni->hasPages())
        <div class="mt-4 d-flex flex-column align-items-center gap-2">
            <div class="d-flex align-items-center gap-1">

                {{-- Prev --}}
                @if($alumni->onFirstPage())
                    <span class="page-btn page-btn-nav disabled">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a href="{{ $alumni->withQueryString()->previousPageUrl() }}" class="page-btn page-btn-nav">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($alumni->withQueryString()->links()->offsetGet('elements') as $element)
                    @if(is_string($element))
                        <span class="page-btn page-btn-dots">…</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $alumni->currentPage())
                                <span class="page-btn page-btn-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if($alumni->hasMorePages())
                    <a href="{{ $alumni->withQueryString()->nextPageUrl() }}" class="page-btn page-btn-nav">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @else
                    <span class="page-btn page-btn-nav disabled">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                @endif

            </div>
            {{-- Info teks --}}
            <div style="font-size: 12px; color: var(--c-fg-muted); font-weight: 500;">
                Menampilkan {{ $alumni->firstItem() }}–{{ $alumni->lastItem() }} dari {{ $alumni->total() }} alumni
            </div>
        </div>

        <style>
            .page-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 34px;
                height: 34px;
                padding: 0 10px;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 600;
                color: var(--c-fg-sec);
                background: #ffffff;
                border: 1px solid var(--c-border);
                text-decoration: none !important;
                transition: all 0.15s;
                cursor: pointer;
            }
            .page-btn:hover:not(.disabled):not(.page-btn-active) {
                background: var(--c-bg);
                border-color: var(--c-primary);
                color: var(--c-primary);
            }
            .page-btn-active {
                background: var(--c-primary);
                border-color: var(--c-primary);
                color: #ffffff !important;
                cursor: default;
            }
            .page-btn-nav {
                color: var(--c-fg-muted);
            }
            .page-btn-nav.disabled {
                opacity: 0.35;
                cursor: not-allowed;
            }
            .page-btn-dots {
                border: none;
                background: transparent;
                color: var(--c-fg-muted);
                cursor: default;
                min-width: 24px;
                padding: 0;
            }
        </style>
    @endif
@else
    <div class="empty-state">
        <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="17" y1="11" x2="23" y2="11"></line>
            </svg>
        </div>
        <h5>Belum ada data alumni</h5>
        <p style="font-size: 14px; color: var(--c-fg-muted);">Data alumni yang sesuai filter tidak ditemukan</p>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
