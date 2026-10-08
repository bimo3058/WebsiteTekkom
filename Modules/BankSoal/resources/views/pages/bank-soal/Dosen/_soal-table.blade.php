<div class="bs-section">
    <div class="bs-section-heading">
        <h2 class="text-lg font-semibold text-slate-900">Daftar Soal</h2>
    </div>

    <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="bs-toolbar" id="filterForm" x-ref="sortForm" onsubmit="window.showLoader();">
        <div class="relative bs-search">
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

        <div class="relative flex-shrink-0" x-data="{
            sortOpen: false,
            selectedSort: @js(request('sort', 'terbaru')),
            sortLabels: {
                terbaru: 'Terbaru',
                terlama: 'Terlama',
                'nama-asc': 'Nama A-Z',
                'nama-desc': 'Nama Z-A'
            },
            chooseSort(value) {
                this.selectedSort = value;
                this.sortOpen = false;
                this.$nextTick(() => document.getElementById('filterForm')?.submit());
            }
        }" @click.away="sortOpen = false">
            <input type="hidden" name="sort" x-model="selectedSort">
            <button type="button" @click="sortOpen = !sortOpen"
                    class="bs-dropdown-trigger"
                    :class="{ 'is-open': sortOpen }"
                    :aria-expanded="sortOpen">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M3 6h18M3 12h12M3 18h6M19 15v6m0 0l-2-2m2 2l2-2"/>
                </svg>
                <span>Sort:</span>
                <span x-text="sortLabels[selectedSort] || 'Terbaru'"></span>
                <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                   :class="sortOpen ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="sortOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                 class="bs-dropdown-menu absolute right-0 origin-top-right z-50">
                <p class="bs-dropdown-label">Urutkan soal</p>
                <template x-for="(label, value) in sortLabels" :key="value">
                    <button type="button" @click="chooseSort(value)"
                            class="bs-dropdown-item"
                            :class="{ 'is-selected': selectedSort === value }">
                        <span x-text="label"></span>
                        <i class="fas fa-check text-xs text-primary" x-show="selectedSort === value"></i>
                    </button>
                </template>
            </div>
        </div>

        <div class="bs-status-filter flex items-center gap-2">
            <x-banksoal::ui.filter-panel formId="filterForm" buttonRadius="rounded-lg" :hasActiveFilter="request('status') ? true : false" resetRoute="{{ route('banksoal.soal.dosen.index') }}" applyLabel="Terapkan">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Status Soal</label>
                    <div class="space-y-2">
                        @foreach([
                            'draft' => 'Draft',
                            'diajukan' => 'Diajukan',
                            'disetujui' => 'Disetujui',
                            'revisi' => 'Perlu Revisi'
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
        <table class="bs-table" id="tableSoal">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell px-6">ID</th>
                    <th class="table-header-cell px-2">Mata Kuliah</th>
                    <th class="table-header-cell px-2">Topik</th>
                    <th class="table-header-cell px-3">Tingkat Kesulitan</th>
                    <th class="table-header-cell px-3">Status</th>
                    <th class="table-header-cell px-6">Tindakan</th>
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
                                @elseif(strtolower($soal->tipe_soal) === 'take_home')
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded uppercase whitespace-nowrap">Take-Home</span>
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
        <div class="bs-pagination">
            <span class="text-sm text-slate-600">
                Menampilkan {{ $soals->firstItem() }} &ndash; {{ $soals->lastItem() }} dari {{ $soals->total() }} soal
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
