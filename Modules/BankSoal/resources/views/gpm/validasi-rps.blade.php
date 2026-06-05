<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Validasi RPS</span>
    @endsection
    <style>
        /* Animasi untuk background gelap (fade in) */
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Animasi untuk kotak modal (pop up dari bawah/kecil ke ukuran asli) */
        @keyframes modalPopUp {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .animate-backdrop {
            animation: modalFadeIn 0.25s ease-out forwards;
        }

        .animate-popup {
            animation: modalPopUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .gpm-rps-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.75rem;
            border: 1px solid #93c5fd;
            background: #eff6ff;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1d4ed8;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .gpm-rps-action-btn:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .gpm-rps-action-btn-lg {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }

        .gpm-rps-btn-primary {
            background: rgb(11, 38, 110) !important;
            border-color: rgb(11, 38, 110) !important;
            color: white !important;
        }

        .gpm-rps-btn-primary:hover {
            background: rgb(9, 31, 90) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>

    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi RPS" subtitle="Pantau riwayat dokumen RPS yang telah direview">
        <x-slot:actions>
            <a href="{{ route('banksoal.rps.gpm.periode-rps.create') }}" class="gpm-rps-action-btn gpm-rps-action-btn-lg gpm-rps-btn-primary">
                <i class="fas fa-calendar-alt"></i> Atur Periode Pengajuan
            </a>
        </x-slot:actions>
    </x-banksoal::ui.page-header>

    @if($activePeriode)
        <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 border-l-4 {{ $isPeriodeRunning ? 'border-emerald-500' : 'border-slate-400' }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $isPeriodeRunning ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-600' }}">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $activePeriode->judul }}</p>
                        <p class="text-xs text-slate-500">Tenggat: {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M Y') }} s.d. {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($isPeriodeRunning)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <i class="fas fa-circle mr-2 text-[8px]"></i> Sesi Dibuka
                        </span>
                        <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" data-modal-open="modalCloseSession">
                            <i class="fas fa-power-off"></i> Matikan Sesi
                        </button>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            <i class="fas fa-times-circle mr-2"></i> Sesi Berakhir
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 rounded-2xl border-l-4 border-amber-400 bg-amber-50 p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-900">
                            @if($inactivePeriodes->count() > 0)
                                Tidak ada sesi yang aktif
                            @else
                                Belum ada jadwal pengajuan
                            @endif
                        </p>
                        <p class="text-xs text-amber-800">
                            @if($inactivePeriodes->count() > 0)
                                Pilih periode di bawah untuk mengaktifkan sesi
                            @else
                                Tidak ada sesi pengajuan RPS yang ditambahkan saat ini
                            @endif
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    <i class="fas fa-exclamation-circle mr-2"></i> Belum Aktif
                </span>
            </div>
        </div>

        @if($inactivePeriodes->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Periode Tersedia</h3>
                <div class="space-y-3">
                    @foreach($inactivePeriodes as $periode)
                        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $periode->judul }}</p>
                                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d M Y H:i') }} s.d. {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            </div>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10" data-modal-open="modalOpenSession" data-periode-id="{{ $periode->id }}" data-periode-judul="{{ $periode->judul }}" onclick="setPeriodeData(this)">
                                <i class="fas fa-power-off"></i> Nyalakan Sesi
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    <div data-tabs>
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-6 text-sm font-semibold">
                <button type="button" class="pb-2 border-b-2 border-primary text-primary" data-tab-target="menunggu" data-tab-active>
                    Menunggu Validasi
                    <span class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary border border-primary/20">{{ $rpsDiajukan->total() }}</span>
                </button>
                <button type="button" class="pb-2 border-b-2 border-transparent text-slate-500" data-tab-target="revisi">
                    Menunggu Revisi
                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $rpsRevisi->total() }}</span>
                </button>
                <button type="button" class="pb-2 border-b-2 border-transparent text-slate-500" data-tab-target="disetujui">
                    Disetujui
                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $rpsDisetujui->total() }}</span>
                </button>
            </div>
        </div>

        <div data-tab-panel="menunggu">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                 x-data="{
                     searchQuery: '',
                     selectedSemester: '',
                     selectedTahun: '',
                     filterOpen: false,
                     applyFilters() {
                         const rows = document.querySelectorAll('#table-menunggu tbody tr:not(.no-results-message)');
                         let visibleCount = 0;
                         rows.forEach(row => {
                             const text = row.textContent.toLowerCase();
                             const matchesSearch = text.includes(this.searchQuery.toLowerCase().trim());
                             const matchesSemester = this.selectedSemester === '' || text.includes('semester ' + this.selectedSemester.toLowerCase());
                             const matchesTahun = this.selectedTahun === '' || text.includes(this.selectedTahun.toLowerCase());
                             
                             if (matchesSearch && matchesSemester && matchesTahun) {
                                 row.classList.remove('hidden');
                                 visibleCount++;
                             } else {
                                 row.classList.add('hidden');
                             }
                         });
                         const noResults = document.querySelector('#table-menunggu .no-results-message');
                         if (noResults) {
                             if (visibleCount === 0) noResults.classList.remove('hidden');
                             else noResults.classList.add('hidden');
                         }
                     }
                 }">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="applyFilters()" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none" placeholder="Cari mata kuliah atau dosen...">
                </div>

                <div class="relative" @click.away="filterOpen = false">
                    <button @click="filterOpen = !filterOpen" type="button"
                        class="flex items-center gap-2 px-5 py-2.5 bg-white border rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm"
                        :class="selectedSemester || selectedTahun ? 'border-primary text-primary bg-primary/5' : 'border-slate-200'">
                        <svg class="w-4 h-4" :class="selectedSemester || selectedTahun ? 'text-primary' : 'text-slate-400'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filter
                        <template x-if="selectedSemester || selectedTahun">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                        </template>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="filterOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Semester</label>
                            <div class="space-y-2">
                                <select x-model="selectedSemester" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                            <div class="space-y-2">
                                <select x-model="selectedTahun" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                        $yearsList = [];
                                        for ($i = -3; $i <= 3; $i++) {
                                            $y1 = $currentYear + $i;
                                            $y2 = $y1 + 1;
                                            $yearsList[] = "$y1/$y2";
                                        }
                                    @endphp
                                    @foreach($yearsList as $yr)
                                        <option value="{{ $yr }}">{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="selectedSemester = ''; selectedTahun = ''; searchQuery = ''; applyFilters(); filterOpen = false;"
                                class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
                                Reset
                            </button>
                            <button type="button" @click="filterOpen = false"
                                class="flex-1 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:opacity-90 shadow-md shadow-primary/20 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="table-menunggu">
                        <thead class="bg-primary text-white border-b border-primary/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Diajukan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsDiajukan as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">Menunggu</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-comment-dots"></i> Review Sekarang
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada RPS yang menunggu validasi</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsDiajukan->count() }} dari {{ $rpsDiajukan->total() }} entri</span>
                {{ $rpsDiajukan->links('banksoal::components.ui.laravel-pagination') }}
            </div>
        </div>

        <div class="hidden" data-tab-panel="revisi">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                 x-data="{
                     searchQuery: '',
                     selectedSemester: '',
                     selectedTahun: '',
                     filterOpen: false,
                     applyFilters() {
                         const rows = document.querySelectorAll('#table-revisi tbody tr:not(.no-results-message)');
                         let visibleCount = 0;
                         rows.forEach(row => {
                             const text = row.textContent.toLowerCase();
                             const matchesSearch = text.includes(this.searchQuery.toLowerCase().trim());
                             const matchesSemester = this.selectedSemester === '' || text.includes('semester ' + this.selectedSemester.toLowerCase());
                             const matchesTahun = this.selectedTahun === '' || text.includes(this.selectedTahun.toLowerCase());
                             
                             if (matchesSearch && matchesSemester && matchesTahun) {
                                 row.classList.remove('hidden');
                                 visibleCount++;
                             } else {
                                 row.classList.add('hidden');
                             }
                         });
                         const noResults = document.querySelector('#table-revisi .no-results-message');
                         if (noResults) {
                             if (visibleCount === 0) noResults.classList.remove('hidden');
                             else noResults.classList.add('hidden');
                         }
                     }
                 }">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="applyFilters()" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none" placeholder="Cari mata kuliah atau dosen...">
                </div>

                <div class="relative" @click.away="filterOpen = false">
                    <button @click="filterOpen = !filterOpen" type="button"
                        class="flex items-center gap-2 px-5 py-2.5 bg-white border rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm"
                        :class="selectedSemester || selectedTahun ? 'border-primary text-primary bg-primary/5' : 'border-slate-200'">
                        <svg class="w-4 h-4" :class="selectedSemester || selectedTahun ? 'text-primary' : 'text-slate-400'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filter
                        <template x-if="selectedSemester || selectedTahun">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                        </template>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="filterOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Semester</label>
                            <div class="space-y-2">
                                <select x-model="selectedSemester" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                            <div class="space-y-2">
                                <select x-model="selectedTahun" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                        $yearsList = [];
                                        for ($i = -3; $i <= 3; $i++) {
                                            $y1 = $currentYear + $i;
                                            $y2 = $y1 + 1;
                                            $yearsList[] = "$y1/$y2";
                                        }
                                    @endphp
                                    @foreach($yearsList as $yr)
                                        <option value="{{ $yr }}">{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="selectedSemester = ''; selectedTahun = ''; searchQuery = ''; applyFilters(); filterOpen = false;"
                                class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
                                Reset
                            </button>
                            <button type="button" @click="filterOpen = false"
                                class="flex-1 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:opacity-90 shadow-md shadow-primary/20 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="table-revisi">
                        <thead class="bg-primary text-white border-b border-primary/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Review</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsRevisi as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->updated_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-700 border border-red-200">Revisi</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.revisi', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-edit"></i> Lihat Catatan
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada RPS yang menunggu revisi</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsRevisi->count() }} dari {{ $rpsRevisi->total() }} entri</span>
                {{ $rpsRevisi->links('banksoal::components.ui.laravel-pagination') }}
            </div>
        </div>

        <div class="hidden" data-tab-panel="disetujui">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                 x-data="{
                     searchQuery: '',
                     selectedSemester: '',
                     selectedTahun: '',
                     filterOpen: false,
                     applyFilters() {
                         const rows = document.querySelectorAll('#table-disetujui tbody tr:not(.no-results-message)');
                         let visibleCount = 0;
                         rows.forEach(row => {
                             const text = row.textContent.toLowerCase();
                             const matchesSearch = text.includes(this.searchQuery.toLowerCase().trim());
                             const matchesSemester = this.selectedSemester === '' || text.includes('semester ' + this.selectedSemester.toLowerCase());
                             const matchesTahun = this.selectedTahun === '' || text.includes(this.selectedTahun.toLowerCase());
                             
                             if (matchesSearch && matchesSemester && matchesTahun) {
                                 row.classList.remove('hidden');
                                 visibleCount++;
                             } else {
                                 row.classList.add('hidden');
                             }
                         });
                         const noResults = document.querySelector('#table-disetujui .no-results-message');
                         if (noResults) {
                             if (visibleCount === 0) noResults.classList.remove('hidden');
                             else noResults.classList.add('hidden');
                         }
                     }
                 }">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="applyFilters()" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none" placeholder="Cari mata kuliah atau dosen...">
                </div>

                <div class="relative" @click.away="filterOpen = false">
                    <button @click="filterOpen = !filterOpen" type="button"
                        class="flex items-center gap-2 px-5 py-2.5 bg-white border rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm"
                        :class="selectedSemester || selectedTahun ? 'border-primary text-primary bg-primary/5' : 'border-slate-200'">
                        <svg class="w-4 h-4" :class="selectedSemester || selectedTahun ? 'text-primary' : 'text-slate-400'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        Filter
                        <template x-if="selectedSemester || selectedTahun">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                        </template>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="filterOpen" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Semester</label>
                            <div class="space-y-2">
                                <select x-model="selectedSemester" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-slate-100">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                            <div class="space-y-2">
                                <select x-model="selectedTahun" @change="applyFilters()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="">Semua Tahun</option>
                                    @php
                                        $currentYear = date('Y');
                                        $yearsList = [];
                                        for ($i = -3; $i <= 3; $i++) {
                                            $y1 = $currentYear + $i;
                                            $y2 = $y1 + 1;
                                            $yearsList[] = "$y1/$y2";
                                        }
                                    @endphp
                                    @foreach($yearsList as $yr)
                                        <option value="{{ $yr }}">{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="selectedSemester = ''; selectedTahun = ''; searchQuery = ''; applyFilters(); filterOpen = false;"
                                class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
                                Reset
                            </button>
                            <button type="button" @click="filterOpen = false"
                                class="flex-1 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:opacity-90 shadow-md shadow-primary/20 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="table-disetujui">
                        <thead class="bg-primary text-white border-b border-primary/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dosen Pengampu</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Disetujui</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rpsDisetujui as $rps)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-900">{{ $rps->mataKuliah->nama }} ({{ $rps->mataKuliah->kode }})</div>
                                        <div class="text-xs text-slate-500">Semester {{ $rps->semester }} {{ $rps->tahun_ajaran }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @forelse($rps->dosens as $dosen)
                                                @php
                                                    $names = explode(' ', $dosen->name);
                                                    $first = $names[0] ?? '';
                                                    $last = $names[array_key_last($names)] ?? '';
                                                    $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
                                                @endphp
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">{{ $initials }}</div>
                                                    <span class="text-sm font-medium text-slate-700">{{ $dosen->name }}</span>
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-500">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ $rps->updated_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4"><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">Disetujui</span></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('banksoal.rps.gpm.validasi-rps.setuju', $rps->id) }}" class="gpm-rps-action-btn">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-600">Belum ada RPS yang disetujui</td>
                                </tr>
                            @endforelse
                            <tr class="no-results-message hidden">
                                <td colspan="5" class="px-6 py-10 text-center text-slate-600">Tidak ada hasil pencarian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs text-slate-500">Menampilkan {{ $rpsDisetujui->count() }} dari {{ $rpsDisetujui->total() }} entri</span>
                {{ $rpsDisetujui->links('banksoal::components.ui.laravel-pagination') }}
            </div>
        </div>
    </div>



    <div id="modalCloseSession" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalCloseSession"></div>
        <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl animate-popup">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.close-session') }}" method="POST">
                @csrf
                <div class="px-5 py-5 text-center">
                    <div class="text-rose-500 mb-3"><i class="fas fa-exclamation-circle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Matikan Sesi Pengajuan?</h3>
                    <p class="text-xs text-slate-500 mt-2">Sesi pengajuan <strong>{{ $activePeriode->judul ?? 'RPS' }}</strong> akan ditutup. Dosen tidak akan bisa lagi mengajukan RPS sampai periode baru diaktifkan.</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalCloseSession">Batal</button>
                        <button type="submit" class="flex-1 rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Matikan Sesi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="modalOpenSession" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-slate-900/40 animate-backdrop" data-modal-overlay="modalOpenSession"></div>
        <div class="relative mx-auto mt-24 w-full max-w-sm rounded-2xl bg-white shadow-xl animate-popup">
            <form action="{{ route('banksoal.rps.gpm.periode-rps.open-session') }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" id="periodeId">
                <div class="px-5 py-5 text-center">
                    <div class="text-primary mb-3"><i class="fas fa-info-circle text-3xl"></i></div>
                    <h3 class="text-sm font-semibold text-slate-900">Nyalakan Sesi Pengajuan?</h3>
                    <p class="text-xs text-slate-500 mt-2">Sesi pengajuan <strong id="periodeJudul">RPS</strong> akan diaktifkan. Dosen akan bisa mengajukan RPS sesuai dengan jadwal periode.</p>
                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600" data-modal-close="modalOpenSession">Batal</button>
                        <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-navy-700">Nyalakan Sesi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
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

                const rows = tabContent.querySelectorAll('table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let rowText = '';
                    if (cells.length >= 2) {
                        rowText = (cells[0].textContent + ' ' + cells[1].textContent).toLowerCase();
                    } else {
                        rowText = row.textContent.toLowerCase();
                    }

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
            }

            const searchInputs = document.querySelectorAll('[data-search-tab]');
            searchInputs.forEach((input) => {
                const tabId = input.getAttribute('data-search-tab');
                input.addEventListener('input', debounce(function () {
                    searchTable(this, tabId);
                }, 300));
            });
        });

        function setPeriodeData(element) {
            const periodeId = element.getAttribute('data-periode-id');
            const periodeJudul = element.getAttribute('data-periode-judul');
            const periodeInput = document.getElementById('periodeId');
            const periodeLabel = document.getElementById('periodeJudul');
            if (periodeInput) periodeInput.value = periodeId;
            if (periodeLabel) periodeLabel.textContent = periodeJudul;
        }

        function closeModalById(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }


    </script>
</x-banksoal::layouts.gpm-master>