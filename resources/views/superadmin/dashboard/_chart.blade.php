{{-- resources/views/superadmin/dashboard/_chart.blade.php --}}
@php
    use App\Models\AuditLog;

    $days = 30;

    // ── Query data login aktual dari audit_logs ──────────────────────────────
    // Filter module='auth' + action='LOGIN' agar tidak tercampur aksi lain
    $startDate = now()->subDays($days - 1)->startOfDay();

    $rawLogins = AuditLog::where('module', 'auth')
        ->where('action', 'LOGIN')
        ->where('created_at', '>=', $startDate)
        ->selectRaw("DATE(created_at) as date, COUNT(*) as total_login, COUNT(DISTINCT user_id) as unique_users")
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    // ── Build array 30 hari berurutan ────────────────────────────────────────
    $chartLabels    = [];
    $loginData      = [];
    $uniqueUserData = [];

    for ($i = $days - 1; $i >= 0; $i--) {
        $date  = now()->subDays($i)->toDateString();
        $label = now()->subDays($i)->translatedFormat('M d');

        $chartLabels[]    = $label;
        $loginData[]      = (int) ($rawLogins->get($date)?->total_login  ?? 0);
        $uniqueUserData[] = (int) ($rawLogins->get($date)?->unique_users ?? 0);
    }

    // ── Summary stats dari data aktual ───────────────────────────────────────
    $totalLoginMonth  = array_sum($loginData);
    $nonZeroDays      = collect($loginData)->filter(fn($v) => $v > 0);
    $avgLoginPerDay   = $nonZeroDays->count() > 0
                            ? round($nonZeroDays->avg(), 1)
                            : 0;
    $peakLogin        = !empty($loginData) ? max($loginData) : 0;
    $peakDate         = '';
    if ($peakLogin > 0) {
        $peakIdx  = array_search($peakLogin, $loginData);
        $peakDate = now()->subDays($days - 1 - $peakIdx)->translatedFormat('d M');
    }

    // Unique users total bulan ini (distinct query langsung)
    $totalUniqueUsers = AuditLog::where('module', 'auth')
        ->where('action', 'LOGIN')
        ->where('created_at', '>=', $startDate)
        ->distinct('user_id')
        ->count('user_id');

    // Hari ini vs kemarin (badge perubahan)
    $todayLogins     = AuditLog::where('module', 'auth')->where('action', 'LOGIN')
                            ->whereDate('created_at', today())->count();
    $yesterdayLogins = AuditLog::where('module', 'auth')->where('action', 'LOGIN')
                            ->whereDate('created_at', today()->subDay())->count();
    $dayChange       = $yesterdayLogins > 0
                            ? round((($todayLogins - $yesterdayLogins) / $yesterdayLogins) * 100, 1)
                            : ($todayLogins > 0 ? 100 : 0);

    $rangeStart = now()->subDays($days - 1)->translatedFormat('d M Y');
    $rangeEnd   = now()->translatedFormat('d M Y');
@endphp

<div style="width: 100%; min-width: 0; background:#fff; border:1px solid var(--c-border); border-radius:14px; margin-bottom:20px; box-shadow:var(--shadow-card); overflow:hidden;">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="chart-header" style="display:flex; align-items:flex-start; justify-content:space-between; padding:16px 20px 14px; border-bottom:1px solid var(--c-border); flex-wrap:wrap; gap:12px;">
        <div>
            <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:2px;">Riwayat Akses Sistem</h3>
            <p style="font-size:11px; color:var(--c-fg-muted);">
                Data login aktual dari audit logs · {{ $rangeStart }} – {{ $rangeEnd }}
            </p>
        </div>

        {{-- Summary stats --}}
        <div class="chart-stats" style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">

            {{-- Total login 30 hari --}}
            <div style="text-align:right;">
                <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg-muted); margin-bottom:2px;">30 Hari</p>
                <p style="font-size:18px; font-weight:700; color:var(--c-primary); letter-spacing:-0.02em; font-variant-numeric:tabular-nums; line-height:1;">
                    {{ number_format($totalLoginMonth) }}
                    <span style="font-size:10px; font-weight:500; color:var(--c-fg-muted);">login</span>
                </p>
            </div>

            <div class="chart-stat-divider" style="width:1px; height:28px; background:var(--c-border);"></div>

            {{-- Unique users --}}
            <div style="text-align:right;">
                <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg-muted); margin-bottom:2px;">Unique Users</p>
                <p style="font-size:18px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; font-variant-numeric:tabular-nums; line-height:1;">
                    {{ $totalUniqueUsers }}
                </p>
            </div>

            <div class="chart-stat-divider" style="width:1px; height:28px; background:var(--c-border);"></div>

            {{-- Peak --}}
            <div style="text-align:right;">
                <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg-muted); margin-bottom:2px;">
                    Puncak{{ $peakDate ? ' · ' . $peakDate : '' }}
                </p>
                <p style="font-size:18px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; font-variant-numeric:tabular-nums; line-height:1;">
                    {{ $peakLogin }}
                </p>
            </div>

            <div class="chart-stat-divider" style="width:1px; height:28px; background:var(--c-border);"></div>

            {{-- Hari ini + perubahan dari kemarin --}}
            <div style="text-align:right;">
                <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg-muted); margin-bottom:2px;">Hari Ini</p>
                <div style="display:flex; align-items:center; gap:5px; justify-content:flex-end;">
                    <p style="font-size:18px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; font-variant-numeric:tabular-nums; line-height:1;">
                        {{ $todayLogins }}
                    </p>
                    @if($yesterdayLogins > 0 || $todayLogins > 0)
                    <span style="font-size:9px; font-weight:700; padding:2px 5px; border-radius:4px; {{ $dayChange >= 0 ? 'background:#DCFCE7;color:#15803D;' : 'background:#FEE2E2;color:#B91C1C;' }}">
                        {{ $dayChange >= 0 ? '+' : '' }}{{ $dayChange }}%
                    </span>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
        @media (max-width: 767px) {
            /* Stack header secara vertikal di mobile */
            .chart-header { flex-direction: column !important; gap: 10px !important; padding: 12px 14px !important; }
            /* Stats: tampilkan sebagai 2x2 grid */
            .chart-stats {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
                width: 100%;
            }
            /* Sembunyikan divider vertikal */
            .chart-stat-divider { display: none !important; }
            /* Rata kiri di mobile */
            .chart-stats > div { text-align: left !important; }
            /* Nilai angka lebih kecil di mobile */
            .chart-stats p[style*="font-size:18px"] { font-size: 16px !important; }
        }
    </style>

    {{-- ── Chart ───────────────────────────────────────────────────────── --}}
    <div style="padding:16px 20px 8px;">
        <div style="height:230px; width:100%; position:relative; min-width: 0;"> 
            <canvas id="loginHistoryChart"></canvas>
        </div>
        {{-- Range label bawah --}}
        <div style="display:flex; justify-content:space-between; font-size:10px; color:var(--c-fg-placeholder); margin-top:6px; padding:0 2px;">
            <span>{{ $rangeStart }}</span>
            <span>Rata-rata {{ $avgLoginPerDay }} login/hari (hari aktif)</span>
            <span>{{ $rangeEnd }}</span>
        </div>
    </div>

    {{-- ── Legend ──────────────────────────────────────────────────────── --}}
    <div style="padding:10px 20px 16px; display:flex; align-items:center; gap:16px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:5px;">
            <div style="width:14px; height:3px; background:#5E53F4; border-radius:2px;"></div>
            <span style="font-size:10px; color:var(--c-fg-muted);">Total Login</span>
        </div>
        <div style="display:flex; align-items:center; gap:5px;">
            {{-- dashed line indicator --}}
            <svg width="14" height="4" viewBox="0 0 14 4"><line x1="0" y1="2" x2="14" y2="2" stroke="#22C55E" stroke-width="1.5" stroke-dasharray="3 2"/></svg>
            <span style="font-size:10px; color:var(--c-fg-muted);">Unique Users per Hari</span>
        </div>
        <div style="margin-left:auto; display:flex; align-items:center; gap:4px;">
            <span style="font-size:10px; color:var(--c-fg-placeholder);">Sumber: audit_logs (module=auth, action=LOGIN)</span>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var canvas = document.getElementById('loginHistoryChart');

    function initChart() {
        if (!window.Chart || !canvas) return;
        var ctx = canvas.getContext('2d');

    // Gradient fill
    const gradPrimary = ctx.createLinearGradient(0, 0, 0, 210);
    gradPrimary.addColorStop(0, 'rgba(94,83,244,0.18)');
    gradPrimary.addColorStop(1, 'rgba(94,83,244,0)');

    const labels     = @json($chartLabels);
    const loginData  = @json($loginData);
    const uniqueData = @json($uniqueUserData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Total Login',
                    data: loginData,
                    borderColor: '#5E53F4',
                    backgroundColor: gradPrimary,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#5E53F4',
                    pointHoverBorderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    order: 2,
                },
                {
                    label: 'Unique Users',
                    data: uniqueData,
                    borderColor: '#22C55E',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    borderDash: [4, 3],
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#22C55E',
                    pointHoverBorderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    order: 1,
                },
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
                        label: (item) => {
                            return item.datasetIndex === 0
                                ? `  ${item.raw} total login`
                                : `  ${item.raw} unique users`;
                        },
                    }
                }
            },
            scales: {
                x: {
                    display: false,
                    grid: { display: false },
                },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: { color: '#F3F4F6' },
                    ticks: {
                        font: { size: 10 },
                        color: '#9CA3AF',
                        maxTicksLimit: 5,
                        // Pastikan tick selalu integer
                        callback: (val) => Number.isInteger(val) ? val : null,
                    },
                }
            }
        }
    });
    }

    // Init hanya saat canvas masuk viewport (Intersection Observer)
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries, observer) {
            if (entries[0].isIntersecting) {
                observer.disconnect();
                // Chart.js mungkin belum selesai load karena defer — tunggu sebentar
                if (window.Chart) {
                    initChart();
                } else {
                    document.querySelector('script[src*="chart.js"]')
                        .addEventListener('load', initChart);
                }
            }
        }, { threshold: 0.1 }).observe(canvas);
    } else {
        // Fallback untuk browser lama
        window.addEventListener('load', initChart);
    }
}());
</script>