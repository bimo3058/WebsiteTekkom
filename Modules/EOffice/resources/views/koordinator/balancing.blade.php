<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Balancing Dosen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .drag-over { background-color: #f1f5f9; border-color: #3b82f6; }
        .dragging { opacity: 0.5; }
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="balancingApp()" x-cloak>
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden"
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
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

            <!-- Active Menu -->
            <a href="{{ route('eoffice.kp.koordinator.balancing') }}"
                class="flex items-center px-4 py-3 mb-1 text-sm font-semibold text-indigo-700 bg-white shadow-sm border border-indigo-100 rounded-xl relative">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Balancing Dosen
            </a>
            
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Approval Berkas
            </a>
            
            <a href="{{ route('eoffice.kp.koordinator.nilai_lapangan') }}"
                class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Nilai Lapangan
            </a>
            <!-- Pengaturan Menu -->
            <a href="{{ route('eoffice.kp.koordinator.pengaturan') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.pengaturan') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>
        </div>

                @if(auth()->user() && auth()->user()->email === 'ike.pertiwi@undip.ac.id')
        <div class="px-4 pb-4 mt-auto">
            <a href="{{ route('eoffice.kp.dosen.dashboard') }}" class="flex items-center px-4 py-2.5 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-all text-sm font-semibold border border-emerald-200 shadow-sm">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Beralih ke Dosen
            </a>
        </div>
        @endif
        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                <div
                    class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">
                    {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                </div>
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
        <header
            class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0 shrink-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true"
                    class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Sistem</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Balancing Dosen</span>
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
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            
            <!-- Toast Notification -->
            <div x-show="toast.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed top-20 right-4 sm:right-8 z-50 bg-white border shadow-lg rounded-xl flex items-center gap-3 px-4 py-3 min-w-[300px]"
                 :class="toast.type === 'success' ? 'border-emerald-200' : 'border-red-200'">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                     :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900" x-text="toast.title"></p>
                    <p class="text-xs text-slate-500" x-text="toast.message"></p>
                </div>
                <button type="button" @click="toast.show = false" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Header & Actions -->
            <form action="{{ route('eoffice.kp.koordinator.balancing.store') }}" method="POST" id="balancingForm" @submit.prevent="submitForm">
                @csrf
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Balancing Dosen</h1>
                        <p class="text-sm text-slate-500 mt-1">Kelola pembagian mahasiswa KP ke dosen pembimbing secara efisien.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="confirmAutoBalance()" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Auto Balancing
                        </button>
                        <button type="button" @click="submitForm('draft')" class="inline-flex items-center justify-center px-5 py-2 bg-slate-900 border border-transparent rounded-lg text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors" :disabled="isSaving" :class="{'opacity-75 cursor-not-allowed': isSaving}">
                            <svg x-show="!isSaving" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <svg x-show="isSaving" class="animate-spin w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Progress'"></span>
                        </button>
                        <button type="button" @click="showFinalizeModal = true" class="inline-flex items-center justify-center px-5 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Finalisasi Balancing
                        </button>
                    </div>
                </div>

                <!-- Dosen Quota Cards -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Pengaturan Kuota Dosen</h2>
                        <div class="relative w-64">
                            <input type="text" x-model="searchDosen" placeholder="Cari dosen..." class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm">
                            <svg class="absolute left-3 top-2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    <div class="flex overflow-x-auto pb-4 gap-4 snap-x">
                        <template x-for="dosen in filteredDosens" :key="dosen.id">
                            <div class="snap-start min-w-[240px] bg-white border rounded-xl p-4 shadow-sm flex-shrink-0 transition-all"
                                 :class="getDosenStatusColor(dosen).border">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                                        <span class="text-sm font-bold text-slate-600" x-text="dosen.name.substring(0,2).toUpperCase()"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate" :title="dosen.name" x-text="dosen.name"></p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">NIDN: -</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-bold" :class="getDosenStatusColor(dosen).text" x-text="dosen.mahasiswas.length"></span>
                                        <span class="text-xs font-medium text-slate-500">/ <span x-text="dosen.kuota_maksimal"></span> Mhs</span>
                                    </div>
                                    <div class="w-16">
                                        <input type="number" min="1" x-model.number="dosen.kuota_maksimal" class="w-full text-center py-1 px-2 text-xs border border-slate-200 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
                                    </div>
                                </div>
                                <!-- Progress bar -->
                                <div class="w-full h-1.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300"
                                         :class="getDosenStatusColor(dosen).bg"
                                         :style="`width: ${Math.min((dosen.mahasiswas.length / dosen.kuota_maksimal) * 100, 100)}%`"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Auto Balancing Banner -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <h3 class="font-bold text-blue-900">Sistem Auto Balancing</h3>
                        </div>
                        <p class="text-sm text-blue-700">Sistem akan membagi mahasiswa secara otomatis berdasarkan kuota dosen yang tersedia.</p>
                        <div class="flex items-center gap-4 mt-3 text-xs font-medium">
                            <span class="px-2.5 py-1 bg-white rounded-md text-blue-800 border border-blue-200 shadow-sm"><span x-text="unassignedStudents.length"></span> Mahasiswa belum mendapat dosen</span>
                            <span class="px-2.5 py-1 bg-white rounded-md text-indigo-800 border border-indigo-200 shadow-sm"><span x-text="totalSisaKuota"></span> Total kuota tersedia</span>
                        </div>
                    </div>
                    <button type="button" @click="confirmAutoBalance()" class="shrink-0 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center">
                        Jalankan Auto Balancing
                    </button>
                </div>

                <!-- Main Balancing Workspace (2 Columns) -->
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- Left Col: Unassigned Students -->
                    <div class="w-full lg:w-1/3 flex flex-col gap-4">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col h-[600px]">
                            <div class="p-4 border-b border-slate-100">
                                <h3 class="font-bold text-slate-800 flex items-center justify-between">
                                    Mahasiswa Belum Dapat Dosen
                                    <span class="bg-slate-100 text-slate-600 py-0.5 px-2.5 rounded-full text-xs" x-text="unassignedStudents.length"></span>
                                </h3>
                                <div class="mt-3 relative">
                                    <input type="text" x-model="searchStudent" placeholder="Cari nama/NIM..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                            
                            <!-- Student List (Droppable back to unassigned) -->
                            <div class="flex-1 overflow-y-auto p-3 bg-slate-50/50 space-y-2"
                                 @dragover.prevent="dragOverTarget = 'unassigned'"
                                 @dragleave="dragOverTarget = null"
                                 @drop.prevent="onDropUnassigned()"
                                 :class="{'bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg m-2': dragOverTarget === 'unassigned'}">
                                
                                <template x-if="filteredUnassigned.length === 0">
                                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                                        <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm font-medium">Semua mahasiswa yang dicari sudah ter-assign.</p>
                                    </div>
                                </template>

                                <template x-for="mhs in filteredUnassigned" :key="mhs.id">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm cursor-move hover:border-blue-300 hover:shadow-md transition-all group"
                                         draggable="true"
                                         @dragstart="startDrag(mhs, 'unassigned', null)"
                                         @dragend="endDrag()">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors" x-text="mhs.nama_mahasiswa"></p>
                                                <p class="text-xs text-slate-500 mt-0.5" x-text="mhs.nim"></p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium" :class="getBadgeClass(mhs.status)" x-text="getBadgeText(mhs.status)"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 mt-2 line-clamp-1 border-t border-slate-50 pt-2" :title="mhs.rencana_judul" x-text="mhs.rencana_judul || 'Belum ada rencana judul'"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right Col: Dosen Drop Targets -->
                    <div class="w-full lg:w-2/3">
                        <div class="bg-slate-100/50 rounded-xl p-4 h-[600px] overflow-y-auto border border-slate-200/60">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                <template x-for="dosen in filteredDosens" :key="dosen.id">
                                    <div class="bg-white rounded-xl border shadow-sm flex flex-col h-72 transition-all"
                                         :class="[getDosenStatusColor(dosen).border, dragOverTarget === dosen.id ? 'ring-2 ring-blue-400 shadow-md scale-[1.02]' : '']"
                                         @dragover.prevent="dragOverTarget = dosen.id"
                                         @dragleave="dragOverTarget = null"
                                         @drop.prevent="onDropDosen(dosen)">
                                        
                                        <!-- Dosen Header -->
                                        <div class="p-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-xs font-bold text-slate-600" x-text="dosen.name.substring(0,2).toUpperCase()"></span>
                                                </div>
                                                <div class="truncate">
                                                    <p class="text-sm font-bold text-slate-800 truncate" :title="dosen.name" x-text="dosen.name"></p>
                                                    <div class="flex items-center gap-1 mt-0.5">
                                                        <div class="w-1.5 h-1.5 rounded-full" :class="getDosenStatusColor(dosen).bg"></div>
                                                        <p class="text-[10px] text-slate-500 font-medium"><span x-text="dosen.mahasiswas.length"></span> / <span x-text="dosen.kuota_maksimal"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Assigned Students Area -->
                                        <div class="flex-1 p-2 overflow-y-auto space-y-2 bg-slate-50/30">
                                            <template x-if="dosen.mahasiswas.length === 0">
                                                <div class="h-full flex items-center justify-center">
                                                    <p class="text-[11px] text-slate-400 text-center px-4 border-2 border-dashed border-slate-200 rounded-lg w-full py-6">Tarik mahasiswa ke sini</p>
                                                </div>
                                            </template>
                                            
                                            <template x-for="mhs in dosen.mahasiswas" :key="mhs.id">
                                                <div class="bg-white p-2.5 rounded-lg border border-slate-200 shadow-sm text-sm cursor-move hover:border-slate-300 transition-colors group relative"
                                                     draggable="true"
                                                     @dragstart="startDrag(mhs, 'dosen', dosen.id)"
                                                     @dragend="endDrag()">
                                                    <div class="flex justify-between items-start pr-6">
                                                        <div class="truncate">
                                                            <p class="text-xs font-bold text-slate-800 truncate" :title="mhs.nama_mahasiswa" x-text="mhs.nama_mahasiswa"></p>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                <p class="text-[10px] text-slate-500" x-text="mhs.nim"></p>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider" :class="getBadgeClass(mhs.status)" x-text="getBadgeText(mhs.status)"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" @click.stop="removeFromDosen(dosen.id, mhs)" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 transition-colors" title="Hapus dari dosen ini">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                    
                                                    <!-- Hidden inputs for form submission -->
                                                    <input type="hidden" :name="`assignments[${mhs.id}][kp_id]`" :value="mhs.id">
                                                    <input type="hidden" :name="`assignments[${mhs.id}][dosen_id]`" :value="dosen.id">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Statistics -->
                <div class="mt-8 grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 font-medium mb-1">Total Mhs KP</p>
                        <p class="text-xl font-bold text-slate-800" x-text="totalMahasiswa"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 font-medium mb-1">Sudah Penempatan</p>
                        <p class="text-xl font-bold text-blue-600" x-text="totalAssigned"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 font-medium mb-1">Belum Penempatan</p>
                        <p class="text-xl font-bold text-orange-500" x-text="unassignedStudents.length"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 font-medium mb-1">Total Dosen</p>
                        <p class="text-xl font-bold text-slate-800" x-text="dosens.length"></p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 font-medium mb-1">Sisa Kuota Global</p>
                        <p class="text-xl font-bold text-emerald-600" x-text="totalSisaKuota"></p>
                    </div>
                </div>

                <!-- Modal Auto Balance Confirmation -->
                <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showConfirmModal = false" x-transition.opacity></div>
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative z-10"
                         x-show="showConfirmModal"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        <div class="p-6">
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-4 mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Jalankan Auto Balancing?</h3>
                            <p class="text-sm text-slate-500 text-center mb-6">Sistem akan secara acak mendistribusikan <span class="font-bold text-slate-800" x-text="unassignedStudents.length"></span> mahasiswa ke dosen yang kuotanya masih tersedia. Tindakan ini tidak bisa di-undo sebelum Anda menyimpannya.</p>
                            
                            <div class="flex gap-3">
                                <button type="button" @click="showConfirmModal = false" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                                <button type="button" @click="executeAutoBalance()" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Jalankan</button>
                            </div>
                        </div>
                    </div>
                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end gap-3 sticky bottom-0 bg-slate-50/80 backdrop-blur-sm p-4 border-t border-slate-200">
                    <button type="button" @click="submitForm('draft')" class="px-6 py-2 bg-white border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 shadow-sm transition-all text-sm">Simpan Draft</button>
                    <button type="button" @click="showFinalizeModal = true" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-all text-sm">Finalisasi</button>
                </div>

            </form>
        </main>
    </div>
    <!-- Confirm Finalize Modal -->
    <div x-show="showFinalizeModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="showFinalizeModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showFinalizeModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showFinalizeModal = false"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">Finalisasi Balancing</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">Mahasiswa yang sudah difinalisasi akan tampil pada role dosen pembimbing dan mahasiswa. Lanjutkan?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                        <button type="button" @click="submitForm('finalize'); showFinalizeModal = false" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Lanjutkan</button>
                        <button type="button" @click="showFinalizeModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function balancingApp() {
        return {
            sidebarOpen: false,
            unassignedStudents: @json($unassignedStudents),
            dosens: @json($dosens),
            searchStudent: '',
            searchDosen: '',
            isSaving: false,
            
            draggedStudent: null,
            sourceType: null,
            sourceId: null,
            dragOverTarget: null,
            
            showConfirmModal: false,
            
            toast: {
                show: false,
                type: 'success',
                title: '',
                message: ''
            },

            init() {
                // Parse kuota to int for safety
                this.dosens.forEach(d => {
                    d.kuota_maksimal = parseInt(d.kuota_maksimal) || 10;
                    if(!d.mahasiswas) d.mahasiswas = [];
                });
            },

            showToast(type, title, message) {
                this.toast.type = type;
                this.toast.title = title;
                this.toast.message = message;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },

            get filteredUnassigned() {
                if (!this.searchStudent) return this.unassignedStudents;
                const lowerSearch = this.searchStudent.toLowerCase();
                return this.unassignedStudents.filter(s => 
                    (s.nama_mahasiswa && s.nama_mahasiswa.toLowerCase().includes(lowerSearch)) ||
                    (s.nim && s.nim.toLowerCase().includes(lowerSearch))
                );
            },

            get filteredDosens() {
                if (!this.searchDosen) return this.dosens;
                const lowerSearch = this.searchDosen.toLowerCase();
                return this.dosens.filter(d => d.name.toLowerCase().includes(lowerSearch));
            },

            get totalMahasiswa() {
                let total = this.unassignedStudents.length;
                this.dosens.forEach(d => total += d.mahasiswas.length);
                return total;
            },

            get totalAssigned() {
                let total = 0;
                this.dosens.forEach(d => total += d.mahasiswas.length);
                return total;
            },

            get totalSisaKuota() {
                let sisa = 0;
                this.dosens.forEach(d => {
                    let s = parseInt(d.kuota_maksimal) - d.mahasiswas.length;
                    if (s > 0) sisa += s;
                });
                return sisa;
            },

            getDosenStatusColor(dosen) {
                const max = parseInt(dosen.kuota_maksimal);
                const current = dosen.mahasiswas.length;
                const ratio = current / max;
                
                if (ratio >= 1) return { border: 'border-red-200', bg: 'bg-red-500', text: 'text-red-600' };
                if (ratio >= 0.8) return { border: 'border-amber-200', bg: 'bg-amber-400', text: 'text-amber-600' };
                return { border: 'border-emerald-200', bg: 'bg-emerald-500', text: 'text-emerald-600' };
            },

            // Drag and Drop Handlers
            startDrag(mhs, type, srcId) {
                this.draggedStudent = mhs;
                this.sourceType = type;
                this.sourceId = srcId;
            },

            endDrag() {
                this.draggedStudent = null;
                this.sourceType = null;
                this.sourceId = null;
                this.dragOverTarget = null;
            },

            onDropUnassigned() {
                if (!this.draggedStudent || this.sourceType === 'unassigned') {
                    this.endDrag();
                    return;
                }
                
                // Move from dosen to unassigned
                if (this.sourceType === 'dosen') {
                    const srcDosen = this.dosens.find(d => d.id === this.sourceId);
                    if (srcDosen) {
                        srcDosen.mahasiswas = srcDosen.mahasiswas.filter(s => s.id !== this.draggedStudent.id);
                        this.draggedStudent.status = 'belum';
                        this.unassignedStudents.push(this.draggedStudent);
                        this.autoSave();
                    }
                }
                this.endDrag();
            },

            onDropDosen(targetDosen) {
                if (!this.draggedStudent) {
                    this.endDrag();
                    return;
                }

                // Check quota
                if (targetDosen.mahasiswas.length >= parseInt(targetDosen.kuota_maksimal)) {
                    this.showToast('error', 'Kuota Penuh', `Kuota untuk dosen ${targetDosen.name} sudah penuh!`);
                    this.endDrag();
                    return;
                }

                // Move from unassigned
                if (this.sourceType === 'unassigned') {
                    this.unassignedStudents = this.unassignedStudents.filter(s => s.id !== this.draggedStudent.id);
                    this.draggedStudent.status = this.draggedStudent.status === 'finalized' ? 'finalized' : 'draft';
                    targetDosen.mahasiswas.push(this.draggedStudent);
                    this.autoSave();
                } 
                // Move from another dosen
                else if (this.sourceType === 'dosen') {
                    if (this.sourceId === targetDosen.id) {
                        this.endDrag();
                        return; // Same target
                    }
                    const srcDosen = this.dosens.find(d => d.id === this.sourceId);
                    if (srcDosen) {
                        srcDosen.mahasiswas = srcDosen.mahasiswas.filter(s => s.id !== this.draggedStudent.id);
                        this.draggedStudent.status = this.draggedStudent.status === 'finalized' ? 'finalized' : 'draft';
                        targetDosen.mahasiswas.push(this.draggedStudent);
                        this.autoSave();
                    }
                }

                this.endDrag();
            },

            removeFromDosen(dosenId, mhs) {
                const dosen = this.dosens.find(d => d.id === dosenId);
                if (dosen) {
                    dosen.mahasiswas = dosen.mahasiswas.filter(s => s.id !== mhs.id);
                    mhs.status = 'belum';
                    this.unassignedStudents.push(mhs);
                    this.autoSave();
                }
            },

            // Auto Balancing Logic
            confirmAutoBalance() {
                if (this.unassignedStudents.length === 0) {
                    this.showToast('error', 'Tidak ada mahasiswa', 'Semua mahasiswa sudah mendapatkan dosen pembimbing.');
                    return;
                }
                if (this.totalSisaKuota < this.unassignedStudents.length) {
                    this.showToast('error', 'Kuota tidak mencukupi', `Sisa kuota (${this.totalSisaKuota}) kurang dari jumlah mahasiswa (${this.unassignedStudents.length}). Silakan tambah kuota dosen.`);
                    return;
                }
                this.showConfirmModal = true;
            },

            executeAutoBalance() {
                this.showConfirmModal = false;
                
                let unassigned = [...this.unassignedStudents];
                
                // Sort dosens by remaining quota descending
                let sortedDosens = [...this.dosens].sort((a, b) => {
                    let sisaA = parseInt(a.kuota_maksimal) - a.mahasiswas.length;
                    let sisaB = parseInt(b.kuota_maksimal) - b.mahasiswas.length;
                    return sisaB - sisaA;
                });

                let assignedCount = 0;
                while (unassigned.length > 0) {
                    let assignedInThisRound = false;
                    for (let i = 0; i < sortedDosens.length; i++) {
                        if (unassigned.length === 0) break;
                        
                        let dRef = this.dosens.find(d => d.id === sortedDosens[i].id);
                        if (dRef.mahasiswas.length < parseInt(dRef.kuota_maksimal)) {
                            let student = unassigned.shift();
                            student.status = 'draft';
                            dRef.mahasiswas.push(student);
                            this.unassignedStudents = this.unassignedStudents.filter(s => s.id !== student.id);
                            assignedInThisRound = true;
                            assignedCount++;
                        }
                    }
                    if (!assignedInThisRound) {
                        // Means no dosen has quota left
                        break;
                    }
                }

                if (assignedCount > 0) {
                    this.autoSave(true, `${assignedCount} mahasiswa berhasil didistribusikan secara otomatis!`);
                }
            },

            getBadgeClass(status) {
                if (status === 'finalized') return 'bg-indigo-100 text-indigo-700 border-indigo-200 border';
                if (status === 'draft') return 'bg-amber-100 text-amber-700 border-amber-200 border';
                return 'bg-slate-100 text-slate-600 border-slate-200 border';
            },

            getBadgeText(status) {
                if (status === 'finalized') return 'Final';
                if (status === 'draft') return 'Draft';
                return 'Belum';
            },

            submitForm(action = 'draft') {
                this.isSaving = true;
                const form = document.getElementById('balancingForm');
                
                // Remove existing hidden inputs if any
                document.querySelectorAll('.balancing-payload').forEach(el => el.remove());
                
                const dosensInput = document.createElement('input');
                dosensInput.type = 'hidden';
                dosensInput.name = 'dosens';
                dosensInput.className = 'balancing-payload';
                dosensInput.value = JSON.stringify(this.dosens);
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.className = 'balancing-payload';
                actionInput.value = action;

                form.appendChild(dosensInput);
                form.appendChild(actionInput);
                
                form.submit();
            },

            autoSave(showToast = false, customMessage = 'Perubahan otomatis tersimpan') {
                this.isSaving = true;
                const payload = {
                    dosens: JSON.stringify(this.dosens),
                    action: 'draft',
                    _token: document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : document.querySelector('input[name="_token"]').value
                };

                fetch(document.getElementById('balancingForm').action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    this.isSaving = false;
                    if (data.success && showToast) {
                        this.showToast('success', 'Auto Save Aktif', customMessage);
                    }
                })
                .catch(error => {
                    this.isSaving = false;
                    console.error('Error auto-saving:', error);
                    this.showToast('error', 'Gagal', 'Terjadi kesalahan saat menyimpan otomatis.');
                });
            }
        }
    }
</script>

@if(session('success'))
<script>
    document.addEventListener('alpine:init', () => {
        setTimeout(() => {
            const app = document.querySelector('[x-data="balancingApp()"]').__x.$data;
            app.showToast('success', 'Berhasil', '{{ session('success') }}');
        }, 100);
    });
</script>
@endif

</body>
</html>
