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
                    <a href="{{ route('banksoal.soal.dosen.create') }}" class="btn-primary">
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

    @if(session('success'))
        <div style="padding: 15px; margin-bottom: 20px; background-color: #dcfce3; color: #166534; border-radius: 8px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── TABEL SOAL ─────────────────────────────── --}}
    <div class="section-card">
        <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="toolbar" style="justify-content: flex-start; flex-wrap: wrap;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="searchSoal" class="search-input" value="{{ request('searchSoal') }}" placeholder="Search questions, course, or topics...">
            </div>
            
            <select name="mk_id" class="filter-btn" style="appearance: auto; cursor: pointer; outline: none; background: #fff;">
                <option value="">Semua Mata Kuliah...</option>
                @foreach($mataKuliahDosen as $mk)
                    <option value="{{ $mk->id }}" {{ request('mk_id') == $mk->id ? 'selected' : '' }}>{{ $mk->nama }}</option>
                @endforeach
            </select>

            <select name="kesulitan" class="filter-btn" style="appearance: auto; cursor: pointer; outline: none; background: #fff;">
                <option value="">Semua Kesulitan...</option>
                <option value="easy" {{ request('kesulitan') == 'easy' ? 'selected' : '' }}>Mudah</option>
                <option value="intermediate" {{ request('kesulitan') == 'intermediate' ? 'selected' : '' }}>Sedang</option>
                <option value="advanced" {{ request('kesulitan') == 'advanced' ? 'selected' : '' }}>Sulit</option>
            </select>

            <button type="submit" class="filter-btn" style="background: var(--blue-50, #eff6ff); color: var(--blue, #2563eb); border-color: var(--blue-200, #bfdbfe);">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            @if(request()->hasAny(['searchSoal', 'mk_id', 'kesulitan']))
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="filter-btn" style="color: #ef4444; border-color: #fca5a5; text-decoration: none; background: #fef2f2;">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

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
                            <td class="id-cell">{{ $soal->kode_soal }}</td>
                            <td>{{ $soal->mataKuliah->nama ?? '-' }}</td>
                            <td>{{ strip_tags(\Illuminate\Support\Str::limit($soal->soal, 50)) }}</td>
                            <td>
                                @php $diff = strtolower($soal->kesulitan ?? ''); @endphp
                                <span class="badge badge-{{ $diff === 'advanced' ? 'hard' : ($diff === 'easy' ? 'easy' : 'medium') }}">
                                    {{ ucfirst($soal->kesulitan) }}
                                </span>
                            </td>
                            <td>
                                @include('banksoal::partials.dosen._soal-actions', ['soal' => $soal])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">
                                <i class="fas fa-folder-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                Belum ada soal di dalam bank soal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer" style="padding: 10px 0;">
            @if(isset($soals) && $soals->count() > 0)
                <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                    <span class="table-info">
                        Showing {{ $soals->firstItem() }} to {{ $soals->lastItem() }} of {{ $soals->total() }} questions
                    </span>
                    <div style="flex-grow: 1; display:flex; justify-content: flex-end;">
                        {{ $soals->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
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
                                <td>{{ $pkg->str_cpls }}</td>
                                <td>{{ $pkg->str_cpmks }}</td>
                                <td><strong>{{ $pkg->jumlah_soal }}</strong></td>
                                <td>
                                    <div class="row-actions">
                                        <button class="action-btn" title="Lihat"><i class="fas fa-eye"></i></button>
                                        <button class="action-btn" title="Tarik"><i class="fas fa-download"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">
                                    <i class="fas fa-box-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                    Belum ada paket soal yang tersedia untuk ditarik.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer" style="padding: 10px 0;">
                @if(isset($packages) && $packages->count() > 0)
                    <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                        <span class="table-info">
                            Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of {{ $packages->total() }} packages
                        </span>
                        <div style="flex-grow: 1; display:flex; justify-content: flex-end;">
                            {{ $packages->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
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