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
            padding: 0; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: none;
            border-bottom: 1px solid var(--c-border);
            border-radius: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
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
                border-radius: 0;
                border: none;
                border-bottom: 1px solid var(--c-border);
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
                <!-- STATE: EXAM FINISHED — pola stepper + 2 kartu -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap: 1-4 done, 5 Hasil aktif --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">3</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0" aria-current="step">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] @if($lulus) border-[#287F6E] text-[#287F6E] @else border-[#DF1C41] text-[#DF1C41] @endif text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-semibold @if($lulus) text-[#287F6E] @else text-[#DF1C41] @endif whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Kartu hasil full-width --}}
                        @if($lulus)
                        <div class="bg-[#DDF2EE]/60 border border-[#40C4AA]/40 rounded-[12px] p-5 sm:p-6 flex flex-col w-full">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-[#40C4AA]/20 flex items-center justify-center shrink-0" aria-hidden="true">
                                    <svg class="w-4 h-4 text-[#287F6E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                </span>
                                <h2 class="text-[18px] font-semibold text-[#174E43] tracking-tight">Lulus</h2>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex items-start gap-10 sm:gap-16">
                                <div>
                                    <p class="text-[20px] font-semibold text-[#2A3A7C] leading-tight">{{ number_format((float)$skor, 2, '.', '') }}</p>
                                    <p class="text-[12px] font-normal text-[#666D80] mt-1">Nilai Total</p>
                                </div>
                                <div>
                                    <p class="text-[20px] font-semibold text-[#287F6E] leading-tight">Lulus</p>
                                    <p class="text-[12px] font-normal text-[#666D80] mt-1">Status</p>
                                </div>
                            </div>
                            <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                                class="inline-flex sm:ml-auto self-start sm:self-center items-center justify-center px-5 py-2.5 bg-[#2A3A7C] hover:bg-[#2A3A7C]/90 text-white font-semibold text-[13px] text-center transition-colors rounded-lg">
                                Lihat Riwayat Ujian
                            </a>
                            </div>
                        </div>
                        @else
                        <div class="bg-[#FADAE1]/60 border border-[#DF1C41]/40 rounded-[12px] p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4 w-full">
                            <div class="flex items-start gap-10 sm:gap-16">
                                <div>
                                    <p class="text-[20px] font-semibold text-[#2A3A7C] leading-tight">{{ number_format((float)$skor, 2, '.', '') }}</p>
                                    <p class="text-[12px] font-normal text-[#666D80] mt-1">Nilai Total</p>
                                </div>
                                <div>
                                    <p class="text-[20px] font-semibold text-[#DF1C41] leading-tight">Tidak Lulus</p>
                                    <p class="text-[12px] font-normal text-[#666D80] mt-1">Status</p>
                                </div>
                            </div>
                            <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                                class="inline-flex sm:ml-auto self-start sm:self-center items-center justify-center px-5 py-2.5 bg-[#2A3A7C] hover:bg-[#2A3A7C]/90 text-white font-semibold text-[13px] text-center transition-colors rounded-lg">
                                Lihat Riwayat Ujian
                            </a>
                        </div>
                        @endif
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
                <!-- STATE: APPROVED & READY FOR EXAM GATE — pola stepper + 2 kartu -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap: 1-2 done, 3 aktif (4 aktif saat ujian berlangsung) --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0" @if(!$isUjianBerlangsung) aria-current="step" @endif>
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] @if($isUjianBerlangsung) bg-[#2A3A7C] border-[#2A3A7C] text-white @else border-[#2A3A7C] text-[#2A3A7C] bg-white @endif text-[13px] font-semibold flex items-center justify-center shrink-0">3</span>
                                <span class="flex-1 h-[2px] @if($isUjianBerlangsung) bg-[#2A3A7C] @else bg-[#DFE1E6] @endif mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] @if($isUjianBerlangsung) font-semibold text-[#0D0D12] @else font-semibold text-[#2A3A7C] @endif whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0" @if($isUjianBerlangsung) aria-current="step" @endif>
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] @if($isUjianBerlangsung) border-[#2A3A7C] text-[#2A3A7C] bg-white @else border-[#DFE1E6] text-[#A4ABB8] bg-white @endif text-[13px] font-medium flex items-center justify-center shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] @if($isUjianBerlangsung) font-semibold text-[#2A3A7C] @else font-normal text-[#666D80] @endif whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Grid konten 2:1 --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full items-start">
                        <div class="lg:col-span-2 bg-[#ECEFF3]/50 border border-[#AAB4D5]/50 rounded-[12px] p-5 sm:p-6 flex flex-col">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-white border border-[#DFE1E6] flex items-center justify-center shrink-0" aria-hidden="true">
                                    <svg class="w-4 h-4 text-[#5D6DA2]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 9h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                                </span>
                                <h2 class="text-[18px] font-semibold text-[#0D0D12] tracking-tight">Ujian Komprehensif</h2>
                                <span class="inline-flex items-center px-2.5 py-1 bg-[#DDF2EE] text-[#287F6E] text-[11px] font-semibold rounded-full">Disetujui</span>
                            </div>
                            @if($isUjianBerlangsung)
                                <p class="text-[14px] font-normal text-[#353849] leading-relaxed mt-3">
                                    Sesi ujian komprehensif Anda telah dibuka. Silakan masukkan <strong>Token Ujian (6 Digit)</strong> yang diberikan oleh pengawas ruangan untuk memulai pengerjaan soal.
                                </p>
                            @else
                                <p class="text-[14px] font-normal text-[#353849] leading-relaxed mt-3">
                                    Menunggu alokasi jadwal dari admin.
                                </p>
                            @endif
                            @if($hasJadwal)
                                <div class="mt-4 bg-white border border-[#DFE1E6] rounded-lg p-4">
                                    <dl class="text-[13px] space-y-3">
                                        <div class="flex items-start gap-3">
                                            <dt class="w-24 shrink-0 text-[10px] font-bold uppercase tracking-wider text-[#666D80] pt-0.5">Sesi Ujian</dt>
                                            <dd class="font-bold text-[#0D0D12] leading-relaxed">{{ $pendaftar->jadwal->nama_sesi }}</dd>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <dt class="w-24 shrink-0 text-[10px] font-bold uppercase tracking-wider text-[#666D80] pt-0.5">Waktu</dt>
                                            <dd class="font-medium text-[#0D0D12] leading-relaxed">{{ \Carbon\Carbon::parse($pendaftar->jadwal->tanggal_ujian)->translatedFormat('l, d F Y') }}<br><span class="font-normal text-[#353849]">Pukul {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pendaftar->jadwal->waktu_selesai)->format('H:i') }} WIB</span></dd>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <dt class="w-24 shrink-0 text-[10px] font-bold uppercase tracking-wider text-[#666D80] pt-0.5">Lokasi</dt>
                                            <dd class="font-medium text-[#0D0D12] leading-relaxed">Laboratorium Jaringan Komputer<br>Departemen Teknik Komputer</dd>
                                        </div>
                                    </dl>
                                </div>
                            @endif

                            <div class="border-t border-[#DFE1E6] my-4"></div>

                            <div x-data="{
                                confirmModal: false,
                                tokenError: '',
                                checking: false,
                                starting: false,
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
                            }">
                                <form x-ref="examForm" action="{{ route('komprehensif.mahasiswa.engine.validate') }}" method="POST"
                                    class="flex flex-col gap-2"
                                    @submit.prevent="checkAndConfirm()">
                                    @csrf
                                    @php
                                        $hasTokenError = $errors->has('token') || session('error');
                                        $tokenErrorMessage = $errors->first('token') ?: session('error');
                                    @endphp
                                    <label for="token" class="block text-[12px] font-semibold text-[#666D80]">Token ujian</label>
                                    <input type="text" id="token" name="token" required
                                        class="w-full sm:max-w-[320px] h-11 px-4 text-[14px] font-normal tracking-[0.15em] text-[#0D0D12] bg-white border border-[#DFE1E6] focus:border-[#2A3A7C] focus:ring-0 outline-none transition-colors rounded-lg placeholder:text-[#A4ABB8] placeholder:tracking-normal {{ $hasTokenError ? '!border-[#DF1C41] !bg-[#FADAE1]/30' : '' }} {{ !$isUjianBerlangsung ? '!bg-[#F6F8FA] cursor-not-allowed' : '' }}"
                                        placeholder="Masukkan token ujian" maxlength="6" {{ !$isUjianBerlangsung ? 'disabled' : '' }} value="{{ old('token') }}"
                                        @input="tokenError = ''" />
                                    <p class="text-[12px] font-normal text-[#666D80]">Kolom token aktif saat sesi ujian dimulai.</p>

                                    @if($hasTokenError)
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <svg class="w-4 h-4 text-[#DF1C41] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xs font-semibold text-[#DF1C41]">{{ $tokenErrorMessage }}</p>
                                        </div>
                                    @endif

                                    <div x-show="tokenError" x-cloak class="flex items-center gap-1.5 mt-1">
                                        <svg class="w-4 h-4 text-[#DF1C41] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs font-semibold text-[#DF1C41]" x-text="tokenError"></p>
                                    </div>

                                    <button type="submit"
                                        :disabled="checking"
                                        class="inline-flex items-center justify-center px-5 py-2.5 mt-2 self-start text-[13px] font-semibold rounded-lg transition-colors {{ !$isUjianBerlangsung ? 'bg-[#C1C7CF] text-white cursor-not-allowed' : 'bg-[#2A3A7C] hover:bg-[#2A3A7C]/90 text-white cursor-pointer' }}"
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
                                </form>

                                <!-- Modal Popup: Konfirmasi Mulai Ujian -->
                                <div x-show="confirmModal" tabindex="-1" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
                                    <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="confirmModal = false"></div>

                                    <div x-show="confirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden max-h-full">

                                        <div class="px-6 pt-6 pb-4 text-center">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-primary/10">
                                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-[17px] font-semibold text-[#0D0D12] tracking-tight mb-2">Mulai Ujian Sekarang?</h3>

                                            <div class="mt-3 flex gap-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-left">
                                                <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-[12px] font-medium text-amber-800 leading-relaxed"><span class="font-bold">Perhatian:</span> Dilarang menutup halaman atau berpindah aplikasi selama ujian berlangsung.</p>
                                            </div>
                                        </div>

                                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center gap-3">
                                            <button type="button" @click="confirmModal = false" class="flex-1 px-4 py-2.5 text-[13px] font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm rounded-xl focus:outline-none transition-colors cursor-pointer">Batal</button>
                                            <button type="button" :disabled="starting" @click="starting = true; $refs.examForm.submit()" class="flex-1 w-full px-4 py-2.5 text-[13px] font-semibold text-white bg-primary hover:bg-primary/90 disabled:opacity-60 disabled:cursor-not-allowed shadow-sm rounded-xl focus:outline-none transition-all cursor-pointer">
                                                Ya, Mulai
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($hasJadwal)
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-[#666D80]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                                <h4 class="text-[11px] font-semibold uppercase tracking-wider text-[#666D80]">Catatan Penting</h4>
                            </div>
                            <ol class="list-decimal list-outside pl-5 text-[13px] text-[#353849] space-y-1.5 leading-relaxed">
                                <li>Peserta wajib hadir 15 menit sebelum ujian dimulai. Peserta yang terlambat hadir tidak diperkenankan mengikuti ujian.</li>
                                <li>Peserta dimohon membawa Kartu Tanda Mahasiswa (KTM). Peserta yang tidak dapat membuktikan identitasnya tidak diperbolehkan mengikuti ujian.</li>
                                <li>Ujian bersifat buku tertutup dengan format 100 soal pilihan ganda.</li>
                                <li>Peserta dinyatakan lulus jika memperoleh skor total &ge; 60.</li>
                                <li>Ketentuan lain mengikuti aturan Tata Tertib Ujian Departemen Teknik Komputer.</li>
                            </ol>
                        </div>
                        @else
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                            <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->kuota_peserta)
                                            {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                        @else
                                            Tanpa batas kuota
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        @endif
                    </div>
                </div>

            @elseif($pendaftar && $pendaftar->status_pendaftaran->value === 'pending')
                <!-- STATE: PENDING — pola stepper + 2 kartu -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap: 1 done, 2 aktif --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Grid konten 2:1 --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                        <div class="lg:col-span-2 bg-[#F9ECCB]/60 border border-[#FFBD4C]/40 rounded-[12px] p-5 sm:p-6 flex flex-col">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 rounded-full bg-[#FFBD4C]/20 flex items-center justify-center shrink-0" aria-hidden="true">
                                    <svg class="w-4 h-4 text-[#956321]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                </span>
                                <h2 class="text-[18px] font-semibold text-[#5B3D1E] tracking-tight">Menunggu Verifikasi</h2>
                            </div>
                            <p class="text-[14px] font-normal text-[#5B3D1E] leading-relaxed">
                                Pendaftaran kamu sedang diperiksa admin. Status diperbarui di halaman ini setelah verifikasi selesai.
                            </p>
                        </div>
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                            <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->kuota_peserta)
                                            {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                        @else
                                            Tanpa batas kuota
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            @elseif($pendaftar && $pendaftar->status_pendaftaran->value === 'rejected')
                <!-- STATE: REJECTED — pola stepper + 2 kartu -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap: 1 done, 2 aktif (gagal) --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full bg-[#2A3A7C] border-[1.5px] border-[#2A3A7C] text-white text-[13px] font-semibold flex items-center justify-center shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#2A3A7C] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DF1C41] text-[#DF1C41] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#DF1C41] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Grid konten 2:1 --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full items-start">
                        <div class="lg:col-span-2 bg-[#FADAE1]/60 border border-[#DF1C41]/40 rounded-[12px] p-5 sm:p-6 flex flex-col">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-[#DF1C41]/15 flex items-center justify-center shrink-0" aria-hidden="true">
                                    <svg class="w-4 h-4 text-[#95122B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                </span>
                                <h2 class="text-[18px] font-semibold text-[#710E21] tracking-tight">Pendaftaran Ditolak</h2>
                            </div>
                            <p class="text-[14px] font-normal text-[#710E21] leading-relaxed mt-3">
                                Pendaftaran Anda tidak disetujui.
                            </p>
                            @if(!empty($pendaftar->catatan_admin))
                                <div class="mt-4 bg-white border border-[#DFE1E6] rounded-lg p-4">
                                    <span class="block text-[11px] font-semibold uppercase tracking-wider text-[#666D80] mb-1">Catatan Admin</span>
                                    <p class="text-[13px] text-[#353849] leading-relaxed">{{ $pendaftar->catatan_admin }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                            <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->kuota_peserta)
                                            {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                        @else
                                            Tanpa batas kuota
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

            @elseif(!$isEligible)
                <!-- STATE: NOT ELIGIBLE — selalu menang atas semua state periode -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap: 1 aktif --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Grid konten 2:1 --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full items-start">
                        <div class="lg:col-span-2 bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6 flex flex-col">
                            <h2 class="text-[18px] font-semibold text-[#0D0D12] tracking-tight">Belum Memenuhi Persyaratan</h2>
                            <p class="text-[14px] font-normal text-[#666D80] leading-relaxed mt-2">
                                Anda belum dapat melakukan pendaftaran Ujian Komprehensif karena belum memenuhi persyaratan akademik.
                            </p>
                            <button type="button" disabled
                                class="inline-flex self-start items-center justify-center px-5 py-2.5 mt-4 bg-[#ECEFF3] text-[#A4ABB8] font-semibold text-[13px] text-center rounded-lg cursor-not-allowed">
                                Daftar Ujian Komprehensif
                            </button>
                        </div>
                        @if($activePeriode)
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                            <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->kuota_peserta)
                                            {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                        @else
                                            Tanpa batas kuota
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        @else
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-2">Periode ujian</h3>
                            <p class="text-[13px] font-normal text-[#666D80] leading-relaxed">
                                Detail periode, kuota, dan jadwal pendaftaran tampil di sini setelah admin membuka pendaftaran.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

            @elseif($activePeriode)
                @if($activePeriode->pendaftaran_ditutup_paksa)
                    <!-- STATE: CLOSED BY ADMIN (EMERGENCY) — pola stepper + 2 kartu -->
                    <div class="flex flex-col gap-4 w-full">
                        {{-- Stepper 5 tahap: 1 aktif --}}
                        <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                            <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                                <div class="flex items-center w-full">
                                    <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">1</span>
                                    <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                </div>
                                <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Pendaftaran</span>
                            </li>
                            <li class="flex-1 flex flex-col min-w-0">
                                <div class="flex items-center w-full">
                                    <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">2</span>
                                    <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                </div>
                                <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Verifikasi</span>
                            </li>
                            <li class="flex-1 flex flex-col min-w-0">
                                <div class="flex items-center w-full">
                                    <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                    <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                </div>
                                <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                            </li>
                            <li class="flex-1 flex flex-col min-w-0">
                                <div class="flex items-center w-full">
                                    <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                    <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                </div>
                                <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                            </li>
                            <li class="flex-none flex flex-col min-w-0">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                                </div>
                                <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                            </li>
                        </ol>

                        {{-- Grid konten 2:1 --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full items-start">
                            <div class="lg:col-span-2 bg-white border border-[#DADEE9] rounded-[12px] p-7 flex flex-col gap-4">
                                <div class="flex items-center gap-3.5">
                                    <span class="w-11 h-11 rounded-full bg-[#E8EBF3] text-[#4A5168] flex items-center justify-center shrink-0" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                    <h2 class="text-[22px] font-semibold text-[#0D0D12] leading-tight tracking-tight">Pendaftaran Ditutup</h2>
                                </div>
                                <p class="text-[14px] font-normal text-[#4A5168] leading-relaxed max-w-[56ch]">
                                    Pendaftaran Ujian Komprehensif telah ditutup oleh admin.
                                </p>
                            </div>
                            <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                                <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                                <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                    <div class="flex items-start justify-between gap-3">
                                        <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                        <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-3">
                                        <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                        <dd class="font-medium text-[#0D0D12] text-right">
                                            @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                                {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="flex items-start justify-between gap-3">
                                        <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                        <dd class="font-medium text-[#0D0D12] text-right">
                                            @if($activePeriode->kuota_peserta)
                                                {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                            @else
                                                Tanpa batas kuota
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>
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
                        <!-- STATE: QUOTA FULL — pola stepper + 2 kartu -->
                        <div class="flex flex-col gap-4 w-full">
                            {{-- Stepper 5 tahap: 1 aktif --}}
                            <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                                <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">1</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Pendaftaran</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">2</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Verifikasi</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                                </li>
                                <li class="flex-none flex flex-col min-w-0">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                                </li>
                            </ol>

                            {{-- Grid konten 2:1 --}}
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full items-start">
                                <div class="lg:col-span-2 bg-[#F9ECCB]/60 border border-[#FFBD4C]/40 rounded-[12px] p-5 sm:p-6 flex flex-col">
                                    <h2 class="text-[18px] font-semibold text-[#5B3D1E] tracking-tight">Kuota penuh</h2>
                                    <p class="text-[14px] font-normal text-[#5B3D1E] leading-relaxed mt-2">
                                        Kuota pendaftaran ujian komprehensif telah penuh.
                                    </p>
                                </div>
                                <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                                    <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                                    <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                        <div class="flex items-start justify-between gap-3">
                                            <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                            <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                        </div>
                                        <div class="flex items-start justify-between gap-3">
                                            <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                            <dd class="font-medium text-[#0D0D12] text-right">
                                                @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                                    {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </dd>
                                        </div>
                                        <div class="flex items-start justify-between gap-3">
                                            <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                            <dd class="font-medium text-[#0D0D12] text-right">
                                                @if($activePeriode->kuota_peserta)
                                                    {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                                @else
                                                    Tanpa batas kuota
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- STATE: ELIGIBLE & REGISTRATION OPEN — pola stepper + 2 kartu -->
                        <div class="flex flex-col gap-4 w-full">
                            {{-- Stepper 5 tahap --}}
                            <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                                <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">1</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Pendaftaran</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">2</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Verifikasi</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                                </li>
                                <li class="flex-1 flex flex-col min-w-0">
                                    <div class="flex items-center w-full">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                        <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                                </li>
                                <li class="flex-none flex flex-col min-w-0">
                                    <div class="flex items-center">
                                        <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                                    </div>
                                    <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                                </li>
                            </ol>

                            {{-- Grid konten 2:1 --}}
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                                <div class="lg:col-span-2 bg-[#DDF2EE]/60 border border-[#40C4AA]/40 rounded-[12px] p-5 sm:p-6 flex flex-col">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="w-8 h-8 rounded-full bg-[#40C4AA]/20 flex items-center justify-center shrink-0" aria-hidden="true">
                                            <svg class="w-4 h-4 text-[#287F6E]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                        </span>
                                        <h2 class="text-[18px] font-semibold text-[#174E43] tracking-tight">Pendaftaran Terbuka</h2>
                                    </div>
                                    <p class="text-[14px] font-normal text-[#174E43] leading-relaxed mb-4">
                                        Anda memenuhi syarat untuk mengikuti ujian komprehensif periode ini.<br>
                                        Pendaftaran ditutup {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('l, d F Y') }} pukul 23:59 WIB.
                                    </p>
                                    <a href="{{ route('komprehensif.mahasiswa.pendaftaran.form') }}"
                                        class="inline-flex self-start items-center justify-center px-5 py-2.5 mt-2 bg-[#2A3A7C] hover:bg-[#2A3A7C]/90 text-white font-semibold text-[13px] text-center transition-colors rounded-lg">
                                        Daftar Ujian Komprehensif
                                    </a>
                                </div>
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-3 leading-snug">Periode {{ $activePeriode->nama_periode }}</h3>
                            <dl class="flex flex-col gap-2.5 text-[13px] leading-relaxed">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pendaftaran</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">{{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Pelaksanaan Ujian</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->tanggal_mulai_ujian && $activePeriode->tanggal_selesai_ujian)
                                            {{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="font-normal text-[#666D80] shrink-0">Kuota</dt>
                                    <dd class="font-medium text-[#0D0D12] text-right">
                                        @if($activePeriode->kuota_peserta)
                                            {{ $kuotaTerisi ?? 0 }}/{{ $activePeriode->kuota_peserta }}
                                        @else
                                            Tanpa batas kuota
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                    @endif
                @endif
            @else
                <!-- STATE: NO ACTIVE PERIOD — pola Image 1: stepper 5 + grid 2:1 -->
                <div class="flex flex-col gap-4 w-full">
                    {{-- Stepper 5 tahap --}}
                    <ol class="bg-white border border-[#DFE1E6] rounded-[12px] px-6 py-5 flex items-start w-full" aria-label="Tahapan ujian komprehensif">
                        <li class="flex-1 flex flex-col min-w-0" aria-current="step">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#2A3A7C] text-[#2A3A7C] text-[13px] font-semibold flex items-center justify-center bg-white shrink-0">1</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#2A3A7C] whitespace-nowrap mt-2">Pendaftaran</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">2</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Verifikasi</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">3</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Jadwal dan token</span>
                        </li>
                        <li class="flex-1 flex flex-col min-w-0">
                            <div class="flex items-center w-full">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">4</span>
                                <span class="flex-1 h-[2px] bg-[#DFE1E6] mx-2 sm:mx-3 rounded-full" aria-hidden="true"></span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Ujian</span>
                        </li>
                        <li class="flex-none flex flex-col min-w-0">
                            <div class="flex items-center">
                                <span class="w-8 h-8 rounded-full border-[1.5px] border-[#DFE1E6] text-[#A4ABB8] text-[13px] font-medium flex items-center justify-center bg-white shrink-0">5</span>
                            </div>
                            <span class="text-[13px] font-normal text-[#666D80] whitespace-nowrap mt-2">Hasil</span>
                        </li>
                    </ol>

                    {{-- Grid konten 2:1 --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 w-full">
                        <div class="lg:col-span-2 bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-10 h-10 rounded-full bg-[#ECEFF3] flex items-center justify-center shrink-0" aria-hidden="true">
                                    <svg class="w-5 h-5 text-[#666D80]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 9h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                                </span>
                                <h2 class="text-[18px] font-semibold text-[#0D0D12] tracking-tight">Belum Ada Periode Ujian</h2>
                            </div>
                            <p class="text-[14px] font-normal text-[#666D80] leading-relaxed">
                                Saat ini tidak ada periode ujian komprehensif yang aktif. Informasi pendaftaran akan tampil di sini setelah admin membuka pendaftaran.
                            </p>
                        </div>
                        <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6">
                            <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-2">Periode ujian</h3>
                            <p class="text-[13px] font-normal text-[#666D80] leading-relaxed">
                                Detail periode, kuota, dan jadwal pendaftaran tampil di sini setelah admin membuka pendaftaran.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
                </div>
            </div>
        </div>
    </div>
</x-banksoal::layouts.mahasiswa>