<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.filter-popover')

<style>
    /* ── Search Bar ── */
    .search-wrapper {
        position: relative;
        width: min(220px, calc(100vw - 200px));
        min-width: 120px;
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
        height: 34px;
        padding-left: 34px;
        font-size: 12px;
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

    /* ── Table Card ── */
    /* Struktur & warna disamakan 1:1 dengan kartu tabel User Management global
       (resources/views/superadmin/users/_table.blade.php) */
    .table-card {
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 14px;
        /* Tanpa overflow:hidden — panel filter harus bisa keluar dari kartu
           (kelas .filter-pop-host di partials/filter-popover). */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid var(--c-border);
        gap: 10px;
        flex-wrap: wrap;
    }
    .table-toolbar-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--c-fg);
        margin: 0;
        flex-shrink: 0;
    }
    .table-toolbar-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin: 0;
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
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 600;
        color: var(--c-fg-muted);
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
        font-size: 13px;
        color: var(--c-fg);
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    /* Avatar inisial netral, ukuran & border disamakan dengan komponen user-avatar
       global ukuran "md" (resources/views/components/ui/user-avatar.blade.php) */
    .mhs-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--c-grey-50);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--c-fg-muted);
        font-size: 12px;
        flex-shrink: 0;
        overflow: hidden;
        border: 1.5px solid var(--c-border);
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

    /* Kolom Aksi memakai tombol .mk-btn--icon + panel .mk-menu milik modul
       (resources/views/partials/button-theme.blade.php). */

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

<!-- Page Header -->
<x-manajemenmahasiswa::ui.page-header bordered
    title="Direktori Alumni"
    subtitle="Daftar dan profil karir seluruh lulusan program studi" />

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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

<!-- Alumni Table -->
<div class="table-card filter-pop-host">
    {{-- Table Toolbar: judul + pencarian + filter menyatu, mengikuti struktur
         kartu tabel User Management global --}}
    <div class="table-toolbar">
        <h2 class="table-toolbar-title">Tabel Alumni</h2>
        <form method="GET" action="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" id="filterForm" class="table-toolbar-form">
            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" name="search" class="search-input"
                       placeholder="Cari nama, NIM, atau instansi..." value="{{ request('search') }}">
            </div>

            {{-- Filter Angkatan, Status, & Industri dikumpulkan dalam satu panel, sama dengan
                 panel "Advanced Filters" tabel Audit Log global. Backdrop (bukan @click.outside)
                 dipakai supaya klik pada <select> di dalam panel tidak menutupnya. --}}
            @php
                $filterDropdownAktif = collect(['angkatan', 'status_karir', 'bidang_industri'])
                    ->contains(fn ($k) => request()->filled($k) && request($k) !== 'semua');
                $filterApaPun = $filterDropdownAktif || request()->filled('search');
            @endphp
            <div class="filter-pop" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($filterDropdownAktif)
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
                            <label class="filter-pop-label" for="filterAngkatan">Angkatan</label>
                            <x-manajemenmahasiswa::ui.select name="angkatan" id="filterAngkatan">
                                <option value="semua">Semua Angkatan</option>
                                @foreach($angkatanList as $ank)
                                    <option value="{{ $ank }}" {{ request('angkatan') == $ank ? 'selected' : '' }}>
                                        Angkatan {{ $ank }}
                                    </option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterStatusKarir">Status</label>
                            <x-manajemenmahasiswa::ui.select name="status_karir" id="filterStatusKarir">
                                <option value="semua">Semua Status</option>
                                @foreach($statusKarirOptions as $val => $label)
                                    <option value="{{ $val }}" {{ request('status_karir') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterIndustri">Industri</label>
                            <x-manajemenmahasiswa::ui.select name="bidang_industri" id="filterIndustri">
                                <option value="semua">Semua Industri</option>
                                @foreach($bidangIndustriOptions as $val => $label)
                                    <option value="{{ $val }}" {{ request('bidang_industri') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </x-manajemenmahasiswa::ui.select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($filterApaPun)
                                <a href="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" class="filter-pop-reset">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="mhs-table">
            <thead>
                <tr>
                    <th style="width: 56px;">No</th>
                    <th>Alumni</th>
                    <th>NIM</th>
                    <th>Tahun Lulus</th>
                    <th>Karir / Instansi</th>
                    <th>Status</th>
                    <th style="width: 72px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumni as $index => $alm)
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
                                    <div style="font-size: 13px; font-weight: 600; color: var(--c-fg);">{{ $alm->user->name ?? 'Tanpa Nama' }}</div>
                                    @if($alm->user && $alm->user->email)
                                        <div style="font-size: 11px; color: var(--c-fg-muted); margin-top: 1px;">{{ $alm->user->email }}</div>
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
                        {{-- Aksi: tombol "..." + dropdown, mengikuti pola kolom Action
                             User Management global (Alpine.js sudah dimuat di layout admin/dosen/mahasiswa) --}}
                        <td style="text-align: center;">
                            <div style="position: relative; display: inline-block;" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.outside="open = false" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm">
                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="mk-menu" style="display: none;">
                                    <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alm->id) }}" class="mk-menu-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Detail
                                    </a>
                                    @if($isAdmin)
                                        <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alm->id) }}" class="mk-menu-item">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 60px 24px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #E5E7EB;">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <line x1="17" y1="11" x2="23" y2="11"></line>
                                </svg>
                                <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted);">Belum ada data alumni</p>
                                <p style="font-size: 12px; color: var(--c-fg-placeholder);">Data alumni yang sesuai filter tidak ditemukan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($alumni->hasPages())
        <div style="padding: 14px 16px; border-top: 1px solid var(--c-border); display: flex; flex-direction: column; align-items: center; gap: 8px;">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
