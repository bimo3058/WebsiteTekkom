<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Ujian Komprehensif - CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- DOMPurify harus dimuat sebelum Alpine.js untuk sanitasi x-html -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
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
            max-width: 177.78vh;
            /* 100 * 16/9 */
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
        [x-cloak] {
            display: none !important;
        }

        .cbt-mobile-control {
            display: none;
        }

        .cbt-control-icon {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        @media(max-width:767px) {
            .viewport-container {
                aspect-ratio: auto;
                max-width: none;
                max-height: none;
                height: 100dvh;
                padding-bottom: calc(74px + env(safe-area-inset-bottom, 0px));
            }

            .viewport-container>header {
                padding: 12px;
            }

            .viewport-container>header>div {
                flex-wrap: wrap;
                gap: 10px;
                width: 100%;
            }

            .viewport-container>header>div>div {
                gap: 12px;
                flex-wrap: wrap;
            }

            .viewport-container>header .text-right {
                max-width: 150px;
            }

            .viewport-container>.flex-grow {
                min-height: 0;
            }

            .viewport-container main {
                width: 100%;
                padding: 12px;
                min-width: 0;
            }

            .viewport-container main>div {
                padding: 16px;
            }

            .viewport-container main h1 {
                font-size: 20px;
            }

            .viewport-container main .prose {
                overflow-wrap: anywhere;
                min-width: 0;
                font-size: 16px;
            }

            .viewport-container main img {
                max-width: 100%;
                height: auto;
            }

            .viewport-container main label {
                padding: 12px;
                gap: 10px;
                min-height: 48px;
            }

            .viewport-container main label input {
                flex-shrink: 0;
            }

            .cbt-question-list {
                display: none;
                position: fixed;
                inset: 90px 12px calc(88px + env(safe-area-inset-bottom, 0px));
                width: auto;
                padding: 0;
                z-index: 55;
                box-shadow: 0 0 0 100vmax rgb(15 23 42 / .4);
            }

            .cbt-question-list.cbt-question-list-open {
                display: block;
            }

            .cbt-question-list .p-3 {
                display: block;
                overflow-y: auto;
                min-height: 0;
            }

            .cbt-question-list .grid {
                grid-template-rows: none !important;
                grid-auto-rows: 44px;
                aspect-ratio: auto !important;
                max-height: none;
                gap: 8px;
            }

            .cbt-question-list .flex-nowrap {
                flex-wrap: wrap;
                row-gap: 8px;
            }

            .cbt-mobile-control {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
            }

            .viewport-container>footer {
                position: fixed;
                inset: auto 0 0;
                padding: 8px 6px calc(8px + env(safe-area-inset-bottom, 0px));
                min-height: 74px;
            }

            .viewport-container>footer>div {
                width: 100%;
                gap: 4px;
            }

            .viewport-container>footer button {
                flex: 1;
                min-width: 0;
                min-height: 52px;
                padding: 6px 2px;
                flex-direction: column;
                gap: 3px;
                font-size: 10px;
                letter-spacing: 0;
            }

            .viewport-container>footer svg {
                width: 19px;
                height: 19px;
                fill: none;
                stroke: currentColor;
                stroke-width: 1.8;
            }
        }
    </style>
</head>

<body class="text-slate-900 overflow-hidden flex items-center justify-center min-h-screen"
    oncontextmenu="return false;">
    <div x-data="cbtEngine()" x-init="initEngine()"
        class="viewport-container bg-white border-x border-[primary]/20 shadow-2xl relative w-full h-full">

        <!-- ============================================================ -->
        <!-- OVERLAY 1: Fullscreen Required (tampil saat halaman dimuat)  -->
        <!-- ============================================================ -->
        <div x-show="showFullscreenOverlay" x-cloak
            class="fixed inset-0 z-[9999] bg-black flex flex-col items-center justify-center gap-8 text-white p-8">
            <div class="text-center max-w-lg">
                <div class="text-6xl mb-6">🖥️</div>
                <h2 class="text-2xl font-black uppercase tracking-widest mb-3 text-[#4A80E8]">Mode Layar Penuh
                    Diperlukan</h2>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Demi integritas ujian, sistem mengharuskan Anda mengerjakan soal dalam mode <strong
                        class="text-white">layar penuh (fullscreen)</strong>.
                    Keluar dari layar penuh akan dicatat sebagai pelanggaran.
                </p>
            </div>
            <template x-if="fullscreenSupported">
                <button @click="enterFullscreen()"
                    class="bg-white text-black border-2 border-white px-12 py-4 text-sm font-black uppercase tracking-widest hover:bg-[#EEF2FF] active:scale-95 transition-transform flex items-center gap-3">
                    <span class="material-symbols-outlined">fullscreen</span>
                    Masuk Mode Ujian (Layar Penuh)
                </button>
            </template>
            <template x-if="!fullscreenSupported">
                <div class="text-center">
                    <p class="text-amber-400 text-sm font-bold mb-4">⚠️ Browser Anda tidak mendukung mode layar penuh
                        secara otomatis.</p>
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
        <div x-show="showFullscreenWarning" x-cloak
            class="fixed inset-0 z-[9998] bg-red-900 flex flex-col items-center justify-center gap-8 text-slate-800 p-8">
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
        <div x-show="showDuplicateTabWarning" x-cloak
            class="fixed inset-0 z-[10000] bg-slate-950 flex flex-col items-center justify-center gap-8 text-slate-800 p-8">
            <div class="text-center max-w-lg">
                <div class="text-6xl mb-6">🚫</div>
                <h2 class="text-2xl font-black uppercase tracking-widest mb-3 text-red-300">Sesi Ganda Terdeteksi</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Ujian ini sudah dibuka di tab atau jendela browser lain.
                    Untuk keamanan integritas ujian, halaman ini <strong class="text-slate-800">tidak dapat
                        digunakan</strong>.
                    Tutup tab ini dan lanjutkan ujian di tab pertama.
                </p>
            </div>
            <button onclick="window.close()"
                class="bg-red-600 text-slate-800 border-2 border-red-400 px-10 py-3 text-sm font-black uppercase tracking-widest hover:bg-red-700">
                Tutup Tab Ini
            </button>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 4: Submit Konfirmasi (kustom, tidak keluar fullscreen) -->
        <!-- ============================================================ -->
        <div x-show="showSubmitModal" x-cloak
            class="fixed inset-0 z-[9997] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6">
            <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-full"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                <div class="px-6 pt-6 pb-4 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                         :class="submitMessage.includes('BELUM') ? 'bg-amber-50' : 'bg-primary/10'">
                        <span class="material-symbols-outlined text-[28px]"
                              :class="submitMessage.includes('BELUM') ? 'text-amber-600' : 'text-primary'"
                              x-text="submitMessage.includes('BELUM') ? 'warning' : 'assignment_turned_in'"></span>
                    </div>
                    <h3 class="text-[17px] font-extrabold text-slate-800 tracking-tight mb-2">Selesaikan Ujian?</h3>
                    <p class="text-[13px] font-medium leading-relaxed"
                       :class="submitMessage.includes('BELUM') ? 'text-amber-800 bg-amber-50 border border-amber-200 rounded-xl p-3' : 'text-slate-500'"
                       x-text="submitMessage"></p>
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center gap-3">
                    <button @click="showSubmitModal = false"
                        class="flex-1 px-4 py-2.5 text-[13px] font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-sm rounded-xl focus:outline-none transition-colors">
                        Kembali
                    </button>
                    <button @click="confirmSubmit()"
                        class="flex-1 px-4 py-2.5 text-[13px] font-bold text-white bg-primary hover:bg-primary/90 shadow-sm rounded-xl focus:outline-none transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px">done_all</span>
                        Ya, Selesaikan
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- OVERLAY 5: Alert / Notifikasi (ganti browser alert)          -->
        <!-- ============================================================ -->
        <div x-show="showAlertModal" x-cloak
            class="fixed inset-0 z-[9996] bg-black/75 flex items-center justify-center p-8">
            <div class="bg-white border-2 border-black max-w-md w-full p-8 shadow-2xl">
                <div class="text-center mb-6">
                    <div class="text-5xl mb-4" x-text="alertIcon"></div>
                    <h3 class="text-xl font-black uppercase tracking-tight mb-3" x-text="alertTitle"></h3>
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="alertMessage"></p>
                </div>
                <button @click="closeAlert()"
                    class="w-full bg-black text-slate-800 border-2 border-black px-4 py-3 text-sm font-black uppercase tracking-tight hover:bg-zinc-800 active:translate-y-0.5 transition-none">
                    OK
                </button>
            </div>
        </div>

        <!-- TopAppBar -->
        <header
            class="w-full z-50 flex justify-between items-center py-4 bg-[primary] border-b-2 border-[primary-700] px-10 shrink-0">
            <div class="w-full flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div
                        class="text-xl font-black border border-primary bg-slate-50 border-b border-slate-200 text-slate-900/10 text-primary rounded-lg px-2 py-1 uppercase tracking-widest text-slate-800">
                        CBT</div>
                    <div class="text-sm font-bold uppercase tracking-widest text-slate-800/60 hidden md:block">
                        {{ trim(preg_replace('/\s*\d+$/', '', $session->title)) }}
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2 border border-red-200 px-4 py-2 text-lg font-black bg-red-50 text-red-600 rounded-xl shadow-sm"
                        :class="timeLeft < 300 ? 'animate-pulse' : ''">
                        <span class="material-symbols-outlined">timer</span>
                        <span x-text="formattedTime">--:--:--</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-bold uppercase text-slate-800">Student: {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-slate-800/60 font-bold tracking-wider">
                                {{ optional(auth()->user()->student)->student_number ?? 'NIM' }}
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-slate-800">account_circle</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-grow flex overflow-hidden">
            <!-- Main Content Area (80%) -->
            <main class="w-4/5 px-10 py-8 overflow-y-auto">
                <template x-if="currentSoal">
                    <div class="border border-slate-200 p-8 bg-white min-h-full flex flex-col shadow-sm rounded-2xl">
                        <div class="flex justify-between items-start mb-8">
                            <h1 class="text-3xl font-black uppercase tracking-tight">Soal No. <span
                                    x-text="currentIndex + 1"></span></h1>
                            <div class="border border-[primary] bg-[primary] text-slate-800 px-3 py-1 text-xs font-bold tracking-widest uppercase"
                                x-text="currentSoal?.cpl_kode ?? 'SOAL UJIAN'"></div>
                        </div>

                        <div class="mb-10 text-lg font-medium leading-relaxed prose max-w-none prose-p:my-2"
                            x-html="currentSoal.soal">
                            <!-- Konten soal dirender di sini -->
                        </div>

                        <!-- Multiple Choice Options -->
                        <div class="space-y-4 mt-auto">
                            <template x-for="(opsi, index) in currentSoal.opsi" :key="opsi.id">
                                <label
                                    class="flex items-center gap-4 p-4 border border-slate-200 transition-all cursor-pointer group rounded-xl hover:shadow-sm"
                                    :class="currentJawaban == opsi.id ? 'border-[primary] bg-[#EEF2FF]' : 'border-transparent ring-1 ring-slate-200 hover:bg-[#F0F4FF]'">

                                    <input class="w-6 h-6 border-2 border-slate-300 text-[primary] focus:ring-0"
                                        :name="'soal_'+currentSoal.id" type="radio" :value="opsi.id"
                                        x-model="currentJawaban" @change="saveAnswer(opsi.id)" />

                                    <span class="font-bold text-lg w-6"
                                        x-text="String.fromCharCode(65 + index) + '.'"></span>
                                    <div class="font-medium prose max-w-none prose-p:my-0" x-html="opsi.teks"></div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </main>

            <!-- SideNavBar (20%) -->
            <aside class="cbt-question-list w-1/5 pr-10 py-8 overflow-hidden"
                :class="{ 'cbt-question-list-open': mobileQuestions }" aria-label="Daftar nomor soal">
                <div
                    class="bg-white border border-slate-200 h-full flex flex-col shadow-sm rounded-2xl overflow-hidden">
                    <div class="p-5 border-b border-slate-200 bg-white">
                        <h2 class="text-sm font-black uppercase tracking-tight text-slate-800">Navigasi Soal</h2>
                        <button type="button" class="cbt-mobile-control mt-2 text-sm" @click="mobileQuestions = false"
                            aria-label="Tutup daftar soal">Tutup daftar soal</button>
                        <div class="flex flex-nowrap gap-x-4 mt-3">
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-slate-500">
                                <div class="w-4 h-4 bg-primary border-none rounded-[3px]"></div> Terjawab
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-slate-500">
                                <div class="w-4 h-4 bg-yellow-400 border-none rounded-[3px]"></div> Ragu-Ragu
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase text-slate-500">
                                <div class="w-4 h-4 bg-white border border-slate-300 rounded-[3px]"></div> Kosong
                            </div>
                        </div>
                    </div>

                    <div class="p-3 overflow-hidden flex-grow flex justify-center items-center">
                        <div class="grid gap-1 w-full max-h-full"
                            style="grid-template-columns: repeat(5, 1fr); grid-template-rows: repeat(20, 1fr); aspect-ratio: 1/4;">
                            <template x-for="(soal, idx) in soals" :key="soal.id">
                                <button type="button" @click="goToSoal(idx); mobileQuestions = false" :class="{
                                         'border-[3px] border-primary scale-110 z-10 shadow-md': currentIndex === idx,
                                         'border border-slate-300': currentIndex !== idx,
                                         'bg-yellow-400 text-yellow-900': soal.ragu_ragu,
                                         'bg-primary text-white': !soal.ragu_ragu && soal.jawaban_terpilih,
                                         'bg-white text-slate-600': !soal.ragu_ragu && !soal.jawaban_terpilih
                                     }"
                                    class="flex items-center justify-center font-bold text-xs cursor-pointer rounded-lg transition-transform hover:scale-105 active:scale-95"
                                    :title="'Soal ' + (idx + 1)">
                                    <span x-text="idx + 1"></span>
                                </button>
                            </template>
                        </div>
                    </div>


                </div>
            </aside>
        </div>

        <!-- Footer Actions -->
        <footer
            class="w-full z-50 flex justify-center items-center px-10 py-5 bg-white border-t border-slate-200 shrink-0 relative">
            <div class="flex items-center gap-3">
                <button @click="prevSoal()" :disabled="currentIndex === 0"
                    :class="currentIndex === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:opacity-90 active:translate-y-0.5'"
                    class="border border-slate-200 px-6 py-2.5 text-sm font-bold uppercase tracking-tight transition-colors flex items-center gap-2 bg-slate-100 text-slate-800 hover:bg-slate-200 rounded-xl">
                    <svg class="cbt-control-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m12 5-7 7 7 7M5 12h14" />
                    </svg>
                    Prev
                </button>
                <button type="button"
                    class="cbt-mobile-control border border-slate-200 rounded-xl bg-slate-100 text-slate-800"
                    @click="mobileQuestions = !mobileQuestions" :aria-expanded="mobileQuestions"
                    aria-label="Buka daftar soal">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z" />
                    </svg>
                    Soal
                </button>

                <button @click="toggleRagu()"
                    :class="isRagu ? 'bg-yellow-400 text-yellow-900 border-yellow-500' : 'bg-yellow-400 border-yellow-500 text-yellow-900 hover:bg-yellow-300'"
                    class="border px-8 py-2.5 rounded-xl text-sm font-bold uppercase tracking-tight transition-colors active:translate-y-0.5 flex items-center gap-2">
                    <svg class="cbt-control-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 21V4c5-4 9 4 14 0v10c-5 4-9-4-14 0" :fill="isRagu ? 'currentColor' : 'none'" />
                    </svg>
                    <span x-text="isRagu ? 'Hapus Ragu' : 'Ragu-ragu'"></span>
                </button>

                <template x-if="currentIndex < soals.length - 1">
                    <button @click="nextSoal()"
                        class="bg-primary text-white border-2 border-primary px-6 py-2.5 text-sm font-bold uppercase tracking-tight flex items-center gap-2 hover:opacity-90 active:translate-y-0.5 transition-colors rounded-xl">
                        Next
                        <svg class="cbt-control-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12 5 7 7-7 7M5 12h14" />
                        </svg>
                    </button>
                </template>
                <template x-if="currentIndex === soals.length - 1">
                    <button @click="submitExam()"
                        class="bg-red-600 text-white border px-6 py-2.5 rounded-xl shadow-sm hover:shadow-md text-sm font-bold uppercase tracking-tight flex items-center gap-2 hover:bg-red-700 active:translate-y-0.5 transition-colors">
                        <svg class="cbt-control-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m3 12 5 5L19 6M13 16l2 2 7-8" />
                        </svg>
                        Submit
                    </button>
                </template>
            </div>
        </footer>

        <!-- Overlay Loading Auto-Save -->
        <div x-show="isSaving" x-transition.opacity
            class="fixed top-6 left-1/2 -translate-x-1/2 bg-primary border border-white/20 text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest flex items-center gap-3 z-50 shadow-2xl">
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
                mobileQuestions: false,
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

                    // Polling force submit admin — jika sesi di-akhiri paksa, langsung redirect
                    this.statusInterval = setInterval(async () => {
                        try {
                            const res = await fetch('{{ route('komprehensif.mahasiswa.engine.status') }}', {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data.status === 'finished') {
                                clearInterval(this.timerInterval);
                                clearInterval(this.statusInterval);
                                window.onbeforeunload = null;
                                // Tampilkan alert lalu redirect ke dashboard
                                if (this.showAlert) {
                                    this.showAlert('Ujian Diakhiri', 'Sesi ujian Anda telah diakhiri paksa oleh pengawas.', 'warning');
                                    setTimeout(() => { window.location.href = '{{ route('komprehensif.mahasiswa.dashboard') }}'; }, 2000);
                                } else {
                                    window.location.href = '{{ route('komprehensif.mahasiswa.dashboard') }}';
                                }
                            }
                        } catch (e) {
                            // Abaikan error polling
                        }
                    }, 5000);

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
                            const el = document.documentElement;
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
                    const TAB_KEY = 'cbt_session_' + sessionId;
                    const TAB_ID = Date.now() + '-' + Math.random().toString(36).substr(2, 9);

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
                    this.alertTitle = title;
                    this.alertMessage = message;
                    this.alertIcon = icon;
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