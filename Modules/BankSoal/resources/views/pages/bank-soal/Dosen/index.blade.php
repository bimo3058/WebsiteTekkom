<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Bank Soal</span>
    @endsection

<style>
    @keyframes modalPopUp {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .animate-popup {
        animation: modalPopUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Pagination */
    .pagination-list { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .pagination-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 8px;
        border: 1px solid #e2e8f0; background: #fff; color: #334155;
        border-radius: 8px; font-size: 12px; font-weight: 600;
        text-decoration: none; transition: all 0.2s;
    }
    .pagination-btn:hover:not(.opacity-45) { border-color: rgb(11,38,110); color: rgb(11,38,110); }
    .pagination-btn.active { background: rgb(11,38,110); border-color: rgb(11,38,110); color: #fff; }
    .pagination-ellipsis { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; font-size: 12px; color: #94a3b8; }

    /* Table loading spinner */
    .tbl-loading {
        align-items: center; justify-content: center;
        gap: 10px; padding: 40px 20px;
        color: #475569; font-size: 13px;
    }
    .tbl-spinner {
        width: 32px; height: 32px;
        border: 3px solid #e2e8f0;
        border-top-color: rgb(11, 38, 110);
        border-radius: 50%;
        animation: tbl-spin 0.7s linear infinite;
        flex-shrink: 0;
    }
    @keyframes tbl-spin { to { transform: rotate(360deg); } }
</style>

<x-banksoal::ui.page-header title="Manajemen Bank Soal" subtitle="Kelola dan organisir repositori pertanyaan Anda">
    <x-slot:actions>
        @can('banksoal.edit')
            <div class="relative" id="uploadDropdownContainer">
                <button type="button" onclick="toggleUploadDropdown()" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl px-4 py-2.5 font-medium text-slate-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-100">
                    <i class="fas fa-upload text-slate-500"></i> Upload Soal <i class="fas fa-chevron-down text-[10px] ml-1 text-slate-400"></i>
                </button>
                
                <div id="uploadDropdownMenu" class="absolute right-0 top-[110%] w-56 bg-white border border-slate-100 rounded-xl shadow-lg transition-all duration-200 ease-out origin-top-right z-50 overflow-hidden transform opacity-0 scale-95 pointer-events-none">
                    <a href="{{ route('banksoal.soal.dosen.export-csv') }}" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors border-b border-slate-50">
                        <i class="fas fa-download w-5 text-center mr-2 text-emerald-500"></i> Unduh Template Excel
                    </a>
                    <button type="button" onclick="openImportModal(); toggleUploadDropdown();" class="w-full flex items-center text-left px-4 py-3 text-sm text-slate-700 hover:bg-primary/10 hover:text-primary transition-colors">
                        <i class="fas fa-file-csv w-5 text-center mr-2 text-primary"></i> Import CSV
                    </button>
                </div>
            </div>

            <a href="{{ route('banksoal.soal.dosen.create') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg px-4 py-2.5 font-medium transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Buat Soal
            </a>
            
            <button type="button" onclick="openAjukanModal()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-4 py-2.5 font-medium transition-colors shadow-sm">
                <i class="fas fa-paper-plane"></i> Ajukan Soal
            </button>
        @else
            <div class="text-sm text-slate-500 italic">
                <i class="fas fa-info-circle"></i> Mode Lihat Saja — Anda tidak memiliki izin untuk menambah/mengubah soal
            </div>
        @endcan
    </x-slot:actions>
</x-banksoal::ui.page-header>

<div
    id="banksoal-dosen-page-data"
    data-mk-json="{{ base64_encode($mataKuliahDosen->toJson()) }}"
    hidden
></div>



<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h2 class="text-lg font-semibold text-slate-900">Daftar Soal</h2>
    </div>

    <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="p-6 border-b border-slate-200 flex flex-col md:flex-row gap-3 flex-wrap items-center" id="filterForm" onsubmit="window.showLoader();">
        <div class="relative flex-1 min-w-[250px] w-full">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" data-search-tab="soal" name="searchSoal" list="search-suggestions" value="{{ request('searchSoal') }}" placeholder="Cari soal, kursus, atau topik..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none" autocomplete="off" id="searchSoal">
            <datalist id="search-suggestions">
                @foreach($mataKuliahDosen as $mk)
                    <option value="{{ $mk->nama }}"></option>
                @endforeach
            </datalist>
        </div>

        <select name="sort" id="sortBy" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none cursor-pointer min-w-[140px] flex-shrink-0 focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none">
            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
            <option value="nama-asc" {{ request('sort') == 'nama-asc' ? 'selected' : '' }}>Nama A-Z</option>
            <option value="nama-desc" {{ request('sort') == 'nama-desc' ? 'selected' : '' }}>Nama Z-A</option>
        </select>

        <div class="flex items-center gap-2">
            <x-banksoal::ui.filter-panel formId="filterForm" :hasActiveFilter="request('status') ? true : false" resetRoute="{{ route('banksoal.soal.dosen.index') }}" applyLabel="Terapkan">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Status Soal</label>
                    <div class="space-y-2">
                        @foreach([
                            'draft' => '📄 Draft',
                            'diajukan' => '⏳ Diajukan',
                            'disetujui' => '✅ Disetujui',
                            'revisi' => '✕ Perlu Revisi'
                        ] as $val => $label)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer group">
                            <input type="radio" name="status" value="{{ $val }}" @checked(request('status') == $val) class="w-4 h-4 rounded-full border-slate-300 text-primary focus:ring-primary transition-all">
                            <span class="text-sm text-slate-700 group-hover:text-primary transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </x-banksoal::ui.filter-panel>
        </div>
    </form>

    <div class="overflow-x-auto" data-tab-panel="soal">
        <table class="w-full" id="tableSoal">
            <thead class="bg-primary text-white border-b border-primary/20">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                    <th class="px-2 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                    <th class="px-2 py-4 text-left text-xs font-semibold uppercase tracking-wider">Topik</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tingkat Kesulitan</th>
                    <th class="px-3 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse(($soals ?? collect()) as $soal)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">
                            <div class="flex flex-col items-start gap-1">
                                <span>{{ $soal->kode_soal }}</span>
                                @if(strtolower($soal->tipe_soal) === 'essay')
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold rounded uppercase whitespace-nowrap">Essay</span>
                                @else
                                    <span class="px-2 py-0.5 bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold rounded uppercase whitespace-nowrap">Pilihan Ganda</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-2 py-4 text-slate-600">{{ $soal->mataKuliah->nama ?? '-' }}</td>
                        <td class="px-2 py-4 text-slate-600">{!! \Illuminate\Support\Str::limit(strip_tags($soal->soal), 80, '...') !!}</td>
                        <td class="px-3 py-4">
                            @php $diff = strtolower($soal->kesulitan ?? ''); @endphp
                            @if($diff === 'easy')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @elseif($diff === 'advanced')
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ ucfirst($soal->kesulitan) }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-4">
                            @php $status = strtolower($soal->status ?? 'draft'); @endphp
                            @if($status === 'draft')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200"><i class="fas fa-file-alt mr-1 mt-0.5"></i> Draf</span>
                            @elseif($status === 'diajukan')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-primary/10 text-primary border border-primary/20"><i class="fas fa-paper-plane mr-1 mt-0.5"></i> Diajukan</span>
                            @elseif($status === 'disetujui')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="fas fa-check mr-1 mt-0.5"></i> Disetujui</span>
                            @elseif($status === 'revisi' || $status === 'ditolak')
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-600 border border-red-200"><i class="fas fa-times mr-1 mt-0.5"></i> Revisi/Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @include('banksoal::partials.dosen.soal-actions', ['soal' => $soal])
                        </td>
                    </tr>
                @empty
                    <tr class="no-results-message">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada soal di dalam bank soal.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($soals) && $soals->count() > 0)
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
            <span class="text-sm text-slate-600">
                Showing {{ $soals->firstItem() }} to {{ $soals->lastItem() }} of {{ $soals->total() }} questions
            </span>
            <div class="pagination-list">
                @php
                    $currentPage = $soals->currentPage();
                    $totalPages  = $soals->lastPage();
                    $pages = [];
                    if ($totalPages < 10) {
                        $start = max(1, $currentPage - 2);
                        $end   = min($totalPages, $start + 4);
                        $start = max(1, $end - 4);
                        $pages = range($start, $end);
                    } else {
                        $pages = [1, 2, 3, '...', $totalPages - 1, $totalPages];
                    }
                @endphp
                <a href="{{ $soals->appends(request()->query())->previousPageUrl() ?? '#' }}"
                   class="pagination-btn {{ $currentPage === 1 ? 'opacity-45 pointer-events-none' : '' }}">&lsaquo;</a>
                @foreach($pages as $page)
                    @if($page === '...')
                        <span class="pagination-ellipsis">...</span>
                    @else
                        <a href="{{ $soals->appends(request()->query())->url($page) }}"
                           class="pagination-btn {{ (int)$page === $currentPage ? 'active' : '' }}">{{ $page }}</a>
                    @endif
                @endforeach
                <a href="{{ $soals->appends(request()->query())->nextPageUrl() ?? '#' }}"
                   class="pagination-btn {{ $currentPage === $totalPages ? 'opacity-45 pointer-events-none' : '' }}">&rsaquo;</a>
            </div>
        </div>
    @endif
</div>

@can('banksoal.edit')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Ekstraksi Soal (Tarik Soal)</h2>
                <p class="text-sm text-slate-600 mt-1">Tarik kumpulan soal untuk digunakan pada ujian atau asesmen.</p>
            </div>
            <button type="button" onclick="openTarikModal()" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-xl px-4 py-2.5 font-medium transition-colors shadow-sm">
                <i class="fas fa-download"></i> Tarik Soal
            </button>
        </div>

        <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="p-6 border-b border-slate-200 flex flex-col sm:flex-row gap-3" onsubmit="window.showLoader();">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" data-search-tab="paket" list="packageSuggestions" autocomplete="off" name="searchPackages" value="{{ request('searchPackages') }}" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none" placeholder="Cari paket soal, kode mata kuliah, atau nama..." id="searchPackages">
                <datalist id="packageSuggestions">
                    @foreach(($mataKuliahDosen ?? collect()) as $mk)
                        <option value="{{ $mk->nama }}"></option>
                        <option value="{{ $mk->kode }}"></option>
                    @endforeach
                </datalist>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 hover:bg-primary/20 rounded-xl px-4 py-2.5 font-medium text-primary transition-colors">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request('searchPackages'))
                <a href="{{ route('banksoal.soal.dosen.index') }}" class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl px-4 py-2.5 font-medium transition-colors border border-red-200">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>

        <div class="overflow-x-auto" data-tab-panel="paket">
            <table class="w-full" id="tablePackages">
                <thead class="bg-primary text-white border-b border-primary/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Kode MK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Terkait CPL</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Terkait CPMK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse(($packages ?? collect()) as $pkg)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $pkg->kode }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $pkg->nama }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($pkg->str_cpls !== '-')
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-primary/10 text-primary whitespace-normal break-words max-w-[150px] leading-relaxed">{{ $pkg->str_cpls }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dipetakan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($pkg->str_cpmks !== '-')
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-emerald-50 text-emerald-700 whitespace-normal break-words max-w-[150px] leading-relaxed">{{ $pkg->str_cpmks }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dipetakan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-900">{{ $pkg->jumlah_soal }}</span> Set
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" data-package-action="lihat" data-mk-id="{{ $pkg->id }}" data-mk-nama="{{ e($pkg->nama) }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-primary rounded-lg transition-colors" title="Lihat Daftar Soal"><i class="fas fa-eye text-sm"></i></button>
                                    <button type="button" data-package-action="tarik" data-mk-id="{{ $pkg->id }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-emerald-600 rounded-lg transition-colors" title="Tarik Paket Soal"><i class="fas fa-download text-sm"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-results-message">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-box-open text-4xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Belum ada paket soal yang tersedia untuk ditarik.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($packages) && $packages->count() > 0)
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-sm text-slate-600">
                    Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of {{ $packages->total() }} packages
                </span>
                <div class="pagination-list">
                    @php
                        $pkgPage  = $packages->currentPage();
                        $pkgTotal = $packages->lastPage();
                        $pkgPages = [];
                        if ($pkgTotal < 10) {
                            $s = max(1, $pkgPage - 2);
                            $e = min($pkgTotal, $s + 4);
                            $s = max(1, $e - 4);
                            $pkgPages = range($s, $e);
                        } else {
                            $pkgPages = [1, 2, 3, '...', $pkgTotal - 1, $pkgTotal];
                        }
                    @endphp
                    <a href="{{ $packages->appends(request()->query())->previousPageUrl() ?? '#' }}"
                       class="pagination-btn {{ $pkgPage === 1 ? 'opacity-45 pointer-events-none' : '' }}">&lsaquo;</a>
                    @foreach($pkgPages as $p)
                        @if($p === '...')
                            <span class="pagination-ellipsis">...</span>
                        @else
                            <a href="{{ $packages->appends(request()->query())->url($p) }}"
                               class="pagination-btn {{ (int)$p === $pkgPage ? 'active' : '' }}">{{ $p }}</a>
                        @endif
                    @endforeach
                    <a href="{{ $packages->appends(request()->query())->nextPageUrl() ?? '#' }}"
                       class="pagination-btn {{ $pkgPage === $pkgTotal ? 'opacity-45 pointer-events-none' : '' }}">&rsaquo;</a>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden opacity-50">
        <div class="px-6 py-12 text-center text-slate-600">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-lock text-4xl text-slate-300 mb-3"></i>
                <p class="font-semibold text-lg">Fitur Ekstraksi Soal Terkunci</p>
                <p class="text-sm">Anda memerlukan izin <strong>Edit</strong> untuk melakukan penarikan soal.</p>
            </div>
        </div>
    </div>
@endcan

<!-- Tarik Soal Modal -->
<div id="tarikSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="tarikSoalModalContent" class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tarik Soal</h3>
                <p class="text-sm text-slate-500 mt-0.5">Atur parameter untuk mengekstrak soal</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeTarikModal()">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto w-full">
            <form action="{{ route('banksoal.soal.dosen.ekstrak') }}" method="POST" id="formTarikSoal">
                @csrf
                
                <!-- Mata Kuliah -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Mata Kuliah</label>
                    <div class="relative">
                        <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none py-2.5 pl-4 pr-10 shadow-sm appearance-none" name="mk_id" id="tarikMkId" required onchange="loadCplCpmk(this.value)">
                            <option value="">Pilih  Mata Kuliah</option>
                            @foreach($mataKuliahDosen as $mk)
                                <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Jenis Soal -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Jenis Soal</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Pilihan Ganda" class="w-4 h-4 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Pilihan Ganda</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="jenis_soal[]" value="Essay" class="w-4 h-4 border-slate-300 rounded">
                            <span class="text-sm font-medium text-slate-700">Essay</span>
                        </label>
                    </div>
                </div>

                <!-- CPL -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPL (Capaian Pembelajaran Lulusan)</label>
                    <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none" name="cpl_id[]" id="cplSelectElement" multiple placeholder="Pilih CPL">
                        <option value="">Pilih CPL</option>
                    </select>
                </div>

                <!-- CPMK -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">CPMK (Capaian Pembelajaran Mata Kuliah)</label>
                    <select class="w-full bg-white border border-slate-300 rounded-lg text-sm focus:outline-none" name="cpmk_id[]" id="cpmkSelectElement" multiple placeholder="Pilih CPMK">
                        <option value="">Pilih CPMK</option>
                    </select>
                </div>


            </form>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 rounded-b-2xl flex items-center justify-end gap-3 mt-auto">
            <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors" onclick="closeTarikModal()">
                Batal
            </button>
            <button type="submit" form="formTarikSoal" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                <i class="fas fa-check"></i> Proses Tarik Soal
            </button>
        </div>

    </div>
</div>

<script>
    const pageData = document.getElementById('banksoal-dosen-page-data');
    let mkData = [];

    try {
        if (pageData?.dataset.mkJson) {
            mkData = JSON.parse(atob(pageData.dataset.mkJson));
        }
    } catch (error) {
        console.error('Gagal memuat data mata kuliah dosen', error);
        mkData = [];
    }

    window.tarikSoalsData = [];
    window.currentTarikMkId = null;

    let isUpdatingFilter = false;

    function renderCplCpmk(selectedMk, validCplIds = null, validCpmkIds = null) {
        const cplSelect = document.getElementById('cplSelectElement');
        const cpmkSelect = document.getElementById('cpmkSelectElement');

        // Remember existing values
        const currentCpls = cplSelect.tomselect ? cplSelect.tomselect.getValue() : [];
        const currentCpmks = cpmkSelect.tomselect ? cpmkSelect.tomselect.getValue() : [];

        // Destroy existing TomSelect instance if it exists
        if (cplSelect.tomselect) cplSelect.tomselect.destroy();
        if (cpmkSelect.tomselect) cpmkSelect.tomselect.destroy();

        cplSelect.innerHTML = '<option value="">Pilih CPL</option>';
        cpmkSelect.innerHTML = '<option value="">Pilih CPMK</option>';

        if (selectedMk.all_cpls && selectedMk.all_cpls.length > 0) {
            selectedMk.all_cpls.forEach(cpl => {
                if (validCplIds === null || validCplIds.includes(cpl.id)) {
                    cplSelect.innerHTML += `<option value="${cpl.id}">${cpl.kode}</option>`;
                }
            });
        }
        if (selectedMk.all_cpmks && selectedMk.all_cpmks.length > 0) {
            selectedMk.all_cpmks.forEach(cpmk => {
                if (validCpmkIds === null || validCpmkIds.includes(cpmk.id)) {
                    cpmkSelect.innerHTML += `<option value="${cpmk.id}">${cpmk.kode}</option>`;
                }
            });
        }

        // Init TomSelect for multi-select
        new TomSelect(cplSelect, {
            plugins: { remove_button: { title: 'Hapus CPL' } },
            create: false,
            placeholder: 'Pilih CPL (bisa lebih dari 1)',
            searchField: ['text'],
            onChange: function() {
                if (!isUpdatingFilter) filterCplCpmk();
            }
        });
        
        new TomSelect(cpmkSelect, {
            plugins: { remove_button: { title: 'Hapus CPMK' } },
            create: false,
            placeholder: 'Pilih CPMK (bisa lebih dari 1)',
            searchField: ['text'],
            onChange: function() {
                if (!isUpdatingFilter) filterCplCpmk();
            }
        });

        // Restore values
        isUpdatingFilter = true;
        if (currentCpls.length) cplSelect.tomselect.setValue(currentCpls);
        if (currentCpmks.length) cpmkSelect.tomselect.setValue(currentCpmks);
        isUpdatingFilter = false;
    }

    function filterCplCpmk() {
        if (!window.currentTarikMkId || window.tarikSoalsData.length === 0) {
            return;
        }
        
        if (isUpdatingFilter) return;
        isUpdatingFilter = true;
        
        const cbs = Array.from(document.querySelectorAll('input[name="jenis_soal[]"]:checked'));
        const typesChecked = cbs.map(cb => {
            return cb.value === 'Pilihan Ganda' ? 'pilihan_ganda' : 'essay';
        });

        const selectedMk = mkData.find(mk => mk.id == window.currentTarikMkId);
        if (!selectedMk) {
            isUpdatingFilter = false;
            return;
        }

        const cplSelect = document.getElementById('cplSelectElement');
        const cpmkSelect = document.getElementById('cpmkSelectElement');
        
        const selectedCplIds = cplSelect && cplSelect.tomselect ? 
            (Array.isArray(cplSelect.tomselect.getValue()) ? cplSelect.tomselect.getValue() : [cplSelect.tomselect.getValue()]).filter(v => v).map(Number) : [];
            
        const selectedCpmkIds = cpmkSelect && cpmkSelect.tomselect ? 
            (Array.isArray(cpmkSelect.tomselect.getValue()) ? cpmkSelect.tomselect.getValue() : [cpmkSelect.tomselect.getValue()]).filter(v => v).map(Number) : [];

        // Base filter by Tipe Soal
        let filteredSoals = window.tarikSoalsData;
        if (typesChecked.length > 0) {
            filteredSoals = filteredSoals.filter(soal => typesChecked.includes(soal.tipe_soal));
        }

        // To determine available CPLs: filter by selected types & selected CPMKs
        const availableCplsFromSoal = filteredSoals
            .filter(soal => selectedCpmkIds.length === 0 || selectedCpmkIds.includes(soal.cpmk_id))
            .map(soal => soal.cpl_id)
            .filter(id => id !== null);

        // To determine available CPMKs: filter by selected types & selected CPLs
        const availableCpmksFromSoal = filteredSoals
            .filter(soal => selectedCplIds.length === 0 || selectedCplIds.includes(soal.cpl_id))
            .map(soal => soal.cpmk_id)
            .filter(id => id !== null);

        const uniqueCplIds = [...new Set(availableCplsFromSoal)];
        const uniqueCpmkIds = [...new Set(availableCpmksFromSoal)];
        
        renderCplCpmk(selectedMk, typesChecked.length === 0 && selectedCpmkIds.length === 0 ? null : uniqueCplIds, typesChecked.length === 0 && selectedCplIds.length === 0 ? null : uniqueCpmkIds);
        isUpdatingFilter = false;
    }

    function loadCplCpmk(mk_id) {
        window.currentTarikMkId = mk_id;
        const selectedMk = mkData.find(mk => mk.id == mk_id);
        
        if (!selectedMk) return;

        // Reset checkboxes state
        document.querySelectorAll('input[name="jenis_soal[]"]').forEach(cb => {
            cb.checked = false;
        });

        renderCplCpmk(selectedMk, null, null);

        // Fetch questions exactly for this MK to drive the filtering
        fetch(`/bank-soal/soal/dosen/get-by-mk/${mk_id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success && data.soals) {
                window.tarikSoalsData = data.soals;
            } else {
                window.tarikSoalsData = [];
            }

            filterCplCpmk();
        })
        .catch(() => {
            window.tarikSoalsData = [];
            renderCplCpmk(selectedMk, null, null);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[name="jenis_soal[]"]').forEach(cb => {
            cb.addEventListener('change', filterCplCpmk);
        });
    });

    function openTarikModal(mk_id = null) {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Timeout to trigger transition after display swap
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
        
        if (mk_id) {
            document.getElementById('tarikMkId').value = mk_id;
            loadCplCpmk(mk_id);
        }
    }

    function closeTarikModal() {
        const modal = document.getElementById('tarikSoalModal');
        const modalContent = document.getElementById('tarikSoalModalContent');
        
        // Start exit animation
        modal.classList.add('opacity-0');
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        
        // Hide elements after animation finishes
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    // Modal Lihat Soal
    function openLihatSoalModal(mk_id, mk_nama) {
        document.getElementById('lihatSoalSubtitle').innerText = 'Mata Kuliah: ' + mk_nama;
        const modal = document.getElementById('lihatSoalModal');
        const modalContent = document.getElementById('lihatSoalModalContent');
        const listDiv = document.getElementById('lihatSoalList');
        const loadDiv = document.getElementById('lihatSoalLoading');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);

        listDiv.classList.add('hidden');
        loadDiv.classList.remove('hidden');
        loadDiv.classList.add('flex');

        fetch(`/bank-soal/soal/dosen/get-by-mk/${mk_id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            loadDiv.classList.remove('flex');
            loadDiv.classList.add('hidden');
            listDiv.classList.remove('hidden');
            listDiv.innerHTML = '';

            if (data.success && data.soals.length > 0) {
                data.soals.forEach(soal => {
                    const fmtId = String(soal.id).padStart(3, '0');
                    let badges = '';
                    if (soal.cpl) {
                        badges += `<span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 text-[10px] font-bold rounded uppercase">${soal.cpl}</span>`;
                    }
                    if (soal.cpmk) {
                        badges += `<span class="px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold rounded uppercase">${soal.cpmk}</span>`;
                    }
                    
                    const tipeLabel = soal.tipe_soal === 'essay' ? 'Essay' : 'Pilihan Ganda';
                    const tipeColor = soal.tipe_soal === 'essay' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-primary/10 text-primary border-primary/20';
                    badges += `<span class="px-2 py-0.5 ${tipeColor} border text-[10px] font-bold rounded uppercase">${tipeLabel}</span>`;

                    listDiv.innerHTML += `
                        <div class="px-5 py-4 border-b border-slate-100 hover:bg-slate-50/50 transition-colors flex items-start cursor-default">
                            <div class="flex-shrink-0 w-10 h-10 bg-white border border-slate-200 shadow-sm rounded-lg flex items-center justify-center font-bold text-slate-800 mr-4 text-xs">
                                Q-${fmtId}
                            </div>
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex flex-wrap gap-2 items-center mb-1">
                                    ${badges}
                                </div>
                                <div class="text-sm text-slate-700 leading-relaxed prose prose-sm prose-slate max-w-none prose-img:max-h-48 prose-img:w-auto prose-img:rounded-md mt-2">
                                    ${soal.soal}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                listDiv.innerHTML = `
                    <div class="text-center py-12 text-slate-500 bg-white rounded-xl border border-slate-200 shadow-sm mx-5 mb-5">
                        <i class="fas fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                        Tidak ada butir soal di paket ini.
                    </div>`;
            }
        })
        .catch(error => {
            loadDiv.classList.add('hidden');
            loadDiv.classList.remove('flex');
            listDiv.classList.remove('hidden');
            listDiv.innerHTML = `<div class="text-center py-12 text-red-500 bg-white border border-slate-200 shadow-sm rounded-xl mx-5 mb-5"><i class="fas fa-exclamation-triangle text-3xl mb-3"></i><p class="text-sm font-medium">Gagal memuat soal.</p></div>`;
        });
    }

    function closeLihatSoalModal() {
        const modal = document.getElementById('lihatSoalModal');
        const modalContent = document.getElementById('lihatSoalModalContent');
        
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    // AJAX for Form Tarik Soal
    function handleTarikSoalSubmit(e, formEl = null) {
        if(e) e.preventDefault();
        
        const form = formEl || document.getElementById('formTarikSoal');
        const formData = new FormData(form);
        // Cari tombol submit, entah di dalam form atau di luar dengan atribut form="..."
        const submitBtn = document.querySelector('button[form="formTarikSoal"]') || form.querySelector('[type="submit"]');
        let originalText = '';
        
        if (submitBtn) {
            originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
        }
        window.showLoader();

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            window.hideLoader();
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            
            if (data.success) {
                closeTarikModal();
                // Pass extra param for modal filler
                data.mk_id = formData.get('mk_id');
                setTimeout(() => {
                    openReviewModal(data);
                }, 300);
            } else {
                alert(data.message || 'Terjadi kesalahan saat menarik soal.');
            }
        })
        .catch(error => {
            window.hideLoader();
            console.error('Error:', error);
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
            alert('Terjadi kesalahan tidak terduga pada server.');
        });
    }

    document.getElementById('formTarikSoal').addEventListener('submit', handleTarikSoalSubmit);
</script>

<!-- Lihat Soal Modal -->
<div id="lihatSoalModal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
    <div id="lihatSoalModalContent" class="relative w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 transform transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Kumpulan Butir Soal Terkait</h3>
                <p class="text-sm text-slate-500 mt-0.5" id="lihatSoalSubtitle">Mata Kuliah: ...</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-2 transition-colors" onclick="closeLihatSoalModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto bg-slate-50/50">
            <div id="lihatSoalLoading" class="hidden flex-col items-center justify-center py-16 tbl-loading">
                <div class="tbl-spinner mb-4"></div>
                <p class="text-sm font-medium text-slate-500">Memuat rincian soal...</p>
            </div>
            <div id="lihatSoalList" class="flex flex-col">
                <!-- Items go here -->
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-slate-200 bg-white rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeLihatSoalModal()" class="px-6 py-2 bg-slate-800 text-white font-medium hover:bg-slate-700 rounded-lg transition-colors">Tutup Jendela</button>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="importModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="if(event.target === this) closeImportModal()">
    <div id="importModalContent" class="relative w-full max-w-lg transform rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 translate-y-4">
        
        <form action="{{ route('banksoal.soal.dosen.import-csv') }}" method="POST" enctype="multipart/form-data" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
            @csrf
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Import Massal via Spreadsheet</h3>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Unggah file template XLS/CSV Bank Soal.</p>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <div class="mb-4 text-sm text-slate-600 bg-primary/10 border border-primary/20 rounded-xl p-4">
                    <p class="mb-2"><i class="fas fa-info-circle text-primary mr-1.5"></i> <strong>Langkah Import Baru:</strong></p>
                    <ol class="list-decimal ml-6 space-y-1">
                        <li>Gunakan template impor standar (SOAL pada kolom Jenis).</li>
                        <li>Pastikan baris jawaban berada persis di bawah baris soal tersebut.</li>
                        <li>Format yang didukung: <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.xls</code>, <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.xlsx</code>, <code class="bg-primary/20 px-1 py-0.5 rounded text-primary">.csv</code>.</li>
                    </ol>
                </div>

                <label for="csv_file" class="block text-sm font-semibold text-slate-700 mb-2">Unggah File Target (Excel/CSV)</label>
                <div class="relative group cursor-pointer">
                    <input type="file" name="csv_file" id="csv_file" accept=".csv, .txt, .xls, .xlsx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="document.getElementById('fileNameP').textContent = this.files[0]?.name || 'Pilih file excel/csv Anda';">
                    <div class="w-full flex-col flex items-center justify-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-8 text-center group-hover:bg-slate-100 group-hover:border-primary/20 transition-all">
                        <div class="w-12 h-12 mb-3 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <i class="fas fa-file-excel text-xl"></i>
                        </div>
                        <p id="fileNameP" class="text-sm font-medium text-slate-700">Pilih file atau seret file .xls/.xlsx/.csv ke sini</p>
                        <p class="text-xs text-slate-500 mt-1">Maksimal ukuran file: 5MB</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
                <button type="submit" onclick="if(this.closest('form').checkValidity()){ this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Memproses...'; this.classList.add('opacity-75'); }" class="px-5 py-2.5 bg-emerald-600 text-white font-semibold hover:bg-emerald-700 rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-emerald-500/20">
                    <i class="fas fa-upload mr-1.5"></i> Proses Import
                </button>
            </div>
            
        </form>
    </div>
</div>

<script>
    function toggleUploadDropdown() {
        const menu = document.getElementById('uploadDropdownMenu');
        const isClosed = menu.classList.contains('opacity-0');
        
        if (isClosed) {
            menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            menu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
        } else {
            closeUploadDropdown();
        }
    }

    function closeUploadDropdown() {
        const menu = document.getElementById('uploadDropdownMenu');
        if (menu && !menu.classList.contains('opacity-0')) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        }
    }

    // Tutup dropdown jika user klik/klik di luar area dropdown
    window.addEventListener('click', function(e) {
        const container = document.getElementById('uploadDropdownContainer');
        if (container && !container.contains(e.target)) {
            closeUploadDropdown();
        }
    });

    function openImportModal() {
        const modal = document.getElementById('importModal');
        const modalContent = document.getElementById('importModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        const modalContent = document.getElementById('importModalContent');
        
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // reset value input file
            document.getElementById('csv_file').value = '';
            document.getElementById('fileNameP').textContent = 'Pilih file atau seret file .csv ke sini';
        }, 300);
    }

    // Load CPL based on chosen MK
    document.getElementById('import_mk_id')?.addEventListener('change', function() {
        const cplSelect = document.getElementById('import_cpl_id');
        const mkId = this.value;
        cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';

        if(mkId) {
            fetch(`{{ route('banksoal.rps.dosen.cpl', '') }}/${mkId}`)
                .then(r => r.json())
                .then(data => {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                    data.forEach(c => cplSelect.innerHTML += `<option value="${c.id}">${c.kode} - ${c.deskripsi ? c.deskripsi.substring(0, 60) : ''}...</option>`);
                })
                .catch(error => {
                    console.error('Error fetching CPL:', error);
                    cplSelect.innerHTML = '<option value="">Gagal memuat CPL</option>';
                });
        } else {
            cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
        }
    });
</script>


<!-- Modal Ajukan Soal -->
<div id="ajukanModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-[100] justify-center items-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div id="ajukanModalContent" class="relative p-4 w-full max-w-lg max-h-full transform transition-all duration-300 scale-95 opacity-0 translate-y-4">
        <form action="{{ route('banksoal.soal.dosen.ajukan-semua') }}" method="POST" class="relative bg-white rounded-2xl shadow-xl border border-slate-100 flex flex-col max-h-[90vh]" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
            @csrf
            
            <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Ajukan Soal (Draf)</h3>
                    <p class="text-xs text-slate-500 mt-1">Pilih Mata Kuliah untuk mengajukan semua soal draf ke GPM.</p>
                </div>
                <button type="button" onclick="closeAjukanModal()" class="text-slate-400 bg-white hover:bg-slate-100 hover:text-slate-900 rounded-xl p-2 text-sm drop-shadow-sm inline-flex justify-center items-center transition-all focus:ring-2 focus:ring-slate-100">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
                <div class="mb-5">
                    <label for="ajukan_mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Pilih Mata Kuliah</label>
                    <select name="mk_id" id="ajukan_mk_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <option value="all" class="font-semibold text-emerald-700">-- Ajukan Semua Mata Kuliah --</option>
                        @foreach($mataKuliahDosen as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode }} - {{ $mk->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <p class="text-xs text-amber-700 leading-relaxed">Semua soal pada mata kuliah terpilih yang masih berstatus <strong>Draf</strong> akan diubah menjadi <strong>Diajukan</strong> dan dikirimkan ke GPM untuk divalidasi. Apakah Anda yakin?</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/80 rounded-b-2xl flex items-center justify-end gap-3 mt-auto">
                <button type="button" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors" onclick="closeAjukanModal()">
                    Batal
                </button>
                <button type="submit" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin mr-2\'></i>Memproses...'; this.classList.add('opacity-75');" class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors shadow-sm">
                    <i class="fas fa-paper-plane"></i> Ya, Ajukan Pilihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAjukanModal() {
        const modal = document.getElementById('ajukanModal');
        const modalContent = document.getElementById('ajukanModalContent');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
            modalContent.classList.add('opacity-100', 'scale-100', 'translate-y-0');
        }, 10);
    }

    function closeAjukanModal() {
        const modal = document.getElementById('ajukanModal');
        const modalContent = document.getElementById('ajukanModalContent');
        
        modalContent.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'scale-95', 'translate-y-4');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    document.addEventListener('DOMContentLoaded', function () {
        function debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function searchTable(searchInput, tabId) {
            const searchValue = searchInput.value.toLowerCase().trim();
            const tabContent = document.querySelector(`[data-tab-panel="${tabId}"]`);
            if (!tabContent) return;

            const rows = tabContent.querySelectorAll('table tbody tr:not(.no-results-message)');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchValue) || searchValue === '') {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            const noResultsMsg = tabContent.querySelector('.no-results-message');
            if (noResultsMsg) {
                if (visibleCount === 0) {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            }
            
            // Prevent actual form submission to retain real-time UI mapping
            // But if user hits enter, it will trigger the server-side pagination flow.
        }

        document.querySelectorAll('[data-package-action]').forEach((button) => {
            button.addEventListener('click', function () {
                const action = this.dataset.packageAction;
                const mkId = this.dataset.mkId;
                const mkNama = this.dataset.mkNama || '';

                if (action === 'lihat') {
                    openLihatSoalModal(mkId, mkNama);
                }

                if (action === 'tarik') {
                    openTarikModal(mkId);
                }
            });
        });

        const searchInputs = document.querySelectorAll('[data-search-tab]');
        searchInputs.forEach((input) => {
            const tabId = input.getAttribute('data-search-tab');
            input.addEventListener('input', debounce(function () {
                // Untuk table yang di-paginasi server, kita butuh submit form otomatis.
                // Dengan debouncing 500ms, UX akan terasa seperti live-search 
                // tetapi akan melakukan fetch ke seluruh data di server.
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    searchTable(this, tabId); // Fallback ke client-side search
                }
            }, 600));
        });

        // Sort change listener
        const sortBy = document.getElementById('sortBy');
        const filterForm = document.getElementById('filterForm');
        sortBy.addEventListener('change', function() {
            filterForm.submit();
        });
    });
</script>

@include('banksoal::pages.bank-soal.Dosen.review-modal')

</x-banksoal::layouts.dosen-admin>
