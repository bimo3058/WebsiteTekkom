<x-eoffice::layouts.dosen title="Bimbingan Mahasiswa">
    <script>
        window.initialMahasiswaData = {!! json_encode($mahasiswas) !!};
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div x-data="bimbinganApp()" x-init="initData(window.initialMahasiswaData)" class="max-w-7xl mx-auto">

        <!-- Toast Notification -->
        <div x-cloak x-show="toast.show" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="fixed top-24 right-6 lg:right-10 z-50 bg-white border shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]"
            :class="toast.type === 'success' ? 'border-emerald-100' : 'border-red-100'">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5 border"
                :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-red-50 border-red-100 text-red-600'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-900 mb-0.5"
                    x-text="toast.type === 'success' ? 'Berhasil!' : 'Gagal!'"></p>
                <p class="text-[13px] text-slate-500 leading-relaxed" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false"
                class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Page Title -->
        <div class="mb-5">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Bimbingan Mahasiswa</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar mahasiswa bimbingan yang membutuhkan peninjauan</p>
        </div>

        <!-- Dashboard Style Unified Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative mb-6">

            <!-- Unified Header (Controls Only) -->
            <div
                class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row gap-4 items-center justify-between">

                <!-- Table Title -->
                <h2 class="text-lg font-bold text-slate-800 tracking-tight shrink-0">Bimbingan Mahasiswa</h2>

                <!-- Search & Filters -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto shrink-0">

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Search..."
                            class="block w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors bg-white outline-none">
                    </div>

                    <!-- Filter Style Select -->
                    <div class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </div>
                        <select x-model="filterStatus"
                            class="block w-full sm:w-32 pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 font-medium bg-white hover:bg-slate-50 transition-colors outline-none cursor-pointer appearance-none shadow-sm"
                            style="background-image: none;">
                            <option value="all">Filter</option>
                            <option value="Pra-KP">Pra-KP</option>
                            <option value="Saat KP">Saat KP</option>
                            <option value="Pasca KP">Pasca KP</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <!-- Sort Style Select -->
                    <div class="relative w-full sm:w-auto">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                            </svg>
                        </div>
                        <select x-model="sortOrder"
                            class="block w-full sm:w-32 pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 font-medium bg-white hover:bg-slate-50 transition-colors outline-none cursor-pointer appearance-none shadow-sm"
                            style="background-image: none;">
                            <option value="asc">A - Z</option>
                            <option value="desc">Z - A</option>
                            <option value="progress_desc">Progres</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Loading State -->
            <div x-show="loading"
                class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-slate-200">
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 tracking-wide w-1/4">
                                Nama Mahasiswa</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 tracking-wide w-1/4">
                                Judul / Tempat</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 tracking-wide w-1/5">
                                Progres</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 tracking-wide text-center">
                                Nilai Akhir</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 tracking-wide text-center">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="item in paginatedMahasiswa" :key="item.id">
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 border border-indigo-200">
                                            <span x-text="item.nama.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 transition-colors"
                                                x-text="item.nama"></p>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="item.nim"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="max-w-xs">
                                        <p class="text-sm font-semibold text-slate-800 truncate" title="item.judul_kp"
                                            x-text="item.judul_kp"></p>
                                        <div class="flex items-center gap-1.5 mt-1 text-xs text-slate-500 truncate">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="truncate" x-text="item.tempat_kp"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide uppercase border whitespace-nowrap"
                                            :class="getStatusBadgeClass(item.status_kp)">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0 mr-1.5"
                                                :class="getStatusDotClass(item.status_kp)"></span>
                                            <span x-text="getStatusLabel(item.status_kp)"></span>
                                        </span>
                                        <span class="text-xs font-bold text-slate-700"
                                            x-text="item.progress + '%'"></span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500"
                                            :style="'width: ' + item.progress + '%'"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <template x-if="item.nilai_akhir !== null && item.nilai_akhir !== undefined">
                                        <span
                                            class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-700 font-extrabold text-sm border border-blue-100 shadow-sm"
                                            x-text="item.nilai_akhir"></span>
                                    </template>
                                    <template x-if="item.nilai_akhir === null || item.nilai_akhir === undefined">
                                        <span
                                            class="text-sm font-black text-slate-300 select-none cursor-default">—</span>
                                    </template>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a :href="'/eoffice/kp/dosen/bimbingan/' + item.id"
                                        class="inline-flex items-center justify-center px-4 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded hover:bg-slate-50 hover:text-indigo-600 hover:border-slate-300 shadow-sm transition-all focus:ring-2 focus:ring-slate-200">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="filteredMahasiswa.length === 0" x-cloak>
                            <td colspan="5" class="py-16 text-center border-b-0">
                                <div
                                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 border border-slate-100">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mb-1">Tidak Ada Data Mahasiswa</h3>
                                <p class="text-sm text-slate-500">Mahasiswa bimbingan dengan kriteria tersebut tidak
                                    ditemukan.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="bg-white px-6 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4"
                x-show="filteredMahasiswa.length > 0" x-cloak>
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <span>Per page</span>
                        <select x-model="itemsPerPage"
                            class="border border-slate-200 rounded text-slate-700 py-1 pl-2 pr-7 focus:outline-none focus:ring-1 focus:ring-indigo-500 hover:bg-slate-50 cursor-pointer bg-white">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="border-l border-slate-300 pl-3 hidden sm:block">
                        <p>Showing <span class="font-bold text-slate-700"
                                x-text="(currentPage - 1) * itemsPerPage + 1"></span> to <span
                                class="font-bold text-slate-700"
                                x-text="Math.min(currentPage * itemsPerPage, filteredMahasiswa.length)"></span> of
                            <strong x-text="filteredMahasiswa.length" class="text-slate-700"></strong> results
                        </p>
                    </div>
                </div>
                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <template x-for="page in paginationRange" :key="page">
                        <button @click="changePage(page)"
                            class="w-8 h-8 flex items-center justify-center rounded-lg font-medium text-xs transition-colors"
                            :class="page === currentPage ? 'bg-indigo-900 border border-indigo-900 text-white font-bold shadow-sm' : (page === '...' ? 'cursor-default text-slate-400 bg-transparent border-transparent' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50')"
                            x-text="page" :disabled="page === '...'"></button>
                    </template>

                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('bimbinganApp', () => ({
                    sidebarOpen: false,
                    mahasiswas: [],
                    searchQuery: '',
                    filterStatus: 'all',
                    sortOrder: 'asc',
                    loading: true,

                    // Pagination State
                    currentPage: 1,
                    itemsPerPage: 10,

                    // Toast State
                    toast: { show: false, type: 'success', message: '' },

                    init() {
                        this.$watch('searchQuery', () => this.currentPage = 1);
                        this.$watch('filterStatus', () => this.currentPage = 1);
                        this.$watch('sortOrder', () => this.currentPage = 1);
                        this.$watch('itemsPerPage', () => this.currentPage = 1);
                    },

                    initData(data) {
                        this.mahasiswas = data;
                        setTimeout(() => this.loading = false, 400);
                    },

                    get filteredMahasiswa() {
                        let result = this.mahasiswas;
                        const q = this.searchQuery.toLowerCase();
                        if (q) result = result.filter(m => m.nama.toLowerCase().includes(q) || m.nim.toLowerCase().includes(q));
                        if (this.filterStatus !== 'all') result = result.filter(m => m.status_kp === this.filterStatus);
                        result = result.sort((a, b) => {
                            if (this.sortOrder === 'asc') return a.nama.localeCompare(b.nama);
                            if (this.sortOrder === 'desc') return b.nama.localeCompare(a.nama);
                            if (this.sortOrder === 'progress_desc') return b.progress - a.progress;
                            return 0;
                        });
                        return result;
                    },

                    get totalPages() {
                        return Math.ceil(this.filteredMahasiswa.length / this.itemsPerPage) || 1;
                    },

                    get paginatedMahasiswa() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        const end = start + parseInt(this.itemsPerPage); // ensure it's not string concatenation
                        return this.filteredMahasiswa.slice(start, end);
                    },

                    get paginationRange() {
                        const current = this.currentPage;
                        const total = this.totalPages;
                        const range = [];
                        if (total <= 5) {
                            for (let i = 1; i <= total; i++) range.push(i);
                        } else {
                            if (current <= 3) {
                                range.push(1, 2, 3, 4, '...', total);
                            } else if (current >= total - 2) {
                                range.push(1, '...', total - 3, total - 2, total - 1, total);
                            } else {
                                range.push(1, '...', current - 1, current, current + 1, '...', total);
                            }
                        }
                        return range;
                    },

                    changePage(page) {
                        if (page !== '...' && page >= 1 && page <= this.totalPages) this.currentPage = page;
                    },
                    nextPage() {
                        if (this.currentPage < this.totalPages) this.currentPage++;
                    },
                    prevPage() {
                        if (this.currentPage > 1) this.currentPage--;
                    },

                    getStatusLabel(status) {
                        const labels = { 'Pra-KP': 'Pra-KP', 'Saat KP': 'Saat KP', 'Pasca KP': 'Pasca KP', 'Selesai': 'Selesai KP', 'Dibatalkan': 'Dibatalkan', 'Gagal': 'Gagal' };
                        return labels[status] || status;
                    },

                    getStatusBadgeClass(status) {
                        const classes = {
                            'Pra-KP': 'text-amber-600 bg-amber-50 border-amber-100',
                            'Saat KP': 'text-indigo-600 bg-indigo-50 border-indigo-100',
                            'Pasca KP': 'text-purple-600 bg-purple-50 border-purple-100',
                            'Selesai': 'text-emerald-600 bg-emerald-50 border-emerald-100',
                            'Dibatalkan': 'text-rose-600 bg-rose-50 border-rose-100',
                            'Gagal': 'text-rose-600 bg-rose-50 border-rose-100'
                        };
                        return classes[status] || 'text-slate-600 bg-slate-50 border-slate-200';
                    },

                    getStatusDotClass(status) {
                        const classes = {
                            'Pra-KP': 'bg-amber-500',
                            'Saat KP': 'bg-indigo-500',
                            'Pasca KP': 'bg-purple-500',
                            'Selesai': 'bg-emerald-500',
                            'Dibatalkan': 'bg-rose-500',
                            'Gagal': 'bg-rose-500'
                        };
                        return classes[status] || 'bg-slate-400';
                    },



                    showToast(type, message) {
                        this.toast.type = type;
                        this.toast.message = message;
                        this.toast.show = true;
                        setTimeout(() => {
                            this.toast.show = false;
                        }, 4000);
                    }
                }));
            });
        </script>
    @endpush
</x-eoffice::layouts.dosen>