<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Ujian Komprehensif - CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            background-color: #eeeeee;
        }
        .diagonal-hash {
            background-image: repeating-linear-gradient(45deg, #e2e2e2 0px, #e2e2e2 2px, transparent 2px, transparent 8px);
        }
        .diagonal-hash-active {
            background-image: repeating-linear-gradient(45deg, #18181b 0px, #18181b 2px, transparent 2px, transparent 8px);
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
    </style>
</head>
<body class="text-slate-900 overflow-hidden flex items-center justify-center min-h-screen" oncontextmenu="return false;">
    <div x-data="cbtEngine()" x-init="initEngine()" class="viewport-container bg-white border-x-2 border-black shadow-2xl relative w-full h-full">

        <!-- TopAppBar -->
        <header class="w-full z-50 flex justify-between items-center py-4 bg-white border-b-2 border-black px-10 shrink-0">
            <div class="w-full flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="text-xl font-black border-2 border-black px-2 py-1 uppercase tracking-widest">CBT</div>
                    <div class="text-sm font-bold uppercase tracking-widest opacity-60 hidden md:block">
                        {{ $session->title }}
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2 border-2 border-black px-4 py-2 text-lg font-black bg-black text-white" :class="timeLeft < 300 ? 'bg-red-600 text-white animate-pulse' : 'bg-black text-white'">
                        <span class="material-symbols-outlined">timer</span>
                        <span x-text="formattedTime">--:--:--</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-bold uppercase">Student: {{ auth()->user()->name }}</p>
                            <p class="text-[10px] opacity-60 font-bold tracking-wider">{{ auth()->user()->nim ?? 'NIM' }}</p>
                        </div>
                        <span class="material-symbols-outlined text-4xl">account_circle</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-grow flex overflow-hidden">
            <!-- Main Content Area (75%) -->
            <main class="w-3/4 px-10 py-8 overflow-y-auto">
                <template x-if="currentSoal">
                    <div class="border-2 border-black p-8 bg-white min-h-full flex flex-col">
                        <div class="flex justify-between items-start mb-8">
                            <h1 class="text-3xl font-black uppercase tracking-tight">Soal No. <span x-text="currentIndex + 1"></span></h1>
                            <div class="border border-black bg-black text-white px-3 py-1 text-xs font-bold tracking-widest uppercase">SOAL UJIAN</div>
                        </div>
                        
                        <div class="mb-10 text-lg font-medium leading-relaxed prose max-w-none prose-p:my-2" x-html="currentSoal.soal">
                            <!-- Konten soal dirender di sini -->
                        </div>

                        <!-- Multiple Choice Options -->
                        <div class="space-y-4 mt-auto">
                            <template x-for="(opsi, index) in currentSoal.opsi" :key="opsi.id">
                                <label class="flex items-center gap-4 p-4 border-2 transition-none cursor-pointer group"
                                       :class="currentJawaban == opsi.id ? 'border-black bg-zinc-100' : 'border-transparent ring-1 ring-black hover:bg-zinc-50'">
                                    
                                    <input class="w-6 h-6 border-2 border-black text-black focus:ring-0" 
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

            <!-- SideNavBar (25%) -->
            <aside class="w-1/4 pr-10 py-8 overflow-hidden">
                <div class="bg-zinc-50 border-2 border-black h-full flex flex-col">
                    <div class="p-6 border-b-2 border-black bg-white">
                        <h2 class="text-base font-black uppercase tracking-tight">Navigasi Soal (<span x-text="soals.length"></span>)</h2>
                        <div class="flex flex-wrap gap-x-4 gap-y-2 mt-4">
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase"><div class="w-3 h-3 bg-zinc-800 border border-black"></div> Terjawab</div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase"><div class="w-3 h-3 diagonal-hash border border-black"></div> Ragu</div>
                            <div class="flex items-center gap-2 text-[10px] font-bold uppercase"><div class="w-3 h-3 bg-white border border-black"></div> Kosong</div>
                        </div>
                    </div>
                    
                    <div class="p-5 overflow-y-auto flex-grow scrollbar-hide">
                        <div class="grid grid-cols-5 xl:grid-cols-5 lg:grid-cols-4 gap-1.5">
                            <template x-for="(soal, idx) in soals" :key="soal.id">
                                <div @click="goToSoal(idx)" 
                                     :class="{
                                         'border-[3px] border-black scale-110 z-10': currentIndex === idx,
                                         'border border-black': currentIndex !== idx,
                                         'diagonal-hash': soal.ragu_ragu && !soal.jawaban_terpilih,
                                         'diagonal-hash-active': soal.ragu_ragu && soal.jawaban_terpilih,
                                         'bg-zinc-800 text-white': !soal.ragu_ragu && soal.jawaban_terpilih,
                                         'bg-white text-black': !soal.ragu_ragu && !soal.jawaban_terpilih
                                     }"
                                     class="aspect-square flex items-center justify-center font-bold text-xs cursor-pointer transition-transform hover:scale-105 active:scale-95" 
                                     :title="'Soal ' + (idx + 1)">
                                    <span x-text="idx + 1"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-6 border-t-2 border-black bg-white">
                        <button @click="submitExam()" class="w-full bg-red-600 text-white border-2 border-black px-4 py-3 text-sm font-black uppercase tracking-tight hover:bg-red-700 active:translate-y-0.5 transition-none flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">done_all</span>
                            Selesaikan Ujian
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Footer Actions -->
        <footer class="w-full z-50 flex justify-between items-center px-10 py-5 bg-white border-t-2 border-black shrink-0">
            <div class="w-full flex justify-between items-center">
                <button @click="prevSoal()" :disabled="currentIndex === 0" :class="currentIndex === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:invert active:translate-y-0.5'" class="border-2 border-black px-6 py-2.5 text-sm font-bold uppercase tracking-tight transition-none flex items-center gap-2 bg-white">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Prev
                </button>
                
                <button @click="toggleRagu()" :class="isRagu ? 'bg-amber-300' : 'diagonal-hash hover:bg-zinc-200'" class="border-2 border-black px-8 py-2.5 text-sm font-bold uppercase tracking-tight transition-none active:translate-y-0.5 flex items-center gap-2">
                    <span class="material-symbols-outlined" x-text="isRagu ? 'flag' : 'outlined_flag'">flag</span>
                    <span x-text="isRagu ? 'Hapus Ragu' : 'Ragu-ragu'"></span>
                </button>
                
                <button @click="nextSoal()" :disabled="currentIndex === soals.length - 1" :class="currentIndex === soals.length - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:invert active:translate-y-0.5'" class="bg-black text-white border-2 border-black px-10 py-2.5 text-sm font-bold uppercase tracking-tight transition-none flex items-center gap-2">
                    Next
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </footer>

        <!-- Overlay Loading Auto-Save -->
        <div x-show="isSaving" x-transition.opacity class="fixed top-6 left-1/2 -translate-x-1/2 bg-black border-2 border-white text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest flex items-center gap-3 z-50 shadow-2xl">
            <span class="material-symbols-outlined animate-spin" style="font-size: 18px;">autorenew</span>
            Menyimpan...
        </div>

    </div>

    <script>
        // Konfigurasi dari backend
        const rawSoals = @json($jawabans);
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
                        if (!data.success) {
                            alert('Gagal menyimpan jawaban. Periksa koneksi internet Anda!');
                        } else {
                            // Validasi perubahan UI
                            this.currentJawaban = opsiId;
                        }
                    } catch (error) {
                        console.error('Save error:', error);
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
                        
                        if (response.ok) {
                            this.isRagu = statusRagu;
                        }
                    } catch (error) {
                        console.error('Save error:', error);
                    } finally {
                        setTimeout(() => { this.isSaving = false; }, 300);
                    }
                },

                submitExam() {
                    const unAnswered = this.soals.filter(s => !s.jawaban_terpilih).length;
                    let msg = 'Apakah Anda yakin ingin menyelesaikan ujian ini?\n\nSetelah klik OK, Anda tidak bisa mengubah jawaban lagi.';
                    if (unAnswered > 0) {
                        msg = `PERINGATAN: Masih ada ${unAnswered} soal yang BELUM dijawab.\n\nYakin ingin mengakhiri?`;
                    }

                    if (confirm(msg)) {
                        window.onbeforeunload = null;
                        window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}";
                    }
                },

                forceSubmitTimeUp() {
                    window.onbeforeunload = null;
                    alert("Waktu Ujian Telah Habis! Jawaban Anda akan otomatis disubmit.");
                    window.location.href = "{{ route('komprehensif.mahasiswa.engine.finish') }}";
                }
            }));
        });
    </script>
</body>
</html>