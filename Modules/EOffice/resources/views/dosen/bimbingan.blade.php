<x-eoffice::layouts.dosen title="Bimbingan Mahasiswa">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Bimbingan Mahasiswa</span>
    @endsection
    <div x-data="bimbinganApp()" x-init="initData({{ json_encode($mahasiswas) }})" class="max-w-7xl mx-auto">

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
                    <option value="active">Aktif KP</option>
                    <option value="pending">Menunggu Seminar</option>
                    <option value="completed">Selesai KP</option>
                    <option value="revisi">Revisi</option>
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
                                Laporan</th>
                            <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Seminar</th>
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
                                    <template x-if="item.nilai_laporan !== null && item.nilai_laporan !== undefined">
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-extrabold text-lg border border-blue-100 shadow-sm"
                                            x-text="item.nilai_laporan"></span>
                                    </template>
                                    <template x-if="item.nilai_laporan === null || item.nilai_laporan === undefined">
                                        <span class="text-xs font-medium text-slate-400 italic">Belum</span>
                                    </template>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <template x-if="item.nilai_seminar !== null">
                                        <span
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-lg border border-emerald-100 shadow-sm"
                                            x-text="item.nilai_seminar"></span>
                                    </template>
                                    <template x-if="item.nilai_seminar === null">
                                        <span class="text-xs font-medium text-slate-400 italic">Belum</span>
                                    </template>
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


    <!-- Side Detail Drawer -->
    <div x-cloak x-show="drawerOpen" class="relative z-50" aria-labelledby="slide-over-title" role="dialog"
        aria-modal="true">
        <div x-show="drawerOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeDrawer()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div x-show="drawerOpen"
                        x-transition:enter="transform transition ease-out duration-300 sm:duration-400"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in duration-300"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        class="pointer-events-auto w-screen max-w-xl">

                        <div
                            class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-200">

                            <!-- Drawer Header -->
                            <div
                                class="px-6 py-6 border-b border-slate-100 bg-slate-50/50 sticky top-0 z-10 backdrop-blur-md flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900" id="slide-over-title">Detail
                                        Mahasiswa Bimbingan</h2>
                                    <p class="text-sm text-slate-500 mt-1">Informasi lengkap dan progres Kerja
                                        Praktik.</p>
                                </div>
                                <button type="button" @click="closeDrawer()"
                                    class="rounded-lg bg-white p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body -->
                            <div class="flex-1 px-6 py-6 space-y-8" x-show="selectedData">

                                <!-- Profile Section -->
                                <div class="flex items-start gap-5">
                                    <div
                                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-100 to-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl shrink-0 border border-indigo-200 shadow-sm">
                                        <span x-text="selectedData?.nama?.charAt(0)"></span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-slate-900" x-text="selectedData?.nama">
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-sm font-medium text-slate-600"
                                                x-text="selectedData?.nim"></span>
                                        </div>
                                        <div class="mt-3">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide uppercase border"
                                                :class="getStatusBadgeClass(selectedData?.status_kp)">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                    :class="getStatusDotClass(selectedData?.status_kp)"></span>
                                                <span x-text="getStatusLabel(selectedData?.status_kp)"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Informasi KP -->
                                <!-- Detail Informasi KP -->
                                <div>
                                    <h4
                                        class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">
                                        Informasi Kerja Praktik</h4>
                                    <div class="space-y-4">

                                        <!-- Judul KP - samping -->
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                                            <div class="text-sm font-medium text-slate-500 sm:col-span-1">Judul KP
                                            </div>
                                            <div class="text-sm font-semibold text-slate-900 sm:col-span-2 leading-relaxed"
                                                x-text="selectedData?.judul_kp"></div>
                                        </div>

                                        <!-- Tempat KP - samping -->
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                                            <div class="text-sm font-medium text-slate-500 sm:col-span-1">Tempat KP
                                            </div>
                                            <div class="text-sm font-semibold text-slate-900 sm:col-span-2"
                                                x-text="selectedData?.tempat_kp"></div>
                                        </div>

                                        <!-- Tanggal Mulai & Selesai - berdampingan vertikal dalam 2 kolom -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                                                <p class="text-xs font-medium text-slate-500 mb-1">Tanggal Mulai</p>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-bold text-slate-800"
                                                        x-text="selectedData?.tanggal_mulai ?? '-'"></span>
                                                </div>
                                            </div>
                                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                                                <p class="text-xs font-medium text-slate-500 mb-1">Tanggal Selesai
                                                </p>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-bold text-slate-800"
                                                        x-text="selectedData?.tanggal_selesai ?? '-'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Dokumen - vertikal -->
                                        <div>
                                            <p class="text-xs font-medium text-slate-500 mb-1.5">Status Dokumen</p>
                                            <span
                                                class="inline-flex items-center text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-xs font-bold border border-emerald-100"
                                                x-show="selectedData?.status_dokumen === 'Lengkap'">Lengkap</span>
                                            <span
                                                class="inline-flex items-center text-amber-600 bg-amber-50 px-2 py-0.5 rounded text-xs font-bold border border-amber-100"
                                                x-show="selectedData?.status_dokumen !== 'Lengkap'"
                                                x-text="selectedData?.status_dokumen"></span>
                                        </div>

                                    </div>
                                </div>

                                <!-- Progress Tracker -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                            Progress Bimbingan</h4>
                                        <span class="text-sm font-bold text-indigo-600"
                                            x-text="selectedData?.progress + '%'"></span>
                                    </div>
                                    <div
                                        class="w-full bg-slate-100 rounded-full h-2.5 mb-6 border border-slate-200 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-500 h-2.5 rounded-full transition-all duration-1000"
                                            :style="'width: ' + selectedData?.progress + '%'"></div>
                                    </div>

                                    <!-- Simple Timeline -->
                                    <div class="relative border-l-2 border-slate-100 ml-3 space-y-6">
                                        <div class="relative pl-6">
                                            <span
                                                class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full bg-indigo-500 ring-4 ring-white"></span>
                                            <p class="text-xs font-bold text-indigo-600 uppercase mb-0.5">Pra KP</p>
                                            <p class="text-sm font-medium text-slate-900">Pendaftaran & Topik
                                                Disetujui</p>
                                            <p class="text-xs text-slate-500 mt-1">Selesai</p>
                                        </div>
                                        <div class="relative pl-6">
                                            <span class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full"
                                                :class="selectedData?.progress >= 50 ? 'bg-indigo-500 ring-4 ring-white' : 'bg-slate-200 ring-4 ring-white'"></span>
                                            <p class="text-xs font-bold uppercase mb-0.5"
                                                :class="selectedData?.progress >= 50 ? 'text-indigo-600' : 'text-slate-400'">
                                                Saat KP</p>
                                            <p class="text-sm font-medium"
                                                :class="selectedData?.progress >= 50 ? 'text-slate-900' : 'text-slate-500'">
                                                Pelaksanaan & Bimbingan Laporan</p>
                                        </div>
                                        <div class="relative pl-6">
                                            <span class="absolute -left-1.5 top-1.5 w-3 h-3 rounded-full"
                                                :class="selectedData?.progress >= 100 ? 'bg-emerald-500 ring-4 ring-white' : 'bg-slate-200 ring-4 ring-white'"></span>
                                            <p class="text-xs font-bold uppercase mb-0.5"
                                                :class="selectedData?.progress >= 100 ? 'text-emerald-600' : 'text-slate-400'">
                                                Pasca KP</p>
                                            <p class="text-sm font-medium"
                                                :class="selectedData?.progress >= 100 ? 'text-slate-900' : 'text-slate-500'">
                                                Seminar & Penilaian</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Nilai Seminar Section -->
                                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900">Penilaian Seminar Dosen
                                            </h4>
                                            <p class="text-xs text-slate-500">Input atau ubah nilai seminar
                                                mahasiswa.</p>
                                        </div>
                                    </div>

                                    <!-- Form actually posts to the backend route if we submit properly, but for UI demo we use Alpine.prevent to show toast -->
                                    <form @submit.prevent="submitNilai">
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Nilai
                                                    Laporan (0-100) <span class="text-red-500">*</span></label>
                                                <input type="number" x-model="formNilai.laporan" min="0" max="100"
                                                    required
                                                    class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-center text-lg font-bold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 border bg-white outline-none transition-all"
                                                    placeholder="0" :disabled="isSubmitting">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-2">Nilai
                                                    Seminar (0-100) <span class="text-red-500">*</span></label>
                                                <input type="number" x-model="formNilai.seminar" min="0" max="100"
                                                    required
                                                    class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-center text-lg font-bold focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 border bg-white outline-none transition-all"
                                                    placeholder="0" :disabled="isSubmitting">
                                            </div>
                                        </div>

                                        <div class="flex justify-end gap-3">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 outline-none w-full sm:w-auto disabled:opacity-70 disabled:cursor-not-allowed"
                                                :disabled="isSubmitting">
                                                <svg x-show="isSubmitting"
                                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                <span x-show="!isSubmitting">Simpan Nilai</span>
                                                <span x-show="isSubmitting">Menyimpan...</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
                            'active': 'Aktif KP',
                            'pending': 'Menunggu Seminar',
                            'completed': 'Selesai KP',
                            'revisi': 'Revisi'
                        };
                        return labels[status] || status;
                    },

                    getStatusBadgeClass(status) {
                        const classes = {
                            'active': 'text-indigo-600 bg-indigo-50 border-indigo-100',
                            'pending': 'text-amber-600 bg-amber-50 border-amber-100',
                            'completed': 'text-emerald-600 bg-emerald-50 border-emerald-100',
                            'revisi': 'text-red-600 bg-red-50 border-red-100'
                        };
                        return classes[status] || 'text-slate-600 bg-slate-50 border-slate-200';
                    },

                    getStatusDotClass(status) {
                        const classes = {
                            'active': 'bg-indigo-500',
                            'pending': 'bg-amber-500',
                            'completed': 'bg-emerald-500',
                            'revisi': 'bg-red-500'
                        };
                        return classes[status] || 'bg-slate-400';
                    },

                    openDrawer(data) {
                        this.selectedData = data;
                        this.formNilai.laporan = data.nilai_laporan;
                        this.formNilai.seminar = data.nilai_seminar;
                        this.drawerOpen = true;
                    },

                    closeDrawer() {
                        this.drawerOpen = false;
                        // setTimeout(() => { this.selectedData = null; }, 300);
                    },

                    simpanNilai() {
                        if (!this.formNilai.laporan || !this.formNilai.seminar) {
                            this.showToast('error', 'Semua form nilai harus diisi dengan angka.');
                            return;
                        }

                        // Simulate Confirm Modal
                        if (!confirm(`Simpan nilai Laporan (${this.formNilai.laporan}) & Seminar (${this.formNilai.seminar}) untuk mahasiswa ${this.selectedData.nama}?`)) return;

                        this.isSubmitting = true;

                        // Simulate save
                        setTimeout(() => {
                            this.isSubmitting = false;
                            this.closeDrawer();
                            this.showToast('success', `Nilai ${this.selectedData.nama} berhasil disimpan.`);
                        }, 800);
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