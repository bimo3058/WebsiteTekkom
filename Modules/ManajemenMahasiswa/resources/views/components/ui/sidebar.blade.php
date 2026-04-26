<div class="sidebar">
    {{-- Sidebar Toggle Button --}}
    <button @click="sidebarOpen = !sidebarOpen" class="sidebar-toggle">
        <svg x-show="sidebarOpen" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <svg x-show="!sidebarOpen" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>

    @php
        $sidebarRoles = auth()->user()->roles->pluck('name')->toArray();
        $showDashboardAnalitik = count(array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm'])) > 0;
        $showManajemenPengguna = count(array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit'])) > 0;

        if (array_intersect($sidebarRoles, ['superadmin', 'admin', 'admin_kemahasiswaan'])) {
            $portalLabel = 'Portal Admin';
        } elseif (array_intersect($sidebarRoles, ['gpm', 'dosen_koordinator', 'dosen'])) {
            $portalLabel = 'Portal Dosen';
        } elseif (in_array('pengurus_himpunan', $sidebarRoles)) {
            $portalLabel = 'Portal Mahasiswa';
        } else {
            $portalLabel = 'Portal Mahasiswa';
        }
    @endphp

    <div class="d-flex align-items-center gap-3 mb-4" :class="{ 'justify-content-center': !sidebarOpen }">
        <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0"
            style="width: 44px; height: 44px; background: #4f46e5;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                <path d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
        </div>
        <div class="portal-info" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px; letter-spacing: -0.01em;">{{ $portalLabel }}</h5>
            <small class="text-muted" style="font-size: 12px; line-height: 1;">Manajemen Kemahasiswaan</small>
        </div>
    </div>

    <div class="menu-title mb-2" x-show="sidebarOpen">Main Menu</div>

    <nav class="sidebar-nav d-flex flex-column gap-1">
        @if($showDashboardAnalitik)
            <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.dashboard') }}"
                routeName="manajemenmahasiswa.dashboard" label="Dashboard Analitik">
                <x-slot:iconSlot>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"></path>
                        <path d="M18 17V9"></path>
                        <path d="M13 17V5"></path>
                        <path d="M8 17v-3"></path>
                    </svg>
                </x-slot:iconSlot>
            </x-manajemenmahasiswa::ui.sidebar-item>
        @endif


        @if($showManajemenPengguna)
            <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengguna.index') }}"
                routeName="manajemenmahasiswa.pengguna" label="Manajemen Pengguna">
                <x-slot:iconSlot>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        <line x1="19" y1="8" x2="23" y2="12"></line>
                        <line x1="23" y1="8" x2="19" y2="12"></line>
                    </svg>
                </x-slot:iconSlot>
            </x-manajemenmahasiswa::ui.sidebar-item>
        @endif

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengumuman.index') }}"
            routeName="manajemenmahasiswa.pengumuman" label="Pengumuman">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 11 18-5v12L3 14v-3z"></path>
                    <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                </svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <div class="sidebar-dropdown {{ request()->routeIs('manajemenmahasiswa.direktori.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-link-item sidebar-dropdown-toggle {{ request()->routeIs('manajemenmahasiswa.direktori.*') ? 'active' : '' }}"
                onclick="event.stopPropagation(); this.closest('.sidebar-dropdown').classList.toggle('open')"
                :class="{ 'justify-content-center': !sidebarOpen }">
                <span class="nav-icon d-inline-flex">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="nav-label" style="flex-grow: 1;" x-show="sidebarOpen">Direktori Mahasiswa</span>
                <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;"
                    x-show="sidebarOpen">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </a>
            <div class="sidebar-dropdown-menu" x-show="sidebarOpen">
                @php
                    $userRoles = auth()->user()->roles->pluck('name')->toArray();
                    // Pengurus himpunan dan mahasiswa bisa lihat list tapi tidak bisa edit (dikontrol di routes)
                    $canViewAll = (bool) array_intersect($userRoles, ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'mahasiswa']);
                    $mahasiswaRoute = $canViewAll
                        ? route('manajemenmahasiswa.direktori.mahasiswa.index')
                        : route('manajemenmahasiswa.direktori.mahasiswa.profil');
                    $alumniRoute = $canViewAll
                        ? route('manajemenmahasiswa.direktori.alumni.index')
                        : route('manajemenmahasiswa.direktori.alumni.profil');
                @endphp
                <a href="{{ $mahasiswaRoute }}"
                    class="nav-link-item sub-item {{ request()->routeIs('manajemenmahasiswa.direktori.mahasiswa.*') ? 'active' : '' }}">
                    <span class="nav-icon d-inline-flex">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <span class="nav-label">Mahasiswa</span>
                </a>
                <a href="{{ $alumniRoute }}"
                    class="nav-link-item sub-item {{ request()->routeIs('manajemenmahasiswa.direktori.alumni.*') ? 'active' : '' }}">
                    <span class="nav-icon d-inline-flex">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c3 3 9 3 12 0v-5" />
                        </svg>
                    </span>
                    <span class="nav-label">Alumni</span>
                </a>
            </div>
        </div>

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.kegiatan.index') }}"
            routeName="manajemenmahasiswa.kegiatan" label="Kegiatan">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                    <line x1="16" x2="16" y1="2" y2="6"></line>
                    <line x1="8" x2="8" y1="2" y2="6"></line>
                    <line x1="3" x2="21" y1="10" y2="10"></line>
                </svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.forum.index') }}"
            routeName="manajemenmahasiswa.forum" label="Forum Diskusi">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        @if(!array_intersect($sidebarRoles, ['gpm', 'dosen_koordinator', 'dosen', 'alumni']))
            <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengaduan.index') }}"
                routeName="manajemenmahasiswa.pengaduan" label="Layanan Pengaduan">
                <x-slot:iconSlot>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                </x-slot:iconSlot>
            </x-manajemenmahasiswa::ui.sidebar-item>
        @endif
    </nav>

    <div class="bottom-menu pe-4">
        <!-- User Profile Card -->
        @php
            $currentUser = auth()->user();
            $currentName = $currentUser->name ?? 'User';
            $currentInitials = strtoupper(substr($currentName, 0, 1));
            $spIdx = strpos($currentName, ' ');
            if ($spIdx !== false) {
                $currentInitials .= strtoupper(substr($currentName, $spIdx + 1, 1));
            }
        @endphp
        <div class="d-flex align-items-center gap-3 px-3 py-2 mb-2 rounded"
            style="background: #f8fafc; border: 1px solid #e5e7eb;"
            :class="{ 'justify-content-center px-2': !sidebarOpen }">
            <div class="d-flex align-items-center justify-content-center rounded-circle border border-2 border-white shadow-sm flex-shrink-0"
                style="width: 36px; height: 36px; background: #e0e7ff; color: #4f46e5; font-weight: 600; font-size: 13px; overflow: hidden;">
                @if(isset($currentUser->avatar_url) && $currentUser->avatar_url)
                    <img src="{{ $currentUser->avatar_url }}" alt="Avatar"
                        style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ $currentInitials }}
                @endif
            </div>
            <div class="user-info" style="min-width: 0; flex: 1;" x-show="sidebarOpen">
                <p class="mb-0 text-dark fw-bold text-truncate" style="font-size: 13px; letter-spacing: -0.01em;">
                    {{ $currentName }}
                </p>
                <p class="mb-0 text-muted text-uppercase text-truncate"
                    style="font-size: 10px; font-weight: 700; letter-spacing: 0.05em; line-height: 1.2;">
                    {{ implode(' / ', array_map('ucfirst', $currentUser->roles->pluck('name')->toArray())) }}
                </p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn-logout" type="submit" :class="{ 'justify-content-center': !sidebarOpen }">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    :style="!sidebarOpen ? 'margin-left: 0' : 'margin-left: 2px;'">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span x-show="sidebarOpen">Logout</span>
            </button>
        </form>
    </div>
</div>