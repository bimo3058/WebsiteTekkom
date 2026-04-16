<div class="sidebar">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center rounded" style="width: 44px; height: 44px; background: #4f46e5; flex-shrink: 0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
        </div>
        <div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 15px; letter-spacing: -0.01em;">Portal Mahasiswa</h5>
            <small class="text-muted" style="font-size: 12px; line-height: 1;">Manajemen Kemahasiswaan</small>
        </div>
    </div>

    <div class="menu-title mb-2" style="font-size: 13px; font-weight: 500; color: #9ca3af;">Main Menu</div>

    <nav class="sidebar-nav d-flex flex-column gap-1">
        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.mahasiswa.dashboard') }}"
            routeName="manajemenmahasiswa.mahasiswa.dashboard" label="Dashboard Analitik">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18 17V9"></path><path d="M13 17V5"></path><path d="M8 17v-3"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengumuman.index') }}"
            routeName="manajemenmahasiswa.pengumuman" label="Pengumuman">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"></path><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="#">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </x-slot:iconSlot>
            <div class="d-flex justify-content-between align-items-center w-100" style="flex-grow: 1;">
                <span>Direktori Mahasiswa</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="#" label="Kegiatan">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.forum.index') }}"
            routeName="manajemenmahasiswa.forum" label="Forum Diskusi">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengaduan.index') }}"
            routeName="manajemenmahasiswa.pengaduan" label="Layanan Pengaduan">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="#" label="Layanan Pengaduan">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>
    </nav>

    <div class="bottom-menu pe-4">
        <x-manajemenmahasiswa::ui.sidebar-item route="#" label="Settings">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <x-manajemenmahasiswa::ui.sidebar-item route="#" label="Help &amp; Center">
            <x-slot:iconSlot>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
            </x-slot:iconSlot>
        </x-manajemenmahasiswa::ui.sidebar-item>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-link p-0 mt-1 d-flex align-items-center gap-3 w-100 text-decoration-none" style="padding: 12px 16px !important; color: #ef4444; font-weight: 500; font-size: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </button>
        </form>
    </div>
</div>