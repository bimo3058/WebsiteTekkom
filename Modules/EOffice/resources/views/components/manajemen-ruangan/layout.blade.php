<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Manajemen Ruangan' }} — SIPERKOM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'], 'build-eoffice')
    <style>
        [x-cloak] { display: none !important; }
        /* ─── SITKOM Design System — ManajemenRuangan component layer ─── */

        /* Box / Wrap (superadmin pattern) */
        .mp-wrap {
            flex: 1;
            overflow: hidden;
            padding: 10px;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .mp-box {
            flex: 1;
            min-height: 0;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .mp-box-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            scrollbar-width: thin;
            scrollbar-color: var(--c-border-strong) transparent;
        }

        @media (max-width: 767px) {
            .mp-box-body {
                padding-bottom: 80px;
            }
        }

        .mp-box-body::-webkit-scrollbar {
            width: 5px;
        }

        .mp-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        /* Flash */
        .mp-flash {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            flex-shrink: 0;
            border-bottom: 1px solid transparent;
        }

        .mp-flash-success {
            background: #DDF2EE;
            color: #174E43;
            border-color: #40C4AA;
        }

        .mp-flash-error {
            background: #FADAE1;
            color: #7C1028;
            border-color: #DF1C41;
        }

        /* Page header (like superadmin _header) */
        .mp-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .mp-page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--c-fg);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0;
        }

        .mp-page-sub {
            font-size: 12px;
            color: var(--c-fg-muted);
            margin-top: 3px;
        }

        .mp-page-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Badges */
        .mp-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 9999px;
            letter-spacing: 0.01em;
            line-height: 1;
            white-space: nowrap;
            font-family: 'Inter Tight', sans-serif;
        }

        .mp-badge.sm {
            font-size: 11px;
            padding: 2px 7px;
        }

        .mp-badge.lg {
            font-size: 13px;
            padding: 4px 10px;
        }

        .mp-badge.primary {
            background: rgba(11, 38, 110, 0.08);
            color: #0B266E;
        }

        .mp-badge.success {
            background: #DDF2EE;
            color: #174E43;
        }

        .mp-badge.danger {
            background: #FADAE1;
            color: #710E21;
        }

        .mp-badge.secondary {
            background: #E2E8F0;
            color: #475569;
        }

        /* Buttons */
        .mp-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Inter Tight', sans-serif;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all .15s;
            letter-spacing: 0.01em;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
        }

        .mp-btn.lg {
            padding: 10px 18px;
            font-size: 14px;
        }

        .mp-btn.md {
            padding: 8px 14px;
            font-size: 13px;
        }

        .mp-btn.sm {
            padding: 6px 10px;
            font-size: 12px;
        }

        .mp-btn.primary {
            background: #0B266E;
            color: #fff;
            box-shadow: 0 2px 6px rgba(11, 38, 110, .22);
        }

        .mp-btn.primary:hover {
            background: #091958;
            box-shadow: 0 4px 12px rgba(11, 38, 110, .3);
        }

        .mp-btn.secondary {
            background: #fff;
            color: var(--c-fg-sec);
            border: 1px solid var(--c-border);
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .mp-btn.secondary:hover {
            background: var(--c-bg);
            border-color: var(--c-border-strong);
        }

        .mp-btn.danger {
            background: #DF1C41;
            color: #fff;
        }

        .mp-btn.danger:hover {
            background: #95122B;
        }

        /* Table / Card containers */
        .mp-card {
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 14px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            width: 100%;
            min-width: 0;
            overflow: visible;
        }

        .mp-card-header {
            padding: 14px 18px;
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            border-radius: 14px 14px 0 0;
        }

        .mp-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--c-fg);
            margin: 0;
        }

        .mp-card-header .right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mp-card-body {
            flex: 1;
            /* Allow natural content flow. Let .mp-box-body handle the full scroll instead of clipping internally */
            overflow: visible;
        }

        .mp-th {
            font-size: 11px;
            font-weight: 600;
            color: var(--c-fg-placeholder);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .mp-tr {
            border-bottom: 1px solid #F8F9FB;
        }

        .mp-tr:last-child {
            border-bottom: none;
        }

        .mp-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mp-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mp-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--c-fg-placeholder);
            text-transform: uppercase;
            letter-spacing: .06em;
            background: #F8F9FB;
            border-bottom: 1px solid var(--c-border);
            white-space: nowrap;
        }

        .mp-table td {
            padding: 10px 14px;
            text-align: left;
            font-size: 13px;
            color: var(--c-fg);
            border-bottom: 1px solid #F2F4F7;
            white-space: nowrap;
            vertical-align: middle;
        }

        .mp-table tbody tr:last-child td {
            border-bottom: none;
        }

        .mp-table tbody tr:hover td {
            background: #FAFBFC;
        }

        /* Stats Grid */
        .mp-stats-grid {
            display: grid;
            gap: 16px;
            min-width: 0;
        }

        .mp-stats-grid.cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        .mp-stats-grid.cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .mp-stat {
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
        }

        .mp-stat-icon {
            position: absolute;
            top: 16px;
            right: 20px;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mp-stat-icon.violet {
            background: linear-gradient(135deg, #f3f0ff, #e4dffd);
            color: #2A3A7C;
        }

        .mp-stat-icon.sky {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            color: #0284c7;
        }

        .mp-stat-icon.yellow {
            background: linear-gradient(135deg, #fefce8, #fef08a);
            color: #ca8a04;
        }

        .mp-stat-icon.green {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #16a34a;
        }

        .mp-stat-icon.red {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
        }

        .mp-stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--c-fg-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .mp-stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--c-fg);
            line-height: 1;
            letter-spacing: -0.02em;
            font-family: 'Inter Tight', sans-serif;
        }

        .mp-stat-sub {
            font-size: 12px;
            color: var(--c-fg-sec);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        @media(max-width:1024px) {

            .mp-stats-grid.cols-4,
            .mp-stats-grid.cols-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:640px) {

            .mp-stats-grid.cols-4,
            .mp-stats-grid.cols-3 {
                grid-template-columns: 1fr;
            }
        }

        /* Form inputs */
        .mp-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--c-border);
            border-radius: 8px;
            font-size: 13px;
            color: var(--c-fg);
            font-family: 'Inter Tight', sans-serif;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #fff;
        }

        .mp-input:focus {
            border-color: #0B266E;
            box-shadow: 0 0 0 3px rgba(11, 38, 110, .08);
        }

        /* Section title */
        .sec-head {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .sec-bar {
            width: 4px;
            height: 22px;
            background: #0B266E;
            border-radius: 19px;
            flex-shrink: 0;
        }

        .sec-title {
            font-family: 'Inter Tight', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #0D0D12;
            white-space: nowrap;
        }

        .sec-rule {
            flex: 1;
            height: 1px;
            background: #DFE1E7;
        }

        /* Avatar */
        .mp-av {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font: 700 11px/1 'Inter Tight', sans-serif;
            flex-shrink: 0;
        }

        .mp-av.violet {
            background: #E4DFFD;
            color: #2A3A7C;
        }
    </style>
    <x-mobile-navigation-assets />
</head>

<body class="h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased"
    style="font-family:'Inter Tight',system-ui,sans-serif;">

    @php
        $user = auth()->user();
        $name = $user?->name ?? 'User';
        $initials = strtoupper(substr($name, 0, 1));
        $sp = strpos($name, ' ');
        if ($sp !== false)
            $initials .= strtoupper(substr($name, $sp + 1, 1));
        $currentRoute = request()->route()?->getName() ?? '';

        // Simplified Role Checks for Manajemen Ruangan
        $isAdmin = $user && ($user->hasRole('superadmin') || $user->hasRole('admin_eoffice'));
        $multiRole = false;

        // Icons
        $iHome = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
        $iUser = "M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z";
        $iCal = "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z";
        $iBook = "M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z";
        $iLogout = "M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.94L7.87 4.06C6.39 3.66 5 5.21 5 7.27v9.46C5 18.79 6.39 20.34 7.87 19.94l3.2-.87C12.19 18.76 13 17.42 13 15.86v-.59M11 12h8M19 12l-2.5-2.72M19 12l-2.5 2.72";
        $iBack = "M19 12H5M5 12l7-7M5 12l7 7";
        $iList = "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2";
        $iBlock = "M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z";
        $iKey = "M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L6.5 21.5H3v-3.5l1.5-1.5v-2l1.5-1.5 2-2 2.257-2.257A6 6 0 1121 9z";

        // Query Menu Visibility Settings for Sidebar
        $rawSettings = \Modules\EOffice\Models\Pengaturan::where('key', 'like', 'sb_%')->pluck('value', 'key')->toArray();
        $sb_katalog = filter_var($rawSettings['sb_user_katalog'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_kalender = filter_var($rawSettings['sb_user_kalender'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_peminjaman = filter_var($rawSettings['sb_user_peminjaman'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_riwayat = filter_var($rawSettings['sb_user_riwayat'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $isSuperadmin = $user && $user->hasRole('superadmin');

        $sb_adm_klg = $isSuperadmin || filter_var($rawSettings['sb_admin_kalenderglobal'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_jad = $isSuperadmin || filter_var($rawSettings['sb_admin_jadwalakademik'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_evt = $isSuperadmin || filter_var($rawSettings['sb_admin_event'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_set = $isSuperadmin || filter_var($rawSettings['sb_admin_persetujuan'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_ars = $isSuperadmin || filter_var($rawSettings['sb_admin_arsip'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_fas = $isSuperadmin || filter_var($rawSettings['sb_admin_manajemenfasilitas'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_rua = $isSuperadmin || filter_var($rawSettings['sb_admin_manajemenruangan'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $sb_adm_pgt = $isSuperadmin || filter_var($rawSettings['sb_admin_pengaturan'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $sections = [];

        if ($isAdmin) {
            $admGroups = [];

            $admGroups['Utama'] = [
                ['href' => route('eoffice.peminjaman.dashboard'), 'label' => 'Dashboard', 'match' => 'peminjaman.dashboard', 'icon' => $iHome],
            ];

            $admSisRuangan = [];
            if ($sb_adm_klg)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.kalender-global.index'), 'label' => 'Kalender Global', 'match' => 'admin.kalender', 'icon' => $iCal];
            if ($sb_adm_jad)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.jadwal-akademik.index'), 'label' => 'Jadwal Akademik', 'match' => 'admin.jadwal-akademik', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'];
            if ($sb_adm_evt)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.jadwal-internal.index'), 'label' => 'Blokir Ruangan', 'match' => 'admin.jadwal-internal', 'icon' => $iBlock];
            if ($sb_adm_set)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.persetujuan.index'), 'label' => 'Persetujuan', 'match' => 'admin.persetujuan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'];
            if ($sb_adm_ars)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.riwayat.index'), 'label' => 'Arsip & Rekap', 'match' => 'admin.riwayat', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
            if (count($admSisRuangan) > 0)
                $admGroups['Sistem Ruangan'] = $admSisRuangan;

            $admMasterData = [];
            if ($sb_adm_rua) {
                $admMasterData[] = ['href' => route('eoffice.peminjaman.admin.ruangan.index'), 'label' => 'Manajemen Ruangan', 'match' => 'admin.ruangan', 'icon' => $iBook];
            }
            if ($sb_adm_fas) {
                $admMasterData[] = ['href' => route('eoffice.peminjaman.admin.fasilitas.index'), 'label' => 'Manajemen Fasilitas', 'match' => 'admin.fasilitas', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'];
            }
            $admMasterData[] = ['href' => route('eoffice.peminjaman.admin.hak-akses.index'), 'label' => 'Hak Akses Menu', 'match' => 'admin.hak-akses', 'icon' => $iKey];

            if (count($admMasterData) > 0)
                $admGroups['Master Data'] = $admMasterData;

            $admSistemWeb = [];
            if ($sb_adm_pgt)
                $admSistemWeb[] = ['href' => route('eoffice.peminjaman.admin.pengaturan.index'), 'label' => 'Pengaturan Operasional', 'match' => 'admin.pengaturan', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'];

            if (count($admSistemWeb) > 0)
                $admGroups['Sistem Web'] = $admSistemWeb;

            $sections[] = [
                'label' => 'Admin Ruangan',
                'color' => '#10B981',
                'bg' => 'rgba(16, 185, 129, 0.08)',
                'match' => 'peminjaman.admin',
                'groups' => $admGroups,
            ];
        } else {
            // General User / Mahasiswa / Dosen View

            $userGroups = [];

            $userGroups['Utama'] = [
                ['href' => route('eoffice.peminjaman.dashboard'), 'label' => 'Dashboard', 'match' => 'peminjaman.dashboard', 'icon' => $iHome],
            ];

            $sistemRuangan = [];
            if ($sb_katalog)
                $sistemRuangan[] = ['href' => route('eoffice.peminjaman.user.booking'), 'label' => 'Katalog Ruangan', 'match' => 'user.booking', 'icon' => $iBook];
            if ($sb_kalender)
                $sistemRuangan[] = ['href' => route('eoffice.peminjaman.user.kalender'), 'label' => 'Kalender Ruangan', 'match' => 'user.kalender', 'icon' => $iCal];
            if (count($sistemRuangan) > 0)
                $userGroups['Sistem Ruangan'] = $sistemRuangan;

            $peminjamanItems = [];
            if ($sb_peminjaman)
                $peminjamanItems[] = ['href' => route('eoffice.peminjaman.user.saya'), 'label' => 'Peminjaman Saya', 'match' => 'user.saya', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
            if ($sb_riwayat)
                $peminjamanItems[] = ['href' => route('eoffice.peminjaman.user.riwayat'), 'label' => 'Riwayat', 'match' => 'user.riwayat', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
            if (count($peminjamanItems) > 0)
                $userGroups['Peminjaman'] = $peminjamanItems;



            $sections[] = [
                'label' => 'Akses Mahasiswa',
                'color' => '#3B82F6',
                'bg' => 'rgba(59, 130, 246, 0.08)',
                'match' => 'peminjaman.user',
                'groups' => $userGroups,
            ];
        }
    @endphp

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('mr_sb') !== '0' }"
        x-init="$watch('sidebarOpen', v => localStorage.setItem('mr_sb', v ? '1' : '0'))">

        {{-- SIDEBAR --}}
        <aside
            class="hidden md:flex flex-col flex-shrink-0 w-[240px] bg-white border-r border-[#DFE1E7] relative overflow-visible z-20 transition-all duration-[240ms] ease-[cubic-bezier(.4,0,.2,1)]"
            :class="sidebarOpen ? '' : '!w-[64px]'">

            <div class="relative px-[14px] h-[60px] flex items-center border-b border-[#DFE1E7] flex-shrink-0 transition-all duration-200"
                :class="sidebarOpen ? 'gap-[8px]' : 'justify-center'">
                <div class="flex items-center justify-center w-[32px] h-[32px] flex-shrink-0">
                    <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP"
                        class="w-full h-full object-contain drop-shadow-sm">
                </div>
                <div class="flex-1 min-w-0 overflow-hidden" x-show="sidebarOpen" x-transition.opacity.duration.200ms>
                    <div
                        class="font-bold text-[14px] text-[#0D0D12] leading-[1.2] whitespace-nowrap tracking-[-0.01em]">
                        SIPERKOM</div>
                    <div class="text-[9px] font-medium text-[#808897] whitespace-nowrap mt-[2px]">Manajemen Ruangan
                        Teknik Komputer
                    </div>
                </div>

                {{-- Floating collapse button --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-[-14px] top-1/2 -translate-y-1/2 flex items-center justify-center w-[28px] h-[28px] rounded-[7px] bg-white border border-[#DFE1E7] shadow-[0_1px_4px_rgba(0,0,0,.05)] cursor-pointer z-30 text-[#64748B] hover:bg-[#F8FAFC] hover:text-[#0F172A] transition-colors"
                    title="Toggle Sidebar">
                    <svg class="transition-transform duration-[240ms]" :class="sidebarOpen ? '' : 'rotate-180'"
                        width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                </button>
            </div>

            <nav
                class="flex-1 overflow-y-auto overflow-x-hidden px-[10px] py-1 flex flex-col [scrollbar-width:thin] [scrollbar-color:#DFE1E7_transparent] [&::-webkit-scrollbar]:w-[3px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-[#DFE1E7] [&::-webkit-scrollbar-thumb]:rounded-full">
                @foreach($sections as $section)
                    @php
                        $sectionColor = $section['color'];
                        $sectionActive = str_contains($currentRoute, $section['match']);
                    @endphp
                    <div class="mb-[2px]">
                        <div>
                            @foreach($section['groups'] as $groupLabel => $items)
                                <div class="text-[11px] font-semibold text-[#64748b] uppercase tracking-[.05em] px-[14px] pt-[16px] pb-[6px] whitespace-nowrap overflow-hidden transition-opacity duration-200"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $groupLabel }}</div>

                                @foreach($items as $item)
                                    @php $active = str_contains($currentRoute, $item['match']); @endphp
                                    <a href="{{ $item['href'] }}"
                                        class="group relative flex items-center gap-[9px] pl-[14px] pr-[10px] py-[7px] rounded-[8px] mb-[2px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap {{ $active ? 'bg-[#EEF2FF] text-[#0B266E] font-semibold' : 'text-[#475569] font-medium hover:bg-[#F8FAFC]' }}"
                                        :class="sidebarOpen ? '' : '!gap-0 justify-center !px-0'">
                                        @if($active)
                                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-[20px] bg-[#0B266E] rounded-r-[3px]"
                                                x-show="sidebarOpen"></div>
                                        @endif
                                        <svg class="w-[16px] h-[16px] flex-shrink-0 transition-colors {{ $active ? 'text-[#0B266E]' : 'text-[#94A3B8]' }}"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="{{ $item['icon'] }}" />
                                        </svg>
                                        <span class="text-[13px] flex-1 overflow-hidden text-ellipsis"
                                            x-show="sidebarOpen">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="h-px bg-[#F0F1F4] mx-[6px] my-[4px]"></div>
                    @endif
                @endforeach
            </nav>

            {{-- Bottom fixed section: Dasbor Utama, Pengaturan Profil, Keluar --}}
            <div class="flex-shrink-0 border-t border-[#DFE1E7] px-[10px] py-[8px] pb-[12px]">

                {{-- Kembali ke EOffice --}}
                <a href="{{ url('/eoffice/dashboard') }}"
                    class="relative flex items-center gap-[9px] w-full min-h-[36px] px-[10px] py-[8px] pl-[14px] rounded-[8px] no-underline transition-colors duration-[120ms] hover:bg-[#F6F8FA] text-[#353849] font-medium text-[13px] leading-[1.4] whitespace-nowrap"
                    :class="sidebarOpen ? '' : '!gap-0 justify-center !px-0 !pl-0'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0 text-[#94A3B8]" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5m7-7-7 7 7 7" />
                    </svg>
                    <span class="min-w-0 overflow-hidden text-ellipsis" x-show="sidebarOpen">Kembali ke Eoffice</span>
                </a>

                {{-- Pengaturan Profil --}}
                <a href="{{ url('/profile') }}"
                    class="relative flex items-center gap-[9px] w-full min-h-[36px] px-[10px] py-[8px] pl-[14px] rounded-[8px] no-underline transition-colors duration-[120ms] hover:bg-[#F6F8FA] text-[#353849] font-medium text-[13px] leading-[1.4] whitespace-nowrap"
                    :class="sidebarOpen ? '' : '!gap-0 justify-center !px-0 !pl-0'">
                    <svg class="w-[16px] h-[16px] flex-shrink-0 text-[#94A3B8]" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a7 7 0 0 0-14 0v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8" />
                    </svg>
                    <span class="min-w-0 overflow-hidden text-ellipsis" x-show="sidebarOpen">Pengaturan Profil</span>
                </a>

                {{-- Keluar --}}
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit"
                        class="relative flex items-center gap-[9px] w-full min-h-[36px] px-[10px] py-[8px] pl-[14px] rounded-[8px] transition-colors duration-[120ms] hover:bg-[#FEF1F4] text-[#DF1C41] font-medium text-[13px] leading-[1.4] whitespace-nowrap bg-transparent border-none cursor-pointer text-left"
                        :class="sidebarOpen ? '' : '!gap-0 justify-center !px-0 !pl-0'">
                        <svg class="w-[16px] h-[16px] flex-shrink-0 text-[#DF1C41]" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9" />
                        </svg>
                        <span class="min-w-0 overflow-hidden text-ellipsis" x-show="sidebarOpen">Keluar</span>
                    </button>
                </form>
            </div>


        </aside>

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            <div
                class="flex items-center justify-between px-[14px] md:pl-[28px] md:pr-8 bg-white border-b border-[#DFE1E7] flex-shrink-0 h-[52px] md:h-[60px] transition-all">
                <div class="flex items-center gap-3 min-w-0">
                    <div>
                        <div class="font-bold text-[14px] md:text-[15px] text-[#0D0D12] leading-[1.2] truncate">
                            {{ $pageTitle ?? 'Dashboard' }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 md:gap-5">
                    {{-- ── Right Actions ── --}}
                    <div class="flex items-center gap-2 md:gap-4">
                        {{-- Notification Bell --}}
                        <div class="relative" x-data="{ openNotif: false }" @click.outside="openNotif = false">
                            <button @click="openNotif = !openNotif" type="button"
                                class="relative flex items-center justify-center cursor-pointer transition-colors hover:bg-gray-50 rounded-[10px] border border-[#DFE1E7] bg-white text-[#666D80] w-[30px] h-[30px] md:w-[36px] md:h-[36px]">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                @php
                                    $unreadNotifications = $user->unreadNotifications;
                                    $notifCount = $unreadNotifications->count();
                                @endphp
                                @if($notifCount > 0)
                                    <span id="notif-badge" class="absolute flex items-center justify-center rounded-full min-w-[18px] h-[18px] px-[4px] bg-[#F43F5E] border-2 border-white text-white text-[10px] font-bold top-[-5px] right-[-5px] shadow-sm leading-none">
                                        {{ $notifCount > 99 ? '99+' : $notifCount }}
                                    </span>
                                @endif
                            </button>

                            {{-- Dropdown Notifikasi --}}
                            <div x-show="openNotif" x-transition.opacity.duration.200ms
                                class="absolute right-0 mt-2 w-[340px] bg-white border border-[#DFE1E7] rounded-[16px] shadow-lg overflow-hidden z-[99]"
                                style="display: none;">
                                <div class="px-4 py-3 border-b border-[#DFE1E7] flex justify-between items-start bg-white">
                                    <div class="flex flex-col gap-1">
                                        <h3 class="font-bold text-[13px] text-gray-900 leading-none mt-0.5">Notifikasi</h3>
                                        <p class="text-[11px] text-gray-500">Aktivitas Terkini</p>
                                    </div>
                                    @if($notifCount > 0)
                                    <form method="POST" action="{{ route('eoffice.peminjaman.user.notifikasi.read-all') }}" class="m-0" id="mark-all-read-form">
                                        @csrf
                                        <button type="submit" class="text-[12px] text-[#0B266E] hover:underline cursor-pointer bg-transparent border-none p-0">Tandai semua dibaca</button>
                                    </form>
                                    @endif
                                </div>
                                <div class="max-h-[350px] overflow-y-auto bg-white">
                                    @forelse($user->notifications()->limit(5)->get() as $notification)
                                        <form method="POST" action="{{ route('eoffice.peminjaman.user.notifikasi.read', $notification->id) }}" class="m-0 border-b border-[#F0F1F4] last:border-b-0">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-3 transition-colors cursor-pointer {{ $notification->read_at ? 'bg-white opacity-60 hover:bg-gray-50' : 'bg-[#EFF6FF] hover:bg-[#E0F2FE] unread-item' }}">
                                                <div class="flex flex-col gap-1">
                                                    <div class="flex justify-between items-start gap-2">
                                                        <h4 class="text-[13px] font-bold text-gray-900">{{ $notification->data['title'] ?? 'Pemberitahuan Sistem' }}</h4>
                                                        @if(!$notification->read_at)
                                                            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1 blue-dot"></span>
                                                        @endif
                                                    </div>
                                                    @php
                                                        $notifMessage = htmlspecialchars($notification->data['message'] ?? 'Pemberitahuan Baru');
                                                        $notifMessage = preg_replace('/(disetujui)/i', '<span class="font-bold text-emerald-600">$1</span>', $notifMessage);
                                                        $notifMessage = preg_replace('/(ditolak)/i', '<span class="font-bold text-rose-600">$1</span>', $notifMessage);
                                                        $notifMessage = preg_replace('/(dibatalkan(?: oleh admin)?)/i', '<span class="font-bold text-rose-600">$1</span>', $notifMessage);
                                                    @endphp
                                                    <p class="text-[12px] text-gray-600 leading-snug">{!! $notifMessage !!}</p>
                                                    <span class="text-[11px] text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                            </button>
                                        </form>
                                    @empty
                                        <div class="p-5 text-center text-[12px] text-gray-500">
                                            Belum ada notifikasi
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Separator --}}
                        <div class="hidden md:block w-px h-[24px] bg-[#DFE1E7]"></div>

                        {{-- User Profile --}}
                        <div class="flex items-center gap-1.5 md:gap-2.5">
                            <div class="flex items-center justify-center text-white font-bold flex-shrink-0 overflow-hidden rounded-full w-[30px] h-[30px] text-[10px] md:w-[36px] md:h-[36px] md:text-[13px]"
                                style="background: linear-gradient(135deg, #1F2937, #111827);">
                                {{ $initials }}
                            </div>
                            <div class="hidden md:flex flex-col min-w-[100px]">
                                <span class="text-[13px] font-semibold text-[#0D0D12] truncate">{{ $name }}</span>
                                <span class="text-[11px] text-[#666D80] truncate">{{ $user?->email ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mp-wrap">
                <div class="mp-box">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                            x-transition.opacity.duration.300ms class="mp-flash mp-flash-success"
                            style="justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2" stroke-linecap="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                {{ session('success') }}
                            </div>
                            <button @click="show = false"
                                style="background: transparent; border: none; cursor: pointer; color: inherit; padding: 0; display: flex; align-items: center; opacity: 0.6;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                            x-transition.opacity.duration.300ms class="mp-flash mp-flash-error"
                            style="justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                {{ session('error') }}
                            </div>
                            <button @click="show = false"
                                style="background: transparent; border: none; cursor: pointer; color: inherit; padding: 0; display: flex; align-items: center; opacity: 0.6;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                            x-transition.opacity.duration.300ms class="mp-flash mp-flash-error"
                            style="justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span>Terdapat kesalahan pada input Anda. Mohon periksa kembali form.</span>
                            </div>
                            <button @click="show = false"
                                style="background: transparent; border: none; cursor: pointer; color: inherit; padding: 0; display: flex; align-items: center; opacity: 0.6;"
                                onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <div class="mp-box-body">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!$isAdmin)
        @include('eoffice::components.manajemen-ruangan.sidebar-mobile', [
            'user' => $user,
            'rawSettings' => $rawSettings,
            'currentRoute' => $currentRoute
        ])
    @else
        @include('eoffice::components.manajemen-ruangan.sidebar-admin-mobile', [
            'user' => $user,
            'rawSettings' => $rawSettings,
            'currentRoute' => $currentRoute
        ])
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markAllForm = document.getElementById('mark-all-read-form');
            if (markAllForm) {
                markAllForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Optimistic UI updates
                    // 1. Hilangkan badge angka merah di atas lonceng
                    const badge = document.getElementById('notif-badge');
                    if (badge) badge.style.display = 'none';
                    
                    // 2. Ubah semua notif biru terang menjadi abu-abu pudar
                    const unreadItems = document.querySelectorAll('.unread-item');
                    unreadItems.forEach(item => {
                        item.classList.remove('bg-[#EFF6FF]', 'hover:bg-[#E0F2FE]', 'unread-item');
                        item.classList.add('bg-white', 'opacity-60', 'hover:bg-gray-50');
                        
                        // Sembunyikan titik biru (dot)
                        const dot = item.querySelector('.blue-dot');
                        if (dot) dot.style.display = 'none';
                    });
                    
                    // 3. Sembunyikan tombol "Tandai semua dibaca"
                    markAllForm.style.display = 'none';
                    
                    // 4. Sembunyikan pill merah "X Baru" di header dropdown (jika ada)
                    const newCountPill = document.querySelector('span.bg-\\[\\#DF1C41\\]');
                    if (newCountPill) newCountPill.style.display = 'none';

                    // 5. Kirim request di belakang layar
                    fetch(markAllForm.action, {
                        method: 'POST',
                        body: new FormData(markAllForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    }).catch(err => console.error(err));
                });
            }

            // AJAX Polling khusus Notifikasi tiap 10 detik
            setInterval(() => {
                fetch('{{ route('eoffice.peminjaman.user.notifikasi.count') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    const count = data.count;
                    let badge = document.getElementById('notif-badge');
                    
                    if (count > 0) {
                        let displayCount = count > 99 ? '99+' : count;
                        if (badge) {
                            badge.innerText = displayCount;
                            badge.style.display = 'flex';
                        } else {
                            // Buat badge jika belum ada (dari state 0 ke >0)
                            const btn = document.querySelector('[x-data="{ openNotif: false }"] button');
                            if (btn) {
                                btn.insertAdjacentHTML('beforeend', `<span id="notif-badge" class="absolute flex items-center justify-center rounded-full min-w-[18px] h-[18px] px-[4px] bg-[#F43F5E] border-2 border-white text-white text-[10px] font-bold top-[-5px] right-[-5px] shadow-sm leading-none">${displayCount}</span>`);
                            }
                        }
                    } else {
                        // Hilangkan jika 0
                        if (badge) badge.style.display = 'none';
                    }
                })
                .catch(err => console.error('Notif Polling Error:', err));
            }, 10000);
        });
    </script>
</body>
</html>