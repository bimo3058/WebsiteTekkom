<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Balancing Dosen</title>
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
        <div class="h-16 flex items-center px-6 border-b border-transparent">
            <div class="w-8 h-8 bg-slate-900 rounded flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
            <div>
                <h1 class="font-bold text-slate-900 text-sm leading-tight">SIKP</h1>
                <p class="text-[10px] text-slate-500 font-medium">Sistem Informasi KP</p>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-6 mb-2"><p class="text-[11px] font-semibold text-slate-400">Main Menu</p></div>
            <a href="{{ route('eoffice.kp.koordinator.dashboard') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <div class="px-6 mt-6 mb-2"><p class="text-[11px] font-semibold text-slate-400">Koordinator</p></div>
            <a href="{{ route('eoffice.kp.koordinator.pengumuman') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Membuat Pengumuman
            </a>
            <!-- Active Menu -->
            <a href="{{ route('eoffice.kp.koordinator.balancing') }}" class="flex items-center px-6 py-2.5 bg-slate-50 border-l-4 border-slate-900 text-slate-900 font-semibold text-sm">
                <svg class="w-5 h-5 mr-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Balancing Dosen
            </a>
            <a href="{{ route('eoffice.kp.koordinator.validasi_berkas') }}" class="flex items-center px-6 py-2.5 border-l-4 border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors text-sm font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Validasi & Approval Berkas
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
                <nav class="hidden sm:flex text-sm text-slate-500 font-medium">
                    <span class="text-slate-400">SIKP</span>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-900">Balancing Dosen</span>
                </nav>
            </div>
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">K</div>
                    <div class="hidden sm:block">
                        <p class="text-[13px] font-bold text-slate-900 leading-tight">Koordinator KP</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed top-20 right-4 sm:right-8 z-50 bg-white border border-emerald-200 shadow-lg rounded-xl flex items-center gap-3 px-4 py-3 min-w-[300px]">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900">Berhasil</p>
                    <p class="text-xs text-slate-500">{{ session('success') }}</p>
                </div>
                <button type="button" @click="show = false" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Balancing Dosen</h1>
                    <p class="text-sm text-slate-500 mt-1">Konfigurasikan pembagian kuota dosen pembimbing untuk mahasiswa KP.</p>
                </div>
                <div class="relative">
                    <button class="flex items-center justify-between w-full sm:w-auto min-w-[220px] px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Semester Genap 2025/2026
                        <svg class="w-4 h-4 text-slate-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Dosen Load Info (Horizontal Scrollable) -->
            <div class="mb-6">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Beban Bimbingan Saat Ini</p>
                <div class="flex overflow-x-auto pb-2 gap-3 snap-x">
                    @foreach($dosens as $dosen)
                    <div class="snap-start min-w-[160px] bg-white border {{ $dosen->jumlah_bimbingan >= 5 ? 'border-red-200 bg-red-50/30' : 'border-slate-200' }} rounded-xl p-3 shadow-sm flex-shrink-0">
                        <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $dosen->name }}">{{ $dosen->name }}</p>
                        <div class="flex items-end justify-between mt-2">
                            <p class="text-2xl font-extrabold {{ $dosen->jumlah_bimbingan >= 5 ? 'text-red-600' : 'text-blue-600' }}">{{ $dosen->jumlah_bimbingan }}</p>
                            <p class="text-[10px] font-medium text-slate-400 pb-1">Mhs</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Balancing Form & Table -->
            <form action="{{ route('eoffice.kp.koordinator.balancing.store') }}" method="POST">
                @csrf
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white">
                        <h2 class="text-base font-semibold text-slate-800">Tabel Assign Dosen Pembimbing</h2>
                        
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <!-- Filter & Search (Visual Only for now) -->
                            <div class="relative w-full sm:w-64">
                                <input type="text" placeholder="Cari mahasiswa..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors">
                                <svg class="absolute left-3 top-2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="button" class="hidden sm:flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 bg-white">
                                <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filter
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">No</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Data Mahasiswa</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Topik KP</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-72">Assign Dosen</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($mahasiswas as $index => $mhs)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                                                {{ strtoupper(substr($mhs->nama_mahasiswa ?? 'M', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">{{ $mhs->nama_mahasiswa ?? 'Nama Tidak Ditemukan' }}</p>
                                                <p class="text-xs text-slate-500">{{ $mhs->nim }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-sm text-slate-700 line-clamp-2" title="{{ $mhs->rencana_judul }}">{{ $mhs->rencana_judul ?? 'Belum ada judul' }}</p>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <input type="hidden" name="assignments[{{ $index }}][kp_id]" value="{{ $mhs->id }}">
                                        <select name="assignments[{{ $index }}][dosen_id]" class="block w-full rounded-lg border-slate-300 py-2 pl-3 pr-10 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm bg-slate-50 hover:bg-white transition-colors cursor-pointer border">
                                            <option value="">-- Pilih Dosen Pembimbing --</option>
                                            @foreach($dosens as $dosen)
                                                <option value="{{ $dosen->id }}">{{ $dosen->name }} (Beban: {{ $dosen->jumlah_bimbingan }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-900">Semua mahasiswa sudah memiliki Dosen Pembimbing!</p>
                                        <p class="text-xs text-slate-500 mt-1">Tidak ada mahasiswa yang perlu di-balancing saat ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($mahasiswas->isNotEmpty())
                    <div class="px-5 py-4 border-t border-slate-200 bg-slate-50 flex justify-between items-center">
                        <p class="text-xs text-slate-500 font-medium">Menampilkan {{ $mahasiswas->count() }} mahasiswa</p>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-800 active:bg-slate-900 focus:outline-none focus:border-slate-900 focus:ring ring-slate-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Simpan Perubahan
                        </button>
                    </div>
                    @endif
                </div>
            </form>

        </main>
    </div>
</div>
</body>
</html>
