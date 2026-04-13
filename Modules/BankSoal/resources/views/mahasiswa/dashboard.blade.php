<x-banksoal::layouts.mahasiswa>
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Mahasiswa</h1>
        </div>
    </div>

    <!-- Welcome Hero Banner -->
    <div class="bg-blue-600 rounded-2xl p-8 mb-6 relative overflow-hidden flex justify-between items-center shadow-sm">
        <!-- Abstract shape decoration -->
        <div class="absolute right-0 top-0 w-64 h-full pointer-events-none">
            <svg class="absolute right-0 top-0 h-full text-blue-500/30 transform translate-x-1/3" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor">
                <polygon points="50,0 100,0 100,100 0,100" />
            </svg>
        </div>
        
        <div class="relative z-10 text-white w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Mahasiswa' }}!</h2>
                    <p class="text-blue-100 text-[13px] font-medium tracking-wide">
                        NIM: {{ optional(auth()->user()->student)->student_number ?? auth()->user()->external_id ?? '-' }} &bull; Program Studi S1 Teknik Komputer
                    </p>
                </div>
                <div class="mt-5 sm:mt-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-white/10 text-white border border-white/20 backdrop-blur-sm shadow-sm tracking-wide">
                        Semester Ganjil 2025/2026
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Major Application Status / Info Card -->
        <div class="col-span-1 lg:col-span-2 flex flex-col">
            @if($activePeriode)
                <!-- Registration Open State -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex-1 flex flex-col items-start relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold bg-blue-600 text-white tracking-widest uppercase">
                            TERBUKA
                        </span>
                    </div>

                    <h3 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight mb-4">Informasi Pendaftaran: {{ $activePeriode->nama_periode }}</h3>
                    
                    <p class="text-[13.5px] text-slate-600 leading-relaxed mb-8 max-w-xl">
                        Pendaftaran untuk periode ujian {{ $activePeriode->nama_periode }} telah resmi dibuka. Pastikan Anda melengkapi berkas dan persyaratan seperti transkrip nilai, sertifikat TOEFL, dan draf tugas akhir sebelum batas waktu berakhir.
                    </p>

                    <div class="mt-auto w-full flex flex-col sm:flex-row items-center justify-between bg-slate-50 border border-slate-100 rounded-xl p-4 gap-4 transition-colors group-hover:bg-slate-50/80">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center border border-slate-200 shadow-sm text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4M12 15h2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-0.5">Batas Akhir</p>
                                <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}, 23:59 WIB</p>
                            </div>
                        </div>
                        <a href="{{ route('komprehensif.mahasiswa.pendaftaran') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white rounded-xl px-7 py-2.5 text-sm font-bold shadow hover:bg-blue-700 hover:shadow-md transition-all group">
                            Daftar Sekarang
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @else
                <!-- Registration Closed State -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex-1 flex flex-col items-start relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 tracking-widest uppercase">
                            DITUTUP
                        </span>
                    </div>

                    <h3 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight mb-4">Pendaftaran Ujian Komprehensif Ditutup</h3>
                    
                    <p class="text-[13.5px] text-slate-600 leading-relaxed mb-8 max-w-xl">
                        Saat ini pendaftaran ujian komprehensif belum dibuka atau masa pendaftarannya sudah melewati batas waktu. Silakan pantau informasi kegiatan akademik secara berkala atau hubungi pihak tata usaha jika ini adalah kekeliruan.
                    </p>

                    <div class="mt-auto pt-4">
                        <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-slate-700 border border-slate-300 rounded-xl px-6 py-2.5 text-sm font-bold shadow-sm hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Refresh Dashboard
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Status Tracker Card -->
        <div class="col-span-1 flex flex-col">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex-1 flex flex-col items-center justify-center text-center relative overflow-hidden">
                <div class="absolute top-6 left-6 w-full text-left">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status Pendaftaran</h4>
                </div>
                
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mb-5 mt-8 border border-slate-100 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                
                <h3 class="text-[15px] font-bold text-slate-800 mb-2">
                    @if($pendaftar)
                        @switch($pendaftar->status_pendaftaran)
                            @case('pending') Sedang Ditinjau @break
                            @case('approved') Disetujui @break
                            @case('rejected') Ditolak @break
                            @default Belum Terdaftar
                        @endswitch
                    @else
                        Belum Terdaftar
                    @endif
                </h3>
                <p class="text-[12.5px] text-slate-500 max-w-[200px] leading-relaxed mb-8">
                    @if($pendaftar)
                        Sedang dalam tahap verifikasi atau telah diproses oleh admin.
                    @else
                        Silakan lakukan pengajuan pendaftaran ujian terlebih dahulu.
                    @endif
                </p>

                <div class="mt-auto w-full flex justify-end">
                    <span class="text-[9px] font-bold {{ $pendaftar ? 'text-blue-400' : 'text-slate-300' }} tracking-widest flex items-center gap-1.5 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full {{ $pendaftar ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
                        Status: {{ $pendaftar ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>
