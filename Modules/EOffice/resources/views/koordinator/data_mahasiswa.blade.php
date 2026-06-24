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

    <div x-data="pageData()"
        @open-edit-modal.window="selectedMahasiswa = $event.detail; detailModal = true; modalTab = 'profil'">

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
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 whitespace-nowrap"
                                style="width:25%">
                                Mahasiswa</th>
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 whitespace-nowrap"
                                style="width:20%">
                                Periode</th>
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:12%">
                                Kelas</th>
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:13%">
                                Nilai Akhir</th>
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:15%">
                                Status KP</th>
                            <th class="py-3 px-4 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center whitespace-nowrap"
                                style="width:15%">
                                Action</th>
                        </tr>
                    </thead>
                    <template x-for="m in filteredMahasiswas" :key="m.id">
                        <tbody x-data="{ expanded: false }" class="border-b border-slate-100">
                            <!-- Main Row -->
                            <tr @click="expanded = !expanded"
                                class="hover:bg-slate-50/80 transition-colors cursor-pointer group"
                                :class="expanded ? 'bg-indigo-50/30' : ''">
                                <td class="py-3 px-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24"
                                            :class="expanded ? 'rotate-90 text-indigo-500' : 'text-slate-400'"
                                            class="transition-transform duration-200 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7">
                                            </path>
                                        </svg>
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs flex-shrink-0"
                                            x-text="m.nama.charAt(0)"></div>
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-bold text-slate-900 truncate" x-text="m.nama">
                                            </p>
                                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="m.nim"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 align-middle">
                                    <p class="text-xs font-semibold text-slate-700 truncate" x-text="m.periode_name">
                                    </p>
                                </td>
                                <td class="py-3 px-4 align-middle text-center">
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200"
                                        x-text="m.kelas"></span>
                                </td>
                                <td class="py-3 px-4 align-middle text-center">
                                    <span x-show="m.nilai_akhir !== null"
                                        class="inline-flex items-center justify-center px-2 py-1 rounded text-[13px] font-black bg-indigo-100 text-indigo-700 shadow-sm"
                                        x-text="m.nilai_akhir"></span>
                                    <span x-show="m.nilai_akhir === null" class="text-slate-400 text-xs">-</span>
                                </td>
                                <td class="py-3 px-4 align-middle text-center">
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="{
                                            'bg-amber-50 text-amber-600': m.status_kp === 'Pra KP',
                                            'bg-blue-50 text-blue-600': m.status_kp === 'Saat KP',
                                            'bg-emerald-50 text-emerald-600': m.status_kp === 'Pasca KP'
                                        }" x-text="m.status_kp">
                                    </span>
                                </td>
                                <td class="py-3 px-4 align-middle text-center relative" x-data="{ menuOpen: false }"
                                    @click.outside="menuOpen = false">
                                    <div class="flex items-center justify-center pointer-events-auto" @click.stop>
                                        <button type="button" @click="menuOpen = !menuOpen"
                                            class="p-1 px-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                                </path>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div x-show="menuOpen" style="display:none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-10 top-10 mt-0 w-32 bg-white rounded-lg shadow-lg border border-slate-100 overflow-hidden z-50 text-left">

                                            <button type="button"
                                                @click="$dispatch('open-edit-modal', m); menuOpen = false"
                                                class="w-full text-left px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                                Edit
                                            </button>
                                            <form
                                                :action="`{{ route('eoffice.kp.koordinator.pendaftar.destroy', 'DUMMY_ID') }}`.replace('DUMMY_ID', m.id)"
                                                method="POST" class="inline w-full m-0 p-0"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus / mereset data mahasiswa ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Expandable Detail Row -->
                            <tr x-show="expanded" x-transition x-cloak
                                class="bg-slate-50 border-b border-slate-200 overflow-hidden shadow-inner">
                                <td colspan="6" class="p-0">
                                    <div class="px-8 py-5 grid grid-cols-1 lg:grid-cols-2 gap-x-16 gap-y-6">
                                        <!-- Left Column: PROFIL KERJA PRAKTIK -->
                                        <div>
                                            <div class="space-y-2.5">
                                                <div>
                                                    <span class="block text-[13px] font-medium text-slate-400 mb-0.5">Judul Laporan</span>
                                                    <span class="block text-[13px] font-semibold text-slate-800 leading-snug" x-text="m.judul_kp || '-'"></span>
                                                </div>
                                                <div>
                                                    <span class="block text-[13px] font-medium text-slate-400 mb-0.5">Instansi</span>
                                                    <span class="block text-[13px] font-semibold text-slate-800 leading-snug" x-text="m.tempat_kp || '-'"></span>
                                                </div>
                                                <div>
                                                    <span class="block text-[13px] font-medium text-slate-400 mb-0.5">Dosen Pembimbing</span>
                                                    <span class="block text-[13px] font-semibold text-slate-800 leading-snug" x-text="m.dosen_pembimbing || 'Belum di-assign'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: KOMPONEN PENILAIAN -->
                                        <div>
                                            <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Komponen Penilaian</h4>
                                            <div class="space-y-1.5">
                                                <!-- Dynamic Values -->
                                                <template x-if="m.semua_nilai && m.semua_nilai.length > 0">
                                                    <div>
                                                        <template x-for="n in m.semua_nilai" :key="n.nama">
                                                            <div class="flex justify-between items-center gap-4 py-0.5">
                                                                <span class="text-[13px] font-medium text-slate-400" x-text="n.nama"></span>
                                                                <span class="text-[13px] font-bold text-slate-800" x-text="n.nilai !== null && n.nilai !== '' ? n.nilai : '-'"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Legacy Values for undefined periods -->
                                                <template x-if="!m.semua_nilai || m.semua_nilai.length === 0">
                                                    <div>
                                                        <div class="flex justify-between items-center gap-4 py-0.5">
                                                            <span class="text-[13px] font-medium text-slate-400">Nilai Lapangan</span>
                                                            <span class="text-[13px] font-bold text-slate-800" x-text="m.nilai_lapangan !== null ? m.nilai_lapangan : '-'"></span>
                                                        </div>
                                                        <div class="flex justify-between items-center gap-4 py-0.5 mt-1.5">
                                                            <span class="text-[13px] font-medium text-slate-400">Nilai Laporan</span>
                                                            <span class="text-[13px] font-bold text-slate-800" x-text="m.nilai_laporan !== null ? m.nilai_laporan : '-'"></span>
                                                        </div>
                                                        <div class="flex justify-between items-center gap-4 py-0.5 mt-1.5">
                                                            <span class="text-[13px] font-medium text-slate-400">Nilai Seminar</span>
                                                            <span class="text-[13px] font-bold text-slate-800" x-text="m.nilai_seminar !== null ? m.nilai_seminar : '-'"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Final Evaluated Score -->
                                                <div class="flex justify-between items-center gap-4 pt-2 mt-2 border-t border-slate-200">
                                                    <span class="text-[11px] font-bold text-slate-800 uppercase">Nilai Mutu Akhir</span>
                                                    <span class="text-sm font-black text-indigo-600" x-text="m.nilai_akhir !== null ? m.nilai_akhir : '-'"></span>
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
                            <td colspan="6" class="py-12 px-6 text-center">
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

        <!-- Edit Modal -->
        <div x-show="detailModal" class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>

            <div x-show="detailModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="detailModal = false"></div>

            <div x-show="detailModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 overflow-hidden transform"
                @keydown.escape.window="detailModal = false">

                <form
                    :action="selectedMahasiswa ? `{{ route('eoffice.kp.koordinator.data_mahasiswa.update', 'DUMMY_ID') }}`.replace('DUMMY_ID', selectedMahasiswa.id) : '#'"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Head -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900" id="modal-title">Edit Data Mahasiswa</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Override konfigurasi Dosen atau Nilai</p>
                            </div>
                        </div>
                        <button type="button" @click="detailModal = false"
                            class="text-slate-400 hover:text-slate-600 transition-colors hover:bg-slate-100 p-2 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-slate-100 px-6">
                        <button type="button" @click="modalTab = 'profil'"
                            class="px-4 py-3 text-xs font-bold border-b-2 transition-colors relative"
                            :class="modalTab === 'profil' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Profil & Dosen
                        </button>
                        <button type="button" @click="modalTab = 'nilai'"
                            class="px-4 py-3 text-xs font-bold border-b-2 transition-colors relative"
                            :class="modalTab === 'nilai' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'">
                            Override Nilai
                        </button>
                    </div>

                    <div class="p-6">
                        <template x-if="selectedMahasiswa">
                            <div>
                                <!-- TAB PROFIL -->
                                <div x-show="modalTab === 'profil'"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0">
                                    <div class="space-y-4">
                                        <!-- Readonly Identifier -->
                                        <div
                                            class="bg-slate-50 p-3 rounded-lg border border-slate-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Mahasiswa
                                                </p>
                                                <p class="text-sm font-bold text-slate-800"
                                                    x-text="selectedMahasiswa.nama"></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase">NIM</p>
                                                <p class="text-sm font-bold text-slate-800"
                                                    x-text="selectedMahasiswa.nim"></p>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Kerja
                                                Praktik <span
                                                    class="text-slate-400 font-normal ml-1">(Read-only)</span></label>
                                            <input type="text"
                                                class="w-full text-sm placeholder-slate-400 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 text-slate-500 cursor-not-allowed focus:ring-0 focus:outline-none"
                                                :value="selectedMahasiswa.judul_kp" readonly>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                for="dosen_pembimbing_id">Assign Dosen Pembimbing</label>
                                            <p class="text-xs text-slate-500 mb-2 leading-relaxed">Pengubahan ini akan
                                                membuat bypass dari proses balancing secara langsung untuk mahasiswa
                                                terkait. Harap berhati-hati.</p>
                                            <select name="dosen_pembimbing_id" id="dosen_pembimbing_id"
                                                class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm transition-shadow">
                                                <option value="">-- Kosongi untuk lepaskan Dosen --</option>
                                                <template x-for="dosen in dosens" :key="dosen.id">
                                                    <option :value="dosen.id"
                                                        :selected="selectedMahasiswa.dosen_pembimbing === dosen.nama_lengkap"
                                                        x-text="dosen.nama_lengkap + ' (' + dosen.nip + ')'">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                for="kelas">Alokasi Kelas Mahasiswa</label>
                                            <select name="kelas" id="kelas"
                                                class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm transition-shadow">
                                                <option value="" :selected="selectedMahasiswa.kelas === '-'">-- Belum
                                                    Dialokasikan --</option>
                                                <option value="Reguler"
                                                    :selected="selectedMahasiswa.kelas === 'Reguler'">Reguler</option>
                                                <option value="Internasional"
                                                    :selected="selectedMahasiswa.kelas === 'Internasional'">
                                                    Internasional</option>
                                                <option value="Karyawan"
                                                    :selected="selectedMahasiswa.kelas === 'Karyawan'">Karyawan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB NILAI -->
                                <div x-show="modalTab === 'nilai'" style="display:none;"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0">
                                    <div
                                        class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 flex items-start gap-3">
                                        <div class="pt-0.5 text-amber-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-amber-800">Peringatan Override</h4>
                                            <p class="text-[11px] text-amber-700 mt-1 leading-relaxed">Hindari merubah
                                                nilai jika mahasiswa belum mengunggah dokumen/form penilaian resmi.
                                                Pastikan Anda memiliki wewenang untuk meniru konversi nilai ini.</p>
                                        </div>
                                    </div>

                                    <template
                                        x-if="selectedMahasiswa.komponen_koordinator && selectedMahasiswa.komponen_koordinator.length > 0">
                                        <div class="grid grid-cols-2 gap-4">
                                            <template x-for="komp in selectedMahasiswa.komponen_koordinator"
                                                :key="komp.id">
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                        :for="'nilai_' + komp.id">
                                                        <span x-text="komp.nama_komponen"></span>
                                                        <span class="text-slate-400 font-normal ml-1"
                                                            x-text="`(${komp.bobot}%)`"></span>
                                                    </label>
                                                    <input type="number" step="0.01" min="0" max="100"
                                                        :name="'nilai_' + komp.id" :id="'nilai_' + komp.id"
                                                        class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm"
                                                        :value="komp.nilai_angka">
                                                </div>
                                            </template>
                                            <div class="col-span-full mt-2">
                                                <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                    for="nilai_akhir">Nilai Akhir / Mutu (Override Opsional)</label>
                                                <input type="text" name="nilai_akhir" id="nilai_akhir"
                                                    class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm placeholder-slate-400"
                                                    placeholder="A, AB, B, dsb" :value="selectedMahasiswa.nilai_akhir">
                                            </div>
                                        </div>
                                    </template>

                                    <template
                                        x-if="!selectedMahasiswa.komponen_koordinator || selectedMahasiswa.komponen_koordinator.length === 0">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                    for="nilai_lapangan">Nilai Lapangan</label>
                                                <input type="number" step="0.01" min="0" max="100" name="nilai_lapangan"
                                                    id="nilai_lapangan"
                                                    class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm"
                                                    :value="selectedMahasiswa.nilai_lapangan">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1.5"
                                                    for="nilai_akhir">Nilai Akhir / Mutu</label>
                                                <input type="text" name="nilai_akhir" id="nilai_akhir"
                                                    class="w-full text-sm border-slate-200 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-700 shadow-sm placeholder-slate-400"
                                                    placeholder="A, AB, B, dsb" :value="selectedMahasiswa.nilai_akhir">
                                            </div>
                                        </div>
                                    </template>
                                    <p class="text-[10px] text-slate-400 mt-3">*Nilai Laporan dan Seminar adalah
                                        otoritas penuh Dosen Pembimbing melalui Menu Bimbingan Mahasiswa.</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div
                        class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="detailModal = false"
                            class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200 bg-slate-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageData', () => ({
                    search: '',
                    filterStatus: '',
                    filterPeriode: '',
                    selectedMahasiswa: null,
                    detailModal: false,
                    modalTab: 'profil',
                    toast: { show: false, type: 'success', title: '', message: '' },
                    mahasiswas: @json($mahasiswas),
                    dosens: @json($dosens),

                    init() {
                        this.$watch('detailModal', value => {
                            if (!value) setTimeout(() => { this.selectedMahasiswa = null; this.modalTab = 'profil'; }, 300);
                        });
                    },


                    get filteredMahasiswas() {
                        return this.mahasiswas.filter(m => {
                            const matchSearch = m.nama.toLowerCase().includes(this.search.toLowerCase()) || m.nim.toLowerCase().includes(this.search.toLowerCase());
                            const matchStatus = this.filterStatus === '' || m.status_kp === this.filterStatus;
                            const matchPeriode = this.filterPeriode === '' || m.periode_id == this.filterPeriode;
                            return matchSearch && matchStatus && matchPeriode;
                        });
                    },

                    showToast(type, title, message) {
                        this.toast.type = type; this.toast.title = title; this.toast.message = message; this.toast.show = true;
                        setTimeout(() => { this.toast.show = false; }, 4000);
                    }
                }));
            });
        </script>
    @endpush
</x-eoffice::layouts.koordinator>