<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        /* Override main-wrapper to be transparent so cards sit on the gray bg */
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* ── Breadcrumb ──────────────────────────────────────────────────── */
        .breadcrumb-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .breadcrumb-bar .bc-path {
            font-size: 13px;
            color: #9ca3af;
        }
        .breadcrumb-bar .bc-path span {
            color: #374151;
            font-weight: 500;
        }
        .breadcrumb-bar .bc-actions {
            display: flex;
            gap: 8px;
        }

        /* ── Page Title ──────────────────────────────────────────────────── */
        .page-title {
            margin-bottom: 22px;
        }
        .page-title h1 {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 2px;
            letter-spacing: -0.02em;
        }
        .page-title p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }

        /* ── Stat Cards ──────────────────────────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 20px;
            border: 1px solid #e5e7eb;
        }
        .stat-card .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .stat-card .stat-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        .stat-card .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.02em;
            line-height: 1;
            margin-bottom: 8px;
        }
        .stat-card .stat-trend {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .stat-card .stat-trend.up   { color: #10b981; }
        .stat-card .stat-trend.down { color: #ef4444; }
        .stat-card .stat-trend .trend-label { color: #9ca3af; }

        /* ── Chart Row ───────────────────────────────────────────────────── */
        .chart-row {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 16px;
            margin-bottom: 20px;
        }
        .chart-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 20px;
        }
        .chart-card .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .chart-card .chart-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        /* donut legend */
        .donut-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 16px;
        }
        .donut-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #374151;
        }
        .donut-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            flex-shrink: 0;
        }
        .donut-total {
            text-align: center;
            margin-top: 8px;
        }
        .donut-total .total-num {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.02em;
            display: block;
        }
        .donut-total .total-label {
            font-size: 12px;
            color: #9ca3af;
        }

        /* ── Table Card ──────────────────────────────────────────────────── */
        .table-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 20px;
        }
        .table-card .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .table-card .table-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }
        .table-card .table-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-input {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 7px 12px 7px 32px;
            font-size: 13px;
            color: #374151;
            outline: none;
            width: 200px;
            background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center;
        }
        .search-input:focus { border-color: #4f46e5; }
        .btn-outline-sm {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: border-color 0.2s;
        }
        .btn-outline-sm:hover { border-color: #9ca3af; }
        .alumni-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .alumni-table th {
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            padding: 10px 14px;
            border-bottom: 1px solid #f3f4f6;
            background: #f9fafb;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .alumni-table th:first-child { border-radius: 8px 0 0 0; }
        .alumni-table th:last-child  { border-radius: 0 8px 0 0; }
        .alumni-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            vertical-align: middle;
        }
        .alumni-table tr:last-child td { border-bottom: none; }
        .alumni-table tr:hover td { background: #fafafa; }
        .alumni-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .alumni-name { font-weight: 600; color: #111827; font-size: 13px; }
        .alumni-nim  { font-size: 11px; color: #9ca3af; }
        .level-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .level-full-time  { background: #dbeafe; color: #1d4ed8; }
        .level-internship { background: #fef3c7; color: #92400e; }
        .level-part-time  { background: #d1fae5; color: #065f46; }
        .level-freelance  { background: #ede9fe; color: #5b21b6; }

    

        /* ── Period select ───────────────────────────────────────────────── */
        .period-select {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 12px;
            color: #374151;
            background: #fff;
            cursor: pointer;
        }

        .info-icon-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #9ca3af;
            font-size: 11px;
            font-weight: 700;
        }
    </style>
    @endpush

    {{-- ── Page Title ─────────────────────────────────────────────────── --}}
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Wadah komunikasi mahasiswa &amp; alumni</p>
    </div>

    {{-- ── Stat Cards ─────────────────────────────────────────────────── --}}
    <div class="stat-grid">

        {{-- Total Mahasiswa --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Mahasiswa</span>
                <div class="stat-icon" style="background:#ede9fe;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($snapshot['total_mahasiswa_aktif']) }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                <span>+8.2%</span>
                <span class="trend-label">vs last year</span>
            </div>
        </div>

        {{-- Serapan Kerja --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Serapan Kerja</span>
                <div class="stat-icon" style="background:#fef3c7;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </div>
            </div>
            @php
                $bekerja = ($snapshot['alumni_per_status']['bekerja'] ?? 0)
                         + ($snapshot['alumni_per_status']['wirausaha'] ?? 0);
            @endphp
            <div class="stat-value">{{ number_format($bekerja) }}</div>
            <div class="stat-trend down">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
                <span>-8.5%</span>
                <span class="trend-label">vs last year</span>
            </div>
        </div>

        {{-- Kegiatan Semester --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Kegiatan Semester</span>
                <div class="stat-icon" style="background:#d1fae5;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($snapshot['total_kegiatan']) }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                <span>+4.5%</span>
                <span class="trend-label">vs last year</span>
            </div>
        </div>

        {{-- Preview Mahasiswa (total alumni) --}}
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Preview Mahasiswa</span>
                <div class="stat-icon" style="background:#fee2e2;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($snapshot['total_alumni']) }}</div>
            <div class="stat-trend up">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
                <span>+3.9%</span>
                <span class="trend-label">vs last year</span>
            </div>
        </div>
    </div>

    {{-- ── Chart Row ───────────────────────────────────────────────────── --}}
    <div class="chart-row">

        {{-- Tren Mahasiswa / Semester --}}
        <div class="chart-card">
            <div class="chart-header">
                <span class="chart-title">Tren Mahasiswa / Semester</span>
                <select class="period-select" id="periodSelect" onchange="updateChart(this.value)">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <canvas id="trenChart" height="100"></canvas>
        </div>

        {{-- Status Mahasiswa --}}
        <div class="chart-card">
            <div class="chart-header">
                <span class="chart-title">Status Mahasiswa</span>
                <button class="info-icon-btn" title="Info">i</button>
            </div>
            <div style="position:relative; max-width: 200px; margin: 0 auto;">
                <canvas id="statusChart"></canvas>
                <div class="donut-total" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                    <span class="total-num">{{ number_format($statusMahasiswa['aktif'] + $statusMahasiswa['cuti'] + $statusMahasiswa['do'] + $statusMahasiswa['lulus']) }}</span>
                    <span class="total-label" style="font-size:11px;color:#9ca3af;display:block;">Total Employees</span>
                </div>
            </div>
            <div class="donut-legend">
                <div class="donut-legend-item">
                    <div style="display:flex;align-items:center;">
                        <span class="donut-legend-dot" style="background:#4f46e5;"></span>
                        Aktif
                    </div>
                    <span style="font-weight:600;">{{ number_format($statusMahasiswa['aktif']) }}</span>
                </div>
                <div class="donut-legend-item">
                    <div style="display:flex;align-items:center;">
                        <span class="donut-legend-dot" style="background:#f59e0b;"></span>
                        Cuti
                    </div>
                    <span style="font-weight:600;">{{ number_format($statusMahasiswa['cuti']) }}</span>
                </div>
                <div class="donut-legend-item">
                    <div style="display:flex;align-items:center;">
                        <span class="donut-legend-dot" style="background:#ef4444;"></span>
                        DO
                    </div>
                    <span style="font-weight:600;">{{ number_format($statusMahasiswa['do']) }}</span>
                </div>
                <div class="donut-legend-item">
                    <div style="display:flex;align-items:center;">
                        <span class="donut-legend-dot" style="background:#10b981;"></span>
                        Lulus
                    </div>
                    <span style="font-weight:600;">{{ number_format($statusMahasiswa['lulus']) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabel Serapan Alumni ────────────────────────────────────────── --}}
    <div class="table-card">
        <div class="table-header">
            <span class="table-title">List of Serapan Alumni</span>
            <div class="table-actions">
                <input type="text" class="search-input" placeholder="Search…" id="alumniSearch" oninput="filterTable()">
                <button class="btn-outline-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filter
                </button>
                <button class="btn-outline-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M7 12h10M11 18h2"/>
                    </svg>
                    Sort by
                </button>
            </div>
        </div>

        <table class="alumni-table" id="alumniTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee Name</th>
                    <th>Profesi</th>
                    <th>Status</th>
                    <th>Angkatan</th>
                    <th>Tahun Lulus</th>
                    <th>Level</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($serapanAlumni as $i => $alumni)
                @php
                    $name    = $alumni->user->name ?? 'Unknown';
                    $initials = strtoupper(substr($name, 0, 2));
                    $status  = $alumni->status_posisi_pekerjaan ?? '-';
                    $levelMap = [
                        'bekerja'       => ['label' => 'Full Time',  'class' => 'level-full-time'],
                        'wirausaha'     => ['label' => 'Freelance',  'class' => 'level-freelance'],
                        'studi_lanjut'  => ['label' => 'Internship', 'class' => 'level-internship'],
                        'belum_bekerja' => ['label' => 'Part Time',  'class' => 'level-part-time'],
                    ];
                    $level = $levelMap[$status] ?? ['label' => ucfirst($status), 'class' => 'level-part-time'];
                @endphp
                <tr>
                    <td>
                        <div class="alumni-avatar">{{ $initials }}</div>
                    </td>
                    <td>
                        <div class="alumni-name">{{ $name }}</div>
                        <div class="alumni-nim">{{ $alumni->user->email ?? '-' }}</div>
                    </td>
                    <td>{{ $alumni->profesi ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $status)) }}</td>
                    <td>{{ $alumni->angkatan ?? '-' }}</td>
                    <td>{{ $alumni->tahun_lulus ?? '-' }}</td>
                    <td>
                        <span class="level-badge {{ $level['class'] }}">{{ $level['label'] }}</span>
                    </td>
                    <td>
                        <button class="btn-outline-sm" style="padding:4px 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:#9ca3af;">
                        Belum ada data alumni terserapan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Data dari server ──────────────────────────────────────────────
        const angkatanLabels = @json(array_keys($snapshot['mahasiswa_per_angkatan']->toArray()));
        const angkatanData   = @json(array_values($snapshot['mahasiswa_per_angkatan']->toArray()));
        const kegiatanLabels = @json(array_keys($snapshot['kegiatan_per_bulan']->toArray()));
        const kegiatanData   = @json(array_values($snapshot['kegiatan_per_bulan']->toArray()));

        // ── Tren Chart (line) ─────────────────────────────────────────────
        const trenCtx = document.getElementById('trenChart').getContext('2d');
        const trenChart = new Chart(trenCtx, {
            type: 'line',
            data: {
                labels: angkatanLabels.length ? angkatanLabels : ['2019','2020','2021','2022','2023','2024'],
                datasets: [
                    {
                        label: 'Mahasiswa',
                        data: angkatanData.length ? angkatanData : [120,180,200,230,260,210],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.08)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        label: 'Kegiatan',
                        data: kegiatanData.length ? kegiatanData : [60,90,85,110,100,95],
                        borderColor: '#a5b4fc',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        fill: false,
                        borderDash: [5, 4],
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1f2937',
                        titleColor: '#f9fafb',
                        bodyColor: '#d1d5db',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 11 } }
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: { color: '#9ca3af', font: { size: 11 } }
                    }
                }
            }
        });

        function updateChart(period) {
            if (period === 'yearly') {
                trenChart.data.labels = angkatanLabels.length ? angkatanLabels : ['2019','2020','2021','2022','2023','2024'];
                trenChart.data.datasets[0].data = angkatanData.length ? angkatanData : [120,180,200,230,260,210];
            } else {
                trenChart.data.labels = kegiatanLabels.length ? kegiatanLabels : ['Jan','Feb','Mar','Apr','May','Jun'];
                trenChart.data.datasets[0].data = kegiatanData.length ? kegiatanData : [60,90,85,110,100,95];
            }
            trenChart.update();
        }

        // ── Status Donut Chart ────────────────────────────────────────────
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Cuti', 'DO', 'Lulus'],
                datasets: [{
                    data: [
                        {{ $statusMahasiswa['aktif'] }},
                        {{ $statusMahasiswa['cuti'] }},
                        {{ $statusMahasiswa['do'] }},
                        {{ $statusMahasiswa['lulus'] }},
                    ],
                    backgroundColor: ['#4f46e5', '#f59e0b', '#ef4444', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f9fafb',
                        bodyColor: '#d1d5db',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                hover: {
                    mode: 'nearest',
                    animationDuration: 0
                },
                animation: {
                    duration: 800
                }
            }
        });

        // ── Live search tabel ─────────────────────────────────────────────
        function filterTable() {
            const q   = document.getElementById('alumniSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#alumniTable tbody tr');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
