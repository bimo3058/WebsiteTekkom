<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Approval Berkas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Custom scrollbar for left panel */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="approvalApp()" x-cloak>
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-transition.opacity @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <div class="h-20 flex items-center px-8 border-b border-slate-100 shrink-0">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold mr-4 shadow-md shadow-indigo-200 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">Balancing Center</h1>
                <p class="text-xs text-slate-500 font-medium">Koordinator Dashboard</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4">
            <div class="mb-2 px-4">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</p>
            </div>

            <a href="{{ route('eoffice.kp.koordinator.dashboard') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Informasi Menu -->
            <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi
            </a>
            <a href="{{ route('eoffice.kp.koordinator.template') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.template') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Template Dokumen
            </a>

            <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Data Mahasiswa
            </a>

            <a href="{{ route('eoffice.kp.koordinator.balancing') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Balancing Dosen
            </a>
            <a href="{{ route('eoffice.kp.koordinator.upload_berkas') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload Berkas
            </a>
            
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.validasi_berkas') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Approval Berkas
            </a>
            <a href="{{ route('eoffice.kp.koordinator.nilai_lapangan') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.nilai_lapangan') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Nilai Lapangan
            </a>
            <a href="{{ route('eoffice.kp.koordinator.pengaturan') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.pengaturan') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>
        </div>

        <div class="p-4 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">{{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Koordinator KP' }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? 'Sistem Balancing' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">

        <!-- Topbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0 shrink-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Sistem</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Approval Berkas</span>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <button class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full transition-colors relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                </button>
            </div>
        </header>

        <!-- Toast Notification -->
        <div x-show="toast.show" 
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

        <!-- Dashboard Body: 2 Panels Layout -->
        <div class="flex-1 flex overflow-hidden">
            
            <!-- Left Panel: Student List -->
            <div class="w-full lg:w-[400px] xl:w-[450px] bg-white border-r border-slate-200 flex flex-col shrink-0 transition-transform duration-300 z-10"
                 :class="{'hidden lg:flex': selectedStudent !== null}">
                
                <div class="p-6 border-b border-slate-100 shrink-0 bg-slate-50/50">
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight mb-1">Approval Berkas</h2>
                    <p class="text-xs text-slate-500 mb-5">Verifikasi dokumen mahasiswa KP</p>
                    
                    <div class="relative mb-4">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama atau NIM..." 
                               class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-sm">
                        <svg class="absolute left-3.5 top-3 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>

                    <div class="flex gap-2">
                        <select x-model="filterStatus" class="flex-1 bg-white border border-slate-200 rounded-lg text-xs font-medium px-3 py-2 focus:outline-none focus:border-indigo-500 text-slate-600 shadow-sm">
                            <option value="all">Semua Status</option>
                            <option value="Menunggu Review">Menunggu Review</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Disetujui">Disetujui</option>
                        </select>
                        <select x-model="filterTahap" class="flex-1 bg-white border border-slate-200 rounded-lg text-xs font-medium px-3 py-2 focus:outline-none focus:border-indigo-500 text-slate-600 shadow-sm">
                            <option value="all">Semua Tahapan</option>
                            <option value="Pra KP">Pra KP</option>
                            <option value="Saat KP">Saat KP</option>
                            <option value="Pasca KP">Pasca KP</option>
                        </select>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-2 bg-slate-50/30">
                    <template x-for="mhs in filteredMahasiswas" :key="mhs.id">
                        <button @click="selectStudent(mhs)" 
                                class="w-full text-left p-4 rounded-2xl border transition-all duration-200 relative group overflow-hidden"
                                :class="selectedStudent && selectedStudent.id === mhs.id ? 'bg-indigo-50/50 border-indigo-200 shadow-sm' : 'bg-white border-slate-200 hover:border-indigo-200 hover:shadow-md hover:shadow-indigo-100/50'">
                            
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 transition-colors" x-text="mhs.nama"></h3>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5" x-text="mhs.nim"></p>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide rounded-md border"
                                      :class="getStatusColor(mhs.status_keseluruhan)" x-text="mhs.status_keseluruhan"></span>
                            </div>
                            
                            <div class="flex items-center gap-4 mt-3">
                                <div class="flex items-center text-[11px] text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    <span x-text="mhs.tahap_aktif"></span>
                                </div>
                                <div class="flex items-center text-[11px] text-slate-500 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span x-text="mhs.jumlah_dokumen + ' File'"></span>
                                </div>
                            </div>
                        </button>
                    </template>
                    
                    <div x-show="filteredMahasiswas.length === 0" class="py-12 px-4 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-900">Tidak ada data</p>
                        <p class="text-xs text-slate-500 mt-1">Coba ubah kata kunci pencarian atau filter.</p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Document Details -->
            <div class="flex-1 bg-slate-50/50 flex flex-col overflow-hidden relative" :class="{'hidden lg:flex': selectedStudent === null}">
                
                <!-- Empty State (No selection) -->
                <div x-show="selectedStudent === null" class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10 bg-white">
                    <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mb-6 border-8 border-indigo-50/50">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight mb-2">Pilih Mahasiswa</h3>
                    <p class="text-sm text-slate-500 max-w-sm leading-relaxed">Pilih mahasiswa dari daftar di sebelah kiri untuk melihat dan memverifikasi dokumen Kerja Praktik mereka.</p>
                </div>

                <!-- Content State -->
                <template x-if="selectedStudent !== null">
                    <div class="flex flex-col h-full overflow-hidden">
                        
                        <!-- Mobile back button -->
                        <div class="lg:hidden p-4 border-b border-slate-200 bg-white flex items-center shrink-0">
                            <button @click="selectedStudent = null" class="flex items-center text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Kembali ke Daftar
                            </button>
                        </div>

                        <!-- Header Detail Mahasiswa -->
                        <div class="bg-white p-6 lg:p-10 border-b border-slate-200 shrink-0">
                            <div class="max-w-4xl mx-auto">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                                    <div class="flex gap-5 items-start">
                                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-2xl shrink-0 shadow-sm">
                                            <span x-text="selectedStudent.nama.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight" x-text="selectedStudent.nama"></h2>
                                            <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                                                <span x-text="selectedStudent.nim"></span>
                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                <span x-text="selectedStudent.prodi"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 w-full md:w-64 shrink-0">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Dokumen</p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-slate-800" x-text="selectedStudent.status_keseluruhan"></span>
                                            <span class="w-2.5 h-2.5 rounded-full" :class="getStatusDotColor(selectedStudent.status_keseluruhan)"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 p-1.5 bg-slate-100 rounded-lg text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" /></svg></div>
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase">Tempat KP</p>
                                            <p class="text-sm font-semibold text-slate-800 mt-0.5" x-text="selectedStudent.tempat_kp"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 p-1.5 bg-slate-100 rounded-lg text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg></div>
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase">Dosen Pembimbing</p>
                                            <p class="text-sm font-semibold text-slate-800 mt-0.5" x-text="selectedStudent.dosen_pembimbing || 'Belum diplot'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs Tahapan -->
                        <div class="px-6 lg:px-10 pt-6 bg-slate-50 shrink-0 border-b border-slate-200">
                            <div class="max-w-4xl mx-auto flex gap-6">
                                <button @click="activeTab = 'pra_kp'" 
                                        class="pb-3 text-sm font-bold transition-all relative border-b-2"
                                        :class="activeTab === 'pra_kp' ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-slate-800 hover:border-slate-300'">
                                    Pra KP
                                    <span class="ml-1.5 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-[10px]" x-text="selectedStudent.dokumen.pra_kp.length"></span>
                                </button>
                                <button @click="activeTab = 'saat_kp'" 
                                        class="pb-3 text-sm font-bold transition-all relative border-b-2"
                                        :class="activeTab === 'saat_kp' ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-slate-800 hover:border-slate-300'">
                                    Saat KP
                                    <span class="ml-1.5 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-[10px]" x-text="selectedStudent.dokumen.saat_kp.length"></span>
                                </button>
                                <button @click="activeTab = 'pasca_kp'" 
                                        class="pb-3 text-sm font-bold transition-all relative border-b-2"
                                        :class="activeTab === 'pasca_kp' ? 'text-indigo-600 border-indigo-600' : 'text-slate-500 border-transparent hover:text-slate-800 hover:border-slate-300'">
                                    Pasca KP
                                    <span class="ml-1.5 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-[10px]" x-text="selectedStudent.dokumen.pasca_kp.length"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Documents List -->
                        <div class="flex-1 overflow-y-auto p-6 lg:p-10">
                            <div class="max-w-4xl mx-auto space-y-4">
                                
                                <template x-for="doc in currentDocuments" :key="doc.id">
                                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 transition-all hover:shadow-md hover:border-indigo-100">
                                        <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
                                            
                                            <!-- Doc Info -->
                                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center border border-red-100 shrink-0">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-bold text-slate-900 truncate" x-text="doc.jenis"></h4>
                                                    <p class="text-xs text-slate-500 mt-1 truncate" x-text="doc.nama_file"></p>
                                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                                        <span class="text-[10px] font-medium text-slate-400" x-text="'Diunggah: ' + doc.tanggal"></span>
                                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded border"
                                                              :class="getDocStatusColor(doc.status)" x-text="doc.status"></span>
                                                        <!-- Badge: file tidak tersedia -->
                                                        <span x-show="!doc.file_url" class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-600 border border-orange-200 text-[10px] font-bold rounded">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                            File tidak tersedia
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0 border-t sm:border-t-0 pt-4 sm:pt-0 border-slate-100">
                                                <!-- Preview button: disabled if no file -->
                                                <button @click="doc.file_url ? openPreview(doc) : null"
                                                        :disabled="!doc.file_url"
                                                        :class="doc.file_url ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border-indigo-100 cursor-pointer' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                                                        :title="doc.file_url ? 'Preview file' : 'File belum tersedia, minta mahasiswa upload ulang'"
                                                        class="flex-1 sm:flex-none px-4 py-2 font-bold text-xs rounded-xl transition-colors text-center border flex items-center gap-1.5 justify-center">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span x-text="doc.file_url ? 'Lihat' : 'Kosong'"></span>
                                                </button>
                                                
                                                <template x-if="doc.status === 'pending'">
                                                    <div class="flex gap-2 flex-1 sm:flex-none">
                                                        <button @click="processApproval(doc.id, 'approved')" class="flex-1 sm:flex-none p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 rounded-xl transition-colors border border-emerald-100" title="Setujui">
                                                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        </button>
                                                        <button @click="openRejectModal(doc)" class="flex-1 sm:flex-none p-2 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-xl transition-colors border border-red-100" title="Tolak / Revisi">
                                                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        <!-- Catatan Rejection Info -->
                                        <div x-show="doc.status === 'rejected' && doc.catatan" class="mt-4 p-3 bg-red-50/50 border border-red-100 rounded-xl flex gap-3 items-start">
                                            <div class="mt-0.5"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                            <div>
                                                <p class="text-xs font-bold text-red-800">Catatan Revisi:</p>
                                                <p class="text-xs text-red-600 mt-0.5 leading-relaxed" x-text="doc.catatan"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty State for Documents -->
                                <div x-show="currentDocuments.length === 0" class="py-16 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-sm">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Dokumen</h3>
                                    <p class="text-sm text-slate-500">Mahasiswa belum mengunggah dokumen untuk tahapan ini.</p>
                                </div>
                                
                                <div x-show="currentDocuments.length > 0" class="pt-6 border-t border-slate-200 mt-8 text-right">
                                    <button class="px-6 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors">
                                        Simpan & Kirim Notifikasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Preview PDF -->
    <div x-show="previewModalOpen" class="relative z-50" style="display: none;">
        <div x-show="previewModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-hidden flex items-center justify-center p-4 sm:p-6 lg:p-12">
            <div x-show="previewModalOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 @click.away="previewModalOpen = false"
                 class="relative w-full max-w-5xl h-full flex flex-col rounded-2xl bg-white shadow-2xl border border-slate-100 overflow-hidden">
                
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center border border-red-100 shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900" x-text="previewDoc ? previewDoc.nama_file : ''"></h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5" x-text="previewDoc && isPdf(previewDoc.nama_file) ? 'Preview PDF • Diunggah oleh Mahasiswa' : 'File Dokumen • Klik Download untuk membuka'"></p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a :href="previewDoc ? previewDoc.file_url : '#'" :download="previewDoc ? previewDoc.nama_file : ''" target="_blank" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100" title="Download">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </a>
                        <button @click="previewModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition-colors" title="Tutup">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex-1 bg-slate-100 flex flex-col items-center justify-center relative overflow-hidden">
                    <!-- PDF: embed with iframe -->
                    <template x-if="previewDoc && previewDoc.file_url && isPdf(previewDoc.nama_file)">
                        <iframe :src="previewDoc.file_url" class="w-full h-full border-0" style="min-height:500px;"></iframe>
                    </template>

                    <!-- Word/Doc: cannot preview in browser, show download prompt -->
                    <template x-if="previewDoc && previewDoc.file_url && !isPdf(previewDoc.nama_file)">
                        <div class="text-center p-10">
                            <div class="w-20 h-20 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                                <svg class="w-10 h-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
                            </div>
                            <h4 class="text-base font-bold text-slate-800 mb-1" x-text="previewDoc.nama_file"></h4>
                            <p class="text-sm text-slate-500 mb-2">File Word/Dokumen tidak bisa dipreview langsung di browser.</p>
                            <p class="text-xs text-slate-400 mb-6">Gunakan tombol Download di atas untuk membuka file ini.</p>
                            <a :href="previewDoc.file_url" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Buka / Download File
                            </a>
                        </div>
                    </template>

                    <!-- Fallback jika file_url null -->
                    <template x-if="!previewDoc || !previewDoc.file_url">
                        <div class="text-center p-8">
                            <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <p class="text-slate-600 font-semibold">File tidak tersedia</p>
                            <p class="text-xs text-slate-400 mt-1">Dokumen ini belum memiliki file yang bisa dipreview.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject / Catatan Revisi -->
    <div x-show="rejectModalOpen" class="relative z-50" style="display: none;">
        <div x-show="rejectModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
            <div x-show="rejectModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 @click.away="rejectModalOpen = false"
                 class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden border border-slate-100">

                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Tolak / Minta Revisi</h3>
                    <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 p-1.5 rounded-lg transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <!-- File info -->
                    <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="w-9 h-9 bg-red-100 text-red-500 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-900 truncate" x-text="rejectDoc ? rejectDoc.nama_file : ''"></p>
                    </div>

                    <!-- Pilihan aksi -->
                    <div>
                        <p class="text-sm font-bold text-slate-700 mb-2">Pilih Tindakan</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="rejectAction === 'revision' ? 'border-amber-400 bg-amber-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" x-model="rejectAction" value="revision" class="mt-0.5 accent-amber-500">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Minta Revisi</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Dokumen perlu diperbaiki sebelum disetujui</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all"
                                   :class="rejectAction === 'rejected' ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <input type="radio" x-model="rejectAction" value="rejected" class="mt-0.5 accent-red-500">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Tolak Dokumen</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Dokumen ditolak dan harus diupload ulang</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Catatan <span class="text-red-500">*</span></label>
                        <textarea x-model="rejectReason" rows="3" placeholder="Tuliskan alasan atau bagian yang perlu diperbaiki..."
                                  class="w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border outline-none resize-y"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex gap-3 justify-end">
                    <button @click="rejectModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50">Batal</button>
                    <button @click="submitReject()"
                            :disabled="isLoading"
                            :class="rejectAction === 'rejected' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-200'"
                            class="px-5 py-2 text-white text-sm font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2 disabled:opacity-60">
                        <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="rejectAction === 'rejected' ? 'Tolak Dokumen' : 'Kirim ke Revisi'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-[100] min-w-[320px] max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-100 flex items-start gap-4 p-4"
         style="display: none;">
        <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5"
             :class="toast.type === 'success' ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100'">
            <svg x-show="toast.type === 'success'" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="toast.type !== 'success'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-slate-900" x-text="toast.title"></p>
            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

</div>

<script>
    function approvalApp() {
        return {
            sidebarOpen: false,
            searchQuery: '',
            filterStatus: 'all',
            filterTahap: 'all',
            activeTab: 'pra_kp',
            mahasiswas: @json($mahasiswas),
            selectedStudent: null,
            isLoading: false,
            // Modal States
            previewModalOpen: false,
            previewDoc: null,
            rejectModalOpen: false,
            rejectDoc: null,
            rejectReason: '',
            rejectAction: 'revision', // 'revision' | 'rejected'

            // Toast state
            toast: {
                show: false,
                type: 'success',
                title: '',
                message: ''
            },
            
            get filteredMahasiswas() {
                return this.mahasiswas.filter(m => {
                    const matchSearch = m.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                        m.nim.includes(this.searchQuery);
                    const matchStatus = this.filterStatus === 'all' || m.status_keseluruhan === this.filterStatus;
                    const matchTahap = this.filterTahap === 'all' || m.tahap_aktif === this.filterTahap;
                    return matchSearch && matchStatus && matchTahap;
                });
            },
            
            get currentDocuments() {
                if (!this.selectedStudent) return [];
                return this.selectedStudent.dokumen[this.activeTab] || [];
            },
            
            selectStudent(mhs) {
                this.selectedStudent = mhs;
                // Auto switch tab based on tahap_aktif if possible
                if(mhs.tahap_aktif === 'Pra KP') this.activeTab = 'pra_kp';
                if(mhs.tahap_aktif === 'Saat KP') this.activeTab = 'saat_kp';
                if(mhs.tahap_aktif === 'Pasca KP') this.activeTab = 'pasca_kp';
            },
            
            getStatusColor(status) {
                switch(status) {
                    case 'Menunggu Review': return 'bg-amber-50 text-amber-600 border-amber-200';
                    case 'Disetujui': return 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    case 'Revisi': return 'bg-red-50 text-red-600 border-red-200';
                    default: return 'bg-slate-100 text-slate-500 border-slate-200';
                }
            },
            
            getStatusDotColor(status) {
                switch(status) {
                    case 'Menunggu Review': return 'bg-amber-400';
                    case 'Disetujui': return 'bg-emerald-500';
                    case 'Revisi': return 'bg-red-500';
                    default: return 'bg-slate-300';
                }
            },
            
            getDocStatusColor(status) {
                switch(status) {
                    case 'pending':  return 'bg-slate-100 text-slate-500 border-slate-200';
                    case 'approved': return 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    case 'rejected': return 'bg-red-50 text-red-600 border-red-200';
                    case 'revision': return 'bg-amber-50 text-amber-600 border-amber-200';
                    default:         return 'bg-slate-100 text-slate-500 border-slate-200';
                }
            },
            
            openPreview(doc) {
                this.previewDoc = doc;
                this.previewModalOpen = true;
            },

            isPdf(filename) {
                if (!filename) return false;
                return filename.toLowerCase().endsWith('.pdf');
            },
            
            openRejectModal(doc) {
                this.rejectDoc = doc;
                this.rejectReason = '';
                this.rejectAction = 'revision';
                this.rejectModalOpen = true;
            },
            
            processApproval(docId, status) {
                if (this.isLoading) return;
                this.isLoading = true;

                fetch(`/eoffice/kp/koordinator/validasi-berkas/${docId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Fix Alpine reactivity: mutate nested array properly
                        const phase = this.activeTab;
                        const idx = this.selectedStudent.dokumen[phase].findIndex(d => d.id === docId);
                        if (idx !== -1) {
                            this.selectedStudent.dokumen[phase][idx] = {
                                ...this.selectedStudent.dokumen[phase][idx],
                                status: 'approved',
                                catatan: ''
                            };
                            // Force reactivity
                            this.selectedStudent = { ...this.selectedStudent };
                        }
                        this.showToast('success', 'Dokumen Disetujui ✓', data.message);
                    } else {
                        this.showToast('error', 'Gagal', 'Terjadi kesalahan sistem.');
                    }
                })
                .catch(() => this.showToast('error', 'Gagal', 'Terjadi kesalahan jaringan.'))
                .finally(() => { this.isLoading = false; });
            },
            
            submitReject() {
                if (!this.rejectReason.trim()) {
                    this.showToast('error', 'Error', 'Catatan wajib diisi.');
                    return;
                }
                if (this.isLoading) return;
                this.isLoading = true;

                const endpoint = this.rejectAction === 'rejected'
                    ? `/eoffice/kp/koordinator/validasi-berkas/${this.rejectDoc.id}/reject`
                    : `/eoffice/kp/koordinator/validasi-berkas/${this.rejectDoc.id}/revise`;

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ revision_note: this.rejectReason })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const newStatus = this.rejectAction;
                        const phase = this.activeTab;
                        const idx = this.selectedStudent.dokumen[phase].findIndex(d => d.id === this.rejectDoc.id);
                        if (idx !== -1) {
                            this.selectedStudent.dokumen[phase][idx] = {
                                ...this.selectedStudent.dokumen[phase][idx],
                                status: newStatus,
                                catatan: this.rejectReason
                            };
                            this.selectedStudent = { ...this.selectedStudent };
                        }
                        const msg = newStatus === 'rejected' ? 'Dokumen Ditolak' : 'Revisi Dikirim';
                        this.showToast('success', msg, data.message);
                        this.rejectModalOpen = false;
                    } else {
                        this.showToast('error', 'Gagal', 'Terjadi kesalahan sistem.');
                    }
                })
                .catch(() => this.showToast('error', 'Gagal', 'Terjadi kesalahan jaringan.'))
                .finally(() => { this.isLoading = false; });
            },
            
            showToast(type, title, message) {
                this.toast.type = type;
                this.toast.title = title;
                this.toast.message = message;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            }
        }
    }
</script>
</body>
</html>
