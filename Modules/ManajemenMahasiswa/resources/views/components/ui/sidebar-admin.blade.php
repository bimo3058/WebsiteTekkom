<div class="sidebar">
    <h5 class="fw-bold">🎓 Portal Mahasiswa</h5>
    <small class="text-muted">Manajemen Kemahasiswaan</small>

    <div class="menu-title">Main Menu</div>

    <nav class="sidebar-nav">
        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.mahasiswa.dashboard') }}"
            routeName="manajemenmahasiswa.mahasiswa.dashboard" icon="📊" label="Dashboard Analitik" />

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.pengumuman.index') }}"
            routeName="manajemenmahasiswa.pengumuman" icon="📢" label="Pengumuman" />

        <x-manajemenmahasiswa::ui.sidebar-item route="#" icon="👥" label="Direktori Mahasiswa" />

        <x-manajemenmahasiswa::ui.sidebar-item route="#" icon="📅" label="Kegiatan" />

        <x-manajemenmahasiswa::ui.sidebar-item route="{{ route('manajemenmahasiswa.forum.index') }}"  routeName="manaje
 m          enma hasiswa.forum" icon="💬" label="Forum Diskusi" />

        <x-manajemenmahasiswa::ui.sidebar-item route="#" icon="📞" label="Layanan Pengaduan" />
    </nav>

    <div class="bottom-menu">
        <x-manajemenmahasiswa::ui.sidebar-item route="#" icon="⚙️" label="Settings" />
        <a href="#">🆘 Help & Center</a>

        <form action="{{ route('logout') }}" method="POST">
  @csrf      <button class="btn btn-link text-danger p-0 mt-2">🚪 Logout</button>
            </form>
    </div>
    </div>