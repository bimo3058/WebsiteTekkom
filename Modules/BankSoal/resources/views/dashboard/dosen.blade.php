<x-banksoal::layouts.master>
<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Manajemen Bank Soal & RPS</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('modules/banksoal/css/dashboard.css') }}">
    </head>
    <body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-university"></i></div>
        <div class="brand-text">
        <strong>Departemen Teknik Komputer</strong>
        <span>Universitas Wakamsi</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('banksoal.dashboard') }}" class="nav-item active"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Home</a>
        <a href="{{ route('banksoal.rps.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Manajemen RPS</a>
        <a href="{{ route('banksoal.soal.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-database"></i></span> Bank Soal</a>
        <a href="{{ route('banksoal.arsip.dosen.index') }}" class="nav-item"><span class="nav-icon"><i class="fas fa-archive"></i></span> Arsip Soal</a>
    
        <a href="#" class="nav-item" style="margin-top: auto; color: #EF4444;" 
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="nav-icon"><i class="fas fa-power-off"></i></span> Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden" style="display: none;">
            @csrf
        </form>
    </nav>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
    <button class="topbar-btn"><i class="fas fa-cog"></i></button>
    <button class="topbar-btn notif-btn"><i class="fas fa-bell"></i><span class="notif-dot"></span></button>
    <div class="user-chip">
        <div class="user-avatar-chip">A</div>
        <div class="user-info">
        <strong>Prof. Dr. Siti Rahayu</strong>
        <span>198503122010121001</span>
        </div>
    </div>
    </header>

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
            <svg width="160" height="160" viewBox="0 0 160 160" id="donutChart"></svg>
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
            <div class="prof-avatar-placeholder">A</div>
            <div class="prof-name">Prof. Dr. Aris S.</div>
            <div class="prof-nip">NIP: 198503122010121001</div>
            <div class="prof-dept">Computer Engineering</div>
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

    <script>
    // Donut chart
    (function(){
    const svg = document.getElementById('donutChart');
    const cx=80,cy=80,r=60,stroke=22;
    const circ = 2*Math.PI*r;
    const segments=[
        {value:75,color:'#22C55E'},
        {value:28,color:'#F59E0B'},
        {value:15,color:'#3B82F6'},
        {value:10,color:'#EF4444'},
    ];
    const total=128;
    let offset=0;
    segments.forEach(seg=>{
        const pct=seg.value/total;
        const dash=pct*circ;
        const gap=circ-dash;
        const rot=-90+(offset/total*360);
        const el=document.createElementNS('http://www.w3.org/2000/svg','circle');
        el.setAttribute('cx',cx);el.setAttribute('cy',cy);el.setAttribute('r',r);
        el.setAttribute('fill','none');el.setAttribute('stroke',seg.color);
        el.setAttribute('stroke-width',stroke);
        el.setAttribute('stroke-dasharray',`${dash.toFixed(2)} ${gap.toFixed(2)}`);
        el.setAttribute('stroke-dashoffset','0');
        el.setAttribute('transform',`rotate(${rot} ${cx} ${cy})`);
        svg.appendChild(el);
        offset+=seg.value;
    });
    const inner=document.createElementNS('http://www.w3.org/2000/svg','circle');
    inner.setAttribute('cx',cx);inner.setAttribute('cy',cy);inner.setAttribute('r',r-stroke/2-4);
    inner.setAttribute('fill','white');svg.appendChild(inner);
    })();

    // CPL bar chart
    (function(){
    const data={
        'CPL 01':45,'CPL 02':30,'CPL 03':60,'CPL 04':20,'CPL 05':38
    };
    const max=Math.max(...Object.values(data));
    const wrap=document.getElementById('cplChart');
    Object.entries(data).forEach(([label,val])=>{
        const h=Math.max(8,(val/max)*90);
        wrap.innerHTML+=`<div class="bar-group">
        <span class="bar-val">${val}</span>
        <div class="bar" style="height:${h}px"></div>
        <span class="bar-label">${label}</span>
        </div>`;
    });
    })();

    // MK bar chart
    (function(){
    const data=[
        {mk:'CS-201',count:54,color:'#22C55E'},
        {mk:'CS-304',count:32,color:'#22C55E'},
        {mk:'CS-401',count:0, color:'#CBD5E1'},
    ];
    const max=Math.max(...data.map(d=>d.count))||1;
    const wrap=document.getElementById('mkChart');
    data.forEach(d=>{
        const h=Math.max(4,(d.count/max)*80);
        const valColor=d.count>0?'#22C55E':'var(--gray-400)';
        wrap.innerHTML+=`<div style="display:flex;flex-direction:column;align-items:center;gap:6px">
        <span style="font-size:20px;font-weight:800;color:${valColor}">${d.count||''}</span>
        <div style="width:48px;border-radius:8px 8px 0 0;background:${d.color};height:${h}px"></div>
        <span style="font-size:12px;color:var(--gray-400);font-weight:500">${d.mk}</span>
        </div>`;
    });
    })();
    </script>
    </body>
    </html>
</x-banksoal::layouts.master>