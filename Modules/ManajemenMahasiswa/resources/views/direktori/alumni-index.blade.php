<x-dynamic-component :component="$layout">

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
        border: 1px solid #DFE1E7;
        background: #ffffff;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: all 0.15s;
        height: 38px;
    }
    .filter-select-custom:focus {
        border-color: #0B266E;
        box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
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
        color: #666D80;
    }
    .search-input {
        background-color: #ffffff;
        border: 1px solid #DFE1E7;
        border-radius: 8px;
        height: 38px;
        padding-left: 36px;
        font-size: 13px;
        font-weight: 500;
        width: 100%;
        color: #374151;
    }
    .search-input:focus {
        background-color: #ffffff;
        border-color: #0B266E;
        box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
        outline: none;
    }

    /* ── Table ── */
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
        color: #666D80;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #DFE1E7;
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
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .mhs-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #eef2ff, #dbe4f5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #0B266E;
        font-size: 14px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid #eef2ff;
    }
    .mhs-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
        white-space: nowrap;
    }
    .status-badge.bekerja { background: #ECFDF5; color: #059669; }
    .status-badge.wirausaha { background: #FFFBEB; color: #92400e; }
    .status-badge.studi_lanjut { background: #dbeafe; color: #1e40af; }
    .status-badge.belum_bekerja { background: #fef2f2; color: #991b1b; }
    .status-badge.belum_terdata { background: #f3f4f6; color: #353849; }

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
        background: #eef2ff;
        color: #0B266E;
    }
    .btn-action-view:hover {
        background: #eef2ff;
        color: #091958;
    }
    .btn-action-edit {
        background: #FFFBEB;
        color: #92400e;
    }
    .btn-action-edit:hover {
        background: #fde68a;
        color: #78350f;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666D80;
    }
    .empty-state h5 {
        color: #666D80;
        font-weight: 600;
        margin-bottom: 4px;
    }

    /* ── Stat Cards ── */
    .stat-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
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
        color: #0D0D12;
        line-height: 1;
    }
    .stat-label {
        font-size: 12px;
        color: #666D80;
        font-weight: 500;
    }
</style>

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Direktori Alumni</h3>
        <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">Daftar dan profil karir seluruh lulusan program studi</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4 row-cols-2 row-cols-md-4">
    <div class="col">
        <div class="stat-card p-3">
            <div class="stat-icon" style="background: #eef2ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <div class="stat-icon" style="background: #ECFDF5;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <div class="stat-icon" style="background: #dbeafe;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <div class="stat-icon" style="background: #FFFBEB;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <select name="angkatan" class="form-select border-1 filter-select-custom"
                    style="min-width: 160px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Angkatan</option>
                @foreach($angkatanList as $ank)
                    <option value="{{ $ank }}" {{ request('angkatan') == $ank ? 'selected' : '' }}>
                        Angkatan {{ $ank }}
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
    <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #f3f4f6;">
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
                        <td style="color: #666D80; font-weight: 500;">{{ $alumni->firstItem() + $index }}</td>
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
                                    <div style="font-weight: 600; color: #0D0D12;">{{ $alm->user->name ?? 'Tanpa Nama' }}</div>
                                    @if($alm->user && $alm->user->email)
                                        <div style="font-size: 12px; color: #666D80;">{{ $alm->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600; font-family: monospace; color: #0B266E;">{{ $alm->nim }}</td>
                        <td>
                            <span style="font-weight: 600;">{{ $alm->tahun_lulus }}</span>
                            <div style="font-size: 11px; color: #666D80;">Angk. {{ $alm->angkatan }}</div>
                        </td>
                        <td>
                            @if($alm->perusahaan)
                                <div style="font-weight: 600; color: #374151;">{{ Str::limit($alm->perusahaan, 28) }}</div>
                                @if($alm->jabatan)
                                    <div style="font-size: 12px; color: #666D80;">{{ Str::limit($alm->jabatan, 28) }}</div>
                                @endif
                            @else
                                <span style="color: #C1C7CF; font-style: italic;">—</span>
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
            <div style="font-size: 12px; color: #666D80; font-weight: 500;">
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
                color: #374151;
                background: #ffffff;
                border: 1px solid #DFE1E7;
                text-decoration: none !important;
                transition: all 0.15s;
                cursor: pointer;
            }
            .page-btn:hover:not(.disabled):not(.page-btn-active) {
                background: #F6F8FA;
                border-color: #0B266E;
                color: #0B266E;
            }
            .page-btn-active {
                background: #0B266E;
                border-color: #0B266E;
                color: #ffffff !important;
                cursor: default;
            }
            .page-btn-nav {
                color: #666D80;
            }
            .page-btn-nav.disabled {
                opacity: 0.35;
                cursor: not-allowed;
            }
            .page-btn-dots {
                border: none;
                background: transparent;
                color: #666D80;
                cursor: default;
                min-width: 24px;
                padding: 0;
            }
        </style>
    @endif
@else
    <div class="empty-state">
        <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="17" y1="11" x2="23" y2="11"></line>
            </svg>
        </div>
        <h5>Belum ada data alumni</h5>
        <p style="font-size: 14px; color: #666D80;">Data alumni yang sesuai filter tidak ditemukan</p>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
