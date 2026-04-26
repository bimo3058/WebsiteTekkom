<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Komprehensif - CBT Engine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Mencegah seleksi teks dan klik kanan */
        body {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        /* Style scrollbar khusus grid navigasi */
        .grid-scroll::-webkit-scrollbar { width: 6px; }
        .grid-scroll::-webkit-scrollbar-track { background: transparent; }
        .grid-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="h-full flex flex-col overflow-hidden" oncontextmenu="return false;">

<div x-data="cbtEngine()" x-init="initEngine()" class="h-full flex flex-col relative">

    <!-- Header / Topbar -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0 shadow-sm z-10">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-md">
                CBT
            </div>
            <div>
                <h1 class="font-bold text-slate-900 leading-tight">{{ $session->title }}</h1>
                <p class="text-xs text-slate-500 font-medium">{{ auth()->user()->name }} &bull; {{ auth()->user()->nim ?? 'NIM' }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <!-- Indikator Ragu-ragu -->
            <label class="flex items-center gap-2 cursor-pointer bg-amber-50 px-4 py-2 rounded-lg border border-amber-200 hover:bg-amber-100 transition-colors">
                <input type="checkbox" x-model="isRagu" @change="toggleRagu()" class="w-4 h-4 text-amber-500 border-amber-300 rounded focus:ring-amber-500">
                <span class="text-sm font-semibold text-amber-700">Ragu-ragu</span>
            </label>

            <!-- Timer -->
            <div class="flex items-center gap-3 bg-slate-900 text-white px-5 py-2.5 rounded-xl shadow-inner">
                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="font-mono text-xl font-bold tracking-wider" x-text="formattedTime" :class="timeLeft < 300 ? 'text-red-400 animate-pulse' : ''">
                    --:--:--
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex overflow-hidden">
        
        <!-- Left Side: Soal & Opsi (70%) -->
        <div class="flex-1 bg-white overflow-y-auto px-8 py-8 relative">
            
            <template x-if="currentSoal">
                <div class="max-w-4xl mx-auto pb-20">
                    
                    <!-- Nomor Soal -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-lg font-bold border border-blue-100">
                            Soal No. <span x-text="currentIndex + 1"></span>
                        </div>
                    </div>

                    <!-- Teks Soal -->
                    <div class="prose max-w-none text-slate-800 text-lg mb-10 leading-relaxed font-medium" x-html="currentSoal.soal">
                        <!-- Konten soal dirender di sini (Rich Text) -->
                    </div>

                    <!-- Pilihan Ganda -->
                    <div class="space-y-4">
                        <template x-for="(opsi, index) in currentSoal.opsi" :key="opsi.id">
                            <label class="block relative cursor-pointer group">
                                <input type="radio" 
                                       :name="'soal_'+currentSoal.id" 
                                       :value="opsi.id" 
                                       x-model="currentJawaban"
                                       @change="saveAnswer(opsi.id)"
                                       class="peer sr-only">
                                
                                <div class="flex p-4 bg-white border-2 border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-md items-start gap-4">
                                    <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center font-bold text-sm bg-slate-100 text-slate-600 peer-checked:bg-blue-600 peer-checked:text-white transition-colors">
                                        <span x-text="opsi.label"></span>
                                    </div>
                                    <div class="pt-1 prose max-w-none text-slate-700 text-base" x-html="opsi.teks"></div>
                                </div>
                            </label>
                        </template>
                    </div>

                </div>
            </template>

            <!-- Bottom Navigation Bar (Prev/Next) -->
            <div class="absolute bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-slate-200 p-4 px-8 flex justify-between items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <button @click="prevSoal()" :disabled="currentIndex === 0" class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-slate-700 bg-slate-100 hover:bg-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Soal Sebelumnya
                </button>
                <button @click="nextSoal()" :disabled="currentIndex === soals.length - 1" class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                    Soal Selanjutnya
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

        </div>

        <!-- Right Side: Grid Navigasi (30%) -->
        <div class="w-80 bg-slate-50 border-l border-slate-200 flex flex-col shrink-0 z-10 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)]">
            <div class="p-5 border-b border-slate-200 bg-white">
                <h3 class="font-bold text-slate-800">Navigasi Soal</h3>
                <div class="flex gap-4 mt-3 text-xs font-medium">
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-green-500"></div> Terjawab</div>
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-amber-400"></div> Ragu-ragu</div>
                    <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-white border border-slate-300"></div> Kosong</div>
                </div>
            </div>
            
            <div class="p-5 flex-1 overflow-y-auto grid-scroll">
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="(soal, idx) in soals" :key="soal.id">
                        <button @click="goToSoal(idx)" 
                                :class="{
                                    'ring-2 ring-blue-600 ring-offset-1': currentIndex === idx,
                                    'bg-amber-400 text-white border-transparent': soal.ragu_ragu,
                                    'bg-green-500 text-white border-transparent': !soal.ragu_ragu && soal.jawaban_terpilih,
                                    'bg-white text-slate-700 border-slate-300 hover:bg-slate-100': !soal.ragu_ragu && !soal.jawaban_terpilih
                                }"
                                class="w-full aspect-square rounded-lg border font-semibold text-sm transition-all flex items-center justify-center">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="p-5 border-t border-slate-200 bg-white">
                <button @click="submitExam()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold transition-colors shadow-md flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Selesaikan Ujian
                </button>
            </div>
        </div>

    </main>

    <!-- Overlay Loading Auto-Save (Hanya tampil sangat singkat) -->
    <div x-show="isSaving" x-transition.opacity class="fixed top-6 left-1/2 -translate-x-1/2 bg-slate-900/90 text-white px-4 py-2 rounded-full text-xs font-medium flex items-center gap-2 z-50 shadow-lg">
        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        Menyimpan jawaban...
    </div>

</div>

<script>
    // Konfigurasi dari backend
    const rawSoals = @json($jawabans);
    const endTimeRaw = "{{ $endTime->toIso8601String() }}";
    
    // Mencegah Copy Paste
    document.addEventListener('copy', (e) => { e.preventDefault(); return false; });
    document.addEventListener('paste', (e) => { e.preventDefault(); return false; });

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
                if(this.currentSoal) this.currentSoal.jawaban_terpilih = val;
            },

            get isRagu() {
                return this.currentSoal ? this.currentSoal.ragu_ragu : false;
            },
            set isRagu(val) {
                if(this.currentSoal) this.currentSoal.ragu_ragu = val;
            },

            initEngine() {
                this.updateTimer();
                this.timerInterval = setInterval(() => {
                    this.updateTimer();
                }, 1000);

                // Disable back button by pushing state forward
                history.pushState(null, null, location.href);
                window.onpopstate = function () {
                    history.go(1);
                };
                
                // Mencegah leave tanpa sengaja
                window.onbeforeunload = function() {
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
                const statusRagu = this.isRagu;

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
                } catch (error) {
                    console.error('Save error:', error);
                } finally {
                    setTimeout(() => { this.isSaving = false; }, 300);
                }
            },

            submitExam() {
                const unAnswered = this.soals.filter(s => !s.jawaban_terpilih).length;
                let msg = 'Apakah Anda yakin ingin menyelesaikan ujian ini?';
                if (unAnswered > 0) {
                    msg = `PERINGATAN: Masih ada ${unAnswered} soal yang BELUM dijawab. Yakin ingin mengakhiri?`;
                }

                if (confirm(msg)) {
                    window.onbeforeunload = null; // lepas pengunci
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
