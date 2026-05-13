<x-banksoal::layouts.admin>
    <div class="px-6 py-6 sm:px-8 sm:py-8 max-w-7xl mx-auto space-y-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Aktivasi Sesi & Token Ujian</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola status jadwal ujian, generate token, dan izinkan mahasiswa
                    untuk memulai CBT.</p>
            </div>

            @php
                $periodesForJs = $periodes->map(fn($p) => [
                    'id' => $p->id,
                    'nama' => $p->nama_periode,
                    'url' => route('banksoal.aktivasi.index', ['periode_id' => $p->id]),
                ])->values();
            @endphp
            <div x-data="{
                    open: false,
                    search: '',
                    periodeList: {{ $periodesForJs->toJson() }},
                    get filtered() {
                        const q = this.search.trim().toLowerCase();
                        if (!q) return this.periodeList;
                        return this.periodeList.filter(p => p.nama.toLowerCase().includes(q));
                    },
                    toggle() {
                        this.open = !this.open;
                        if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus());
                    },
                    close() { this.open = false; this.search = ''; }
                 }" class="relative flex-shrink-0" @keydown.escape.window="close()">

                <!-- Trigger Button -->
                <button type="button" @click="toggle()" class="group inline-flex items-center gap-2.5 pl-4 pr-3 py-2 rounded-lg text-[13px] font-semibold border transition-all duration-200 shadow-sm
                               {{ $selectedPeriode
    ? 'bg-white text-slate-700 border-slate-300 hover:border-blue-400 hover:shadow-md'
    : 'bg-blue-600 text-white border-blue-600 hover:bg-blue-700' }}">
                    @if($selectedPeriode)
                        <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                        <span class="max-w-[220px] truncate">{{ $selectedPeriode->nama_periode }}</span>
                    @else
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>Pilih Periode Ujian</span>
                    @endif
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 {{ $selectedPeriode ? 'text-slate-400' : 'text-white/70' }}"
                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Popover Panel -->
                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1" @click.outside="close()"
                    class="absolute top-full right-0 mt-2 z-50 bg-white border border-slate-200 rounded-2xl shadow-2xl w-72 overflow-hidden"
                    style="display:none">

                    <!-- Search Header -->
                    <div class="p-3 border-b border-slate-100">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                            </svg>
                            <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari nama periode..."
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2 pl-1">
                            <span x-text="filtered.length"></span> dari {{ $periodes->count() }} periode
                        </p>
                    </div>

                    <!-- Period List -->
                    <div class="overflow-y-auto max-h-64 py-1.5">
                        <template x-for="p in filtered" :key="p.id">
                            <a :href="p.url"
                                class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-slate-50 group/item"
                                :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-blue-50 text-blue-700' : 'text-slate-700'">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                    :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-blue-600' : 'bg-slate-300'"></span>
                                <span x-text="p.nama" class="flex-1 truncate"></span>
                                <svg x-show="p.id == {{ $selectedPeriodeId ?? 'null' }}"
                                    class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </a>
                        </template>

                        <!-- Empty state -->
                        <div x-show="filtered.length === 0" class="px-4 py-8 text-center">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                            </svg>
                            <p class="text-[13px] text-slate-400 font-medium">Tidak ditemukan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jadwals as $jadwal)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative">

                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($jadwal->status->value === 'aktif')
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 shadow-sm animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> AKTIF
                            </span>
                        @elseif($jadwal->status->value === 'selesai')
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                Selesai
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                Menunggu Jadwal
                            </span>
                        @endif
                    </div>

                    <div class="p-5 flex-1">
                        <div class="text-xs font-bold tracking-wider text-blue-600 uppercase mb-1">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('l, d M Y') }}</div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $jadwal->nama_sesi }}</h3>

                        <div class="flex items-center gap-2 text-sm text-slate-600 mb-1">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            {{ $jadwal->pendaftars_count }} / {{ $jadwal->kuota }} Mahasiswa
                        </div>

                        <!-- Token Section -->
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mt-auto">
                            <div class="text-xs font-medium text-slate-500 mb-1">Token Ujian Mahasiswa</div>
                            @if($jadwal->token)
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-mono text-xl font-black text-slate-800 tracking-widest">{{ $jadwal->token }}</span>
                                </div>
                            @else
                                <span class="text-sm font-medium text-slate-400 italic">Belum di-generate.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-slate-50 border-t border-slate-200 px-5 py-3">
                        @if($jadwal->status->value === 'menunggu_jadwal')
                            <form action="{{ route('banksoal.aktivasi.toggle', $jadwal->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit"
                                    class="w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                    Aktifkan Sesi & Generate Token
                                </button>
                            </form>
                        @elseif($jadwal->status->value === 'aktif')
                            <form action="{{ route('banksoal.aktivasi.toggle', $jadwal->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit"
                                    class="w-full text-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm font-semibold rounded-lg transition-colors">
                                    Tutup Sesi
                                </button>
                            </form>
                        @else
                            <button disabled
                                class="w-full text-center px-4 py-2 bg-slate-100 text-slate-400 cursor-not-allowed text-sm font-medium rounded-lg">
                                Sesi Telah Ditutup
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200 border-dashed">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-slate-500 font-medium">Belum ada jadwal ujian pada periode ini.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-banksoal::layouts.admin>