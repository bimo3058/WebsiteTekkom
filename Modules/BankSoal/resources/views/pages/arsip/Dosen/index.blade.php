<x-banksoal::layouts.dosen-admin :bank-soal="true">
    @include('banksoal::pages.arsip.Dosen._styles')
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Arsip Soal</span>
    @endsection


    <x-banksoal::notification.alerts />

    <x-banksoal::ui.bank-soal-page class="bs-archive-page">
    <x-slot:header>
    <x-banksoal::ui.page-header title="Arsip Soal Dosen" subtitle="Kelola riwayat penarikan dan arsip final dokumen ujian Anda.">
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.prevent.stop="open = false; $refs.addArchiveTrigger.focus()">
                    <button @click="open = !open" type="button" class="bs-archive-primary bs-archive-add-trigger" x-ref="addArchiveTrigger" :aria-expanded="open" aria-controls="archiveAddMenu">
                        <i class="fas fa-plus" aria-hidden="true"></i> Tambah Arsip
                        <i class="fas fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div id="archiveAddMenu" x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bs-dropdown-menu bs-archive-add-menu absolute right-0 origin-top-right z-50">
                        <a href="{{ route('banksoal.arsip.dosen.create-pdf') }}" class="bs-archive-add-option">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                            <span>Upload PDF</span>
                        </a>
                        <a href="{{ route('banksoal.arsip.dosen.create-csv') }}" class="bs-archive-add-option">
                            <i class="fas fa-file-excel" aria-hidden="true"></i>
                            <span>Import CSV/Excel</span>
                        </a>
                    </div>
                </div>
            </div>
        </x-slot:actions>
    </x-banksoal::ui.page-header>
    </x-slot:header>

    @include('banksoal::pages.arsip.Dosen._stats')

    <div class="bs-archive-sections">
        <div class="bs-section bs-archive-final" x-data="{ expandedGroups: [] }">
            <div class="bs-archive-section-header">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Daftar Arsip Final</h3>
                        <p class="mt-1 text-sm text-slate-500">Kelola arsip soal final berdasarkan mata kuliah.</p>
                    </div>
                    <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $stats['total_arsip'] }} Arsip</span>
                </div>
            </div>

            <div class="bs-archive-filter">
                <form action="{{ route('banksoal.arsip.dosen.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full" id="filterForm">
                            <div class="relative w-full md:w-96">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari nama arsip atau MK..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none">
                            </div>

                            <div class="relative" x-data="{ filterOpen: false }" @click.outside="filterOpen = false" @keydown.escape.prevent.stop="filterOpen = false; $refs.archiveFilterTrigger.focus()">
                                <button type="button" class="bs-dropdown-trigger" :class="{ 'is-open': filterOpen }" @click="filterOpen = !filterOpen" x-ref="archiveFilterTrigger" :aria-expanded="filterOpen" aria-controls="archiveFilterMenu">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" /></svg>
                                    <span>Filter</span>
                                    @if(request('years') || request('semesters'))
                                        <span class="bs-archive-filter-dot" aria-label="Filter aktif"></span>
                                    @endif
                                </button>
                                <div id="archiveFilterMenu" x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bs-dropdown-menu bs-archive-filter-menu absolute right-0 origin-top-right z-50">
                                <div>
                                    <div class="bs-dropdown-label">Tahun Ajaran</div>
                                    <div class="bs-archive-filter-years">
                                        @foreach($availableYears as $year)
                                        <label class="bs-archive-filter-option">
                                            <input type="checkbox" name="years[]" value="{{ $year }}" {{ in_array($year, (array)request('years')) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary transition-all">
                                            <span>{{ $year }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="bs-dropdown-divider"></div>
                                <div>
                                    <div class="bs-dropdown-label">Semester</div>
                                    <div>
                                        @foreach(['Ganjil', 'Genap'] as $sem)
                                        <label class="bs-archive-filter-option">
                                            <input type="checkbox" name="semesters[]" value="{{ $sem }}" {{ in_array($sem, (array)request('semesters')) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary transition-all">
                                            <span>{{ $sem }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="bs-archive-filter-footer">
                                    <a href="{{ route('banksoal.arsip.dosen.index') }}" class="bs-archive-secondary">Reset</a>
                                    <button type="submit" form="filterForm" @click="filterOpen = false" class="bs-archive-primary">Terapkan</button>
                                </div>
                                </div>
                            </div>

                            @if($filters['search'] || request('years') || request('semesters'))
                            <a href="{{ route('banksoal.arsip.dosen.index') }}" class="text-rose-500 hover:text-rose-700 text-xs font-bold underline px-2">Reset</a>
                            @endif
                </form>
            </div>

            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="bs-table bs-archive-table">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-y border-slate-200">
                                <th class="w-12 px-6 py-4"></th>
                                <th class="px-4 py-4">Mata Kuliah</th>
                                <th class="px-6 py-4">Jumlah Arsip</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <?php $arsipGroups = $arsipPaginated->groupBy('mk_id'); ?>
                            <?php if($arsipGroups->isNotEmpty()): ?>
                                <?php foreach($arsipGroups as $mkId => $items): ?>
                                <?php $first = $items->first(); ?>
                                <tr class="hover:bg-slate-50/50 cursor-pointer transition-colors group" @click="expandedGroups.includes({{ $mkId }}) ? expandedGroups = expandedGroups.filter(i => i !== {{ $mkId }}) : expandedGroups.push({{ $mkId }})">
                                    <td class="px-8 py-5 text-center">
                                        <i class="fas fa-chevron-right text-slate-300 transition-transform duration-300" :class="expandedGroups.includes({{ $mkId }}) ? 'rotate-90 text-primary' : ''"></i>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-book text-sm"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-900 text-base">{{ $first->mataKuliah->nama }}</span>
                                                <span class="text-xs text-slate-400 font-medium tracking-wide uppercase">{{ $first->mataKuliah->kode }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full bg-primary/5 text-primary text-xs font-bold">{{ $items->count() }} Versi</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-[10px] font-bold text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                                            <i class="fas fa-check-circle text-[8px]"></i> Aktif
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="flex -space-x-2">
                                                <?php foreach($items->unique('dosen_id')->take(3) as $a): ?>
                                                    <div class="h-8 w-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 overflow-hidden shadow-sm" title="{{ $a->dosen->name ?? 'Dosen' }}">
                                                        {{ substr($a->dosen->name ?? 'D', 0, 1) }}
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button
                                                type="button"
                                                @click.stop="expandedGroups.includes({{ $mkId }}) ? expandedGroups = expandedGroups.filter(i => i !== {{ $mkId }}) : expandedGroups.push({{ $mkId }})"
                                                class="rounded-lg p-2 text-slate-300 transition-colors hover:bg-slate-100 hover:text-slate-600"
                                                aria-label="Lihat aksi arsip {{ $first->mataKuliah->nama }}"
                                            >
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <?php foreach($items as $arsip): ?>
                                <?php
                                    preg_match('/\((.*?)\)/', $arsip->nama_arsip, $matches);
                                    $categoryAbbr = $matches[1] ?? (strpos($arsip->nama_arsip, 'UTS') !== false ? 'UTS' : (strpos($arsip->nama_arsip, 'UAS') !== false ? 'UAS' : 'Arsip'));
                                ?>
                                <tr x-show="expandedGroups.includes({{ $mkId }})" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-50/30">
                                    <td class="px-8 py-0"></td>
                                    <td colspan="4" class="px-4 py-3">
                                        <div class="bs-archive-version">
                                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                                <div class="flex flex-col">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pengarsip</p>
                                                    <div class="flex items-center gap-2">
                                                        <div class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-inner">
                                                            {{ substr($arsip->dosen->name ?? 'D', 0, 1) }}
                                                        </div>
                                                        <p class="text-sm font-bold text-slate-800 truncate" title="{{ $arsip->dosen->name ?? '-' }}">
                                                            {{ $arsip->dosen->name ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kategori</p>
                                                    <span class="inline-flex w-fit px-2.5 py-0.5 rounded-lg bg-primary/5 text-primary text-[10px] font-bold border border-primary/10">
                                                        {{ $categoryAbbr }}
                                                    </span>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Periode</p>
                                                    <p class="text-sm text-slate-700 font-medium">{{ $arsip->tahun_akademik }} - <span class="text-primary text-xs">{{ $arsip->semester }}</span></p>
                                                </div>
                                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Statistik</p>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-list-ol text-[10px] text-slate-300"></i> {{ $arsip->jumlah_soal }}</span>
                                                        <span class="text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-star text-[10px] text-slate-300"></i> {{ number_format($arsip->total_bobot, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                                    <button type="button" @click.stop="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-primary transition-all">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div x-show="menuOpen" @click.away="menuOpen = false" x-cloak class="bs-dropdown-menu bs-archive-action-menu absolute right-0 z-50">
                                                        <a href="{{ route('banksoal.arsip.dosen.show', $arsip->id) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary transition-all">
                                                            <i class="fas fa-external-link-alt w-4"></i> Buka Detail
                                                        </a>
                                                        <form action="{{ route('banksoal.arsip.dosen.destroy', $arsip->id) }}" method="POST" onsubmit="if(confirm('Hapus arsip ini?')){ window.showLoader(); return true; } else { return false; }" class="block">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                                <i class="fas fa-trash-alt w-4"></i> Hapus Arsip
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center text-slate-500">
                                    Tidak ada data arsip yang ditemukan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="bs-archive-pagination">
                    {{ $arsipPaginated->appends(request()->all())->links('banksoal::components.ui.laravel-pagination') }}
                </div>
            </div>
        </div>

        @if($penarikanPending->isNotEmpty())
        <div class="bs-section bs-archive-pending">
            <div class="bs-archive-section-header flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </span>
                    <h3 class="font-bold text-slate-900">Riwayat Penarikan (Pending)</h3>
                </div>
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">{{ $penarikanPending->count() }} Item</span>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="bs-table bs-archive-table">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">
                                <th class="px-8 py-4">Detail Penarikan</th>
                                <th class="px-8 py-4">Mata Kuliah</th>
                                <th class="px-8 py-4">Waktu</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($penarikanPending as $penarikan)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 group-hover:text-primary transition-colors">{{ $penarikan->nama_ekstraksi }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-[10px] font-bold text-amber-600 border border-amber-100 uppercase">{{ $penarikan->tipe_ujian }}</span>
                                            @if($penarikan->metode_ujian === 'offline')
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold text-slate-600 border border-slate-200 uppercase">Offline</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-700">{{ $penarikan->mataKuliah->nama }}</span>
                                        <span class="text-xs text-slate-400">{{ $penarikan->mataKuliah->kode }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-sm text-slate-600">{{ $penarikan->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2 relative" x-data="{ menuOpen: false }">
                                        <button type="button" @click.stop="menuOpen = !menuOpen" class="p-2 rounded-xl border border-slate-200 text-slate-400 hover:text-primary transition-all">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="menuOpen" @click.away="menuOpen = false" x-cloak class="bs-dropdown-menu bs-archive-action-menu absolute right-0 z-50">
                                            <a href="{{ route('banksoal.arsip.dosen.penarikan.edit', $penarikan->id) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-primary hover:bg-slate-50 transition-all">
                                                <i class="fas fa-file-export w-4"></i> Konversi
                                            </a>
                                            <form action="{{ route('banksoal.arsip.dosen.penarikan.destroy', $penarikan->id) }}" method="POST" onsubmit="if(confirm('Hapus riwayat penarikan ini?')){ window.showLoader(); return true; } else { return false; }" class="block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-xs font-bold text-rose-500 hover:bg-rose-50 transition-all">
                                                    <i class="fas fa-trash-alt w-4"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    </x-banksoal::ui.bank-soal-page>

    <script>
        document.getElementById('filterForm').addEventListener('submit', function() {
            window.showLoader();
        });
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function() {
                window.showLoader();
            });
        });
    </script>
</x-banksoal::layouts.dosen-admin>
