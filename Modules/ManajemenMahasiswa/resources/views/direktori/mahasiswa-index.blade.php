<x-dynamic-component :component="$layout">

@include('manajemenmahasiswa::direktori.partials.palette')
@include('manajemenmahasiswa::partials.card-frame')
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
        font-size: 12.5px;
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

    /* ── Tombol Reset ── */
    /* Sama dengan tombol outline "Audit Logs"/"Users" di dashboard global */
    .btn-reset {
        height: 34px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s;
        text-decoration: none !important;
        border: 1px solid var(--c-border);
        background: #ffffff;
        color: var(--c-fg-sec);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-reset:hover { background: var(--c-bg); border-color: var(--c-border-strong); color: var(--c-fg); }

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
    /* Warna tiap status ada di partials/palette */
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    /* ── Kolom Aksi: tombol "..." + dropdown menu, disamakan dengan kolom Action
       User Management global ── */
    .action-menu-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid var(--c-border);
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--c-fg-muted);
        transition: all 0.15s;
        margin: 0 auto;
    }
    .action-menu-btn:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
    }
    .action-menu-panel {
        position: absolute;
        right: 0;
        top: calc(100% + 5px);
        background: #ffffff;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        min-width: 150px;
        z-index: 40;
        overflow: hidden;
        padding: 5px;
    }
    .action-menu-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border: none;
        border-radius: 6px;
        background: none;
        font-size: 11px;
        font-weight: 500;
        color: var(--c-fg-sec);
        text-decoration: none !important;
        cursor: pointer;
        font-family: inherit;
        text-align: left;
        transition: background 0.12s;
    }
    .action-menu-item:hover {
        background: var(--c-bg);
        color: var(--c-fg-sec);
    }

    /* ── Stat Cards ── */
    /* Grid yang menyesuaikan jumlah kartu (7 buah) — menghindari baris terakhir
       yang hanya berisi 2 kartu nyangkut di kiri seperti pada grid 5 kolom. */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
        gap: 12px;
    }
    /* Jarak antarkartu diatur .mm-frame-body (gap 16px), bukan margin masing-masing */
    .mm-frame-body > .alert { margin: 0; }
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

{{-- Band judul: garis pemisah selebar kotak, sama dengan header User Management /
     Role & Permission SITKOM. Kartu-kartu di bawahnya ada di .mm-frame-body. --}}
<div class="mm-frame-header">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-fg);letter-spacing:-.02em;line-height:1.2;margin:0;">Direktori Mahasiswa</h1>
    <p style="font-size:12px;color:var(--c-fg-muted);margin:3px 0 0;">Daftar seluruh mahasiswa yang terdaftar di program studi</p>
</div>

<div class="mm-frame-body">

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-success-subtle); color: var(--c-success); font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    // Pesan gangguan bisa datang dari dua arah: dari halaman ini sendiri ($error)
    // atau dari halaman detail yang melempar balik ke sini (session('error')).
    $pesanGangguan = session('error') ?: ($error ?? null);
@endphp
@if($pesanGangguan)
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2" role="alert"
         style="border-radius: 10px; border: none; background: var(--c-error-subtle); color: var(--c-error-200); font-weight: 500; font-size: 14px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span>{{ $pesanGangguan }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <!-- Stat Cards -->
<div class="stat-grid">
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
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->sum() }}</div>
                <div class="stat-label" style="font-size: 11px;">Total Mahasiswa</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('aktif', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">Aktif</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('cuti', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">Cuti</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('drop_out', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">DO</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 16 12 20 8 16"></polyline>
                    <line x1="12" y1="12" x2="12" y2="20"></line>
                    <polyline points="8 8 12 4 16 8"></polyline>
                    <line x1="12" y1="4" x2="12" y2="12"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('pindah_studi', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">Pindah Studi</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-outline">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="17" y1="11" x2="23" y2="11"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('wafat', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">Wafat</div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon tone-warning">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value" style="font-size: 18px;">{{ $statusCounts->get('mangkir', 0) }}</div>
                <div class="stat-label" style="font-size: 11px;">Mangkir</div>
            </div>
        </div>
    </div>
</div>

<!-- Mahasiswa Table -->
<div class="table-card filter-pop-host">
    {{-- Table Toolbar: judul + pencarian + filter menyatu, mengikuti struktur
         kartu tabel User Management global --}}
    <div class="table-toolbar">
        <h2 class="table-toolbar-title">Tabel Mahasiswa</h2>
        <form method="GET" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" id="filterForm" class="table-toolbar-form">
            {{-- Pertahankan pilihan "Per page" saat pencarian/filter dikirim ulang --}}
            @if(request()->filled('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif

            <div class="search-wrapper">
                <span class="search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" name="search" class="search-input"
                       placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
            </div>

            {{-- Filter Angkatan & Status dikumpulkan dalam satu panel, sama dengan panel
                 "Advanced Filters" tabel Audit Log global. Backdrop (bukan @click.outside)
                 dipakai supaya klik pada <select> di dalam panel tidak menutupnya. --}}
            @php
                $filterAngkatanAktif = request()->filled('angkatan') && request('angkatan') !== 'semua';
                $filterStatusAktif   = request()->filled('status') && request('status') !== 'semua';
            @endphp
            <div class="filter-pop" x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">
                <button type="button" class="filter-pop-btn"
                        @click="filterOpen = !filterOpen"
                        :class="{ 'is-open': filterOpen }">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink: 0;">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span style="line-height: 1;">Filter</span>
                    @if($filterAngkatanAktif || $filterStatusAktif)
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
                            <select name="angkatan" id="filterAngkatan" class="filter-pop-select">
                                <option value="semua">Semua Angkatan</option>
                                @foreach($angkatanList as $ank)
                                    <option value="{{ $ank }}" {{ request('angkatan') == $ank ? 'selected' : '' }}>
                                        Angkatan {{ $ank }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="filter-pop-label" for="filterStatus">Status</label>
                            <select name="status" id="filterStatus" class="filter-pop-select">
                                <option value="semua">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="drop_out" {{ request('status') == 'drop_out' ? 'selected' : '' }}>DO</option>
                                <option value="pindah_studi" {{ request('status') == 'pindah_studi' ? 'selected' : '' }}>Pindah Studi</option>
                                <option value="wafat" {{ request('status') == 'wafat' ? 'selected' : '' }}>Wafat</option>
                                <option value="mangkir" {{ request('status') == 'mangkir' ? 'selected' : '' }}>Mangkir</option>
                            </select>
                        </div>

                        <div class="filter-pop-actions">
                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                            @if($isFiltered ?? false)
                                <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" class="filter-pop-reset">Reset</a>
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
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Angkatan</th>
                    <th>Status</th>
                    <th style="width: 72px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $index => $mhs)
                    <tr>
                        <td style="color: var(--c-fg-muted); font-weight: 500;">{{ $mahasiswa->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                {{-- Titik indikator online dihapus: nilainya hanya berubah saat
                                     login & logout eksplisit, sehingga user yang menutup browser
                                     tetap terlihat "online" selamanya. Menyusul penghapusan kolom
                                     "Aktivitas"/"Terakhir Aktif" pada revisi sebelumnya. --}}
                                <div class="mhs-avatar">
                                    @if($mhs->user && $mhs->user->avatar_url)
                                        <img src="{{ $mhs->user->avatar_url }}" alt="{{ $mhs->nama }}">
                                    @else
                                        {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: var(--c-fg);">{{ $mhs->nama }}</div>
                                    @if($mhs->user && $mhs->user->email)
                                        <div style="font-size: 11px; color: var(--c-fg-muted); margin-top: 1px;">{{ $mhs->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600; font-family: monospace; color: var(--c-primary);">{{ $mhs->nim }}</td>
                        <td><span style="font-weight: 600;">{{ $mhs->angkatan }}</span></td>
                        <td>
                            <span class="status-badge {{ $mhs->status }}">
                                @switch($mhs->status)
                                    @case('aktif') Aktif @break
                                    @case('cuti') Cuti @break
                                    @case('drop_out') DO @break
                                    @case('pindah_studi') Pindah Studi @break
                                    @case('wafat') Wafat @break
                                    @case('mangkir') Mangkir @break
                                    @default {{ ucfirst($mhs->status) }}
                                @endswitch
                            </span>
                        </td>
                        {{-- Aksi: tombol "..." + dropdown, mengikuti pola kolom Action
                             User Management global (Alpine.js sudah dimuat di layout admin/dosen/mahasiswa) --}}
                        <td style="text-align: center;">
                            <div style="position: relative; display: inline-block;" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.outside="open = false" class="action-menu-btn">
                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                </button>
                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="action-menu-panel" style="display: none;">
                                    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}" class="action-menu-item">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        Detail
                                    </a>
                                    @if($isAdmin)
                                        <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.edit', $mhs->id) }}" class="action-menu-item">
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
                        <td colspan="6" style="padding: 60px 24px; text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: #E5E7EB;">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <line x1="17" y1="11" x2="23" y2="11"></line>
                                </svg>
                                @if($pesanGangguan)
                                    {{-- Tabel kosong karena gangguan, BUKAN karena datanya tidak ada.
                                         Keterangan lengkapnya sudah tampil sebagai peringatan merah di atas. --}}
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted);">Data belum bisa ditampilkan</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder);">
                                        Silakan baca keterangan di bagian atas halaman. Data mahasiswa tidak terhapus.
                                    </p>
                                @elseif($isFiltered ?? false)
                                    @php
                                        $kriteria = [];
                                        if (request('search')) {
                                            $kriteria[] = 'pencarian "' . request('search') . '"';
                                        }
                                        if (request('angkatan') && request('angkatan') !== 'semua') {
                                            $kriteria[] = 'angkatan ' . request('angkatan');
                                        }
                                        if (request('status') && request('status') !== 'semua') {
                                            $kriteria[] = 'status ' . str_replace('_', ' ', request('status'));
                                        }
                                    @endphp
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted);">Tidak ada mahasiswa yang cocok</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder);">
                                        Tidak ditemukan hasil untuk {{ implode(' + ', $kriteria) }}.<br>
                                        Coba kata kunci lain, atau kosongkan filternya.
                                    </p>
                                    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" class="btn-reset mt-1">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        Reset pencarian &amp; filter
                                    </a>
                                @else
                                    <p style="font-size: 13px; font-weight: 600; color: var(--c-fg-muted);">Belum ada data mahasiswa</p>
                                    <p style="font-size: 12px; color: var(--c-fg-placeholder);">Data mahasiswa yang terdaftar akan muncul di sini</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer: Per page + Showing X to Y of Z results + nomor halaman (partial bersama) --}}
    @include('manajemenmahasiswa::partials.table-footer', ['paginator' => $mahasiswa])
</div>

</div>{{-- end mm-frame-body --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
