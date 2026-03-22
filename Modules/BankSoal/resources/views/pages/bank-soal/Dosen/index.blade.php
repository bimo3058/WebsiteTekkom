<x-banksoal::layouts.master>

<!-- SIDEBAR -->
    <aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
        <strong>Departemen Teknik Komputer</strong>
        <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('banksoal.dashboard') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Home</a>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS</a>
        <a href="{{ route('banksoal.banksoal.dosen.index') }}" class="nav-item active"><span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal</a>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal</a>
    </nav>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
    <button class="topbar-btn"><i class="fas fa-cog"></i></button>
    <button class="topbar-btn notif-btn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
    <div class="user-chip">
        <div class="user-avatar-chip">A</div>
        <div class="user-info">
        <strong>Prof. Dr. Siti Rahayu</strong>
        <span>198503122010121001</span>
        </div>
    </div>
    </header>

    <!-- MAIN -->
    <main class="main">

<div class="page-header">
    <div class="page-header-left">
        <h1>Manajemen Bank Soal</h1>
        <p>Manage and organize your question repository</p>
    </div>
    <div class="header-actions" style="display:flex; align-items:center; gap:10px;">
    <a href="{{ route('banksoal.banksoal.dosen.index') }}" class="btn-outline">
        <i class="fas fa-paper-plane"></i> Ajukan Soal
    </a>
    <div class="btn-split">
        <a href="{{ route('banksoal.banksoal.dosen.index') }}" class="btn-primary">
            <i class="fas fa-plus"></i> Buat Soal
        </a>
        <button class="btn-divider"><i class="fas fa-chevron-down"></i></button>
    </div>
</div>
</div>

{{-- ── TABEL SOAL ─────────────────────────────── --}}
<div class="section-card">
    <div class="toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search questions, course, or topics..." id="searchSoal">
        </div>
        <button class="filter-btn"><i class="fas fa-sliders-h"></i> Filter</button>
    </div>

    <div class="table-wrap">
        <table id="tableSoal">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Topic</th>
                    <th>Difficulty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($soals ?? [] as $soal)
                <tr>
                    <td class="id-cell">{{ $soal->kode }}</td>
                    <td>{{ $soal->mataKuliah->nama ?? '-' }}</td>
                    <td>{{ $soal->topik }}</td>
                    <td>
                        @php $diff = strtolower($soal->tingkat_kesulitan ?? ''); @endphp
                        <span class="badge badge-{{ $diff === 'hard' ? 'hard' : ($diff === 'easy' ? 'easy' : 'medium') }}">
                            {{ ucfirst($soal->tingkat_kesulitan) }}
                        </span>
                    </td>
                    <td>
                        <div class="row-actions">
                            <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="action-btn del" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                {{-- Demo rows --}}
                @foreach([
                    ['Q-101','Algorithms','Dynamic Programming','Hard'],
                    ['Q-102','Data Structures','Binary Trees','Medium'],
                    ['Q-103','Databases','SQL Joins','Easy'],
                    ['Q-104','Networks','TCP/IP','Medium'],
                ] as $row)
                <tr>
                    <td class="id-cell">{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($row[3]) === 'hard' ? 'hard' : (strtolower($row[3]) === 'easy' ? 'easy' : 'medium') }}">
                            {{ $row[3] }}
                        </span>
                    </td>
                    <td>
                        <div class="row-actions">
                            <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="action-btn del" title="Hapus"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <span class="table-info">
            Showing 1 to {{ $soals?->count() ?? 4 }} of {{ $soals?->total() ?? 24 }} questions
        </span>
        <div class="pagination">
            <a href="#" class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:11px"></i> Previous</a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <span class="page-btn" style="border:none;cursor:default">...</span>
            <a href="#" class="page-btn">Next <i class="fas fa-chevron-right" style="font-size:11px"></i></a>
        </div>
    </div>
</div>

{{-- ── EKSTRAKSI SOAL ─────────────────────────── --}}
<div class="section-card">
    <div class="section-header">
        <div>
            <div class="section-title">Ekstraksi Soal (Tarik Soal)</div>
            <div class="section-desc">Tarik kumpulan soal dari bank soal untuk digunakan pada ujian atau asesmen.</div>
        </div>
        <button class="btn-green"><i class="fas fa-download"></i> Tarik Soal</button>
    </div>

    <div class="toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Search packages, course codes, or names..." id="searchPackages">
        </div>
        <button class="filter-btn"><i class="fas fa-sliders-h"></i> Filter</button>
    </div>

    <div class="table-wrap">
        <table id="tablePackages">
            <thead>
                <tr>
                    <th>Kode MK</th>
                    <th>Mata Kuliah</th>
                    <th>CPL</th>
                    <th>CPMK</th>
                    <th>Jumlah Soal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages ?? [] as $pkg)
                <tr>
                    <td class="id-cell">{{ $pkg->kode }}</td>
                    <td>{{ $pkg->nama }}</td>
                    <td>{{ $pkg->cpls }}</td>
                    <td>{{ $pkg->cpmks }}</td>
                    <td><strong>{{ $pkg->jumlah_soal }}</strong></td>
                    <td>
                        <div class="row-actions">
                            <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Tarik"><i class="fas fa-download"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                @foreach([
                    ['CS-201','Algorithms','CPL-01, CPL-02','CPMK-01, CPMK-03',50],
                    ['CS-202','Data Structures','CPL-02, CPL-04','CPMK-02, CPMK-05',45],
                    ['CS-301','Databases','CPL-03','CPMK-01, CPMK-02',60],
                    ['CS-305','Networks','CPL-01, CPL-05','CPMK-04',40],
                ] as $row)
                <tr>
                    <td class="id-cell">{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>{{ $row[3] }}</td>
                    <td><strong>{{ $row[4] }}</strong></td>
                    <td>
                        <div class="row-actions">
                            <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Tarik"><i class="fas fa-download"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <span class="table-info">
            Showing 1 to {{ $packages?->count() ?? 4 }} of {{ $packages?->total() ?? 12 }} packages
        </span>
        <div class="pagination">
            <a href="#" class="page-btn disabled">Previous</a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <a href="#" class="page-btn">Next</a>
        </div>
    </div>
</div>
    </main>

<script>
document.getElementById('searchSoal').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tableSoal tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.getElementById('searchPackages').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablePackages tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>

</x-banksoal::layouts.master>