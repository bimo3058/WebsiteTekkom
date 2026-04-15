<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🎓</div>
        <div class="brand-text">
            <strong>Portal Mahasiswa</strong>
            <span>Manajemen Kemahasiswaan</span>
        </div>
    </div>

    <div class="menu-title">Main Menu</div>

    <nav class="sidebar-nav">
        <x-manajemenmahasiswa::ui.sidebar-item
            route="{{ route('manajemenmahasiswa.pengumuman.index') }}"
            routeName="manajemenmahasiswa.pengumuman"
            icon="📢" label="Pengumuman" />

        <x-manajemenmahasiswa::ui.sidebar-item
            route="#"
            icon="👥" label="Direktori Mahasiswa" />

        <x-manajemenmahasiswa::ui.sidebar-item
            route="#"
            icon="📅" label="Kegiatan" />

        <x-manajemenmahasiswa::ui.sidebar-item
            route="#"
            icon="💬" label="Forum Diskusi" />
    </nav>

    <div class="bottom-menu">
        <x-manajemenmahasiswa::ui.sidebar-item
            route="#"
            icon="⚙️" label="Settings" />

        <a href="#" class="nav-link-item">
            <span class="nav-icon">🆘</span> Help & Center
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <span class="nav-icon">🚪</span> Logout
            </button>
        </form>
    </div>
</div>