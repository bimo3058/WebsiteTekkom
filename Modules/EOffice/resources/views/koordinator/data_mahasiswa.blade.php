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
                <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa.export') }}"
                    class="flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm text-sm font-semibold">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Controls: Search & Filter (Mirrors Periode Mockup) -->
        <div
            style="margin-bottom:16px; display:flex; gap:16px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
            <!-- Search Input -->
            <div style="position:relative; flex:1; min-width:260px; max-width:320px;">
                <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#98A2B3;"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" placeholder="Cari nama atau NIM..." x-model="search" style="
                        width:100%;
                        padding:8px 12px 8px 36px;
                        border:1px solid #E4E7EC;
                        border-radius:8px;
                        font-family:'Inter Tight',sans-serif;
                        font-size:14px;
                        outline:none;
                        transition:border-color 0.2s;
                    " onfocus="this.style.borderColor='#A8B4FB'" onblur="this.style.borderColor='#E4E7EC'">
            </div>

            <!-- Filters -->
            <div style="display:flex; gap:12px; align-items:center;">
                <!-- Filter Dropdown -->
                <div x-data="{ openFilter: false }" style="position:relative;">
                    <button @click="openFilter = !openFilter" @click.away="openFilter = false" style="
                        display:inline-flex; align-items:center; gap:8px;
                        padding:8px 14px;
                        background:#fff; border:1px solid #E4E7EC; border-radius:8px;
                        font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                        transition:background 0.2s;
                        white-space:nowrap;
                    " class="hover:bg-gray-50">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filter Status
                    </button>
                    <!-- Dropdown Content (Status Filter) -->
                    <div x-show="openFilter"
                        style="display:none; position:absolute; right:0; top:100%; margin-top:8px; width:180px; background:#fff; border:1px solid #E4E7EC; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); z-index:50;"
                        x-transition>
                        <div style="padding:4px;">
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md">
                                <input type="radio" x-model="filterStatus" value="" name="status_kp"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Semua
                                    Status</span>
                            </label>
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md">
                                <input type="radio" x-model="filterStatus" value="Pra KP" name="status_kp"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Pra
                                    KP</span>
                            </label>
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md">
                                <input type="radio" x-model="filterStatus" value="Saat KP" name="status_kp"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Saat
                                    KP</span>
                            </label>
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md">
                                <input type="radio" x-model="filterStatus" value="Pasca KP" name="status_kp"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Pasca
                                    KP</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Filter Periode -->
                <div x-data="{ openPeriode: false }" style="position:relative;">
                    <button @click="openPeriode = !openPeriode" @click.away="openPeriode = false" style="
                        display:inline-flex; align-items:center; gap:8px;
                        padding:8px 14px;
                        background:#fff; border:1px solid #E4E7EC; border-radius:8px;
                        font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                        transition:background 0.2s;
                        white-space:nowrap;
                    " class="hover:bg-gray-50">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Filter Periode
                    </button>
                    <!-- Dropdown Content (Periode Filter) -->
                    <div x-show="openPeriode"
                        style="display:none; position:absolute; right:0; top:100%; margin-top:8px; width:220px; background:#fff; border:1px solid #E4E7EC; border-radius:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); z-index:50;"
                        x-transition>
                        <div style="padding:4px; max-height: 250px; overflow-y: auto;">
                            <!-- Opsi Semua Periode -->
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md text-left">
                                <input type="radio" x-model="filterPeriode" value="" name="filter_periode"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Semua
                                    Periode</span>
                            </label>

                            @foreach($periodes as $p)
                                <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                    class="hover:bg-gray-50 rounded-md text-left">
                                    <input type="radio" x-model="filterPeriode" value="{{ $p->id }}" name="filter_periode"
                                        class="text-indigo-600 focus:ring-indigo-500">
                                    <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">
                                        Semester {{ $p->semester }} {{ $p->tahun_ajaran }}
                                    </span>
                                </label>
                            @endforeach
                            <!-- Fallback for unknown items -->
                            <label style="display:flex; align-items:center; padding:8px 12px; gap:8px; cursor:pointer;"
                                class="hover:bg-gray-50 rounded-md text-left">
                                <input type="radio" x-model="filterPeriode" value="unknown" name="filter_periode"
                                    class="text-indigo-600 focus:ring-indigo-500">
                                <span style="font-family:'Inter Tight',sans-serif; font-size:13px; color:#4B5563;">Tidak
                                    Terklasifikasi</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sort Button Placeholder (Cosmetic) -->
                <button type="button" style="
                    display:inline-flex; align-items:center; gap:8px;
                    padding:8px 14px;
                    background:#fff; border:1px solid #E4E7EC; border-radius:8px;
                    font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#4B5563;
                    transition:background 0.2s;
                    white-space:nowrap;
                " class="hover:bg-gray-50">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                    Sort by
                </button>
            </div>
        </div>

        <!-- Table Area -->
        <div
            style="background:#fff; border:1px solid #EAECF0; border-radius:12px; box-shadow:0 1px 2px rgba(16, 24, 40, 0.05); overflow:hidden;">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead class="sticky-header bg-slate-50 shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                        <tr>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 whitespace-nowrap"
                                style="width:22%">
                                Mahasiswa</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 whitespace-nowrap"
                                style="width:16%">
                                Tempat KP</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:10%">
                                Nilai Lap.</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:10%">
                                Nilai Sem.</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:10%">
                                Nilai Lap.</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:10%">
                                Nilai Akhir</th>
                            <th class="py-3 px-3 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:12%">
                                Status KP</th>
                        </tr>
                    </thead>
                    <template x-for="m in filteredMahasiswas" :key="m.id">
                        <tbody x-data="{ expanded: false }" class="border-b border-slate-100">
                            <tr @click="expanded = !expanded"
                                class="hover:bg-slate-50/80 transition-colors cursor-pointer group">
                                <td class="py-3 px-3 align-middle">
                                    <div class="flex items-center gap-2">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24" :class="expanded ? 'rotate-180' : ''"
                                            class="transition-transform duration-200 text-[#666D80] shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7">
                                            </path>
                                        </svg>
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs flex-shrink-0"
                                            x-text="m.nama.charAt(0)"></div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate" x-text="m.nama"></p>
                                            <p class="text-[10px] text-slate-500 mt-0.5" x-text="m.nim"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 align-middle">
                                    <p class="text-xs font-semibold text-slate-700 truncate" :title="m.tempat_kp"
                                        x-text="m.tempat_kp"></p>
                                </td>
                                <td class="py-3 px-3 align-middle text-center">
                                    <span x-show="m.nilai_lapangan !== null && m.nilai_lapangan !== undefined"
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100"
                                        x-text="m.nilai_lapangan"></span>
                                    <span x-show="m.nilai_lapangan === null || m.nilai_lapangan === undefined"
                                        class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3 px-3 align-middle text-center">
                                    <span x-show="m.nilai_seminar !== null"
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100"
                                        x-text="m.nilai_seminar"></span>
                                    <span x-show="m.nilai_seminar === null" class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3 px-3 align-middle text-center">
                                    <span x-show="m.nilai_laporan !== null"
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100"
                                        x-text="m.nilai_laporan"></span>
                                    <span x-show="m.nilai_laporan === null" class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3 px-3 align-middle text-center">
                                    <span x-show="m.nilai_akhir !== null"
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700"
                                        x-text="m.nilai_akhir"></span>
                                    <span x-show="m.nilai_akhir === null" class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3 px-3 align-middle text-center">
                                    <span :class="{
                                        'bg-emerald-100 text-emerald-700 border-emerald-200': m.status_kp === 'Pasca KP',
                                        'bg-amber-100 text-amber-700 border-amber-200': m.status_kp === 'Saat KP',
                                        'bg-slate-100 text-slate-600 border-slate-200': m.status_kp === 'Pra KP',
                                    }" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border whitespace-nowrap"
                                        x-text="m.status_kp"></span>
                                </td>
                            </tr>

                            <!-- Expandable Details Row -->
                            <tr x-show="expanded" x-cloak x-transition>
                                <td colspan="7" class="p-0 border-b border-[#F1F1F3] bg-[#FAFAFC]">
                                    <div class="flex flex-col md:flex-row gap-4 px-4 py-3" style="padding-left: 56px;">
                                        <!-- Kiri: Judul KP & Dosen -->
                                        <div class="flex-[2] flex flex-col gap-3">
                                            <div>
                                                <p
                                                    style="font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                    Periode</p>
                                                <p class="text-xs font-semibold text-slate-700" x-text="m.periode_name">
                                                </p>
                                            </div>
                                            <div>
                                                <p
                                                    style="font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                    Judul Kerja Praktik</p>
                                                <p class="text-xs font-semibold text-slate-800 leading-relaxed"
                                                    x-text="m.judul_kp || 'Belum diisi'"></p>
                                            </div>
                                            <div>
                                                <p
                                                    style="font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                    Dosen Pembimbing</p>
                                                <p class="text-xs font-semibold text-slate-800"
                                                    x-text="m.dosen_pembimbing || 'Belum diplot'"></p>
                                            </div>
                                        </div>
                                        <!-- Kanan: Status Proses -->
                                        <div class="flex-1 flex flex-col gap-3">
                                            <div>
                                                <p
                                                    style="font-size:10px; font-weight:700; color:#A0AABF; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:4px;">
                                                    Status Proses</p>
                                                <div class="flex gap-1.5 flex-wrap">
                                                    <span
                                                        :class="{ 'bg-emerald-50 text-emerald-700': m.status_dokumen === 'Lengkap', 'bg-rose-50 text-rose-700': m.status_dokumen === 'Tidak Lengkap' }"
                                                        class="px-2 py-0.5 text-[10px] font-bold border rounded"
                                                        x-text="'Dok: ' + m.status_dokumen"></span>
                                                    <span :class="{
                                                           'bg-emerald-50 text-emerald-700 border-emerald-200': m.status_seminar === 'Lulus',
                                                           'bg-amber-50 text-amber-700 border-amber-200': m.status_seminar === 'Menunggu Jadwal',
                                                           'bg-slate-50 text-slate-600 border-slate-200': m.status_seminar === 'Belum Daftar'
                                                      }" class="px-2 py-0.5 text-[10px] font-bold border rounded"
                                                        x-text="'Seminar: ' + m.status_seminar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </template>

                    <!-- Empty State -->
                    <tbody x-show="filteredMahasiswas.length === 0" x-cloak>
                        <tr>
                            <td colspan="8" class="py-12 px-6 text-center">
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



    </div>

    @push('scripts')
        <script>         function pageData() {
                return {
                    sidebarOpen: true, search: '', filterStatus: '', filterPeriode: '', mahasiswas: @json($mahasiswas), periodes: @json($periodes), selectedMahasiswa: null, detailModal: false, toast: { show: false, type: 'success', title: '', message: '' }, get filteredMahasiswas() {
                        let filtered = this.mahasiswas;
                        if (this.filterPeriode !== '') { filtered = filtered.filter(m => m.periode_id == this.filterPeriode); }
                        if (this.filterStatus !== '') { filtered = filtered.filter(m => m.status_kp === this.filterStatus); }
                        return filtered.filter(m => { let matchSearch = m.nama.toLowerCase().includes(this.search.toLowerCase()) || m.nim.includes(this.search); return matchSearch; });
                    }, showToast(type, title, message) { this.toast.type = type; this.toast.title = title; this.toast.message = message; this.toast.show = true; setTimeout(() => { this.toast.show = false; }, 3000); }
                }
            }
        </script>
    @endpush

</x-eoffice::layouts.koordinator>