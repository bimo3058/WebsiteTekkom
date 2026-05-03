<x-dynamic-component :component="$layout">

@push('styles')
<style>
    /* Override main-wrapper for transparent bg like dashboard */
    .main-wrapper {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    /* ── Page Header ── */
    .page-header-alumni {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .page-header-alumni::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .page-header-alumni h3 {
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }
    .page-header-alumni p {
        font-size: 14px;
        color: rgba(255,255,255,0.8);
        margin: 0;
    }

    /* ── Stat Cards ── */
    .stat-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    @media (max-width: 992px) { .stat-strip { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stat-strip { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #e5e7eb;
        transition: box-shadow 0.2s;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1;
    }
    .stat-label {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
        margin-top: 2px;
    }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px 20px;
        margin-bottom: 16px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    .search-input {
        background-color: #f3f4f6;
        border: none;
        border-radius: 8px;
        height: 40px;
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
    .filter-select {
        padding: 8px 14px;
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: all 0.2s;
        height: 40px;
        min-width: 150px;
    }
    .filter-select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* ── Table ── */
    .table-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .alumni-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .alumni-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
    }
    .alumni-table tbody tr {
        transition: background 0.15s;
    }
    .alumni-table tbody tr:hover {
        background: #f8fafc;
    }
    .alumni-table tbody td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }
    .alumni-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4f46e5;
        font-size: 13px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid #e0e7ff;
    }
    .alumni-avatar img {
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
    .status-badge.bekerja { background: #dcfce7; color: #166534; }
    .status-badge.wirausaha { background: #fef3c7; color: #92400e; }
    .status-badge.studi_lanjut { background: #dbeafe; color: #1e40af; }
    .status-badge.belum_bekerja { background: #fef2f2; color: #991b1b; }
    .status-badge.belum_terdata { background: #f3f4f6; color: #4b5563; }

    .btn-action {
        padding: 5px 12px;
        border-radius: 7px;
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
    .btn-action-view { background: #eef2ff; color: #4f46e5; }
    .btn-action-view:hover { background: #e0e7ff; color: #4338ca; }
    .btn-action-edit { background: #fef3c7; color: #92400e; }
    .btn-action-edit:hover { background: #fde68a; color: #78350f; }

    /* ── Empty State ── */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    .empty-state h5 {
        color: #374151;
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 6px;
    }
    .empty-state p {
        color: #9ca3af;
        font-size: 14px;
        margin: 0;
    }

    /* ── Pagination ── */
    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid #f3f4f6;
        display: flex;
        justify-content: center;
    }
</style>
@endpush

<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #dcfce7; color: #166534; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Page Header -->
<div class="page-header-alumni">
    <h3>🎓 Direktori Alumni</h3>
    <p>Daftar dan profil karir seluruh lulusan program studi.</p>
</div>

<!-- Stat Cards -->
<div class="stat-strip">
    <div class="stat-card">
        <div class="stat-icon" style="background: #eef2ff;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $totalAlumni }}</div>
            <div class="stat-label">Total Alumni</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce7;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $bekerja + $wirausaha }}</div>
            <div class="stat-label">Bekerja / Wirausaha</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #dbeafe;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $studiLanjut }}</div>
            <div class="stat-label">Studi Lanjut</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div>
            <div class="stat-value">{{ $belumTerdata }}</div>
            <div class="stat-label">Belum Terdata</div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" id="filterForm">
    <div class="filter-bar">
        <div class="search-wrapper">
            <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari nama, NIM, atau instansi alumni..." value="{{ request('search') }}">
        </div>
        <select name="angkatan" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="semua">Semua Angkatan</option>
            @foreach($angkatanList as $ank)
                <option value="{{ $ank }}" {{ request('angkatan') == $ank ? 'selected' : '' }}>Angkatan {{ $ank }}</option>
            @endforeach
        </select>
        <select name="status_karir" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="semua">Semua Status</option>
            @foreach($statusKarirOptions as $val => $label)
                <option value="{{ $val }}" {{ request('status_karir') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="bidang_industri" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="semua">Semua Industri</option>
            @foreach($bidangIndustriOptions as $val => $label)
                <option value="{{ $val }}" {{ request('bidang_industri') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</form>

<!-- Alumni Table -->
@if($alumni->count() > 0)
    <div class="table-card">
        <table class="alumni-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Alumni</th>
                    <th>NIM</th>
                    <th>Tahun Lulus</th>
                    <th>Karir / Instansi</th>
                    <th>Status</th>
                    <th style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumni as $index => $alm)
                    <tr>
                        <td style="color: #9ca3af; font-weight: 500;">{{ $alumni->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="alumni-avatar">
                                    @if($alm->user && $alm->user->avatar_url)
                                        <img src="{{ $alm->user->avatar_url }}" alt="{{ $alm->user->name }}">
                                    @else
                                        {{ strtoupper(substr($alm->user->name ?? 'A', 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 13.5px;">{{ $alm->user->name ?? 'Tanpa Nama' }}</div>
                                    @if($alm->user && $alm->user->email)
                                        <div style="font-size: 11.5px; color: #9ca3af;">{{ $alm->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600; font-family: monospace; color: #4f46e5; font-size: 13px;">{{ $alm->nim }}</td>
                        <td>
                            <span style="font-weight: 600;">{{ $alm->tahun_lulus }}</span>
                            <div style="font-size: 11px; color: #9ca3af;">Angk. {{ $alm->angkatan }}</div>
                        </td>
                        <td>
                            @if($alm->perusahaan)
                                <div style="font-weight: 600; color: #374151; font-size: 13px;">{{ Str::limit($alm->perusahaan, 28) }}</div>
                                @if($alm->jabatan)
                                    <div style="font-size: 11.5px; color: #6b7280;">{{ Str::limit($alm->jabatan, 28) }}</div>
                                @endif
                            @else
                                <span style="color: #cbd5e1; font-style: italic; font-size: 13px;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $alm->status_karir ?? 'belum_terdata' }}">
                                {{ $alm->status_karir_label }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('manajemenmahasiswa.direktori.alumni.show', $alm->id) }}"
                                   class="btn-action btn-action-view">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                                @if($isAdmin)
                                    <a href="{{ route('manajemenmahasiswa.direktori.alumni.edit', $alm->id) }}"
                                       class="btn-action btn-action-edit">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if($alumni->hasPages())
            <div class="pagination-wrapper">
                {{ $alumni->withQueryString()->links() }}
            </div>
        @endif
    </div>
@else
    <div class="table-card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="17" y1="11" x2="23" y2="11"></line>
                </svg>
            </div>
            <h5>Belum ada data alumni</h5>
            <p>Data alumni yang sesuai filter tidak ditemukan.</p>
        </div>
    </div>
@endif

</x-dynamic-component>
