<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.layout-styles')
@include('banksoal::partials.dosen.sidebar', ['active' => 'bank-soal'])
@include('banksoal::partials.dosen.topbar')

<!-- MAIN -->
<main class="main">

    <div class="page-header">
        <div class="page-header-left">
            <h1>Manajemen Bank Soal</h1>
            <p>Manage and organize your question repository</p>
        </div>

        {{-- Header Actions — hanya muncul jika punya edit permission --}}
        @can('banksoal.edit')
            <div class="header-actions" style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="btn-outline">
                    <i class="fas fa-paper-plane"></i> Ajukan Soal
                </a>
                <div class="btn-split">
                    <a href="{{ route('banksoal.soal.dosen.index') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Buat Soal
                    </a>
                    <button class="btn-divider"><i class="fas fa-chevron-down"></i></button>
                </div>
            </div>
        @else
            <div style="color: #94a3b8; font-size: 12px; font-style: italic;">
                <i class="fas fa-info-circle"></i> Mode Lihat Saja — Anda tidak memiliki izin untuk menambah/mengubah soal
            </div>
        @endcan
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
                                @include('banksoal::partials.dosen._soal-actions')
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
                                    @include('banksoal::partials.dosen._soal-actions')
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

    {{-- ── EKSTRAKSI SOAL — hanya muncul jika punya edit permission ─── --}}
    @can('banksoal.edit')
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
    @else
        {{-- Tampilan terkunci untuk user tanpa edit permission --}}
        <div class="section-card" style="opacity: 0.5; background: #f8fafc; border: 1px dashed #cbd5e1;">
            <div style="text-align: center; padding: 30px; color: #64748b;">
                <i class="fas fa-lock" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                <p style="font-size: 14px; font-weight: bold; margin: 0 0 4px;">Fitur Ekstraksi Soal Terkunci</p>
                <p style="font-size: 12px; margin: 0;">Anda memerlukan izin <strong>Edit</strong> untuk melakukan penarikan soal.</p>
            </div>
        </div>
    @endcan

</main>

{{-- ═══ Search Handler Component ═══ --}}
<script src="{{ asset('modules/banksoal/js/Banksoal/shared/SearchHandler.js') }}"></script>

@include('banksoal::partials.dosen.layout-scripts')

</x-banksoal::layouts.dosen-master>