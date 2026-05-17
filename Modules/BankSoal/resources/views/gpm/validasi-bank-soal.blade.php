<x-banksoal::layouts.gpm-master>
    @section('breadcrumbs')
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Validasi Soal</span>
    @endsection
    <style>
        @keyframes modalPopUp {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .animate-popup {
            animation: modalPopUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
    
    <x-banksoal::notification.alerts />
    <x-banksoal::ui.page-header title="Validasi Bank Soal" subtitle="Pilih paket soal mata kuliah yang perlu dievaluasi" />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" data-search-tab="menunggu" placeholder="Cari mata kuliah atau dosen..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none">
        </div>

        <div class="flex items-center gap-3">
            <button type="button" id="filterBtn" class="inline-flex items-center gap-2 bg-primary/10 hover:bg-primary/20 text-primary rounded-xl px-4 py-2.5 font-medium transition-colors border border-primary/20">
                <i class="fas fa-filter"></i> Filter
            </button>
            <select class="px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none cursor-pointer min-w-[180px]" id="sortBy">
                <option value="terbaru">Terbaru</option>
                <option value="terlama">Terlama</option>
                <option value="nama-asc">Nama A-Z</option>
                <option value="nama-desc">Nama Z-A</option>
            </select>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-start justify-center pt-8 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-md mx-auto animate-popup">
            <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Filter</h3>
                <button type="button" id="closeFilterBtn" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Validasi</option>
                        <option value="selesai">Selesai Direview</option>
                    </select>
                </div>
            </div>
            <div class="border-t border-slate-200 px-6 py-4 flex items-center gap-3">
                <button type="button" id="applyFilterBtn" class="flex-1 inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-white rounded-xl px-4 py-2.5 font-medium transition-colors">
                    <i class="fas fa-check"></i> Terapkan
                </button>
                <button type="button" id="resetFilterBtn" class="flex-1 inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl px-4 py-2.5 font-medium transition-colors">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 pt-4 border-b border-slate-200">
            <nav class="flex gap-6 text-sm font-semibold">
                <a href="#" class="pb-3 border-b-2 border-primary text-primary flex items-center">
                    Menunggu Validasi
                    <span class="ml-2 inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary border border-primary/20">{{ $counts->menunggu ?? 0 }}</span>
                </a>
                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center">
                    Selesai Direview
                    <span class="ml-2 inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 border border-slate-200">{{ $counts->selesai ?? 0 }}</span>
                </a>
            </nav>
        </div>

        <div class="overflow-x-auto" data-tab-panel="menunggu">
            <table class="w-full">
                <thead class="bg-primary text-white border-b border-primary/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Soal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Diajukan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($paket_soal as $paket)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $paket->mk_nama }}</div>
                                <div class="text-xs text-slate-500">{{ $paket->mk_kode }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                        {{ strtoupper(substr($paket->dosen_pengampu ?? $paket->mk_nama, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-800">{{ $paket->dosen_pengampu ?? 'Dosen Pengampu' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $paket->jumlah_soal }} Butir</td>
                            <td class="px-6 py-4 text-sm text-slate-500">-</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">Menunggu</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review', ['mk_id' => $paket->mk_id]) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary/90">
                                    Mulai Validasi <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-results-message">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-circle text-3xl text-slate-300 mb-3"></i>
                                    <p class="font-medium">Antrean kosong.</p>
                                    <p class="text-xs text-slate-500">Saat ini tidak ada bank soal yang menunggu untuk divalidasi oleh GPM.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($all_paket_soal->count() > 0)
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-500">Menampilkan <span id="count-menunggu">{{ $all_paket_soal->count() }}</span> mata kuliah</span>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter Modal Handlers
            const filterBtn = document.getElementById('filterBtn');
            const closeFilterBtn = document.getElementById('closeFilterBtn');
            const filterModal = document.getElementById('filterModal');
            const applyFilterBtn = document.getElementById('applyFilterBtn');
            const resetFilterBtn = document.getElementById('resetFilterBtn');
            const filterStatus = document.getElementById('filterStatus');

            filterBtn.addEventListener('click', () => {
                filterModal.classList.remove('hidden');
            });

            closeFilterBtn.addEventListener('click', () => {
                filterModal.classList.add('hidden');
            });

            filterModal.addEventListener('click', (e) => {
                if (e.target === filterModal) {
                    filterModal.classList.add('hidden');
                }
            });

            applyFilterBtn.addEventListener('click', () => {
                filterModal.classList.add('hidden');
                // Filter logic can be extended here
            });

            resetFilterBtn.addEventListener('click', () => {
                filterStatus.value = '';
            });

            // Sort functionality
            const sortBy = document.getElementById('sortBy');
            sortBy.addEventListener('change', function() {
                // Sort logic can be extended here
            });

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

                // Update counter logic
                const countElement = document.getElementById(`count-${tabId}`);
                if (countElement) {
                    countElement.textContent = visibleCount;
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
    </script>
    @endpush
</x-banksoal::layouts.gpm-master>