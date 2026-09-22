<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
        <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Validasi Soal</span>
    @endsection
    <style>
        @keyframes modalPopUp {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-popup {
            animation: modalPopUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .gpm-bank-filter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            padding: 0.625rem 1.25rem;
            color: #334155;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .gpm-bank-filter-btn:hover {
            border-color: #0b266e;
            background: #f8fafc;
            color: #0b266e;
        }

        .gpm-bank-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 7.5rem;
            border: 1px solid #0b266e;
            border-radius: 8px;
            background: #0b266e;
            padding: 0.5rem 0.75rem;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .gpm-bank-action-btn:hover {
            border-color: #081c52;
            background: #081c52;
        }
    </style>

    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi Bank Soal"
        subtitle="Pilih paket soal mata kuliah yang perlu dievaluasi" />

    <div x-data="{
        searchQuery: '',
        selectedDosen: '',
        sortBy: 'terbaru',
        filterOpen: false,
        
        applyFiltersAndSort() {
            const tbody = document.querySelector('#table-menunggu tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr:not(.no-results-message)'));
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = text.includes(this.searchQuery.toLowerCase().trim());
                const matchesDosen = this.selectedDosen === '' || text.includes(this.selectedDosen.toLowerCase());
                
                if (matchesSearch && matchesDosen) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            if (this.sortBy === 'nama-asc') {
                rows.sort((a, b) => {
                    const nameA = (a.querySelector('td:first-child .font-semibold')?.textContent || '').trim();
                    const nameB = (b.querySelector('td:first-child .font-semibold')?.textContent || '').trim();
                    return nameA.localeCompare(nameB);
                });
            } else if (this.sortBy === 'nama-desc') {
                rows.sort((a, b) => {
                    const nameA = (a.querySelector('td:first-child .font-semibold')?.textContent || '').trim();
                    const nameB = (b.querySelector('td:first-child .font-semibold')?.textContent || '').trim();
                    return nameB.localeCompare(nameA);
                });
            }
            
            rows.forEach(row => tbody.appendChild(row));
            
            const noResults = tbody.querySelector('.no-results-message');
            if (noResults) {
                if (visibleCount === 0) noResults.classList.remove('hidden');
                else noResults.classList.add('hidden');
            }
            
            const countElem = document.getElementById('count-menunggu');
            if (countElem) countElem.textContent = visibleCount;
        }
    }">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Daftar Bank Soal</h2>
                <p class="mt-1 text-sm text-slate-500">Paket soal yang perlu diperiksa dan divalidasi oleh GPM.</p>
            </div>

            <div
                class="mx-4 mt-4 mb-4 rounded-xl border border-slate-200 bg-slate-50 p-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="applyFiltersAndSort()"
                        placeholder="Cari mata kuliah atau dosen..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none">
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative" @click.away="filterOpen = false">
                        <button @click="filterOpen = !filterOpen" type="button" class="gpm-bank-filter-btn"
                            :class="selectedDosen ? 'border-primary bg-primary/5 text-primary' : ''">
                            Filter
                            <template x-if="selectedDosen">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                            </template>
                        </button>

                        <div x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                            <div>
                                <label
                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Dosen
                                    Pengampu</label>
                                <div class="space-y-2">
                                    <select x-model="selectedDosen" @change="applyFiltersAndSort()"
                                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20">
                                        <option value="">Semua Dosen</option>
                                        @php
                                            $dosens = $paket_soal->pluck('dosen_pengampu')->filter()->unique();
                                        @endphp
                                        @foreach($dosens as $dsn)
                                            <option value="{{ $dsn }}">{{ $dsn }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-slate-100">
                                <button type="button"
                                    @click="selectedDosen = ''; searchQuery = ''; applyFiltersAndSort(); filterOpen = false;"
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

                    <select x-model="sortBy" @change="applyFiltersAndSort()"
                        class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none cursor-pointer min-w-45 focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none"
                        id="sortBy">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                        <option value="nama-asc">Nama A-Z</option>
                        <option value="nama-desc">Nama Z-A</option>
                    </select>
                </div>
            </div>

            <div class="px-6 pt-4 border-b border-slate-200">
                <nav class="flex gap-6 text-sm font-semibold">
                    <a href="#" class="pb-3 border-b-2 border-primary text-primary flex items-center">
                        Menunggu Validasi
                        <span
                            class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary border border-primary/20">{{ $counts->menunggu ?? 0 }}</span>
                    </a>
                    <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}"
                        class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center">
                        Selesai Direview
                        <span
                            class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $counts->selesai ?? 0 }}</span>
                    </a>
                </nav>
            </div>

            <div class="overflow-x-auto" data-tab-panel="menunggu">
                <table class="min-w-full table-fixed text-sm" id="table-menunggu">
                    <colgroup>
                        <col class="w-[25%]">
                        <col class="w-[30%]">
                        <col class="w-[13%]">
                        <col class="w-[14%]">
                        <col class="w-[8%]">
                        <col class="w-[10%]">
                    </colgroup>
                    <thead
                        class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500 border-y border-slate-200">
                        <tr>
                            <th class="whitespace-nowrap px-6 py-4 text-left">Mata Kuliah</th>
                            <th class="whitespace-nowrap px-6 py-4 text-left">Dosen Pengampu</th>
                            <th class="whitespace-nowrap px-6 py-4 text-left">Jumlah Soal</th>
                            <th class="whitespace-nowrap px-6 py-4 text-left">Tanggal Diajukan</th>
                            <th class="whitespace-nowrap px-6 py-4 text-left">Status</th>
                            <th class="whitespace-nowrap px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($paket_soal as $paket)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900">{{ $paket->mk_nama }}</div>
                                    <div class="text-xs text-slate-500">{{ $paket->mk_kode }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $dosenPengampu = collect(explode('|||', (string) ($paket->dosen_pengampu ?? '')))
                                            ->map(fn($nama) => trim($nama))
                                            ->filter()
                                            ->values();
                                    @endphp
                                    <div class="flex flex-col gap-2">
                                        @forelse($dosenPengampu as $namaDosen)
                                            <div>
                                                <span class="text-sm font-medium text-slate-800">{{ $namaDosen }}</span>
                                            </div>
                                        @empty
                                            <span class="text-xs text-slate-500">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700">{{ $paket->jumlah_soal }} Butir</td>
                                <td class="px-6 py-4 text-sm text-slate-500">-</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-amber-700">Menunggu</span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review', ['mk_id' => $paket->mk_id]) }}"
                                        class="gpm-bank-action-btn">
                                        Review Sekarang
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="no-results-message">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-check-circle text-3xl text-slate-300 mb-3"></i>
                                        <p class="font-medium">Antrean kosong.</p>
                                        <p class="text-xs text-slate-500">Saat ini tidak ada bank soal yang menunggu untuk
                                            divalidasi oleh GPM.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($all_paket_soal->count() > 0)
                <div class="px-6 py-4 border-t border-slate-200 bg-white flex items-center justify-between">
                    <span class="text-xs text-slate-500">Menampilkan <span
                            id="count-menunggu">{{ $all_paket_soal->count() }}</span> mata kuliah</span>
                </div>
            @endif
        </div>

    </div>

</x-banksoal::layouts.gpm-master>