<x-banksoal::layouts.mahasiswa>
    @section('breadcrumbs')
        <span class="text-slate-900 font-semibold">Portal Ujian</span>
    @endsection

    {{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh tanpa scroll ganda */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        
        /* Override layout default (main area) untuk layout Mahasiswa */
        main.overflow-y-auto { overflow: hidden !important; }
        main.overflow-y-auto > div { padding: 0 !important; max-width: none !important; height: 100% !important; display: flex; flex-direction: column; }
        #banksoal-main-content { padding: 0 !important; max-width: 100% !important; height: 100% !important; display: flex; flex-direction: column; }

        /* Container luar */
        .dash-wrap {
            display: flex; flex-direction: column; height: 100%;
            padding: 16px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 16px 24px;
        }

        /* Area Konten Box (Scrollable) */
        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 2px;
        }

        .dash-box-body > * {
            flex-shrink: 0;
            width: 100%;
            min-width: 0;
        }

        /* Opsional: Percantik scrollbar */
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        /* ── Mobile ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .dash-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .dash-box {
                border-radius: 10px;
                display: block;
                height: auto;
                overflow: visible;
            }
            .dash-box-header {
                padding: 12px 14px;
                position: sticky; top: 0; z-index: 20;
            }
            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    <div class="dash-wrap">
        <div class="dash-box">
            
            {{-- Area Header (Diam) --}}
            <div class="dash-box-header">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Portal Ujian Komprehensif</h1>
                        <p class="text-[13px] text-gray-500 mt-0.5">Pusat informasi dan pelaksanaan ujian komprehensif mahasiswa.</p>
                    </div>
                </div>
            </div>

            {{-- Area Konten (Bisa di-scroll) --}}
            <div class="dash-box-body">
                <div class="flex flex-col gap-4 w-full">
                    <!-- Major Application Status / Info Card -->
                    <div class="w-full flex flex-col">
            @if(isset($finishedSession) && $finishedSession)
                @php
                    $skor = $finishedSession->score ?? 0;
                    $lulus = $skor >= 60;
                @endphp
                <!-- STATE: EXAM FINISHED -->
                <div
                    class="flex flex-col border-2 border-slate-200 bg-white rounded-2xl shadow-sm overflow-hidden rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-700 border-slate-200 text-[11px] font-bold tracking-widest uppercase border  rounded-full mb-6">
                            Ujian Selesai
                        </span>

                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-8">
                            {{ $lulus ? 'Selamat! Anda Telah Lulus Ujian Komprehensif' : 'Mohon Maaf, Anda Belum Lulus Ujian Komprehensif' }}
                        </h3>

                        <div
                            class="flex items-center gap-6 p-6 border border-slate-200 bg-slate-50 w-full max-w-md  rounded-xl shadow-sm mb-8">
                            <div class="flex-shrink-0">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1 block">Total Poin</span>
                                <span
                                    class="text-5xl font-black text-slate-900">{{ (int)$skor }}</span>
                            </div>
                            <div class="border-l border-slate-200 h-16 mx-2"></div>
                            <div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1 block">Status</span>
                                <span
                                    class="text-xl font-black {{ $lulus ? 'text-emerald-700' : 'text-red-700' }}">{{ $lulus ? 'LULUS' : 'TIDAK LULUS' }}</span>
                            </div>
                        </div>

                        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                            class="py-3 px-6 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm tracking-widest uppercase transition-colors shadow-sm inline-block rounded-xl">
                            Lihat Riwayat Ujian
                        </a>
                    </div>
                </div>

            @elseif($pendaftar && $pendaftar->status_pendaftaran->value === 'approved')
                @php
                    $isUjianBerlangsung = false;
                    $hasJadwal = false;
                    $tanggalWaktuTeks = "";
                    if ($pendaftar->jadwal && $pendaftar->jadwal->tanggal_ujian) {
                        $hasJadwal = true;
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
                <div class="flex flex-col w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 w-full items-start">
                        <!-- Kolom Kiri: Status, Jadwal & Form Token -->
                        <div class="lg:col-span-7 flex flex-col w-full">
                        @if(!$isUjianBerlangsung)
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 border-green-200 text-[11px] font-bold tracking-widest uppercase border rounded-full">
                                Disetujui
                            </span>
                        </div>
                        @endif

                        @if($isUjianBerlangsung)
                            <h3 class="text-[20px] font-bold text-slate-900 tracking-tight mb-1">Akses Sesi Ujian</h3>
                            <p class="text-[13px] text-slate-600 leading-relaxed mb-4 max-w-xl">
                                Sesi ujian komprehensif Anda telah dibuka. Silakan masukkan <strong>Token Ujian (6 Digit)</strong> yang diberikan oleh pengawas ruangan untuk memulai pengerjaan soal. 
                                <br><br>
                                <span class="text-rose-600 font-medium">Perhatian:</span> Dilarang menutup halaman atau berpindah aplikasi selama ujian berlangsung.
                            </p>
                        @else
                            <h3 class="text-[20px] font-bold text-slate-900 tracking-tight mb-1">Pendaftaran Berhasil Disetujui</h3>
                            @if($hasJadwal)
                                <p class="text-[13px] text-slate-600 leading-relaxed mb-3 max-w-xl">
                                    Anda telah terdaftar sebagai peserta ujian komprehensif. Berikut adalah rincian jadwal ujian Anda:
                                </p>
                            @else
                                <p class="text-[13px] text-slate-600 leading-relaxed mb-4 max-w-xl">
                                    Anda telah terdaftar sebagai peserta ujian komprehensif. Saat ini Anda masih <strong>menunggu alokasi jadwal</strong> ujian dari admin. Silakan cek halaman ini secara berkala.
                                </p>
                            @endif
                        @endif

                        @if($hasJadwal)
                            <div class="mb-5 bg-slate-50/70 border border-slate-200 rounded-lg p-4 inline-block min-w-[280px] sm:min-w-[400px]">
                                <ul class="text-[13px] text-slate-700 space-y-3">
                                    <li class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-0">
                                        <span class="w-24 flex-shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider sm:mt-0.5">Sesi Ujian</span>
                                        <span class="font-bold text-slate-900">{{ $pendaftar->jadwal->nama_sesi }}</span>
                                    </li>
                                    <li class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-0">
                                        <span class="w-24 flex-shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider sm:mt-0.5">Waktu</span>
                                        <span class="font-medium text-slate-800">
                                            {{ \Carbon\Carbon::parse($pendaftar->jadwal->tanggal_ujian)->translatedFormat('l, d F Y') }}<br>
                                            Pukul {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_selesai)->format('H:i') }} WIB
                                        </span>
                                    </li>
                                    <li class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-0">
                                        <span class="w-24 flex-shrink-0 font-bold text-slate-500 uppercase text-[10px] tracking-wider sm:mt-0.5">Lokasi</span>
                                        <span class="font-medium text-slate-800">Laboratorium Jaringan Komputer<br>Departemen Teknik Komputer</span>
                                    </li>
                                </ul>
                            </div>
                        @endif                        <div x-data="{
                                confirmModal: false,
                                tokenError: '',
                                checking: false,
                                async checkAndConfirm() {
                                    const tokenInput = document.getElementById('token');
                                    const token = tokenInput ? tokenInput.value.trim() : '';

                                    if (!token || token.length !== 6) {
                                        this.tokenError = 'Token harus 6 karakter.';
                                        return;
                                    }

                                    this.tokenError = '';
                                    this.checking = true;

                                    try {
                                        const res = await fetch('{{ route('komprehensif.mahasiswa.engine.check-token') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                            },
                                            body: JSON.stringify({ token })
                                        });
                                        const data = await res.json();

                                        if (data.valid) {
                                            this.confirmModal = true;
                                        } else {
                                            this.tokenError = data.message || 'Token tidak valid.';
                                        }
                                    } catch (e) {
                                        this.tokenError = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                                    } finally {
                                        this.checking = false;
                                    }
                                }
                            }"
                            class="lg:col-span-7 border-t border-slate-200 pt-5 mt-2 lg:mt-0 {{ !$isUjianBerlangsung ? 'opacity-50 pointer-events-none' : '' }}">
                            <form x-ref="examForm" action="{{ route('komprehensif.mahasiswa.engine.validate') }}" method="POST"
                                class="flex flex-col gap-3 pb-2 sm:pb-4"
                                @submit.prevent="checkAndConfirm()">
                                @csrf
                                @php
                                    $hasTokenError = $errors->has('token') || session('error');
                                    $tokenErrorMessage = $errors->first('token') ?: session('error');
                                @endphp
                                <div class="flex-1 w-full sm:max-w-[280px]">
                                    <label for="token"
                                        class="block text-[11px] font-bold uppercase tracking-widest text-slate-900">Token
                                        Ujian</label>
                                    <div class="flex flex-row items-center gap-3 mt-2 relative">
                                        <input type="text" id="token" name="token" required
                                            :class="tokenError ? 'border-red-500 bg-red-50 focus:border-red-600' : 'bg-white border-slate-300 focus:border-slate-900'"
                                            class="flex-1 h-12 px-4 text-xl tracking-[0.5em] font-mono font-bold text-slate-900 border-2 focus:ring-0 outline-none transition-colors uppercase placeholder:text-slate-300 {{ $hasTokenError ? 'border-red-500 bg-red-50' : '' }}"
                                            placeholder="XXXXXX" maxlength="6" {{ !$isUjianBerlangsung ? 'disabled' : '' }} value="{{ old('token') }}"
                                            @input="tokenError = ''" />
                                        <button type="submit"
                                            :disabled="checking"
                                            class="h-12 px-6 {{ !$isUjianBerlangsung ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-primary hover:bg-primary/90 text-white' }} font-bold text-sm tracking-widest uppercase transition-colors flex items-center justify-center whitespace-nowrap rounded-xl shadow-sm"
                                            {{ !$isUjianBerlangsung ? 'disabled' : '' }}>
                                            <span x-show="!checking">Mulai Ujian</span>
                                            <span x-show="checking" x-cloak class="flex items-center gap-2">
                                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                Memeriksa...
                                            </span>
                                        </button>
                                    </div>

                                    {{-- Server-side error (from redirect back) --}}
                                    @if($hasTokenError)
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xs font-bold text-red-600 tracking-wide">{{ $tokenErrorMessage }}</p>
                                        </div>
                                    @endif

                                    {{-- AJAX error (Alpine.js) --}}
                                    <div x-show="tokenError" x-cloak
                                         class="flex items-center gap-1.5 mt-1.5">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-bold text-red-600 tracking-wide" x-text="tokenError"></p>
                                    </div>
                                </div>
                            </form>

                            <!-- Modal Popup: Konfirmasi Mulai Ujian -->
                            <div x-show="confirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
                                <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="confirmModal = false"></div>

                                <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden max-h-full">

                                    <div class="px-6 pt-6 pb-4 text-center">
                                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-50">
                                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-[17px] font-extrabold text-slate-800 tracking-tight mb-2">Mulai Ujian Sekarang?</h3>
                                        <p class="text-[13px] text-slate-500 font-medium leading-relaxed">
                                            Token valid. Mulai ujian dan kerjakan soal dengan durasi 100 menit.
                                        </p>
                                    </div>

                                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center gap-3">
                                        <button type="button" @click="confirmModal = false" class="flex-1 px-4 py-2.5 text-[13px] font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm rounded-xl focus:outline-none transition-colors">Batal</button>
                                        <button type="button" @click="$refs.examForm.submit()" class="flex-1 w-full px-4 py-2.5 text-[13px] font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm rounded-xl focus:outline-none transition-all">
                                            Ya, Mulai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div> <!-- Akhir Kolom Kiri -->

                        <!-- Kolom Kanan: Catatan Penting -->
                        <div class="lg:col-span-5 w-full">
                            @if($pendaftar->jadwal)
                                <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl h-full">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <h4 class="text-[11px] font-bold text-slate-800 uppercase tracking-widest">Catatan Penting</h4>
                                    </div>
                                    <ol class="list-decimal list-outside pl-4 text-[13px] text-slate-700 space-y-2 leading-relaxed">
                                        <li>Peserta wajib hadir 15 menit sebelum ujian dimulai. Peserta yang terlambat hadir tidak diperkenankan mengikuti ujian.</li>
                                        <li>Peserta dimohon membawa Kartu Tanda Mahasiswa (KTM). Peserta yang tidak dapat membuktikan identitasnya tidak diperbolehkan mengikuti ujian.</li>
                                        <li>Ujian bersifat buku tertutup dengan format 100 soal pilihan ganda.</li>
                                        <li>Peserta dinyatakan lulus jika memperoleh skor total &ge; 60.</li>
                                        <li>Ketentuan lain mengikuti aturan Tata Tertib Ujian Departemen Teknik Komputer.</li>
                                    </ol>
                                </div>
                            @endif
                        </div> <!-- Akhir Kolom Kanan -->
                    </div> <!-- Akhir Grid -->
                </div> <!-- Akhir Pembungkus State -->

            @elseif($pendaftar && $pendaftar->status_pendaftaran->value === 'pending')
                <!-- STATE: PENDING -->
                <div class="flex flex-col border border-amber-300 bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-200  rounded-full mb-6">Menunggu
                            Verifikasi</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Sedang Diproses
                        </h3>
                        <p class="text-slate-600 leading-relaxed max-w-xl mb-10 text-base">
                            Form pendaftaran ujian komprehensif Anda telah diterima dan sedang dalam tahap verifikasi oleh
                            admin. Hasil verifikasi dan jadwal ujian akan diperbarui di halaman ini.
                        </p>
                    </div>
                </div>
            @elseif($pendaftar && $pendaftar->status_pendaftaran->value === 'rejected')
                <!-- STATE: REJECTED -->
                <div class="flex flex-col border-2 border-red-500 bg-red-50 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-8 sm:p-10 flex flex-col items-start">
                        <span
                            class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200  rounded-full mb-6">Ditolak</span>
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
                    <div class="flex flex-col border border-amber-300 bg-amber-50 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-bold tracking-widest uppercase border border-amber-300  rounded-full mb-6">
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
                                oleh staf akademik. Silakan hubungi admin untuk informasi lebih lanjut.
                            </p>
                        </div>
                    </div>
                @elseif(now()->lt(\Carbon\Carbon::parse($activePeriode->tanggal_mulai)->startOfDay()))
                    <!-- STATE: NOT YET OPEN -->
                    <div class="flex flex-col border border-slate-300 bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-600 text-[11px] font-bold tracking-widest uppercase border border-slate-200  rounded-full mb-6">Belum
                                Dibuka</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Menunggu Jadwal Buka</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Registrasi ujian komprehensif <strong>{{ $activePeriode->nama_periode }}</strong> baru dapat
                                diakses mulai tanggal <strong
                                    class="text-slate-900">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d F Y') }}</strong>.
                            </p>
                            <button type="button" onclick="window.location.reload()"
                                class="py-3 px-6 bg-white border border-slate-300 text-slate-700 font-bold text-sm tracking-widest uppercase hover:bg-slate-100 hover:text-white transition-colors rounded-xl">
                                Cek Status Terbaru
                            </button>
                        </div>
                    </div>
                @elseif(now()->gt(\Carbon\Carbon::parse($activePeriode->tanggal_selesai)->endOfDay()))
                    <!-- STATE: CLOSED BUT ACTIVE -->
                    <div class="flex flex-col border border-slate-300 bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                            <span
                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-[11px] font-bold tracking-widest uppercase border border-red-200  rounded-full mb-6">Waktu Habis</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Masa Pendaftaran Berakhir</h3>
                            <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                Batas waktu pendaftaran Ujian Komprehensif untuk periode <strong>{{ $activePeriode->nama_periode }}</strong>
                                telah berakhir pada <strong>{{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d F Y') }}</strong>. 
                                Anda tidak dapat melakukan pendaftaran lagi pada periode ini. Silakan pantau halaman ini secara berkala untuk periode berikutnya.
                            </p>
                            <div class="mt-auto w-full">
                                <button disabled
                                    class="py-3 px-6 bg-slate-100 border border-slate-200 text-slate-400 font-bold text-sm tracking-widest uppercase cursor-not-allowed rounded-xl">
                                    Pendaftaran Ditutup
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- STATE: REGISTRATION OPEN -->
                    @if($kuotaPenuh)
                        <!-- STATE: QUOTA FULL -->
                        <div class="flex flex-col border border-slate-300 bg-slate-50 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-8 sm:p-10 flex flex-col items-start h-full">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 border-red-200 text-[11px] font-bold tracking-widest uppercase mb-6 rounded-full border">Kuota Penuh</span>
                                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Pendaftaran Ditutup Sementara</h3>
                                <p class="text-base text-slate-600 leading-relaxed mb-10 max-w-xl">
                                    Mohon maaf, kuota pendaftaran ujian komprehensif periode <strong>{{ $activePeriode->nama_periode }}</strong> saat ini telah terpenuhi sepenuhnya. Silakan hubungi bagian akademik atau pantau kembali halaman ini jika ada penambahan kuota.
                                </p>
                            </div>
                        </div>
                    @elseif(!$isEligible)
                        <!-- STATE: NOT ELIGIBLE -->
                        <div class="flex flex-col border border-slate-300 bg-white rounded-2xl shadow-sm overflow-hidden">
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
                        <div class="flex flex-col border-2 border-primary bg-white rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-8 sm:p-10 flex flex-col h-full">
                                <div class="flex items-center gap-4 mb-6">
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-primary/10 text-primary border border-primary/20 text-[11px] font-bold tracking-widest uppercase">Terbuka</span>
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
                                        class="inline-block py-4 px-8 bg-primary hover:bg-primary/90 text-white font-bold text-sm tracking-widest uppercase text-center transition-colors rounded-xl shadow-sm shadow-primary/25 rounded-xl">
                                        Daftar Ujian Komprehensif
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @else
                <!-- STATE: NO ACTIVE PERIOD -->
                <div class="flex flex-col border border-slate-300 bg-slate-50 rounded-2xl shadow-sm overflow-hidden">
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
        </div>
    </div>
</x-banksoal::layouts.mahasiswa>