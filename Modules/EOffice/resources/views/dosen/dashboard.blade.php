<x-eoffice::layouts.dosen title="Dashboard">
    @section('breadcrumbs')
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Dashboard</span>
    @endsection

    <div>
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1.5">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight"
                        style="font-family:'Inter Tight',sans-serif;">
                        Dashboard Dosen
                    </h1>
                    <span
                        class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold uppercase tracking-wider border border-indigo-100">
                        Dosen
                    </span>
                </div>
                <p class="text-sm font-medium text-slate-500">
                    Selamat datang, {{ auth()->user()->name }} &middot;
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            {{-- Period Filter --}}
            <div style="min-width:220px;" class="mt-2 sm:mt-0">
                <form action="{{ route('eoffice.kp.dosen.dashboard') }}" method="GET" id="filterForm">
                    <select name="periode_id" onchange="document.getElementById('filterForm').submit()" style="
                        width:100%; padding:8px 12px;
                        border:1px solid #E4E7EC; border-radius:8px;
                        font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#09090B;
                        background:#fff; cursor:pointer;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                    ">
                        <option value="all" {{ $selectedPeriode === 'all' ? 'selected' : '' }}>Semua Periode</option>
                        @if(isset($periodes))
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}" {{ $selectedPeriode == $p->id ? 'selected' : '' }}>Semester
                                    {{ $p->semester }} {{ $p->tahun_ajaran }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </form>
            </div>
        </div>

        {{-- Ringkasan Section --}}
        <div class="mb-8">
            <div class="flex items-center gap-2.5 mb-5">
                <div class="h-6 w-1 rounded-full bg-slate-800"></div>
                <h2 class="text-lg font-bold text-slate-900" style="font-family:'Inter Tight', sans-serif;">
                    Ringkasan
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Card 1 --}}
                <div
                    class="bg-white border border-slate-200 rounded-[12px] p-3.5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[22px] font-black text-slate-900 leading-none mb-1">
                            {{ $stats['total_bimbingan'] }}
                        </div>
                        <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Total Bimbingan Aktif
                        </h3>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div
                    class="bg-white border border-slate-200 rounded-[12px] p-3.5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[22px] font-black text-slate-900 leading-none mb-1">
                            {{ $stats['dokumen_pending'] }}
                        </div>
                        <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Validasi Dokumen</h3>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div
                    class="bg-white border {{ $stats['menunggu_nilai'] > 0 ? 'border-red-300 ring-2 ring-red-50' : 'border-slate-200' }} rounded-[12px] p-3.5 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-xl {{ $stats['menunggu_nilai'] > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-600' }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div
                            class="text-[22px] font-black {{ $stats['menunggu_nilai'] > 0 ? 'text-red-700' : 'text-slate-900' }} leading-none mb-1">
                            {{ $stats['menunggu_nilai'] }}
                        </div>
                        <h3
                            class="text-[11px] font-bold {{ $stats['menunggu_nilai'] > 0 ? 'text-red-600' : 'text-slate-500' }} uppercase tracking-wide">
                            {{ $stats['menunggu_nilai'] > 0 ? '⚠️ Menunggu Input Nilai' : 'Menunggu Input Nilai' }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- To Do List Section --}}
        <div>
            <div class="flex items-center gap-2.5 mb-5">
                <div class="h-6 w-1 rounded-full bg-slate-800"></div>
                <h2 class="text-lg font-bold text-slate-900" style="font-family:'Inter Tight', sans-serif;">
                    Dokumen Mahasiswa
                </h2>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                @if(!isset($dokumenTodos) || $dokumenTodos->isEmpty())
                    <div class="py-8 px-6 flex flex-col items-center justify-center text-center">
                        <div
                            class="w-12 h-12 bg-slate-50 rounded-[12px] flex items-center justify-center text-slate-300 mb-3 border border-slate-100 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1">Belum ada dokumen yang perlu ditinjau.</h3>
                        <p class="text-[11px] font-medium text-slate-400 max-w-xs">Anda telah menyelesaikan seluruh tugas
                            validasi dan tinjauan. Nikmati sisa waktu Anda!</p>
                    </div>
                @else
                    @php
                        // Pisahkan dokumen vs seminar
                        $dokumenTodos = $todoList->where('type', 'dokumen');
                        $seminarTodos = $todoList->where('type', 'seminar');

                        // Group dokumen by Mahasiswa Name
                        $groupedTodos = $dokumenTodos->groupBy('mahasiswa');
                        $topStudents = $groupedTodos->take(5);
                        $totalStudents = $groupedTodos->count();
                    @endphp
                    <div class="overflow-x-auto border-b border-t border-slate-200">
                        <table class="w-full text-left text-sm text-slate-600 border-collapse">
                            <thead
                                class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-10 text-center">NO</th>
                                    <th class="px-4 py-3 w-48">NAMA MAHASISWA</th>
                                    <th class="px-4 py-3">STATUS DOKUMEN</th>
                                    <th class="px-4 py-3 w-32 text-center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @php $mahasiswaIndex = 1; @endphp
                                @foreach($topStudents as $mahasiswa => $todos)
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-4 py-3 align-middle font-medium text-slate-500 text-center">
                                            {{ $mahasiswaIndex++ }}
                                        </td>
                                        <td class="px-4 py-3 align-middle">
                                            <span class="font-bold text-slate-800 text-xs">{{ $mahasiswa }}</span>
                                        </td>
                                        <td class="px-4 py-3 align-middle">
                                            <div class="flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                <span class="text-[11px] font-bold text-slate-700">Ada {{ count($todos) }}
                                                    Antrean Baru</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-0.5 truncate max-w-xs">
                                                ({{ $todos->pluck('title')->implode(', ') }})
                                            </p>
                                        </td>
                                        <td class="px-4 py-3 align-middle text-center">
                                            <a href="{{ $todos->first()->url }}"
                                                class="inline-flex items-center justify-center px-4 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded hover:bg-slate-50 hover:text-indigo-600 hover:border-slate-300 shadow-sm transition-all focus:ring-2 focus:ring-slate-200">
                                                Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($totalStudents > 5)
                            <div class="p-3 border-t border-slate-200 bg-slate-50 text-center">
                                <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center justify-center gap-1">
                                    👉 Lihat Semua Validasi ({{ $totalStudents }} Antrean Mahasiswa)
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Jadwal Seminar Section --}}
        <div class="mt-8">
            <div class="flex items-center gap-2.5 mb-5">
                <div class="h-6 w-1 rounded-full bg-emerald-500"></div>
                <h2 class="text-lg font-bold text-slate-900" style="font-family:'Inter Tight', sans-serif;">
                    Jadwal Seminar Terdekat (Terkonfirmasi)
                </h2>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                @if(!isset($seminarTodos) || $seminarTodos->isEmpty())
                    <div class="py-8 px-6 flex flex-col items-center justify-center text-center">
                        <div
                            class="w-12 h-12 bg-slate-50 rounded-[12px] flex items-center justify-center text-slate-300 mb-3 border border-slate-100 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1">Belum ada agenda seminar.</h3>
                        <p class="text-[11px] font-medium text-slate-400 max-w-xs">Jadwal mahasiswa yang sudah terkonfirmasi
                            akan muncul di sini sebagai pengingat Anda.</p>
                    </div>
                @else
                    <div class="overflow-x-auto border-b border-t border-slate-200">
                        <table class="w-full text-left text-sm text-slate-600 border-collapse">
                            <thead
                                class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-4 w-48">WAKTU PELAKSANAAN</th>
                                    <th class="px-5 py-4 min-w-[200px]">NAMA MAHASISWA</th>
                                    <th class="px-5 py-4 w-40">LOKASI / RUANGAN</th>
                                    <th class="px-5 py-4 w-32 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($seminarTodos->take(5) as $seminar)
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-5 py-4 align-middle">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800 text-[13px]">
                                                    {{ \Carbon\Carbon::parse($seminar->date)->translatedFormat('l, d M Y') }}
                                                </span>
                                                <span
                                                    class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full w-fit mt-1 border border-emerald-100">
                                                    {{ $seminar->waktu_mulai ?? '-' }} WIB
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 align-middle">
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-slate-800 text-[13px]">{{ $seminar->mahasiswa }}</span>
                                                <span
                                                    class="text-[11px] text-slate-500 font-medium tracking-wide">{{ $seminar->nim ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 align-middle">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-[12px] font-semibold text-slate-700 truncate max-w-[150px]"
                                                    title="{{ $seminar->ruangan ?? '-' }}">
                                                    {{ $seminar->ruangan ?? '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 align-middle text-center">
                                            @php
                                                // If event is today or past, encourage grading
                                                $isPassed = \Carbon\Carbon::parse($seminar->date)->isPast() || \Carbon\Carbon::parse($seminar->date)->isToday();
                                            @endphp
                                            <a href="{{ $seminar->url }}"
                                                class="inline-flex items-center justify-center px-4 py-1.5 border border-slate-200 text-xs font-bold rounded shadow-sm transition-all focus:ring-2 focus:ring-slate-200 {{ $isPassed ? 'bg-indigo-600 text-white hover:bg-indigo-700 hover:border-indigo-700 border-indigo-600' : 'bg-white text-slate-600 hover:bg-slate-50 hover:text-indigo-600 hover:border-slate-300' }}">
                                                {{ $isPassed ? 'Input Nilai' : 'Lihat Laporan' }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($seminarTodos->count() > 5)
                            <div class="p-3 border-t border-slate-200 bg-slate-50 text-center">
                                <a href="{{ route('eoffice.kp.dosen.bimbingan.index') }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center justify-center gap-1">
                                    👉 Lihat Semua Jadwal ({{ $seminarTodos->count() }} Seminar)
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-eoffice::layouts.dosen>