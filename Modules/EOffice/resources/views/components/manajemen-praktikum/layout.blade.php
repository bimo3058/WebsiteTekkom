@props(['pageTitle' => null, 'noBox' => false])
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $pageTitle ?? 'Manajemen Praktikum' }} — SIPERKOM</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
@include('eoffice::manajemen-praktikum.partials._styles')
@include('eoffice::manajemen-praktikum.partials._shell-styles')
    <x-mobile-navigation-assets />
</head>
<body class="mp-app h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased" style="font-family:'Inter Tight',system-ui,sans-serif;">

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
    $isMhs    = $user->hasRole('mahasiswa') || $user->hasRole('praktikan');

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
                    ['href' => route('eoffice.manprak.admin.periode-praktikan.index'),'label' => 'Periode Praktikan','match' => 'periode-praktikan',         'icon' => $iCal],
                    ['href' => route('eoffice.manprak.admin.kelola-role.index'),      'label' => 'Kelola Role',        'match' => 'kelola-role',              'icon' => $iShield],
                ],
            ],
        ];
    }

        if ($isDosen) {
            $sections[] = [
                'label' => 'Dosen',
                'color' => '#0B266E',
                'bg' => 'rgba(11,38,110,0.08)',
                'match' => 'manprak.dosen',
                'groups' => [
                    'Utama' => [
                        ['href' => route('eoffice.manprak.dosen.dashboard'), 'label' => 'Dashboard', 'match' => 'dosen.dashboard', 'icon' => $iHome],
                    ],
                    'Kelola' => [
                        ['href' => route('eoffice.manprak.dosen.praktikum.index'), 'label' => 'Praktikum', 'match' => ['dosen.praktikum', 'dosen.modul', 'dosen.tugas', 'dosen.nilai', 'dosen.asprak', 'dosen.pengumuman', 'dosen.pendaftaran-koor'], 'icon' => $iBook],
                    ],
                ],
            ];
        }

        if ($isKoor) {
            $sections[] = [
                'label' => 'Koordinator',
                'color' => '#0284C7',
                'bg' => 'rgba(2,132,199,0.08)',
                'match' => 'manprak.koor',
                'groups' => [
                    'Utama' => [
                        ['href' => route('eoffice.manprak.koor.dashboard'), 'label' => 'Dashboard', 'match' => 'koor.dashboard', 'icon' => $iHome],
                    ],
                    'Kelola' => [
                        ['href' => route('eoffice.manprak.koor.pendaftaran-praktikan.index'), 'label' => 'Pendaftaran Praktikan', 'match' => 'koor.pendaftaran-praktikan', 'icon' => $iClipboard],
                        ['href' => route('eoffice.manprak.koor.praktikum.index'), 'label' => 'Praktikum', 'match' => ['koor.praktikum', 'koordinator.periode-pendaftaran', 'koor.pendaftaran-asprak', 'koor.modul', 'bagi-modul', 'koor.praktikan', 'koor.nilai'], 'icon' => $iBook],
                    ],
                ],
            ];
        }

        if ($isAsprak) {
            $sections[] = [
                'label' => 'Asisten',
                'color' => '#0891B2',
                'bg' => 'rgba(8,145,178,0.10)',
                'match' => 'manprak.asprak',
                'groups' => [
                    'Utama' => [
                        ['href' => route('eoffice.manprak.asprak.dashboard'), 'label' => 'Dashboard', 'match' => 'asprak.dashboard', 'icon' => $iHome],
                    ],
                    'Kelola' => [
                        ['href' => route('eoffice.manprak.asprak.praktikum.index'), 'label' => 'Praktikum', 'match' => ['asprak.praktikum', 'asprak.daftar-praktikan', 'asprak.absensi', 'asprak.tugas', 'asprak.materi', 'asprak.pengumuman'], 'icon' => $iBook],
                    ],
                ],
            ];
        }

        if ($isMhs) {
            $sections[] = [
                'label' => 'Mahasiswa',
                'color' => '#64748B',
                'bg' => 'rgba(100,116,139,0.10)',
                'match' => 'manprak.mahasiswa',
                'groups' => [
                    'Utama' => [
                        ['href' => route('eoffice.manprak.mahasiswa.dashboard'), 'label' => 'Dashboard', 'match' => 'mahasiswa.dashboard', 'icon' => $iHome],
                    ],
                    'Aktivitas' => [
                        ['href' => route('eoffice.manprak.mahasiswa.pendaftaran-praktikan.index'), 'label' => 'Pendaftaran Praktikan', 'match' => 'mahasiswa.pendaftaran-praktikan', 'icon' => $iClipboard],
                        ['href' => route('eoffice.manprak.mahasiswa.praktikum.index'), 'label' => 'Praktikum', 'match' => ['mahasiswa.praktikum', 'mahasiswa.modul', 'mahasiswa.pengumuman', 'mahasiswa.tugas', 'mahasiswa.nilai', 'mahasiswa.daftar-praktikan'], 'icon' => $iBook],
                        ['href' => route('eoffice.manprak.mahasiswa.daftar-asprak.index'), 'label' => 'Pendaftaran', 'match' => 'daftar-asprak', 'icon' => $iUser],
                    ],
                ],
            ];
        }

    $multiRole    = count($sections) > 1;
    $manprakActive= str_contains($currentRoute, 'manprak');
    $notifCount = \Modules\EOffice\Models\Notifikasi::where('user_id', $user->id)->where('is_read', false)->count();
    $activeSection = collect($sections)->first(fn ($section) => str_contains($currentRoute, $section['match']));
    $activeRoleLabel = $activeSection['label'] ?? ($sections[0]['label'] ?? 'Pengguna');
    if ($activeRoleLabel === 'Admin') $activeRoleLabel = $user->hasRole('superadmin') ? 'Superadmin' : 'Admin E-Office';
@endphp

<div class="mp-shell" x-data="{ sidebarOpen: false, isMobile: window.innerWidth < 768 }"
     :class="{ 'mp-sidebar-open': sidebarOpen }"
     x-init="(() => { try { sidebarOpen = !isMobile &amp;&amp; localStorage.getItem('mp_sb') !== '0'; } catch { sidebarOpen = !isMobile; } $watch('sidebarOpen', v => { if (!isMobile) { try { localStorage.setItem('mp_sb', v ? '1' : '0'); } catch {} } }); })()"
     @resize.window.debounce.150ms="if (isMobile !== (window.innerWidth < 768)) { isMobile = window.innerWidth < 768; try { sidebarOpen = !isMobile &amp;&amp; localStorage.getItem('mp_sb') !== '0'; } catch { sidebarOpen = !isMobile; } }"
     @keydown.escape.window="if (isMobile &amp;&amp; sidebarOpen) { sidebarOpen = false; $refs.mpMenuButton.focus(); }">
    <button type="button" class="mp-sidebar-backdrop" x-show="sidebarOpen &amp;&amp; isMobile" x-cloak
            @click="sidebarOpen = false; $refs.mpMenuButton.focus()" aria-label="Tutup menu praktikum"></button>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SIDEBAR                                                        --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @include('eoffice::manajemen-praktikum.partials._sidebar')

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MAIN AREA                                                      --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="mp-main" :inert="isMobile &amp;&amp; sidebarOpen">

        {{-- Topbar --}}
        @include('eoffice::manajemen-praktikum.partials._topbar')

        {{-- Content Box (SITKOM box/wrap pattern) --}}
        <div class="mp-wrap">
            <div class="mp-box">
                @if(isset($header))
                <div class="mp-header-slot">
                    {{ $header }}
                </div>
                @endif
                <div class="mp-box-body" role="region" aria-label="Konten {{ $pageTitle ?? 'praktikum' }}" tabindex="0">
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
    <x-mobile-navigation />
@stack('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('modal', { isOpen: false, open() { this.isOpen = true; }, close() { this.isOpen = false; } });
});
</script>
</body>
</html>
