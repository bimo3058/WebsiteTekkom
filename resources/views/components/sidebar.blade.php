@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
    $currentRoute = request()->route()->getName();

    $name = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false)
        $initials .= strtoupper(substr($name, $sp + 1, 1));

    // ── Icons ─────────────────────────────────────────────────
    $iconDashboard = 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10';
    $iconUsers = 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75';
    $iconShield = 'M12 2L4 6v6c0 5 3.4 9.5 8 10 4.6-.5 8-5 8-10V6l-8-4z';
    $iconAuditLog = 'M9 4H6a2 2 0 00-2 2v12a2 2 0 002 2h11a2 2 0 002-2v-7M7 13h7M7 17h5';
    $iconBankSoal = 'M6 4h12v16H6zM9 8h6M9 12h6M9 16h4';
    $iconCapstone = 'M3 17l5-5 4 4 8-8M14 8h6v6';
    $iconSimenma = 'M9 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM2.5 20a6.5 6.5 0 0113 0M17 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z';
    $iconClock = 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v5l3 2';
    $iconGear = 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z';
    $iconMonitor = 'M3 4h18v12H3zM8 20h8M12 16v4';
    $iconHelp = 'M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01';
    $iconLogout = 'M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9';

    // ── Topbar breadcrumb map ──────────────────────────────────
    $topbarRoutes = [
        'superadmin.dashboard' => 'Dashboard',
        'superadmin.users.index' => 'User Management',
        'superadmin.users.online' => 'User Management',
        'superadmin.users.suspended' => 'User Management',
        'superadmin.permissions' => 'Permissions',
        'superadmin.permissions.category' => 'Permissions',
        'superadmin.modules' => 'Modul Setting',
        'superadmin.audit-logs' => 'Audit Logs',
        'profile.edit' => 'Settings',
    ];
    $pageTitle = $topbarRoutes[$currentRoute] ?? 'Dashboard';
@endphp

<div x-data="{ open: localStorage.getItem('sidebarOpen') !== 'false' }"
    x-init="$watch('open', v => localStorage.setItem('sidebarOpen', v))" class="sitkom-shell"
    style="font-family:'Inter Tight',system-ui,sans-serif;">

    {{-- ═══════════ SIDEBAR ═══════════ --}}
    <aside :class="open ? 'is-open' : 'is-collapsed'" class="sitkom-sidebar">

        {{-- Brand + Collapse Button --}}
        <div class="sb-brand">
            {{-- Logo: selalu tampil --}}
            <div class="sb-brand-logo">
                <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP"
                    style="width:32px;height:32px;object-fit:contain;">
            </div>

            {{-- Brand text: hanya saat open --}}
            <div x-show="open" x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="sb-brand-text">
                <div class="sb-brand-name">SITKOM</div>
                <div class="sb-brand-tag">Sistem Informasi Teknik Komputer</div>
            </div>

            {{-- Collapse button — di dalam sidebar, berkotak border --}}
            <button @click="open = !open" class="sb-collapse-btn" title="Toggle Sidebar">
                {{-- Panah kiri saat open, panah kanan saat collapsed --}}
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s ease;"
                    :style="open ? '' : 'transform:rotate(180deg)'">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="sb-nav">

            {{-- MAIN --}}
            <div x-show="open" class="sb-section-label">Main Menu</div>

<<<<<<< HEAD
                @if($isSuperadmin)
                    <x-sidebar-link href="{{ route('superadmin.dashboard') }}"
                        icon="{{ $iconDashboard }}"
                        label="Dashboard"
                        :active="str_contains($currentRoute, 'dashboard')"/>
                @else
                    <x-sidebar-link href="{{ route('dashboard') }}"
                        icon="{{ $iconDashboard }}"
                        label="Dashboard"
                        :active="$currentRoute === 'dashboard'"/>
                @endif
            </div>

            {{-- KENDALI (Superadmin) --}}
=======
>>>>>>> d6381d8f518da8a9d88010bb391d0a2195d0578d
            @if($isSuperadmin)
                <x-sidebar-link :href="route('superadmin.dashboard')" :icon="$iconDashboard" label="Dashboard"
                    :active="str_contains($currentRoute, 'dashboard')" />
            @else
                <x-sidebar-link :href="route('dashboard')" :icon="$iconDashboard" label="Dashboard"
                    :active="$currentRoute === 'dashboard'" />
            @endif

            {{-- KENDALI --}}
            @if($isSuperadmin)
                <div x-show="open" class="sb-section-label">Kendali</div>
                <x-sidebar-link :href="route('superadmin.users.index')" :icon="$iconUsers" label="User Management"
                    :active="str_contains($currentRoute, 'users')" />
                <x-sidebar-link :href="route('superadmin.permissions')" :icon="$iconShield" label="Permissions"
                    :active="str_contains($currentRoute, 'permissions')" />
                <x-sidebar-link :href="route('superadmin.audit-logs')" :icon="$iconAuditLog" label="Audit Logs"
                    :active="str_contains($currentRoute, 'audit-logs')" />
            @endif

            {{-- AKADEMIK --}}
            <div x-show="open" class="sb-section-label">Akademik</div>

            <x-sidebar-link :href="route('banksoal.dashboard')" :icon="$iconBankSoal" label="SIBASO"
                :active="str_contains($currentRoute, 'banksoal')" />
            <x-sidebar-link :href="route('capstone.dashboard')" :icon="$iconCapstone" label="SICATA"
                :active="str_contains($currentRoute, 'capstone')" />
            <x-sidebar-link :href="route('manajemenmahasiswa.dashboard')" :icon="$iconSimenma" label="SIMENMA"
                :active="str_contains($currentRoute, 'manajemen-mahasiswa')" />
            <x-sidebar-link :href="route('eoffice.dashboard')" :icon="$iconClock" label="SIPERKOM"
                :active="str_contains($currentRoute, 'eoffice')" />

            {{-- SETTING --}}
            @if($isSuperadmin)
                <div x-show="open" class="sb-section-label">Setting</div>
                <x-sidebar-link :href="route('superadmin.modules')" :icon="$iconGear" label="Modul Setting"
                    :active="str_contains($currentRoute, 'modules')" />
                <x-sidebar-link href="/pulse" :icon="$iconMonitor" label="System Monitor" target="_blank"
                    :active="request()->is('pulse*')" />
            @endif
        </nav>

        {{-- Footer --}}
        <div class="sb-footer">
            <x-sidebar-link :href="route('profile.edit')" :icon="$iconGear" label="Settings"
                :active="str_contains($currentRoute, 'profile')" />
            <a class="sb-link" href="#">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="{{ $iconHelp }}" />
                </svg>
                <span x-show="open">Help &amp; Center</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;" data-no-loader>
                @csrf
                <button type="submit" class="sb-link sb-link-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="{{ $iconLogout }}" />
                    </svg>
                    <span x-show="open">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══════════ MAIN ═══════════ --}}
    <main class="sitkom-main">

        {{-- Topbar --}}
        <div class="sitkom-topbar">
            <div class="sitkom-crumb">
                <span>SITKOM</span>
                <span class="sitkom-crumb-sep">/</span>
                <b>{{ $pageTitle }}</b>
            </div>

            <div class="sitkom-topbar-right">
                <button class="sitkom-icon-btn" title="Notifications">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 8a6 6 0 1112 0c0 7 3 9 3 9H3s3-2 3-9" />
                        <path d="M10 21a2 2 0 004 0" />
                    </svg>
                </button>
                <div class="sitkom-topbar-user">
                    <div class="sitkom-topbar-avatar">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt=""
                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div class="sitkom-topbar-meta">
                        <div class="sitkom-topbar-name">{{ $user->name }}</div>
                        <div class="sitkom-topbar-role">
                            @if($isSuperadmin) Super Admin
                            @elseif($isDosen) Dosen
                            @elseif($isMahasiswa) Mahasiswa
                            @else User
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Page content --}}
        <div class="sitkom-content">
            {{ $slot }}
        </div>
    </main>

    @if(View::exists('components.sidebar-mobile'))
        <x-sidebar-mobile :user="$user" />
    @endif
</div>

{{-- ═══════════ STYLES ═══════════ --}}
<style>
    :root {
        --c-primary: #0B266E;
        --c-primary-hover: #091958;
        --c-primary-subtle: rgba(11, 38, 110, 0.08);
        --c-primary-border: #5C78B8;
        --c-bg: #F6F8FA;
        --c-fg: #0D0D12;
        --c-fg-sec: #353849;
        --c-fg-muted: #666D80;
        --c-fg-placeholder: #808897;
        --c-border: #DFE1E7;
        --c-border-strong: #C1C7CF;
        --c-success: #287F6E;
        --c-success-subtle: #DDF2EE;
        --c-error: #DF1C41;
        --c-error-subtle: #FADAE1;
        --c-warning: #956321;
        --c-warning-subtle: #F9ECCB;
        --c-sky: #0C4D6E;
        --c-sky-subtle: #D1F0F9;
        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
        --font-sans: 'Inter Tight', system-ui, sans-serif;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .sitkom-shell {
        display: flex;
        min-height: 100vh;
        background: var(--c-bg);
    }

    /* ── Sidebar ──────────────────────────────────── */
    .sitkom-sidebar {
        background: #fff;
        border-right: 1px solid var(--c-border);
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
        flex-shrink: 0;
        transition: width .25s ease;
        overflow: hidden;
    }

    .sitkom-sidebar.is-open {
        width: 240px;
    }

    .sitkom-sidebar.is-collapsed {
        width: 64px;
    }

    /* Brand row */
    .sb-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 14px;
        border-bottom: 1px solid var(--c-border);
        min-height: 60px;
        flex-shrink: 0;
    }

    .sb-brand-logo {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sb-brand-text {
        flex: 1;
        min-width: 0;
    }

    .sb-brand-name {
        font-family: 'Geist', 'Inter Tight', sans-serif;
        font-weight: 700;
        font-size: 14px;
        color: var(--c-fg);
        letter-spacing: -.01em;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-brand-tag {
        font-size: 9px;
        color: var(--c-fg-placeholder);
        font-weight: 500;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Collapse button — di dalam sidebar, berkotak */
    .sb-collapse-btn {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 7px;
        border: 1px solid var(--c-border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--c-fg-muted);
        transition: background .15s, border-color .15s, color .15s;
        padding: 0;
        /* Pastikan selalu kelihatan walau sidebar collapsed */
        margin-left: auto;
    }

    .sb-collapse-btn:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
        color: var(--c-fg);
    }

    /* Saat collapsed, tombol tetap centered di baris brand */
    .sitkom-sidebar.is-collapsed .sb-brand {
        justify-content: center;
    }

    .sitkom-sidebar.is-collapsed .sb-collapse-btn {
        margin-left: 0;
    }

    /* Nav */
    .sb-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 6px 10px 10px;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .sb-nav::-webkit-scrollbar {
        width: 3px;
    }

    .sb-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sb-nav::-webkit-scrollbar-thumb {
        background: var(--c-border);
        border-radius: 9999px;
    }

    .sb-section-label {
        font-size: 10px;
        font-weight: 600;
        color: var(--c-fg-placeholder);
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 12px 10px 5px;
        white-space: nowrap;
    }

    /* Footer */
    .sb-footer {
        padding: 8px 10px 12px;
        border-top: 1px solid var(--c-border);
        display: flex;
        flex-direction: column;
        gap: 1px;
        flex-shrink: 0;
    }

    .sb-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg-sec);
        cursor: pointer;
        text-decoration: none;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        font-family: inherit;
        transition: background .12s, color .12s;
        white-space: nowrap;
        overflow: hidden;
    }

    .sb-link:hover {
        background: var(--c-bg);
    }

    .sb-link svg {
        width: 16px;
        height: 16px;
        color: var(--c-fg-muted);
        flex-shrink: 0;
    }

    .sb-link-danger {
        color: var(--c-error);
    }

    .sb-link-danger svg {
        color: var(--c-error);
    }

    .sb-link-danger:hover {
        background: #FEF1F4;
    }

    /* ── Main area ────────────────────────────────── */
    .sitkom-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        overflow: hidden;
    }

    /* Topbar */
    .sitkom-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        height: 60px;
        padding: 0 28px;
        background: #fff;
        border-bottom: 1px solid var(--c-border);
        position: sticky;
        top: 0;
        z-index: 20;
        flex-shrink: 0;
    }

    .sitkom-crumb {
        font-size: 12px;
        color: var(--c-fg-muted);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .sitkom-crumb b {
        color: var(--c-fg);
        font-weight: 600;
    }

    .sitkom-crumb-sep {
        color: var(--c-border-strong);
    }

    .sitkom-topbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sitkom-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--c-border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-fg-muted);
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }

    .sitkom-icon-btn:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
        color: var(--c-fg);
    }

    .sitkom-topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-left: 12px;
        border-left: 1px solid var(--c-border);
        margin-left: 4px;
    }

    .sitkom-topbar-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5C78B8, #0B266E);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }

    .sitkom-topbar-meta {
        line-height: 1.2;
    }

    .sitkom-topbar-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-fg);
        white-space: nowrap;
    }

    .sitkom-topbar-role {
        font-size: 11px;
        color: var(--c-fg-muted);
        white-space: nowrap;
    }

    /* Content */
    .sitkom-content {
        padding: 24px 28px 48px;
    }

    /* ═══════════════════════════════════════════
       MOBILE RESPONSIVE (<768px)
       Sidebar disembunyikan, bottom nav aktif
       ═══════════════════════════════════════════ */
    @media (max-width: 767px) {
        /* Sembunyikan sidebar desktop sepenuhnya */
        .sitkom-sidebar {
            display: none !important;
        }

        /* Biarkan main area scroll secara native */
        .sitkom-main {
            overflow-y: auto !important;
            height: auto !important;
            min-height: 100dvh;
        }

        /* Compact topbar di mobile */
        .sitkom-topbar {
            padding: 0 14px;
            height: 52px;
            gap: 10px;
        }

        /* Sembunyikan teks nama & role di topbar — avatar tetap tampil */
        .sitkom-topbar-meta {
            display: none;
        }

        /* Kurangi gap di area user topbar */
        .sitkom-topbar-user {
            padding-left: 8px;
            gap: 6px;
        }

        /* Avatar sedikit lebih kecil */
        .sitkom-topbar-avatar {
            width: 30px;
            height: 30px;
            font-size: 10px;
        }

        /* Tambah padding-bottom untuk bottom nav (16px konten + 64px nav) */
        .sitkom-content {
            padding: 16px 14px 80px !important;
            display: block !important;
            overflow-y: visible !important;
        }

        /* Notification button ukuran yang lebih compact */
        .sitkom-icon-btn {
            width: 30px;
            height: 30px;
        }
    }
</style>