<x-layouts.mahasiswa>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Ruang Tunggu Ujian</h1>
            <p class="text-slate-600 mt-2 text-lg">{{ $jadwal->nama_sesi }}</p>
        </div>

        <x-ui.card class="overflow-hidden border-primary/20 shadow-lg relative">
            <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
            
            <div class="p-8 sm:p-10">
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start justify-between">
                    
                    <!-- Info Peserta -->
                    <div class="space-y-6 w-full md:w-1/2">
                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Peserta Ujian</p>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold text-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-lg">{{ auth()->user()->name }}</p>
                                    <p class="text-slate-500 font-mono">{{ $pendaftar->nim }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">Jadwal Ujian</p>
                            <p class="font-bold text-slate-900 text-lg">{{ $jadwal->tanggal_ujian->translatedFormat('l, d F Y') }}</p>
                            <p class="text-slate-600 mt-1 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-bold">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}</span> s.d <span class="font-bold">{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WIB</span>
                            </p>
                        </div>
                    </div>

                    <!-- Status/Countdown -->
                    <div class="w-full md:w-1/2 bg-slate-50 rounded-2xl p-8 border border-slate-200 text-center flex flex-col items-center justify-center min-h-[250px]">
                        
                        @if($canStart)
                            <div class="mb-6">
                                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Ujian Telah Dimulai</h3>
                                <p class="text-slate-500 text-sm mt-2">Pastikan koneksi internet Anda stabil. Ujian berlangsung selama 100 menit.</p>
                            </div>
                            
                            <form action="{{ route('komprehensif.mahasiswa.engine.start') }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin sudah siap memulai ujian? Waktu akan langsung berjalan.')">
                                @csrf
                                <x-ui.button type="submit" size="lg" class="w-full font-bold text-lg h-14 shadow-md bg-green-600 hover:bg-green-700 border-none gap-2">
                                    Mulai Ujian Sekarang
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </x-ui.button>
                            </form>
                        @else
                            <div x-data="countdownTimer('{{ $waktuMulai->toIso8601String() }}')" class="w-full">
                                <p class="text-slate-500 font-bold uppercase tracking-widest mb-4">Ujian Akan Dimulai Dalam</p>
                                
                                <div class="flex justify-center gap-4 mb-6" x-show="!isFinished">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-3xl font-bold text-primary" x-text="hours">00</div>
                                        <span class="text-xs text-slate-400 mt-2 font-bold uppercase">Jam</span>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-300 mt-3">:</div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-3xl font-bold text-primary" x-text="minutes">00</div>
                                        <span class="text-xs text-slate-400 mt-2 font-bold uppercase">Menit</span>
                                    </div>
                                    <div class="text-3xl font-bold text-slate-300 mt-3">:</div>
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-3xl font-bold text-primary" x-text="seconds">00</div>
                                        <span class="text-xs text-slate-400 mt-2 font-bold uppercase">Detik</span>
                                    </div>
                                </div>

                                <div x-show="isFinished" class="text-green-600 font-bold text-lg mb-4" style="display: none;">
                                    Menyiapkan ujian...
                                </div>

                                <x-ui.button disabled class="w-full font-bold opacity-50 cursor-not-allowed">
                                    Menunggu Waktu Ujian...
                                </x-ui.button>
                            </div>
                            
                            <script>
                                document.addEventListener('alpine:init', () => {
                                    Alpine.data('countdownTimer', (targetDate) => ({
                                        targetTime: new Date(targetDate).getTime(),
                                        hours: '00',
                                        minutes: '00',
                                        seconds: '00',
                                        isFinished: false,
                                        
                                        init() {
                                            this.updateTime();
                                            setInterval(() => {
                                                this.updateTime();
                                            }, 1000);
                                        },
                                        
                                        updateTime() {
                                            const now = new Date().getTime();
                                            const distance = this.targetTime - now;
                                            
                                            if (distance < 0) {
                                                this.isFinished = true;
                                                this.hours = '00';
                                                this.minutes = '00';
                                                this.seconds = '00';
                                                
                                                // Auto reload after 2 seconds to show the start button
                                                setTimeout(() => {
                                                    window.location.reload();
                                                }, 2000);
                                                return;
                                            }
                                            
                                            this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                                            this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                                            this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                                        }
                                    }));
                                });
                            </script>
                        @endif

                    </div>

                </div>
                
                <div class="mt-12 pt-8 border-t border-slate-100">
                    <h4 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Tata Tertib / Pakta Integritas
                    </h4>
                    <ul class="space-y-3 text-slate-600 text-[14.5px] pl-5 list-disc">
                        <li>Ujian berlangsung selama <strong>100 menit</strong> dan terdiri dari <strong>100 soal</strong> pilihan ganda.</li>
                        <li>Waktu ujian akan otomatis berjalan setelah Anda menekan tombol "Mulai Ujian".</li>
                        <li>Sistem dilengkapi dengan <strong>Anti-Cheat</strong>. Dilarang berpindah *Tab*, *Copy-Paste*, atau meminimalisir browser. Tindakan tersebut akan dicatat sebagai pelanggaran dan dapat membatalkan ujian Anda.</li>
                        <li>Jawaban Anda akan tersimpan secara otomatis setiap kali Anda memilih opsi. Jika terjadi putus koneksi, silakan <i>refresh</i> browser Anda.</li>
                        <li>Dengan menekan tombol "Mulai Ujian Sekarang", Anda menyetujui seluruh tata tertib ini.</li>
                    </ul>
                </div>

            </div>
        </x-ui.card>
    </div>
</x-layouts.mahasiswa>
