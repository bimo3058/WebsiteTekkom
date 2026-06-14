<div class="sidebar">
    @php
        $sidebarRoles = auth()->user()->roles->pluck('name')->toArray();
        $showDashboardAnalitik = count(array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'ketua_departemen'])) > 0;
        $showManajemenPengguna = count(array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'dpm'])) > 0;

        $mainDashboardUrl = in_array('superadmin', $sidebarRoles) ? route('superadmin.dashboard') : route('dashboard');
        $currentRoute = request()->route()->getName();
    @endphp

    <style>
        .sidebar {
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
        }

        .sb-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 14px;
            border-bottom: 1px solid #DFE1E7;
            min-height: 60px;
            flex-shrink: 0;
        }

        .sb-brand-logo {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sb-brand-text {
            flex: 1;
            min-width: 0;
        }

        .sb-brand-name {
            font-family: 'Geist', 'Inter Tight', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #0D0D12;
            letter-spacing: -.01em;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sb-brand-tag {
            font-size: 9px;
            color: #808897;
            font-weight: 500;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sb-collapse-btn {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid #DFE1E7;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #666D80;
            transition: background .15s, border-color .15s, color .15s;
            padding: 0;
            margin-left: auto;
        }

        .sb-collapse-btn:hover {
            background: #F6F8FA;
            border-color: #C1C7CF;
            color: #0D0D12;
        }

        .sidebar-collapsed .sb-brand {
            justify-content: center;
        }

        .sidebar-collapsed .sb-collapse-btn {
            margin-left: 0;
        }

        .sb-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 6px 10px 10px;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .sb-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sb-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sb-nav::-webkit-scrollbar-thumb {
            background: #DFE1E7;
            border-radius: 9999px;
        }

        .sb-section-label {
            font-size: 10px;
            font-weight: 600;
            color: #808897;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 12px 10px 5px;
            white-space: nowrap;
        }

        .sb-footer {
            padding: 8px 10px 12px;
            border-top: 1px solid #DFE1E7;
            display: flex;
            flex-direction: column;
            gap: 1px;
            flex-shrink: 0;
        }

        .sidebar-dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease;
        }

        .sidebar-dropdown.open .sidebar-dropdown-menu {
            max-height: 300px;
        }

        .sidebar-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .sub-item {
            padding-left: 38px !important;
        }

        .sidebar-collapsed .sb-section-label,
        .sidebar-collapsed .nav-label,
        .sidebar-collapsed .sb-brand-text,
        .sidebar-collapsed .dropdown-arrow,
        .sidebar-collapsed .sidebar-dropdown-menu {
            display: none !important;
        }
    </style>

    <div class="sb-brand">
        <div class="sb-brand-logo">
            <img src="{{ asset('images/logo-undip.png') }}" alt="UNDIP"
                style="width:32px;height:32px;object-fit:contain;">
        </div>
        <div x-show="sidebarOpen" x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="sb-brand-text">
            <div class="sb-brand-name">SIMENMA</div>
            <div class="sb-brand-tag">Sistem Manajemen Mahasiswa</div>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="sb-collapse-btn" title="Toggle Sidebar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s ease;"
                :style="sidebarOpen ? '' : 'transform:rotate(180deg)'">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
    </div>

    <nav class="sb-nav">
        <div x-show="sidebarOpen" class="sb-section-label">Main Menu</div>

        <a href="{{ $mainDashboardUrl }}"
            class="{{ str_contains($currentRoute, 'dashboard') && !str_contains($currentRoute, 'manajemenmahasiswa') ? 'active' : '' }}">
            <span class="nav-icon d-inline-flex">
                {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/home-01.svg'))) !!}
            </span>
            <span class="nav-label" style="flex-grow:1;">Dashboard</span>
        </a>

        @if($showDashboardAnalitik)
            <a href="{{ route('manajemenmahasiswa.dashboard') }}"
                class="{{ $currentRoute === 'manajemenmahasiswa.dashboard' ? 'active' : '' }}">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow:1;">Analitik</span>
            </a>
        @endif

        @php
            $isKetua = (bool) array_intersect($sidebarRoles, ['ketua_unit', 'ketua_bidang', 'ketua_himpunan', 'wakil_ketua_himpunan']);
            $isAdminVerifier = (bool) array_intersect($sidebarRoles, ['admin', 'admin_kemahasiswaan', 'superadmin', 'dpm']);
            $isStaffHimpunan = in_array('staff_himpunan', $sidebarRoles) && !$isKetua && !$isAdminVerifier;

            $pengumumanDropdownActive = request()->routeIs('manajemenmahasiswa.pengumuman.*');

            // Counter pending verifikasi pengumuman
            $pendingVerifCount = 0;
            if ($isKetua) {
                // Ketua: hanya request yang ditujukan ke mereka
                $pendingVerifCount = \Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest::where('verifier_id', auth()->id())
                    ->where('status', 'pending')->count();
            } elseif ($isAdminVerifier) {
                // Admin: semua request pending (bisa override siapapun)
                $pendingVerifCount = \Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest::where('status', 'pending')->count();
            }

            // Counter untuk staff: berapa pengajuan milik dia yang masih pending
            $staffPendingCount = 0;
            if ($isStaffHimpunan) {
                $staffPendingCount = \Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest::where('requester_id', auth()->id())
                    ->where('status', 'pending')->count();
            }
        @endphp

        @if($isKetua || $isAdminVerifier)
            {{-- Dropdown Pengumuman — untuk ketua/admin yang bisa verifikasi pengumuman orang lain --}}
            <div class="sidebar-dropdown {{ $pengumumanDropdownActive ? 'open' : '' }}">
                <a href="javascript:void(0)" class="sidebar-dropdown-toggle {{ $pengumumanDropdownActive ? 'active' : '' }}"
                    onclick="event.stopPropagation(); this.closest('.sidebar-dropdown').classList.toggle('open')">
                    <span class="nav-icon d-inline-flex">
                        {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/announcement-01.svg'))) !!}
                    </span>
                    <span class="nav-label" style="flex-grow: 1;">Pengumuman</span>
                    @if($pendingVerifCount > 0)
                        <span class="nav-label"
                            style="background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:50px;">{{ $pendingVerifCount }}</span>
                    @endif
                    <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </a>
                <div class="sidebar-dropdown-menu">
                    <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.pengumuman.index') || request()->routeIs('manajemenmahasiswa.pengumuman.show') || request()->routeIs('manajemenmahasiswa.pengumuman.create') || request()->routeIs('manajemenmahasiswa.pengumuman.edit') ? 'active' : '' }}">
                        <span class="nav-label">Daftar Pengumuman</span>
                    </a>
                    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.pengumuman.verifikasi.*') ? 'active' : '' }}">
                        <span class="nav-label">Verifikasi Pengumuman</span>
                        @if($pendingVerifCount > 0)
                            <span style="background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:50px;margin-left:auto;">{{ $pendingVerifCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

        @elseif($isStaffHimpunan)
            {{-- Dropdown Pengumuman — untuk staff himpunan: lihat daftar + status verifikasi miliknya --}}
            <div class="sidebar-dropdown {{ $pengumumanDropdownActive ? 'open' : '' }}">
                <a href="javascript:void(0)" class="sidebar-dropdown-toggle {{ $pengumumanDropdownActive ? 'active' : '' }}"
                    onclick="event.stopPropagation(); this.closest('.sidebar-dropdown').classList.toggle('open')">
                    <span class="nav-icon d-inline-flex">
                        {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/announcement-01.svg'))) !!}
                    </span>
                    <span class="nav-label" style="flex-grow: 1;">Pengumuman</span>
                    @if($staffPendingCount > 0)
                        <span class="nav-label"
                            style="background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:50px;">{{ $staffPendingCount }}</span>
                    @endif
                    <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </a>
                <div class="sidebar-dropdown-menu">
                    <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.pengumuman.index') || request()->routeIs('manajemenmahasiswa.pengumuman.show') || request()->routeIs('manajemenmahasiswa.pengumuman.create') || request()->routeIs('manajemenmahasiswa.pengumuman.edit') ? 'active' : '' }}">
                        <span class="nav-label">Daftar Pengumuman</span>
                    </a>
                    <a href="{{ route('manajemenmahasiswa.pengumuman.riwayat.verifikasi') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.pengumuman.riwayat.verifikasi') ? 'active' : '' }}">
                        <span class="nav-label">Status Verifikasi</span>
                        @if($staffPendingCount > 0)
                            <span style="background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:50px;margin-left:auto;">{{ $staffPendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

        @else
            {{-- Link tunggal Pengumuman — untuk role lain (mahasiswa, alumni, dosen, dll) --}}
            <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}"
                class="{{ request()->routeIs('manajemenmahasiswa.pengumuman.*') ? 'active' : '' }}">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/announcement-01.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow:1;">Pengumuman</span>
            </a>
        @endif

        <div x-show="sidebarOpen" class="sb-section-label">Direktori Mahasiswa</div>
        @php
            $isDirektoriActive = request()->routeIs('manajemenmahasiswa.direktori.*');
            $canViewAll = (bool) array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'ketua_departemen', 'dosen', 'dosen_koordinator', 'pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'mahasiswa', 'alumni']);
            $mahasiswaRoute = $canViewAll ? route('manajemenmahasiswa.direktori.mahasiswa.index') : route('manajemenmahasiswa.direktori.mahasiswa.profil');
            $alumniRoute = $canViewAll ? route('manajemenmahasiswa.direktori.alumni.index') : route('manajemenmahasiswa.direktori.alumni.profil');
        @endphp
        <div class="sidebar-dropdown {{ $isDirektoriActive ? 'open' : '' }}">
            <a href="javascript:void(0)" class="sidebar-dropdown-toggle {{ $isDirektoriActive ? 'active' : '' }}"
                onclick="event.stopPropagation(); this.closest('.sidebar-dropdown').classList.toggle('open')">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/users-01.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow: 1;">Mahasiswa & Alumni</span>
                <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </a>
            <div class="sidebar-dropdown-menu">
                <a href="{{ $mahasiswaRoute }}"
                    class="sub-item {{ request()->routeIs('manajemenmahasiswa.direktori.mahasiswa.*') ? 'active' : '' }}">
                    <span class="nav-label">Mahasiswa</span>
                </a>
                <a href="{{ $alumniRoute }}"
                    class="sub-item {{ request()->routeIs('manajemenmahasiswa.direktori.alumni.*') ? 'active' : '' }}">
                    <span class="nav-label">Alumni</span>
                </a>
            </div>
        </div>

        <div x-show="sidebarOpen" class="sb-section-label">Manajemen</div>

        @if($showManajemenPengguna)
            <a href="{{ route('manajemenmahasiswa.pengguna.index') }}"
                class="{{ request()->routeIs('manajemenmahasiswa.pengguna.*') ? 'active' : '' }}">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/users-03.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow:1;">Permission</span>
            </a>
        @endif

        @php
            $kegiatanRoutes = ['manajemenmahasiswa.proker.*', 'manajemenmahasiswa.pelaksanaan.*', 'manajemenmahasiswa.kegiatan.*'];
            $kegiatanActive = collect($kegiatanRoutes)->contains(fn($r) => request()->routeIs($r));
            $canViewProker = (bool) array_intersect($sidebarRoles, ['superadmin', 'admin_kemahasiswaan', 'dpm', 'gpm', 'ketua_departemen', 'wakil_ketua_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit']);
            $canViewPelaksanaan = (bool) array_intersect($sidebarRoles, ['superadmin', 'admin_kemahasiswaan', 'dpm', 'gpm', 'ketua_departemen', 'wakil_ketua_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan']);
        @endphp
        <div class="sidebar-dropdown {{ $kegiatanActive ? 'open' : '' }}">
            <a href="javascript:void(0)" class="sidebar-dropdown-toggle {{ $kegiatanActive ? 'active' : '' }}"
                onclick="event.stopPropagation(); this.closest('.sidebar-dropdown').classList.toggle('open')">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/calendar.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow: 1;">Kegiatan</span>
                <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </a>
            <div class="sidebar-dropdown-menu">
                @if($canViewProker)
                    <a href="{{ route('manajemenmahasiswa.proker.index') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.proker.*') ? 'active' : '' }}">
                        <span class="nav-label">Rencana Proker</span>
                    </a>
                @endif
                @if($canViewPelaksanaan)
                    <a href="{{ route('manajemenmahasiswa.pelaksanaan.index') }}"
                        class="sub-item {{ request()->routeIs('manajemenmahasiswa.pelaksanaan.*') ? 'active' : '' }}">
                        <span class="nav-label">Pelaksanaan Kegiatan</span>
                    </a>
                @endif
                <a href="{{ route('manajemenmahasiswa.kegiatan.index') }}"
                    class="sub-item {{ request()->routeIs('manajemenmahasiswa.kegiatan.*') ? 'active' : '' }}">
                    <span class="nav-label">Laporan & Arsip</span>
                </a>
            </div>
        </div>

        @if(!array_intersect($sidebarRoles, ['dosen', 'dosen_koordinator']))
            <a href="{{ route('manajemenmahasiswa.verifikasi.index') }}"
                class="{{ request()->routeIs('manajemenmahasiswa.verifikasi.*') ? 'active' : '' }}">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/check-square-1.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow:1;">Verifikasi Data</span>
            </a>
        @endif

        <a href="{{ route('manajemenmahasiswa.forum.index') }}"
            class="{{ request()->routeIs('manajemenmahasiswa.forum.*') ? 'active' : '' }}">
            <span class="nav-icon d-inline-flex">
                {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/message-text-square.svg'))) !!}
            </span>
            <span class="nav-label" style="flex-grow:1;">Forum Diskusi</span>
        </a>

        @if(array_intersect($sidebarRoles, ['mahasiswa', 'pengurus_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dosen', 'dosen_koordinator', 'dpm', 'ketua_departemen']))
            <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}"
                class="{{ request()->routeIs('manajemenmahasiswa.pengaduan.*') ? 'active' : '' }}">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/headphone-01.svg'))) !!}
                </span>
                <span class="nav-label" style="flex-grow:1;">Layanan Pengaduan</span>
            </a>
        @endif
    </nav>

    <div class="sb-footer">
        <a href="{{ route('profile.edit') }}" class="btn-logout">
            <span class="nav-icon d-inline-flex">
                {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/gear.svg'))) !!}
            </span>
            <span class="nav-label">Settings</span>
        </a>

        <a href="#" class="btn-logout">
            <span class="nav-icon d-inline-flex">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" style="width: 16px; height: 16px;">
                    <path d="M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01" />
                </svg>
            </span>
            <span class="nav-label">Help & Center</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin-bottom: 0;">
            @csrf
            <button class="btn-logout" type="submit" style="color: #DF1C41;">
                <span class="nav-icon d-inline-flex">
                    {!! str_replace(['#0D0D12', 'black'], 'currentColor', file_get_contents(public_path('images/icons/log-out-01.svg'))) !!}
                </span>
                <span class="nav-label" style="color: #DF1C41 !important;">Logout</span>
            </button>
        </form>
    </div>
</div>