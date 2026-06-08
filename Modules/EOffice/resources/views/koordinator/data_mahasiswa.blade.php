<x-eoffice::layouts.koordinator title="Data Mahasiswa">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Data Mahasiswa</span>
    @endsection

    @push('styles')
        <style>
            .sticky-header th {
                position: sticky;
                top: 0;
                z-index: 10;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    <div x-data="pageData()">

        <!-- Toast Notification -->
        <div x-show="toast.show" x-cloak x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="fixed top-20 right-6 lg:right-10 z-50 bg-white border shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]"
            :class="toast.type === 'success' ? 'border-emerald-100' : 'border-red-100'">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5"
                :class="toast.type === 'success' ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-900 mb-0.5" x-text="toast.title"></p>
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
                <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Data Mahasiswa</h1>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed">Kelola dan monitor data mahasiswa kerja praktik
                    secara menyeluruh.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm text-sm font-semibold">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Filters & Search Bar -->
        <div
            class="bg-white rounded-t-2xl border border-slate-200 border-b-0 p-5 flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input x-model="search" type="text" placeholder="Cari nama atau NIM mahasiswa..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-inner text-slate-800 placeholder-slate-400">
            </div>
            <div class="flex w-full md:w-auto items-center gap-3">
                <select x-model="filterStatus"
                    class="w-full md:w-48 bg-white border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium">
                    <option value="">Semua Status KP</option>
                    <option value="Aktif KP">Aktif KP</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Menunggu Nilai">Menunggu Nilai</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div>

        <!-- Table Area -->
        <div class="bg-white border border-slate-200 rounded-b-2xl shadow-sm overflow-hidden relative">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead class="sticky-header bg-slate-50 shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                        <tr>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                                Mahasiswa</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                                Tempat KP</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">
                                Pembimbing</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">
                                Nilai Lapangan</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">
                                Nilai Seminar</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">
                                Nilai Laporan</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">
                                Nilai Akhir</th>
                            <th
                                class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">
                                Status KP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="m in filteredMahasiswas" :key="m.id">
                            <tr @click="openDetail(m)"
                                class="hover:bg-slate-50/80 transition-colors cursor-pointer group">
                                <td class="py-4 px-6 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0 group-hover:scale-105 transition-transform"
                                            x-text="m.nama.charAt(0)"></div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900" x-text="m.nama"></p>
                                            <p class="text-xs text-slate-500" x-text="m.nim"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 align-middle">
                                    <p class="text-sm font-semibold text-slate-700" x-text="m.tempat_kp"></p>
                                </td>
                                <td class="py-4 px-6 align-middle">
                                    <p class="text-sm text-slate-600" x-text="m.dosen_pembimbing || 'Belum diplot'"></p>
                                </td>
                                <td class="py-4 px-6 align-middle text-center">
                                    <span x-show="m.nilai_lapangan !== null && m.nilai_lapangan !== undefined"
                                        class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100"
                                        x-text="m.nilai_lapangan"></span>
                                    <span x-show="m.nilai_lapangan === null || m.nilai_lapangan === undefined"
                                        class="text-slate-400 text-xs italic">Belum</span>
                                </td>
                                <td class="py-4 px-6 align-middle text-center">
                                    <span x-show="m.nilai_seminar !== null"
                                        class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100"
                                        x-text="m.nilai_seminar"></span>
                                    <span x-show="m.nilai_seminar === null"
                                        class="text-slate-400 text-xs italic">Belum</span>
                                </td>
                                <td class="py-4 px-6 align-middle text-center">
                                    <span x-show="m.nilai_laporan !== null"
                                        class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100"
                                        x-text="m.nilai_laporan"></span>
                                    <span x-show="m.nilai_laporan === null"
                                        class="text-slate-400 text-xs italic">Belum</span>
                                </td>
                                <td class="py-4 px-6 align-middle text-center">
                                    <span x-show="m.nilai_akhir !== null"
                                        class="inline-flex items-center justify-center px-3 py-1 rounded-md text-sm font-bold bg-indigo-50 text-indigo-700"
                                        x-text="m.nilai_akhir"></span>
                                    <span x-show="m.nilai_akhir === null" class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-4 px-6 align-middle text-center">
                                    <span :class="{
                                        'bg-emerald-100 text-emerald-700 border-emerald-200': m.status_kp === 'Selesai',
                                        'bg-amber-100 text-amber-700 border-amber-200': m.status_kp === 'Aktif KP',
                                        'bg-blue-100 text-blue-700 border-blue-200': m.status_kp === 'Seminar',
                                        'bg-slate-100 text-slate-600 border-slate-200': m.status_kp === 'Menunggu Nilai',
                                        'bg-rose-100 text-rose-700 border-rose-200': m.status_kp === 'Pending',
                                    }" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border"
                                        x-text="m.status_kp"></span>
                                </td>
                            </tr>
                        </template>
                        <!-- Empty State -->
                        <tr x-show="filteredMahasiswas.length === 0" x-cloak>
                            <td colspan="7" class="py-12 px-6 text-center">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 mb-1">Data Tidak Ditemukan</h3>
                                <p class="text-sm text-slate-500">Tidak ada mahasiswa yang sesuai dengan pencarian Anda.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                <p class="text-sm text-slate-500 font-medium">Menampilkan <span class="font-bold text-slate-700"
                        x-text="filteredMahasiswas.length"></span> data</p>
                <div class="flex items-center gap-1">
                    <button
                        class="p-2 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        class="px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm font-bold">1</button>
                    <button
                        class="p-2 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                        disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Drawer (Slide over) -->
        <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title"
            role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <div x-show="detailModal" x-transition.opacity
                    class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                    @click="detailModal = false"></div>
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="detailModal" x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        class="pointer-events-auto w-screen max-w-md">
                        <div class="flex h-full flex-col bg-white shadow-2xl">
                            <template x-if="selectedMahasiswa">
                                <div class="flex h-full flex-col">
                                    <div class="px-6 py-6 border-b border-slate-100 bg-slate-50/50 relative">
                                        <div class="absolute right-6 top-6">
                                            <button @click="detailModal = false" type="button"
                                                class="rounded-md bg-white text-slate-400 hover:text-slate-500 hover:bg-slate-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-4 pr-10">
                                            <div
                                                class="w-14 h-14 rounded-2xl bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-extrabold text-xl shadow-sm">
                                                <span x-text="selectedMahasiswa.nama.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <h2 class="text-xl font-bold text-slate-900"
                                                    x-text="selectedMahasiswa.nama"></h2>
                                                <p class="text-sm text-slate-500 font-medium"
                                                    x-text="selectedMahasiswa.nim"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative flex-1 overflow-y-auto px-6 py-6">
                                        <div class="flex flex-wrap gap-2 mb-8">
                                            <span :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': selectedMahasiswa.status_dokumen === 'Lengkap',
                                                'bg-rose-50 text-rose-700 border-rose-200': selectedMahasiswa.status_dokumen === 'Tidak Lengkap',
                                            }" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold border"
                                                x-text="'Dokumen: ' + selectedMahasiswa.status_dokumen"></span>
                                            <span :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': selectedMahasiswa.status_seminar === 'Lulus',
                                                'bg-amber-50 text-amber-700 border-amber-200': selectedMahasiswa.status_seminar === 'Menunggu Jadwal',
                                                'bg-slate-50 text-slate-600 border-slate-200': selectedMahasiswa.status_seminar === 'Belum Daftar',
                                            }" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold border"
                                                x-text="'Seminar: ' + selectedMahasiswa.status_seminar"></span>
                                        </div>
                                        <div class="mb-8">
                                            <h3
                                                class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                                                </svg>
                                                Informasi Kerja Praktik
                                            </h3>
                                            <dl class="space-y-4">
                                                <div>
                                                    <dt class="text-xs font-medium text-slate-500 mb-1">Tempat KP</dt>
                                                    <dd class="text-sm font-semibold text-slate-800"
                                                        x-text="selectedMahasiswa.tempat_kp"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-slate-500 mb-1">Judul KP</dt>
                                                    <dd class="text-sm font-semibold text-slate-800 leading-relaxed"
                                                        x-text="selectedMahasiswa.judul_kp"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-medium text-slate-500 mb-1">Dosen Pembimbing
                                                    </dt>
                                                    <dd class="text-sm font-semibold text-slate-800"
                                                        x-text="selectedMahasiswa.dosen_pembimbing || 'Belum diplot'">
                                                    </dd>
                                                </div>
                                            </dl>
                                        </div>
                                        <div class="mb-8">
                                            <h3
                                                class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                Rekap Penilaian
                                            </h3>
                                            <div class="grid grid-cols-3 gap-3 mb-3">
                                                <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-100">
                                                    <p class="text-[10px] font-medium text-emerald-700 mb-1">Nilai
                                                        Seminar</p>
                                                    <p class="text-xl font-extrabold text-emerald-800"
                                                        x-text="selectedMahasiswa.nilai_seminar !== null ? selectedMahasiswa.nilai_seminar : '-'">
                                                    </p>
                                                    <p class="text-[9px] text-emerald-600 mt-0.5">Oleh Dosen</p>
                                                </div>
                                                <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                                    <p class="text-[10px] font-medium text-blue-700 mb-1">Nilai Laporan
                                                    </p>
                                                    <p class="text-xl font-extrabold text-blue-800"
                                                        x-text="selectedMahasiswa.nilai_laporan !== null ? selectedMahasiswa.nilai_laporan : '-'">
                                                    </p>
                                                    <p class="text-[9px] text-blue-600 mt-0.5">Oleh Dosen</p>
                                                </div>
                                                <div class="bg-amber-50 p-3 rounded-xl border border-amber-100">
                                                    <p class="text-[10px] font-medium text-amber-700 mb-1">Nilai
                                                        Lapangan</p>
                                                    <p class="text-xl font-extrabold text-amber-800"
                                                        x-text="selectedMahasiswa.nilai_lapangan !== null && selectedMahasiswa.nilai_lapangan !== undefined ? selectedMahasiswa.nilai_lapangan : '-'">
                                                    </p>
                                                    <p class="text-[9px] text-amber-600 mt-0.5">Oleh Koordinator</p>
                                                </div>
                                            </div>
                                            <div
                                                class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 flex items-center justify-between">
                                                <div>
                                                    <p
                                                        class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-0.5">
                                                        Nilai Akhir</p>
                                                    <p class="text-[10px] text-indigo-500 font-medium">Hasil kalkulasi
                                                        akhir</p>
                                                </div>
                                                <p class="text-3xl font-black text-indigo-700"
                                                    x-text="selectedMahasiswa.nilai_akhir !== null ? selectedMahasiswa.nilai_akhir : '-'">
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function pageData() {
                return {
                    sidebarOpen: true,
                    search: '',
                    filterStatus: '',
                    mahasiswas: @json($mahasiswas),
                    selectedMahasiswa: null,
                    detailModal: false,
                    toast: { show: false, type: 'success', title: '', message: '' },
                    get filteredMahasiswas() {
                        return this.mahasiswas.filter(m => {
                            let matchSearch = m.nama.toLowerCase().includes(this.search.toLowerCase()) || m.nim.includes(this.search);
                            let matchStatus = this.filterStatus === '' || m.status_kp === this.filterStatus;
                            return matchSearch && matchStatus;
                        });
                    },
                    openDetail(m) {
                        this.selectedMahasiswa = m;
                        this.detailModal = true;
                    },
                    showToast(type, title, message) {
                        this.toast.type = type;
                        this.toast.title = title;
                        this.toast.message = message;
                        this.toast.show = true;
                        setTimeout(() => { this.toast.show = false; }, 3000);
                    }
                }
            }
        </script>
    @endpush

</x-eoffice::layouts.koordinator>