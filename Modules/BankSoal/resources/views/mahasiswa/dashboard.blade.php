<x-banksoal::layouts.mahasiswa>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
            Dashboard Mahasiswa
        </h1>
        <p class="text-sm text-slate-500 mt-2 font-medium">Ujian Komprehensif S1 Teknik Komputer</p>
    </div>

    <!-- Welcome Hero Banner -->
    <div class="bg-primary rounded-[20px] p-8 mb-8 relative overflow-hidden flex justify-between items-center shadow-lg shadow-primary/20">
        <!-- Abstract shape decoration -->
        <div class="absolute right-0 top-0 w-64 h-full pointer-events-none opacity-50">
            <svg class="absolute right-[-20%] md:right-0 top-0 h-full text-white transform translate-x-1/3" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor">
                <polygon points="50,0 100,0 100,100 0,100" />
            </svg>
        </div>
        
        <div class="relative z-10 text-primary-foreground w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-2 tracking-tight">Selamat Datang, {{ auth()->user()->name ?? 'Mahasiswa' }}!</h2>
                    <p class="text-primary-foreground/80 text-[13px] font-medium tracking-wide">
                        NIM: {{ optional(auth()->user()->student)->student_number ?? auth()->user()->external_id ?? '-' }} &bull; Program Studi S1 Teknik Komputer
                    </p>
                </div>
                <div>
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-white/10 text-white border border-white/20 backdrop-blur-sm tracking-wide shadow-sm">
                        Semester Ganjil 2025/2026
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Left: Major Application Status / Info Card -->
        <div class="col-span-1 xl:col-span-2 flex flex-col">
            @if($pendaftar && $pendaftar->status_pendaftaran === 'approved')
                
                <!-- STATE: APPROVED & READY FOR EXAM GATE -->
                <x-ui.card class="flex-1 flex flex-col relative overflow-hidden ring-1 ring-success/20 shadow-md">
                    <div class="absolute top-0 left-0 w-full h-1 bg-success"></div>
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <x-ui.badge variant="success" class="tracking-widest rounded-full uppercase">PENDAFTARAN DISETUJUI</x-ui.badge>
                            <span class="text-sm font-medium text-slate-500">&bull; Sesi Ujian Aktif</span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-900 tracking-tight mb-3">Gerbang Ujian Komprehensif</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-8 max-w-xl">
                            Untuk mengerjakan soal Ujian Komprehensif, silakan minta <strong class="text-slate-800">Token Akses (PIN 6 Digit)</strong> kepada pengawas.
                        </p>

                        <!-- Token Entry Form -->
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                            <form action="#" method="POST" class="flex flex-col sm:flex-row items-end gap-4">
                                @csrf
                                <div class="w-full sm:w-2/3 space-y-2">
                                    <x-ui.label for="token">Masukkan Token / PIN Sesi</x-ui.label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                        </div>
                                        <x-ui.input type="text" id="token" name="token" required class="pl-11 text-lg tracking-widest font-mono uppercase bg-white h-12" placeholder="X X X X X X" maxlength="6" />
                                    </div>
                                    <p class="text-[12px] text-slate-500 mt-1">Pastikan Anda sudah berada di PC/Ruangan Ujian yang ditentukan.</p>
                                </div>
                                <div class="w-full sm:w-1/3">
                                    <x-ui.button type="submit" size="lg" class="w-full h-12 gap-2 shadow-sm font-bold">
                                        Masuk Ujian
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </x-ui.card>

            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'pending')
                 <!-- STATE: PENDING -->
                <x-ui.card class="flex-1 flex flex-col relative overflow-hidden bg-warning/5 border-warning/20">
                    <div class="p-8 pb-10 flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-warning shadow-sm mb-6 mt-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 tracking-tight mb-3">Sedang Diverifikasi</h3>
                        <p class="text-slate-600 leading-relaxed max-w-lg mb-8 text-sm">
                            Pendaftaran Anda ke {{ $activePeriode->nama_periode ?? 'Ujian' }} telah berhasil dikirim dan saat ini sedang menunggu validasi dari staf akademik. Silakan periksa halaman ini secara berkala.
                        </p>
                        <x-ui.button variant="secondary" size="lg" onclick="window.location.reload()" class="font-bold gap-2 bg-white hover:bg-slate-50 text-slate-700">
                            Refresh Halaman
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </x-ui.button>
                    </div>
                </x-ui.card>

            @elseif($activePeriode)
                <!-- STATE: REGISTRATION OPEN -->
                <x-ui.card class="flex-1 flex flex-col relative overflow-hidden border-primary/20">
                    <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
                    <div class="p-8 flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <x-ui.badge variant="info" class="tracking-widest uppercase rounded-full">TERBUKA</x-ui.badge>
                        </div>

                        <h3 class="text-2xl font-bold text-slate-900 tracking-tight mb-4">Informasi Pendaftaran: {{ $activePeriode->nama_periode }}</h3>
                        
                        <p class="text-sm text-slate-600 leading-relaxed mb-8 max-w-xl">
                            Pendaftaran untuk periode ujian {{ $activePeriode->nama_periode }} telah resmi dibuka. Pastikan Anda melengkapi berkas dan persyaratan yang diperlukan.
                        </p>

                        <div class="mt-auto bg-slate-50 border border-slate-100 rounded-2xl p-6 transition-colors hover:bg-slate-50/80">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                                <div class="flex items-center gap-4 w-full">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center border border-slate-200 shadow-sm text-primary shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mb-1">Batas Akhir</p>
                                        <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}, 23:59 WIB</p>
                                    </div>
                                </div>
                                <div class="w-full sm:w-auto shrink-0">
                                    <x-ui.button as="a" href="{{ route('komprehensif.mahasiswa.pendaftaran') }}" size="lg" class="w-full font-bold gap-2">
                                        Daftar Sekarang
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @else
                <!-- STATE: CLOSED -->
                <x-ui.card class="flex-1 flex flex-col relative overflow-hidden bg-slate-50 opacity-90">
                    <div class="p-8 flex flex-col items-start h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <x-ui.badge variant="gray" class="tracking-widest uppercase rounded-full">DITUTUP</x-ui.badge>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight mb-4">Pendaftaran Belum Dibuka</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-8 max-w-xl">
                            Saat ini pendaftaran ujian komprehensif belum dibuka atau masa pendaftarannya sudah melewati batas waktu. Silakan pantau informasi kegiatan akademik secara berkala.
                        </p>
                        <div class="mt-auto">
                            <x-ui.button variant="outline" onclick="window.location.reload()" class="font-bold gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Refresh Halaman
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>

        <!-- Right: Status Tracker Card -->
        <div class="col-span-1 flex flex-col">
            <x-ui.card class="flex-1 flex flex-col relative overflow-hidden">
                <div class="p-8 flex flex-col items-center text-center h-full">
                    <div class="w-full border-b border-slate-100 pb-4 mb-8">
                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest text-left">Pelacak Status</h4>
                    </div>
                    
                    @php
                        $iconClass = "text-slate-400 bg-slate-50";
                        $statusText = "Belum Terdaftar";
                        $statusDesc = "Silakan lakukan pengajuan pendaftaran terlebih dahulu.";
                        $indicatorColor = "bg-slate-300";
                        
                        if ($pendaftar) {
                            switch($pendaftar->status_pendaftaran) {
                                case 'pending':
                                    $iconClass = "text-warning bg-warning/10";
                                    $statusText = "Sedang Ditinjau";
                                    $statusDesc = "Berkas pendaftaran sedang diperiksa oleh BAAK.";
                                    $indicatorColor = "bg-warning";
                                    break;
                                case 'approved':
                                    $iconClass = "text-success bg-success/10 border border-success/20";
                                    $statusText = "Disetujui";
                                    $statusDesc = "Silakan bersiap di tempat ujian dan minta token.";
                                    $indicatorColor = "bg-success shadow-[0_0_8px_rgba(52,216,137,0.8)]";
                                    break;
                                case 'rejected':
                                    $iconClass = "text-destructive bg-destructive/10";
                                    $statusText = "Ditolak";
                                    $statusDesc = "Pengajuan dikembalikan. Hubungi TU untuk revisi.";
                                    $indicatorColor = "bg-destructive";
                                    break;
                            }
                        }
                    @endphp

                    <div class="w-20 h-20 {{ $iconClass }} rounded-full flex items-center justify-center mb-6 shadow-sm border border-slate-50">
                        @if($pendaftar && $pendaftar->status_pendaftaran === 'approved')
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($pendaftar && $pendaftar->status_pendaftaran === 'rejected')
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        @endif
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight mb-3">
                        {{ $statusText }}
                    </h3>
                    <p class="text-[13px] text-slate-500 max-w-[200px] leading-relaxed mb-auto">
                        {{ $statusDesc }}
                    </p>

                    <div class="mt-10 w-full flex justify-between items-center px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Aktivitas</span>
                        <div class="flex justify-center items-center gap-2">
                             <div class="w-2.5 h-2.5 rounded-full {{ $indicatorColor }} {{ $pendaftar && $pendaftar->status_pendaftaran === 'approved' ? 'animate-pulse' : '' }}"></div>
                             <span class="text-[11px] font-bold text-slate-700 uppercase tracking-widest">
                                 {{ $pendaftar ? 'Terdaftar' : 'Belum Ada' }}
                             </span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>
