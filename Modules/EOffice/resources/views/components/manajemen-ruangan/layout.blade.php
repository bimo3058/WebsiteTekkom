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
            color: #6B39F4;
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
            color: #6B39F4;
        }
    </style>
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
        $sb_adm_usu = $isSuperadmin || filter_var($rawSettings['sb_admin_manajemenuser'] ?? true, FILTER_VALIDATE_BOOLEAN);
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
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.jadwal-internal.index'), 'label' => 'Event & Maintenance', 'match' => 'admin.jadwal-internal', 'icon' => $iList];
            if ($sb_adm_set)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.persetujuan.index'), 'label' => 'Persetujuan', 'match' => 'admin.persetujuan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'];
            if ($sb_adm_ars)
                $admSisRuangan[] = ['href' => route('eoffice.peminjaman.admin.riwayat.index'), 'label' => 'Arsip & Rekap', 'match' => 'admin.riwayat', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
            if (count($admSisRuangan) > 0)
                $admGroups['Sistem Ruangan'] = $admSisRuangan;

            $admMasterData = [];
            if ($sb_adm_usu)
                $admMasterData[] = ['href' => route('eoffice.peminjaman.admin.user.index'), 'label' => 'Manajemen User', 'match' => 'admin.user', 'icon' => $iUser];
            if ($sb_adm_rua)
                $admMasterData[] = ['href' => route('eoffice.peminjaman.admin.ruangan.index'), 'label' => 'Manajemen Ruangan', 'match' => 'admin.ruangan', 'icon' => $iBook];
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

            $userGroups['Akun'] = [
                ['href' => '#', 'label' => 'Profil', 'match' => 'user.profil', 'icon' => $iUser],
            ];

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
            class="flex flex-col flex-shrink-0 bg-white border-r border-[#DFE1E7] relative overflow-visible z-20 transition-all duration-[240ms] ease-[cubic-bezier(.4,0,.2,1)]"
            :class="sidebarOpen ? 'w-[272px]' : 'w-[64px]'">

            <div class="relative px-[10px] pt-[18px] pb-[10px]">
                <div class="flex items-center gap-[10px] px-[10px] py-2 rounded-[10px]">
                    <div
                        class="flex items-center justify-center w-[34px] h-[34px] rounded-[9px] flex-shrink-0 bg-[#F3F4F6] shadow-sm overflow-hidden border border-[#E5E7EB]">
                        {{-- Ruangan Icon --}}
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2"
                            stroke-linecap="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        <div class="font-bold text-[13px] text-[#0D0D12] leading-[1.2] whitespace-nowrap">SIPERKOM</div>
                        <div
                            class="text-[9px] font-semibold text-[#10B981] uppercase tracking-[.04em] whitespace-nowrap">
                            Man. Ruangan</div>
                    </div>
                </div>
                <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-[-12px] top-[34px] flex items-center justify-center w-6 h-6 rounded-full bg-white border border-[#DFE1E7] shadow-[0_1px_4px_rgba(0,0,0,.08)] cursor-pointer z-30 hover:bg-[#F6F8FA]">
                    <svg class="transition-transform duration-[240ms]" :class="sidebarOpen ? '' : 'rotate-180'"
                        width="8" height="8" viewBox="0 0 10 10" fill="none" stroke="#666D80" stroke-width="2.2"
                        stroke-linecap="round">
                        <path d="M7 1L3 5L7 9" />
                    </svg>
                </button>
            </div>

            <nav
                class="flex-1 overflow-y-auto overflow-x-hidden px-[10px] py-1 flex flex-col [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach($sections as $section)
                    @php
                        $sectionColor = $section['color'];
                        $sectionActive = str_contains($currentRoute, $section['match']);
                    @endphp
                    <div class="mb-[2px]">
                        <div>
                            @foreach($section['groups'] as $groupLabel => $items)
                                <div class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-[.06em] px-[10px] pt-[6px] pb-[2px] whitespace-nowrap overflow-hidden transition-opacity duration-200"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">{{ $groupLabel }}</div>

                                @foreach($items as $item)
                                    @php $active = str_contains($currentRoute, $item['match']); @endphp
                                    <a href="{{ $item['href'] }}"
                                        class="group relative flex items-center gap-[10px] px-[12px] py-[9px] rounded-lg mb-[1px] no-underline transition-colors duration-[120ms] overflow-hidden whitespace-nowrap {{ $active ? 'bg-[#F1F5F9] text-[#0B266E] font-bold' : 'text-[#353849] font-medium hover:bg-[#F6F8FA]' }}"
                                        :class="sidebarOpen ? '' : 'justify-center'">
                                        @if($active)
                                            <div class="absolute left-0 top-[15%] bottom-[15%] w-[4px] bg-[#0B266E] rounded-r-sm"></div>
                                        @endif
                                        <svg class="w-[18px] h-[18px] flex-shrink-0 transition-colors {{ $active ? 'text-[#0B266E]' : 'text-[#808897]' }}"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="{{ $item['icon'] }}" />
                                        </svg>
                                        <span
                                            class="text-[13px] flex-1 overflow-hidden text-ellipsis transition-[opacity,width] duration-200"
                                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="h-px bg-[#F0F1F4] mx-[6px] my-[4px]"></div>
                    @endif
                @endforeach

                <div class="h-px bg-[#F0F1F4] mx-[14px] my-[6px]"></div>
                <a href="{{ route('eoffice.dashboard') }}"
                    class="flex items-center gap-[10px] px-[10px] py-[9px] rounded-lg no-underline transition-colors hover:bg-[#F6F8FA] text-[#666D80]"
                    :class="sidebarOpen ? '' : 'justify-center'">
                    <svg class="w-[14px] h-[14px] flex-shrink-0 text-[#A4ABB8]" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="{{ $iBack }}" />
                    </svg>
                    <span class="text-[12px] font-medium flex-1 transition-[opacity,width] duration-200"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Kembali ke EOffice Utama</span>
                </a>
            </nav>

            <div class="px-3 py-[10px] border-t border-[#DFE1E7] flex-shrink-0">
                <div
                    class="flex items-center gap-[10px] px-[10px] py-2 rounded-lg overflow-hidden transition-colors hover:bg-[#F6F8FA]">
                    <div class="flex items-center justify-center w-[30px] h-[30px] rounded-full flex-shrink-0 text-white text-[11px] font-bold"
                        style="background:linear-gradient(135deg,#1F2937,#111827);">{{ $initials }}</div>
                    <div class="flex-1 min-w-0 overflow-hidden transition-[opacity,width] duration-200"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                        <div
                            class="text-[12px] font-semibold text-[#0D0D12] whitespace-nowrap overflow-hidden text-ellipsis leading-[1.2]">
                            {{ $name }}
                        </div>
                        <div class="text-[10px] text-[#666D80] whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $user?->email ?? '' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0"
                        :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'">
                        @csrf
                        <button type="submit"
                            class="p-1 rounded text-[#A4ABB8] hover:text-red-500 bg-transparent border-none cursor-pointer">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round">
                                <path d="{{ $iLogout }}" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            <div class="flex items-center justify-between px-6 bg-white border-b border-[#DFE1E7] flex-shrink-0"
                style="height:56px;">
                <div class="flex items-center gap-3 min-w-0">
                    <div>
                        <div class="font-bold text-[15px] text-[#0D0D12] leading-[1.2]">{{ $pageTitle ?? 'Dashboard' }}
                        </div>
                        <div class="text-[11px] text-[#666D80]">Manajemen Ruangan · SIPERKOM</div>
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

                    <div class="mp-box-body">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>