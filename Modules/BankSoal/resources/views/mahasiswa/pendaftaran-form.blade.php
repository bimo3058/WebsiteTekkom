<x-banksoal::layouts.mahasiswa>
    <!-- We break the standard container bounds by using raw expansive space -->
    <div class="relative py-8 lg:py-16">
        
        <!-- Background subtle accent outside normal flow -->
        <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/4 w-[800px] h-[800px] bg-primary/5 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row gap-12 xl:gap-24 items-start relative z-10 w-full max-w-[1400px] mx-auto">
            
            <!-- LEFT COLUMN: Canvas-Integrated Information (No Cards) -->
            <div class="w-full lg:w-1/2 flex flex-col pt-4">
                
                <div class="inline-flex items-center gap-2 text-primary font-semibold tracking-widest text-xs uppercase mb-8">
                    <span class="w-8 h-px bg-primary"></span>
                    <span>Periode {{ $activePeriode->nama_periode }}</span>
                </div>

                <h1 class="text-4xl lg:text-6xl font-bold text-slate-900 tracking-tighter leading-[1.1] mb-8">
                    Pengajuan <br/>
                    <span class="text-slate-400">Pendaftaran Ujian.</span>
                </h1>

                <p class="text-lg lg:text-xl text-slate-600 leading-relaxed font-medium mb-12 max-w-lg">
                    Lengkapi persyaratan administratif untuk mengikuti sidang komprehensif S1 Teknik Komputer. Pastikan data yang Anda masukkan mutlak benar dan sesuai.
                </p>

                <!-- Typographic Info Grid (No borders/boxes) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-10 mt-4 max-w-xl">
                    
                    <div class="space-y-3">
                        <div class="text-primary flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <h4 class="font-bold text-slate-900 text-sm tracking-widest uppercase">Pelaksanaan Ujian</h4>
                        </div>
                        <p class="text-sm font-medium text-slate-600 leading-relaxed">
                            {{ $activePeriode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') : '-' }} — {{ $activePeriode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') : '-' }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-primary flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h4 class="font-bold text-slate-900 text-sm tracking-widest uppercase">Durasi</h4>
                        </div>
                        <p class="text-sm font-medium text-slate-600 leading-relaxed">
                            100 Menit
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-primary flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <h4 class="font-bold text-slate-900 text-sm tracking-widest uppercase">Ruangan</h4>
                        </div>
                        <p class="text-sm font-medium text-slate-600 leading-relaxed">
                            Lab. Jaringan Komputer
                        </p>
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: The Interactive Surface -->
            <div class="w-full lg:w-1/2">
                <!-- We still use a card for the form to clearly demarcate the interactive zone, but styled much more intentionally -->
                <div class="bg-white rounded-[32px] p-8 sm:p-12 shadow-[0_24px_64px_-12px_rgba(0,0,0,0.06)] border border-slate-100 lg:sticky lg:top-8">
                    
                    <div class="mb-10">
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pendaftar</h2>
                        <div class="w-12 h-1 bg-primary mt-4 rounded-full"></div>
                    </div>

                    <form action="{{ route('komprehensif.mahasiswa.pendaftaran.store') }}" method="POST" class="space-y-7 block">
                        @csrf
                        
                        <!-- Seksi 1: Data Identitas -->
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- NIM -->
                                <div class="space-y-2">
                                    <x-ui.label class="text-[15px] text-grey-700 font-semibold">NIM</x-ui.label>
                                    <x-ui.input type="text" name="nim" required readonly value="{{ old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id) }}" class="h-14 bg-grey-50/70 border-grey-100/50 text-grey-900 text-lg font-medium rounded-[16px] px-5 outline-none cursor-not-allowed placeholder:text-grey-400" placeholder="210101xxx" />
                                </div>

                                <!-- Semester Aktif -->
                                <div class="space-y-2">
                                    <x-ui.label required class="text-[15px] text-grey-700 font-semibold">Semester Aktif</x-ui.label>
                                    <x-ui.input type="number" name="semester" required :error="$errors->has('semester')" value="{{ old('semester') }}" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none placeholder:text-grey-400" placeholder="Misal: 7" />
                                    @error('semester')
                                        <p class="text-[13px] text-error-600 font-medium mt-1.5 flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] text-grey-700 font-semibold">Nama Lengkap</x-ui.label>
                                <x-ui.input type="text" name="nama" required readonly value="{{ old('nama', auth()->user()->name) }}" class="h-14 bg-grey-50/70 border-grey-100/50 text-grey-900 text-lg font-medium rounded-[16px] px-5 outline-none cursor-not-allowed placeholder:text-grey-400" placeholder="Masukkan nama lengkap" />
                            </div>
                        </div>

                        <!-- Seksi 2: Kontak & Akademik -->
                        <div class="space-y-5">
                            <!-- Kontak WA -->
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] text-grey-700 font-semibold">Kontak WA Aktif</x-ui.label>
                                <x-ui.input type="text" name="kontak_wa" required :error="$errors->has('kontak_wa')" value="{{ old('kontak_wa') }}" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none placeholder:text-grey-400" placeholder="Contoh: 0812345678" />
                                @error('kontak_wa')
                                    <p class="text-[13px] text-error-600 font-medium mt-1.5 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Wisuda -->
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] text-grey-700 font-semibold">Target Wisuda (Sidang Skripsi)</x-ui.label>
                                <x-ui.input type="text" name="target_wisuda" required :error="$errors->has('target_wisuda')" value="{{ old('target_wisuda') }}" class="h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-grey-900 text-lg font-medium rounded-[16px] px-5 transition-all outline-none placeholder:text-grey-400" placeholder="Misal: Periode 183 (Apr-Jun '26)" />
                                @error('target_wisuda')
                                    <p class="text-[13px] text-error-600 font-medium mt-1.5 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="h-px bg-grey-100/60 w-full my-3"></div>

                        <!-- Seksi 3: Dosen Pembimbing Grid -->
                        <div class="grid grid-cols-1 gap-5">
                            <!-- Dosbing 1 -->
                            <div class="space-y-2">
                                <x-ui.label required class="text-[15px] text-grey-700 font-semibold">Dosen Pembimbing 1</x-ui.label>
                                <div class="relative">
                                    <select name="dosen_pembimbing_1_id" required 
                                            onchange="this.classList.remove('text-grey-400'); this.classList.add('text-grey-900')"
                                            class="w-full h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-lg font-medium rounded-[16px] px-5 transition-all outline-none cursor-pointer {{ old('dosen_pembimbing_1_id') ? 'text-grey-900' : 'text-grey-400' }} @error('dosen_pembimbing_1_id') border-error-500 ring-error-500/20 @enderror">
                                        <option value="" disabled selected class="text-grey-400">Pilih Pembimbing</option>
                                        @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->id }}" class="text-grey-900" {{ old('dosen_pembimbing_1_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('dosen_pembimbing_1_id')
                                    <p class="text-[13px] text-error-600 font-medium mt-1.5 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dosbing 2 -->
                            <div class="space-y-2">
                                <x-ui.label class="text-[15px] text-grey-700 font-semibold">Dosen Pembimbing 2</x-ui.label>
                                <div class="relative">
                                    <select name="dosen_pembimbing_2_id" 
                                            onchange="this.classList.remove('text-grey-400'); this.classList.add('text-grey-900')"
                                            class="w-full h-14 bg-grey-25 border border-grey-100/80 hover:bg-white focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 text-lg font-medium rounded-[16px] px-5 transition-all outline-none cursor-pointer {{ old('dosen_pembimbing_2_id') ? 'text-grey-900' : 'text-grey-400' }} @error('dosen_pembimbing_2_id') border-error-500 ring-error-500/20 @enderror">
                                        <option value="" selected class="text-grey-400">Pilih Pembimbing</option>
                                         @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->id }}" class="text-grey-900" {{ old('dosen_pembimbing_2_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('dosen_pembimbing_2_id')
                                    <p class="text-[13px] text-error-600 font-medium mt-1.5 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-5">
                            <button type="submit" class="w-full h-14 inline-flex items-center justify-center gap-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-[16px] text-lg font-semibold shadow-[0_8px_20px_-4px_rgba(107,79,244,0.4)] hover:shadow-[0_12px_24px_-6px_rgba(107,79,244,0.5)] transform transition-all active:scale-[0.98] group">
                                <span>Kirim Pengajuan</span>
                                <svg class="w-6 h-6 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-banksoal::layouts.mahasiswa>
