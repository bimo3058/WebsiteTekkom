<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Dashboard Koordinator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar (White Clean Design) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0">
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-6 border-b border-transparent">
            <div class="w-8 h-8 bg-slate-900 rounded flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-sm leading-tight">SIKP</h1>
                <p class="text-[10px] text-slate-500 font-medium">Sistem Informasi KP</p>
            </div>
            <button class="ml-auto text-slate-400 hover:text-slate-600 lg:hidden" @click="sidebarOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-6 mb-2">
                <p class="text-[11px] font-semibold text-slate-400">Main Menu</p>
            </div>
            <!-- Active Menu -->
            <a href="{{ route('kp.koordinator.dashboard') }}" class="flex items-center px-6 py-2.5 bg-slate-50 border-l-4 border-slate-900 text-slate-900 font-semibold text-sm">
                <svg class="w-5 h-5 mr-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="px-6 mt-6 mb-2">
                <p class="text-[11px] font-semibold text-slate-400">Koordinator</p>
            </div>
            <a href="{{ route('kp.koordinator.pengumuman') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Membuat Pengumuman
            </a>
            <a href="{{ route('kp.koordinator.balancing') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Balancing Dosen
            </a>
            <a href="{{ route('kp.koordinator.validasi_berkas') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Validasi & Approval Berkas
            </a>
        </div>

        <div class="px-6 py-4">
            <a href="#" class="flex items-center text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-white sm:bg-slate-50">
        
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 z-10">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 mr-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <!-- Breadcrumbs -->
                <nav class="hidden sm:flex text-sm text-slate-500 font-medium">
                    <span class="text-slate-400">SIKP</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-900">Dashboard</span>
                </nav>
            </div>

            <!-- Right Topbar Icons -->
            <div class="flex items-center gap-4 sm:gap-5">
                <button class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button class="text-slate-400 hover:text-slate-600 transition-colors relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                        K
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-[13px] font-bold text-slate-900 leading-tight">Koordinator KP</p>
                        <p class="text-[11px] text-slate-500">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
                    <p class="text-sm text-slate-500 mt-1">Ringkasan data pelaksanaan Kerja Praktik mahasiswa.</p>
                </div>
                
                <!-- Dropdown Simulator -->
                <div class="relative">
                    <button class="flex items-center justify-between w-full sm:w-auto min-w-[220px] px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        Semester Genap 2025/2026
                        <svg class="w-4 h-4 text-slate-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Content Area (Clean White Cards) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-6">Statistik Global KP</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Stat 1 -->
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Pendaftar</p>
                        <div class="flex items-end gap-3">
                            <p class="text-4xl font-extrabold text-slate-900">{{ $stats['total_mahasiswa'] }}</p>
                            <p class="text-sm text-slate-500 font-medium pb-1">Mahasiswa</p>
                        </div>
                    </div>
                    
                    <!-- Stat 2 -->
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Menunggu Dosen</p>
                        <div class="flex items-end gap-3">
                            <p class="text-4xl font-extrabold text-amber-600">{{ $stats['menunggu_dosen'] }}</p>
                            <p class="text-sm text-slate-500 font-medium pb-1">Perlu Balancing</p>
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Fase Pelaksanaan</p>
                        <div class="flex items-end gap-3">
                            <p class="text-4xl font-extrabold text-blue-600">{{ $stats['sedang_kp'] }}</p>
                            <p class="text-sm text-slate-500 font-medium pb-1">Sedang KP</p>
                        </div>
                    </div>

                    <!-- Stat 4 -->
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Validasi Berkas</p>
                        <div class="flex items-end gap-3">
                            <p class="text-4xl font-extrabold text-emerald-600">{{ $stats['menunggu_validasi'] }}</p>
                            <p class="text-sm text-slate-500 font-medium pb-1">Dokumen Baru</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Action Card 1 -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Balancing Dosen Pembimbing</h3>
                    <p class="text-sm text-slate-500 flex-1 mb-6">Lihat daftar mahasiswa yang belum mendapatkan dosen pembimbing, set kuota, dan lakukan pembagian secara merata.</p>
                    <a href="{{ route('kp.koordinator.balancing') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-900 text-white font-medium text-sm rounded-lg hover:bg-slate-800 transition-colors w-fit">
                        Lakukan Balancing
                    </a>
                </div>

                <!-- Action Card 2 -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Validasi & Approval Berkas</h3>
                    <p class="text-sm text-slate-500 flex-1 mb-6">Lakukan verifikasi transkrip nilai, kartu hijau, surat balasan instansi, dan input nilai lapangan mahasiswa.</p>
                    <a href="{{ route('kp.koordinator.validasi_berkas') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-300 text-slate-700 font-medium text-sm rounded-lg hover:bg-slate-50 transition-colors w-fit">
                        Buka Halaman Validasi
                    </a>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>
