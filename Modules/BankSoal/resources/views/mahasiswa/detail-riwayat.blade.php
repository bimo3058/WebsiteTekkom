<x-banksoal::layouts.mahasiswa>
    @section('breadcrumbs')
        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
            class="text-slate-500 hover:text-primary transition-colors">Riwayat Ujian</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Detail Hasil</span>
    @endsection

    {{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
    <style>
        .sitkom-content {
            padding: 0 !important;
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        main.overflow-y-auto {
            overflow: hidden !important;
        }

        main.overflow-y-auto>div {
            padding: 0 !important;
            max-width: none !important;
            height: 100% !important;
            display: flex;
            flex-direction: column;
        }

        #banksoal-main-content {
            padding: 0 !important;
            max-width: 100% !important;
            height: 100% !important;
            display: flex;
            flex-direction: column;
        }

        .dash-wrap {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 16px;
            box-sizing: border-box;
            font-family: 'Inter Tight', sans-serif;
        }

        .dash-box {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }

        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
            padding: 16px 24px;
        }

        .dash-box-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dash-box-body::-webkit-scrollbar {
            width: 6px;
        }

        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

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
                position: sticky;
                top: 0;
                z-index: 20;
            }

            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    @php
        $isNoShow = $session->title === 'Tidak Mengerjakan';
        $totalSoal = $session->jawabans->count();
        $benar = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
        $salah = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
        $tidakDijawab = $totalSoal - ($benar + $salah);
        $skor = (int) ($session->score ?? 0);
        $lulus = $skor >= 60;
        $pct = min(100, $skor);
        $initial = strtoupper(substr($session->user->name ?? 'M', 0, 1));

        $durasiText = '—';
        if (!$isNoShow && $session->started_at && $session->finished_at) {
            $diff = \Carbon\Carbon::parse($session->started_at)->diff(\Carbon\Carbon::parse($session->finished_at));
            $durasiText = $diff->i . ' Menit ' . $diff->s . ' Detik';
        }
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">
            {{-- ── Header ── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1
                                style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">
                                Detail Hasil Ujian</h1>
                        </div>
                        <p style="font-size:13px; color:var(--c-fg-muted);">
                            {{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }} - Sesi
                            {{ $session->jadwal->nama_sesi ?? '-' }}
                        </p>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        {{-- Kembali button --}}
                        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                            style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:all .15s; white-space:nowrap; box-shadow:0 1px 2px rgba(0,0,0,.04);"
                            onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,.07)'"
                            onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'; this.style.boxShadow='0 1px 2px rgba(0,0,0,.04)'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="1.8" stroke-linecap="round">
                                <path d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="dash-box-body">
                {{-- ── Summary Panel (Stats Grid) ── --}}
                <div class="dash-stats"
                    style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;">
                    {{-- Score --}}
                    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                        onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                        onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <div
                                style="width:28px;height:28px;border-radius:8px;background:{{ $lulus ? 'rgba(34,197,94,0.1)' : 'rgba(220,38,38,0.1)' }};color:{{ $lulus ? '#22C55E' : '#DC2626' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    @if($lulus)
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @else
                                        <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @endif
                                </svg>
                            </div>
                            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Skor Akhir
                                ({{ $lulus ? 'Lulus' : 'Tidak Lulus' }})</p>
                        </div>
                        <p
                            style="font-size:24px;font-weight:700;color:{{ $lulus ? '#15803D' : '#B91C1C' }};line-height:1;letter-spacing:-.02em;">
                            {{ $skor }}<span style="font-size:14px;color:var(--c-fg-muted);font-weight:500;">/100</span>
                        </p>
                    </div>

                    {{-- Benar --}}
                    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                        onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                        onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <div
                                style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Jawaban Benar</p>
                        </div>
                        <p
                            style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                            {{ $benar }}
                        </p>
                    </div>

                    {{-- Salah --}}
                    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                        onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                        onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <div
                                style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Jawaban Salah</p>
                        </div>
                        <p
                            style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                            {{ $salah }}
                        </p>
                    </div>

                    {{-- Kosong --}}
                    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                        onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                        onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <div
                                style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 12H4" />
                                </svg>
                            </div>
                            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Tidak Dijawab</p>
                        </div>
                        <p
                            style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                            {{ $tidakDijawab }}
                        </p>
                    </div>

                    {{-- Durasi --}}
                    <div style="background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                        onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                        onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                            <div
                                style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Durasi Pengerjaan</p>
                        </div>
                        <p
                            style="font-size:15px;font-weight:700;color:var(--c-fg);line-height:1.5;letter-spacing:-.02em;">
                            {{ $durasiText }}
                        </p>
                    </div>
                </div>

                <style>
                    @media (max-width: 1280px) {
                        .dash-stats {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }

                    @media (max-width: 640px) {
                        .dash-stats {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                </style>

                {{-- ── Nilai per Capaian Pembelajaran (CPL) ── --}}
                @if(isset($cplStats) && $cplStats->isNotEmpty())
                    @php
                        $cplLabels = [];
                        $cplScores = [];
                        $cplTotal = 0;
                        foreach ($cplStats as $stat) {
                            $cplLabels[] = preg_replace('/^CPL-0*/', 'CPL ', $stat['kode']);
                            $cplScores[] = $stat['benar']; // Score out of 10
                            $cplTotal += $stat['benar'];
                        }
                        $cplCount = count($cplStats);
                        $cplAverage = $cplCount > 0 ? round($cplTotal / $cplCount, 1) : 0;
                    @endphp

                    <div class="flex flex-col lg:flex-row gap-6 mb-6">
                        {{-- 1. Chart Kiri --}}
                        <div class="w-full lg:w-1/2 flex flex-col"
                            style="min-width: 0; background:#fff; border:1px solid var(--c-border); border-radius:14px; box-shadow:var(--shadow-card); overflow:hidden;">
                            {{-- ── Header ─────────────────────────────────────────────────────── --}}
                            <div class="chart-header"
                                style="display:flex; align-items:flex-start; justify-content:space-between; padding:16px 20px 14px; border-bottom:1px solid var(--c-border); flex-wrap:wrap; gap:12px;">
                                <div>
                                    <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:0;">Grafik
                                        Nilai CPL</h3>
                                </div>
                            </div>

                            <style>
                                @media (max-width: 767px) {
                                    .chart-header {
                                        flex-direction: column !important;
                                        gap: 10px !important;
                                        padding: 12px 14px !important;
                                    }

                                    .chart-stats {
                                        display: flex !important;
                                        gap: 10px !important;
                                        width: 100%;
                                        justify-content: flex-start;
                                    }

                                    .chart-stat-divider {
                                        display: none !important;
                                    }

                                    .chart-stats>div {
                                        text-align: left !important;
                                    }

                                    .chart-stats p[style*="font-size:18px"] {
                                        font-size: 16px !important;
                                    }
                                }
                            </style>

                            {{-- ── Chart ───────────────────────────────────────────────────────── --}}
                            <div style="padding:16px 20px 8px;">
                                <div style="height:230px; width:100%; position:relative; min-width: 0;">
                                    <canvas id="cplChart"></canvas>
                                </div>
                            </div>

                            {{-- ── Legend ──────────────────────────────────────────────────────── --}}
                            <div
                                style="padding:10px 20px 16px; display:flex; align-items:center; gap:16px; border-top:1px solid var(--c-border); margin-top:auto;">
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <div style="width:14px; height:3px; background:#5E53F4; border-radius:2px;"></div>
                                    <span style="font-size:10px; color:var(--c-fg-muted);">Nilai CPL</span>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Daftar Keterangan CPL Kanan --}}
                        <div class="w-full lg:w-1/2 flex flex-col"
                            style="min-width: 0; background:#fff; border:1px solid var(--c-border); border-radius:14px; box-shadow:var(--shadow-card); overflow:hidden;">
                            <div
                                style="padding:16px 20px 14px; border-bottom:1px solid var(--c-border); display:flex; align-items:center; gap:8px;">

                                <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:0;">Deskripsi
                                    Indikator
                                    CPL</h3>
                            </div>
                            <div style="padding:0; overflow-y:auto; flex:1; max-height: 290px;">
                                <table style="width: 100%; text-align: left; border-collapse: collapse;">
                                    <thead style="position: sticky; top: 0; z-index: 1;">
                                        <tr>
                                            <th
                                                style="padding: 10px 20px; font-size: 11px; font-weight: 600; color: var(--c-fg-muted); text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid var(--c-border); background: var(--c-bg); width: 25%;">
                                                Kode</th>
                                            <th
                                                style="padding: 10px 20px; font-size: 11px; font-weight: 600; color: var(--c-fg-muted); text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid var(--c-border); background: var(--c-bg);">
                                                Deskripsi CPL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cplStats as $stat)
                                            <tr style="border-bottom: 1px solid var(--c-border);">
                                                <td
                                                    style="padding: 12px 20px; font-size: 12px; font-weight: 600; color: var(--c-fg); vertical-align: top;">
                                                    {{ preg_replace('/^CPL-0*/', 'CPL ', $stat['kode']) }}
                                                </td>
                                                <td
                                                    style="padding: 12px 20px; font-size: 12px; color: var(--c-fg-muted); line-height: 1.5; vertical-align: top;">
                                                    {{ $stat['deskripsi'] ?: '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif


            </div>

            @if(isset($cplStats) && $cplStats->isNotEmpty())
                @push('scripts')
                    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var canvas = document.getElementById('cplChart');

                            function initChart() {
                                if (!window.Chart || !canvas) return;
                                var ctx = canvas.getContext('2d');

                                const gradPrimary = ctx.createLinearGradient(0, 0, 0, 210);
                                gradPrimary.addColorStop(0, 'rgba(94,83,244,0.7)');
                                gradPrimary.addColorStop(1, 'rgba(94,83,244,0.05)');

                                const labels = @json($cplLabels);
                                const cplScores = @json($cplScores);

                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [
                                            {
                                                label: 'Nilai CPL',
                                                data: cplScores,
                                                backgroundColor: gradPrimary,
                                                borderColor: '#5E53F4',
                                                borderWidth: 1,
                                                borderRadius: 6,
                                                borderSkipped: 'bottom',
                                                barPercentage: 0.45,
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        interaction: { mode: 'index', intersect: false },
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                backgroundColor: '#1A1A2E',
                                                titleColor: '#9CA3AF',
                                                bodyColor: '#fff',
                                                titleFont: { size: 10, weight: '500' },
                                                bodyFont: { size: 12, weight: '600' },
                                                padding: 12,
                                                cornerRadius: 8,
                                                displayColors: true,
                                                boxWidth: 8,
                                                boxHeight: 8,
                                                callbacks: {
                                                    title: (items) => items[0]?.label ?? '',
                                                    label: (item) => `  ${item.raw} / 10`,
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                grid: { display: false },
                                                ticks: {
                                                    font: { size: 11, family: 'Inter, sans-serif' },
                                                    color: '#9CA3AF'
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                max: 10,
                                                border: { display: false },
                                                grid: { color: '#F3F4F6', drawTicks: false },
                                                ticks: {
                                                    font: { size: 11, family: 'Inter, sans-serif' },
                                                    color: '#9CA3AF',
                                                    stepSize: 1,
                                                    padding: 8
                                                },
                                            }
                                        }
                                    }
                                });
                            }

                            if ('IntersectionObserver' in window) {
                                new IntersectionObserver(function (entries, observer) {
                                    if (entries[0].isIntersecting) {
                                        observer.disconnect();
                                        if (window.Chart) {
                                            initChart();
                                        } else {
                                            document.querySelector('script[src*="chart.js"]')
                                                ?.addEventListener('load', initChart);
                                        }
                                    }
                                }, { threshold: 0.1 }).observe(canvas);
                            } else {
                                window.addEventListener('load', initChart);
                            }
                        });
                    </script>
                @endpush
            @endif
        </div> {{-- End dash-box-body --}}
    </div> {{-- End dash-box --}}
    </div> {{-- End dash-wrap --}}
</x-banksoal::layouts.mahasiswa>