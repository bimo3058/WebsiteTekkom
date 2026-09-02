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

        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-4">
                <span class="font-medium">Dosen</span>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-900">Bimbingan</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bimbingan Mahasiswa</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola daftar mahasiswa bimbingan dan pantau progres KP</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-full bg-primary-50 text-primary-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-700 text-sm">Total Bimbingan</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-slate-800" x-text="countTotal">0</h3>
                    <p class="text-xs text-slate-500 mt-1">Seluruh mahasiswa bimbingan</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-full bg-amber-50 text-amber-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-700 text-sm">Pra-KP</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-slate-800" x-text="countPraKP">0</h3>
                    <p class="text-xs text-slate-500 mt-1">Mahasiswa tahap persiapan</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-full bg-primary-50 text-primary-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-700 text-sm">Saat KP</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-slate-800" x-text="countSaatKP">0</h3>
                    <p class="text-xs text-slate-500 mt-1">Sedang melaksanakan KP</p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-full bg-emerald-50 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-700 text-sm">Selesai / Pasca</p>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-slate-800" x-text="countSelesai">0</h3>
                    <p class="text-xs text-slate-500 mt-1">Telah menyelesaikan KP</p>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex space-x-1 p-1 bg-slate-100/70 rounded-lg max-w-fit mb-6">
            <button @click="filterStatus = 'all'" 
                :class="filterStatus === 'all' ? 'bg-white shadow-sm text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'"
                class="px-4 py-2 text-sm rounded-md transition-colors">Semua Status</button>
            <button @click="filterStatus = 'Pra-KP'" 
                :class="filterStatus === 'Pra-KP' ? 'bg-white shadow-sm text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'"
                class="px-4 py-2 text-sm rounded-md transition-colors">Pra-KP</button>
            <button @click="filterStatus = 'Saat KP'" 
                :class="filterStatus === 'Saat KP' ? 'bg-white shadow-sm text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'"
                class="px-4 py-2 text-sm rounded-md transition-colors">Saat KP</button>
            <button @click="filterStatus = 'Selesai'" 
                :class="filterStatus === 'Selesai' ? 'bg-white shadow-sm text-slate-800 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'"
                class="px-4 py-2 text-sm rounded-md transition-colors">Selesai</button>
        </div>

        <!-- Dashboard Style Unified Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative mb-6">
            
            <!-- Table Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
                <h3 class="text-base font-bold text-slate-800">Tabel Mahasiswa Bimbingan</h3>
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative flex-1 sm:flex-none">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" placeholder="Search" x-model="searchQuery" 
                            class="w-full sm:w-64 pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 placeholder-slate-400 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-colors bg-white">
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="relative shrink-0 flex items-center">
                        <div class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg bg-white">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                            <span class="text-sm font-medium text-slate-500">Sort by</span>
                            <select x-model="sortOrder" 
                                class="pl-2 pr-6 py-0 border-none bg-transparent text-sm font-medium text-slate-700 cursor-pointer appearance-none focus:outline-none focus:ring-0" style="background-image:none;">
                                <option value="asc">A-Z</option>
                                <option value="desc">Z-A</option>
                                <option value="progress_desc">Progress</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Loading State -->
            <div x-show="loading"
                class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary-500"></div>
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
                                            class="w-10 h-10 rounded-full bg-primary-100 text-primary-500 flex items-center justify-center font-bold text-sm shrink-0 border border-primary-200">
                                            <span x-text="item.nama.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 group-hover:text-primary-500 transition-colors"
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
                                        <div class="bg-primary-500 h-1.5 rounded-full transition-all duration-500"
                                            :style="'width: ' + item.progress + '%'"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <template x-if="item.nilai_akhir !== null && item.nilai_akhir !== undefined">
                                        <span
                                            class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-primary-50 text-primary-500 font-extrabold text-sm border border-primary-100 shadow-sm"
                                            x-text="item.nilai_akhir"></span>
                                    </template>
                                    <template x-if="item.nilai_akhir === null || item.nilai_akhir === undefined">
                                        <span
                                            class="text-sm font-black text-slate-300 select-none cursor-default">—</span>
                                    </template>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a :href="'/eoffice/kp/dosen/bimbingan/' + item.id"
                                        class="inline-flex items-center justify-center px-4 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded hover:bg-slate-50 hover:text-primary-500 hover:border-slate-300 shadow-sm transition-all focus:ring-2 focus:ring-slate-200">
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
                
                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        :class="{'text-slate-300 cursor-not-allowed': currentPage === 1, 'text-slate-600 hover:bg-slate-50': currentPage > 1}"
                        class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
                        <template x-for="page in paginationRange" :key="page">
                            <button @click="changePage(page)"
                                :class="{'bg-[#354371] text-white': page === currentPage, 'text-slate-600 hover:bg-slate-50': page !== currentPage}"
                                class="w-8 h-8 flex items-center justify-center border-r border-slate-200 transition-colors"
                                x-text="page" :disabled="page === '...'"></button>
                        </template>
                    </div>

                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        :class="{'text-slate-300 cursor-not-allowed': currentPage === totalPages, 'text-slate-600 hover:bg-slate-50': currentPage < totalPages}"
                        class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white shadow-sm transition-colors border-l-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center border border-slate-200 rounded-md bg-white overflow-hidden text-[13px] shadow-sm">
                        <span class="px-3 py-1.5 text-slate-600 font-medium border-r border-slate-200 bg-slate-50">Per halaman</span>
                        <select x-model.number="itemsPerPage"
                            class="px-2.5 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border-none appearance-none pr-7 relative bg-no-repeat"
                            style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' stroke=\'%2394a3b8\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/></svg>'); background-position: right 0.5rem center; background-size: 0.9rem;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <p class="text-xs font-medium text-slate-600 hidden sm:block">Menampilkan <span class="font-bold text-slate-800"
                            x-text="((currentPage - 1) * itemsPerPage) + 1"></span> sampai <span
                            class="font-bold text-slate-800"
                            x-text="Math.min(currentPage * itemsPerPage, filteredMahasiswa.length)"></span> dari <span
                            class="font-bold text-slate-800" x-text="filteredMahasiswa.length"></span> entri</p>
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

                    get countTotal() {
                        return this.mahasiswas.length;
                    },
                    get countPraKP() {
                        return this.mahasiswas.filter(m => m.status_kp === 'Pra-KP').length;
                    },
                    get countSaatKP() {
                        return this.mahasiswas.filter(m => m.status_kp === 'Saat KP').length;
                    },
                    get countSelesai() {
                        return this.mahasiswas.filter(m => ['Selesai', 'Pasca KP'].includes(m.status_kp)).length;
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
                            'Saat KP': 'text-primary-500 bg-primary-50 border-primary-100',
                            'Pasca KP': 'text-primary-500 bg-primary-50 border-primary-100',
                            'Selesai': 'text-emerald-600 bg-emerald-50 border-emerald-100',
                            'Dibatalkan': 'text-rose-600 bg-rose-50 border-rose-100',
                            'Gagal': 'text-rose-600 bg-rose-50 border-rose-100'
                        };
                        return classes[status] || 'text-slate-600 bg-slate-50 border-slate-200';
                    },

                    getStatusDotClass(status) {
                        const classes = {
                            'Pra-KP': 'bg-amber-500',
                            'Saat KP': 'bg-primary-500',
                            'Pasca KP': 'bg-primary-500',
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