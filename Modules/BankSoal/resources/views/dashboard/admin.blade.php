<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <span class="text-slate-800 font-semibold">Dashboard</span>
    @endsection

    <div class="w-full space-y-8">

        {{-- ===== HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Admin</h1>
            </div>
            <div class="flex items-center gap-2">
                @if($sesiOngoing > 0)
                    <a href="{{ route('banksoal.admin.cbt.live-proctoring') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors shadow-sm animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-white"></span>
                        Live Sekarang
                    </a>
                @endif

            </div>
        </div>

        {{-- ===== PLACEHOLDER BANK SOAL (akan diisi fase berikutnya) ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 border-dashed p-10 flex flex-col items-center justify-center transition-all hover:bg-slate-50/50">
            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-slate-500 font-medium text-center">Statistik Bank Soal</p>
            <p class="text-sm text-slate-400 mt-1 text-center">Akan ditambahkan di fase berikutnya.</p>
        </div>

        {{-- ===== SEKSI 2: UJIAN KOMPREHENSIF ===== --}}
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <h2 class="text-[13px] font-bold text-slate-500 uppercase tracking-wider">Ujian Komprehensif</h2>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            {{-- Zona C: Status Periode Aktif --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $periodeAktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                    <h3 class="text-[14px] font-semibold text-slate-800">Status Periode Ujian</h3>
                </div>

                @if($periodeAktif)
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-5">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-semibold rounded-full mb-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                                <p class="text-[16px] font-bold text-slate-800">{{ $periodeAktif->nama_periode }}</p>
                                <p class="text-[12px] text-slate-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->translatedFormat('d M') }} –
                                    {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="{{ route('banksoal.pendaftaran.index', ['periode_id' => $periodeAktif->id]) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Kelola Pendaftar
                                </a>
                                <a href="{{ route('banksoal.pendaftaran.alokasi-sesi.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl bg-primary text-white hover:bg-primary/90 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Kelola Jadwal
                                </a>
                            </div>
                        </div>

                        @if($statPendaftar)
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-4 text-center">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total</p>
                                    <p class="text-2xl font-black text-slate-800">{{ $statPendaftar->total }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Pendaftar</p>
                                </div>
                                <div class="bg-amber-50 rounded-xl p-4 text-center border border-amber-100">
                                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Menunggu</p>
                                    <p class="text-2xl font-black text-amber-700">{{ $statPendaftar->pending }}</p>
                                    <p class="text-[10px] text-amber-500 mt-0.5">Pending</p>
                                </div>
                            </div>

                            @if($statPendaftar->pending > 0)
                                <div class="mt-4 flex items-center justify-between gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                    <div class="flex items-center gap-2 text-[13px] text-amber-700">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span><strong>{{ $statPendaftar->pending }} pendaftar</strong> menunggu review Anda.</span>
                                    </div>
                                    <a href="{{ route('banksoal.pendaftaran.index', ['periode_id' => $periodeAktif->id]) }}"
                                       class="text-[12px] font-bold text-amber-700 hover:underline shrink-0">Review →</a>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="p-10 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="text-[13px] font-medium text-slate-600 mb-3">Tidak ada periode ujian yang sedang aktif.</p>
                        <a href="{{ route('banksoal.periode.setup') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#2A3A7C] text-white text-[13px] font-semibold rounded-lg hover:bg-[#1E2A5E] transition-colors">
                            + Buat Periode Baru
                        </a>
                    </div>
                @endif
            </div>

            {{-- Zona D: Analitik CBT --}}
            <div class="space-y-4">

                {{-- Filter Periode — Multi-select Custom Dropdown --}}
                <div class="flex items-center justify-between">
                    <form method="GET" action="{{ route('banksoal.dashboard') }}"
                          x-data="{
                              open: false,
                              search: '',
                              periode: @js($semuaPeriode),
                              selectedIds: @js($periodeIdFilters),
                              get filtered() {
                                  if (!this.search) return this.periode;
                                  return this.periode.filter(p => p.nama_periode.toLowerCase().includes(this.search.toLowerCase()));
                              }
                          }"
                          x-on:keydown.escape="open = false"
                          class="relative flex items-center gap-3">

                        {{-- Trigger Button --}}
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    @click="open = !open"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                <span>Filter</span>
                            </button>

                            {{-- Selected Badge Indicator --}}
                            @if(count($periodeIdFilters) > 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-primary/10 text-primary border border-primary/20 rounded-xl text-[12px] font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.553.894l-4 2A1 1 0 016 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/></svg>
                                    {{ count($periodeIdFilters) === 1 ? $semuaPeriode->firstWhere('id', $periodeIdFilters[0])?->nama_periode : count($periodeIdFilters) . ' Periode Dipilih' }}
                                </span>
                            @endif
                        </div>

                        {{-- Dropdown Panel --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             @click.outside="open = false"
                             class="absolute left-0 top-full mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 flex flex-col overflow-hidden"
                             style="display: none;">

                            {{-- Search --}}
                            <div class="p-3 border-b border-slate-100 shrink-0">
                                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text"
                                           x-model="search"
                                           placeholder="Cari nama periode..."
                                           class="flex-1 bg-transparent text-sm text-slate-700 placeholder-slate-400 outline-none"
                                           @keydown.enter.prevent
                                           @click.stop>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2 px-1">
                                    <span x-text="filtered.length"></span> dari {{ $semuaPeriode->count() }} periode
                                </p>
                            </div>

                            {{-- List --}}
                            <div class="max-h-56 overflow-y-auto py-2">
                                {{-- Pilih Semua --}}
                                <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors cursor-pointer border-b border-slate-50 mb-1">
                                    <input type="checkbox"
                                           @change="selectedIds = $event.target.checked ? periode.map(p => p.id) : []"
                                           :checked="selectedIds.length === periode.length && periode.length > 0"
                                           class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary/30">
                                    <span class="text-[13px] font-semibold text-slate-800">Pilih Semua</span>
                                </label>

                                {{-- Daftar Periode --}}
                                <template x-for="p in filtered" :key="p.id">
                                    <label class="flex items-center gap-3 px-4 py-2 hover:bg-slate-50 transition-colors cursor-pointer">
                                        <input type="checkbox" name="periode_id[]" :value="p.id" x-model="selectedIds"
                                               class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary/30">
                                        <span class="text-[13px] text-slate-700"
                                              :class="selectedIds.includes(p.id) ? 'font-medium text-slate-900' : ''"
                                              x-text="p.nama_periode"></span>
                                    </label>
                                </template>

                                <template x-if="filtered.length === 0">
                                    <div class="px-4 py-6 text-center text-[13px] text-slate-400">
                                        Tidak ditemukan.
                                    </div>
                                </template>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="p-3 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2 shrink-0">
                                <button type="button" @click="selectedIds = []" class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                                    Clear
                                </button>
                                <button type="submit" class="px-4 py-1.5 text-xs font-semibold bg-primary text-white rounded-lg hover:bg-primary/90 shadow-sm transition-colors">
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Reset badge --}}
                    @if(count($periodeIdFilters) > 0)
                        <a href="{{ route('banksoal.dashboard') }}"
                           class="inline-flex items-center gap-1.5 text-[12px] font-medium text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset filter
                        </a>
                    @endif
                </div>

                @php
                    $statSubtitle = empty($periodeIdFilters)
                        ? 'Dari semua periode'
                        : (count($periodeIdFilters) === 1
                            ? 'Dari ' . ($semuaPeriode->firstWhere('id', $periodeIdFilters[0])?->nama_periode ?? 'periode terpilih')
                            : 'Dari ' . count($periodeIdFilters) . ' periode terpilih');
                @endphp

                {{-- 4 Stat Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Total Selesai</p>
                        <p class="text-3xl font-black text-slate-800">{{ $cbTotalPeserta }}</p>
                        <p class="text-[11px] text-slate-400 mt-1 truncate" title="{{ $statSubtitle }}">{{ $statSubtitle }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Rata-rata Nilai</p>
                        <p class="text-3xl font-black text-[#2A3A7C]">{{ $cbtRataRata }}</p>
                        <p class="text-[11px] text-slate-400 mt-1 truncate" title="{{ $statSubtitle }}">{{ $statSubtitle }}</p>
                    </div>
                    <div class="col-span-2 bg-white rounded-xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Rasio Kelulusan</p>
                            <div class="flex gap-6">
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <p class="text-[11px] text-slate-500 font-medium">Lulus (≥60)</p>
                                    </div>
                                    <p class="text-2xl font-black text-slate-800">{{ $cbtLulus }}</p>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                        <p class="text-[11px] text-slate-500 font-medium">Tidak Lulus</p>
                                    </div>
                                    <p class="text-2xl font-black text-slate-800">{{ $cbtTidakLulus }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="h-24 w-24 shrink-0 flex items-center justify-center" id="chartKelulusan"></div>
                    </div>
                </div>

                {{-- CPL Achievement --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    {{-- Progress Bars --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-slate-100 shrink-0">
                            <h3 class="text-[14px] font-semibold text-slate-800">Ketercapaian CPL</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Persentase jawaban benar per Capaian Pembelajaran Lulusan</p>
                        </div>
                        <div class="p-6 space-y-5 overflow-y-auto max-h-[350px]">
                            @forelse($cplStats as $stat)
                                @php
                                    $barColor = $stat['persentase'] >= 80 ? 'bg-emerald-500'
                                              : ($stat['persentase'] >= 60 ? 'bg-amber-400' : 'bg-rose-500');
                                @endphp
                                <div>
                                    <div class="flex justify-between items-start gap-4 mb-1.5">
                                        <div>
                                            <span class="text-[12px] font-bold text-slate-800">{{ $stat['cpl_kode'] }}</span>
                                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">{{ $stat['deskripsi'] }}</p>
                                        </div>
                                        <span class="text-[13px] font-black text-slate-900 shrink-0">{{ $stat['persentase'] }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $stat['persentase'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-slate-400 text-[13px]">Data CPL belum tersedia.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Radar Chart --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
                        <h3 class="text-[14px] font-semibold text-slate-800 mb-1">Peta Kompetensi</h3>
                        <p class="text-[11px] text-slate-500 mb-4">Sebaran penguasaan materi</p>
                        <div class="flex-1 flex items-center justify-center min-h-[250px]" id="chartCPL"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Chart Kelulusan (Donut)
            const lulus = {{ $cbtLulus }};
            const tidakLulus = {{ $cbtTidakLulus }};
            
            if (lulus > 0 || tidakLulus > 0) {
                new ApexCharts(document.querySelector("#chartKelulusan"), {
                    series: [lulus, tidakLulus],
                    chart: { type: 'donut', height: '100%', sparkline: { enabled: true } },
                    labels: ['Lulus', 'Tidak Lulus'],
                    colors: ['#10B981', '#F43F5E'], // Emerald 500, Rose 500
                    plotOptions: { pie: { donut: { size: '75%' } } },
                    dataLabels: { enabled: false },
                    tooltip: { y: { formatter: val => val + " Mahasiswa" } },
                    stroke: { width: 0 }
                }).render();
            } else {
                document.querySelector("#chartKelulusan").innerHTML = '<span class="text-[10px] text-slate-400 text-center">Belum ada<br>data ujian</span>';
            }

            // 2. Chart CPL (Radar)
            const cplLabels = {!! json_encode($cplStats->pluck('cpl_kode')) !!};
            const cplData = {!! json_encode($cplStats->pluck('persentase')) !!};
            
            if (cplData.length > 0) {
                new ApexCharts(document.querySelector("#chartCPL"), {
                    series: [{ name: 'Persentase Capaian', data: cplData }],
                    chart: { type: 'radar', height: 350, toolbar: { show: false } },
                    labels: cplLabels,
                    stroke: { width: 2, colors: ['#2A3A7C'] },
                    fill: { opacity: 0.2, colors: ['#2A3A7C'] },
                    markers: { size: 3, colors: ['#fff'], strokeColors: '#2A3A7C', strokeWidth: 2, hover: { size: 5 } },
                    yaxis: { show: false, min: 0, max: 100 },
                    xaxis: { labels: { style: { colors: '#64748b', fontSize: '11px', fontFamily: 'Inter, sans-serif', fontWeight: 600 } } },
                    dataLabels: { enabled: false } // Disable label di tengah radar agar lebih rapi
                }).render();
            } else {
                document.querySelector("#chartCPL").innerHTML = '<span class="text-[12px] text-slate-400">Belum ada data CPL</span>';
            }
        });
    </script>
    @endpush

</x-banksoal::layouts.admin>
