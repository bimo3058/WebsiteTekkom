<x-banksoal::layouts.mahasiswa>
    @php
        $nim = old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id);
        $semesterAktif = 1;

        if ($nim && strlen($nim) >= 8) {
            $kodeTahun = substr($nim, 6, 2);
            if (is_numeric($kodeTahun)) {
                $tahunMasuk = 2000 + (int) $kodeTahun;
                $tahunSekarang = (int) date('Y');
                $bulanSekarang = (int) date('n');

                if ($bulanSekarang == 1) {
                    $semesterAktif = (($tahunSekarang - 1) - $tahunMasuk) * 2 + 1;
                } elseif ($bulanSekarang >= 2 && $bulanSekarang <= 7) {
                    $semesterAktif = ($tahunSekarang - $tahunMasuk) * 2;
                } else {
                    $semesterAktif = ($tahunSekarang - $tahunMasuk) * 2 + 1;
                }

                if ($semesterAktif < 1)
                    $semesterAktif = 1;
            }
        }
    @endphp

    <div class="flex flex-col lg:flex-row gap-8 xl:gap-16 items-start w-full">

        <!-- LEFT COLUMN: Info -->
        <div class="w-full lg:w-[42%] flex flex-col">

            <div
                class="inline-flex items-center gap-3 text-slate-900 font-bold tracking-[0.2em] text-[10px] uppercase mb-5 border-b border-slate-200 pb-3">
                <span>Periode {{ $activePeriode->nama_periode }}</span>
            </div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                Pendaftaran<br>
                <span class="text-slate-400">Ujian Komprehensif.</span>
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed max-w-lg mb-6">
                Formulir ini digunakan untuk pendaftaran Ujian Komprehensif Program Studi S1 Teknik Komputer bulan
                <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('F Y') }}</strong>.
                Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah
                siap mengikuti Sidang Tugas Akhir.<br><br>
                Dengan mengisi formulir ini, Anda menyatakan bersedia mematuhi seluruh aturan ujian yang
                berlaku.<br><br>
                Form akan ditutup pada hari
                <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('l, d F Y') }}</strong>
                pukul <strong>23.59 WIB</strong>.
            </p>

            <!-- Info Grid (Compact) -->
            <div class="grid grid-cols-2 gap-x-8 gap-y-5 border-t border-slate-200 pt-5">
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Pelaksanaan</h4>
                    <p class="text-xs font-mono font-medium text-slate-700">
                        {{ $activePeriode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M Y') : '-' }}
                        â€”
                        {{ $activePeriode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Durasi</h4>
                    <p class="text-xs font-mono font-medium text-slate-700">100 Menit</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Lokasi</h4>
                    <p class="text-xs font-mono font-medium text-slate-700">Lab. Jaringan Komputer</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Syarat</h4>
                    <p class="text-xs font-mono font-medium text-slate-700">Minimal Semester 7</p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Form -->
        <div class="w-full lg:w-[58%]">
            <div class="bg-white p-6 border border-slate-300 lg:sticky lg:top-4">

                <div class="mb-5 pb-4 border-b border-slate-200">
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight uppercase">Data Mahasiswa</h2>
                </div>

                <form action="{{ route('komprehensif.mahasiswa.pendaftaran.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Seksi 1: Identitas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIM -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Nomor
                                Induk</label>
                            <input type="text" name="nim" required readonly
                                value="{{ old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id) }}"
                                class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-mono text-sm px-3 outline-none cursor-not-allowed" />
                        </div>
                        <!-- Semester -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Semester</label>
                            <input type="number" name="semester" required readonly value="{{ $semesterAktif }}"
                                class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-mono text-sm px-3 outline-none cursor-not-allowed" />
                        </div>
                    </div>

                    <!-- Nama -->
                    <div class="space-y-1">
                        <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Nama
                            Lengkap</label>
                        <input type="text" name="nama" required readonly value="{{ old('nama', auth()->user()->name) }}"
                            class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-medium text-sm px-3 outline-none cursor-not-allowed" />
                    </div>

                    <!-- Seksi 2: Kontak & Akademik -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- WA -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">WhatsApp
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak_wa" required value="{{ old('kontak_wa') }}"
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-mono text-sm px-3 transition-colors outline-none @error('kontak_wa') border-red-500 @enderror"
                                placeholder="08xxxxxxxx" />
                            @error('kontak_wa')
                                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Target Lulus -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Target
                                Lulus <span class="text-red-500">*</span></label>
                            <input type="text" name="target_wisuda" required value="{{ old('target_wisuda') }}"
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none @error('target_wisuda') border-red-500 @enderror"
                                placeholder="Contoh: Periode 183 (Apr-Jun '26)" />
                            @error('target_wisuda')
                                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="border-t border-slate-200 w-full"></div>

                    <!-- Seksi 3: Dosen Pembimbing -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Dosbing 1 -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Pembimbing
                                Utama <span class="text-red-500">*</span></label>
                            <select name="dosen_pembimbing_1_id" required
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none cursor-pointer @error('dosen_pembimbing_1_id') border-red-500 @enderror">
                                <option value="" disabled selected>PILIH DOSEN</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_pembimbing_1_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ strtoupper($dosen->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_1_id')
                                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Dosbing 2 -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Pembimbing
                                2</label>
                            <select name="dosen_pembimbing_2_id"
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none cursor-pointer @error('dosen_pembimbing_2_id') border-red-500 @enderror">
                                <option value="" selected>PILIH DOSEN</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_pembimbing_2_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ strtoupper($dosen->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_2_id')
                                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full h-12 inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold tracking-widest uppercase transition-colors">
                        Submit Pendaftaran &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>