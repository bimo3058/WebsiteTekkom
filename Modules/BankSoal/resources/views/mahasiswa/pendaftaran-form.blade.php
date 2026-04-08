<x-banksoal::layouts.mahasiswa>
    <!-- Simple Header (Matching Wireframe) -->
    <div class="mb-4 flex items-center gap-3">
        <div class="w-6 h-6 rounded flex items-center justify-center text-[#0B66E4] border border-[#0B66E4]/20 bg-blue-50/50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h1 class="text-[22px] font-bold text-slate-900 tracking-tight">Pengajuan Pendaftaran Ujian</h1>
    </div>

    <!-- Divider Line -->
    <div class="h-px w-full bg-slate-200 mb-6"></div>

    <p class="text-[15px] font-bold text-slate-500 mb-8 tracking-wide">Pendaftaran Ujian Komprehensif S1 Teknik Komputer - {{ $activePeriode->nama_periode }}</p>

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
        
        <!-- Left Column: Information -->
        <div class="w-full lg:w-7/12 flex flex-col gap-6">
            
            <!-- Info Card -->
            <div class="bg-white rounded-[20px] shadow-sm border border-slate-100 overflow-hidden">
                <!-- Graphic top bar -->
                <div class="h-[180px] bg-[#E8F0FE] relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/20"></div>
                    <div class="relative z-10 w-24 h-24 text-[#0B66E4]/30">
                        <svg fill="none" class="w-full h-full" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path><circle cx="15" cy="15" r="4" stroke-width="1.5" fill="none"></circle></svg>
                    </div>
                </div>

                <div class="p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[11px] font-bold bg-[#E8F0FE] text-[#0B66E4] tracking-widest uppercase">
                            INFORMASI PENDAFTARAN
                        </span>
                        <span class="text-[13px] font-medium text-slate-500">&bull; {{ $activePeriode->nama_periode }}</span>
                    </div>

                    <h3 class="text-[18px] font-bold text-slate-900 mb-4 tracking-tight">Ketentuan & Jadwal Ujian</h3>
                    <p class="text-[14.5px] text-slate-600 leading-relaxed mb-8">
                        Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah siap mengikuti Sidang Tugas Akhir pada bulan Desember 2025.
                    </p>

                    <!-- Info list boxes -->
                    <div class="space-y-4">
                        <!-- Jadwal -->
                        <div class="flex items-start gap-4 p-5 rounded-2xl bg-[#F8FAFC]">
                            <div class="flex-shrink-0 text-[#0B66E4] mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-slate-900 mb-1">Jadwal Pelaksanaan</p>
                                <p class="text-[14px] text-slate-600">
                                    {{ $activePeriode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('l, d F Y') : '-' }} s.d.<br>
                                    {{ $activePeriode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('l, d F Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div class="flex items-start gap-4 p-5 rounded-2xl bg-[#F8FAFC]">
                            <div class="flex-shrink-0 text-[#0B66E4] mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-slate-900 mb-1">Lokasi</p>
                                <p class="text-[14px] text-slate-600">Lab. Jaringan Komputer, Lt. 3</p>
                            </div>
                        </div>

                        <!-- Durasi -->
                        <div class="flex items-start gap-4 p-5 rounded-2xl bg-[#F8FAFC]">
                            <div class="flex-shrink-0 text-[#0B66E4] mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-slate-900 mb-1">Durasi Ujian</p>
                                <p class="text-[14px] text-slate-600">100 Menit</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Warning instead of Sesi Ujian -->
            <div class="bg-amber-50 rounded-[20px] shadow-sm border border-amber-100 p-6 flex items-start gap-4 mt-6">
                <div class="text-amber-500 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <p class="text-[13.5px] font-bold text-amber-700 mb-1.5">Catatan Penting:</p>
                    <p class="text-[13.5px] text-amber-600 leading-relaxed">Form pendaftaran akan otomatis ditutup melewati tanggal <strong class="font-extrabold">{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('l, d F Y') }} pukul 23:59 WIB</strong>.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Registration Form -->
        <div class="w-full lg:w-5/12">
            <div class="bg-white rounded-[20px] shadow-[0_2px_10px_-4px_rgba(0,0,0,0.1)] border border-slate-100 p-8 lg:sticky lg:top-8">
                <h2 class="text-[20px] font-bold text-slate-900 mb-2 tracking-tight">Form Pendaftaran</h2>
                <p class="text-[13px] text-slate-500 mb-8">Lengkapi data diri dengan benar untuk mengikuti ujian.</p>

                <form action="{{ route('komprehensif.mahasiswa.pendaftaran.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- NIM -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" name="nim" required value="{{ old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id) }}" class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] text-slate-800 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all placeholder-slate-400" placeholder="Contoh: 210101xxx">
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" required value="{{ old('nama', auth()->user()->name) }}" class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] text-slate-800 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all placeholder-slate-400" placeholder="Masukkan nama sesuai KTM">
                    </div>

                    <!-- Semester Aktif -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Semester Aktif</label>
                        <input type="number" name="semester" class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] text-slate-800 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all placeholder-slate-400" placeholder="Contoh: 7">
                        <p class="text-[11px] text-slate-500 mt-2 font-medium">Catatan: minimal semester 7</p>
                    </div>

                    <!-- Target Wisuda -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Target Wisuda (Sidang Skripsi) <span class="text-red-500">*</span></label>
                        <input type="text" name="target_wisuda" required class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] text-slate-800 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all placeholder-slate-400" placeholder="Contoh: Periode 183 (Apr-Jun '26)">
                    </div>

                    <!-- Dosbing 1 -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Dosen Pembimbing 1</label>
                        <div class="relative">
                            <select name="dosen_pembimbing_1" class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] font-medium text-slate-700 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih Dosen Pembimbing 1</option>
                                <option value="1">Prof. Yos Johan Utama</option>
                                <option value="2">Dr. Iwan Setiawan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Dosbing 2 -->
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Dosen Pembimbing 2</label>
                        <div class="relative">
                            <select name="dosen_pembimbing_2" class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-[14px] font-medium text-slate-700 focus:border-[#0B66E4] focus:ring-1 focus:ring-[#0B66E4] outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih Dosen Pembimbing 2</option>
                                <option value="3">Dr. Adian Fatchur Rochim</option>
                                <option value="4">Ir. Kurniawan Teguh Martono</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-[#0B66E4] hover:bg-blue-700 text-white rounded-lg px-4 py-3 text-[14px] font-bold shadow-sm transition-all group">
                            <svg class="w-[18px] h-[18px] transition-transform group-hover:translate-x-1 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Submit Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>
