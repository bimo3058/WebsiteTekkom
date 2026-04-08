<x-banksoal::layouts.dosen-master>

@include('banksoal::partials.dosen.sidebar', ['active' => 'home'])
@include('banksoal::partials.dosen.topbar')

    <!-- MAIN -->
    <main class="main">

    <!-- ALERT BANNER -->
    <div class="alert-banner">
        <div class="alert-icon-wrap"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="alert-text">
        <strong>Peringatan: Anda belum mengupload RPS Mata Kuliah {CS-201}.</strong>
        <p>Segera upload RPS sebelum tenggat waktu selesai!</p>
        </div>
        <button class="alert-btn">Upload Now</button>
    </div>

    <!-- TOP 3 CARDS -->
    <div class="grid-3">

        <!-- Question Analytics -->
        <div class="card">
        <div class="card-header">
            <span class="card-title">Question Analytics</span>
            <i class="fas fa-chart-pie card-icon"></i>
        </div>
        <div class="donut-wrap">
            <svg width="160" height="160" viewBox="0 0 160 160" id="donutChart" style="width:160px;height:160px;max-width:100%"></svg>
            <div class="donut-label">
            <strong>128</strong>
            <span>TOTAL</span>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item"><span class="legend-dot" style="background:#22C55E"></span> Approved: 75</div>
            <div class="legend-item"><span class="legend-dot" style="background:#F59E0B"></span> Review: 28</div>
            <div class="legend-item"><span class="legend-dot" style="background:#3B82F6"></span> Pending: 15</div>
            <div class="legend-item"><span class="legend-dot" style="background:#EF4444"></span> Rejected: 10</div>
        </div>
        </div>

        <!-- Academic Period -->
        <div class="card">
        <div class="card-header">
            <span class="card-title">Academic Period</span>
            <i class="fas fa-calendar card-icon"></i>
        </div>
        <div style="text-align:center;padding:8px 0">
            <div class="rps-badge">RPS: NOT UPLOADED</div>
            <div class="academic-year">Academic Year</div>
            <div class="year-text">Genap 2025/2026</div>
            <div class="courses-label">Active Courses</div>
            <div class="course-tags" style="justify-content:center">
            <span class="course-tag">CS-201</span>
            <span class="course-tag">CS-304</span>
            </div>
        </div>
        </div>

        <!-- Lecturer Profile -->
        <div class="card">
        <div class="card-header">
            <span class="card-title">Lecturer Profile</span>
            <i class="fas fa-shield-alt card-icon"></i>
        </div>
        <div style="text-align:center">
            <div class="prof-avatar-placeholder">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="prof-name">{{ auth()->user()->name }}</div>
            <div class="prof-nip">{{ auth()->user()->lecturer?->employee_number ?? auth()->user()->email }}</div>
            <div class="prof-dept">{{ auth()->user()->lecturer?->department ?? 'Teknik Komputer' }}</div>
            <button class="update-btn">Update Data</button>
        </div>
        </div>

    </div>

    <!-- BOTTOM 2 CARDS -->
    <div class="grid-2">

        <!-- CPL Distribution -->
        <div class="card">
        <div class="card-header">
            <div>
            <div class="card-title">Question Distribution per CPL</div>
            <div style="font-size:12px;color:var(--gray-400);margin-top:2px">Based on Learning Outcomes (CPL)</div>
            </div>
            <a href="#" class="card-link">Details <i class="fas fa-arrow-up-right-from-square" style="font-size:11px"></i></a>
        </div>
        <div class="bar-chart" id="cplChart"></div>
        </div>

        <!-- MK Distribution -->
        <div class="card">
        <div class="card-header">
            <div>
            <div class="card-title">Question Count per MK</div>
            <div style="font-size:12px;color:var(--gray-400);margin-top:2px">Distribution across active courses</div>
            </div>
            <a href="#" class="card-link">Details <i class="fas fa-arrow-up-right-from-square" style="font-size:11px"></i></a>
        </div>
        <div style="display:flex;align-items:flex-end;gap:32px;height:120px" id="mkChart"></div>
        </div>

    </div>
    </main>

    {{-- ═══ Dashboard Charts Component ═══ --}}
    <script src="{{ asset('modules/banksoal/js/Banksoal/components/DosenDashboard.js') }}"></script>

    @include('banksoal::partials.dosen.layout-scripts')

</x-banksoal::layouts.dosen-master>