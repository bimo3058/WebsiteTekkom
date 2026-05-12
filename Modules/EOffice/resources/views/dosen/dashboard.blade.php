<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Dashboard Dosen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <!-- Alpine.js untuk fitur Dropdown & Mobile Menu (opsional tapi disarankan) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 overflow-y-auto transition-transform duration-300 lg:static lg:translate-x-0 lg:block flex flex-col justify-between">
            <div>
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 border-b border-slate-200 px-6 mt-4 mb-4">
                    <div class="flex items-center gap-3 w-full">
                        <div class="w-10 h-10 bg-[#1e293b] rounded-lg flex items-center justify-center text-white font-bold">
                            <!-- Icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span class="font-bold text-lg text-slate-900 tracking-tight">SIKP</span>
                            <span class="text-[10px] text-slate-500 font-medium">Sistem Informasi KP</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="px-4 py-4 space-y-8">
                    <!-- Main Menu -->
                    <div>
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
                        <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                    </div>

                    <!-- Dosen Menu -->
                    <div>
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu Dosen</p>
                        <div class="space-y-1">
                            <!-- Menu dihapus sesuai permintaan -->
                            <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                List Anak Bimbingan
                            </a>
                            <a href="{{ route('kp.dosen.validasi_berkas') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Validasi & Approval Berkas
                            </a>
                            <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Berkas B4
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Bottom Settings / Logout -->
            <div class="p-4 border-t border-slate-200">
                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full overflow-hidden relative">
            
            <!-- Topbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <!-- Breadcrumbs -->
                    <div class="text-sm text-slate-500 font-medium">
                        SIKP <span class="mx-2">/</span> <span class="text-slate-900">Dashboard</span>
                    </div>
                </div>

                <!-- Right Topbar -->
                <div class="flex items-center gap-4">
                    <button class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    <button class="text-slate-400 hover:text-slate-600 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>
                    <div class="h-8 w-px bg-slate-200 mx-1"></div>
                    <div class="flex items-center gap-2 cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="hidden md:block text-sm text-right leading-tight">
                            <p class="font-semibold text-slate-700">Dosen Pembimbing</p>
                            <p class="text-[10px] text-slate-500">NIP. 198001012005011002</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mahasiswa Bimbingan</h1>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <select class="block w-full sm:w-auto pl-3 pr-10 py-2 text-sm border-slate-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm border text-slate-700">
                            <option>Semester Genap 2025/2026</option>
                            <option>Semester Ganjil 2025/2026</option>
                        </select>
                        <button class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-[#1e293b] hover:bg-slate-800 focus:outline-none transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Export Data
                        </button>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    
                    <!-- Table Header Tools -->
                    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <h2 class="text-base font-semibold text-slate-800">Tabel Daftar Bimbingan</h2>
                        
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative w-full sm:w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg leading-5 bg-white placeholder-slate-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" placeholder="Search mahasiswa...">
                            </div>
                            <button class="inline-flex items-center px-3 py-2 border border-slate-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition-colors">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table Responsive Wrapper -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Topik & Tempat KP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Fase</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($bimbingan as $index => $kp)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                                {{ strtoupper(substr($kp->nama_mahasiswa ?? $kp->nim ?? 'M', 0, 2)) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-slate-900">{{ $kp->nama_mahasiswa ?? 'Mahasiswa' }}</p>
                                                <p class="text-xs text-slate-500">NIM: {{ $kp->nim ?? 'Belum Diisi' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-slate-900 font-medium">{{ $kp->rencana_judul ?? 'Belum ada usulan judul' }}</p>
                                        <p class="text-xs text-slate-500">{{ $kp->rencana_tempat ?? 'Instansi belum ditentukan' }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($kp->status_kp == 'pending' || !$kp->status_kp)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800">
                                                <svg class="mr-1.5 h-2 w-2 text-amber-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Pra KP (Menunggu ACC)
                                            </span>
                                        @elseif($kp->status_kp == 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Saat KP
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <svg class="mr-1.5 h-2 w-2 text-emerald-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Pasca KP (Selesai)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('kp.dosen.bimbingan.show', $kp->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1.5 rounded-md hover:bg-blue-100 transition-colors">Tinjau</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            <p class="text-sm font-medium">Belum ada data pendaftaran KP.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination / Footer -->
                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div class="flex items-center text-sm text-slate-500">
                            <span>Per page</span>
                            <select class="mx-2 block pl-2 pr-6 py-1 text-xs border-slate-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md border bg-white">
                                <option>10</option>
                                <option>20</option>
                                <option>50</option>
                            </select>
                            <span>Showing 1 to {{ count($bimbingan) }} of {{ count($bimbingan) }} results</span>
                        </div>
                        
                        <div class="flex items-center gap-1">
                            <button class="p-1 rounded text-slate-400 hover:text-slate-600 hover:bg-slate-200 disabled:opacity-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                            <button class="w-8 h-8 rounded bg-[#1e293b] text-white text-sm font-medium flex items-center justify-center">1</button>
                            <button class="p-1 rounded text-slate-400 hover:text-slate-600 hover:bg-slate-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>
