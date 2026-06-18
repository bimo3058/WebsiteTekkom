<x-manajemenmahasiswa::layouts.admin>

@push('styles')
<style>
    .main-wrapper { background:transparent !important; box-shadow:none !important; padding:0 !important; }

    /* Wadah section dashboard — urutan diatur per-scope via CSS order (lihat blok PHP $isGpm) */
    .da-sections { display:flex; flex-direction:column; }
    .da-section { display:block; }

    /* ─── Header ─────────────────────────────────────────── */
    .da-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px; }
    .da-header h4 { font-size:1.45rem; font-weight:800; color:#1e1b4b; margin-bottom:2px; letter-spacing:-.02em; }
    .da-header-meta { display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:4px; }
    .da-tier-badge {
        display:inline-flex; align-items:center; gap:5px; padding:3px 10px;
        border-radius:50px; font-size:.72rem; font-weight:700; letter-spacing:.02em;
    }

    .da-refresh-btn {
        display:inline-flex; align-items:center; gap:6px; padding:9px 18px;
        border:1px solid #e5e7eb; border-radius:10px; background:#fff;
        color:#6b7280; font-size:.85rem; font-weight:600; cursor:pointer;
        transition:all .2s; text-decoration:none; flex-shrink:0;
    }
    .da-refresh-btn:hover { border-color:#293C79; color:#293C79; background:#E7E8F0; }

    /* ─── Section Label ──────────────────────────────────── */
    .section-header {
        display:flex; align-items:center; gap:10px; margin-bottom:14px;
    }
    .section-label {
        font-size:.8rem; font-weight:700; color:#9ca3af;
        text-transform:uppercase; letter-spacing:.07em;
    }
    .section-line { flex:1; height:1px; background:#f3f4f6; }

    /* ─── Tier 1: Action Cards ───────────────────────────── */
    .action-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-bottom:28px; }
    @media(max-width:1100px){ .action-grid { grid-template-columns:repeat(3,1fr); } }
    @media(max-width:640px) { .action-grid { grid-template-columns:1fr 1fr; } }

    .action-card {
        background:#fff; border-radius:14px; border:1px solid;
        padding:16px 18px; display:flex; flex-direction:column; gap:10px;
        text-decoration:none; transition:all .2s; cursor:pointer; position:relative;
        overflow:hidden;
    }
    .action-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
    .action-card:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.08); }

    .action-card-red    { border-color:#fecaca; background:#fff; }
    .action-card-red::before    { background:#ef4444; }
    .action-card-orange { border-color:#fed7aa; background:#fff; }
    .action-card-orange::before { background:#f97316; }
    .action-card-blue   { border-color:#bfdbfe; background:#fff; }
    .action-card-blue::before   { background:#3b82f6; }
    .action-card-purple { border-color:#CED4E0; background:#fff; }
    .action-card-purple::before { background:#6F7DA4; }
    .action-card-amber  { border-color:#fde68a; background:#fff; }
    .action-card-amber::before  { background:#f59e0b; }
    .action-card-green  { border-color:#bbf7d0; background:#fff; }
    .action-card-green::before  { background:#10b981; }

    .action-card-top { display:flex; align-items:center; justify-content:space-between; }
    .action-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .action-icon-red    { background:#fef2f2; color:#dc2626; }
    .action-icon-orange { background:#fff7ed; color:#ea580c; }
    .action-icon-blue   { background:#eff6ff; color:#2563eb; }
    .action-icon-purple { background:#E7E8F0; color:#415086; }
    .action-icon-amber  { background:#fffbeb; color:#d97706; }
    .action-icon-green  { background:#ecfdf5; color:#059669; }

    .action-num { font-size:2rem; font-weight:900; line-height:1; }
    .action-num-red    { color:#dc2626; }
    .action-num-orange { color:#ea580c; }
    .action-num-blue   { color:#2563eb; }
    .action-num-purple { color:#415086; }
    .action-num-amber  { color:#d97706; }
    .action-num-green  { color:#059669; }

    .action-label { font-size:.82rem; color:#6b7280; font-weight:500; line-height:1.4; }
    .action-link-hint { font-size:.72rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }
    .action-card.zero-state .action-num { color:#d1d5db; }
    .action-card.zero-state { opacity:.7; }

    /* ─── Tier 2: Activity Stats + Chart ────────────────── */
    .activity-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px; }
    @media(max-width:900px){ .activity-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:480px){ .activity-grid { grid-template-columns:1fr; } }

    .stat-card {
        background:#fff; border:1px solid #e5e7eb; border-radius:14px;
        padding:18px 20px; display:flex; align-items:center; gap:14px;
        transition:box-shadow .2s;
    }
    .stat-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.06); }
    .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-icon-blue   { background:#eff6ff; color:#2563eb; }
    .stat-icon-green  { background:#ecfdf5; color:#059669; }
    .stat-icon-purple { background:#E7E8F0; color:#415086; }
    .stat-icon-amber  { background:#fffbeb; color:#d97706; }
    .stat-value { font-size:1.6rem; font-weight:800; color:#1e1b4b; line-height:1; margin-bottom:2px; }
    .stat-label { font-size:.8rem; color:#9ca3af; font-weight:500; }
    .stat-sub   { font-size:.72rem; color:#d1d5db; margin-top:2px; }

    /* ─── Tier 3 & 4: KPI Mini Cards ────────────────────── */
    .kpi-row { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px; }
    @media(max-width:900px){ .kpi-row { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:480px){ .kpi-row { grid-template-columns:1fr; } }
    .kpi-mini {
        background:#fff; border:1px solid #e5e7eb; border-radius:12px;
        padding:14px 16px; display:flex; align-items:center; gap:12px;
    }
    .kpi-mini-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .kpi-mini-val { font-size:1.4rem; font-weight:800; color:#1e1b4b; line-height:1; margin-bottom:1px; }
    .kpi-mini-label { font-size:.78rem; color:#9ca3af; font-weight:500; }

    /* ─── Chart Grid ─────────────────────────────────────── */
    .chart-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px; }
    @media(max-width:768px){ .chart-grid-2 { grid-template-columns:1fr; } }
    .chart-card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:22px 24px; }
    .chart-title { font-size:.9rem; font-weight:700; color:#1e1b4b; margin-bottom:16px; display:flex; align-items:center; gap:7px; }
    .chart-title svg { color:#293C79; flex-shrink:0; }
    .chart-title-right { margin-left:auto; font-size:.75rem; color:#9ca3af; font-weight:500; }
    .chart-wrap { position:relative; height:220px; }
    .chart-wrap-sm { position:relative; height:170px; }

    /* ─── Donut + Legend ─────────────────────────────────── */
    .donut-row { display:flex; align-items:center; gap:18px; }
    .donut-canvas { flex:0 0 150px; height:150px; position:relative; }
    .donut-legend { flex:1; display:flex; flex-direction:column; gap:7px; }
    .legend-item { display:flex; align-items:center; gap:8px; font-size:.82rem; color:#4b5563; }
    .legend-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
    .legend-val { margin-left:auto; font-weight:700; color:#1e1b4b; font-size:.84rem; }

    /* ─── Progress Bar Card ──────────────────────────────── */
    .progress-card { display:flex; flex-direction:column; gap:13px; }
    .progress-item {}
    .progress-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
    .progress-label { font-size:.84rem; font-weight:600; color:#374151; }
    .progress-count { font-size:.84rem; font-weight:700; }
    .progress-bar-bg { height:6px; background:#f3f4f6; border-radius:50px; overflow:hidden; }
    .progress-bar-fill { height:100%; border-radius:50px; }

    /* ─── Table ──────────────────────────────────────────── */
    .table-card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:22px 24px; margin-bottom:18px; overflow-x:auto; }
    .table-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
    .table-title { font-size:.9rem; font-weight:700; color:#1e1b4b; display:flex; align-items:center; gap:7px; }
    .table-title svg { color:#293C79; }
    .table-link { font-size:.8rem; font-weight:600; color:#293C79; text-decoration:none; }
    .table-link:hover { text-decoration:underline; }
    .da-table { width:100%; border-collapse:collapse; }
    .da-table th { font-size:.73rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; padding:9px 12px; text-align:left; border-bottom:1px solid #f3f4f6; white-space:nowrap; }
    .da-table td { padding:11px 12px; border-bottom:1px solid #f9fafb; font-size:.87rem; color:#374151; vertical-align:middle; }
    .da-table tr:last-child td { border-bottom:none; }
    .da-table tr:hover td { background:#fafafa; }

    /* ─── Misc ───────────────────────────────────────────── */
    .badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:50px; font-size:.73rem; font-weight:600; }
    .badge-aktif     { background:#dcfce7; color:#15803d; }
    .badge-bekerja   { background:#d1fae5; color:#065f46; }
    .badge-wirausaha { background:#e0f2fe; color:#0369a1; }
    .badge-studi     { background:#ede9fe; color:#5b21b6; }
    .badge-belum     { background:#f3f4f6; color:#6b7280; }
    .avatar-sm { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#293C79,#415086); color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .empty-row td { text-align:center; color:#9ca3af; padding:28px; font-size:.87rem; }
    .section-gap { margin-bottom:28px; }

    /* ─── Dashboard Modal ────────────────────────────────── */
    .dm-overlay {
        position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:9000;
        display:flex; align-items:center; justify-content:center; padding:20px;
        opacity:0; visibility:hidden; transition:opacity .2s, visibility .2s;
    }
    .dm-overlay.open { opacity:1; visibility:visible; }
    .dm-box {
        background:#fff; border-radius:18px; width:100%; max-width:720px;
        max-height:88vh; display:flex; flex-direction:column;
        box-shadow:0 24px 60px rgba(0,0,0,.18);
        transform:translateY(12px) scale(.98); transition:transform .22s;
    }
    .dm-overlay.open .dm-box { transform:translateY(0) scale(1); }
    .dm-head {
        display:flex; align-items:center; gap:12px; padding:18px 22px;
        border-bottom:1px solid #f3f4f6; flex-shrink:0;
    }
    .dm-head h5 { font-size:1rem; font-weight:700; color:#1e1b4b; margin:0; flex:1; }
    .dm-badge { font-size:.75rem; font-weight:600; color:#9ca3af; background:#f3f4f6; padding:3px 10px; border-radius:50px; flex-shrink:0; }
    .dm-close {
        width:30px; height:30px; border-radius:50%; border:none; background:#f3f4f6;
        color:#6b7280; font-size:1.1rem; display:flex; align-items:center; justify-content:center;
        cursor:pointer; flex-shrink:0; transition:all .15s;
    }
    .dm-close:hover { background:#e5e7eb; color:#1e1b4b; }
    .dm-toolbar {
        padding:12px 22px; border-bottom:1px solid #f3f4f6; flex-shrink:0;
        display:flex; flex-wrap:wrap; gap:10px; align-items:center;
    }
    .dm-search {
        flex:1; min-width:180px; padding:8px 14px 8px 36px; border:1px solid #e5e7eb;
        border-radius:10px; font-size:.85rem; color:#374151; background:#fafafa;
        outline:none; transition:all .2s; position:relative;
    }
    .dm-search:focus { border-color:#293C79; background:#fff; box-shadow:0 0 0 3px rgba(41,60,121,.1); }
    .dm-search-wrap { position:relative; flex:1; min-width:180px; }
    .dm-search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }
    .dm-filter-chips { display:flex; flex-wrap:wrap; gap:5px; }
    .dm-chip {
        padding:4px 12px; border-radius:50px; border:1.5px solid #e5e7eb;
        background:#fff; color:#6b7280; font-size:.78rem; font-weight:600;
        cursor:pointer; transition:all .15s;
    }
    .dm-chip:hover { border-color:#293C79; color:#293C79; background:#E7E8F0; }
    .dm-chip.active { border-color:#293C79; background:#293C79; color:#fff; }
    .dm-body { overflow-y:auto; flex:1; }
    .dm-table { width:100%; border-collapse:collapse; }
    .dm-table th { position:sticky; top:0; background:#fafafa; font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; padding:10px 22px; text-align:left; border-bottom:1px solid #f3f4f6; white-space:nowrap; z-index:1; }
    .dm-table td { padding:11px 22px; border-bottom:1px solid #f9fafb; font-size:.87rem; color:#374151; vertical-align:middle; }
    .dm-table tr:last-child td { border-bottom:none; }
    .dm-table tr:hover td { background:#fafafa; }
    .dm-empty { text-align:center; padding:48px 20px; color:#9ca3af; font-size:.88rem; }
    .dm-footer { padding:12px 22px; border-top:1px solid #f3f4f6; font-size:.8rem; color:#9ca3af; flex-shrink:0; display:flex; align-items:center; gap:8px; }
    .dm-loading { display:flex; align-items:center; justify-content:center; padding:48px; gap:12px; color:#9ca3af; font-size:.88rem; }
    @keyframes dm-spin { to { transform:rotate(360deg); } }
    .dm-spinner { width:22px; height:22px; border:2.5px solid #e5e7eb; border-top-color:#293C79; border-radius:50%; animation:dm-spin .7s linear infinite; }

    /* Clickable rows */
    .dm-table tr.dm-row-link { cursor:pointer; }
    .dm-table tr.dm-row-link:hover td { background:#E7E8F0 !important; }
    .dm-table tr.dm-row-link:hover td.dm-arrow-cell { color:#293C79; }
    .dm-arrow-cell { width:28px; text-align:right; color:#d1d5db; transition:color .15s; font-size:.9rem; padding-right:16px !important; }

    /* Clickable card indicator */
    .card-clickable { cursor:pointer; }
    .card-clickable:hover { box-shadow:0 6px 20px rgba(41,60,121,.12); border-color:#9FA6C1 !important; transform:translateY(-2px); }
</style>
@endpush

@php
    // Section yang aktif untuk scope role ini
    $sections = $dashboard['sections'] ?? [];
    $hasSection = fn ($key) => in_array($key, $sections, true);

    // Scope GPM/Ketua Departemen — hanya scope ini yang punya metrik evaluasi mutu.
    // Dipakai untuk urutan section (FASE 2A) serta chart/tren khusus GPM (2C/2D).
    $isGpm = in_array('evaluasi_mutu', $sections, true);
    $isDpm = ($dashboard['scope'] ?? null) === 'dpm';
    $canAccessVerifikasi = !$isGpm;
    $useBarDistribusiMahasiswa = $isGpm || $isDpm || $hasSection('admin_operasional');

    // Akses data null-safe — sebagian scope (mis. DPM) tidak punya semua section
    $act   = $dashboard['action_items'] ?? [];   // Tier 1
    $ops   = $dashboard['admin_operasional'] ?? []; // Admin operations
    $acty  = $dashboard['activity']     ?? [];    // Tier 2
    $eval  = $dashboard['evaluasi']     ?? [];    // Metrik Evaluasi Mutu (GPM)
    $dpmHimpunan    = $dashboard['dpm_himpunan'] ?? [];
    $dpmProker      = $dashboard['dpm_proker'] ?? [];
    $dpmPelaksanaan = $dashboard['dpm_pelaksanaan'] ?? [];
    $dpmPartisipasi = $dashboard['dpm_partisipasi'] ?? [];
    $dpmReward      = $dashboard['dpm_prestasi_reward'] ?? [];
    $mhs   = $dashboard['mahasiswa']    ?? [];    // Tier 3
    $alm   = $dashboard['alumni']       ?? [];    // Tier 4
    $cdo   = $dashboard['calon_do']     ?? [];    // Calon DO
    $lulus = $dashboard['lulusan']      ?? [];    // Lulusan per periode
    $genAt = $dashboard['generated_at'];

    $tingkatLabels = ['internasional'=>'Internasional','nasional'=>'Nasional','regional'=>'Regional','universitas'=>'Universitas','prodi'=>'Prodi'];
    $karirLabels   = ['bekerja'=>'Bekerja','wirausaha'=>'Wirausaha','studi_lanjut'=>'Studi Lanjut','belum_bekerja'=>'Belum Terdata','belum_terdata'=>'Belum Terdata'];
    $industryLabels = \Modules\ManajemenMahasiswa\Models\Alumni::BIDANG_INDUSTRI_LIST;

    // Serapan kerja total dari terdata (hanya jika section alumni ada)
    $totalBekerja = ($alm['per_status_karir']['bekerja'] ?? 0) + ($alm['per_status_karir']['wirausaha'] ?? 0);
    $pctSerapan   = ($alm['total_terdata'] ?? 0) > 0 ? round($totalBekerja / $alm['total_terdata'] * 100) : 0;

    // Total mahasiswa semua status
    $totalSemuaMhs = ($mhs['total_aktif'] ?? 0) + ($mhs['total_cuti'] ?? 0) + ($mhs['total_do'] ?? 0) + ($mhs['total_pindah'] ?? 0) + ($mhs['total_alumni_status'] ?? 0) + ($mhs['total_wafat'] ?? 0) + ($mhs['total_mangkir'] ?? 0);
@endphp

{{-- ─── Page Header ─────────────────────────────────────────────── --}}
<div class="da-header">
    <div>
        <h4>Dashboard Analitik</h4>
        <div class="da-header-meta">
            <span style="font-size:.82rem;color:#9ca3af;">Diperbarui: {{ $genAt->format('d M Y, H:i') }} WIB</span>
        </div>
    </div>
    <a href="{{ route('manajemenmahasiswa.dashboard') }}" class="da-refresh-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
        Refresh Data
    </a>
</div>

{{-- Wadah semua section. Urutan visual diatur via CSS `order` di tiap .da-section
     sehingga GPM/Kadep memakai urutan evaluasi, sedangkan admin/dpm tetap urutan semula. --}}
<div class="da-sections">

{{-- Dashboard DPM - Pembinaan himpunan dan kegiatan mahasiswa --}}
@if($hasSection('dpm_himpunan'))
<div class="da-section" style="order:5;">
<div class="section-header section-gap">
    <span class="section-label">Ringkasan Pembinaan Himpunan</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $dpmHimpunanKpis = [
            ['label'=>'Periode Aktif', 'val'=>$dpmHimpunan['periode_aktif'] ?? '-', 'bg'=>'#E7E8F0', 'color'=>'#293C79'],
            ['label'=>'Total Pengurus', 'val'=>number_format($dpmHimpunan['total_pengurus'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
            ['label'=>$dpmHimpunan['kpi_3_label'] ?? 'Pengurus Aktif', 'val'=>number_format($dpmHimpunan['kpi_3_value'] ?? ($dpmHimpunan['total_aktif'] ?? 0)), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
            ['label'=>$dpmHimpunan['kpi_4_label'] ?? 'Nonaktif / Cuti', 'val'=>number_format($dpmHimpunan['kpi_4_value'] ?? (($dpmHimpunan['total_nonaktif'] ?? 0) + ($dpmHimpunan['total_cuti'] ?? 0))), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
        ];
    @endphp
    @foreach($dpmHimpunanKpis as $k)
        <div class="kpi-mini">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
            {{ $dpmHimpunan['composition_label'] ?? 'Komposisi Pengurus per Divisi' }}
            <span class="chart-title-right">Periode {{ $dpmHimpunan['periode_aktif'] ?? '-' }}</span>
        </div>
        @php $maxDivisiDpm = max(1, (int) (collect($dpmHimpunan['per_divisi'] ?? [])->max() ?? 0)); @endphp
        @if(!empty($dpmHimpunan['per_divisi']))
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($dpmHimpunan['per_divisi'] as $divisi => $total)
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-size:.82rem;font-weight:600;color:#374151;width:150px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $divisi }}</span>
                        <div style="flex:1;height:22px;background:#f3f4f6;border-radius:6px;overflow:hidden;">
                            <div style="height:100%;width:{{ round($total / $maxDivisiDpm * 100) }}%;background:linear-gradient(90deg,#293C79,#415086);border-radius:6px;min-width:24px;"></div>
                        </div>
                        <span style="font-size:.82rem;font-weight:700;color:#1e1b4b;width:32px;text-align:right;flex-shrink:0;">{{ $total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:.85rem;">Belum ada data pengurus himpunan</div>
        @endif
    </div>

    <div class="chart-card" style="overflow-x:auto;">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/users-01.svg'))) !!}</span>
            Pengurus Inti
        </div>
        <table class="da-table">
            <thead><tr><th>Nama</th><th>NIM</th><th>Jabatan</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($dpmHimpunan['pengurus_inti'] ?? [] as $pengurus)
                    @php
                        $namaPengurus = $pengurus['nama'] ?? 'Mahasiswa';
                        $statusPengurus = $pengurus['status'] ?? '-';
                        $statusPengurusKey = strtolower($statusPengurus);
                    @endphp
                    <tr>
                        <td><div style="display:flex;align-items:center;gap:9px;"><div class="avatar-sm">{{ strtoupper(substr($namaPengurus,0,2)) }}</div><span style="font-weight:600;color:#1e1b4b;">{{ $namaPengurus }}</span></div></td>
                        <td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">{{ $pengurus['nim'] ?? '-' }}</td>
                        <td>{{ $pengurus['jabatan'] ?? '-' }}</td>
                        <td><span class="badge {{ $statusPengurusKey === 'aktif' ? 'badge-bekerja' : 'badge-belum' }}">{{ ucfirst($statusPengurus) }}</span></td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">Belum ada pengurus inti</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endif

@if($hasSection('dpm_proker'))
<div class="da-section" style="order:4;">
<div class="section-header section-gap">
    <span class="section-label">Status Program Kerja Himpunan</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $dpmProkerKpis = [
            ['label'=>'Total Proker', 'val'=>number_format($dpmProker['total'] ?? 0), 'bg'=>'#E7E8F0', 'color'=>'#293C79'],
            ['label'=>'Draft', 'val'=>number_format($dpmProker['total_draft'] ?? 0), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
            ['label'=>'Pelaksanaan', 'val'=>number_format($dpmProker['total_pelaksanaan'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
            ['label'=>'Selesai', 'val'=>number_format($dpmProker['total_selesai'] ?? 0), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
        ];
    @endphp
    @foreach($dpmProkerKpis as $k)
        <div class="kpi-mini">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-12.svg'))) !!}</span>
            Sebaran Proker per Bidang
            <span class="chart-title-right">{{ number_format($dpmProker['tahun_ini'] ?? 0) }} tahun ini</span>
        </div>
        @php $maxBidangDpm = max(1, (int) (collect($dpmProker['per_bidang'] ?? [])->max() ?? 0)); @endphp
        @if(!empty($dpmProker['per_bidang']))
            <div class="progress-card">
                @foreach($dpmProker['per_bidang'] as $bidang => $total)
                    @php $pctBidangDpm = round($total / $maxBidangDpm * 100); @endphp
                    <div class="progress-item">
                        <div class="progress-row">
                            <span class="progress-label">{{ $bidang }}</span>
                            <span class="progress-count" style="color:#293C79;">{{ $total }}</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width:{{ $pctBidangDpm }}%;background:#293C79;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:.85rem;">Belum ada bidang proker</div>
        @endif
    </div>

    <div class="chart-card" style="overflow-x:auto;">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/file-01.svg'))) !!}</span>
            Draft Proker Terbaru
        </div>
        <table class="da-table">
            <thead><tr><th>Kegiatan</th><th>Ketua</th><th>Kategori</th><th>Dibuat</th></tr></thead>
            <tbody>
                @forelse($dpmProker['proker_terbaru'] ?? [] as $proker)
                    @php
                        $ketuaProker = $proker->ketuaPelaksana?->user?->name ?? $proker->ketuaPelaksana?->name ?? '-';
                        $kategoriProker = $proker->kategoris?->pluck('nama_kategori')->filter()->take(2)->implode(', ');
                    @endphp
                    <tr>
                        <td style="font-weight:600;color:#1e1b4b;">{{ $proker->nama_kegiatan ?? '-' }}</td>
                        <td>{{ $ketuaProker }}</td>
                        <td>{{ $kategoriProker ?: '-' }}</td>
                        <td style="color:#9ca3af;">{{ optional($proker->created_at)->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">Tidak ada draft proker</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════════
     🔴 TIER 1 — BUTUH TINDAKAN SEGERA (cache 30 detik)
     Data yang paling sering berubah — admin/GPM harus tangani segera.
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('dpm_pelaksanaan'))
<div class="da-section" style="order:3;">
<div class="section-header section-gap">
    <span class="section-label">Pelaksanaan dan Realisasi Kegiatan</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $dpmPelaksanaanKpis = [
            ['label'=>'Bulan Ini', 'val'=>number_format($dpmPelaksanaan['bulan_ini'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
            ['label'=>'Berlangsung Hari Ini', 'val'=>number_format($dpmPelaksanaan['berlangsung_hari_ini'] ?? 0), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
            ['label'=>'Belum Realisasi', 'val'=>number_format($dpmPelaksanaan['belum_realisasi'] ?? 0), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
            ['label'=>'Tanpa Dosen Pendamping', 'val'=>number_format($dpmPelaksanaan['tanpa_dosen_pendamping'] ?? 0), 'bg'=>'#fef2f2', 'color'=>'#dc2626'],
        ];
    @endphp
    @foreach($dpmPelaksanaanKpis as $k)
        <div class="kpi-mini">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-12.svg'))) !!}</span>
            Rencana vs Realisasi
        </div>
        <div class="progress-card">
            <div class="progress-item">
                <div class="progress-row">
                    <span class="progress-label">Peserta</span>
                    <span class="progress-count" style="color:#2563eb;">{{ number_format($dpmPelaksanaan['realisasi_peserta'] ?? 0) }} / {{ number_format($dpmPelaksanaan['target_peserta'] ?? 0) }} ({{ $dpmPelaksanaan['rate_peserta'] ?? 0 }}%)</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ min(100, (int) ($dpmPelaksanaan['rate_peserta'] ?? 0)) }}%;background:#2563eb;"></div>
                </div>
            </div>
            <div class="progress-item">
                <div class="progress-row">
                    <span class="progress-label">Anggaran</span>
                    <span class="progress-count" style="color:#059669;">Rp {{ number_format($dpmPelaksanaan['anggaran_realisasi'] ?? 0, 0, ',', '.') }} / Rp {{ number_format($dpmPelaksanaan['anggaran_rencana'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width:{{ min(100, (int) ($dpmPelaksanaan['rate_anggaran'] ?? 0)) }}%;background:#059669;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card" style="overflow-x:auto;">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/calendar.svg'))) !!}</span>
            Jadwal Pelaksanaan Mendatang
        </div>
        <table class="da-table">
            <thead><tr><th>Kegiatan</th><th>Tanggal</th><th>Dosen Pendamping</th></tr></thead>
            <tbody>
                @forelse($dpmPelaksanaan['pelaksanaan_mendatang'] ?? [] as $kegiatan)
                    @php
                        $tanggalMulaiDpm = $kegiatan->tanggal_mulai ? \Illuminate\Support\Carbon::parse($kegiatan->tanggal_mulai)->format('d M Y') : '-';
                        $dosenDpm = $kegiatan->dosenPendamping?->user?->name ?? $kegiatan->dosenPendamping?->name ?? '-';
                    @endphp
                    <tr>
                        <td style="font-weight:600;color:#1e1b4b;">{{ $kegiatan->nama_kegiatan ?? '-' }}</td>
                        <td style="color:#9ca3af;">{{ $tanggalMulaiDpm }}</td>
                        <td>{{ $dosenDpm }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Tidak ada jadwal pelaksanaan mendatang</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endif

@if($hasSection('dpm_partisipasi'))
<div class="da-section" style="order:6;">
<div class="section-header section-gap">
    <span class="section-label">Partisipasi Mahasiswa</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $dpmPartisipasiKpis = [
            ['label'=>'Mahasiswa Terlibat', 'val'=>number_format($dpmPartisipasi['mahasiswa_terlibat'] ?? 0), 'bg'=>'#E7E8F0', 'color'=>'#293C79'],
            ['label'=>'Total Riwayat', 'val'=>number_format($dpmPartisipasi['total_riwayat'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
            ['label'=>'Riwayat Disetujui', 'val'=>number_format($dpmPartisipasi['riwayat_approved'] ?? 0), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
            ['label'=>'Menunggu Verifikasi', 'val'=>number_format($dpmPartisipasi['riwayat_pending'] ?? 0), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
        ];
    @endphp
    @foreach($dpmPartisipasiKpis as $k)
        <div class="kpi-mini">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5 1.34 3.5 3 3.5Z"/><path d="M8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11Z"/><path d="M2 20c.7-3.2 3-5 6-5"/><path d="M22 20c-.7-3.2-3-5-6-5"/><path d="M8 15c1.5 1 6.5 1 8 0"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
            Riwayat Kegiatan per Angkatan
            <span class="chart-title-right">{{ number_format($dpmPartisipasi['total_panitia'] ?? 0) }} panitia</span>
        </div>
        @php $maxAngkatanDpm = max(1, (int) (collect($dpmPartisipasi['per_angkatan'] ?? [])->max() ?? 0)); @endphp
        @if(!empty($dpmPartisipasi['per_angkatan']))
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($dpmPartisipasi['per_angkatan'] as $angkatan => $total)
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:.8rem;font-weight:700;color:#374151;width:48px;flex-shrink:0;">{{ $angkatan }}</span>
                        <div style="flex:1;height:22px;background:#f3f4f6;border-radius:6px;overflow:hidden;">
                            <div style="height:100%;width:{{ round($total / $maxAngkatanDpm * 100) }}%;background:linear-gradient(90deg,#293C79,#415086);border-radius:6px;min-width:24px;"></div>
                        </div>
                        <span style="font-size:.82rem;font-weight:700;color:#1e1b4b;width:36px;text-align:right;flex-shrink:0;">{{ $total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:.85rem;">Belum ada riwayat kegiatan mahasiswa</div>
        @endif
    </div>

    <div class="chart-card" style="overflow-x:auto;">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
            Mahasiswa Paling Aktif
        </div>
        <table class="da-table">
            <thead><tr><th>Mahasiswa</th><th>NIM</th><th>Riwayat</th></tr></thead>
            <tbody>
                @forelse($dpmPartisipasi['top_mahasiswa'] ?? [] as $row)
                    @php
                        $student = $row->student;
                        $namaAktif = $student?->user?->name ?? $student?->name ?? 'Mahasiswa';
                    @endphp
                    <tr>
                        <td><div style="display:flex;align-items:center;gap:9px;"><div class="avatar-sm">{{ strtoupper(substr($namaAktif,0,2)) }}</div><span style="font-weight:600;color:#1e1b4b;">{{ $namaAktif }}</span></div></td>
                        <td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">{{ $student?->student_number ?? '-' }}</td>
                        <td style="font-weight:700;color:#293C79;">{{ number_format($row->total ?? 0) }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Belum ada partisipasi terdata</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endif

@if($hasSection('dpm_prestasi_reward'))
<div class="da-section" style="order:2;">
<div class="section-header section-gap">
    <span class="section-label">Prestasi dan Reward Mahasiswa</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $dpmRewardKpis = [
            ['label'=>'Prestasi Disetujui', 'val'=>number_format($dpmReward['prestasi_approved'] ?? 0), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
            ['label'=>'Prestasi Pending', 'val'=>number_format($dpmReward['prestasi_pending'] ?? 0), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
            ['label'=>'Reward Diajukan', 'val'=>number_format($dpmReward['reward_diajukan'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
            ['label'=>'Reward Disetujui', 'val'=>number_format($dpmReward['reward_disetujui'] ?? 0), 'bg'=>'#E7E8F0', 'color'=>'#293C79'],
        ];
    @endphp
    @foreach($dpmRewardKpis as $k)
        <div class="kpi-mini">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-12.svg'))) !!}</span>
            Usulan Reward Aktif
        </div>
        <div class="kpi-row" style="grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:0;">
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#eff6ff;color:#2563eb;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div>
                    <div class="kpi-mini-val" style="color:#2563eb;">{{ number_format($dpmReward['reward_mk_diajukan'] ?? 0) }}</div>
                    <div class="kpi-mini-label">Mata Kuliah Diajukan</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon" style="background:#ecfdf5;color:#059669;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="kpi-mini-val" style="color:#059669;">{{ number_format($dpmReward['reward_sks_diajukan'] ?? 0) }}</div>
                    <div class="kpi-mini-label">SKS Diajukan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-card" style="overflow-x:auto;">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
            Reward Menunggu Tindak Lanjut
        </div>
        <table class="da-table">
            <thead><tr><th>Mahasiswa</th><th>Prestasi</th><th>Reward</th></tr></thead>
            <tbody>
                @forelse($dpmReward['reward_terbaru'] ?? [] as $prestasi)
                    @php $namaPrestasi = $prestasi->kemahasiswaan?->user?->name ?? $prestasi->kemahasiswaan?->nama ?? 'Mahasiswa'; @endphp
                    <tr>
                        <td><div style="display:flex;align-items:center;gap:9px;"><div class="avatar-sm">{{ strtoupper(substr($namaPrestasi,0,2)) }}</div><span style="font-weight:600;color:#1e1b4b;">{{ $namaPrestasi }}</span></div></td>
                        <td>{{ $prestasi->nama_prestasi ?? '-' }}</td>
                        <td style="color:#293C79;font-weight:700;">{{ (int) ($prestasi->reward_jml_mk_max ?? 0) }} MK / {{ (int) ($prestasi->reward_sks_max ?? 0) }} SKS</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Tidak ada reward yang sedang diajukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endif

@if($hasSection('action_items'))
<div class="da-section" style="order:1;">
<div class="section-header section-gap">
    <span class="section-label">Butuh Tindakan Segera</span>
    <div class="section-line"></div>
</div>

<div class="action-grid">

    {{-- Pengaduan Baru --}}
    <a href="{{ route('manajemenmahasiswa.pengaduan.index') }}"
       class="action-card action-card-red {{ $act['pengaduan_baru'] === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-red">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/headphone-01.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-red">{{ $act['pengaduan_baru'] }}</div>
        <div>
            <div class="action-label">Pengaduan belum dijawab</div>
            <div class="action-link-hint">Jawab sekarang →</div>
        </div>
    </a>

    {{-- Verifikasi Kegiatan Pending --}}
    <a href="{{ route('manajemenmahasiswa.verifikasi.index') }}"
       class="action-card action-card-orange {{ $act['verif_kegiatan'] === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-orange">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/check-square.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-orange">{{ $act['verif_kegiatan'] }}</div>
        <div>
            <div class="action-label">Verifikasi kegiatan pending</div>
            <div class="action-link-hint">Review sekarang →</div>
        </div>
    </a>

    {{-- Verifikasi Prestasi Pending --}}
    <a href="{{ route('manajemenmahasiswa.verifikasi.index') }}"
       class="action-card action-card-amber {{ $act['verif_prestasi'] === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-amber">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-amber">{{ $act['verif_prestasi'] }}</div>
        <div>
            <div class="action-label">Verifikasi prestasi pending</div>
            <div class="action-link-hint">Review sekarang →</div>
        </div>
    </a>

    {{-- Laporan Forum --}}
    <a href="{{ route('manajemenmahasiswa.forum.index') }}"
       class="action-card action-card-purple {{ $act['laporan_forum'] === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-purple">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/alert-circle.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-purple">{{ $act['laporan_forum'] }}</div>
        <div>
            <div class="action-label">Laporan forum belum ditangani</div>
            <div class="action-link-hint">Moderasi sekarang →</div>
        </div>
    </a>

    {{-- Pengumuman Pending Verifikasi --}}
    <a href="{{ route('manajemenmahasiswa.pengumuman.verifikasi.index') }}"
       class="action-card action-card-blue {{ $act['pengumuman_pending'] === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-blue">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/announcement-01.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-blue">{{ $act['pengumuman_pending'] }}</div>
        <div>
            <div class="action-label">Pengumuman menunggu verifikasi</div>
            <div class="action-link-hint">Verifikasi sekarang →</div>
        </div>
    </a>

    {{-- Klaim Reward Pending --}}
    <a href="{{ route('manajemenmahasiswa.verifikasi.reward.index') }}"
       class="action-card action-card-green {{ ($act['reward_pending'] ?? 0) === 0 ? 'zero-state' : '' }}">
        <div class="action-card-top">
            <div class="action-icon action-icon-green">
                <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
            </div>
        </div>
        <div class="action-num action-num-green">{{ $act['reward_pending'] ?? 0 }}</div>
        <div>
            <div class="action-label">Klaim reward menunggu persetujuan</div>
            <div class="action-link-hint">Review reward â†’</div>
        </div>
    </a>

</div>
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════════
     🟠 TIER 2 — AKTIVITAS TERKINI (cache 60 detik)
     Berubah beberapa kali sehari: kegiatan, pengumuman, forum.
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('admin_operasional'))
<div class="da-section" style="order:2;">
<div class="section-header section-gap">
    <span class="section-label">Monitoring Operasional Admin</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $adminKpis = [
            ['label'=>'Pengaduan Lewat SLA', 'val'=>number_format($ops['pengaduan_sla_lewat'] ?? 0), 'bg'=>'#fef2f2', 'color'=>'#dc2626'],
            ['label'=>'Reward Pending', 'val'=>number_format($ops['reward_pending'] ?? 0), 'bg'=>'#ecfdf5', 'color'=>'#059669'],
            ['label'=>'Kegiatan Tanpa Dosen', 'val'=>number_format($ops['kegiatan_tanpa_dosen'] ?? 0), 'bg'=>'#fff7ed', 'color'=>'#ea580c'],
            ['label'=>'Lulusan Belum Sinkron', 'val'=>number_format($ops['lulusan_belum_sinkron'] ?? 0), 'bg'=>'#eff6ff', 'color'=>'#2563eb'],
        ];
    @endphp
    @foreach($adminKpis as $k)
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
            <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/check-square.svg'))) !!}</span>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
            <div class="kpi-mini-label">{{ $k['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-top:18px;">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/headphone-01.svg'))) !!}</span>
            Layanan Pengaduan
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px;margin-bottom:16px;">
            @foreach([
                ['label'=>'Baru', 'val'=>$ops['pengaduan_baru'] ?? 0, 'color'=>'#dc2626'],
                ['label'=>'Dibaca', 'val'=>$ops['pengaduan_dibaca'] ?? 0, 'color'=>'#2563eb'],
                ['label'=>'Didelegasikan', 'val'=>$ops['pengaduan_didelegasikan'] ?? 0, 'color'=>'#d97706'],
                ['label'=>'Selesai', 'val'=>$ops['pengaduan_selesai'] ?? 0, 'color'=>'#059669'],
            ] as $row)
            <div style="padding:12px;border:1px solid #eef2f7;border-radius:12px;background:#fff;">
                <div style="font-size:1.25rem;font-weight:800;color:{{ $row['color'] }};">{{ number_format($row['val']) }}</div>
                <div style="font-size:.75rem;color:#6b7280;font-weight:600;">{{ $row['label'] }}</div>
            </div>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;font-size:.82rem;">
            <span style="font-weight:700;color:#1e1b4b;">Responsivitas Pengaduan</span>
            <span style="font-weight:800;color:#059669;">{{ $ops['responsivitas_pengaduan'] ?? 0 }}%</span>
        </div>
        <div style="height:7px;background:#f3f4f6;border-radius:999px;overflow:hidden;">
            <div style="width:{{ min(100, $ops['responsivitas_pengaduan'] ?? 0) }}%;height:100%;background:#059669;border-radius:999px;"></div>
        </div>
        <div style="margin-top:10px;font-size:.72rem;color:#9ca3af;">
            SLA {{ $ops['sla_hari'] ?? 7 }} hari, {{ number_format($ops['pengaduan_belum_dibaca'] ?? 0) }} pengaduan belum dibaca.
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/check-square.svg'))) !!}</span>
            Verifikasi & Approval
        </div>
        <table class="da-table">
            <thead><tr><th>Jenis</th><th style="text-align:right;">Pending</th></tr></thead>
            <tbody>
                @foreach([
                    'Riwayat Kegiatan' => $ops['verif_riwayat_pending'] ?? 0,
                    'Prestasi' => $ops['verif_prestasi_pending'] ?? 0,
                    'Reward Prestasi' => $ops['reward_pending'] ?? 0,
                    'Pengumuman' => $ops['pengumuman_pending'] ?? 0,
                    'Laporan Forum' => $ops['forum_report_pending'] ?? 0,
                ] as $label => $val)
                <tr>
                    <td style="font-weight:600;color:#1e1b4b;">{{ $label }}</td>
                    <td style="text-align:right;font-weight:800;color:{{ $val > 0 ? '#dc2626' : '#059669' }};">{{ number_format($val) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:10px;font-size:.72rem;color:#9ca3af;">
            Potensi reward pending: {{ number_format($ops['reward_mk_pending'] ?? 0) }} MK / {{ number_format($ops['reward_sks_pending'] ?? 0) }} SKS.
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-top:18px;">
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/calendar.svg'))) !!}</span>
            Realisasi Kegiatan
        </div>
        @php
            $pesertaPct = min(100, $ops['persen_realisasi_peserta'] ?? 0);
            $anggaranPct = min(100, $ops['persen_realisasi_anggaran'] ?? 0);
        @endphp
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;margin-bottom:16px;">
            @foreach([
                ['label'=>'Draft', 'val'=>$ops['kegiatan_draft'] ?? 0],
                ['label'=>'Pelaksanaan', 'val'=>$ops['kegiatan_pelaksanaan'] ?? 0],
                ['label'=>'Selesai', 'val'=>$ops['kegiatan_selesai'] ?? 0],
            ] as $row)
            <div style="padding:12px;border:1px solid #eef2f7;border-radius:12px;background:#fff;">
                <div style="font-size:1.25rem;font-weight:800;color:#293C79;">{{ number_format($row['val']) }}</div>
                <div style="font-size:.75rem;color:#6b7280;font-weight:600;">{{ $row['label'] }}</div>
            </div>
            @endforeach
        </div>
        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:.82rem;margin-bottom:6px;"><strong>Peserta</strong><span>{{ number_format($ops['realisasi_peserta'] ?? 0) }} / {{ number_format($ops['target_peserta'] ?? 0) }} ({{ $ops['persen_realisasi_peserta'] ?? 0 }}%)</span></div>
        <div style="height:7px;background:#f3f4f6;border-radius:999px;overflow:hidden;margin-bottom:12px;"><div style="width:{{ $pesertaPct }}%;height:100%;background:#2563eb;border-radius:999px;"></div></div>
        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:.82rem;margin-bottom:6px;"><strong>Anggaran</strong><span>Rp {{ number_format($ops['anggaran_realisasi'] ?? 0, 0, ',', '.') }} / Rp {{ number_format($ops['anggaran_rencana'] ?? 0, 0, ',', '.') }}</span></div>
        <div style="height:7px;background:#f3f4f6;border-radius:999px;overflow:hidden;"><div style="width:{{ $anggaranPct }}%;height:100%;background:#059669;border-radius:999px;"></div></div>
        <div style="margin-top:10px;font-size:.72rem;color:#9ca3af;">
            {{ number_format($ops['kegiatan_selesai_belum_realisasi'] ?? 0) }} kegiatan selesai belum lengkap realisasinya.
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
            Kelengkapan Data Modul
        </div>
        <table class="da-table">
            <thead><tr><th>Data</th><th style="text-align:right;">Perlu Dilengkapi</th></tr></thead>
            <tbody>
                @foreach([
                    'Mahasiswa tanpa NIM' => $ops['mahasiswa_tanpa_nim'] ?? 0,
                    'Mahasiswa tanpa angkatan' => $ops['mahasiswa_tanpa_angkatan'] ?? 0,
                    'Lulusan belum sinkron alumni' => $ops['lulusan_belum_sinkron'] ?? 0,
                    'Alumni belum mengisi status karir' => $ops['alumni_belum_terdata'] ?? 0,
                ] as $label => $val)
                <tr>
                    <td style="font-weight:600;color:#1e1b4b;">{{ $label }}</td>
                    <td style="text-align:right;font-weight:800;color:{{ $val > 0 ? '#d97706' : '#059669' }};">{{ number_format($val) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
@endif

@if($hasSection('activity'))
<div class="da-section" style="order:{{ $isGpm ? 6 : ($hasSection('admin_operasional') ? 3 : 2) }};">
<div class="section-header section-gap">
    <span class="section-label">Aktivitas Terkini</span>
    <div class="section-line"></div>
</div>

<div class="activity-grid">
    {{-- Kegiatan Bulan Ini --}}
    <div class="stat-card card-clickable" onclick="openDashModal('kegiatan',{},'Kegiatan Bulan Ini')">
        <div class="stat-icon stat-icon-green">
            <span style="display:inline-flex;width:20px;height:20px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/calendar.svg'))) !!}</span>
        </div>
        <div>
            <div class="stat-value">{{ number_format($acty['kegiatan_bulan_ini']) }}</div>
            <div class="stat-label">Kegiatan Bulan Ini</div>
            <div class="stat-sub">Ditambahkan {{ now()->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- Pengumuman Bulan Ini --}}
    <div class="stat-card card-clickable" onclick="openDashModal('pengumuman',{},'Pengumuman Bulan Ini')">
        <div class="stat-icon stat-icon-purple">
            <span style="display:inline-flex;width:20px;height:20px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/announcement-01.svg'))) !!}</span>
        </div>
        <div>
            <div class="stat-value">{{ number_format($acty['pengumuman_bulan_ini']) }}</div>
            <div class="stat-label">Pengumuman Bulan Ini</div>
            <div class="stat-sub">Dipublish {{ now()->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- Kegiatan Berlangsung Hari Ini — monitoring real-time organisasi --}}
    <div class="stat-card {{ $acty['kegiatan_berlangsung'] > 0 ? 'card-clickable' : '' }}"
         @if($acty['kegiatan_berlangsung'] > 0)
             onclick="window.location.href='{{ route('manajemenmahasiswa.kegiatan.index') }}'"
         @endif>
        <div class="stat-icon stat-icon-amber">
            <span style="display:inline-flex;width:20px;height:20px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/clock-02.svg'))) !!}</span>
        </div>
        <div>
            <div class="stat-value" style="{{ $acty['kegiatan_berlangsung'] > 0 ? 'color:#d97706;' : '' }}">
                {{ number_format($acty['kegiatan_berlangsung']) }}
            </div>
            <div class="stat-label">Kegiatan Berlangsung</div>
            <div class="stat-sub">Aktif hari ini</div>
        </div>
    </div>
</div>

<div class="chart-card section-gap">
    <div class="chart-title">
        <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
        {{ $isGpm ? 'Tren Pelaksanaan Kegiatan (6 Bulan Terakhir)' : 'Tren Kegiatan Ditambahkan (6 Bulan Terakhir)' }}
    </div>
    <div class="chart-wrap"><canvas id="chartKegiatanTrend"></canvas></div>
</div>
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════════
     📊 METRIK EVALUASI MUTU (GPM & Ketua Departemen)
     Indikator kualitas penyelenggaraan prodi untuk evaluasi.
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('evaluasi_mutu') && !empty($eval))
<div class="da-section" style="order:1;">
<div class="section-header section-gap">
    <span class="section-label">Metrik Evaluasi Mutu</span>
    <div class="section-line"></div>
</div>

@php
    $almTotal      = $eval['total_alumni'] ?? 0;
    $almTerdata    = $eval['total_terdata'] ?? 0;
    $minSampel     = $eval['min_sampel'] ?? 3;
    $nMasaStudi    = $eval['sample_masa_studi'] ?? 0;
    $nWaktuTunggu  = $eval['sample_waktu_tunggu'] ?? 0;
    // Teks abu-abu "Data belum cukup" untuk metrik dengan sampel di bawah ambang
    $dataKurang    = '<span style="font-size:1.05rem;font-weight:700;color:#9ca3af;">Data belum cukup</span>';

    // Badge status terhadap target mutu (FASE 3A): tercapai/tidak/kurang + label target
    $qTargets = $eval['targets'] ?? [];
    $targetBadge = function ($key) use ($qTargets) {
        $t = $qTargets[$key] ?? null;
        if (!$t) return '';
        $map = [
            'tercapai' => ['bg'=>'#ecfdf5','color'=>'#059669','txt'=>'Tercapai'],
            'tidak'    => ['bg'=>'#fef2f2','color'=>'#dc2626','txt'=>'Tidak tercapai'],
            'kurang'   => ['bg'=>'#f3f4f6','color'=>'#9ca3af','txt'=>'Data belum cukup'],
        ];
        $c = $map[$t['status']] ?? $map['kurang'];
        return '<div style="margin-top:6px;display:flex;flex-direction:column;gap:2px;">'
            . '<span style="display:inline-block;width:fit-content;padding:1px 8px;border-radius:50px;font-size:.66rem;font-weight:700;background:'.$c['bg'].';color:'.$c['color'].';">'.$c['txt'].'</span>'
            . '<span style="font-size:.64rem;color:#9ca3af;">'.$t['label'].'</span>'
            . '</div>';
    };
@endphp
<div class="kpi-row" style="grid-template-columns:repeat(3,1fr);margin-bottom:18px;">
    {{-- Rata-rata Masa Studi --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#eff6ff;color:#2563eb;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.42a12 12 0 0 1 .84 4.42 12 12 0 0 1-7 1 12 12 0 0 1-7-1 12 12 0 0 1 .84-4.42L12 14z"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#2563eb;">
                @if($nMasaStudi < $minSampel){!! $dataKurang !!}@else{{ $eval['rata_masa_studi'] }} <span style="font-size:.8rem;font-weight:600;">thn</span>@endif
            </div>
            <div class="kpi-mini-label">Rata-rata Masa Studi</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">dari {{ $nMasaStudi }} lulusan</div>
            {!! $targetBadge('masa_studi') !!}
        </div>
    </div>
    {{-- Rata-rata Waktu Tunggu Kerja --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#fffbeb;color:#d97706;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#d97706;">
                @if($nWaktuTunggu < $minSampel){!! $dataKurang !!}@else{{ $eval['rata_waktu_tunggu'] }} <span style="font-size:.8rem;font-weight:600;">thn</span>@endif
            </div>
            <div class="kpi-mini-label">Waktu Tunggu Kerja</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">{{ $nWaktuTunggu }} dari {{ $almTotal }} alumni terdata</div>
            {!! $targetBadge('waktu_tunggu_kerja') !!}
        </div>
    </div>
    {{-- Serapan Kerja --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#ecfdf5;color:#059669;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#059669;">
                @if($almTerdata < $minSampel){!! $dataKurang !!}@else{{ $eval['serapan_kerja'] }}%@endif
            </div>
            <div class="kpi-mini-label">Serapan Kerja Alumni</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">{{ $almTerdata }} dari {{ $almTotal }} alumni terdata</div>
            {!! $targetBadge('serapan_kerja') !!}
        </div>
    </div>
    {{-- Kelulusan Tepat Waktu ≤ 4 thn (FASE 3A) --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#fff7ed;color:#ea580c;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#ea580c;">
                @if($nMasaStudi < $minSampel){!! $dataKurang !!}@else{{ $eval['kelulusan_tepat_waktu'] ?? 0 }}%@endif
            </div>
            <div class="kpi-mini-label">Kelulusan Tepat Waktu</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">lulus ≤ 4 thn · dari {{ $nMasaStudi }} lulusan</div>
            {!! $targetBadge('kelulusan_tepat_waktu') !!}
        </div>
    </div>
    {{-- Kelengkapan Data Alumni (FASE 1A) --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#eef2ff;color:#4f46e5;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#4f46e5;">{{ $eval['kelengkapan_data_alumni'] ?? 0 }}%</div>
            <div class="kpi-mini-label">Kelengkapan Data Alumni</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">{{ $almTerdata }} dari {{ $almTotal }} mengisi data karir</div>
        </div>
    </div>
    {{-- Responsivitas Pengaduan --}}
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#fdf4ff;color:#a855f7;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#a855f7;">{{ $eval['responsivitas'] }}%</div>
            <div class="kpi-mini-label" title="Persentase pengaduan (non-draft) yang dijawab dalam ≤ {{ $eval['sla_hari'] ?? 7 }} hari sejak dibuat.">Responsivitas Pengaduan (SLA)</div>
            <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">{{ $eval['pengaduan_sla_terpenuhi'] ?? 0 }} dari {{ $eval['pengaduan_total'] ?? 0 }} dijawab ≤ {{ $eval['sla_hari'] ?? 7 }} hari</div>
        </div>
    </div>
</div>

{{-- Tabel kelulusan & DO rate per angkatan --}}
<div class="chart-card section-gap">
    <div class="chart-title">
        <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
        Tingkat Kelulusan &amp; Drop Out per Angkatan
    </div>
    <div style="overflow-x:auto;">
        <table class="da-table" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">Angkatan</th>
                    <th style="text-align:center;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">Total</th>
                    <th style="text-align:center;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">Lulus</th>
                    <th style="text-align:center;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">% Kelulusan</th>
                    <th style="text-align:center;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">Drop Out</th>
                    <th style="text-align:center;font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;padding:8px 12px;border-bottom:1px solid #f3f4f6;">% DO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eval['kelulusan_per_angkatan'] as $angkatan => $row)
                    <tr style="border-bottom:1px solid #f9fafb;">
                        <td style="padding:9px 12px;font-size:.85rem;font-weight:700;color:#1e1b4b;">{{ $angkatan }}</td>
                        <td style="padding:9px 12px;text-align:center;font-size:.85rem;color:#374151;">{{ $row['total'] }}</td>
                        <td style="padding:9px 12px;text-align:center;font-size:.85rem;color:#059669;font-weight:600;">{{ $row['lulus'] }}</td>
                        <td style="padding:9px 12px;text-align:center;">
                            @if(empty($row['jatuh_tempo']))
                                <span style="display:inline-block;padding:2px 10px;border-radius:50px;font-size:.72rem;font-weight:600;background:#f3f4f6;color:#9ca3af;" title="Angkatan belum mencapai masa studi normal (4 tahun)">Belum jatuh tempo</span>
                            @else
                                <span style="display:inline-block;min-width:46px;padding:2px 8px;border-radius:50px;font-size:.78rem;font-weight:700;background:#ecfdf5;color:#059669;">{{ $row['rate_lulus'] }}%</span>
                            @endif
                        </td>
                        <td style="padding:9px 12px;text-align:center;font-size:.85rem;color:#dc2626;font-weight:600;">{{ $row['do'] }}</td>
                        <td style="padding:9px 12px;text-align:center;">
                            <span style="display:inline-block;min-width:46px;padding:2px 8px;border-radius:50px;font-size:.78rem;font-weight:700;background:{{ $row['rate_do'] > 0 ? '#fef2f2' : '#f3f4f6' }};color:{{ $row['rate_do'] > 0 ? '#dc2626' : '#9ca3af' }};">{{ $row['rate_do'] }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center;color:#9ca3af;font-size:.85rem;">Belum ada data angkatan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>{{-- /da-section evaluasi_mutu --}}

{{-- Evaluasi Kegiatan Kemahasiswaan — bahan evaluasi kurikulum/mutu (urutan #4 untuk GPM) --}}
<div class="da-section" style="order:3;">
<div class="section-header section-gap" style="margin-top:6px;">
    <span class="section-label">Evaluasi Kegiatan Kemahasiswaan</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row" style="grid-template-columns:repeat(4,1fr);margin-bottom:18px;">
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#eff6ff;color:#2563eb;">
            <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/calendar.svg'))) !!}</span>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#2563eb;">{{ number_format($eval['kegiatan_total'] ?? 0) }}</div>
            <div class="kpi-mini-label">Total Kegiatan</div>
        </div>
    </div>
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#ecfdf5;color:#059669;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#059669;">{{ number_format($eval['kegiatan_selesai'] ?? 0) }}</div>
            <div class="kpi-mini-label">Terlaksana / Selesai</div>
        </div>
    </div>
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#fffbeb;color:#d97706;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#d97706;">{{ number_format($eval['kegiatan_berjalan'] ?? 0) }}</div>
            <div class="kpi-mini-label">Berjalan / Disetujui</div>
        </div>
    </div>
    <div class="kpi-mini">
        <div class="kpi-mini-icon" style="background:#f5f3ff;color:#7c3aed;">
            <span style="display:inline-flex;width:18px;height:18px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
        </div>
        <div>
            <div class="kpi-mini-val" style="color:#7c3aed;">{{ $eval['rate_realisasi_kegiatan'] ?? 0 }}%</div>
            <div class="kpi-mini-label">Tingkat Realisasi</div>
        </div>
    </div>
</div>

{{-- Breakdown SEMUA status kegiatan (FASE 3D) — jumlahnya konsisten dengan total --}}
<div class="chart-card section-gap">
    <div class="chart-title">
        <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
        Breakdown Status Kegiatan
        <span class="chart-title-right">Total: {{ number_format($eval['kegiatan_total'] ?? 0) }}</span>
    </div>
    @php
        $statusLabelMap = [
            'draft'       => ['Draft / Perencanaan', '#6b7280', '#f3f4f6'],
            'disetujui'   => ['Pelaksanaan',         '#2563eb', '#eff6ff'],
            'selesai'     => ['Selesai',             '#059669', '#ecfdf5'],
            'diajukan'    => ['Diajukan (legacy)',   '#d97706', '#fffbeb'],
            'akan_datang' => ['Akan Datang (legacy)','#d97706', '#fffbeb'],
            'berlangsung' => ['Berlangsung (legacy)','#d97706', '#fffbeb'],
        ];
        $perStatus = $eval['kegiatan_per_status'] ?? [];
        $sumStatus = array_sum($perStatus);
    @endphp
    @if($sumStatus > 0)
        <div style="display:flex;flex-wrap:wrap;gap:10px;">
            @foreach($perStatus as $st => $cnt)
                @php $cfg = $statusLabelMap[$st] ?? [ucfirst(str_replace('_',' ',$st)), '#6b7280', '#f3f4f6']; @endphp
                <div style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;background:{{ $cfg[2] }};">
                    <span style="font-size:1.2rem;font-weight:800;color:{{ $cfg[1] }};line-height:1;">{{ number_format($cnt) }}</span>
                    <span style="font-size:.78rem;font-weight:600;color:{{ $cfg[1] }};">{{ $cfg[0] }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:20px;color:#9ca3af;font-size:.85rem;">Belum ada kegiatan</div>
    @endif
</div>

{{-- Distribusi kegiatan per kategori --}}
<div class="chart-card section-gap">
    <div class="chart-title">
        <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/calendar.svg'))) !!}</span>
        Distribusi Kegiatan per Kategori
        <span class="chart-title-right">Total kategori: {{ count($eval['kegiatan_per_kategori'] ?? []) }}</span>
    </div>
    @php $maxKat = max(1, (int) (collect($eval['kegiatan_per_kategori'] ?? [])->max() ?? 0)); @endphp
    @if(!empty($eval['kegiatan_per_kategori']))
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($eval['kegiatan_per_kategori'] as $kategori => $jml)
                <div style="display:flex;align-items:center;gap:12px;">
                    <span style="font-size:.82rem;font-weight:600;color:#374151;width:160px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $kategori }}</span>
                    <div style="flex:1;height:22px;background:#f3f4f6;border-radius:6px;overflow:hidden;">
                        <div style="height:100%;width:{{ round($jml / $maxKat * 100) }}%;background:linear-gradient(90deg,#293C79,#415086);border-radius:6px;min-width:24px;"></div>
                    </div>
                    <span style="font-size:.82rem;font-weight:700;color:#1e1b4b;width:32px;text-align:right;flex-shrink:0;">{{ $jml }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:.85rem;">Belum ada kegiatan berkategori</div>
    @endif
</div>
</div>{{-- /da-section evaluasi_kegiatan --}}
@endif


{{-- ══════════════════════════════════════════════════════════════════
     🟡 TIER 3 — MAHASISWA AKTIF (cache 120 detik)
     Berubah saat admin update status — biasanya mingguan/bulanan.
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('mahasiswa'))
<div class="da-section" style="order:{{ $isGpm ? 4 : ($isDpm ? 7 : ($hasSection('admin_operasional') ? 4 : 3)) }};">
<div class="section-header section-gap">
    <span class="section-label">Mahasiswa Aktif</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $mhsKpis = [
            ['label'=>'Aktif',        'val'=>$mhs['total_aktif'],           'bg'=>'#eff6ff', 'color'=>'#2563eb', 'status'=>'aktif'],
            ['label'=>'Cuti',         'val'=>$mhs['total_cuti'],            'bg'=>'#fffbeb', 'color'=>'#d97706', 'status'=>'cuti'],
            ['label'=>'Drop Out',     'val'=>$mhs['total_do'],              'bg'=>'#fef2f2', 'color'=>'#dc2626', 'status'=>'drop_out'],
            ['label'=>'Pindah Studi', 'val'=>$mhs['total_pindah'],          'bg'=>'#E7E8F0', 'color'=>'#415086', 'status'=>'pindah_studi'],
            ['label'=>'Mangkir',      'val'=>$mhs['total_mangkir'] ?? 0,   'bg'=>'#fdf4ff', 'color'=>'#a855f7', 'status'=>'mangkir'],
            ['label'=>'Wafat',        'val'=>$mhs['total_wafat'] ?? 0,     'bg'=>'#f0fdf4', 'color'=>'#6b7280', 'status'=>'wafat'],
        ];
    @endphp
    @foreach($mhsKpis as $k)
        <div class="kpi-mini card-clickable"
             onclick="openDashModal('mahasiswa',{status:'{{ $k['status'] }}'},'Mahasiswa — {{ $k['label'] }}')">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ number_format($k['val']) }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    {{-- Donut: Status Seluruh Mahasiswa --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/pie-chart-01.svg'))) !!}</span>
            Status Seluruh Mahasiswa
            <span class="chart-title-right">Total: {{ number_format($totalSemuaMhs) }}</span>
        </div>
        <div class="donut-row">
            <div class="donut-canvas"><canvas id="chartStatusMhs"></canvas></div>
            <div class="donut-legend">
                @php
                    $mhsStatusItems = [
                        ['label'=>'Aktif',        'val'=>$mhs['total_aktif'],          'color'=>'#3b82f6'],
                        ['label'=>'Alumni',       'val'=>$mhs['total_alumni_status'],  'color'=>'#10b981'],
                        ['label'=>'Cuti',         'val'=>$mhs['total_cuti'],           'color'=>'#f59e0b'],
                        ['label'=>'Drop Out',     'val'=>$mhs['total_do'],             'color'=>'#ef4444'],
                        ['label'=>'Pindah',       'val'=>$mhs['total_pindah'],         'color'=>'#6F7DA4'],
                        ['label'=>'Mangkir',      'val'=>$mhs['total_mangkir'] ?? 0,  'color'=>'#a855f7'],
                        ['label'=>'Wafat',        'val'=>$mhs['total_wafat'] ?? 0,    'color'=>'#9ca3af'],
                    ];
                @endphp
                @foreach($mhsStatusItems as $si)
                    <div class="legend-item">
                        <div class="legend-dot" style="background:{{ $si['color'] }};"></div>
                        <span>{{ $si['label'] }}</span>
                        <span class="legend-val">{{ number_format($si['val']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Line: Semua Status Mahasiswa per Angkatan --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/line-chart-up-01.svg'))) !!}</span>
            Distribusi Mahasiswa per Angkatan
        </div>
        {{-- Filter Buttons --}}
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;" id="angkatanFilters">
            @php
                $lineStatuses = [
                    'aktif'       => ['label'=>'Aktif',        'color'=>'#3b82f6','bg'=>'#eff6ff','border'=>'#bfdbfe'],
                    'alumni'      => ['label'=>'Alumni',       'color'=>'#10b981','bg'=>'#ecfdf5','border'=>'#a7f3d0'],
                    'cuti'        => ['label'=>'Cuti',         'color'=>'#f59e0b','bg'=>'#fffbeb','border'=>'#fde68a'],
                    'mangkir'     => ['label'=>'Mangkir',      'color'=>'#a855f7','bg'=>'#fdf4ff','border'=>'#e9d5ff'],
                    'wafat'       => ['label'=>'Wafat',        'color'=>'#6b7280','bg'=>'#f9fafb','border'=>'#e5e7eb'],
                    'drop_out'    => ['label'=>'Drop Out',     'color'=>'#ef4444','bg'=>'#fef2f2','border'=>'#fecaca'],
                    'pindah_studi'=> ['label'=>'Pindah Studi', 'color'=>'#6F7DA4','bg'=>'#E7E8F0','border'=>'#CED4E0'],
                ];
            @endphp
            @foreach($lineStatuses as $key => $cfg)
                <button
                    class="angkatan-filter-btn active"
                    data-status="{{ $key }}"
                    onclick="toggleAngkatanLine('{{ $key }}', this)"
                    style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:50px;border:1.5px solid {{ $cfg['border'] }};background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;">
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $cfg['color'] }};display:inline-block;flex-shrink:0;"></span>
                    {{ $cfg['label'] }}
                </button>
            @endforeach
        </div>
        <div style="position:relative;height:230px;"><canvas id="chartAngkatan"></canvas></div>
    </div>
</div>

<div class="chart-grid-2">
    {{-- Prestasi Card — redesigned --}}
    <div class="chart-card" style="display:flex;flex-direction:column;gap:16px;">
        @php
            $pColors    = ['#293C79','#3b82f6','#10b981','#f59e0b','#ef4444'];
            $tingkatOrder = ['internasional','nasional','regional','universitas','prodi'];
            // cari tingkat tertinggi yang punya data
            $tingkatTertinggi = null;
            foreach ($tingkatOrder as $tk) {
                if (!empty($mhs['prestasi_per_tingkat'][$tk])) {
                    $tingkatTertinggi = $tk;
                    break;
                }
            }
            // Counter pending diambil dari data mahasiswa (selalu ada untuk GPM),
            // fallback ke action_items untuk admin. Daftar tetap hanya terverifikasi.
            $pendingPrestasi = $mhs['prestasi_pending'] ?? ($act['verif_prestasi'] ?? 0);
        @endphp

        {{-- Header --}}
        <div class="chart-title" style="margin-bottom:0;">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
            Prestasi Mahasiswa
            @if($canAccessVerifikasi)
            <a href="{{ route('manajemenmahasiswa.verifikasi.index') }}" class="table-link" style="margin-left:auto;">Verifikasi →</a>
            @endif
        </div>

        {{-- KPI Mini Row --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            <div style="background:#E7E8F0;border-radius:10px;padding:10px 12px;text-align:center;">
                <div style="font-size:1.4rem;font-weight:800;color:#293C79;line-height:1;">{{ $mhs['total_prestasi'] }}</div>
                <div style="font-size:.72rem;color:#9ca3af;font-weight:500;margin-top:2px;">Terverifikasi</div>
            </div>
            <div style="background:{{ $pendingPrestasi > 0 ? '#fff7ed' : '#f9fafb' }};border-radius:10px;padding:10px 12px;text-align:center;">
                <div style="font-size:1.4rem;font-weight:800;color:{{ $pendingPrestasi > 0 ? '#ea580c' : '#d1d5db' }};line-height:1;">{{ $pendingPrestasi }}</div>
                <div style="font-size:.72rem;color:#9ca3af;font-weight:500;margin-top:2px;">Menunggu Review</div>
            </div>
            <div style="background:#f0fdf4;border-radius:10px;padding:10px 12px;text-align:center;">
                <div style="font-size:.9rem;font-weight:700;color:#059669;line-height:1.3;">{{ $tingkatTertinggi ? ucfirst($tingkatTertinggi) : '—' }}</div>
                <div style="font-size:.72rem;color:#9ca3af;font-weight:500;margin-top:2px;">Tingkat Tertinggi</div>
            </div>
        </div>

        @if($mhs['total_prestasi'] > 0)
            {{-- Donut + Legend --}}
            <div class="donut-row" style="gap:14px;">
                <div style="flex:0 0 120px;height:120px;position:relative;">
                    <canvas id="chartPrestasi"></canvas>
                </div>
                <div class="donut-legend" style="flex:1;gap:5px;">
                    @php $pi=0; @endphp
                    @foreach($tingkatOrder as $tk)
                        @php $cnt = $mhs['prestasi_per_tingkat'][$tk] ?? 0; @endphp
                        <div class="legend-item" style="{{ $cnt === 0 ? 'opacity:.35;' : '' }}">
                            <div class="legend-dot" style="background:{{ $pColors[$pi % 5] }};"></div>
                            <span style="font-size:.8rem;">{{ $tingkatLabels[$tk] ?? ucfirst($tk) }}</span>
                            <span class="legend-val" style="font-size:.82rem;">{{ $cnt }}</span>
                        </div>
                        @php $pi++; @endphp
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div style="border-top:1px solid #f3f4f6;margin:0 -2px;"></div>

            {{-- Recent Prestasi List --}}
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="font-size:.75rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Terbaru Diverifikasi</div>
                    <button onclick="openPrestasiModal()"
                        style="display:inline-flex;align-items:center;gap:4px;font-size:.78rem;font-weight:700;color:#6B4FF4;background:none;border:none;cursor:pointer;padding:0;transition:opacity .15s;"
                        onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                        Lihat Semua
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
                @php
                    $tingkatBadgeColor = [
                        'internasional' => ['bg'=>'#fef2f2','color'=>'#dc2626','border'=>'#fecaca'],
                        'nasional'      => ['bg'=>'#fff7ed','color'=>'#ea580c','border'=>'#fed7aa'],
                        'regional'      => ['bg'=>'#fffbeb','color'=>'#d97706','border'=>'#fde68a'],
                        'universitas'   => ['bg'=>'#eff6ff','color'=>'#2563eb','border'=>'#bfdbfe'],
                        'prodi'         => ['bg'=>'#E7E8F0','color'=>'#415086','border'=>'#CED4E0'],
                    ];
                @endphp
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @forelse($mhs['prestasi_terbaru'] as $pr)
                        @php
                            $studentName = $pr->kemahasiswaan?->user?->name ?? $pr->kemahasiswaan?->nama ?? 'Mahasiswa';
                            $initials    = strtoupper(substr($studentName, 0, 2));
                            $bc          = $tingkatBadgeColor[$pr->tingkat] ?? ['bg'=>'#f3f4f6','color'=>'#6b7280','border'=>'#e5e7eb'];
                        @endphp
                        <div style="display:flex;align-items:center;gap:9px;padding:7px 10px;background:#fafafa;border-radius:10px;border:1px solid #f3f4f6;">
                            <div class="avatar-sm" style="width:28px;height:28px;font-size:10px;flex-shrink:0;">{{ $initials }}</div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.82rem;font-weight:600;color:#1e1b4b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $pr->nama_prestasi }}</div>
                                <div style="font-size:.75rem;color:#9ca3af;">{{ $studentName }}</div>
                            </div>
                            <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:50px;font-size:.7rem;font-weight:700;background:{{ $bc['bg'] }};color:{{ $bc['color'] }};border:1px solid {{ $bc['border'] }};white-space:nowrap;flex-shrink:0;">
                                {{ ucfirst($pr->tingkat) }}
                            </span>
                        </div>
                    @empty
                        <div style="text-align:center;padding:12px;color:#9ca3af;font-size:.82rem;">—</div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- Empty state yang lebih menarik --}}
            <div style="display:flex;flex-direction:column;align-items:center;padding:24px 16px;gap:10px;">
                <div style="width:56px;height:56px;border-radius:50%;background:#E7E8F0;display:flex;align-items:center;justify-content:center;">
                    <span style="display:inline-flex;width:26px;height:26px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/star.svg'))) !!}</span>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:.9rem;font-weight:600;color:#374151;margin-bottom:3px;">Belum ada prestasi terverifikasi</div>
                    <div style="font-size:.8rem;color:#9ca3af;">Prestasi mahasiswa yang disetujui akan tampil di sini</div>
                </div>
                @if($pendingPrestasi > 0 && $canAccessVerifikasi)
                    <a href="{{ route('manajemenmahasiswa.verifikasi.index') }}"
                        style="display:inline-flex;align-items:center;gap:5px;padding:7px 16px;background:#E7E8F0;color:#293C79;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;border:1px solid #CED4E0;">
                        <span style="display:inline-flex;width:13px;height:13px;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/check.svg'))) !!}</span>
                        Review {{ $pendingPrestasi }} Pengajuan Pending
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Table: Mahasiswa Aktif Terbaru --}}
    <div class="chart-card" style="overflow-x:auto;padding:22px 20px;">
        <div class="table-header">
            <div class="table-title">
                <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/users-01.svg'))) !!}</span>
                Mahasiswa Aktif Terbaru
            </div>
            <a href="{{ route('manajemenmahasiswa.direktori.mahasiswa.index') }}" class="table-link">Lihat Semua →</a>
        </div>
        <table class="da-table">
            <thead><tr><th>Mahasiswa</th><th>NIM</th><th>Angkatan</th></tr></thead>
            <tbody>
                @forelse($mhs['mahasiswa_terbaru'] as $m)
                    @php $nm = $m->nama ?? $m->user?->name ?? '-'; @endphp
                    <tr>
                        <td><div style="display:flex;align-items:center;gap:9px;"><div class="avatar-sm">{{ strtoupper(substr($nm,0,2)) }}</div><span style="font-weight:600;color:#1e1b4b;">{{ $nm }}</span></div></td>
                        <td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">{{ $m->nim ?? '-' }}</td>
                        <td>{{ $m->angkatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>{{-- /da-section mahasiswa --}}
@endif


{{-- ══════════════════════════════════════════════════════════════════
     ⚠️ EVALUASI CALON DO & LULUSAN PER PERIODE
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('calon_do') || $hasSection('lulusan'))
<div class="da-section" style="order:{{ $isGpm ? 2 : ($isDpm ? 1 : ($hasSection('admin_operasional') ? 5 : 4)) }};">
<div class="section-header section-gap">
    <span class="section-label">Pemantauan Akademik</span>
    <div class="section-line"></div>
</div>

<div class="chart-grid-2" style="margin-bottom:18px;">
    {{-- Evaluasi Calon DO --}}
    @if($hasSection('calon_do') && !empty($cdo))
    @php $cdoTotal = $cdo['total_count'] ?? 0; @endphp
    <div class="chart-card" style="{{ $cdoTotal > 0 ? 'border:1px solid #fde68a;' : '' }}">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#d97706;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/alert-triangle.svg'))) !!}</span>
            Deteksi Dini Drop Out
            @if($cdoTotal > 0)
                <button onclick="openDashModal('calon-do',{},'Deteksi Dini Drop Out (Semester ≥ 9)')"
                    style="margin-left:auto;font-size:.78rem;font-weight:700;color:#d97706;background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                    Lihat Detail
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            @endif
        </div>

        {{-- Dua tier deteksi dini (FASE 3B) --}}
        <div style="display:flex;gap:10px;margin-bottom:12px;">
            <div style="flex:1;text-align:center;padding:12px;border-radius:14px;background:#fef2f2;border:1px solid #fecaca;">
                <div style="font-size:2rem;font-weight:900;line-height:1;color:#dc2626;">{{ $cdo['kritis_count'] ?? 0 }}</div>
                <div style="font-size:.72rem;color:#dc2626;font-weight:700;margin-top:4px;">Kritis · Smt ≥ 12</div>
            </div>
            <div style="flex:1;text-align:center;padding:12px;border-radius:14px;background:#fffbeb;border:1px solid #fde68a;">
                <div style="font-size:2rem;font-weight:900;line-height:1;color:#d97706;">{{ $cdo['pantau_count'] ?? 0 }}</div>
                <div style="font-size:.72rem;color:#d97706;font-weight:700;margin-top:4px;">Perlu Pemantauan · Smt 9–11</div>
            </div>
        </div>
        <div style="font-size:.78rem;color:#9ca3af;line-height:1.5;margin-bottom:12px;">
            Mahasiswa <strong>aktif</strong> pada semester lanjut (angkatan ≤ {{ $cdo['threshold_pantau'] ?? '-' }}). Perlu evaluasi &amp; pendampingan akademik bertingkat.
        </div>

        @if(!empty($cdo['list']) && count($cdo['list']) > 0)
        <div style="border-top:1px solid #f3f4f6;padding-top:10px;">
            <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Perlu Perhatian</div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($cdo['list'] as $m)
                    @php $isKritis = ($m['tier'] ?? '') === 'kritis'; @endphp
                    <div style="display:flex;align-items:center;gap:9px;padding:6px 10px;background:#fafafa;border-radius:8px;">
                        <div class="avatar-sm" style="width:26px;height:26px;font-size:9px;flex-shrink:0;">{{ strtoupper(substr($m['nama'],0,2)) }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.8rem;font-weight:600;color:#1e1b4b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $m['nama'] }}</div>
                            <div style="font-size:.72rem;color:#9ca3af;">{{ $m['nim'] }} · Angkatan {{ $m['angkatan'] }}</div>
                        </div>
                        <span style="font-size:.72rem;font-weight:700;color:{{ $isKritis ? '#dc2626' : '#d97706' }};background:{{ $isKritis ? '#fef2f2' : '#fffbeb' }};padding:2px 8px;border-radius:50px;white-space:nowrap;flex-shrink:0;">Smt {{ $m['semester'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="text-align:center;padding:16px;color:#059669;font-size:.85rem;font-weight:600;">✓ Tidak ada mahasiswa pada kategori pemantauan</div>
        @endif
    </div>
    @endif

    {{-- Lulusan per Periode --}}
    @if($hasSection('lulusan') && !empty($lulus))
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bank-02.svg'))) !!}</span>
            Lulusan per Periode
            <button onclick="openDashModal('lulusan-periode',{},'Lulusan Mahasiswa per Periode')"
                style="margin-left:auto;font-size:.78rem;font-weight:700;color:#293C79;background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                Lihat Detail
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </div>

        {{-- Indikator sinkronisasi alumni --}}
        @if(($lulus['belum_sinkron'] ?? 0) > 0)
        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;margin-bottom:14px;">
            <span style="display:inline-flex;width:18px;height:18px;color:#d97706;flex-shrink:0;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/alert-circle.svg'))) !!}</span>
            <div style="flex:1;font-size:.8rem;color:#92400e;line-height:1.5;">
                <strong>{{ $lulus['belum_sinkron'] }}</strong> mahasiswa berstatus lulus belum tersinkron ke direktori alumni.
            </div>
        </div>
        @else
        <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:14px;font-size:.8rem;color:#166534;font-weight:600;">
            ✓ Semua lulusan sudah tersinkron ke direktori alumni
        </div>
        @endif

        {{-- Bar lulusan per tahun --}}
        @php $maxLulus = max(1, (int) (collect($lulus['per_tahun'] ?? [])->max() ?? 0)); @endphp
        @if(!empty($lulus['per_tahun']))
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($lulus['per_tahun'] as $tahun => $jml)
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:.8rem;font-weight:700;color:#374151;width:46px;flex-shrink:0;">{{ $tahun }}</span>
                        <div style="flex:1;height:22px;background:#f3f4f6;border-radius:6px;overflow:hidden;position:relative;">
                            <div style="height:100%;width:{{ round($jml / $maxLulus * 100) }}%;background:linear-gradient(90deg,#293C79,#415086);border-radius:6px;min-width:24px;"></div>
                        </div>
                        <span style="font-size:.82rem;font-weight:700;color:#1e1b4b;width:32px;text-align:right;flex-shrink:0;">{{ $jml }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:.85rem;">Belum ada data lulusan</div>
        @endif
    </div>
    @endif
</div>
</div>{{-- /da-section pemantauan --}}
@endif


{{-- ══════════════════════════════════════════════════════════════════
     🟢 TIER 4 — ALUMNI (cache 300 detik / 5 menit)
     Paling jarang berubah — alumni update profil karir setiap beberapa bulan.
══════════════════════════════════════════════════════════════════════ --}}
@if($hasSection('alumni') && !empty($alm))
<div class="da-section" style="order:{{ $hasSection('admin_operasional') ? 6 : 5 }};">
<div class="section-header section-gap">
    <span class="section-label">Alumni</span>
    <div class="section-line"></div>
</div>

<div class="kpi-row">
    @php
        $almKpis = [
            ['label'=>'Total Alumni',  'val'=>$alm['total'],               'bg'=>'#E7E8F0','color'=>'#415086','filter'=>'semua'],
            ['label'=>'Sudah Terdata', 'val'=>$alm['total_terdata'],       'bg'=>'#ecfdf5','color'=>'#059669','filter'=>'terdata'],
            ['label'=>'Belum Terdata', 'val'=>$alm['total_belum_terdata'], 'bg'=>'#fffbeb','color'=>'#d97706','filter'=>'belum_terdata'],
            ['label'=>'Serapan Kerja', 'val'=>$pctSerapan.'%',             'bg'=>'#eff6ff','color'=>'#2563eb','filter'=>'bekerja'],
        ];
    @endphp
    @foreach($almKpis as $k)
        <div class="kpi-mini card-clickable"
             onclick="openDashModal('alumni',{filter:'{{ $k['filter'] }}'},'Alumni — {{ $k['label'] }}')">
            <div class="kpi-mini-icon" style="background:{{ $k['bg'] }};color:{{ $k['color'] }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div>
                <div class="kpi-mini-val" style="color:{{ $k['color'] }};">{{ $k['val'] }}</div>
                <div class="kpi-mini-label">{{ $k['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="chart-grid-2">
    {{-- Donut: Status Karir --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/pie-chart-01.svg'))) !!}</span>
            Status Karir Alumni
        </div>
        <div class="donut-row">
            <div class="donut-canvas"><canvas id="chartStatusKarir"></canvas></div>
            <div class="donut-legend">
                @php $karirCM=['bekerja'=>'#10b981','wirausaha'=>'#3b82f6','studi_lanjut'=>'#6F7DA4','belum_bekerja'=>'#f59e0b','belum_terdata'=>'#9ca3af']; @endphp
                @foreach($alm['per_status_karir'] as $s => $cnt)
                    <div class="legend-item">
                        <div class="legend-dot" style="background:{{ $karirCM[$s] ?? '#9ca3af' }};"></div>
                        <span>{{ $karirLabels[$s] ?? ucfirst($s) }}</span>
                        <span class="legend-val">{{ number_format($cnt) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bar: Serapan per Angkatan --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-11.svg'))) !!}</span>
            Serapan Kerja per Angkatan (%)
        </div>
        <div class="chart-wrap"><canvas id="chartSerapan"></canvas></div>
    </div>
</div>

<div class="chart-grid-2">
    {{-- Donut: Distribusi Industri --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/briefcase-01.svg'))) !!}</span>
            Distribusi Bidang Industri
        </div>
        @if(count($alm['distribusi_industri']) > 0)
            <div class="donut-row">
                <div class="donut-canvas"><canvas id="chartIndustri"></canvas></div>
                <div class="donut-legend" style="max-height:170px;overflow-y:auto;">
                    @php $iC=['#293C79','#3b82f6','#10b981','#f59e0b','#ef4444','#6F7DA4','#06b6d4','#84cc16']; $ii=0; @endphp
                    @foreach($alm['distribusi_industri'] as $b => $cnt)
                        <div class="legend-item">
                            <div class="legend-dot" style="background:{{ $iC[$ii%8] }};"></div>
                            <span>{{ $industryLabels[$b] ?? ucfirst(str_replace('_',' ',$b)) }}</span>
                            <span class="legend-val">{{ $cnt }}</span>
                        </div>
                        @php $ii++; @endphp
                    @endforeach
                </div>
            </div>
        @else
            <div style="text-align:center;padding:36px 0;color:#9ca3af;font-size:.87rem;">Belum ada data industri</div>
        @endif
    </div>

    {{-- Progress bars: Ringkasan Karir --}}
    <div class="chart-card">
        <div class="chart-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/bar-chart-12.svg'))) !!}</span>
            Distribusi Persentase Karir
        </div>
        @php
            $totalK = max(1, array_sum($alm['per_status_karir']));
            $karirProg = ['bekerja'=>['label'=>'Bekerja','color'=>'#10b981'],'wirausaha'=>['label'=>'Wirausaha','color'=>'#3b82f6'],'studi_lanjut'=>['label'=>'Studi Lanjut','color'=>'#6F7DA4'],'belum_bekerja'=>['label'=>'Belum Terdata','color'=>'#9ca3af'],'belum_terdata'=>['label'=>'Belum Terdata','color'=>'#9ca3af']];
        @endphp
        <div class="progress-card">
            @foreach($alm['per_status_karir'] as $s => $cnt)
                @php $cfg=$karirProg[$s]??['label'=>ucfirst($s),'color'=>'#9ca3af']; $pct=round($cnt/$totalK*100,1); @endphp
                <div class="progress-item">
                    <div class="progress-row">
                        <span class="progress-label">{{ $cfg['label'] }}</span>
                        <span class="progress-count" style="color:{{ $cfg['color'] }};">{{ number_format($cnt) }} <span style="color:#9ca3af;font-weight:500;font-size:.78rem;">({{ $pct }}%)</span></span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width:{{ $pct }}%;background:{{ $cfg['color'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Table: Alumni Terbaru --}}
<div class="table-card">
    <div class="table-header">
        <div class="table-title">
            <span style="display:inline-flex;width:15px;height:15px;color:#293C79;">{!! str_replace(['#0D0D12','black','width="24"','height="24"'], ['currentColor','currentColor','width="100%"','height="100%"'], file_get_contents(public_path('images/icons/users-01.svg'))) !!}</span>
            Data Alumni Terbaru
        </div>
        <a href="{{ route('manajemenmahasiswa.direktori.alumni.index') }}" class="table-link">Lihat Semua →</a>
    </div>
    <table class="da-table">
        <thead><tr><th>#</th><th>Alumni</th><th>NIM</th><th>Angkatan</th><th>Tahun Lulus</th><th>Perusahaan / Instansi</th><th>Status Karir</th></tr></thead>
        <tbody>
            @forelse($alm['alumni_terbaru'] as $i => $al)
                @php
                    $nm = $al->user?->name ?? '-';
                    $bm = ['bekerja'=>'badge-bekerja','wirausaha'=>'badge-wirausaha','studi_lanjut'=>'badge-studi'];
                    $badge = $bm[$al->status_karir ?? ''] ?? 'badge-belum';
                @endphp
                <tr>
                    <td style="color:#d1d5db;font-size:.8rem;">{{ $i+1 }}</td>
                    <td><div style="display:flex;align-items:center;gap:9px;"><div class="avatar-sm">{{ strtoupper(substr($nm,0,2)) }}</div><span style="font-weight:600;color:#1e1b4b;">{{ $nm }}</span></div></td>
                    <td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">{{ $al->nim ?? '-' }}</td>
                    <td>{{ $al->angkatan ?? '-' }}</td>
                    <td>{{ $al->tahun_lulus ?? '-' }}</td>
                    <td>{{ $al->perusahaan ?? '-' }}</td>
                    <td><span class="badge {{ $badge }}">{{ $karirLabels[$al->status_karir ?? 'belum_terdata'] ?? 'Belum Terdata' }}</span></td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="7">Belum ada data alumni</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>{{-- /da-section alumni --}}
@endif

</div>{{-- /da-sections --}}

{{-- ── Dashboard Modal ───────────────────────────────────────────────────── --}}
<div class="dm-overlay" id="dashModal" onclick="if(event.target===this)closeDashModal()">
    <div class="dm-box">
        <div class="dm-head">
            <h5 id="dmTitle">—</h5>
            <span class="dm-badge" id="dmBadge">0 data</span>
            <button class="dm-close" onclick="closeDashModal()">✕</button>
        </div>
        <div class="dm-toolbar" id="dmToolbar">
            <div class="dm-search-wrap">
                <svg class="dm-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input class="dm-search" id="dmSearch" placeholder="Cari..." oninput="filterModalRows()" autocomplete="off">
            </div>
            <div class="dm-filter-chips" id="dmAngkatanChips"></div>
        </div>
        <div class="dm-body" id="dmBody">
            <div class="dm-loading"><div class="dm-spinner"></div> Memuat data...</div>
        </div>
        <div class="dm-footer" id="dmFooter"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function mkDonut(id, labels, data, colors) {
    const el = document.getElementById(id);
    if (!el || !data.some(v => v > 0)) return;
    new Chart(el, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${c.parsed}` } } },
        },
    });
}
function mkBar(id, labels, data, color, suffix = '') {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'bar',
        data: { labels, datasets: [{ data, backgroundColor: color, borderRadius: 6, borderSkipped: false }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => v + suffix } },
            },
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.parsed.y}${suffix}` } } },
        },
    });
}

// ── Global variables — harus di luar DOMContentLoaded agar bisa diakses onclick HTML
let _angkatanChart = null;   // instance Chart.js multi-line angkatan

// FIX Bug #1: toggleAngkatanLine di scope global agar onclick HTML bisa memanggil
function toggleAngkatanLine(statusKey, btn) {
    if (!_angkatanChart) return;
    const dsIndex = _angkatanChart.data.datasets.findIndex(d => d._statusKey === statusKey);
    if (dsIndex === -1) return;

    const isActive = btn.classList.contains('active');
    _angkatanChart.data.datasets[dsIndex].hidden = isActive;
    _angkatanChart.update();

    if (isActive) {
        btn.classList.remove('active');
        btn.style.opacity = '0.4';
        btn.style.textDecoration = 'line-through';
    } else {
        btn.classList.add('active');
        btn.style.opacity = '1';
        btn.style.textDecoration = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {

    // Tier 2 — Trend Kegiatan (GPM: berdasarkan tanggal pelaksanaan; admin: tanggal input)
    @if($hasSection('activity'))
    @php $trendKey = $isGpm ? 'kegiatan_trend_pelaksanaan' : 'kegiatan_trend'; @endphp
    mkBar('chartKegiatanTrend',
        {!! json_encode(array_keys($acty[$trendKey] ?? [])) !!},
        {!! json_encode(array_values($acty[$trendKey] ?? [])) !!},
        '#293C79'
    );
    @endif

    // Tier 3 — Mahasiswa
    @if($hasSection('mahasiswa'))
    mkDonut('chartStatusMhs',
        ['Aktif','Alumni','Cuti','Drop Out','Pindah','Mangkir','Wafat'],
        [{{ $mhs['total_aktif'] }},{{ $mhs['total_alumni_status'] }},{{ $mhs['total_cuti'] }},{{ $mhs['total_do'] }},{{ $mhs['total_pindah'] }},{{ $mhs['total_mangkir'] ?? 0 }},{{ $mhs['total_wafat'] ?? 0 }}],
        ['#3b82f6','#10b981','#f59e0b','#ef4444','#6F7DA4','#a855f7','#9ca3af']
    );
    // ── Multi-line chart: semua status per angkatan ──────────────────────────
    @php
        $angkatanLabels = $mhs['angkatan_list'];
        $angkatanByStatus = $mhs['per_angkatan_by_status'];
        $lineDatasets = [
            ['status'=>'aktif',        'label'=>'Aktif',        'color'=>'59,130,246'],
            ['status'=>'alumni',       'label'=>'Alumni',       'color'=>'16,185,129'],
            ['status'=>'cuti',         'label'=>'Cuti',         'color'=>'245,158,11'],
            ['status'=>'drop_out',     'label'=>'Drop Out',     'color'=>'239,68,68'],
            ['status'=>'pindah_studi', 'label'=>'Pindah Studi', 'color'=>'111,125,164'],
            ['status'=>'mangkir',      'label'=>'Mangkir',      'color'=>'168,85,247'],
            ['status'=>'wafat',        'label'=>'Wafat',        'color'=>'156,163,175'],
        ];
    @endphp

    const angkatanChartData = {
        labels: {!! json_encode($angkatanLabels) !!},
        datasets: [
            @foreach($lineDatasets as $ds)
            {
                label: '{{ $ds['label'] }}',
                data: {!! json_encode($angkatanByStatus[$ds['status']] ?? array_fill(0, count($angkatanLabels), 0)) !!},
                borderColor: 'rgb({{ $ds['color'] }})',
                backgroundColor: '{{ $useBarDistribusiMahasiswa ? "rgb(".$ds["color"].")" : "rgba(".$ds["color"].", 0.08)" }}',
                fill: true,
                tension: 0.35,
                borderWidth: {{ $useBarDistribusiMahasiswa ? '1' : '2.5' }},
                stack: 'mhs',
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb({{ $ds['color'] }})',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                _statusKey: '{{ $ds['status'] }}',
            },
            @endforeach
        ],
    };

    // FIX: assign ke variabel global (dideklarasikan di luar DOMContentLoaded)
    // sehingga toggleAngkatanLine() bisa mengaksesnya dari scope global
    _angkatanChart = new Chart(document.getElementById('chartAngkatan'), {
        type: '{{ $useBarDistribusiMahasiswa ? "bar" : "line" }}',
        data: angkatanChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { stacked: {{ $useBarDistribusiMahasiswa ? 'true' : 'false' }}, grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    stacked: {{ $useBarDistribusiMahasiswa ? 'true' : 'false' }},
                    grid: { color: '#f3f4f6' },
                    ticks: { font: { size: 11 }, stepSize: 1, precision: 0 },
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: items => 'Angkatan ' + items[0].label,
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} mahasiswa`,
                    },
                },
            },
        },
    });
    @if(($mhs['total_prestasi'] ?? 0) > 0)
    mkDonut('chartPrestasi',
        {!! json_encode(array_map(fn($k) => $tingkatLabels[$k] ?? ucfirst($k), array_keys($mhs['prestasi_per_tingkat'] ?? []))) !!},
        {!! json_encode(array_values($mhs['prestasi_per_tingkat'] ?? [])) !!},
        ['#293C79','#3b82f6','#10b981','#f59e0b','#ef4444']
    );
    @endif
    @endif {{-- /mahasiswa charts --}}

    // Tier 4 — Alumni
    @if($hasSection('alumni') && !empty($alm))
    @php
        $kChartL=[]; $kChartD=[]; $kChartC=[];
        $kCM=['bekerja'=>'#10b981','wirausaha'=>'#3b82f6','studi_lanjut'=>'#6F7DA4','belum_bekerja'=>'#f59e0b','belum_terdata'=>'#9ca3af'];
        foreach($alm['per_status_karir'] as $s=>$v) {
            $kChartL[] = $karirLabels[$s] ?? ucfirst($s);
            $kChartD[] = $v;
            $kChartC[] = $kCM[$s] ?? '#9ca3af';
        }
    @endphp
    mkDonut('chartStatusKarir',
        {!! json_encode($kChartL) !!},
        {!! json_encode($kChartD) !!},
        {!! json_encode($kChartC) !!}
    );
    mkBar('chartSerapan',
        {!! json_encode(array_keys($alm['serapan_per_angkatan'] ?? [])) !!},
        {!! json_encode(array_values($alm['serapan_per_angkatan'] ?? [])) !!},
        '#10b981', '%'
    );
    @if(count($alm['distribusi_industri'] ?? []) > 0)
    @php
        $iL=[]; $iD=[]; $iC=[]; $iCols=['#293C79','#3b82f6','#10b981','#f59e0b','#ef4444','#6F7DA4','#06b6d4','#84cc16']; $ic=0;
        foreach($alm['distribusi_industri'] as $b=>$v) {
            $iL[] = $industryLabels[$b] ?? ucfirst(str_replace('_',' ',$b));
            $iD[] = $v; $iC[] = $iCols[$ic%8]; $ic++;
        }
    @endphp
    mkDonut('chartIndustri',
        {!! json_encode($iL) !!},
        {!! json_encode($iD) !!},
        {!! json_encode($iC) !!}
    );
    @endif
    @endif {{-- /alumni charts --}}

});

// ── Dashboard Modal ─────────────────────────────────────────────────────────

const MODAL_URL = '{{ route('manajemenmahasiswa.dashboard.modal') }}';
let _modalAllRows = [];   // semua row yang difetch
let _modalType    = '';   // tipe modal aktif
let _activeAngkatan = 'semua'; // nilai filter chip aktif
let _filterField    = 'angkatan'; // field yang difilter oleh chip (angkatan / tahun_lulus)

// FIX Bug #4: helper sanitasi XSS — escape karakter berbahaya sebelum render ke innerHTML
function escHtml(str) {
    return String(str ?? '-')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function openDashModal(type, params, title) {
    _modalType = type;
    _activeAngkatan = 'semua';
    document.getElementById('dmTitle').textContent = title;
    document.getElementById('dmBadge').textContent = '...';
    document.getElementById('dmSearch').value = '';
    document.getElementById('dmBody').innerHTML = '<div class="dm-loading"><div class="dm-spinner"></div> Memuat data...</div>';
    document.getElementById('dmFooter').textContent = '';
    document.getElementById('dashModal').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset chips & tentukan field filter sesuai tipe modal
    const chipsWrap = document.getElementById('dmAngkatanChips');
    chipsWrap.innerHTML = '';
    const chipTypes = ['mahasiswa', 'calon-do', 'lulusan-periode'];
    chipsWrap.style.display = chipTypes.includes(type) ? 'flex' : 'none';
    _filterField = type === 'lulusan-periode' ? 'tahun_lulus' : 'angkatan';

    const qs = new URLSearchParams({ type, ...params }).toString();
    fetch(`${MODAL_URL}?${qs}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
        },
    })
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(json => {
            _modalAllRows = json.data || [];
            document.getElementById('dmBadge').textContent = `${json.total ?? _modalAllRows.length} data`;
            document.getElementById('dmFooter').textContent = `Total: ${json.total ?? _modalAllRows.length} item`;

            // Filter chips — angkatan (mahasiswa/calon-do) atau tahun lulus (lulusan-periode)
            const chipList = json.angkatan_list ?? json.tahun_list ?? null;
            if (chipTypes.includes(type) && chipList?.length) {
                buildAngkatanChips(chipList);
            }

            renderTable(_modalAllRows, type);
        })
        .catch(err => {
            document.getElementById('dmBody').innerHTML =
                '<div class="dm-empty">Gagal memuat data. Pastikan koneksi aktif dan coba refresh.</div>';
            console.error('[DashModal]', err);
        });
}

function closeDashModal() {
    document.getElementById('dashModal').classList.remove('open');
    document.body.style.overflow = '';
}

function buildAngkatanChips(list) {
    const wrap = document.getElementById('dmAngkatanChips');
    wrap.innerHTML = '';
    const all = document.createElement('button');
    all.className = 'dm-chip active';
    all.textContent = 'Semua';
    all.dataset.angkatan = 'semua';
    all.onclick = () => setAngkatanFilter('semua');
    wrap.appendChild(all);

    list.forEach(a => {
        const btn = document.createElement('button');
        btn.className = 'dm-chip';
        btn.textContent = a;
        btn.dataset.angkatan = a;
        btn.onclick = () => setAngkatanFilter(a);
        wrap.appendChild(btn);
    });
}

function setAngkatanFilter(angkatan) {
    _activeAngkatan = angkatan;
    document.querySelectorAll('#dmAngkatanChips .dm-chip').forEach(b => {
        b.classList.toggle('active', b.dataset.angkatan == angkatan);
    });
    filterModalRows();
}

function filterModalRows() {
    const q = document.getElementById('dmSearch').value.toLowerCase().trim();
    let rows = _modalAllRows;

    // Filter chip (angkatan untuk mahasiswa/calon-do, tahun_lulus untuk lulusan-periode)
    const chipTypes = ['mahasiswa', 'calon-do', 'lulusan-periode'];
    if (chipTypes.includes(_modalType) && _activeAngkatan !== 'semua') {
        rows = rows.filter(r => r[_filterField] == _activeAngkatan);
    }
    // Filter search
    if (q) {
        rows = rows.filter(r => JSON.stringify(Object.values(r)).toLowerCase().includes(q));
    }

    renderTable(rows, _modalType);
    document.getElementById('dmFooter').textContent = `Menampilkan ${rows.length} dari ${_modalAllRows.length} item`;
}

function renderTable(rows, type) {
    if (!rows.length) {
        document.getElementById('dmBody').innerHTML = '<div class="dm-empty">Tidak ada data ditemukan.</div>';
        return;
    }

    // FIX Bug #4: semua nilai di-escape sebelum masuk ke innerHTML
    const av = (nm) => `<div class="avatar-sm" style="width:28px;height:28px;font-size:10px;">${escHtml((nm||'-').substring(0,2).toUpperCase())}</div>`;
    const nameCell = (nm) => `<td><div style="display:flex;align-items:center;gap:8px;">${av(nm)}<span style="font-weight:600;color:#1e1b4b;">${escHtml(nm)}</span></div></td>`;

    const configs = {
        mahasiswa: {
            headers: ['Nama', 'NIM', 'Angkatan', 'Email'],
            cells: r => `${nameCell(r.nama)}<td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">${escHtml(r.nim)}</td><td>${escHtml(r.angkatan)}</td><td style="color:#6b7280;font-size:.82rem;">${escHtml(r.email)}</td>`,
        },
        alumni: {
            headers: ['Nama', 'NIM', 'Angkatan', 'Th. Lulus', 'Status Karir', 'Perusahaan'],
            cells: r => `${nameCell(r.nama)}<td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">${escHtml(r.nim)}</td><td>${escHtml(r.angkatan)}</td><td>${escHtml(r.tahun_lulus)}</td><td>${escHtml(r.status_karir)}</td><td style="color:#6b7280;">${escHtml(r.perusahaan)}</td>`,
        },
        kegiatan: {
            headers: ['Judul Kegiatan', 'Tanggal Mulai', 'Lokasi'],
            cells: r => `<td style="font-weight:600;color:#1e1b4b;">${escHtml(r.judul)}</td><td style="color:#6b7280;">${escHtml(r.tanggal_mulai)}</td><td style="color:#6b7280;">${escHtml(r.lokasi)}</td>`,
        },
        pengumuman: {
            headers: ['Judul', 'Kategori', 'Target', 'Pembuat', 'Dipublish'],
            cells: r => `<td style="font-weight:600;color:#1e1b4b;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(r.judul)}</td><td>${escHtml(r.kategori)}</td><td>${escHtml(r.target_audience)}</td><td style="color:#6b7280;">${escHtml(r.author)}</td><td style="color:#6b7280;white-space:nowrap;">${escHtml(r.published_at)}</td>`,
        },
        thread: {
            headers: ['Judul Thread', 'Kategori', 'Pembuat', '👍', '💬', 'Dibuat'],
            cells: r => `<td style="font-weight:600;color:#1e1b4b;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(r.judul)}</td><td style="color:#6b7280;font-size:.8rem;">${escHtml(r.kategori)}</td><td style="color:#6b7280;">${escHtml(r.author)}</td><td style="color:#6b7280;">${escHtml(r.vote_count)}</td><td style="color:#6b7280;">${escHtml(r.comment_count)}</td><td style="color:#9ca3af;white-space:nowrap;font-size:.8rem;">${escHtml(r.created_at)}</td>`,
        },
        'calon-do': {
            headers: ['Nama', 'NIM', 'Angkatan', 'Semester', 'Status', 'Email'],
            cells: r => {
                const kritis = String(r.tier || '').toLowerCase().includes('kritis');
                const c = kritis ? '#dc2626' : '#d97706';
                const bg = kritis ? '#fef2f2' : '#fffbeb';
                return `${nameCell(r.nama)}<td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">${escHtml(r.nim)}</td><td>${escHtml(r.angkatan)}</td><td><span style="display:inline-block;padding:2px 8px;border-radius:50px;font-size:.75rem;font-weight:700;background:${bg};color:${c};">Smt ${escHtml(r.semester)}</span></td><td><span style="font-size:.75rem;font-weight:700;color:${c};">${escHtml(r.tier)}</span></td><td style="color:#6b7280;font-size:.82rem;">${escHtml(r.email)}</td>`;
            },
        },
        'lulusan-periode': {
            headers: ['Nama', 'NIM', 'Angkatan', 'Th. Lulus', 'Sinkron Alumni'],
            cells: r => `${nameCell(r.nama)}<td style="font-family:monospace;color:#9ca3af;font-size:.82rem;">${escHtml(r.nim)}</td><td>${escHtml(r.angkatan)}</td><td style="font-weight:600;color:#1e1b4b;">${escHtml(r.tahun_lulus)}</td><td>${r.tersinkron ? '<span style="display:inline-block;padding:2px 8px;border-radius:50px;font-size:.72rem;font-weight:700;background:#ecfdf5;color:#059669;">✓ Tersinkron</span>' : '<span style="display:inline-block;padding:2px 8px;border-radius:50px;font-size:.72rem;font-weight:700;background:#fffbeb;color:#d97706;">Belum</span>'}</td>`,
        },
    };

    const cfg = configs[type] || { headers: Object.keys(rows[0]), cells: r => Object.values(r).map(v => `<td>${escHtml(v)}</td>`).join('') };

    // Cek apakah ada row yang punya URL (untuk menentukan apakah kolom arrow perlu ditambahkan)
    const hasLinks = rows.some(r => r.url);
    const arrowHeader = hasLinks ? '<th class="dm-arrow-cell"></th>' : '';
    const thead = `<tr>${cfg.headers.map(h => `<th>${escHtml(h)}</th>`).join('')}${arrowHeader}</tr>`;

    const tbody = rows.map(r => {
        const cells = cfg.cells(r);
        const arrowCell = hasLinks
            ? `<td class="dm-arrow-cell">${r.url ? '→' : ''}</td>`
            : '';

        if (r.url) {
            // Baris bisa diklik — navigasi ke halaman detail
            const safeUrl = escHtml(r.url);
            return `<tr class="dm-row-link" onclick="window.location.href='${safeUrl}'" title="Buka detail">${cells}${arrowCell}</tr>`;
        }
        return `<tr>${cells}${arrowCell}</tr>`;
    }).join('');

    document.getElementById('dmBody').innerHTML = `<table class="dm-table"><thead>${thead}</thead><tbody>${tbody}</tbody></table>`;
}

// Escape key closes modal
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeDashModal(); closePrestasiModal(); }
});

// ── Prestasi Modal ────────────────────────────────────────────────────────────

const PRESTASI_MODAL_URL = '{{ route('manajemenmahasiswa.dashboard.modal') }}';
let _prestasiAllRows = [];
let _prestasiAngkatan = 'semua';
let _prestasiTingkat  = 'semua';

function openPrestasiModal() {
    document.getElementById('prestasiModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('prestasiSearch').value = '';
    document.getElementById('prestasiBody').innerHTML = '<div class="dm-loading"><div class="dm-spinner"></div> Memuat data...</div>';
    document.getElementById('prestasiFooter').textContent = '';
    _prestasiAngkatan = 'semua';
    _prestasiTingkat  = 'semua';

    fetch(`${PRESTASI_MODAL_URL}?type=prestasi-semua`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(json => {
        _prestasiAllRows = json.data || [];
        document.getElementById('prestasiBadge').textContent = `${json.total} data`;

        // Bangun angkatan chips
        buildPrestasiChips('angkatan', json.angkatan_list || [], 'prestasiAngkatanChips', setPrestasiAngkatan);
        renderPrestasiList(_prestasiAllRows);
    })
    .catch(() => {
        document.getElementById('prestasiBody').innerHTML = '<div class="dm-empty">Gagal memuat data.</div>';
    });
}

function closePrestasiModal() {
    document.getElementById('prestasiModal').classList.remove('open');
    document.body.style.overflow = '';
}

function buildPrestasiChips(type, list, wrapId, callback) {
    const wrap = document.getElementById(wrapId);
    wrap.innerHTML = '';
    const all = document.createElement('button');
    all.className = 'dm-chip active'; all.textContent = 'Semua';
    all.dataset.val = 'semua'; all.onclick = () => callback('semua');
    wrap.appendChild(all);
    list.forEach(v => {
        const btn = document.createElement('button');
        btn.className = 'dm-chip'; btn.textContent = v;
        btn.dataset.val = v; btn.onclick = () => callback(v);
        wrap.appendChild(btn);
    });
}

function setPrestasiAngkatan(val) {
    _prestasiAngkatan = val;
    document.querySelectorAll('#prestasiAngkatanChips .dm-chip')
        .forEach(b => b.classList.toggle('active', b.dataset.val == val));
    filterPrestasiRows();
}

function setPrestasiTingkat(val) {
    _prestasiTingkat = val;
    document.querySelectorAll('#prestasiTingkatChips .dm-chip')
        .forEach(b => b.classList.toggle('active', b.dataset.val == val));
    filterPrestasiRows();
}

function filterPrestasiRows() {
    const q = document.getElementById('prestasiSearch').value.toLowerCase().trim();
    let rows = _prestasiAllRows;
    if (_prestasiAngkatan !== 'semua') rows = rows.filter(r => r.angkatan == _prestasiAngkatan);
    if (_prestasiTingkat  !== 'semua') rows = rows.filter(r => r.tingkat === _prestasiTingkat);
    if (q) rows = rows.filter(r =>
        r.nama_prestasi.toLowerCase().includes(q) ||
        r.student_name.toLowerCase().includes(q) ||
        r.nim.toLowerCase().includes(q)
    );
    renderPrestasiList(rows);
    document.getElementById('prestasiFooter').textContent =
        `Menampilkan ${rows.length} dari ${_prestasiAllRows.length} prestasi`;
}

const _tingkatColors = {
    internasional: { bg:'#fef2f2', color:'#dc2626', border:'#fecaca' },
    nasional:      { bg:'#fff7ed', color:'#ea580c', border:'#fed7aa' },
    regional:      { bg:'#fffbeb', color:'#d97706', border:'#fde68a' },
    universitas:   { bg:'#eff6ff', color:'#2563eb', border:'#bfdbfe' },
    prodi:         { bg:'#E7E8F0', color:'#415086', border:'#CED4E0' },
};

function renderPrestasiList(rows) {
    const body = document.getElementById('prestasiBody');
    if (!rows.length) {
        body.innerHTML = '<div class="dm-empty">Tidak ada prestasi ditemukan.</div>';
        return;
    }

    body.innerHTML = rows.map((r, idx) => {
        const bc  = _tingkatColors[r.tingkat] || { bg:'#f3f4f6', color:'#6b7280', border:'#e5e7eb' };
        const ini = escHtml((r.student_name || '?').substring(0, 2).toUpperCase());

        const buktiBtns = r.bukti.length
            ? `<button onclick="toggleBukti(${idx})"
                   style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-size:.73rem;font-weight:600;color:#6B4FF4;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:6px;padding:3px 9px;cursor:pointer;transition:all .15s;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    ${r.bukti.length} Bukti
               </button>`
            : `<span style="font-size:.72rem;color:#d1d5db;margin-top:4px;display:inline-block;">Tidak ada bukti</span>`;

        const buktiItems = r.bukti.map(b => {
            if (b.is_image) {
                return `<a href="${escHtml(b.url)}" target="_blank" style="display:block;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                    <img src="${escHtml(b.url)}" alt="${escHtml(b.nama)}"
                         style="width:100%;max-height:160px;object-fit:cover;display:block;cursor:zoom-in;">
                </a>`;
            }
            return `<a href="${escHtml(b.url)}" target="_blank"
                       style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;text-decoration:none;color:#374151;font-size:.8rem;font-weight:600;transition:background .15s;"
                       onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='#f8fafc'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B4FF4" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        ${escHtml(b.nama)}
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="margin-left:auto;flex-shrink:0;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
               </a>`;
        }).join('');

        return `<div class="prestasi-row" style="border:1px solid #f3f4f6;border-radius:12px;padding:12px 14px;background:#fff;">
            <div style="display:flex;align-items:flex-start;gap:10px;">
                <div class="avatar-sm" style="width:32px;height:32px;font-size:11px;flex-shrink:0;">${ini}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.88rem;font-weight:700;color:#1e1b4b;line-height:1.3;margin-bottom:2px;">${escHtml(r.nama_prestasi)}</div>
                    <div style="font-size:.78rem;color:#6b7280;">${escHtml(r.student_name)} <span style="color:#d1d5db;">·</span> ${escHtml(r.nim)} <span style="color:#d1d5db;">·</span> Angkatan ${r.angkatan || '-'}</div>
                    <div style="font-size:.74rem;color:#9ca3af;margin-top:2px;">Diverifikasi: ${escHtml(r.verified_at)}</div>
                    ${buktiBtns}
                </div>
                <span style="display:inline-flex;align-items:center;padding:3px 9px;border-radius:50px;font-size:.7rem;font-weight:700;background:${bc.bg};color:${bc.color};border:1px solid ${bc.border};white-space:nowrap;flex-shrink:0;">
                    ${escHtml(r.tingkat.charAt(0).toUpperCase() + r.tingkat.slice(1))}
                </span>
            </div>
            <div id="bukti-${idx}" style="display:none;margin-top:10px;display:none;flex-direction:column;gap:6px;">
                ${buktiItems || '<div style="font-size:.8rem;color:#9ca3af;text-align:center;padding:8px;">Tidak ada bukti terlampir</div>'}
            </div>
        </div>`;
    }).join('');

    document.getElementById('prestasiFooter').textContent =
        `Menampilkan ${rows.length} dari ${_prestasiAllRows.length} prestasi`;
}

function toggleBukti(idx) {
    const el = document.getElementById(`bukti-${idx}`);
    if (!el) return;
    const isOpen = el.style.display === 'flex';
    el.style.display = isOpen ? 'none' : 'flex';
    el.style.flexDirection = 'column';
}

document.getElementById('prestasiModal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('prestasiModal')) closePrestasiModal();
});

</script>
@endpush

{{-- ── Prestasi Modal ─────────────────────────────────────────────────────── --}}
<div class="dm-overlay" id="prestasiModal" onclick="if(event.target===this)closePrestasiModal()">
    <div class="dm-box" style="max-width:760px;">

        {{-- Header --}}
        <div class="dm-head">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B4FF4" stroke-width="2">
                <circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
            </svg>
            <h5>Semua Prestasi Terverifikasi</h5>
            <span class="dm-badge" id="prestasiBadge">...</span>
            <button class="dm-close" onclick="closePrestasiModal()">✕</button>
        </div>

        {{-- Toolbar --}}
        <div class="dm-toolbar" style="flex-direction:column;gap:10px;align-items:stretch;">
            {{-- Search --}}
            <div class="dm-search-wrap">
                <svg class="dm-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input class="dm-search" id="prestasiSearch" placeholder="Cari nama prestasi, mahasiswa, NIM..." oninput="filterPrestasiRows()" autocomplete="off">
            </div>
            {{-- Filter Angkatan --}}
            <div>
                <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Angkatan</div>
                <div class="dm-filter-chips" id="prestasiAngkatanChips"></div>
            </div>
            {{-- Filter Tingkat --}}
            <div>
                <div style="font-size:.72rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Tingkat</div>
                <div class="dm-filter-chips" id="prestasiTingkatChips">
                    @php $tingkatList = ['semua'=>'Semua','internasional'=>'Internasional','nasional'=>'Nasional','regional'=>'Regional','universitas'=>'Universitas','prodi'=>'Prodi']; @endphp
                    @foreach($tingkatList as $val => $lbl)
                        <button class="dm-chip {{ $val === 'semua' ? 'active' : '' }}"
                            data-val="{{ $val }}"
                            onclick="setPrestasiTingkat('{{ $val }}')">{{ $lbl }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="dm-body" id="prestasiBody" style="display:flex;flex-direction:column;gap:8px;padding:16px;">
            <div class="dm-loading"><div class="dm-spinner"></div> Memuat data...</div>
        </div>

        {{-- Footer --}}
        <div class="dm-footer" id="prestasiFooter"></div>
    </div>
</div>

</x-manajemenmahasiswa::layouts.admin>
