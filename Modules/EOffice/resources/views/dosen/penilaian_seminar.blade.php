<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKP - Penilaian Seminar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased" x-data="penilaianApp()">
    <div class="flex h-screen w-full overflow-hidden bg-slate-50/50">

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
                    class="flex items-center px-4 py-3 mb-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-xl transition-all text-sm font-medium">
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
                    class="flex items-center px-4 py-3 mb-1 text-indigo-700 bg-indigo-50/50 rounded-xl transition-all text-sm font-semibold relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-1 before:bg-indigo-600 before:rounded-r-full">
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
                        <span class="text-indigo-700 font-semibold bg-indigo-50 px-2.5 py-1 rounded-md">Penilaian
                            Seminar</span>
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

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                <!-- Flash Messages -->
                @if(session('success'))
                    <div
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div
                        class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900">Penilaian Seminar & Approval Jadwal</h1>
                    <p class="text-sm text-slate-500 mt-1">Tinjau jadwal seminar yang diajukan dan berikan nilai seminar
                        mahasiswa.</p>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <h2 class="text-base font-semibold text-slate-800">Daftar Seminar KP Mahasiswa</h2>
                    </div>

                    @if($seminars->isEmpty())
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm font-medium text-slate-900">Belum ada pengajuan seminar</p>
                            <p class="text-xs text-slate-500 mt-1">Mahasiswa bimbingan Anda belum mengajukan jadwal seminar
                                KP.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Mahasiswa</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Jadwal Seminar</th>
                                        <th
                                            class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Tempat / Ruangan</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Penilaian</th>
                                        <th
                                            class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Approval Jadwal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($seminars as $sem)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="h-8 w-8 rounded bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                        {{ strtoupper(substr($sem->nama_mahasiswa ?? 'M', 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-slate-900">
                                                            {{ $sem->nama_mahasiswa ?? 'Mahasiswa' }}</p>
                                                        <p class="text-xs text-slate-500">{{ $sem->nim }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ $sem->tanggal_seminar ? \Carbon\Carbon::parse($sem->tanggal_seminar)->format('d M Y') : '-' }}
                                                </p>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    @php
                                                        $startTime = $sem->waktu_seminar;
                                                        $endTime = $startTime ? \Carbon\Carbon::parse($startTime)->addHours(2)->format('H:i') : '';
                                                    @endphp
                                                    Jam:
                                                    {{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '-' }} -
                                                    {{ $endTime ? $endTime : '-' }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-sm text-slate-700">{{ $sem->ruangan ?? 'Belum ditentukan' }}</span>
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                @if($sem->nilai_seminar_pembimbing !== null)
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 font-extrabold text-lg border border-emerald-100 shadow-sm"
                                                        @click="openDrawer({{ $sem->kp_id }}, '{{ $sem->nama_mahasiswa }}', '{{ $sem->nim }}', {{ $sem->nilai_seminar_pembimbing }})"
                                                        style="cursor: pointer;" title="Edit Nilai">
                                                        {{ $sem->nilai_seminar_pembimbing }}
                                                    </span>
                                                @else
                                                    <button
                                                        @click="openDrawer({{ $sem->kp_id }}, '{{ $sem->nama_mahasiswa }}', '{{ $sem->nim }}', null)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-semibold transition-colors">
                                                        Beri Nilai
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                                @if($sem->status_validasi_dosen == 'pending')
                                                    <div class="flex items-center justify-center gap-2">
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.penilaian_seminar.reject', $sem->kp_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" title="Tolak"
                                                                class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('eoffice.kp.dosen.penilaian_seminar.approve', $sem->kp_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit" title="Setujui"
                                                                class="p-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif($sem->status_validasi_dosen == 'approved')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Disetujui
                                                    </span>
                                                @elseif($sem->status_validasi_dosen == 'rejected')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
    <!-- Slide Over Drawer (From Left to Right) -->
    <div x-cloak x-show="drawerOpen" class="relative z-50" aria-labelledby="slide-over-title" role="dialog"
        aria-modal="true">
        <div x-show="drawerOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeDrawer()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Slide from RIGHT -->
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                    <div x-show="drawerOpen"
                        x-transition:enter="transform transition ease-out duration-300 sm:duration-400"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in duration-300"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                        class="pointer-events-auto w-screen max-w-lg">

                        <div
                            class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-200">

                            <!-- Drawer Header -->
                            <div
                                class="px-6 py-6 border-b border-slate-100 bg-slate-50/50 sticky top-0 z-10 backdrop-blur-md flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900" id="slide-over-title">Form Penilaian
                                        Seminar</h2>
                                    <p class="text-sm text-slate-500 mt-1">Beri atau ubah nilai seminar mahasiswa.</p>
                                </div>
                                <button type="button" @click="closeDrawer()"
                                    class="rounded-lg bg-white p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <span class="sr-only">Tutup</span>
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Drawer Body -->
                            <div class="flex-1 px-6 py-6 space-y-6">
                                <!-- Info Mahasiswa -->
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg border-2 border-white shadow-sm flex-shrink-0"
                                        x-text="selectedStudentName.charAt(0).toUpperCase()">
                                    </div>
                                    <div>
                                        <h1 class="text-lg font-bold text-slate-900" x-text="selectedStudentName"></h1>
                                        <p class="text-sm text-slate-500">NIM: <span
                                                class="font-semibold text-slate-700" x-text="selectedStudentNim"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-800">Nilai Dosen Pembimbing</p>
                                        <p class="text-xs text-blue-600 mt-1">Bagian ini hanya untuk <strong>Nilai
                                                Seminar Pembimbing</strong>. Nilai Lapangan diisi oleh Koordinator KP.
                                        </p>
                                    </div>
                                </div>

                                <form :action="'/eoffice/kp/dosen/bimbingan/' + selectedKpId + '/penilaian'"
                                    method="POST">
                                    @csrf
                                    <div>
                                        <label for="nilai_seminar_pembimbing"
                                            class="block text-sm font-semibold text-slate-700 mb-2">
                                            Nilai Seminar <span class="text-red-500">*</span>
                                            <span class="text-slate-400 font-normal">(0 - 100)</span>
                                        </label>
                                        <input type="number" id="nilai_seminar_pembimbing"
                                            name="nilai_seminar_pembimbing" min="0" max="100" step="0.01"
                                            x-model="selectedNilai" placeholder="Contoh: 85"
                                            class="w-full px-4 py-3 text-2xl font-bold text-slate-900 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-300 placeholder:font-normal placeholder:text-base"
                                            required>
                                    </div>

                                    <div class="mt-8 flex gap-3">
                                        <button type="button" @click="closeDrawer()"
                                            class="flex-1 px-6 py-3 border border-slate-300 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors text-sm text-center">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Simpan Nilai
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('penilaianApp', () => ({
                sidebarOpen: false,
                drawerOpen: false,
                selectedKpId: null,
                selectedStudentName: '',
                selectedStudentNim: '',
                selectedNilai: null,

                openDrawer(kpId, name, nim, nilai) {
                    this.selectedKpId = kpId;
                    this.selectedStudentName = name || 'Mahasiswa';
                    this.selectedStudentNim = nim || '';
                    this.selectedNilai = nilai;
                    this.drawerOpen = true;
                },

                closeDrawer() {
                    this.drawerOpen = false;
                    setTimeout(() => {
                        this.selectedKpId = null;
                        this.selectedStudentName = '';
                        this.selectedStudentNim = '';
                        this.selectedNilai = null;
                    }, 300);
                }
            }));
        });
    </script>
</body>