<x-banksoal::layouts.mahasiswa>
    <!-- Simple Header (Matching Wireframe) -->
    <div class="mb-10 px-2 lg:px-4">
        <h1 class="text-3xl font-bold text-grey-900 tracking-tight">Pengajuan Pendaftaran</h1>
        <p class="text-base text-grey-600 mt-2 font-medium">Kelola pendaftaran ujian komprehensif Anda</p>
    </div>

    @if($activePeriode)
        
        <!-- Status Banner Logic -->
        @if(isset($pendaftar) && $pendaftar->status_pendaftaran === 'pending')
            <div class="mx-2 lg:mx-4 mb-6 bg-[#EFF4FE] border border-[#D0E1FE] rounded-xl flex items-center justify-between p-5 border-l-4 border-l-[#0B66E4] shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#D0E1FE] text-[#0B66E4] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-[15px] font-bold text-[#0B1E3F] mb-0.5">Status: Dalam Pengajuan</h4>
                        <p class="text-[13px] text-slate-600">Pendaftaran Anda sedang menunggu verifikasi oleh admin program studi.</p>
                    </div>
                </div>
                <div class="px-4 py-1.5 rounded-full bg-[#D0E1FE]/60 text-[#0B66E4] text-[12px] font-bold shrink-0">
                    Menunggu Verifikasi
                </div>
            </div>
        @elseif(isset($pendaftar) && $pendaftar->status_pendaftaran === 'rejected')
            <div class="mx-2 lg:mx-4 mb-6 bg-[#FEF2F2] border border-[#FEE2E2] rounded-xl flex items-center justify-between p-5 border-l-4 border-l-[#DC2626] shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#FEE2E2] text-[#DC2626] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-[15px] font-bold text-[#991B1B] mb-0.5">Status: Keputusan Akhir</h4>
                        <p class="text-[13px] text-[#DC2626]">Mohon maaf, pengajuan Anda tidak dapat kami proses</p>
                    </div>
                </div>
                <div class="px-4 py-1.5 rounded-full bg-[#FEE2E2]/80 text-[#DC2626] text-[12px] font-bold flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    DITOLAK
                </div>
            </div>
        @elseif(isset($pendaftar) && $pendaftar->status_pendaftaran === 'approved')
            <div class="mx-2 lg:mx-4 mb-6 bg-[#DCFCE7] border border-[#BBF7D0] rounded-xl flex items-center justify-between p-5 border-l-4 border-l-[#16A34A] shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#BBF7D0]/60 text-[#16A34A] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-[15px] font-bold text-[#16A34A] mb-0.5">Status: Keputusan Akhir</h4>
                        <p class="text-[13px] text-[#15803D]">Pendaftaran Anda telah diverifikasi dan disetujui oleh admin program studi.</p>
                    </div>
                </div>
                <div class="px-4 py-1.5 rounded-full bg-[#16A34A]/20 text-[#16A34A] text-[12px] font-bold flex items-center gap-1.5 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    DISETUJUI
                </div>
            </div>
        @endif

        <!-- Registration Card -->
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden flex flex-col mx-2 lg:mx-auto lg:max-w-5xl">
            
            <!-- Banner Image (Natural Scaling, uncrippled) -->
            <div class="w-full bg-slate-50 flex justify-center border-b border-slate-100">
                <img src="{{ asset('images/banner_pendaftaran.png') }}" class="w-full h-auto max-h-[220px] object-contain sm:object-cover" alt="Banner Pendaftaran">
            </div>

            <!-- Content Section -->
            <div class="w-full p-6 md:p-10 flex flex-col justify-center bg-white">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full text-[12px] font-bold @if(isset($pendaftar) && $pendaftar->status_pendaftaran === 'rejected') bg-red-50 text-red-600 @else bg-[#E6F4EA] text-[#137333] @endif tracking-wide mb-6 w-max">
                    @if(isset($pendaftar) && $pendaftar->status_pendaftaran === 'rejected') Pendaftaran Ditolak @else Pendaftaran Dibuka @endif
                </div>
                
                <h2 class="text-[20px] md:text-[22px] font-extrabold text-slate-900 leading-snug mb-4 tracking-tight">
                    Pendaftaran Ujian Komprehensif S1 Teknik Komputer - {{ $activePeriode->nama_periode }}
                </h2>
                
                <div class="text-[14px] text-slate-600 leading-relaxed mb-8 max-w-4xl space-y-4">
                    <p>
                        Formulir ini digunakan untuk pendaftaran Ujian Komprehensif Program Studi S1 Teknik Komputer bulan {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('F Y') }}. Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah siap mengikuti Sidang Tugas Akhir.
                    </p>
                    <p>
                        Dengan mengisi formulir ini, Anda menyatakan bersedia mematuhi seluruh aturan ujian yang berlaku.<br>
                        Form akan ditutup pada hari <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('l, d F Y') }}</strong> pukul 23.59 WIB.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 sm:gap-12 mb-8">
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 flex items-center justify-center flex-shrink-0 text-[#0B66E4]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[12px] text-slate-500 font-medium mb-0.5">Periode Pendaftaran</p>
                            <p class="text-[13px] font-bold text-slate-800">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->format('d M y') }} - {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->format('d M y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-full border-t border-dashed border-slate-300 mb-6"></div>
                
                <div class="flex justify-end mt-auto">
                    @if(isset($pendaftar))
                        <!-- Already registered / disabled button -->
                        <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#E2E8F0] text-slate-500 rounded-xl px-8 py-3 text-[14px] font-bold shadow-sm cursor-not-allowed border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Anda Sudah Terdaftar
                        </div>
                    @else
                        <!-- Active register button -->
                        <a href="{{ route('komprehensif.mahasiswa.pendaftaran.form') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#0B66E4] hover:bg-blue-700 text-white rounded-xl px-8 py-3 text-[14px] font-bold shadow-sm hover:shadow-md transition-all group">
                            Daftar Sekarang
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- No Active Registration State (Matching Wireframe exactly) -->
        <div class="flex flex-col items-center justify-center text-center min-h-[65vh] w-full relative">
            <div class="relative z-10 flex flex-col items-center w-full max-w-2xl px-4">
                
                <!-- Graphic Container -->
                <div class="relative w-56 h-56 flex items-center justify-center mb-6">
                    <!-- Soft blue shadow/blur behind -->
                    <div class="absolute inset-0 bg-[#E8F0FE] rounded-full blur-[40px] opacity-100 transform -translate-y-2 scale-110"></div>
                    <div class="absolute inset-0 bg-[#E8F0FE]/90 rounded-full scale-100"></div>
                    
                    <!-- White rounded-rectangle Icon Box -->
                    <div class="relative z-10 w-[110px] h-[110px] bg-white rounded-3xl shadow-sm flex items-center justify-center border border-white">
                        <div class="relative">
                            <!-- Calendar Icon -->
                            <svg class="w-14 h-14 text-[#0B66E4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <!-- Clock Icon overlay -->
                            <div class="absolute -bottom-1 -right-1 w-[26px] h-[26px] bg-[#0B66E4] rounded-full flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3"></path>
                                    <circle cx="12" cy="12" r="9" stroke-width="2.5"></circle>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-[26px] md:text-[28px] font-extrabold text-slate-900 tracking-tight mb-4">Periode Ujian belum dijadwalkan</h2>
                <p class="text-slate-600 max-w-[480px] mx-auto leading-relaxed mb-10 text-[14.5px]">
                    Saat ini tidak ada periode pendaftaran ujian yang aktif. Silakan periksa kembali nanti atau hubungi bagian administrasi akademik untuk informasi jadwal semester ini.
                </p>

                <a href="{{ route('komprehensif.mahasiswa.dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-[#0B66E4] hover:bg-blue-700 text-white rounded-xl px-7 py-3 text-[14.5px] font-bold shadow-sm transition-all group">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    @endif
</x-banksoal::layouts.mahasiswa>
