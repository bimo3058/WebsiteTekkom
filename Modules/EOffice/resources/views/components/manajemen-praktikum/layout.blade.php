@props(['pageTitle' => null, 'noBox' => false])
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $pageTitle ?? 'Manajemen Praktikum' }} — SIPERKOM</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'], 'build-eoffice')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
<style>
/* ─── NProgress Override ─── */
#nprogress .bar { background: #0B266E !important; height: 3px !important; }
#nprogress .peg { display: none !important; }
#nprogress .spinner { display: none !important; }

/* ─── SITKOM Design System — ManajemenPraktikum component layer ─── */

/* Box / Wrap (superadmin pattern) */
.mp-wrap { flex:1; overflow:hidden; padding:10px; display:flex; flex-direction:column; min-height:0; }
.mp-box  { flex:1; min-height:0; background:#fff; border:1px solid var(--c-border); border-radius:12px;
           box-shadow:0 1px 3px rgba(0,0,0,.06); overflow:hidden; display:flex; flex-direction:column; }
.mp-box-body { flex:1; overflow-y:auto; padding:20px 24px; display:flex; flex-direction:column;
               gap:18px; scrollbar-width:thin; scrollbar-color:var(--c-border-strong) transparent; }
.mp-box-body::-webkit-scrollbar { width:5px; }
.mp-box-body::-webkit-scrollbar-thumb { background:var(--c-border-strong); border-radius:10px; }

/* Flash */
.mp-flash { display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:13px;
            font-weight:500; flex-shrink:0; border-bottom:1px solid transparent; }
.mp-flash-success { background:#DDF2EE; color:#174E43; border-color:#40C4AA; }
.mp-flash-error   { background:#FADAE1; color:#7C1028; border-color:#DF1C41; }

/* Page header (like superadmin _header) */
.mp-page-header { display:flex; align-items:flex-start; justify-content:space-between;
                  gap:16px; flex-wrap:wrap; flex-shrink:0; }
.mp-page-title  { font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0; }
.mp-page-sub    { font-size:12px; color:var(--c-fg-muted); margin-top:3px; }
.mp-page-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

/* Badges */
.mp-badge { display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600;
            padding:3px 10px; border-radius:9999px; letter-spacing:0.01em; line-height:1;
            white-space:nowrap; font-family:'Inter Tight',sans-serif; border:1px solid transparent; }
.mp-badge.sm { font-size:11px; padding:2px 8px; }
.mp-badge.lg { font-size:13px; padding:4px 12px; }
.mp-badge.primary  { background:#EBEDF6; color:#0B266E; border-color:#C7D2FE; }
.mp-badge.primary-fill { background:#0B266E; color:#fff; border-color:#0B266E; }
.mp-badge.success  { background:#F0FDF9; color:#0D9488; border-color:#99F6E4; }
.mp-badge.success-fill { background:#0D9488; color:#fff; border-color:#0D9488; }
.mp-badge.success .dot { display:none; }
.mp-badge.warning  { background:#FFFBEB; color:#92400E; border-color:#FCD34D; }
.mp-badge.warning-fill { background:#D97706; color:#fff; border-color:#D97706; }
.mp-badge.error    { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
.mp-badge.error-fill { background:#DF1C41; color:#fff; border-color:#DF1C41; }
.mp-badge.sky      { background:#EFF8FF; color:#075985; border-color:#BAE6FD; }
.mp-badge.sky-fill { background:#0369A1; color:#fff; border-color:#0369A1; }
.mp-badge.neutral  { background:#F8FAFC; color:#475569; border-color:#CBD5E1; }
.mp-badge.neutral-fill { background:#334155; color:#fff; border-color:#334155; }
.mp-badge .dot { display:none; }

/* Buttons */
.mp-btn { display:inline-flex; align-items:center; gap:6px; font-family:'Inter Tight',sans-serif;
          font-weight:600; border-radius:8px; border:none; cursor:pointer; transition:all .15s;
          letter-spacing:0.01em; line-height:1; text-decoration:none; white-space:nowrap; }
.mp-btn.lg { padding:10px 18px; font-size:14px; }
.mp-btn.md { padding:8px 14px; font-size:13px; }
.mp-btn.sm { padding:6px 10px; font-size:12px; }
.mp-btn.primary     { background:#0B266E; color:#fff; box-shadow:0 2px 6px rgba(11,38,110,.22); }
.mp-btn.primary:hover { background:#091958; box-shadow:0 4px 12px rgba(11,38,110,.3); }
.mp-btn.secondary   { background:#fff; color:var(--c-fg-sec); border:1px solid var(--c-border);
                      box-shadow:0 1px 2px rgba(0,0,0,.04); }
.mp-btn.secondary:hover { background:var(--c-bg); border-color:var(--c-border-strong); }
.mp-btn.destructive { background:#DF1C41; color:#fff; }
.mp-btn.destructive:hover { background:#95122B; }
.mp-btn.ghost       { background:rgba(11,38,110,.08); color:#0B266E; }
.mp-btn.ghost:hover { background:rgba(11,38,110,.13); }

/* Stat cards */
.mp-stats-grid { display:grid; gap:14px; }
.mp-stats-grid.cols-2 { grid-template-columns:repeat(2,1fr); }
.mp-stats-grid.cols-3 { grid-template-columns:repeat(3,1fr); }
.mp-stats-grid.cols-4 { grid-template-columns:repeat(4,1fr); }
.mp-stat { background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px;
           box-shadow:var(--shadow-card); transition:border-color .15s, box-shadow .15s; }
.mp-stat:hover { border-color:var(--c-primary-border); box-shadow:0 4px 14px rgba(11,38,110,.07); }
.mp-stat-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center;
                justify-content:center; flex-shrink:0; }
.mp-stat-label { font-size:12px; font-weight:500; color:var(--c-fg-muted); margin-top:12px; margin-bottom:6px; }
.mp-stat-value { font-size:28px; font-weight:700; color:var(--c-fg); line-height:1; letter-spacing:-.02em; }
.mp-stat-sub   { font-size:11px; color:var(--c-fg-placeholder); margin-top:4px; }

/* Table / Card containers */
.mp-card { background:#fff; border:1px solid var(--c-border); border-radius:14px;
           box-shadow:var(--shadow-card); display:flex; flex-direction:column; width:100%; min-width:0; }
.mp-card > :first-child { border-top-left-radius: 13px; border-top-right-radius: 13px; }
.mp-card > :last-child { border-bottom-left-radius: 13px; border-bottom-right-radius: 13px; }
.mp-card-header { padding:14px 18px; background:#fff; border-bottom:1px solid var(--c-border);
                  display:flex; align-items:center; gap:10px; flex-shrink:0; }
.mp-card-title { font-size:15px; font-weight:700; color:var(--c-fg); }
.mp-card-header .right { margin-left:auto; display:flex; align-items:center; gap:8px; }
.mp-card-body  { flex:1; overflow-y:auto; }
.mp-card-body > :first-child { border-top-left-radius: 13px; border-top-right-radius: 13px; }
.mp-th { font-size:11px; font-weight:600; color:var(--c-fg-placeholder); text-transform:uppercase;
         letter-spacing:.06em; }
.mp-tr { border-bottom:1px solid #F8F9FB; }
.mp-tr:last-child { border-bottom:none; }
.mp-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.mp-table { width:100%; border-collapse:collapse; }
.mp-table th { padding:10px 14px; text-align:left; font-size:11px; font-weight:600;
               color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:.06em;
               background:#F8F9FB; border-bottom:1px solid var(--c-border); white-space:nowrap; }
.mp-table td { padding:10px 14px; text-align:left; font-size:13px; color:var(--c-fg);
               border-bottom:1px solid #F2F4F7; white-space:nowrap; vertical-align:middle; }
.mp-table tbody tr:last-child td { border-bottom:none; }
.mp-table tbody tr:hover td { background:#FAFBFC; }

/* Form inputs */
.mp-input { width:100%; padding:8px 12px; border:1px solid var(--c-border); border-radius:8px;
            font-size:13px; color:var(--c-fg); font-family:'Inter Tight',sans-serif; outline:none;
            transition:border-color .15s, box-shadow .15s; background:#fff; }
.mp-input:focus { border-color:#0B266E; box-shadow:0 0 0 3px rgba(11,38,110,.08); }
.mp-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666D80' stroke-width='2' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
             background-repeat:no-repeat; background-position:right 10px center; padding-right:30px; }

/* Alert / notice box */
.mp-alert { border-radius:10px; padding:12px 16px; font-size:13px; font-weight:500; }
.mp-alert.info    { background:#D1F0F9; color:#0C4D6E; border:1px solid rgba(16,106,151,.25); }
.mp-alert.warning { background:#F9ECCB; color:#5B3D1E; border:1px solid rgba(211,156,61,.3); }
.mp-alert.success { background:#DDF2EE; color:#174E43; border:1px solid rgba(64,196,170,.3); }

/* Divider */
.mp-divider { height:1px; background:var(--c-border); margin:0 -24px; flex-shrink:0; }

/* Section title — accent bar pattern (§6.2) */
.sec-head   { display:flex; align-items:center; gap:10px; flex-shrink:0; }
.sec-bar    { width:4px; height:22px; background:#0B266E; border-radius:19px; flex-shrink:0; }
.sec-title  { font-family:'Inter Tight',sans-serif; font-size:15px; font-weight:700; color:#0D0D12; white-space:nowrap; }
.sec-rule   { flex:1; height:1px; background:#DFE1E7; }
.sec-action { font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#0B266E; text-decoration:none; white-space:nowrap; }
.sec-action:hover { text-decoration:underline; }

/* Stat icon tone variants */
.mp-stat-icon.navy   { background:#EEF1FA; color:#0E1E54; }
.mp-stat-icon.sky    { background:#D1F0F9; color:#106A97; }
.mp-stat-icon.green  { background:#DDF2EE; color:#174E43; }
.mp-stat-icon.yellow { background:#F9ECCB; color:#956321; }
.mp-stat-icon.red    { background:#FADAE1; color:#95122B; }

/* Avatar (table/chip) */
.mp-av { width:28px; height:28px; border-radius:999px; display:grid; place-items:center;
         font:700 11px/1 'Inter Tight',sans-serif; flex-shrink:0; }
.mp-av.sm { width:22px; height:22px; font-size:9px; }
.mp-av.lg { width:36px; height:36px; font-size:13px; }
.mp-av.navy   { background:#EEF1FA; color:#0E1E54; }
.mp-av.sky    { background:#D1F0F9; color:#106A97; }
.mp-av.green  { background:#DDF2EE; color:#174E43; }
.mp-av.yellow { background:#F9ECCB; color:#956321; }
.mp-av.red    { background:#FADAE1; color:#95122B; }
.mp-av.violet { background:#E4DFFD; color:#2A3A7C; }

/* Mobile overrides */
@media (max-width:767px) {
    .mp-wrap { padding:8px 8px 80px; }
    .mp-box  { border-radius:10px; }
    .mp-box-body { padding:14px; }
    .mp-stats-grid.cols-4 { grid-template-columns:repeat(2,1fr); }
    .mp-stats-grid.cols-3 { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:480px) {
    .mp-stats-grid.cols-2,
    .mp-stats-grid.cols-3,
    .mp-stats-grid.cols-4 { grid-template-columns:repeat(2,1fr); }
}
</style>
</head>
<body class="h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased" style="font-family:'Inter Tight',system-ui,sans-serif;">

@php
    $user         = auth()->user();
    $name         = $user->name;
    $initials     = strtoupper(substr($name, 0, 1));
    $sp           = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
    $currentRoute = request()->route()?->getName() ?? '';

    // Cek semua role yang dimiliki user — multi-role support
    $isAdmin  = $user->hasRole('superadmin') || $user->hasRole('admin_eoffice');
    $isDosen  = $user->hasRole('dosen');
    $isKoor   = $user->hasRole('koor_prak');
    $isAsprak = $user->hasRole('asprak');
    $isMhs    = $user->hasRole('mahasiswa');

    // Icon path strings
    $iHome    = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iList    = "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2";
    $iUser    = "M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z";
    $iCheck   = "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z";
    $iBook    = "M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z";
    $iBell    = "M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0";
    $iBack    = "M19 12H5M5 12l7-7M5 12l7 7";
    $iLogout  = "M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.94L7.87 4.06C6.39 3.66 5 5.21 5 7.27v9.46C5 18.79 6.39 20.34 7.87 19.94l3.2-.87C12.19 18.76 13 17.42 13 15.86v-.59M11 12h8M19 12l-2.5-2.72M19 12l-2.5 2.72";
    $iCal     = "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z";
    $iEdit    = "M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z";
    $iGear    = "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z";
    $iUsers   = "M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75";
    $iClipboard = "M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2M8 2h8v4H8z";
    $iShield  = "M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z";
    // Definisi section per role — tiap section muncul jika user punya role terkait
    $sections = [];

    if ($isAdmin) {
        $sections[] = [
            'label' => 'Admin',
            'color' => '#293C79',
            'bg'    => 'var(--c-primary-50)',
            'match' => 'manprak.admin',
            'groups' => [
                'Menu Utama' => [
                    ['href' => route('eoffice.manprak.admin.dashboard'),              'label' => 'Dashboard',          'match' => 'admin.dashboard',          'icon' => $iHome],
                ],
                'Kelola Data' => [
                    ['href' => route('eoffice.manprak.admin.praktikum.index'),        'label' => 'Daftar Praktikum',   'match' => 'admin.praktikum',          'icon' => $iBook],
                    ['href' => route('eoffice.manprak.admin.matkul-praktikum.index'), 'label' => 'Mata Kuliah Praktikum', 'match' => 'matkul-praktikum',      'icon' => $iList],
                    ['href' => route('eoffice.manprak.admin.dosen.index'),            'label' => 'Daftar Dosen',       'match' => 'admin.dosen',              'icon' => $iUsers],
                    ['href' => route('eoffice.manprak.admin.pendaftaran-koor.index'),'label' => 'Pendaftaran',      'match' => 'pendaftaran',              'icon' => $iClipboard],
                    ['href' => route('eoffice.manprak.admin.kelola-role.index'),      'label' => 'Kelola Role',        'match' => 'kelola-role',              'icon' => $iShield],
                ],
            ],
        ];
    }

    if ($isDosen) {
        $sections[] = [
            'label' => 'Dosen',
            'color' => '#0B266E',
            'bg'    => 'rgba(11,38,110,0.08)',
            'match' => 'manprak.dosen',
            'groups' => [
                'Utama' => [
                    ['href' => route('eoffice.manprak.dosen.dashboard'),                             'label' => 'Dashboard',        'match' => 'dosen.dashboard',        'icon' => $iHome],
                ],
                'Pendaftaran' => [
                    ['href' => route('eoffice.manprak.dosen.periode-pendaftaran.index'),             'label' => 'Buka Periode Koordinator','match' => 'dosen.periode-pendaftaran', 'icon' => $iCal],
                    ['href' => route('eoffice.manprak.dosen.pendaftaran-koor.index'),                'label' => 'Seleksi Koordinator',     'match' => 'dosen.pendaftaran-koor', 'icon' => $iCheck],
                ],
                'Kelola' => [
                    ['href' => route('eoffice.manprak.dosen.asprak.index'),                          'label' => 'Asisten Praktikum','match' => 'dosen.asprak',           'icon' => $iUser],
                    ['href' => route('eoffice.manprak.dosen.daftar-praktikan.index'),                'label' => 'Daftar Praktikan', 'match' => 'dosen.daftar-praktikan', 'icon' => $iList],
                ],
                'Konten' => [
                    ['href' => route('eoffice.manprak.dosen.pengumuman.index'),                      'label' => 'Pengumuman',       'match' => 'dosen.pengumuman',       'icon' => $iBell],
                    ['href' => route('eoffice.manprak.dosen.modul.index', ['praktikumId' => 0]),    'label' => 'Daftar Modul',     'match' => 'dosen.modul',            'icon' => $iBook],
                    ['href' => route('eoffice.manprak.dosen.tugas.index'),                           'label' => 'Daftar Tugas',     'match' => 'dosen.tugas',            'icon' => $iList],
                    ['href' => route('eoffice.manprak.dosen.nilai.index', ['praktikumId' => 0]),    'label' => 'Absensi & Nilai',  'match' => 'dosen.nilai',            'icon' => $iCheck],
                ],
            ],
        ];
    }

    if ($isKoor) {
        $sections[] = [
            'label' => 'Koordinator',
            'color' => '#5D6DA2',
            'bg'    => 'rgba(99,102,241,0.08)',
            'match' => 'manprak.koor',
            'groups' => [
                'Utama' => [
                    ['href' => route('eoffice.manprak.koor.dashboard'),                'label' => 'Dashboard',        'match' => 'koor.dashboard',             'icon' => $iHome],
                ],
                'Pendaftaran' => [
                    ['href' => route('eoffice.manprak.koordinator.periode-pendaftaran.index'), 'label' => 'Buka Periode Asisten Praktikum','match' => 'koordinator.periode-pendaftaran', 'icon' => $iCal],
                    ['href' => route('eoffice.manprak.koor.pendaftaran-asprak.index'), 'label' => 'Seleksi Asisten Praktikum',   'match' => 'koor.pendaftaran-asprak',    'icon' => $iCheck],

                ],
                'Kelola' => [
                    ['href' => route('eoffice.manprak.koor.modul.index'),              'label' => 'Kelola Modul',     'match' => 'koor.modul',                 'icon' => $iBook],
                    ['href' => route('eoffice.manprak.koor.bagi-modul.index'),         'label' => 'Bagi Modul',       'match' => 'bagi-modul',                 'icon' => $iGear],
                    ['href' => route('eoffice.manprak.koor.praktikan.index'),          'label' => 'Data Praktikan',   'match' => 'koor.praktikan',             'icon' => $iUser],
                    ['href' => route('eoffice.manprak.koor.nilai.index'),              'label' => 'Absensi & Nilai',  'match' => 'koor.nilai',                 'icon' => $iCheck],
                ],
            ],
        ];
    }

    if ($isAsprak) {
        $sections[] = [
            'label' => 'Asisten Praktikum',
            'color' => '#40C4AA',
            'bg'    => 'rgba(64,196,170,0.10)',
            'match' => 'manprak.asprak',
            'groups' => [
                'Utama' => [
                    ['href' => route('eoffice.manprak.asprak.dashboard'),                'label' => 'Dashboard',        'match' => 'asprak.dashboard',        'icon' => $iHome],
                    ['href' => route('eoffice.manprak.asprak.daftar-praktikan.index'),   'label' => 'Daftar Praktikan', 'match' => 'asprak.daftar-praktikan', 'icon' => $iUser],
                ],
                'Aktivitas' => [
                    ['href' => route('eoffice.manprak.asprak.absensi.index'),      'label' => 'Absensi & Nilai', 'match' => 'asprak.absensi',     'icon' => $iCheck],
                    ['href' => route('eoffice.manprak.asprak.tugas.index'),        'label' => 'Tugas',       'match' => 'asprak.tugas',       'icon' => $iEdit],
                    ['href' => route('eoffice.manprak.asprak.materi.index'),       'label' => 'Materi',      'match' => 'asprak.materi',      'icon' => $iBook],
                    ['href' => route('eoffice.manprak.asprak.pengumuman.index'),   'label' => 'Pengumuman',  'match' => 'asprak.pengumuman',  'icon' => $iBell],
                ],
            ],
        ];
    }

    if ($isMhs) {
        $sections[] = [
            'label' => 'Mahasiswa',
            'color' => '#D39C3D',
            'bg'    => 'rgba(211,156,61,0.10)',
            'match' => 'manprak.mahasiswa',
            'groups' => [
                'Utama' => [
                    ['href' => route('eoffice.manprak.mahasiswa.dashboard'),            'label' => 'Dashboard',         'match' => 'mahasiswa.dashboard',  'icon' => $iHome],
                ],
                'Aktivitas' => [

                    ['href' => route('eoffice.manprak.mahasiswa.modul.index'),          'label' => 'Daftar Modul',      'match' => 'mahasiswa.modul',      'icon' => $iBook],
                    ['href' => route('eoffice.manprak.mahasiswa.pengumuman.index'),     'label' => 'Pengumuman',        'match' => 'mahasiswa.pengumuman', 'icon' => $iBell],
                    ['href' => route('eoffice.manprak.mahasiswa.tugas.index'),          'label' => 'Tugas',             'match' => 'mahasiswa.tugas',      'icon' => $iEdit],
                    ['href' => route('eoffice.manprak.mahasiswa.nilai.index'),          'label' => 'Absensi & Nilai',   'match' => 'mahasiswa.nilai',      'icon' => $iCheck],
                    ['href' => route('eoffice.manprak.mahasiswa.daftar-asprak.index'), 'label' => 'Daftar Asisten/Koordinator','match' => 'daftar-asprak',        'icon' => $iUser],
                ],
            ],
        ];
    }

    $multiRole    = count($sections) > 1;
    $manprakActive= str_contains($currentRoute, 'manprak');
    $notifCount   = \Modules\EOffice\Models\Notifikasi::where('user_id', $user->id)->where('is_read', false)->count();
@endphp

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('mp_sb') !== '0' }"
     x-init="$watch('sidebarOpen', v => localStorage.setItem('mp_sb', v ? '1' : '0'))">

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR                                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <aside class="flex flex-col flex-shrink-0 bg-white border-r border-[#DFE1E7] relative overflow-visible z-20 transition-all duration-[240ms] ease-[cubic-bezier(.4,0,.2,1)]"
           :class="sidebarOpen ? 'w-[240px]' : 'w-[64px]'">

        {{-- Brand --}}
        <div class="relative flex items-center" style="height: 60px; padding: 0 16px; border-bottom: 1px solid #DFE1E7;">
            <div class="flex items-center" style="gap: 8px; overflow: hidden; padding-right: 36px;">
                <div class="flex items-center justify-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="flex-1 min-w-0 transition-all duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                    <div style="font-size: 13px; font-weight: 700; color: #0D0D12; letter-spacing: -0.01em; line-height: 1;">SIPERKOM</div>
                    <div style="font-size: 10px; font-weight: 400; color: #666D80; margin-top: 2px; white-space: nowrap;">Manajemen Praktikum</div>
                </div>
            </div>
            
            <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute flex items-center justify-center cursor-pointer transition-all hover:bg-gray-50 z-30"
                    :style="sidebarOpen 
                        ? 'width: 26px; height: 26px; border-radius: 6px; border: 1px solid #DFE1E7; background-color: #ffffff; color: #666D80; top: 17px; right: 16px;' 
                        : 'width: 26px; height: 26px; border-radius: 6px; border: 1px solid #DFE1E7; background-color: #ffffff; color: #666D80; top: 17px; right: -13px;'">
                <svg class="transition-transform duration-200" :class="sidebarOpen ? '' : 'rotate-180'"
                     width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        </div>



        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto overflow-x-hidden flex flex-col [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
             style="padding: 15px 10px 10px 10px;">

            @foreach($sections as $section)
            @php
                $sectionColor  = $section['color'];
                $sectionBg     = $section['bg'];
                $sectionActive = str_contains($currentRoute, $section['match']);
            @endphp

            {{-- Section wrapper: multi-role = collapsible header, single = langsung tampil --}}
            <div x-data="{ openSec: {{ ($sectionActive || !$multiRole) ? 'true' : 'false' }} }"
                 class="mb-[2px]">

                @if($multiRole)
                {{-- Collapsible section header --}}
                <button @click="if(sidebarOpen) openSec = !openSec; else { sidebarOpen = true; openSec = true; }"
                        class="w-full flex items-center gap-[8px] px-[10px] py-[6px] rounded-[8px] border-none cursor-pointer text-left mb-[2px] transition-colors"
                        :class="sidebarOpen ? '' : 'justify-center'"
                        :style="openSec ? 'background:{{ $sectionBg }}' : 'background:transparent'"
                        style="background:transparent">
                    {{-- Color dot --}}
                    <span class="w-[7px] h-[7px] rounded-full flex-shrink-0"
                          style="background:{{ $sectionColor }}"></span>
                    <span class="text-[11px] font-bold tracking-[.04em] flex-1 overflow-hidden text-ellipsis whitespace-nowrap transition-[opacity,width] duration-200"
                          :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                          style="color:{{ $sectionColor }}">{{ strtoupper($section['label']) }}</span>
                    <svg class="w-[9px] h-[9px] flex-shrink-0 transition-transform duration-150"
                         :class="[sidebarOpen ? 'opacity-100' : 'opacity-0 w-0', openSec ? 'rotate-180' : '']"
                         viewBox="0 0 24 24" fill="none" stroke="{{ $sectionColor }}" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                @endif

                {{-- Section groups & items --}}
                <div @if($multiRole) x-show="openSec || !sidebarOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-end="opacity-0" @endif
                     @if($multiRole) class="pl-[8px] border-l-2 ml-[12px] mb-[4px]"
                     style="border-color:{{ $sectionColor }}30" @endif>

                    @foreach($section['groups'] as $groupLabel => $items)

                    {{-- Group label --}}
                    <div class="font-semibold uppercase whitespace-nowrap overflow-hidden transition-opacity duration-200"
                         style="font-size: 10px; color: #A4ACB9; letter-spacing: 0.06em; padding: 6px 10px 2px 10px;"
                         :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $groupLabel }}</div>

                    @foreach($items as $item)
                    @php $active = str_contains($currentRoute, $item['match']); @endphp
                    <a href="{{ $item['href'] }}"
                       class="relative flex items-center gap-[10px] px-[12px] py-[9px] rounded-[10px] mb-[1px] no-underline transition-colors duration-[120ms] whitespace-nowrap"
                       :class="sidebarOpen ? '' : 'justify-center'"
                       @if($active)
                           style="background:#EEF1FA; color:#293C79;"
                       @else
                           style="color:#353849;"
                           onmouseover="this.style.background='#F6F8FA'; this.style.color='#0D0D12'"
                           onmouseout="this.style.background='transparent'; this.style.color='#353849'"
                       @endif>
                        @if($active)
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-6 rounded-r-md z-10" style="background-color: #293C79; width: 3px;"></div>
                        @endif

                        <svg class="w-[18px] h-[18px] flex-shrink-0 transition-colors"
                             style="color:{{ $active ? '#293C79' : '#666D80' }}"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200
                                     {{ $active ? 'font-semibold' : 'font-medium' }}"
                              :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                    @endforeach

                </div>
            </div>

            {{-- Divider antara sections --}}
            @if(!$loop->last)
            <div class="h-px bg-[#F0F1F4] mx-[6px] my-[4px]"></div>
            @endif

            @endforeach

        </nav>

        {{-- Bottom Menu --}}
        <style>
            .b-menu-item { color: #666D80; }
            .b-menu-item:hover { color: #0D0D12; background-color: #F6F8FA; }
            .b-menu-logout { color: #EF4444; }
            .b-menu-logout:hover { color: #B91C1C; background-color: #FEF2F2; }
        </style>
        <div class="px-[10px] py-[10px] flex-shrink-0" style="border-top: 1px solid #DFE1E7;">
            <a href="{{ route('eoffice.dashboard') }}"
               class="b-menu-item flex items-center gap-[10px] px-[12px] py-[9px] rounded-[10px] mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="{{ $iBack }}"/></svg>
                <span class="text-[13px] font-medium flex-1 transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Kembali ke E-Office</span>
            </a>

            <a href="#"
               class="b-menu-item flex items-center gap-[10px] px-[12px] py-[9px] rounded-[10px] mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="{{ $iGear }}"/></svg>
                <span class="text-[13px] font-medium flex-1 transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Settings</span>
            </a>

            <a href="#"
               class="b-menu-item flex items-center gap-[10px] px-[12px] py-[9px] rounded-[10px] mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap"
               :class="sidebarOpen ? '' : 'justify-center'">
                <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span class="text-[13px] font-medium flex-1 transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Help & Center</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full m-0 p-0">
                @csrf
                <button type="submit" class="b-menu-logout w-full flex items-center gap-[10px] px-[12px] py-[9px] rounded-[10px] mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap border-none bg-transparent cursor-pointer"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="{{ $iLogout }}"/>
                    </svg>
                    <span class="text-[13px] font-medium flex-1 text-left transition-[opacity,width] duration-200" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MAIN AREA                                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <div class="flex items-center justify-between pr-8 bg-white border-b border-[#DFE1E7] flex-shrink-0" style="height:60px; padding-left:28px;">
            <div class="flex items-center gap-3 min-w-0">
                <div>
                    <div class="leading-[1.5] tracking-[0.02em]" style="color: #818898; font-size: 12px; font-weight: normal;">
                        SIPERKOM <span class="mx-[6px] text-[#D1D5DB]">/</span> MANAJEMEN PRAKTIKUM <span class="mx-[6px] text-[#D1D5DB]">/</span> <span class="text-[#0D0D12]" style="font-weight: 600;">{{ $pageTitle ?? 'Dashboard' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-5">

                {{-- ── Praktikum Switcher Kontekstual ── --}}
                @php
                    $switcherPraktikumList = collect();
                    $switcherActiveId = null;
                    $switcherContext = null;

                    // 1. Context Koordinator
                    if (str_contains($currentRoute, 'manprak.koor') || str_contains($currentRoute, 'manprak.koordinator')) {
                        if ($isKoor) {
                            $switcherContext = 'koor';
                            $switcherPraktikumList = \Modules\EOffice\Models\Praktikum::where('koor_id', $user->id)
                                ->whereIn('status', ['aktif', 'nonaktif'])
                                ->orderByRaw("status = 'aktif' DESC")
                                ->orderBy('created_at', 'desc')
                                ->get();
                            $switcherActiveId = session('koor_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    } 
                    // 2. Context Asisten Praktikum (Asprak)
                    elseif (str_contains($currentRoute, 'manprak.asprak')) {
                        if ($isAsprak) {
                            $switcherContext = 'asprak';
                            $aspraks = \Modules\EOffice\Models\AsprakPraktikum::with('praktikum')
                                ->where('user_id', $user->id)
                                ->where('role', 'asprak')
                                ->whereNull('deleted_at')
                                ->get();
                            $switcherPraktikumList = $aspraks->pluck('praktikum')->filter()->unique('id');
                            $switcherActiveId = session('manprak_asprak_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    } 
                    // 3. Context Mahasiswa (Praktikan)
                    elseif (str_contains($currentRoute, 'manprak.mahasiswa')) {
                        if ($isMhs) {
                            $switcherContext = 'mahasiswa';
                            $dps = \Modules\EOffice\Models\DaftarPraktikan::with('praktikum')
                                ->where('user_id', $user->id)
                                ->get();
                            $switcherPraktikumList = $dps->pluck('praktikum')->filter()->unique('id');
                            $switcherActiveId = session('mhs_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    }

                    $switcherActivePraktikum = $switcherPraktikumList->firstWhere('id', $switcherActiveId) ?? $switcherPraktikumList->first();
                @endphp

                @if($switcherContext && $switcherPraktikumList->count() > 0)
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-[8px] px-3 py-[7px] rounded-[9px] border border-[#DFE1E7] bg-white hover:bg-[#F6F8FA] transition-colors cursor-pointer"
                            style="max-width:260px;">
                        {{-- Status dot --}}
                        <span class="w-[7px] h-[7px] rounded-full flex-shrink-0"
                              style="background:{{ ($switcherActivePraktikum?->status === 'aktif') ? '#40C4AA' : '#A4ABB8' }};"></span>
                        <span class="text-[12px] font-semibold text-[#0D0D12] truncate" style="max-width:160px;">
                            {{ $switcherActivePraktikum?->nama ?? 'Pilih Praktikum' }}
                        </span>
                        <svg class="w-[10px] h-[10px] flex-shrink-0 text-[#A4ABB8] transition-transform duration-150"
                             :class="open ? 'rotate-180' : ''"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-[calc(100%+6px)] z-50 bg-white border border-[#DFE1E7] rounded-[12px] shadow-[0_8px_24px_rgba(0,0,0,.12)] overflow-hidden"
                         style="min-width:240px;">
                        <div class="px-4 py-[10px] border-b border-[#F0F1F4]">
                            <div class="text-[10px] font-bold text-[#A4ABB8] uppercase tracking-[.06em]">Ganti Tampilan Praktikum</div>
                        </div>
                        @foreach($switcherPraktikumList as $p)
                        @if($switcherContext === 'koor')
                        <form method="POST" action="{{ route('eoffice.manprak.koor.switch-praktikum') }}">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                        @else
                        {{-- Form GET Dinamis untuk Asprak & Mahasiswa --}}
                        <form method="GET" action="{{ url()->current() }}">
                            {{-- Mempertahankan query parameters yang sedang aktif (misal filter dll) --}}
                            @foreach(request()->except('praktikum_id') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                        @endif
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-[10px] text-left transition-colors hover:bg-[#F6F8FA] border-none cursor-pointer"
                                    style="background:{{ $p->id === $switcherActiveId ? '#EEF1FA' : 'transparent' }};">
                                <span class="w-[7px] h-[7px] rounded-full flex-shrink-0"
                                      style="background:{{ $p->status === 'aktif' ? '#40C4AA' : '#A4ABB8' }};"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-{{ $p->id === $switcherActiveId ? 'semibold' : 'medium' }} text-[#0D0D12] truncate">{{ $p->nama }}</div>
                                    <div class="text-[11px] text-[#666D80]">{{ $p->kode ?? 'Tanpa kode' }} · {{ ucfirst($p->status) }}</div>
                                </div>
                                @if($p->id === $switcherActiveId)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif
                {{-- ── /Praktikum Switcher Kontekstual ── --}}

                {{-- ── Right Actions ── --}}
                <div class="flex items-center" style="gap: 16px;">
                    {{-- Notification Bell --}}
                    <div class="relative flex items-center justify-center cursor-pointer transition-colors hover:bg-gray-50"
                         style="width: 36px; height: 36px; border-radius: 10px; border: 1px solid #DFE1E7; background-color: #ffffff; color: #666D80;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 8a6 6 0 1112 0c0 7 3 9 3 9H3s3-2 3-9" />
                            <path d="M10 21a2 2 0 004 0" />
                        </svg>
                        @if($notifCount > 0)
                        <span class="absolute rounded-full" style="top: 8px; right: 8px; width: 6px; height: 6px; background-color: #DF1C41; border: 1.5px solid #ffffff;"></span>
                        @endif
                    </div>

                    {{-- Separator --}}
                    <div style="width: 1px; height: 24px; background-color: #DFE1E7;"></div>

                    {{-- User Profile --}}
                    <div class="flex items-center" style="gap: 10px;">
                        <div class="flex items-center justify-center text-white font-bold flex-shrink-0 overflow-hidden"
                             style="width: 36px; height: 36px; border-radius: 50%; background-color: #293C79; font-size: 13px;">
                            @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $name }}" class="w-full h-full object-cover">
                            @else
                            {{ $initials }}
                            @endif
                        </div>
                        <div class="hidden sm:block">
                            <div class="font-semibold leading-tight tracking-tight" style="font-size: 13px; color: #0D0D12;">{{ $user->name }}</div>
                            <div style="font-size: 11px; color: #A4ABB8; margin-top: 2px;">
                                @if($isAdmin) Super Admin
                                @elseif($isDosen) Dosen
                                @elseif($isKoor) Koordinator
                                @elseif($isAsprak) Asisten Praktikum
                                @elseif($isMhs) Mahasiswa
                                @else User
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Box (SITKOM box/wrap pattern) --}}
        <div class="mp-wrap">
            <div class="mp-box">
                @if(isset($header))
                <div class="bg-white border-b border-[var(--c-border)] flex-shrink-0 w-full" style="padding:16px 24px;">
                    {{ $header }}
                </div>
                @endif
                <div class="mp-box-body">
                    {{-- Flash alerts --}}
                    @if(session('success'))
                    <div class="mp-flash mp-flash-success mb-4" style="border-radius:12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mp-flash mp-flash-error mb-4" style="border-radius:12px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ session('error') }}
                    </div>
                    @endif
                    @if(isset($praktikum) && $praktikum && !$praktikum->is_active && !str_contains(request()->route()?->getName() ?? '', 'manprak.admin'))
                    <div class="mp-flash mb-4 flex items-start gap-2" style="border-radius:12px; background-color:#FFFBEB; border:1px solid #FDE68A; color:#92400E; padding:12px 16px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" class="mt-0.5 flex-shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <div class="text-[13px]">
                            <strong>Praktikum Selesai (Arsip):</strong> Anda berada dalam mode <em>read-only</em>. Data historis masih dapat dilihat, tetapi tidak dapat diubah (CUD).
                        </div>
                    </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<script>
    // NProgress configuration
    NProgress.configure({ 
        showSpinner: false, 
        minimum: 0.1,
        speed: 200,          // Animation speed (ms)
        trickleSpeed: 100    // How often to trickle (ms)
    });

    // Start NProgress immediately as the page is parsing
    NProgress.start();

    // Finish NProgress when the page finishes loading
    window.addEventListener('load', () => {
        NProgress.done();
    });

    // Intercept clicks on links to show NProgress
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.href.includes('javascript:') && !link.href.startsWith('#') && link.target !== '_blank') {
            // Check if it's the same page anchor
            const url = new URL(link.href, window.location.href);
            if (url.pathname === window.location.pathname && url.hash) {
                return; // Same page anchor, don't show loading
            }
            NProgress.start();
        }
    });

    // Intercept form submissions
    document.addEventListener('submit', function() {
        NProgress.start();
    });

    // Handle back/forward cache (bfcache)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            NProgress.done();
        }
    });
</script>
</body>
</html>
