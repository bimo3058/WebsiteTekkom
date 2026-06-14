<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="text-gray-500 hover:text-[#2A3A7C] transition-colors">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <a href="{{ route('banksoal.admin.cbt.riwayat') }}" class="text-gray-500 hover:text-[#2A3A7C] transition-colors">Riwayat Ujian</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-800 font-medium">Detail Hasil</span>
    @endsection

    @php
        $isNoShow      = $session->title === 'Tidak Mengerjakan';
        $totalSoal     = $session->jawabans->count();
        $benar         = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
        $salah         = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
        $tidakDijawab  = $totalSoal - ($benar + $salah);
        $skor          = (int) ($session->score ?? 0);
        $lulus         = $skor >= 60;
        $pct           = min(100, $skor);
        $initial       = strtoupper(substr($session->user->name ?? 'M', 0, 1));

        $durasiText = '—';
        if (!$isNoShow && $session->started_at && $session->finished_at) {
            $diff       = \Carbon\Carbon::parse($session->started_at)->diff(\Carbon\Carbon::parse($session->finished_at));
            $durasiText = $diff->i . ' Menit ' . $diff->s . ' Detik';
        }
    @endphp

    <style>
        .score-bar-fill { transition: width 0.9s cubic-bezier(0.16, 1, 0.3, 1); }
    </style>

    <div class="w-full space-y-8">

        {{-- ── Header ── --}}
        <div class="mb-6">
            <h1 class="text-[22px] font-bold text-gray-900 tracking-tight mb-0.5">Detail Hasil Ujian</h1>
            <p class="text-[13px] text-gray-500">Laporan evaluasi komprehensif berbasis capaian pembelajaran (CPL).</p>
        </div>

        {{-- ── Summary Panel ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl p-8 border border-gray-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-start gap-5 mb-8">
                    <div class="w-14 h-14 rounded-full bg-[#2A3A7C]/5 text-[#2A3A7C] flex items-center justify-center font-bold text-xl flex-shrink-0 border border-[#2A3A7C]/10">
                        {{ $initial }}
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-0.5">{{ $session->user->name }}</h2>
                        <p class="text-[13px] text-gray-500 mb-3">{{ optional($session->user->student)->student_number ?? '—' }}</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-50 text-gray-600 border border-gray-200/60 text-[11px] font-medium">
                            S1 Teknik Komputer
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1.5">Periode Ujian</p>
                        <p class="text-[13px] text-gray-800 font-medium leading-snug">
                            {{ $session->jadwal?->periode?->nama_periode ?? 'Ujian Komprehensif' }}
                        </p>
                        <p class="text-[12px] text-gray-400 mt-0.5">
                            Sesi {{ $session->jadwal?->nama_sesi ?? '—' }}
                            @if($session->jadwal?->tanggal_ujian)
                                &bull; {{ \Carbon\Carbon::parse($session->jadwal->tanggal_ujian)->locale('id')->isoFormat('D MMM YYYY') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-1.5">Durasi Pengerjaan</p>
                        <p class="text-[13px] text-gray-800 font-medium">{{ $durasiText }}</p>
                    </div>
                </div>
            </div>

            {{-- Score Hero Card --}}
            <div class="bg-white rounded-xl p-8 border border-gray-200/80 flex flex-col items-center justify-center text-center shadow-sm">
                <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider mb-3">Skor Akhir</p>
                <div class="mb-3">
                    <span class="text-[72px] leading-none font-bold tracking-tight {{ $lulus ? 'text-[#2A3A7C]' : 'text-rose-600' }}">{{ $skor }}</span>
                    <span class="text-2xl font-medium text-gray-300">/100</span>
                </div>
                <span class="inline-flex items-center px-3.5 py-1 rounded-md text-[12px] font-semibold mb-8
                    {{ $lulus ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                    {{ $lulus ? 'LULUS' : 'TIDAK LULUS' }}
                </span>

                {{-- Stats --}}
                <div class="flex items-center justify-center w-full max-w-xs gap-10 border-t border-gray-100 pt-6 mt-auto">
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-emerald-600">{{ $benar }}</span>
                        <span class="text-[11px] font-medium text-gray-400 mt-1 uppercase tracking-wide">Benar</span>
                    </div>
                    <div class="w-px h-8 bg-gray-200"></div>
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-rose-600">{{ $salah }}</span>
                        <span class="text-[11px] font-medium text-gray-400 mt-1 uppercase tracking-wide">Salah</span>
                    </div>
                    <div class="w-px h-8 bg-gray-200"></div>
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-gray-400">{{ $tidakDijawab }}</span>
                        <span class="text-[11px] font-medium text-gray-400 mt-1 uppercase tracking-wide">Kosong</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Nilai per Capaian Pembelajaran (CPL) ── --}}
        @if(isset($cplStats) && $cplStats->isNotEmpty())
        @php
            $cplLabels = [];
            $cplScores = [];
            foreach($cplStats as $stat) {
                $cplLabels[] = $stat['kode'];
                $cplScores[] = $stat['benar']; // Score out of 10
            }
        @endphp
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm mb-8">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-[16px] font-semibold text-[#1e293b]">Nilai per Capaian Pembelajaran (CPL)</h3>
            </div>
            <div class="p-6">
                <div id="cplChart" class="w-full min-h-[300px]"></div>
            </div>
        </div>
        @endif

        {{-- ── Review Jawaban ── --}}
        <div x-data="{ viewMode: 'default' }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Review Jawaban</h2>
                
                <div class="flex items-center gap-5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    {{-- Toggle --}}
                    <div class="bg-gray-100 p-1 rounded-md inline-flex">
                        <button @click="viewMode = 'default'" 
                                :class="{'bg-white text-gray-800 shadow-sm border border-gray-200/60': viewMode === 'default', 'text-gray-500 hover:text-gray-700': viewMode !== 'default'}"
                                class="px-3 py-1.5 text-[12px] font-medium rounded-md transition-all">
                            Urut Nomor
                        </button>
                        <button @click="viewMode = 'cpl'" 
                                :class="{'bg-white text-gray-800 shadow-sm border border-gray-200/60': viewMode === 'cpl', 'text-gray-500 hover:text-gray-700': viewMode !== 'cpl'}"
                                class="px-3 py-1.5 text-[12px] font-medium rounded-md transition-all">
                            Kelompok per CPL
                        </button>
                    </div>

                    {{-- Legend --}}
                    <div class="hidden md:flex items-center gap-3 border-l border-gray-200 pl-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[12px] font-medium text-gray-500">Benar</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-[12px] font-medium text-gray-500">Salah</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            <span class="text-[12px] font-medium text-gray-500">Kosong</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1. Default View (Urut Nomor) --}}
            <div x-show="viewMode === 'default'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                @if($session->jawabans->isEmpty())
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium text-sm">Belum ada data jawaban tersimpan.</p>
                    </div>
                @else
                    <div>
                        @foreach($session->jawabans->sortBy('urutan_soal') as $kompreJawaban)
                            @include('banksoal::admin.cbt.partials._jawaban-card', ['kompreJawaban' => $kompreJawaban])
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 2. CPL View (Kelompok per CPL) --}}
            <div x-show="viewMode === 'cpl'" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                @if(!empty($jawabansPerCpl))
                <div class="space-y-8">
                    @foreach($jawabansPerCpl as $kodeCpl => $jawabansGroup)
                        <div>
                            <div class="flex items-center gap-3 mb-4 sticky top-16 bg-white/95 backdrop-blur-sm py-2 z-10 border-b border-gray-100">
                                <h3 class="text-[14px] font-bold text-gray-800">{{ $kodeCpl }}</h3>
                                <div class="h-px bg-gray-200 flex-1"></div>
                            </div>
                            <div>
                                @foreach($jawabansGroup as $kompreJawaban)
                                    @include('banksoal::admin.cbt.partials._jawaban-card', ['kompreJawaban' => $kompreJawaban, 'hideCplBadge' => true])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>

    @if(isset($cplStats) && $cplStats->isNotEmpty())
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                series: [{
                    name: 'Nilai CPL',
                    data: @json($cplScores)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                        horizontal: false,
                        columnWidth: '35%', 
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.25,
                        gradientToColors: ['#1e40af'], // Deep blue gradient bottom
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 0.85,
                        stops: [0, 100],
                    }
                },
                colors: ['#3b82f6'], // Primary blue color matching the image
                dataLabels: {
                    enabled: false, 
                },
                xaxis: {
                    categories: @json($cplLabels),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '13px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: {
                    min: 0,
                    max: 10,
                    tickAmount: 5,
                    labels: {
                        style: { colors: '#94a3b8' }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4, 
                    xaxis: {
                        lines: { show: true } 
                    },
                    yaxis: {
                        lines: { show: false }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return val + " / 10"
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#cplChart"), options);
            chart.render();
        });
    </script>
    @endpush
    @endif
</x-banksoal::layouts.admin>
