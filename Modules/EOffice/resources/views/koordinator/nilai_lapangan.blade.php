<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Nilai Lapangan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="nilaiLapanganApp()" x-cloak>
<div class="flex h-screen w-full overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/40 backdrop-blur-sm lg:hidden"
        x-transition.opacity @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-72 bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
        <!-- Logo -->
        <div class="h-20 flex items-center px-8 border-b border-slate-100">
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

            <a href="{{ route('eoffice.kp.koordinator.dashboard') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg> Dashboard
            </a>

            <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Informasi
            </a>

            <a href="{{ route('eoffice.kp.koordinator.data_mahasiswa') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Data Mahasiswa
            </a>

            <a href="{{ route('eoffice.kp.koordinator.balancing') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg> Balancing Dosen
            </a>
            
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Approval Berkas
            </a>
            
            <!-- Active Menu -->
            <a href="{{ route('eoffice.kp.koordinator.nilai_lapangan') }}" class="flex items-center px-4 py-3 mb-1 text-sm font-semibold text-indigo-700 bg-white shadow-sm border border-indigo-100 rounded-xl relative">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                Nilai Lapangan
            </a>
        </div>

        <div class="p-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm border border-indigo-200">K</div>
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
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0 shrink-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 mr-4 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <nav class="hidden sm:flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <span class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer" @click="viewMode = 'table'">Nilai Lapangan</span>
                    <template x-if="viewMode === 'detail'">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Berkas</span>
                        </div>
                    </template>
                </nav>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative">
                    <button class="p-2 text-slate-400 hover:text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-full transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-10 relative">

            <!-- Alert Success -->
            <div x-show="showAlert" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
                <div class="bg-emerald-100 p-1.5 rounded-full shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-emerald-800">Berhasil Dikonfirmasi!</p>
                    <p class="text-xs text-emerald-600 mt-0.5">Nilai lapangan berhasil disimpan ke sistem.</p>
                </div>
                <button @click="showAlert = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- View: TABLE -->
            <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-slate-100">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-1">Nilai Lapangan</h2>
                        <p class="text-sm text-slate-500 font-medium">Validasi dan sinkronisasi nilai lapangan mahasiswa kerja praktik.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Nama Mahasiswa</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">NIM</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">Berkas Nilai Lapangan</th>
                                    <th class="py-4 px-6 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="mhs in mahasiswas" :key="mhs.id">
                                    <tr class="hover:bg-slate-50/80 transition-colors duration-200 group">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-800" x-text="mhs.nama"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="text-sm font-medium text-slate-500 font-mono" x-text="mhs.nim"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <template x-if="mhs.file_nilai">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-500 shrink-0 border border-red-100">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-slate-700 truncate max-w-[200px]" x-text="mhs.file_nilai"></p>
                                                </div>
                                            </template>
                                            <template x-if="!mhs.file_nilai">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Belum Ada Berkas
                                                </span>
                                            </template>
                                        </td>
                                        <td class="py-4 px-6">
                                            <button @click="openDetail(mhs)" :disabled="!mhs.file_nilai" 
                                                class="px-4 py-2 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 w-full"
                                                :class="mhs.file_nilai ? 'bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-100 hover:border-transparent shadow-sm hover:shadow-md hover:shadow-indigo-500/20' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                                                Lihat Berkas
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- View: DETAIL -->
            <div x-show="viewMode === 'detail'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <button @click="viewMode = 'table'" class="mb-6 flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors bg-white hover:bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm w-fit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar Mahasiswa
                </button>

                <!-- PDF Viewer & Info Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_4px_24px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col lg:flex-row mb-6">
                    <!-- Embedded Viewer -->
                    <div class="flex-1 bg-slate-900 relative min-h-[500px] flex flex-col items-center justify-center text-center p-8 border-b lg:border-b-0 lg:border-r border-slate-200">
                        <!-- Simulated Loading State -->
                        <div x-show="loadingPdf" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-800 z-10 text-white">
                            <svg class="w-10 h-10 animate-spin text-indigo-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <p class="font-medium animate-pulse">Memuat Dokumen PDF...</p>
                        </div>
                        
                        <!-- Mock PDF Graphic -->
                        <div x-show="!loadingPdf" class="text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-bold text-white mb-2" x-text="selectedStudent?.file_nilai"></p>
                            <p class="text-sm opacity-60">Dokumen PDF dirender di sini via embed.</p>
                        </div>
                    </div>

                    <!-- Input Form Panel -->
                    <div class="w-full lg:w-96 bg-white shrink-0 flex flex-col">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight" x-text="selectedStudent?.nama"></h3>
                            <p class="text-sm font-bold text-indigo-600 font-mono mt-0.5" x-text="selectedStudent?.nim"></p>
                        </div>
                        <div class="p-6 flex-1">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nilai Lapangan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" x-model="inputNilai" min="0" max="100" placeholder="Masukkan nilai 0-100" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-base font-bold px-4 py-3 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 mt-2 font-medium mb-6">Nilai final hasil validasi dari berkas (0 - 100).</p>

                                <button @click="konfirmasiNilai()" class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl transition-all shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 group">
                                    Konfirmasi Nilai
                                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function nilaiLapanganApp() {
        return {
            sidebarOpen: false,
            viewMode: 'table', // 'table' or 'detail'
            mahasiswas: @json($mahasiswas),
            selectedStudent: null,
            inputNilai: '',
            inputCatatan: '',
            loadingPdf: false,
            showAlert: false,

            openDetail(mhs) {
                if(!mhs.file_nilai) return;
                this.selectedStudent = mhs;
                this.inputNilai = '';
                this.inputCatatan = '';
                this.loadingPdf = true;
                this.viewMode = 'detail';
                
                // Simulate network loading
                setTimeout(() => {
                    this.loadingPdf = false;
                }, 800);
            },

            konfirmasiNilai() {
                if(!this.inputNilai || this.inputNilai < 0 || this.inputNilai > 100) {
                    alert("Mohon masukkan nilai yang valid (0-100).");
                    return;
                }
                
                // Set success state
                const mhsIndex = this.mahasiswas.findIndex(m => m.id === this.selectedStudent.id);
                if(mhsIndex !== -1) {
                    this.mahasiswas[mhsIndex].status_nilai = 'Sudah Dinilai';
                }

                this.viewMode = 'table';
                this.showAlert = true;
                this.selectedStudent = null;

                // Hide alert automatically
                setTimeout(() => {
                    this.showAlert = false;
                }, 4000);
            }
        }
    }
</script>
</body>
</html>
