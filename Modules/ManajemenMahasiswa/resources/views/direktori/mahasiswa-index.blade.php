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
        color: #9ca3af;
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

    /* ── Table ── */
    .mhs-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .mhs-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e5e7eb;
        white-space: nowrap;
    }
    .mhs-table tbody tr {
        transition: background 0.15s;
    }
    .mhs-table tbody tr:hover {
        background: #f8fafc;
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
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4f46e5;
        font-size: 14px;
        flex-shrink: 0;
        overflow: hidden;
        border: 2px solid #e0e7ff;
    }
    .mhs-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .online-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #fff;
        position: absolute;
        bottom: -1px;
        right: -1px;
    }
    .online-dot.online { background: #22c55e; }
    .online-dot.offline { background: #d1d5db; }

    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    .status-badge.aktif { background: #dcfce7; color: #166534; }
    .status-badge.alumni { background: #dbeafe; color: #1e40af; }
    .status-badge.cuti { background: #fef3c7; color: #92400e; }
    .status-badge.drop_out { background: #fef2f2; color: #991b1b; }

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
        color: #4f46e5;
    }
    .btn-action-view:hover {
        background: #e0e7ff;
        color: #4338ca;
    }
    .btn-action-edit {
        background: #fef3c7;
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
        color: #9ca3af;
    }
    .empty-state h5 {
        color: #6b7280;
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
        color: #1f2937;
        line-height: 1;
    }
    .stat-label {
        font-size: 12px;
        color: #9ca3af;
        font-weight: 500;
    }

    .sso-badge {
        font-size: 9px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 6px;
        background: #eef2ff;
        color: #4f46e5;
        letter-spacing: 0.05em;
    }

    .last-active {
        font-size: 11px;
        color: #9ca3af;
    }
</style>

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
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Direktori Mahasiswa</h3>
        <p class="text-dark fw-bold mb-0" style="font-size: 14px;">Daftar seluruh mahasiswa yang terdaftar di program studi
            <span class="sso-badge ms-1">SSO UNDIP</span>
        </p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eef2ff;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $mahasiswa->total() }}</div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce7;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::where('status','aktif')->count() }}</div>
                <div class="stat-label">Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::where('status','alumni')->count() }}</div>
                <div class="stat-label">Lulus</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ \Modules\ManajemenMahasiswa\Models\Kemahasiswaan::whereIn('status',['cuti','drop_out'])->count() }}</div>
                <div class="stat-label">Cuti / DO</div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<form method="GET" action="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" name="search" class="form-control search-input w-100"
                   placeholder="Cari nama atau NIM mahasiswa..." value="{{ request('search') }}">
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
            <select name="status" class="form-select border-1 filter-select-custom"
                    style="min-width: 140px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Lulus</option>
                <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                <option value="drop_out" {{ request('status') == 'drop_out' ? 'selected' : '' }}>DO</option>
            </select>
        </div>
    </div>
</form>

<!-- Mahasiswa Table -->
@if($mahasiswa->count() > 0)
    <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #f3f4f6;">
        <table class="mhs-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Angkatan</th>
                    <th>Status</th>
                    <th>Aktivitas</th>
                    <th style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $index => $mhs)
                    <tr>
                        <td style="color: #9ca3af; font-weight: 500;">{{ $mahasiswa->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="position: relative;">
                                    <div class="mhs-avatar">
                                        @if($mhs->user && $mhs->user->avatar_url)
                                            <img src="{{ $mhs->user->avatar_url }}" alt="{{ $mhs->nama }}">
                                        @else
                                            {{ strtoupper(substr($mhs->nama, 0, 1)) }}
                                        @endif
                                    </div>
                                    @if($mhs->user)
                                        <div class="online-dot {{ $mhs->user->is_online ? 'online' : 'offline' }}"></div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">{{ $mhs->nama }}</div>
                                    @if($mhs->user && $mhs->user->email)
                                        <div style="font-size: 12px; color: #9ca3af;">{{ $mhs->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="font-weight: 600; font-family: monospace; color: #4f46e5;">{{ $mhs->nim }}</td>
                        <td><span style="font-weight: 600;">{{ $mhs->angkatan }}</span></td>
                        <td>
                            <span class="status-badge {{ $mhs->status }}">
                                @switch($mhs->status)
                                    @case('aktif') Aktif @break
                                    @case('alumni') Lulus @break
                                    @case('cuti') Cuti @break
                                    @case('drop_out') DO @break
                                    @default {{ ucfirst($mhs->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @if($mhs->user && $mhs->user->last_login)
                                <div class="last-active">
                                    {{ $mhs->user->last_login->diffForHumans() }}
                                </div>
                            @else
                                <span class="last-active">Belum login</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.show', $mhs->id) }}"
                                   class="btn-action btn-action-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Detail
                                </a>
                                @if($isAdmin)
                                    <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.edit', $mhs->id) }}"
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
    @if($mahasiswa->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $mahasiswa->withQueryString()->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="17" y1="11" x2="23" y2="11"></line>
            </svg>
        </div>
        <h5>Belum ada data mahasiswa</h5>
        <p style="font-size: 14px; color: #9ca3af;">Data mahasiswa yang terdaftar akan muncul di sini</p>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</x-dynamic-component>
