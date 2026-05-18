<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - FAQ & Timeline KP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .drag-active {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
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
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased"
    x-data="{ sidebarOpen: false, activeTab: 'dokumen', faqModalOpen: false, deleteFaqOpen: false, deleteDokumenOpen: false, deleteId: null }">
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

                <!-- Informasi Menu (Expanded) -->
                <div class="mb-1" x-data="{ expanded: true }">
                    <button @click="expanded = !expanded"
                        class="w-full flex items-center justify-between px-4 py-3 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi
                        </div>
                        <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform text-indigo-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="expanded" x-collapse class="pl-11 pr-4 py-2 space-y-1">
                        <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}"
                            class="flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-slate-50 rounded-lg transition-colors relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-slate-200">
                            Pengumuman
                        </a>
                        <a href="{{ route('eoffice.kp.koordinator.faq') }}"
                            class="flex items-center px-3 py-2 text-sm font-semibold text-indigo-700 bg-white shadow-sm border border-indigo-100 rounded-lg relative before:absolute before:-left-5 before:top-1/2 before:w-3 before:h-px before:bg-indigo-200">
                            FAQ & Dokumen
                        </a>
                    </div>
                </div>

                <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Data Mahasiswa
                </a>

                <a href="{{ route('eoffice.kp.koordinator.balancing') }}"
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <a href="{{ route('eoffice.kp.koordinator.nilai_lapangan') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    Nilai Lapangan
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
                        <span
                            class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">Informasi</span>
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">FAQ &
                            Dokumen</span>
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
                    <!-- Toast Notification -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            x-init="setTimeout(() => show = false, 4000)"
                            class="fixed top-24 right-6 lg:right-10 z-50 bg-white border border-emerald-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] rounded-2xl flex items-start gap-4 px-5 py-4 min-w-[320px]">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center mt-0.5">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-900 mb-0.5">Berhasil!</p>
                                <p class="text-[13px] text-slate-500 leading-relaxed">{{ session('success') }}</p>
                            </div>
                            <button type="button" @click="show = false"
                                class="flex-shrink-0 text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-1 rounded-md transition-colors mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">FAQ & Timeline KP</h1>
                        <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">Kelola dokumen panduan mahasiswa dan
                            daftar pertanyaan yang sering ditanyakan (FAQ) seputar pelaksanaan Kerja Praktik/Magang.</p>
                    </div>

                    <!-- Custom Tabs -->
                    <div
                        class="flex items-center space-x-1 bg-slate-200/50 p-1 rounded-xl w-fit mb-8 border border-slate-200">
                        <button @click="activeTab = 'dokumen'"
                            :class="activeTab === 'dokumen' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-6 py-2 rounded-lg text-sm transition-all focus:outline-none">
                            Timeline KP
                        </button>
                        <button @click="activeTab = 'faq'"
                            :class="activeTab === 'faq' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-6 py-2 rounded-lg text-sm transition-all focus:outline-none">
                            Manajemen FAQ
                        </button>
                    </div>

                    <!-- TAB: DOKUMEN PANDUAN -->
                    <div x-show="activeTab === 'dokumen'" x-transition.opacity.duration.300ms>

                        <!-- Form Upload Dokumen -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                                <h2 class="text-lg font-bold text-slate-900">Upload Dokumen Baru</h2>
                                <p class="text-sm text-slate-500 mt-1">Upload dokumen PDF yang akan tampil pada
                                    dashboard mahasiswa.</p>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('eoffice.kp.koordinator.faq.dokumen.store') }}" method="POST"
                                    enctype="multipart/form-data" class="flex flex-col md:flex-row gap-6"
                                    x-data="{ dragging: false, fileName: '' }">
                                    @csrf
                                    <!-- Input Details -->
                                    <div class="flex-1 space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama
                                                Dokumen</label>
                                            <input type="text" name="judul" required
                                                class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-slate-50 hover:bg-white transition-all outline-none"
                                                placeholder="Contoh: Buku Panduan KP 2026">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Versi Dokumen
                                                <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                            <input type="text" name="version"
                                                class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-slate-50 hover:bg-white transition-all outline-none"
                                                placeholder="Contoh: v1.0">
                                        </div>
                                    </div>
                                    <!-- Drag & Drop Box -->
                                    <div class="flex-1">
                                        <label class="block text-sm font-bold text-slate-700 mb-1.5">File Dokumen
                                            (PDF/DOC)</label>
                                        <div class="mt-1 flex justify-center px-6 pt-6 pb-7 border-2 border-slate-200 border-dashed rounded-xl relative hover:bg-slate-50 transition-colors group cursor-pointer"
                                            :class="{ 'drag-active': dragging }" @dragover.prevent="dragging = true"
                                            @dragleave.prevent="dragging = false"
                                            @drop.prevent="dragging = false; $refs.docInput.files = $event.dataTransfer.files; fileName = $refs.docInput.files[0].name">

                                            <input type="file" name="attachment" x-ref="docInput"
                                                @change="fileName = $refs.docInput.files[0] ? $refs.docInput.files[0].name : ''"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                accept=".pdf,.doc,.docx" required>

                                            <div class="space-y-2 text-center" x-show="!fileName">
                                                <div
                                                    class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                                    </svg>
                                                </div>
                                                <div class="flex text-sm text-slate-600 justify-center">
                                                    <span
                                                        class="relative cursor-pointer rounded-md font-semibold text-indigo-600 hover:text-indigo-500">
                                                        <span>Upload file</span>
                                                    </span>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-slate-500">PDF up to 10MB</p>
                                            </div>

                                            <div class="flex flex-col items-center gap-2 text-center w-full"
                                                x-show="fileName" style="display: none;">
                                                <div
                                                    class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 mb-2">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-900 truncate w-full px-4"
                                                    x-text="fileName"></p>
                                                <button type="button"
                                                    @click.stop.prevent="$refs.docInput.value = ''; fileName = ''"
                                                    class="text-xs font-semibold text-red-500 hover:text-red-700 z-20 relative uppercase tracking-wider mt-1">Ganti
                                                    File</button>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <button type="submit"
                                                class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors w-full md:w-auto">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Upload Dokumen
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- History Dokumen -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-slate-200 bg-white flex justify-between items-center">
                                <h2 class="text-lg font-bold text-slate-900">History Upload Dokumen</h2>
                                <div class="relative w-64 hidden sm:block">
                                    <input type="text" placeholder="Cari dokumen..."
                                        class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50/80">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Nama File & Versi</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Waktu Upload</th>
                                            <th
                                                class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @forelse($dokumens as $doc)
                                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center border border-red-100">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p
                                                                class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                                                {{ $doc->judul }}</p>
                                                            <div
                                                                class="flex items-center text-xs text-slate-500 mt-0.5 space-x-2">
                                                                <span>{{ $doc->file_name }}</span>
                                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                                <span
                                                                    class="font-medium bg-slate-100 px-1.5 rounded">{{ $doc->version ?? 'v1.0' }}</span>
                                                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                                <span>{{ $doc->file_size ?? 'Unknown' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <p class="text-sm text-slate-900 font-medium">
                                                        {{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Oleh: {{ $doc->pembuat->name }}
                                                    </p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($doc->is_active)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase">Aktif</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide bg-slate-100 text-slate-600 border border-slate-200 uppercase">Archive</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button title="Preview PDF"
                                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors border border-transparent hover:border-indigo-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                        <button title="Hapus"
                                                            @click="deleteId = {{ $doc->id }}; deleteDokumenOpen = true"
                                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div
                                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                                        <svg class="w-8 h-8 text-slate-300" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-slate-900">Belum ada dokumen</p>
                                                    <p class="text-xs text-slate-500 mt-1">Gunakan form di atas untuk
                                                        mengupload panduan pertama.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: MANAJEMEN FAQ -->
                    <div x-show="activeTab === 'faq'" style="display: none;" x-transition.opacity.duration.300ms>

                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Daftar Pertanyaan (FAQ)</h2>
                            <button @click="faqModalOpen = true"
                                class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 shadow-sm transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah FAQ
                            </button>
                        </div>

                        <!-- FAQ Accordion List -->
                        <div class="space-y-4">
                            @forelse($faqs as $faq)
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
                                    x-data="{ expanded: false }">
                                    <div class="flex items-start justify-between px-6 py-5 cursor-pointer hover:bg-slate-50 transition-colors"
                                        @click="expanded = !expanded">
                                        <div class="flex-1 pr-4">
                                            <h3 class="text-[15px] font-bold text-slate-900 leading-snug"
                                                :class="expanded ? 'text-indigo-700' : ''">{{ $faq->pertanyaan }}</h3>
                                            <p class="text-xs text-slate-400 mt-1.5 font-medium flex items-center">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Update terakhir:
                                                {{ \Carbon\Carbon::parse($faq->updated_at)->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <div class="flex gap-1" @click.stop>
                                                <button
                                                    class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteId = {{ $faq->id }}; deleteFaqOpen = true"
                                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300"
                                                :class="expanded ? 'rotate-180 bg-indigo-100 text-indigo-600' : ''">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div x-show="expanded" x-collapse>
                                        <div
                                            class="px-6 py-5 bg-slate-50/50 border-t border-slate-100 text-sm text-slate-600 leading-relaxed border-l-4 border-l-indigo-500">
                                            {{ $faq->jawaban }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="bg-white rounded-2xl border border-slate-200 border-dashed p-12 text-center flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada FAQ</h3>
                                    <p class="text-sm text-slate-500 max-w-sm mb-6">Tambahkan daftar pertanyaan yang sering
                                        diajukan untuk mempermudah mahasiswa.</p>
                                </div>
                            @endforelse
                        </div>

                    </div>

                </div>
            </main>
        </div>

        <!-- Modal Form FAQ -->
        <div x-show="faqModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true"
            style="display: none;">
            <div x-show="faqModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="faqModalOpen" @click.away="faqModalOpen = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">

                        <form action="{{ route('eoffice.kp.koordinator.faq.store') }}" method="POST">
                            @csrf
                            <div class="px-6 py-5 border-b border-slate-100 bg-white flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">Tambah FAQ Baru</h3>
                                </div>
                                <button type="button" @click="faqModalOpen = false"
                                    class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 p-2 rounded-lg transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="px-6 py-6 space-y-5 bg-white">
                                <div
                                    class="text-amber-600 bg-amber-50 p-3 rounded-xl border border-amber-100 text-xs flex items-start gap-2 mb-2">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Note: Fitur tambah ke database belum aktif karena tabel eo_faq belum ada di
                                        database. Saat ini digunakan untuk keperluan design UI.</span>
                                </div>

                                <!-- Pertanyaan -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pertanyaan <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="pertanyaan" required
                                        class="block w-full rounded-xl border-slate-200 py-2.5 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none"
                                        placeholder="Masukkan pertanyaan...">
                                </div>

                                <!-- Jawaban -->
                                <div x-data="{ content: '' }">
                                    <div class="flex items-end justify-between mb-1.5">
                                        <label class="block text-sm font-bold text-slate-700">Jawaban <span
                                                class="text-red-500">*</span></label>
                                        <span class="text-[11px] text-slate-400 font-medium"
                                            x-text="content.length + '/500'"></span>
                                    </div>
                                    <textarea name="jawaban" x-model="content" required rows="5" maxlength="500"
                                        class="block w-full rounded-xl border-slate-200 py-3 px-4 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 border bg-white hover:border-slate-300 transition-all outline-none resize-y"
                                        placeholder="Masukkan jawaban secara lengkap..."></textarea>
                                </div>
                            </div>

                            <div
                                class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                                <button type="button" @click="faqModalOpen = false"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm focus:ring-4 focus:ring-slate-100 outline-none">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 focus:ring-4 focus:ring-indigo-100 outline-none flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan FAQ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals for deletion -->
        <div x-show="deleteFaqOpen" class="relative z-50" style="display: none;">
            <!-- Simple background backdrop for deletion modal -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus FAQ</h3>
                    <p class="text-sm text-slate-500 mb-6">Apakah Anda yakin ingin menghapus FAQ ini? Tindakan ini tidak
                        dapat dibatalkan.</p>
                    <div class="flex gap-3">
                        <button @click="deleteFaqOpen = false"
                            class="flex-1 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 border border-slate-300 hover:bg-slate-50">Batal</button>
                        <form x-bind:action="`/eoffice/kp/koordinator/faq/${deleteId}`" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="deleteDokumenOpen" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto flex items-center justify-center p-4">
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Dokumen</h3>
                    <p class="text-sm text-slate-500 mb-6">Dokumen ini akan dihapus dari sistem. Mahasiswa tidak bisa
                        melihatnya lagi.</p>
                    <div class="flex gap-3">
                        <button @click="deleteDokumenOpen = false"
                            class="flex-1 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-700 border border-slate-300 hover:bg-slate-50">Batal</button>
                        <form x-bind:action="`/eoffice/kp/koordinator/faq/dokumen/${deleteId}`" method="POST"
                            class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>