<x-banksoal::layouts.mahasiswa>
    <!-- Page Header -->
    <div class="mb-12 border-b border-slate-200 pb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <p class="text-[11px] font-bold tracking-widest text-slate-500 uppercase mb-3">Portal Akademik Mahasiswa</p>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Ujian Komprehensif
                </h1>
            </div>
            <div class="text-left md:text-right">
                <p class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ auth()->user()->name ?? 'Mahasiswa' }}</p>
                <p class="text-slate-500 text-sm mt-1 font-mono">NIM: {{ optional(auth()->user()->student)->student_number ?? auth()->user()->external_id ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="flex flex-col gap-8 max-w-4xl">
        
        <!-- Major Application Status / Info Card -->
        <div class="w-full flex flex-col">
            @if($pendaftar && $pendaftar->status_pendaftaran === 'approved')
                
                <!-- STATE: APPROVED & READY FOR EXAM GATE -->
                <div class="flex flex-col border border-slate-300 bg-white">
                    <div class="p-8 sm:p-10">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-[11px] font-bold tracking-widest uppercase border border-green-200">Disetujui</span>
                            @if($pendaftar->jadwal)
                                <span class="text-[12px] font-bold text-slate-500 uppercase tracking-widest">&bull; Sesi Dialokasikan</span>
                            @else
                                <span class="text-[12px] font-bold text-slate-500 uppercase tracking-widest">&bull; Menunggu Jadwal</span>
                            @endif
                        </div>
                        
                        @if($pendaftar->jadwal)
                        <!-- INFO SESI -->
                        <div class="mb-10 p-6 bg-slate-50 border border-slate-200 border-l-4 border-l-slate-800">
                             <h4 class="text-lg font-extrabold text-slate-900 mb-2 uppercase tracking-wide">Jadwal: {{ $pendaftar->jadwal->nama_sesi }}</h4>
                             <p class="text-sm text-slate-600 font-mono">
                                 {{ $pendaftar->jadwal->tanggal_ujian ? \Carbon\Carbon::parse($pendaftar->jadwal->tanggal_ujian)->translatedFormat('l, d F Y') : 'TBA' }}
                                 | 
                                 {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_selesai)->format('H:i') }} WIB
                             </p>
                        </div>
                        @else
                        <!-- MENUNGGU SESI -->
                        <div class="mb-10 p-6 bg-slate-50 border border-slate-200 border-l-4 border-l-amber-500">
                             <h4 class="text-lg font-extrabold text-slate-900 mb-2 uppercase tracking-wide">Menunggu Penjadwalan</h4>
                             <p class="text-sm text-slate-600 leading-relaxed">Pendaftaran Anda telah divalidasi. Silakan tunggu hingga sesi ujian dan ruangan Anda dialokasikan di dalam sistem.</p>
                        </div>
                        @endif

                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4 {{ $pendaftar->jadwal ? '' : 'opacity-50' }}">Gerbang CBT</h3>
                        <p class="text-base text-slate-600 leading-relaxed mb-8 max-w-xl {{ $pendaftar->jadwal ? '' : 'opacity-50' }}">
                            Masukkan <strong>Token Akses (6 Digit)</strong> yang diberikan oleh pengawas ujian untuk memulai Test Engine.
                        </p>

                        <!-- Token Entry Form -->
                        <div class="border-t border-slate-200 pt-8 {{ $pendaftar->jadwal ? '' : 'opacity-50 pointer-events-none' }}">
                            <form action="{{ route('komprehensif.mahasiswa.engine.validate') }}" method="POST" class="flex flex-col sm:flex-row items-end gap-4">
                                @csrf
                                <div class="w-full sm:w-2/3 space-y-3">
                                    <label for="token" class="block text-[12px] font-bold text-slate-900 uppercase tracking-widest">Token Sesi</label>
                                    <input type="text" id="token" name="token" required class="w-full px-5 py-4 text-2xl tracking-[0.5em] font-mono font-bold text-slate-900 bg-white border-2 border-slate-300 focus:border-slate-900 focus:ring-0 outline-none transition-colors uppercase placeholder:text-slate-300" placeholder="XXXXXX" maxlength="6" {{ is_null($pendaftar->jadwal) ? 'disabled' : '' }} />
                                </div>
                                <div class="w-full sm:w-1/3">
                                    <button type="submit" class="w-full py-4 px-6 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm tracking-widest uppercase transition-colors {{ is_null($pendaftar->jadwal) ? 'cursor-not-allowed bg-slate-300' : '' }}" {{ is_null($pendaftar->jadwal) ? 'disabled' : '' }}>
                                        Mulai Ujian &rarr;
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'pending')
                 <!-- STATE: PENDING -->
                <div class="flex flex-col border border-slate-300 bg-white">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-200 mb-6">Verifikasi</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Diproses</h3>
                        <p class="text-slate-600 leading-relaxed max-w-xl mb-10 text-base">
                            Berkas pengajuan pendaftaran Anda untuk <strong>{{ $activePeriode->nama_periode ?? 'Ujian' }}</strong> telah diterima sistem dan sedang dalam tahap verifikasi staf akademik BAAK.
                        </p>
                        <button type="button" onclick="window.location.reload()" class="py-3 px-6 bg-white border-2 border-slate-900 text-slate-900 font-bold text-sm tracking-widest uppercase hover:bg-slate-900 hover:text-white transition-colors">
                            Muat Ulang Halaman
                        </button>
                    </div>
                </div>
            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'rejected')
                 <!-- STATE: REJECTED -->
                <div class="flex flex-col border border-red-300 bg-red-50">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200 mb-6">Ditolak</span>
                        <h3 class="text-3xl font-extrabold text-red-900 tracking-tight mb-4">Pendaftaran Ditolak</h3>
                        <p class="text-red-700 leading-relaxed max-w-xl mb-10 text-base">
                            Mohon maaf, pengajuan pendaftaran Anda untuk <strong>{{ $activePeriode->nama_periode ?? 'Ujian' }}</strong> ditolak.
                        </p>
                       
                    </div>
                </div>

            @elseif($activePeriode)
                @if($activePeriode->pendaftaran_ditutup_paksa)
                    <!-- STATE: CLOSED BY ADMIN (EMERGENCY) -->
                    <div class="flex flex-col border border-amber-300 bg-amber-50">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-300 mb-6">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                Ditutup Admin
                            </span>
                            <h3 class="text-3xl font-extrabold text-amber-900 tracking-tight mb-4">Pendaftaran Ditutup</h3>
                            <p class="text-base text-amber-800 leading-relaxed mb-10 max-w-xl">
                                Pendaftaran untuk <strong>{{ $activePeriode->nama_periode }}</strong> telah ditutup lebih awal oleh staf akademik. Silakan hubungi BAAK untuk informasi lebih lanjut.
                            </p>
                        </div>
                    </div>
                @elseif(now()->lt(\Carbon\Carbon::parse($activePeriode->tanggal_mulai)->startOfDay()))
                    <!-- STATE: NOT YET OPEN -->
                    <div class="flex flex-col border border-slate-300 bg-white">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-600 text-[11px] font-bold tracking-widest uppercase border border-slate-200 mb-6">Belum Dibuka</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Menunggu Jadwal Buka</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Registrasi ujian komprehensif <strong>{{ $activePeriode->nama_periode }}</strong> baru dapat diakses mulai tanggal <strong class="text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d F Y') }}</strong>.
                            </p>
                            <button type="button" onclick="window.location.reload()" class="py-3 px-6 bg-white border-2 border-slate-900 text-slate-900 font-bold text-sm tracking-widest uppercase hover:bg-slate-900 hover:text-white transition-colors">
                                Cek Status Terbaru
                            </button>
                        </div>
                    </div>
                @elseif(now()->gt(\Carbon\Carbon::parse($activePeriode->tanggal_selesai)->endOfDay()))
                    <!-- STATE: CLOSED BUT ACTIVE -->
                    <div class="flex flex-col border border-slate-300 bg-white">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200 mb-6">Ditutup</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Ditutup</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Tenggat waktu registrasi untuk <strong>{{ $activePeriode->nama_periode }}</strong> telah berakhir pada <strong class="text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}</strong>.
                            </p>
                            <button type="button" onclick="window.location.reload()" class="py-3 px-6 bg-white border border-slate-300 text-slate-600 font-bold text-sm tracking-widest uppercase hover:bg-slate-100 transition-colors">
                                Muat Ulang Halaman
                            </button>
                        </div>
                    </div>
                @else
                    <!-- STATE: REGISTRATION OPEN -->
                    @if(!$isEligible)
                        <!-- STATE: NOT ELIGIBLE -->
                        <div class="flex flex-col border border-slate-300 bg-white">
                            <div class="p-8 sm:p-10 flex flex-col h-full">
                                <span class="inline-flex items-center self-start px-3 py-1 bg-slate-900 text-white text-[11px] font-bold tracking-widest uppercase mb-6">Terkunci</span>
                                
                                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Belum Memenuhi Syarat</h3>
                                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                    Periode <strong>{{ $activePeriode->nama_periode }}</strong> sedang berlangsung. Namun, ujian ini mewajibkan minimum <strong>Semester 7</strong>. Anda saat ini tercatat di <strong>Semester {{ $semester }}</strong>.
                                </p>
                                
                                <div class="mt-auto">
                                    <button disabled class="py-3 px-6 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-sm tracking-widest uppercase cursor-not-allowed">
                                        Pendaftaran Terkunci
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- STATE: ELIGIBLE & REGISTRATION OPEN -->
                        <div class="flex flex-col border-2 border-slate-900 bg-white">
                            <div class="p-8 sm:p-10 flex flex-col h-full">
                                <div class="flex items-center gap-4 mb-6">
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-900 text-white text-[11px] font-bold tracking-widest uppercase">Terbuka</span>
                                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-widest">S1 Teknik Komputer</span>
                                </div>

                                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4 leading-tight">{{ $activePeriode->nama_periode }}</h3>
                                
                                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                    Formulir ini digunakan untuk pendaftaran Ujian Komprehensif Program Studi S1 Teknik Komputer bulan <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('F Y') }}</strong>. Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah siap mengikuti Sidang Tugas Akhir.<br>
                                    Dengan mengisi formulir ini, Anda menyatakan bersedia mematuhi seluruh aturan ujian yang berlaku.
                                </p>

                                <div class="mt-auto flex flex-col sm:flex-row sm:items-center justify-between border-t border-slate-200 pt-6 gap-6">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Batas Akhir Registrasi</p>
                                        <p class="text-base font-mono font-bold text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}, 23:59</p>
                                    </div>
                                    <a href="{{ route('komprehensif.mahasiswa.pendaftaran.form') }}" class="inline-block py-4 px-8 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm tracking-widest uppercase text-center transition-colors">
                                        Ajukan Form &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <!-- STATE: NO ACTIVE PERIOD -->
                <div class="flex flex-col border border-slate-300 bg-slate-50">
                    <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                        <span class="inline-flex items-center px-3 py-1 bg-slate-200 text-slate-600 text-[11px] font-bold tracking-widest uppercase mb-6">Ditutup</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Tidak Ada Jadwal</h3>
                        <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                            Belum ada jadwal pendaftaran yang dirilis oleh pihak BAAK.
                        </p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>
