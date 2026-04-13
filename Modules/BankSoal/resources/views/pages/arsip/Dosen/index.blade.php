<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.layout-styles')
@include('banksoal::partials.dosen.sidebar', ['active' => 'arsip'])
@include('banksoal::partials.dosen.topbar')

    <!-- MAIN -->
    <main class="main">

<div class="page-header">
    <div class="page-header-left">
        <h1>Arsip Soal</h1>
        <p>Kelola dan tinjau riwayat soal yang telah diarsipkan per semester.</p>
    </div>
    <a href="#" class="btn-outline"><i class="fas fa-download"></i> Export Arsip</a>
</div>

{{-- STATS ROW --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon-wrap blue"><i class="fas fa-archive"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] ?? 248 }}</div>
            <div class="stat-label">Total Soal Diarsipkan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap green"><i class="fas fa-book-open"></i></div>
        <div>
            <div class="stat-value">{{ $stats['mata_kuliah'] ?? 12 }}</div>
            <div class="stat-label">Mata Kuliah</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap orange"><i class="fas fa-calendar-alt"></i></div>
        <div>
            <div class="stat-value">{{ $stats['semester'] ?? 6 }}</div>
            <div class="stat-label">Semester Tercatat</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap purple"><i class="fas fa-clock-rotate-left"></i></div>
        <div>
            <div class="stat-value">{{ $stats['tahun'] ?? 3 }}</div>
            <div class="stat-label">Tahun Ajaran</div>
        </div>
    </div>
</div>

{{-- ARSIP TABLE --}}
<div class="section-card">
    <div class="section-header-row">
        <div class="section-title">Daftar Arsip Soal</div>
    </div>

    <div class="toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" placeholder="Cari soal, mata kuliah, atau topik..." id="searchArsip">
        </div>
        <div class="filter-group">
            <select class="filter-select">
                <option value="">Semua Tahun</option>
                @foreach($tahunAjarans ?? ['2023/2024','2022/2023','2021/2022'] as $ta)
                    <option value="{{ $ta }}">{{ $ta }}</option>
                @endforeach
            </select>
            <select class="filter-select">
                <option value="">Semua Semester</option>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
            <select class="filter-select">
                <option value="">Semua Tingkat</option>
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <table id="tableArsip">
            <thead>
                <tr>
                    <th>ID Soal</th>
                    <th>Mata Kuliah</th>
                    <th>Topik</th>
                    <th>Semester</th>
                    <th>Tingkat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($arsips ?? [] as $arsip)
                <tr>
                    <td class="id-cell">{{ $arsip->kode }}</td>
                    <td><span class="course-pill">{{ $arsip->mataKuliah->kode }}</span> {{ $arsip->mataKuliah->nama }}</td>
                    <td>{{ $arsip->topik }}</td>
                    <td>{{ $arsip->semester }} {{ $arsip->tahun_ajaran }}</td>
                    <td>
                        @php $d = strtolower($arsip->tingkat_kesulitan ?? ''); @endphp
                        <span class="badge badge-{{ $d === 'hard' ? 'hard' : ($d === 'easy' ? 'easy' : 'medium') }}">
                            {{ ucfirst($arsip->tingkat_kesulitan) }}
                        </span>
                    </td>
                    <td><span class="badge badge-archived">Diarsipkan</span></td>
                    <td>
                        <div class="row-actions">
                            <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Pulihkan"><i class="fas fa-rotate-left"></i></button>
                            <button class="action-btn del" title="Hapus Permanen"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                {{-- Demo rows grouped by tahun --}}
                @foreach([
                    ['divider' => '2023/2024 - Ganjil', 'rows' => [
                        ['A-101','CS-201','Algorithms','Dynamic Programming','Ganjil 2023/2024','Hard'],
                        ['A-102','CS-201','Algorithms','Graph Traversal','Ganjil 2023/2024','Medium'],
                        ['A-103','CS-301','Databases','Normalization','Ganjil 2023/2024','Easy'],
                    ]],
                    ['divider' => '2022/2023 - Genap', 'rows' => [
                        ['A-089','CS-202','Data Structures','Linked Lists','Genap 2022/2023','Medium'],
                        ['A-090','CS-305','Networks','OSI Model','Genap 2022/2023','Easy'],
                        ['A-091','CS-202','Data Structures','Hash Tables','Genap 2022/2023','Hard'],
                    ]],
                ] as $group)
                    <tr><td colspan="7" style="padding:0;border:none">
                        <div class="year-divider">{{ $group['divider'] }}</div>
                    </td></tr>
                    @foreach($group['rows'] as $row)
                    <tr>
                        <td class="id-cell">{{ $row[0] }}</td>
                        <td><span class="course-pill">{{ $row[1] }}</span> {{ $row[2] }}</td>
                        <td>{{ $row[3] }}</td>
                        <td>{{ $row[4] }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($row[5]) === 'hard' ? 'hard' : (strtolower($row[5]) === 'easy' ? 'easy' : 'medium') }}">
                                {{ $row[5] }}
                            </span>
                        </td>
                        <td><span class="badge badge-archived">Diarsipkan</span></td>
                        <td>
                            <div class="row-actions">
                                <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                                <button class="action-btn" title="Pulihkan"><i class="fas fa-rotate-left"></i></button>
                                <button class="action-btn del" title="Hapus"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-footer">
        <span class="table-info">Showing 1–6 of {{ $arsips?->total() ?? 248 }} entries</span>
        <div class="pagination">
            <a href="#" class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:10px"></i></a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <span class="page-btn" style="border:none;cursor:default">...</span>
            <a href="#" class="page-btn">42</a>
            <a href="#" class="page-btn"><i class="fas fa-chevron-right" style="font-size:10px"></i></a>
        </div>
    </div>
</div>
    </main>

{{-- ═══ Search Handler Component ═══ --}}
<script src="{{ asset('modules/banksoal/js/Banksoal/shared/SearchHandler.js') }}"></script>

@include('banksoal::partials.dosen.layout-scripts')

</x-banksoal::layouts.dosen-master>