<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Ujian Komprehensif - CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- DOMPurify harus dimuat sebelum Alpine.js untuk sanitasi x-html -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            font-family: 'Inter', sans-serif;
            background-color: #E8EDF5;
        }
        .diagonal-hash {
            background-color: #FFF8E7;
            background-image: repeating-linear-gradient(45deg, #F59E0B 0px, #F59E0B 2px, transparent 2px, transparent 8px);
        }
        .diagonal-hash-active {
            background-color: #FEF3C7;
            background-image: repeating-linear-gradient(45deg, #D97706 0px, #D97706 2px, transparent 2px, transparent 8px);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 600, 'GRAD' 0, 'opsz' 24;
        }
        /* Lock everything into a 16:9 container */
        .viewport-container {
            aspect-ratio: 16 / 9;
            max-height: 100vh;
            max-width: 177.78vh; /* 100 * 16/9 */
            margin: auto;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Sembunyikan elemen x-cloak sebelum Alpine.js inisialisasi */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-slate-900 overflow-hidden flex items-center justify-center min-h-screen" oncontextmenu="return false;">
    <div x-data="cbtEngine()" x-init="initEngine()" class="viewport-container bg-white border-x border-[#0B266E]/20 shadow-2xl relative w-full h-full">

        <!-- ============================================================ -->
        <!-- OVERLAY 1: Fullscreen Required (tampil saat halaman dimuat)  -->
        <!-- ============================================================ -->
        <div x-show="showFullscreenOverlay"
             x-cloak
             class="fixed inset-0 z-[9999] bg-black flex flex-col items-center justify-center gap-8 text-white p-8">
            <div class="text-center max-w-lg">
                <div class="text-6xl mb-6">🖥️</div>
                <h2 class="text-2xl font-black uppercase tracking-widest mb-3">Mode Layar Penuh Diperlukan</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Demi integritas ujian, sistem mengharuskan Anda mengerjakan soal dalam mode <strong class="text-white">layar penuh (fullscreen)</strong>.
                    Keluar dari layar penuh akan dicatat sebagai pelanggaran.
                </p>
            </div>
            <template x-if="fullscreenSupported">
                <button @click="enterFullscreen()"
                        class="bg-white text-black border-2 border-white px-12 py-4 text-sm font-black uppercase tracking-widest hover:bg-slate-100 active:scale-95 transition-transform flex items-center gap-3">
                    <span class="material-symbols-outlined">fullscreen</span>
                    Masuk Mode Ujian (Layar Penuh)
                </button>
            </template>
            <template x-if="!fullscreenSupported">
                <div class="text-center">
                    <p class="text-amber-400 text-sm font-bold mb-4">⚠️ Browser Anda tidak mendukung mode layar penuh secara otomatis.</p>
                    <button @click="showFullscreenOverlay = false"
                            class="bg-amber-400 text-black px-10 py-3 text-sm font-black uppercase tracking-widest">
                        Lanjutkan Tanpa Layar Penuh
                    </button>
                </div>
            </template>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 2: Fullscreen Exit Warning (tampil jika keluar FS)  -->
        <!-- ============================================================ -->
        <div x-show="showFullscreenWarning"
             x-cloak
             class="fixed inset-0 z-[9998] bg-red-900 flex flex-col items-center justify-center gap-8 text-white p-8">
            <div class="text-center max-w-lg">
                <div class="text-6xl mb-6 animate-bounce">⚠️</div>
                <h2 class="text-2xl font-black uppercase tracking-widest mb-3 text-red-200">Pelanggaran Terdeteksi!</h2>
                <p class="text-red-100 text-sm leading-relaxed">
                    Anda keluar dari mode layar penuh. Tindakan ini telah <strong>dicatat sebagai pelanggaran</strong>.
                    Silakan kembali ke layar penuh untuk melanjutkan ujian.
                </p>
            </div>
            <button @click="enterFullscreen()"
                    class="bg-white text-red-900 border-2 border-white px-12 py-4 text-sm font-black uppercase tracking-widest hover:bg-red-50 active:scale-95 transition-transform flex items-center gap-3">
                <span class="material-symbols-outlined">fullscreen</span>
                Kembali ke Layar Penuh
            </button>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 3: Duplicate Tab Warning                            -->
        <!-- ============================================================ -->
        <div x-show="showDuplicateTabWarning"
             x-cloak
             class="fixed inset-0 z-[10000] bg-slate-950 flex flex-col items-center justify-center gap-8 text-white p-8">
            <div class="text-center max-w-lg">
                <div class="text-6xl mb-6">🚫</div>
                <h2 class="text-2xl font-black uppercase tracking-widest mb-3 text-red-300">Sesi Ganda Terdeteksi</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Ujian ini sudah dibuka di tab atau jendela browser lain.
                    Untuk keamanan integritas ujian, halaman ini <strong class="text-white">tidak dapat digunakan</strong>.
                    Tutup tab ini dan lanjutkan ujian di tab pertama.
                </p>
            </div>
            <button onclick="window.close()"
                    class="bg-red-600 text-white border-2 border-red-400 px-10 py-3 text-sm font-black uppercase tracking-widest hover:bg-red-700">
                Tutup Tab Ini
            </button>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 4: Submit Konfirmasi (kustom, tidak keluar fullscreen) -->
        <!-- ============================================================ -->
        <div x-show="showSubmitModal"
             x-cloak
             class="fixed inset-0 z-[9997] bg-black/75 flex items-center justify-center p-8">
            <div class="bg-white border-2 border-black max-w-md w-full p-8 shadow-2xl">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-4">📋</div>
                    <h3 class="text-xl font-black uppercase tracking-tight mb-3">Selesaikan Ujian?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="submitMessage"></p>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button @click="showSubmitModal = false"
                            class="border-2 border-slate-200 px-4 py-3 text-sm font-black uppercase tracking-tight hover:bg-slate-50 active:translate-y-0.5 transition-colors">
                        Kembali
                    </button>
                    <button @click="confirmSubmit()"
                            class="bg-[#0B266E] text-white border-2 border-[#091E5A] px-4 py-3 text-sm font-black uppercase tracking-tight hover:bg-[#091E5A] active:translate-y-0.5 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px">done_all</span>
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 5: Alert / Notifikasi (ganti browser alert)          -->
        <!-- ============================================================ -->
        <div x-show="showAlertModal"
             x-cloak
             class="fixed inset-0 z-[9996] bg-black/75 flex items-center justify-center p-8">
            <div class="bg-white border-2 border-black max-w-md w-full p-8 shadow-2xl">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-4" x-text="alertIcon"></div>
                    <h3 class="text-xl font-black uppercase tracking-tight mb-3" x-text="alertTitle"></h3>
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="alertMessage"></p>
                </div>
                <button @click="closeAlert()"
                        class="w-full bg-black text-white border-2 border-black px-4 py-3 text-sm font-black uppercase tracking-tight hover:bg-zinc-800 active:translate-y-0.5 transition-none">
                    OK
                </button>
            </div>
        </div>

        <!-- TopAppBar -->
        <header class="w-full z-50 flex justify-between items-center py-4 bg-[#0B266E] border-b-2 border-[#091E5A] px-10 shrink-0">
            <div class="w-full flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="text-xl font-black border-2 border-white/30 px-2 py-1 uppercase tracking-widest text-white">CBT</div>
                    <div class="text-sm font-bold uppercase tracking-widest text-white/60 hidden md:block">
                        {{ trim(preg_replace('/\s*\d+$/', '', $session->title)) }}
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2 border-2 border-red-700 px-4 py-2 text-lg font-black bg-red-600 text-white" :class="timeLeft < 300 ? 'animate-pulse' : ''">
                        <span class="material-symbols-outlined">timer</span>
                        <span x-text="formattedTime">--:--:--</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-bold uppercase text-white">Student: {{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-white/60 font-bold tracking-wider">{{ optional(auth()->user()->student)->student_number ?? 'NIM' }}</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-white">account_circle</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-grow flex overflow-hidden">
            <!-- Main Content Area (80%) -->
            <main class="w-4/5 px-10 py-8 overflow-y-auto">
                <template x-if="currentSoal">
                    <div class="border border-slate-200 p-8 bg-white min-h-full flex flex-col shadow-sm">
                        <div class="flex justify-between items-start mb-8">
                            <h1 class="text-3xl font-black uppercase tracking-tight">Soal No. <span x-text="currentIndex + 1"></span></h1>
                            <div class="border border-[#0B266E] bg-[#0B266E] text-white px-3 py-1 text-xs font-bold tracking-widest uppercase" x-text="currentSoal?.cpl_kode ?? 'SOAL UJIAN'"></div>
                        </div>
                        
                        <div class="mb-10 text-lg font-medium leading-relaxed prose max-w-none prose-p:my-2" x-html="currentSoal.soal">
                            <!-- Konten soal dirender di sini -->
                        </div>

                        <!-- Multiple Choice Options -->
                        <div class="space-y-4 mt-auto">
                            <template x-for="(opsi, index) in currentSoal.opsi" :key="opsi.id">
                                <label class="flex items-center gap-4 p-4 border-2 transition-colors cursor-pointer group"
                                       :class="currentJawaban == opsi.id ? 'border-[#0B266E] bg-[#EEF2FF]' : 'border-transparent ring-1 ring-slate-200 hover:bg-[#F0F4FF]'">
                                    
                                    <input class="w-6 h-6 border-2 border-slate-300 text-[#0B266E] focus:ring-0" 
                                           :name="'soal_'+currentSoal.id" 
                                           type="radio" 
                                           :value="opsi.id"
                                           x-model="currentJawaban" 
                                           @change="saveAnswer(opsi.id)"/>
                                    
                                    <span class="font-bold text-lg w-6" x-text="String.fromCharCode(65 + index) + '.'"></span>
                                    <div class="font-medium prose max-w-none prose-p:my-0" x-html="opsi.teks"></div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </main>

            <!-- SideNavBar (20%) -->
            <aside class="w-1/5 pr-10 py-8 overflow-hidden">
                <div class="bg-slate-50 border border-slate-200 h-full flex flex-col shadow-sm">
                    <div class="p-5 border-b border-slate-200 bg-[#0B266E]">
                        <h2 class="text-sm font-black uppercase tracking-tight text-white">Navigasi Soal (<span x-text="soals.length"></span>)</h2>
                        <div class="flex flex-wrap gap-x-4 gap-y-2 mt-3">
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-white/80"><div class="w-4 h-4 bg-[#4A80E8] border border-white/40"></div> Terjawab</div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-white/80"><div class="w-4 h-4 bg-yellow-400"></div> Ragu</div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-white/80"><div class="w-4 h-4 bg-white border border-white/50"></div> Kosong</div>
                        </div>
                    </div>
                    
                    <div class="p-3 overflow-hidden flex-grow flex justify-center items-center">
                        <div class="grid gap-1 w-full max-h-full" style="grid-template-columns: repeat(5, 1fr); grid-template-rows: repeat(20, 1fr); aspect-ratio: 1/4;">
                            <template x-for="(soal, idx) in soals" :key="soal.id">
                                <div @click="goToSoal(idx)" 
                                     :class="{
                                         'border-[3px] border-[#0B266E] scale-110 z-10 shadow-md': currentIndex === idx,
                                         'border border-slate-300': currentIndex !== idx,
                                         'bg-yellow-400 text-yellow-900': soal.ragu_ragu,
                                         'bg-[#4A80E8] text-white': !soal.ragu_ragu && soal.jawaban_terpilih,
                                         'bg-white text-slate-600': !soal.ragu_ragu && !soal.jawaban_terpilih
                                     }"
                                     class="flex items-center justify-center font-bold text-xs cursor-pointer transition-transform hover:scale-105 active:scale-95" 
                                     :title="'Soal ' + (idx + 1)">
                                    <span x-text="idx + 1"></span>
                                </div>
                            </template>
                        </div>
                    </div>


                </div>
            </aside>
        </div>

        <!-- Footer Actions -->
        <footer class="w-full z-50 flex justify-center items-center px-10 py-5 bg-white border-t border-slate-200 shrink-0 relative">
            <div class="flex items-center gap-3">
                <button @click="prevSoal()" :disabled="currentIndex === 0" :class="currentIndex === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-[#091E5A] active:translate-y-0.5'" class="border-2 border-[#091E5A] px-6 py-2.5 text-sm font-bold uppercase tracking-tight transition-colors flex items-center gap-2 bg-[#0B266E] text-white">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Prev
                </button>
                
                <button @click="toggleRagu()" :class="isRagu ? 'bg-yellow-400 text-yellow-900 border-yellow-500' : 'bg-yellow-400 border-yellow-500 text-yellow-900 hover:bg-yellow-300'" class="border-2 px-8 py-2.5 text-sm font-bold uppercase tracking-tight transition-colors active:translate-y-0.5 flex items-center gap-2">
                    <span class="material-symbols-outlined" x-text="isRagu ? 'flag' : 'outlined_flag'">flag</span>
                    <span x-text="isRagu ? 'Hapus Ragu' : 'Ragu-ragu'"></span>
                </button>
                
                <template x-if="currentIndex < soals.length - 1">
                    <button @click="nextSoal()" class="bg-[#0B266E] text-white border-2 border-[#091E5A] px-6 py-2.5 text-sm font-bold uppercase tracking-tight flex items-center gap-2 hover:bg-[#091E5A] active:translate-y-0.5 transition-colors">
                        Next
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </template>
                <template x-if="currentIndex === soals.length - 1">
                    <button @click="submitExam()" class="bg-red-600 text-white border-2 border-red-700 px-6 py-2.5 text-sm font-bold uppercase tracking-tight flex items-center gap-2 hover:bg-red-700 active:translate-y-0.5 transition-colors">
                        <span class="material-symbols-outlined" style="font-size:18px">done_all</span>
                        Submit
                    </button>
                </template>
            </div>
        </footer>

        <!-- Overlay Loading Auto-Save -->
        <div x-show="isSaving" x-transition.opacity class="fixed top-6 left-1/2 -translate-x-1/2 bg-[#0B266E] border-2 border-white/20 text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest flex items-center gap-3 z-50 shadow-2xl">
            <span class="material-symbols-outlined animate-spin" style="font-size: 18px;">autorenew</span>
            Menyimpan...
        </div>

    </div>

    <script>
        // Konfigurasi dari backend
        // Sanitasi konten soal dengan DOMPurify sebelum render di x-html
        // Mencegah XSS jika konten soal mengandung script/event handler
        const _sanitize = (html) => {
            if (typeof DOMPurify === 'undefined' || !html) return html || '';
            return DOMPurify.sanitize(html, {
                ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'p', 'br', 'ul', 'ol', 'li',
                               'img', 'table', 'tr', 'td', 'th', 'thead', 'tbody',
                               'code', 'pre', 'sub', 'sup', 'span'],
                ALLOWED_ATTR: ['src', 'alt', 'class', 'style', 'width', 'height'],
            });
        };
        const rawSoals = @json($jawabans).map(j => ({
            ...j,
            soal: _sanitize(j.soal),
            opsi: j.opsi.map(o => ({ ...o, teks: _sanitize(o.teks) }))
        }));
        const endTimeRaw = "{{ $endTime->toIso8601String() }}";

        // Mencegah Copy Paste
        document.addEventListener('copy', (e) => { e.preventDefault(); return false; });
        document.addEventListener('paste', (e) => { e.preventDefault(); return false; });

        // Anti-Cheat Logging
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) {
                logCheatEvent('tab_switch', 'Peserta berpindah tab browser atau minimize.');
            }
        });

        window.addEventListener("blur", () => {
            logCheatEvent('window_blur', 'Jendela ujian kehilangan fokus aplikasi.');
        });

        async function logCheatEvent(eventType, description) {
            try {
                await fetch('{{ route('komprehensif.mahasiswa.engine.log-violation') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        event_type: eventType,
                        description: description
                    })
                });
            } catch (e) {
                console.error('Failed to log cheat event:', e);
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('cbtEngine', () => ({
                soals: rawSoals,
                currentIndex: 0,
                endTime: new Date(endTimeRaw).getTime(),
                timeLeft: 0,
                formattedTime: '--:--:--',
                timerInterval: null,
                isSaving: false,
                showFullscreenOverlay: true,
                showFullscreenWarning: false,
                fullscreenSupported: !!document.documentElement.requestFullscreen,
                showDuplicateTabWarning: false,

                // Modal state
                showSubmitModal: false,
                submitMessage: '',
                showAlertModal: false,
                alertTitle: '',
                alertMessage: '',
                alertIcon: '⚠️',
                _alertCallback: null,

                get currentSoal() {
                    return this.soals[this.currentIndex] || null;
                },

                get currentJawaban() {
                    return this.currentSoal ? this.currentSoal.jawaban_terpilih : null;
                },
                set currentJawaban(val) {
                    if (this.currentSoal) this.currentSoal.jawaban_terpilih = val;
                },

                get isRagu() {
                    return this.currentSoal ? this.currentSoal.ragu_ragu : false;
                },
                set isRagu(val) {
                    if (this.currentSoal) this.currentSoal.ragu_ragu = val;
                },

                async enterFullscreen() {
                    const el = document.documentElement;
                    const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen;
                    if (!req) {
                        this.showFullscreenOverlay = false;
                        return;
                    }
                    try {
                        await req.call(el, { navigationUI: 'hide' });
                        this.showFullscreenOverlay = false;
                        this.showFullscreenWarning = false;
                    } catch (e) {
                        // Pengguna menolak atau browser memblokir — catat, tetap lanjutkan
                        this.showFullscreenOverlay = false;
                        logCheatEvent('fullscreen_denied', 'Peserta menolak atau browser memblokir permintaan layar penuh.');
                    }
                },

                initEngine() {
                    this.updateTimer();
                    this.timerInterval = setInterval(() => {
                        this.updateTimer();
                    }, 1000);

                    history.pushState(null, null, location.href);
                    window.onpopstate = function () {
                        history.go(1);
                    };

                    window.onbeforeunload = function () {
                        return "Yakin ingin keluar? Ujian sedang berlangsung.";
                    }

                    // ✅ Fullscreen enforcement: auto re-enter + log + overlay fallback
                    const onFullscreenChange = () => {
                        const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                        if (!isFullscreen && !this.showFullscreenOverlay) {
                            // Log pelanggaran
                            logCheatEvent('fullscreen_exit', 'Peserta keluar dari mode layar penuh selama ujian berlangsung.');

                            // 🔒 Auto re-enter fullscreen tanpa menunggu user klik
                            const el  = document.documentElement;
                            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen;
                            if (req) {
                                req.call(el, { navigationUI: 'hide' })
                                    .then(() => {
                                        // Berhasil kembali — tidak perlu tampilkan warning
                                        this.showFullscreenWarning = false;
                                    })
                                    .catch(() => {
                                        // Gagal (browser blokir tanpa gesture) — tampilkan overlay pemblokir
                                        this.showFullscreenWarning = true;
                                    });
                            } else {
                                this.showFullscreenWarning = true;
                            }
                        } else if (isFullscreen) {
                            this.showFullscreenWarning = false;
                        }
                    };
                    document.addEventListener('fullscreenchange', onFullscreenChange);
                    document.addEventListener('webkitfullscreenchange', onFullscreenChange);

                    // 🔒 Blokir shortcut keyboard yang umum digunakan untuk keluar
                    document.addEventListener('keydown', (e) => {
                        // F11 — toggle fullscreen
                        if (e.key === 'F11') {
                            e.preventDefault();
                            e.stopPropagation();
                            return;
                        }
                        // F5 / Ctrl+R — refresh halaman
                        if (e.key === 'F5' || (e.ctrlKey && (e.key === 'r' || e.key === 'R'))) {
                            e.preventDefault();
                            return;
                        }
                        // Ctrl+W — tutup tab
                        if (e.ctrlKey && (e.key === 'w' || e.key === 'W')) {
                            e.preventDefault();
                            return;
                        }
                        // Ctrl+T — tab baru
                        if (e.ctrlKey && (e.key === 't' || e.key === 'T')) {
                            e.preventDefault();
                            return;
                        }
                        // Ctrl+N — jendela baru
                        if (e.ctrlKey && (e.key === 'n' || e.key === 'N')) {
                            e.preventDefault();
                            return;
                        }
                        // F12 / Ctrl+Shift+I / Ctrl+Shift+J — DevTools
                        if (e.key === 'F12' ||
                            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) ||
                            (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j'))) {
                            e.preventDefault();
                            return;
                        }
                        // Ctrl+U — View Source
                        if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) {
                            e.preventDefault();
                            return;
                        }
                        // Alt+F4 — tutup jendela (Windows)
                        if (e.altKey && e.key === 'F4') {
                            e.preventDefault();
                            return;
                        }
                        // Alt+Left / Alt+Right — navigasi browser back/forward
                        if (e.altKey && (e.key === 'ArrowLeft' || e.key === 'ArrowRight')) {
                            e.preventDefault();
                            return;
                        }
                    }, true); // capture phase agar tidak bisa di-stopPropagation dari bawah

                    // ✅ M2: Deteksi parallel session (2 tab/window) via localStorage + BroadcastChannel
                    const sessionId = rawSoals[0]?.kompre_session_id || 'unknown';
                    const TAB_KEY   = 'cbt_session_' + sessionId;
                    const TAB_ID    = Date.now() + '-' + Math.random().toString(36).substr(2, 9);

                    if (localStorage.getItem(TAB_KEY)) {
                        // Tab lain sudah aktif — blok halaman ini
                        this.showDuplicateTabWarning = true;
                        logCheatEvent('duplicate_tab', 'Peserta membuka sesi ujian di lebih dari satu tab/window.');
                    } else {
                        localStorage.setItem(TAB_KEY, TAB_ID);
                        // Hapus marker saat tab ditutup agar sesi baru bisa dibuka lagi
                        window.addEventListener('pagehide', () => {
                            if (localStorage.getItem(TAB_KEY) === TAB_ID) {
                                localStorage.removeItem(TAB_KEY);
                            }
                        });
                    }

                    // BroadcastChannel untuk deteksi real-time jika tab dibuka hampir bersamaan
                    if (typeof BroadcastChannel !== 'undefined') {
                        const bc = new BroadcastChannel('cbt_' + sessionId);
                        bc.postMessage({ type: 'ping', tabId: TAB_ID });
                        bc.onmessage = (e) => {
                            if (e.data.type === 'ping' && e.data.tabId !== TAB_ID) {
                                // Tab lain baru buka — beritahu bahwa sudah ada tab aktif
                                bc.postMessage({ type: 'duplicate', originTabId: TAB_ID });
                            }
                            if (e.data.type === 'duplicate' && !this.showDuplicateTabWarning) {
                                this.showDuplicateTabWarning = true;
                                logCheatEvent('duplicate_tab', 'Tab duplikat terdeteksi via BroadcastChannel.');
                            }
                        };
                    }
                },

                updateTimer() {
                    const now = new Date().getTime();
                    const distance = this.endTime - now;

                    if (distance <= 0) {
                        clearInterval(this.timerInterval);
                        this.formattedTime = "00:00:00";
                        this.timeLeft = 0;
                        this.forceSubmitTimeUp();
                        return;
                    }

                    this.timeLeft = Math.floor(distance / 1000);
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    this.formattedTime =
                        String(hours).padStart(2, '0') + ":" +
                        String(minutes).padStart(2, '0') + ":" +
                        String(seconds).padStart(2, '0');
                },

                goToSoal(index) {
                    if (index >= 0 && index < this.soals.length) {
                        this.currentIndex = index;
                    }
                },

                nextSoal() {
                    this.goToSoal(this.currentIndex + 1);
                },

                prevSoal() {
                    this.goToSoal(this.currentIndex - 1);
                },

                async saveAnswer(opsiId) {
                    this.isSaving = true;
                    const kompreJawabanId = this.currentSoal.id;

                    // ✅ Optimistic UI: simpan nilai lama untuk rollback jika server gagal
                    const previousAnswer = this.currentSoal.jawaban_terpilih;
                    // Update state frontend langsung agar navigasi soal langsung akurat
                    this.currentSoal.jawaban_terpilih = opsiId;

                    try {
                        const response = await fetch("{{ route('komprehensif.mahasiswa.engine.save-answer') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                jawaban_id: kompreJawabanId,
                                opsi_terpilih: opsiId
                            })
                        });
                        const data = await response.json();

                        // ✅ Server menyatakan waktu habis — paksa finish
                        if (data.expired) {
                            window.onbeforeunload = null;
                            clearInterval(this.timerInterval);
                            this.showAlert(
                                'Waktu Ujian Habis',
                                'Waktu ujian telah habis. Jawaban Anda akan disubmit otomatis oleh server.',
                                '⏰',
                                () => { window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}"; }
                            );
                            return;
                        }

                        if (!data.success) {
                            // 🔄 Rollback: kembalikan ke jawaban sebelumnya jika server menolak
                            this.currentSoal.jawaban_terpilih = previousAnswer;
                            this.showAlert('Gagal Menyimpan', 'Gagal menyimpan jawaban. Periksa koneksi internet Anda!', '❌');
                        }
                        // Jika sukses: state sudah benar (diset secara optimistic di atas)
                    } catch (error) {
                        // 🔄 Rollback juga jika ada error jaringan
                        this.currentSoal.jawaban_terpilih = previousAnswer;
                        console.error('Save error:', error);
                        this.showAlert('Koneksi Bermasalah', 'Jawaban gagal tersimpan karena koneksi terputus. Periksa internet Anda!', '❌');
                    } finally {
                        setTimeout(() => { this.isSaving = false; }, 300);
                    }
                },

                async toggleRagu() {
                    this.isSaving = true;
                    const kompreJawabanId = this.currentSoal.id;
                    const statusRagu = !this.isRagu;

                    try {
                        const response = await fetch("{{ route('komprehensif.mahasiswa.engine.toggle-ragu') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                jawaban_id: kompreJawabanId,
                                is_ragu: statusRagu
                            })
                        });
                        const data = await response.json();

                        // ✅ Server menyatakan waktu habis — paksa finish
                        if (data.expired) {
                            window.onbeforeunload = null;
                            clearInterval(this.timerInterval);
                            this.showAlert(
                                'Waktu Ujian Habis',
                                'Waktu ujian telah habis. Jawaban Anda akan disubmit otomatis oleh server.',
                                '⏰',
                                () => { window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}"; }
                            );
                            return;
                        }

                        if (response.ok) {
                            this.isRagu = statusRagu;
                        }
                    } catch (error) {
                        console.error('Save error:', error);
                    } finally {
                        setTimeout(() => { this.isSaving = false; }, 300);
                    }
                },

                showAlert(title, message, icon = '⚠️', callback = null) {
                    this.alertTitle   = title;
                    this.alertMessage = message;
                    this.alertIcon    = icon;
                    this._alertCallback = callback;
                    this.showAlertModal = true;
                },

                closeAlert() {
                    this.showAlertModal = false;
                    if (this._alertCallback) {
                        const cb = this._alertCallback;
                        this._alertCallback = null;
                        cb();
                    }
                },

                submitExam() {
                    const unAnswered = this.soals.filter(s => !s.jawaban_terpilih).length;
                    this.submitMessage = unAnswered > 0
                        ? `⚠️ Masih ada ${unAnswered} soal yang BELUM dijawab. Yakin ingin mengakhiri ujian?`
                        : 'Setelah dikonfirmasi, Anda tidak dapat mengubah jawaban lagi.';
                    this.showSubmitModal = true;
                },

                confirmSubmit() {
                    this.showSubmitModal = false;
                    window.onbeforeunload = null;
                    window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}";
                },

                forceSubmitTimeUp() {
                    window.onbeforeunload = null;
                    this.showAlert(
                        'Waktu Ujian Habis',
                        'Waktu ujian telah habis. Jawaban Anda akan otomatis disubmit.',
                        '⏰',
                        () => { window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}"; }
                    );
                }
            }));
        });
    </script>
</body>
</html>