<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Pengumuman Koordinator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .drag-active { border-color: #3b82f6 !important; background-color: #eff6ff !important; }
        /* Custom scrollbar for clean look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false, modalOpen: false, deleteModalOpen: false, deleteId: null }">
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>

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

            <!-- Informasi Menu (Expanded) -->
            <div class="mb-1" x-data="{ expanded: true }">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-3 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Informasi
                    </div>
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="expanded" x-collapse class="pl-11 pr-4 py-2 space-y-1">
                    <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-3 py-2 text-sm font-semibold text-indigo-700 bg-white shadow-sm border border-indigo-100 rounded-lg relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-indigo-200">
                        Pengumuman
                    </a>
                    <a href="{{ route('eoffice.kp.koordinator.faq') }}" class="flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-colors relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-slate-200">
                        FAQ & Dokumen
                    </a>
                </div>
            </div>

            <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
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
        
        <!-- User Profile Area in Sidebar -->
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
                    <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Informasi</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Pengumuman</span>
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

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-10">
            
            <div class="max-w-6xl mx-auto">
                <!-- Toast Notification -->
                @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     x-init="setTimeout(() => show = false, 4000)"
                     class="fixed top-24 right-6 lg:right-10 z-50 bg-white border border-emerald-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center mt-0.5">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-900 mb-0.5">Berhasil!</p>
                        <p class="text-[13px] text-slate-500 leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button type="button" @click="show = false" class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif

                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Pengumuman</h1>
                        <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Kelola informasi dan pengumuman untuk mahasiswa. Pengumuman yang diterbitkan akan otomatis tampil dalam bentuk banner di dashboard mahasiswa.</p>
                    </div>
                    <button @click="modalOpen = true" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 transition-all shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Pengumuman
                    </button>
                </div>

                <!-- Filters & Search -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" placeholder="Cari judul pengumuman..." class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none">
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <select class="block w-full md:w-40 py-2 px-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none cursor-pointer">
                            <option value="">Semua Tipe</option>
                            <option value="pengumuman">Pengumuman</option>
                            <option value="timeline">Timeline</option>
                        </select>
                        <select class="block w-full md:w-40 py-2 px-3 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors outline-none cursor-pointer">
                            <option value="">Filter Tanggal</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                    </div>
                </div>

                <!-- Announcement List (Cards) -->
                <div class="space-y-4">
                    @forelse($pengumumen as $item)
                    <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all overflow-hidden p-5 flex flex-col md:flex-row gap-5">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                @if($item->is_published)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-amber-50 text-amber-700 border border-amber-100 uppercase">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Draft
                                    </span>
                                @endif
                                <span class="text-xs font-medium text-slate-400 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $item->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-slate-900 mb-2 truncate group-hover:text-indigo-700 transition-colors">{{ $item->judul }}</h3>
                            <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed mb-4">{{ $item->deskripsi }}</p>
                            
                            <!-- Attachment Mock (Optional visual) -->
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 cursor-pointer transition-colors">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                Lampiran_Pengumuman.pdf
                            </div>
                        </div>
                        
                        <div class="flex flex-row md:flex-col items-center justify-end gap-2 shrink-0 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-5">
                            <button type="button" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 md:px-3 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2 md:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span class="md:hidden">Edit</span>
                            </button>
                            <button type="button" @click="deleteId = {{ $item->id }}; deleteModalOpen = true;" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 md:px-3 py-2 bg-white border border-slate-200 text-red-500 text-sm font-medium rounded-lg hover:bg-red-50 hover:border-red-100 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2 md:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span class="md:hidden">Hapus</span>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Pengumuman</h3>
                        <p class="text-sm text-slate-500 max-w-sm mb-6">Mulai tambahkan pengumuman baru untuk memberikan informasi kepada mahasiswa.</p>
                        <button @click="modalOpen = true" class="inline-flex items-center justify-center px-4 py-2 bg-white border-2 border-indigo-100 text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Buat Pengumuman
                        </button>
                    </div>
                    @endforelse
                </div>

                <!-- Simple Pagination UI -->
                @if(count($pengumumen) > 0)
                <div class="mt-8 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-bold text-slate-900">1</span> sampai <span class="font-bold text-slate-900">{{ count($pengumumen) }}</span> dari <span class="font-bold text-slate-900">{{ count($pengumumen) }}</span> entri</p>
                    <div class="flex gap-1">
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-indigo-600 bg-indigo-600 text-white font-medium text-sm">1</button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-medium text-sm transition-colors">2</button>
                        <button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>

        </main>
    </div>

    <!-- Modal Form Alpine.js -->
    <div x-show="modalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="modalOpen" @click.away="modalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100">
                    
                    <form action="{{ route('eoffice.kp.koordinator.pengumuman.store') }}" method="POST" enctype="multipart/form-data" x-data="{ dragging: false, fileName: '' }">
                        @csrf
                        
                        <!-- Modal Header -->
                        <div class="px-6 py-5 border-b border-slate-100 bg-white flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900" id="modal-title">Tambah Pengumuman</h3>
                                <p class="text-sm text-slate-500 mt-0.5">Isi detail informasi untuk ditampilkan ke mahasiswa.</p>
                            </div>
                            <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 p-2 rounded-lg transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="px-6 py-6 space-y-5 bg-white">
                            
                            <!-- Judul -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Judul Pengumuman <span class="text-red-500">*</span></label>
                                <input type="text" name="judul" required class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none" placeholder="Masukkan judul yang jelas dan deskriptif">
                            </div>
                            
                            <!-- Tipe (Hidden for this specific UX, assuming all in this form are announcements, but keeping for logic compatibility) -->
                            <input type="hidden" name="tipe" value="pengumuman">

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Deskripsi Pengumuman <span class="text-red-500">*</span></label>
                                <textarea name="konten" required rows="5" class="block w-full rounded-xl border-slate-200 py-3 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none resize-y" placeholder="Tuliskan isi pengumuman secara lengkap di sini..."></textarea>
                            </div>

                            <!-- Upload Area -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Upload File / Lampiran <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl relative hover:bg-slate-50 transition-colors group cursor-pointer"
                                     :class="{ 'drag-active': dragging }"
                                     @dragover.prevent="dragging = true"
                                     @dragleave.prevent="dragging = false"
                                     @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name">
                                    
                                    <input type="file" name="attachment" x-ref="fileInput" @change="fileName = $refs.fileInput.files[0] ? $refs.fileInput.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".pdf,.doc,.docx">
                                    
                                    <div class="space-y-2 text-center" x-show="!fileName">
                                        <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <span class="relative cursor-pointer rounded-md font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                                                <span>Upload a file</span>
                                            </span>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-slate-500">PDF, DOC, DOCX up to 10MB</p>
                                    </div>

                                    <!-- File Preview -->
                                    <div class="flex items-center gap-3 text-left w-full" x-show="fileName" style="display: none;">
                                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 truncate" x-text="fileName"></p>
                                            <p class="text-xs text-slate-500">File siap diupload</p>
                                        </div>
                                        <button type="button" @click.stop.prevent="$refs.fileInput.value = ''; fileName = ''" class="text-slate-400 hover:text-red-500 p-1 rounded-md transition-colors z-20 relative">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-amber-600 flex items-start gap-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Info: Fitur upload file (database storage) memerlukan penambahan struktur di sisi Backend nanti. Untuk saat ini hanya demonstrasi UI.</span>
                                </div>
                            </div>

                            <!-- Publish Toggle -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Status Publikasi</p>
                                    <p class="text-xs text-slate-500">Tentukan apakah pengumuman langsung tampil.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="modalOpen = false" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm focus:ring-4 focus:ring-slate-100 outline-none">
                                Batal
                            </button>
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 outline-none flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Submit Pengumuman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal Alpine.js -->
    <div x-show="deleteModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="deleteModalOpen" @click.away="deleteModalOpen = false" x-transition class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title">Hapus Pengumuman</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus pengumuman ini? Data yang dihapus tidak dapat dikembalikan lagi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <!-- We use dynamic action for form submission using x-bind -->
                        <form x-bind:action="`/eoffice/kp/koordinator/pengumuman/${deleteId}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto transition-colors">Ya, Hapus</button>
                        </form>
                        <button type="button" @click="deleteModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
