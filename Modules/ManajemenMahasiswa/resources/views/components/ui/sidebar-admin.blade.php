<div class="sidebar">
    <h5 class="fw-bold">🎓 Portal Mahasiswa</h5>
    <small class="text-muted">Manajemen Kemahasiswaan</small>

    <div class="menu-title">Main Menu</div>

    <x-sidebar-item route="dashboard" icon="📊" label="Dashboard Analitik" />
    <x-sidebar-item route="pengumuman" icon="📢" label="Pengumuman" />
    <x-sidebar-item route="direktori" icon="👥" label="Direktori Mahasiswa" />
    <x-sidebar-item route="kegiatan" icon="📅" label="Kegiatan" />
    <x-sidebar-item route="forum" icon="💬" label="Forum Diskusi" />
    <x-sidebar-item route="pengaduan" icon="📞" label="Layanan Pengaduan" />

    <div class="bottom-menu">
        <x-sidebar-item route="settings" icon="⚙️" label="Settings" />
        <a href="#">🆘 Help & Center</a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-link text-danger p-0 mt-2">🚪 Logout</button>
        </form>
    </div>
</div>