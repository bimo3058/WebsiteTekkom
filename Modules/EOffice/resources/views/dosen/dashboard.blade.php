<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Dashboard Dosen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen w-full overflow-hidden">

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden"
            x-transition.opacity @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
            <div class="h-20 flex items-center px-8 border-b border-slate-100">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold mr-4 shadow-md shadow-indigo-200 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">Balancing Center</h1>
                    <p class="text-xs text-slate-500 font-medium">Dosen Pembimbing</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto py-6 px-4">
                <div class="mb-2 px-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</p>
                </div>

                <a href="{{ route('eoffice.kp.dosen.dashboard') }}"
                    class="flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Bimbingan
                </a>

                <a href="{{ route('eoffice.kp.dosen.validasi_berkas') }}"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Penilaian Laporan
                </a>

                <a href="{{ route('eoffice.kp.dosen.penilaian_seminar') }}"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Penilaian Seminar
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Pengajuan Tugas
                </a>
            </div>

            <!-- User Profile -->
            @if(auth()->user() && auth()->user()->hasRole('koor_kp'))
                <div class="px-4 pb-4 mt-auto">
                    <a href="{{ route('eoffice.kp.koordinator.dashboard') }}"
                        class="flex items-center px-4 py-2.5 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all text-sm font-semibold border border-indigo-200 shadow-sm">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Beralih ke Koordinator
                    </a>
                </div>
            @endif
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div
                        class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">
                        {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">
                            {{ auth()->user()->name ?? 'Dosen Pembimbing' }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'Sistem Bimbingan' }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">

            <!-- Topbar -->
            <header
                class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true"
                        class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                        <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Dashboard</span>
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button
                            class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-10">

                <div class="max-w-6xl mx-auto">
                    <!-- Page Header -->
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8">
                        <div>
                            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Dashboard</h1>
                            <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Ringkasan data mahasiswa
                                bimbingan Kerja Praktik Anda.</p>
                        </div>
                    </div>

                    <!-- Content Area (Clean White Cards) -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8 p-8">
                        <h2 class="text-lg font-bold text-slate-800 mb-6">Statistik Bimbingan KP</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <!-- Stat 1 -->
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total
                                    Bimbingan</p>
                                <div class="flex items-end gap-3">
                                    <p class="text-5xl font-extrabold text-slate-900 tracking-tight">
                                        {{ $stats['total_bimbingan'] ?? 0 }}</p>
                                    <p class="text-sm text-slate-500 font-medium pb-1.5">Mahasiswa</p>
                                </div>
                            </div>

                            <!-- Stat 2 -->
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Menunggu ACC
                                </p>
                                <div class="flex items-end gap-3">
                                    <p class="text-5xl font-extrabold text-amber-500 tracking-tight">
                                        {{ $stats['menunggu_acc'] ?? 0 }}</p>
                                    <p class="text-sm text-slate-500 font-medium pb-1.5">Pra KP</p>
                                </div>
                            </div>

                            <!-- Stat 3 -->
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Sedang KP</p>
                                <div class="flex items-end gap-3">
                                    <p class="text-5xl font-extrabold text-indigo-600 tracking-tight">
                                        {{ $stats['sedang_kp'] ?? 0 }}</p>
                                    <p class="text-sm text-slate-500 font-medium pb-1.5">Mahasiswa</p>
                                </div>
                            </div>

                            <!-- Stat 4 -->
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Selesai KP</p>
                                <div class="flex items-end gap-3">
                                    <p class="text-5xl font-extrabold text-emerald-500 tracking-tight">
                                        {{ $stats['selesai_kp'] ?? 0 }}</p>
                                    <p class="text-sm text-slate-500 font-medium pb-1.5">Sudah Dinilai</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Action Card 1 -->
                        <div
                            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 flex flex-col group hover:border-indigo-200 hover:shadow-md transition-all">
                            <div
                                class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-3 group-hover:text-indigo-700 transition-colors">
                                Bimbingan Mahasiswa</h3>
                            <p class="text-sm text-slate-500 flex-1 mb-8 leading-relaxed">Kelola daftar mahasiswa
                                bimbingan, pantau progres, lihat riwayat bimbingan, dan berikan nilai seminar akhir
                                Kerja Praktik.</p>
                            <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}"
                                class="inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 outline-none w-fit">
                                Kelola Bimbingan
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <!-- Action Card 2 -->
                        <div
                            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 flex flex-col group hover:border-emerald-200 hover:shadow-md transition-all">
                            <div
                                class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 mb-3 group-hover:text-emerald-700 transition-colors">
                                Penilaian Laporan</h3>
                            <p class="text-sm text-slate-500 flex-1 mb-8 leading-relaxed">Periksa, berikan revisi, dan
                                lakukan persetujuan (approval) terhadap laporan maupun makalah mahasiswa bimbingan Anda.
                            </p>
                            <a href="{{ route('eoffice.kp.dosen.validasi_berkas') }}"
                                class="inline-flex items-center justify-center px-5 py-3 bg-white border-2 border-emerald-100 text-emerald-700 font-bold text-sm rounded-xl hover:bg-emerald-50 transition-colors w-fit">
                                Buka Penilaian Laporan
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>

</html>