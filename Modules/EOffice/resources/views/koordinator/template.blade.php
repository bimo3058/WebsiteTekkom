<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Template Dokumen Koordinator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .drag-active { border-color: #3b82f6 !important; background-color: #eff6ff !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ 
    sidebarOpen: false, 
    deleteModalOpen: false, 
    deleteId: null,
    isEditing: false, 
    formAction: '{{ route('eoffice.kp.koordinator.template.store') }}', 
    formData: { title: '', phase: 'pra_kp', fileName: '' }
}">
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

            <!-- Informasi Menu -->
            <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi
            </a>
            <a href="{{ route('eoffice.kp.koordinator.template') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.template') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Template Dokumen
            </a>

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
            <a href="{{ route('eoffice.kp.koordinator.nilai_lapangan') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Nilai Lapangan
            </a>
            <!-- Pengaturan Menu -->
            <a href="{{ route('eoffice.kp.koordinator.pengaturan') }}" class="{{ request()->routeIs('eoffice.kp.koordinator.pengaturan') ? 'flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full' : 'flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Approval Berkas
            </a>
        </div>
        
        <!-- User Profile Area in Sidebar -->
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
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">
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
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <a href="{{ route('eoffice.kp.koordinator.dashboard') }}" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Dashboard</a>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Template Dokumen</span>
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
                
                @if(session('error') || session('warning'))
                <div class="mb-6 p-4 rounded-xl border {{ session('error') ? 'bg-red-50 border-red-200 text-red-700' : 'bg-amber-50 border-amber-200 text-amber-700' }} flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <h4 class="font-bold">{{ session('error') ? 'Error' : 'Peringatan' }}</h4>
                        <p class="text-sm mt-1">{{ session('error') ?? session('warning') }}</p>
                    </div>
                </div>
                @endif

                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Manajemen Template Dokumen</h1>
                    <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Upload template dokumen sesuai fase Kerja Praktik (Pra KP, Saat KP, Pasca KP) untuk didownload oleh mahasiswa.</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-10 overflow-hidden" id="form-section">
                    <form :action="formAction" method="POST" enctype="multipart/form-data" x-data="{ dragging: false, fileName: '' }">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="p-6 md:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">
                                        Nama Template <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" x-model="formData.title" required class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none" placeholder="Contoh: Logbook Harian">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Fase KP <span class="text-red-500">*</span></label>
                                    <select name="phase" x-model="formData.phase" required class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none">
                                        <option value="pra_kp">Pra KP (Sebelum KP)</option>
                                        <option value="saat_kp">Saat KP (Pelaksanaan)</option>
                                        <option value="pasca_kp">Pasca KP (Selesai/Seminar)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex flex-col md:flex-row gap-6 items-start">
                                <!-- Upload Area -->
                                <div class="flex-1 w-full">
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Upload Template (.pdf, .doc, .docx) <span class="text-red-500" x-show="!isEditing">*</span></label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-xl relative hover:bg-slate-50 transition-colors group cursor-pointer"
                                         :class="{ 'drag-active': dragging }"
                                         @dragover.prevent="dragging = true"
                                         @dragleave.prevent="dragging = false"
                                         @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name">
                                        
                                        <input type="file" name="file_path" x-ref="fileInput" @change="fileName = $refs.fileInput.files[0] ? $refs.fileInput.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".pdf,.doc,.docx" :required="!isEditing">
                                        
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
                                            <p class="text-xs text-slate-500">PDF, DOC, DOCX up to 5MB</p>
                                        </div>

                                        <!-- File yang sudah ada (mode edit) -->
                                        <div class="flex items-center gap-3 text-left w-full" x-show="isEditing && formData.fileName && !fileName" style="display: none;">
                                            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate" x-text="formData.fileName"></p>
                                                <p class="text-xs text-emerald-600 font-medium">File saat ini tersimpan — upload baru untuk mengganti</p>
                                            </div>
                                        </div>

                                        <!-- File baru dipilih -->
                                        <div class="flex items-center gap-3 text-left w-full" x-show="fileName" style="display: none;">
                                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate" x-text="fileName"></p>
                                                <p class="text-xs text-slate-500">File baru siap diupload</p>
                                            </div>
                                            <button type="button" @click.stop.prevent="$refs.fileInput.value = ''; fileName = ''" class="text-slate-400 hover:text-red-500 p-1 rounded-md transition-colors z-20 relative">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-full flex flex-col justify-end h-full space-y-4 md:w-64">
                                    <div class="flex gap-2 w-full mt-7">
                                        <button type="button" x-show="isEditing" @click="isEditing = false; formAction = '{{ route('eoffice.kp.koordinator.template.store') }}'; formData = { title: '', phase: 'pra_kp', fileName: '' }" class="flex-1 px-5 py-3.5 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition-colors focus:ring-4 focus:ring-slate-100 outline-none flex items-center justify-center">
                                            Batal
                                        </button>
                                        <button type="submit" class="flex-1 px-5 py-3.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 outline-none flex items-center justify-center">
                                            <svg x-show="!isEditing" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <svg x-show="isEditing" style="display: none;" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span x-text="isEditing ? 'Update' : 'Upload Template'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Template List (Cards) -->
                <div class="space-y-4">
                    @forelse($templates as $item)
                    <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all overflow-hidden p-5 flex flex-col md:flex-row gap-5 items-center">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shrink-0 border border-indigo-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide {{ $item->phase == 'pra_kp' ? 'bg-amber-50 text-amber-700 border-amber-100' : ($item->phase == 'saat_kp' ? 'bg-indigo-50 text-indigo-700 border-indigo-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }} border uppercase">
                                    {{ str_replace('_', ' ', $item->phase) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1 truncate">{{ $item->title }}</h3>
                            @if($item->file_path)
                            <a href="{{ Storage::disk('public')->url($item->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 mt-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 100 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                {{ $item->file_name ?? basename($item->file_path) }}
                            </a>
                            @endif
                        </div>
                        
                        <div class="flex flex-row items-center gap-2 shrink-0">
                            <button type="button" @click='formData = { title: @json($item->title), phase: @json($item->phase), fileName: @json($item->file_name ?? basename($item->file_path ?? "")) }; formAction = `/eoffice/kp/koordinator/template/{{ $item->id }}`; isEditing = true; document.getElementById("form-section").scrollIntoView({ behavior: "smooth" });' class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Edit
                            </button>
                            <button type="button" @click="deleteId = {{ $item->id }}; deleteModalOpen = true;" class="inline-flex items-center justify-center p-2 bg-white border border-slate-200 text-red-500 rounded-lg hover:bg-red-50 hover:border-red-100 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Template Dokumen</h3>
                        <p class="text-sm text-slate-500 max-w-sm mb-6">Mulai upload template dokumen untuk masing-masing fase (Pra, Saat, Pasca) KP agar dapat diunduh mahasiswa.</p>
                    </div>
                    @endforelse
                </div>

            </div>
            
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
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
                                <h3 class="text-lg font-bold leading-6 text-slate-900" id="modal-title">Hapus Template</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus template dokumen ini? Data yang dihapus tidak dapat dikembalikan lagi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <form x-bind:action="`/eoffice/kp/koordinator/template/${deleteId}`" method="POST">
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
