<x-banksoal::layouts.mahasiswa>
    <!-- Page Header -->
    <div class="mb-12 border-b border-slate-200 pb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <p class="text-[11px] font-bold tracking-widest text-slate-500 uppercase mb-3">Portal Akademik Mahasiswa
                </p>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Ujian Komprehensif
                </h1>
            </div>

        </div>
    </div>

    <!-- Main Content Section -->
    <div class="flex flex-col gap-8 max-w-4xl">

        <!-- Major Application Status / Info Card -->
        <div class="w-full flex flex-col">
            @if(isset($finishedSession) && $finishedSession)
                @php
                    $skor = $finishedSession->score ?? 0;
                    $lulus = $skor >= 60;
                @endphp
                <!-- STATE: EXAM FINISHED -->
                <div
                    class="flex flex-col border-2 {{ $lulus ? 'border-emerald-500 bg-white' : 'border-red-500 bg-red-50' }}">
                    <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                        <span
                            class="inline-flex items-center px-3 py-1 {{ $lulus ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-red-100 text-red-800 border-red-200' }} text-[11px] font-bold tracking-widest uppercase border mb-6">
                            {{ $lulus ? 'Ujian Selesai - Lulus' : 'Ujian Selesai - Tidak Lulus' }}
                        </span>

                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">
                            {{ $lulus ? 'Selamat! Anda Telah Lulus Ujian Komprehensif' : 'Mohon Maaf, Anda Belum Lulus Ujian Komprehensif' }}
                        </h3>

                        <div
                            class="flex items-center gap-6 p-6 border {{ $lulus ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-white' }} w-full max-w-md mb-8">
                            <div class="flex-shrink-0">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1 block">Skor
                                    Akhir</span>
                                <span
                                    class="text-5xl font-black {{ $lulus ? 'text-emerald-700' : 'text-red-700' }}">{{ $skor }}</span>
                            </div>
                            <div class="border-l {{ $lulus ? 'border-emerald-200' : 'border-red-200' }} h-16 mx-2"></div>
                            <div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1 block">Status</span>
                                <span
                                    class="text-xl font-black {{ $lulus ? 'text-emerald-700' : 'text-red-700' }}">{{ $lulus ? 'LULUS' : 'TIDAK LULUS' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                            class="py-3 px-6 {{ $lulus ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }} font-bold text-sm tracking-widest uppercase transition-colors shadow-sm inline-block">
                            Lihat Riwayat Ujian
                        </a>
                    </div>
                </div>

            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'approved')
                @php
                    $isUjianBerlangsung = false;
                    $tanggalWaktuTeks = "<strong>menunggu alokasi jadwal</strong>";
                    if ($pendaftar->jadwal && $pendaftar->jadwal->tanggal_ujian) {
                        $tanggal = \Carbon\Carbon::parse($pendaftar->jadwal->tanggal_ujian)->translatedFormat('l, d F Y');
                        $waktu = \Carbon\Carbon::parse($pendaftar->jadwal->waktu_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($pendaftar->jadwal->waktu_selesai)->format('H:i') . ' WIB';
                        $sesi = $pendaftar->jadwal->nama_sesi;
                        $tanggalWaktuTeks = "pada Sesi (<strong>{$sesi}</strong>): <strong>{$tanggal}</strong>, pukul <strong>{$waktu}</strong> di <strong>Laboratorium Jaringan Komputer, Departemen Teknik Komputer</strong>";

                        $now = now();
                        $dateOnly = \Carbon\Carbon::parse($pendaftar->jadwal->tanggal_ujian)->format('Y-m-d');
                        $waktuMulai = \Carbon\Carbon::parse($dateOnly . ' ' . $pendaftar->jadwal->waktu_mulai);
                        $waktuSelesai = \Carbon\Carbon::parse($dateOnly . ' ' . $pendaftar->jadwal->waktu_selesai);

                        if ($now->between($waktuMulai, $waktuSelesai)) {
                            $isUjianBerlangsung = true;
                        }
                    }
                @endphp
                <!-- STATE: APPROVED & READY FOR EXAM GATE -->
                <div
                    class="flex flex-col border-2 {{ $isUjianBerlangsung ? 'border-indigo-500 bg-indigo-50' : 'border-green-500 bg-white' }}">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <span
                                class="inline-flex items-center px-3 py-1 {{ $isUjianBerlangsung ? 'bg-indigo-100 text-indigo-800 border-indigo-200' : 'bg-green-100 text-green-800 border-green-200' }} text-[11px] font-bold tracking-widest uppercase border">
                                {{ $isUjianBerlangsung ? 'Ujian Aktif' : 'Disetujui' }}
                            </span>
                        </div>

                        @if($isUjianBerlangsung)
                            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Waktu Ujian Telah Tiba</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-5 max-w-xl">
                                Sesi ujian komprehensif Anda telah dimulai. Jangan menutup halaman web atau berpindah aplikasi
                                selama ujian berlangsung.<br><br>
                                Masukkan <strong>Token Akses (6 Digit)</strong> yang diberikan oleh pengawas ujian untuk memulai
                                Test Engine.
                            </p>
                        @else
                            <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Pendaftaran Berhasil
                                Disetujui</h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-5 max-w-xl">
                                Anda telah terdaftar sebagai peserta ujian komprehensif. Ujian Komprehensif dijadwalkan
                                {!! $tanggalWaktuTeks !!}.
                            </p>
                        @endif

                        @if($pendaftar->jadwal)
                            <div class="mb-5 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                                <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-widest mb-2">Catatan Penting
                                </h4>
                                <ol class="list-decimal list-outside pl-4 text-[13px] text-slate-700 space-y-1 leading-relaxed">
                                    <li>Peserta wajib hadir 15 menit sebelum ujian dimulai. Peserta yang terlambat hadir tidak
                                        diperkenankan mengikuti ujian.</li>
                                    <li>Peserta dimohon membawa Kartu Tanda Mahasiswa (KTM). Peserta yang tidak dapat
                                        membuktikan identitasnya tidak diperbolehkan mengikuti ujian.</li>
                                    <li>Ujian bersifat buku tertutup dengan format 100 soal pilihan ganda.</li>
                                    <li>Peserta dinyatakan lulus jika memperoleh skor total &ge; 60.</li>
                                    <li>Ketentuan lain mengikuti aturan Tata Tertib Ujian Departemen Teknik Komputer.</li>
                                </ol>
                            </div>
                        @endif

                        <!-- Token Entry Form -->
                        <div
                            class="border-t border-slate-200 pt-5 {{ !$isUjianBerlangsung ? 'opacity-50 pointer-events-none' : '' }}">
                            <form action="{{ route('komprehensif.mahasiswa.engine.validate') }}" method="POST"
                                class="flex flex-col sm:flex-row items-end gap-3">
                                @csrf
                                <div class="w-full sm:w-2/3 space-y-2">
                                    <label for="token"
                                        class="block text-[11px] font-bold text-slate-900 uppercase tracking-widest">Token
                                        Sesi</label>
                                    <input type="text" id="token" name="token" required
                                        class="w-full h-12 px-4 text-xl tracking-[0.5em] font-mono font-bold text-slate-900 bg-white border-2 border-slate-300 focus:border-slate-900 focus:ring-0 outline-none transition-colors uppercase placeholder:text-slate-300"
                                        placeholder="XXXXXX" maxlength="6" {{ !$isUjianBerlangsung ? 'disabled' : '' }} />
                                </div>
                                <div class="w-full sm:w-1/3">
                                    <button type="submit"
                                        class="w-full h-12 px-5 {{ !$isUjianBerlangsung ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-blue-600 hover:bg-blue-700 text-white' }} font-bold text-sm tracking-widest uppercase transition-colors flex items-center justify-center rounded-xl shadow-sm"
                                        {{ !$isUjianBerlangsung ? 'disabled' : '' }}>
                                        Mulai Ujian
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'pending')
                <!-- STATE: PENDING -->
                <div class="flex flex-col border border-amber-300 bg-white">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-200 mb-6">Menunggu
                            Verifikasi</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Sedang Diproses
                        </h3>
                        <p class="text-slate-600 leading-relaxed max-w-xl mb-10 text-base">
                            Form pendaftaran ujian komprehensif Anda telah diterima dan sedang dalam tahap verifikasi oleh
                            admin. Hasil verifikasi dan jadwal ujian akan diperbarui di halaman ini.
                        </p>
                    </div>
                </div>
            @elseif($pendaftar && $pendaftar->status_pendaftaran === 'rejected')
                <!-- STATE: REJECTED -->
                <div class="flex flex-col border-2 border-red-500 bg-red-50">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200 mb-6">Ditolak</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Ditolak</h3>
                        <p class="text-slate-600 leading-relaxed max-w-xl mb-4 text-base">
                            Pendaftaran ujian komprehensif Anda tidak dapat dilanjutkan karena tidak memenuhi persyaratan.
                            Anda dipersilakan untuk mendaftar kembali pada periode ujian komprehensif yang akan datang.
                        </p>
                        @if(!empty($pendaftar->catatan_admin))
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-md w-full max-w-xl">
                                <span class="block text-xs font-bold text-slate-800 uppercase mb-1">Catatan Admin:</span>
                                <p class="text-sm text-slate-600 italic">"{{ $pendaftar->catatan_admin }}"</p>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($activePeriode)
                @if($activePeriode->pendaftaran_ditutup_paksa)
                    <!-- STATE: CLOSED BY ADMIN (EMERGENCY) -->
                    <div class="flex flex-col border border-amber-300 bg-amber-50">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-300 mb-6">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Ditutup Admin
                            </span>
                            <h3 class="text-3xl font-extrabold text-amber-900 tracking-tight mb-4">Pendaftaran Ditutup</h3>
                            <p class="text-base text-amber-800 leading-relaxed mb-10 max-w-xl">
                                Pendaftaran untuk <strong>{{ $activePeriode->nama_periode }}</strong> telah ditutup lebih awal
                                oleh staf akademik. Silakan hubungi BAAK untuk informasi lebih lanjut.
                            </p>
                        </div>
                    </div>
                @elseif(now()->lt(\Carbon\Carbon::parse($activePeriode->tanggal_mulai)->startOfDay()))
                    <!-- STATE: NOT YET OPEN -->
                    <div class="flex flex-col border border-slate-300 bg-white">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-600 text-[11px] font-bold tracking-widest uppercase border border-slate-200 mb-6">Belum
                                Dibuka</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Menunggu Jadwal Buka</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Registrasi ujian komprehensif <strong>{{ $activePeriode->nama_periode }}</strong> baru dapat
                                diakses mulai tanggal <strong
                                    class="text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d F Y') }}</strong>.
                            </p>
                            <button type="button" onclick="window.location.reload()"
                                class="py-3 px-6 bg-white border-2 border-slate-900 text-slate-900 font-bold text-sm tracking-widest uppercase hover:bg-slate-900 hover:text-white transition-colors">
                                Cek Status Terbaru
                            </button>
                        </div>
                    </div>
                @elseif(now()->gt(\Carbon\Carbon::parse($activePeriode->tanggal_selesai)->endOfDay()))
                    <!-- STATE: CLOSED BUT ACTIVE -->
                    <div class="flex flex-col border border-slate-300 bg-white">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200 mb-6">Ditutup</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Ditutup</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Pendaftaran Ujian Komprehensif untuk periode <strong>{{ $activePeriode->nama_periode }}</strong>
                                telah ditutup lebih awal oleh Admin. Silakan pantau halaman ini secara berkala atau hubungi
                                bagian akademik untuk informasi periode pendaftaran berikutnya.
                            </p>
                            <div class="mt-auto w-full">
                                <button disabled
                                    class="py-3 px-6 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-sm tracking-widest uppercase cursor-not-allowed">
                                    Daftar Ujian Komprehensif
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- STATE: REGISTRATION OPEN -->
                    @if(!$isEligible)
                        <!-- STATE: NOT ELIGIBLE -->
                        <div class="flex flex-col border border-slate-300 bg-white">
                            <div class="p-8 sm:p-10 flex flex-col h-full">
                                <span
                                    class="inline-flex items-center self-start px-3 py-1 bg-slate-900 text-white text-[11px] font-bold tracking-widest uppercase mb-6">Terkunci</span>

                                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Belum Memenuhi Persyaratan
                                </h3>
                                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                    Anda belum dapat melakukan pendaftaran Ujian Komprehensif karena belum memenuhi persyaratan
                                    akademik.
                                </p>

                                <div class="mt-auto">
                                    <button disabled
                                        class="py-3 px-6 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-sm tracking-widest uppercase cursor-not-allowed">
                                        Daftar Ujian Komprehensif
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- STATE: ELIGIBLE & REGISTRATION OPEN -->
                        <div class="flex flex-col border-2 border-blue-500 bg-white">
                            <div class="p-8 sm:p-10 flex flex-col h-full">
                                <div class="flex items-center gap-4 mb-6">
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 border border-blue-200 text-[11px] font-bold tracking-widest uppercase">Terbuka</span>
                                </div>

                                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4 leading-tight">Pendaftaran
                                    Ujian Komprehensif Dibuka</h3>

                                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                    Anda dapat mendaftar untuk ujian komprehensif periode
                                    <strong>{{ $activePeriode->nama_periode }}</strong>. Batas akhir pendaftaran adalah
                                    <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }},
                                        23:59 WIB</strong>.
                                </p>

                                <div
                                    class="mt-auto flex flex-col sm:flex-row sm:items-center justify-between border-t border-slate-200 pt-6 gap-6">
                                    <a href="{{ route('komprehensif.mahasiswa.pendaftaran.form') }}"
                                        class="inline-block py-4 px-8 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm tracking-widest uppercase text-center transition-colors rounded-xl shadow-sm shadow-blue-500/25">
                                        Daftar Ujian Komprehensif
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
                        <span
                            class="inline-flex items-center px-3 py-1 bg-slate-200 text-slate-600 text-[11px] font-bold tracking-widest uppercase mb-6">Ditutup</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Belum Dibuka</h3>
                        <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                            Saat ini belum ada periode pendaftaran ujian komprehensif yang aktif. Silakan pantau halaman ini
                            secara berkala atau hubungi bagian akademik untuk informasi periode berikutnya.
                        </p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-banksoal::layouts.mahasiswa>