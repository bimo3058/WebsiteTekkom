{{-- resources/views/superadmin/dashboard/_chart.blade.php --}}
@php
    // 1. Generate Label (Misal: 28 Apr, 29 Apr, ..., 04 Mei)
    $chartLabels = collect(range(6, 0))->map(fn($d) => now()->subDays($d)->translatedFormat('d M'))->values();

    // 2. Query Data Riil dari AuditLog
    // Mengambil data LOGIN 7 hari ke belakang dan mengelompokkannya per tanggal
    $logins = \App\Models\AuditLog::where('action', 'LOGIN')
        ->whereDate('created_at', '>=', now()->subDays(6)->toDateString())
        ->selectRaw('DATE(created_at) as date, count(*) as count')
        ->groupBy('date')
        ->pluck('count', 'date');

    // 3. Mapping data ke urutan array label
    // Jika pada tanggal tersebut tidak ada log login, beri nilai 0
    $loginData = collect(range(6, 0))->map(function($d) use ($logins) {
        $dateStr = now()->subDays($d)->toDateString();
        return $logins->get($dateStr, 0);
    })->values();
@endphp

<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; margin-bottom:20px; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
        <div>
            <h3 style="font-size:13px; font-weight:700; color:var(--c-fg);">Riwayat Akses Sistem</h3>
            <p style="font-size:11px; color:var(--c-fg-muted); margin-top:2px;">Statistik user yang login selama 7 hari terakhir</p>
        </div>
    </div>
    <div style="height:220px; width:100%; position:relative;">
        <canvas id="loginHistoryChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('loginHistoryChart').getContext('2d');
        
        // Gradient Fill
        let gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(11, 38, 110, 0.2)');
        gradient.addColorStop(1, 'rgba(11, 38, 110, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Login',
                    data: @json($loginData),
                    borderColor: '#0B266E',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0B266E',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0D0D12',
                        titleFont: { size: 11 },
                        bodyFont: { size: 12, weight: 'bold' },
                        padding: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#A4ABB8' }
                    },
                    y: {
                        border: { display: false },
                        grid: { color: '#F0F1F4', drawBorder: false },
                        ticks: { font: { size: 10 }, color: '#A4ABB8', stepSize: 20 }
                    }
                }
            }
        });
    });
</script>