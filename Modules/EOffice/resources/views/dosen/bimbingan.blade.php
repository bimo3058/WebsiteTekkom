<x-eoffice::layouts.dosen title="Bimbingan Mahasiswa">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Bimbingan Mahasiswa</span>
    <script>
        window.initialMahasiswaData = {!! json_encode($mahasiswas) !!};
    </script>
    <style>
        [x-cloak] { display: none !important; }
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

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Bimbingan Mahasiswa
                </h1>
                <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Kelola mahasiswa bimbingan kerja
                    praktik. Anda dapat memantau progres, melihat dokumen, dan memberikan penilaian seminar.
                </p>
            </div>
        </div>

        <!-- Filters & Search -->
        <div
            class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="relative w-full lg:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari nama mahasiswa atau NIM..."
                    class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none">
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                <select x-model="filterStatus"
                    class="block w-full sm:w-48 py-2 px-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="Pra-KP">Pra-KP (Menunggu)</option>
                    <option value="Saat KP">Saat KP (Berjalan)</option>
                    <option value="Pasca KP">Pasca KP (Seminar)</option>
                    <option value="Selesai">Selesai KP</option>
                </select>
                <select x-model="sortOrder"
                    class="block w-full sm:w-48 py-2 px-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none cursor-pointer">
                    <option value="asc">Nama (A-Z)</option>
                    <option value="desc">Nama (Z-A)</option>
                    <option value="progress_desc">Progress Terbesar</option>
                </select>
            </div>
        </div>

        <!-- Data Table / List -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative">

            <!-- Loading State -->
            <div x-show="loading"
                class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4">
                                Mahasiswa</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/4">
                                Informasi KP</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/5">
                                Progress & Status</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Nilai Akhir</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="item in filteredMahasiswa" :key="item.id">
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
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide uppercase border"
                                            :class="getStatusBadgeClass(item.status_kp)">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5"
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
                                            class="text-xs font-medium text-slate-400 italic cursor-default px-2 py-0.5 rounded border border-slate-100 bg-slate-50">Belum
                                            Dinilai</span>
                                    </template>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a :href="'/eoffice/kp/dosen/bimbingan/' + item.id"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-[#F8F9FB] border border-[#E9EAEC] text-[#353849] hover:bg-[#0065FF] hover:border-[#0065FF] hover:text-white rounded-lg text-[13px] font-semibold transition-colors duration-200 shadow-sm">
                                        Buka Ruang
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="filteredMahasiswa.length === 0" x-cloak>
                            <td colspan="5" class="py-16 text-center">
                                <div
                                    class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Data Mahasiswa
                                </h3>
                                <p class="text-sm text-slate-500">Mahasiswa bimbingan dengan kriteria
                                    tersebut tidak ditemukan.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between"
                x-show="filteredMahasiswa.length > 0">
                <p class="text-sm text-slate-500">Menampilkan <span class="font-bold text-slate-900"
                        x-text="filteredMahasiswa.length"></span> mahasiswa bimbingan</p>
                <!-- Pagination controls (Visual Only for now) -->
                <div class="flex gap-1">
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-indigo-600 bg-indigo-600 text-white font-medium text-xs">1</button>
                    <button
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
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

                    // Drawer & Form State
                    drawerOpen: false,
                    selectedData: null,
                    formNilai: {
                        seminar: null,
                        laporan: null,
                        catatan: ''
                    },
                    isSubmitting: false,

                    // Toast State
                    toast: {
                        show: false,
                        type: 'success',
                        message: ''
                    },

                    initData(data) {
                        this.mahasiswas = data;
                        // Simulate initial loading for smooth UI feel
                        setTimeout(() => {
                            this.loading = false;
                        }, 400);
                    },

                    get filteredMahasiswa() {
                        let result = this.mahasiswas;

                        const q = this.searchQuery.toLowerCase();

                        // Search
                        if (q) {
                            result = result.filter(m =>
                                m.nama.toLowerCase().includes(q) ||
                                m.nim.toLowerCase().includes(q)
                            );
                        }

                        // Status Filter
                        if (this.filterStatus !== 'all') {
                            result = result.filter(m => m.status_kp === this.filterStatus);
                        }

                        // Sorting
                        result = result.sort((a, b) => {
                            if (this.sortOrder === 'asc') return a.nama.localeCompare(b.nama);
                            if (this.sortOrder === 'desc') return b.nama.localeCompare(a.nama);
                            if (this.sortOrder === 'progress_desc') return b.progress - a.progress;
                            return 0;
                        });

                        return result;
                    },

                    getStatusLabel(status) {
                        const labels = {
                            'Pra-KP': 'Pra-KP',
                            'Saat KP': 'Saat KP',
                            'Pasca KP': 'Pasca KP',
                            'Selesai': 'Selesai KP',
                            'Dibatalkan': 'Dibatalkan',
                            'Gagal': 'Gagal'
                        };
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