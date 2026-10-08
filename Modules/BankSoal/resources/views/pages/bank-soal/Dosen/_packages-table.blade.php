@can('banksoal.edit')
    <div class="bs-section" id="packagesSection">
        <div class="bs-section-heading">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Ekstraksi Soal (Tarik Soal)</h2>
                <p class="text-sm text-slate-600 mt-1">Tarik kumpulan soal untuk digunakan pada ujian atau asesmen.</p>
            </div>
            <button type="button" onclick="openTarikModal()" class="dosen-management-btn dosen-management-btn-success">
                <i class="fas fa-download"></i> Tarik Soal
            </button>
        </div>

        <form action="{{ route('banksoal.soal.dosen.index') }}" method="GET" class="bs-toolbar" id="packageFilterForm">
            <div class="relative bs-search">
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
            <button type="submit" class="dosen-management-btn">
                <i class="fas fa-filter text-slate-500"></i> Filter
            </button>
                <button type="button" id="resetPackageFilter" @if(!request('searchPackages')) hidden @endif class="dosen-management-btn border-rose-200 bg-rose-50 text-rose-600 hover:border-rose-300 hover:bg-rose-100">
                    <i class="fas fa-times"></i> Reset
                </button>
        </form>

        <p id="packageFilterStatus" class="px-4 py-2 text-xs text-slate-600" role="status" aria-live="polite" hidden></p>
        <div id="packageResults" aria-busy="false">
        <div class="overflow-x-auto" data-tab-panel="paket">
            <table class="bs-table" id="tablePackages">
                <thead class="table-header">
                    <tr>
                        <th class="table-header-cell">Kode MK</th>
                        <th class="table-header-cell">Mata Kuliah</th>
                        <th class="table-header-cell">Terkait CPL</th>
                        <th class="table-header-cell">Terkait CPMK</th>
                        <th class="table-header-cell">Jumlah Soal</th>
                        <th class="table-header-cell">Tindakan</th>
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
                                <x-ui.action-menu align="right">
                                    <button type="button" data-package-action="lihat" data-mk-id="{{ $pkg->id }}" data-mk-nama="{{ e($pkg->nama) }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium text-slate-700 hover:text-primary hover:bg-slate-100 transition-colors bg-transparent border-0 text-left cursor-pointer">
                                        <i class="fas fa-eye w-4 text-center"></i>
                                        <span>Lihat Daftar Soal</span>
                                    </button>
                                    <button type="button" data-package-action="tarik" data-mk-id="{{ $pkg->id }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors bg-transparent border-0 text-left cursor-pointer">
                                        <i class="fas fa-download w-4 text-center"></i>
                                        <span>Tarik Paket Soal</span>
                                    </button>
                                </x-ui.action-menu>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-results-message">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-box-open text-4xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">{{ request('searchPackages') ? 'Tidak ada paket soal yang cocok dengan pencarian.' : 'Belum ada paket soal yang tersedia untuk ditarik.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($packages) && $packages->count() > 0)
            <div class="bs-pagination">
                <span class="text-sm text-slate-600">
                    Menampilkan {{ $packages->firstItem() }} &ndash; {{ $packages->lastItem() }} dari {{ $packages->total() }} paket
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
    </div>
@else
    <div class="bs-section opacity-50">
        <div class="px-6 py-12 text-center text-slate-600">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-lock text-4xl text-slate-300 mb-3"></i>
                <p class="font-semibold text-lg">Fitur Ekstraksi Soal Terkunci</p>
                <p class="text-sm">Anda memerlukan izin <strong>Edit</strong> untuk melakukan penarikan soal.</p>
            </div>
        </div>
    </div>
@endcan
