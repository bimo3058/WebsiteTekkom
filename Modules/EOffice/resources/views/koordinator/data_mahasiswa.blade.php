<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Data Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        [x-cloak] { display: none !important; }
        
        /* Table sticky header */
        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="pageData()">
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <div class="h-20 flex items-center px-8 border-b border-slate-100">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold mr-4 shadow-md shadow-indigo-200 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">Balancing Center</h1>
                <p class="text-xs text-slate-500 font-medium">Koordinator Dashboard</p>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto py-6 px-4">
            <div class="mb-2 px-4"><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</p></div>
            
            <a href="{{ route('eoffice.kp.koordinator.dashboard') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <!-- Informasi Menu -->
            <div class="mb-1" x-data="{ expanded: false }">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Informasi
                    </div>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="expanded" x-collapse x-cloak class="pl-11 pr-4 py-2 space-y-1">
                    <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-colors relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-slate-200">
                        Pengumuman
                    </a>
                    <a href="{{ route('eoffice.kp.koordinator.faq') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-colors relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-slate-200">
                        FAQ & Dokumen
                    </a>
                </div>
            </div>

            <!-- Data Mahasiswa (Active) -->
            <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}" class="flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Data Mahasiswa
            </a>

            <a href="{{ route('eoffice.kp.koordinator.balancing') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Balancing Dosen
            </a>
            
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Approval Berkas
            </a>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">
                    K
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">Koordinator KP</p>
                    <p class="text-[11px] text-slate-500 truncate">Sistem Balancing</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        
        <!-- Topbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Data Mahasiswa</span>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <button class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Toast Notification -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed top-24 right-6 lg:right-10 z-50 bg-white border shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]"
             :class="toast.type === 'success' ? 'border-emerald-100' : 'border-red-100'">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5"
                 :class="toast.type === 'success' ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-900 mb-0.5" x-text="toast.title"></p>
                <p class="text-[13px] text-slate-500 leading-relaxed" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-10">
            
            <div class="max-w-7xl mx-auto flex flex-col h-full">
                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Data Mahasiswa</h1>
                        <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Kelola dan monitor data mahasiswa kerja praktik secara menyeluruh.</p>
                    </div>
                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button class="flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm text-sm font-semibold">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Export Excel
                        </button>
                    </div>
                </div>

                <!-- Filters & Search Bar -->
                <div class="bg-white rounded-t-2xl border border-slate-200 border-b-0 p-5 flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input x-model="search" type="text" placeholder="Cari nama atau NIM mahasiswa..." class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-inner text-slate-800 placeholder-slate-400">
                    </div>
                    
                    <div class="flex w-full md:w-auto items-center gap-3">
                        <select x-model="filterStatus" class="w-full md:w-48 bg-white border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-medium appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10">
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
                <div class="bg-white border border-slate-200 rounded-b-2xl shadow-sm flex-1 overflow-hidden relative">
                    <div class="overflow-x-auto h-[60vh] custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[1000px]">
                            <thead class="sticky-header bg-slate-50 shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                                <tr>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">Mahasiswa</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">Tempat KP</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50">Pembimbing</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">Nilai Seminar</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">Nilai Lapangan</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">Nilai Akhir</th>
                                    <th class="py-4 px-6 text-xs font-bold tracking-wider text-slate-500 uppercase border-b border-slate-200 bg-slate-50 text-center">Status KP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="m in filteredMahasiswas" :key="m.id">
                                    <tr @click="openDetail(m)" class="hover:bg-slate-50/80 transition-colors cursor-pointer group">
                                        <td class="py-4 px-6 align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0 group-hover:scale-105 transition-transform" x-text="m.nama.charAt(0)"></div>
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
                                            <span x-show="m.nilai_seminar !== null" class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700" x-text="m.nilai_seminar"></span>
                                            <span x-show="m.nilai_seminar === null" class="text-slate-400 text-xs">-</span>
                                        </td>
                                        <td class="py-4 px-6 align-middle text-center">
                                            <span x-show="m.nilai_lapangan !== null" class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700" x-text="m.nilai_lapangan"></span>
                                            <span x-show="m.nilai_lapangan === null" class="text-slate-400 text-xs">-</span>
                                        </td>
                                        <td class="py-4 px-6 align-middle text-center">
                                            <span x-show="m.nilai_akhir !== null" class="inline-flex items-center justify-center px-3 py-1 rounded-md text-sm font-bold bg-indigo-50 text-indigo-700" x-text="m.nilai_akhir"></span>
                                            <span x-show="m.nilai_akhir === null" class="text-slate-400 text-xs">-</span>
                                        </td>
                                        <td class="py-4 px-6 align-middle text-center">
                                            <span :class="{
                                                'bg-emerald-100 text-emerald-700 border-emerald-200': m.status_kp === 'Selesai',
                                                'bg-amber-100 text-amber-700 border-amber-200': m.status_kp === 'Aktif KP',
                                                'bg-blue-100 text-blue-700 border-blue-200': m.status_kp === 'Seminar',
                                                'bg-slate-100 text-slate-600 border-slate-200': m.status_kp === 'Menunggu Nilai',
                                                'bg-rose-100 text-rose-700 border-rose-200': m.status_kp === 'Pending',
                                            }" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border" x-text="m.status_kp"></span>
                                        </td>
                                    </tr>
                                </template>
                                <!-- Empty State -->
                                <tr x-show="filteredMahasiswas.length === 0" x-cloak>
                                    <td colspan="7" class="py-12 px-6 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900 mb-1">Data Tidak Ditemukan</h3>
                                        <p class="text-sm text-slate-500">Tidak ada mahasiswa yang sesuai dengan pencarian Anda.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination (Static/Visual for now) -->
                    <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                        <p class="text-sm text-slate-500 font-medium">Menampilkan <span class="font-bold text-slate-700" x-text="filteredMahasiswas.length"></span> data</p>
                        <div class="flex items-center gap-1">
                            <button class="p-2 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 disabled:opacity-50" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="px-3 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm font-bold">1</button>
                            <button class="p-2 rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-slate-600 hover:bg-slate-50 disabled:opacity-50" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Detail Drawer (Slide over) -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div x-show="detailModal" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="detailModal = false"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Slide-over panel -->
                <div x-show="detailModal"
                     x-transition:enter="transform transition ease-in-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col bg-white shadow-2xl">
                        <template x-if="selectedMahasiswa">
                            <div class="flex h-full flex-col">
                                <!-- Drawer Header -->
                                <div class="px-6 py-6 border-b border-slate-100 bg-slate-50/50 relative">
                                    <div class="absolute right-6 top-6">
                                        <button @click="detailModal = false" type="button" class="rounded-md bg-white text-slate-400 hover:text-slate-500 hover:bg-slate-100 p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-4 pr-10">
                                        <div class="w-14 h-14 rounded-2xl bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-extrabold text-xl shadow-sm">
                                            <span x-text="selectedMahasiswa.nama.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-slate-900" x-text="selectedMahasiswa.nama"></h2>
                                            <p class="text-sm text-slate-500 font-medium" x-text="selectedMahasiswa.nim + ' • ' + selectedMahasiswa.prodi"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Drawer Body -->
                                <div class="relative flex-1 overflow-y-auto px-6 py-6 custom-scrollbar">
                                    
                                    <!-- Status Badges -->
                                    <div class="flex flex-wrap gap-2 mb-8">
                                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100" x-text="'TA ' + selectedMahasiswa.tahun_kp + ' • ' + selectedMahasiswa.semester"></span>
                                        <span :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200': selectedMahasiswa.status_dokumen === 'Lengkap',
                                            'bg-rose-50 text-rose-700 border-rose-200': selectedMahasiswa.status_dokumen === 'Tidak Lengkap',
                                        }" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold border" x-text="'Dokumen: ' + selectedMahasiswa.status_dokumen"></span>
                                        <span :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200': selectedMahasiswa.status_seminar === 'Lulus',
                                            'bg-amber-50 text-amber-700 border-amber-200': selectedMahasiswa.status_seminar === 'Menunggu Jadwal',
                                            'bg-slate-50 text-slate-600 border-slate-200': selectedMahasiswa.status_seminar === 'Belum Daftar',
                                        }" class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold border" x-text="'Seminar: ' + selectedMahasiswa.status_seminar"></span>
                                    </div>

                                    <!-- Informasi KP -->
                                    <div class="mb-8">
                                        <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                            Informasi Kerja Praktik
                                        </h3>
                                        <dl class="space-y-4">
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500 mb-1">Tempat KP</dt>
                                                <dd class="text-sm font-semibold text-slate-800" x-text="selectedMahasiswa.tempat_kp"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500 mb-1">Judul KP</dt>
                                                <dd class="text-sm font-semibold text-slate-800 leading-relaxed" x-text="selectedMahasiswa.judul_kp"></dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs font-medium text-slate-500 mb-1">Dosen Pembimbing</dt>
                                                <dd class="text-sm font-semibold text-slate-800" x-text="selectedMahasiswa.dosen_pembimbing || 'Belum diplot'"></dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <!-- Penilaian -->
                                    <div class="mb-8">
                                        <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            Rekap Penilaian
                                        </h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                <p class="text-xs font-medium text-slate-500 mb-1">Nilai Seminar</p>
                                                <p class="text-xl font-extrabold text-slate-900" x-text="selectedMahasiswa.nilai_seminar !== null ? selectedMahasiswa.nilai_seminar : '-'"></p>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative group">
                                                <p class="text-xs font-medium text-slate-500 mb-1">Nilai Lapangan</p>
                                                <div x-show="!isEditingNilai" class="flex items-center justify-between">
                                                    <p class="text-xl font-extrabold text-slate-900" x-text="selectedMahasiswa.nilai_lapangan !== null ? selectedMahasiswa.nilai_lapangan : '-'"></p>
                                                    <button @click="isEditingNilai = true; tempNilai = selectedMahasiswa.nilai_lapangan" class="text-slate-400 hover:text-indigo-600 transition-colors p-1 rounded-md hover:bg-indigo-50" title="Input/Edit Nilai Lapangan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </button>
                                                </div>
                                                <div x-show="isEditingNilai" x-cloak class="mt-1 flex flex-col gap-2">
                                                    <input type="number" x-model="tempNilai" min="0" max="100" step="0.1" class="w-full rounded-lg border border-slate-300 py-1.5 px-3 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" placeholder="0 - 100">
                                                    <div class="flex gap-2">
                                                        <button @click="saveNilai()" class="flex-1 bg-indigo-600 text-white px-2 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 transition-colors">Simpan</button>
                                                        <button @click="isEditingNilai = false" class="flex-1 bg-white border border-slate-300 text-slate-600 px-2 py-1.5 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">Batal</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 bg-indigo-50 p-4 rounded-xl border border-indigo-100 flex items-center justify-between">
                                            <div>
                                                <p class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-0.5">Nilai Akhir</p>
                                                <p class="text-[10px] text-indigo-500 font-medium">Rata-rata penilaian</p>
                                            </div>
                                            <p class="text-3xl font-black text-indigo-700" x-text="selectedMahasiswa.nilai_akhir !== null ? selectedMahasiswa.nilai_akhir : '-'"></p>
                                        </div>
                                    </div>

                                    <!-- Riwayat Approval -->
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Riwayat Aktivitas
                                        </h3>
                                        <div class="space-y-4 relative before:absolute before:inset-0 before:ml-2 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-slate-100 pl-6">
                                            <template x-for="(riwayat, index) in selectedMahasiswa.riwayat_approval" :key="index">
                                                <div class="relative">
                                                    <div class="absolute -left-6 mt-1 flex h-4 w-4 items-center justify-center rounded-full ring-4 ring-white"
                                                         :class="{
                                                            'bg-emerald-500': riwayat.status === 'Disetujui',
                                                            'bg-rose-500': riwayat.status === 'Ditolak',
                                                            'bg-amber-500': riwayat.status === 'Revisi'
                                                         }">
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-medium text-slate-400 mb-0.5" x-text="riwayat.tanggal"></span>
                                                        <span class="text-sm font-bold text-slate-800" x-text="riwayat.status"></span>
                                                        <p class="text-sm text-slate-600 mt-1 leading-relaxed" x-text="riwayat.keterangan"></p>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="!selectedMahasiswa.riwayat_approval || selectedMahasiswa.riwayat_approval.length === 0" class="text-sm text-slate-500 italic relative -left-6">
                                                Belum ada riwayat aktivitas.
                                            </div>
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

<script>
function pageData() {
    return {
        sidebarOpen: false,
        search: '',
        filterStatus: '',
        mahasiswas: @json($mahasiswas),
        selectedMahasiswa: null,
        detailModal: false,
        isEditingNilai: false,
        tempNilai: '',
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
            this.isEditingNilai = false;
            this.detailModal = true;
        },
        saveNilai() {
            let nilai = parseFloat(this.tempNilai);
            if(isNaN(nilai) || nilai < 0 || nilai > 100) {
                this.showToast('error', 'Validasi Gagal', 'Nilai harus berada antara 0 dan 100.');
                return;
            }
            
            let idx = this.mahasiswas.findIndex(m => m.id === this.selectedMahasiswa.id);
            if(idx !== -1) {
                this.mahasiswas[idx].nilai_lapangan = nilai;
                
                // Recalculate nilai akhir if needed
                let ns = this.mahasiswas[idx].nilai_seminar;
                let nl = this.mahasiswas[idx].nilai_lapangan;
                if(ns !== null && nl !== null) {
                    this.mahasiswas[idx].nilai_akhir = parseFloat(((ns + nl) / 2).toFixed(1));
                }
                
                this.selectedMahasiswa = this.mahasiswas[idx];
                this.isEditingNilai = false;
                this.showToast('success', 'Berhasil', 'Nilai lapangan berhasil disimpan.');
                
                // In a real app, you would make an AJAX/fetch request here to save to database.
                // fetch(`/eoffice/kp/koordinator/data-mahasiswa/${this.selectedMahasiswa.id}/nilai`, { ... })
            }
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
</body>
</html>
