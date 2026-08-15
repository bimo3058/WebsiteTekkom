{{-- ═══════════════════════════════════════════════════════
     SIKAPE Sidebar — Premium Academic Dashboard
     Design System: Inter Tight, custom color palette
     ═══════════════════════════════════════════════════════ --}}
@php
    $route = request()->route()?->getName() ?? '';
    $user  = auth()->user();

    function isActive(string $prefix, string $currentRoute): bool {
        return str_contains($currentRoute, $prefix);
    }
@endphp

<!-- Mobile overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-20 bg-grey-900/60 backdrop-blur-sm lg:hidden"
     @click="sidebarOpen = false"
     style="display:none"></div>

<!-- Sidebar -->
<aside x-data="{ sidebarCollapsed: false }"
       :class="{
           'translate-x-0': sidebarOpen,
           '-translate-x-full': !sidebarOpen,
           'lg:translate-x-0': true,
           'collapsed': sidebarCollapsed
       }"
       class="fixed inset-y-0 left-0 z-30 w-[260px] bg-white flex flex-col overflow-hidden
              transition-all duration-300 ease-in-out lg:static flex-shrink-0 sikape-sidebar">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 h-16 flex-shrink-0 sikape-logo-container">
        <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="Logo UNDIP" class="w-8 h-8 object-contain flex-shrink-0">
        <div x-show="!sidebarCollapsed" x-transition.opacity class="hidden lg:block">
            <p class="text-[15px] font-semibold text-grey-900 leading-tight tracking-tight">SIKAPE</p>
            <p class="text-[11px] text-grey-400 leading-none">Kerja Praktik</p>
        </div>
        <div class="lg:hidden">
            <p class="text-[15px] font-semibold text-grey-900 leading-tight tracking-tight">SIKAPE</p>
            <p class="text-[11px] text-grey-400 leading-none">Kerja Praktik</p>
        </div>
        {{-- Collapse toggle --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="sikape-collapse-btn">
            <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="sidebarOpen = false" class="ml-auto text-grey-400 hover:text-grey-700 lg:hidden p-1 rounded-md hover:bg-grey-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5" :class="sidebarCollapsed ? 'lg:px-2' : ''">

        <!-- Section label -->
        <p x-show="!sidebarCollapsed" class="px-3 pt-2 pb-1.5 text-[10px] font-semibold text-grey-400 uppercase tracking-widest">Menu</p>

        {{-- Dashboard --}}
        <a href="{{ route('eoffice.kp.mahasiswa.dashboard') }}"
           class="sikape-nav-item {{ isActive('mahasiswa.dashboard', $route) ? 'active' : '' }}">
            <span class="sikape-nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Dashboard</span>
        </a>

        {{-- Informasi (collapsible) --}}
        <div x-data="{ 
                open: localStorage.getItem('sidebar_info_open') === 'true' || {{ isActive('mahasiswa.proposal', $route) || isActive('mahasiswa.surat', $route) || isActive('mahasiswa.pengumuman', $route) || isActive('mahasiswa.faq', $route) ? 'true' : 'false' }},
                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('sidebar_info_open', this.open);
                }
            }">
            <button @click="toggle()"
                    class="sikape-nav-item w-full {{ isActive('mahasiswa.proposal', $route) || isActive('mahasiswa.surat', $route) || isActive('mahasiswa.pengumuman', $route) || isActive('mahasiswa.faq', $route) ? 'active' : '' }}">
                <span class="sikape-nav-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Informasi</span>
                <svg x-show="!sidebarCollapsed" class="ml-auto w-4 h-4 text-grey-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open && !sidebarCollapsed" x-collapse class="mt-0.5 ml-3 pl-3 border-l border-grey-100 space-y-0.5">
                <a href="{{ route('eoffice.kp.mahasiswa.pengumuman') }}"
                   class="sikape-sub-item {{ isActive('mahasiswa.pengumuman', $route) ? 'active' : '' }}">
                    Pengumuman
                </a>
                <a href="{{ route('eoffice.kp.mahasiswa.faq') }}"
                   class="sikape-sub-item {{ isActive('mahasiswa.faq', $route) ? 'active' : '' }}">
                    Pertanyaan Umum
                </a>
                <a href="{{ route('eoffice.kp.mahasiswa.proposal') }}"
                   class="sikape-sub-item {{ isActive('mahasiswa.proposal', $route) ? 'active' : '' }}">
                    Proposal KP
                </a>
                <a href="{{ route('eoffice.kp.mahasiswa.surat') }}"
                   class="sikape-sub-item {{ isActive('mahasiswa.surat', $route) ? 'active' : '' }}">
                    Surat Pengantar
                </a>
            </div>
        </div>

        <p x-show="!sidebarCollapsed" class="px-3 pt-4 pb-1.5 text-[10px] font-semibold text-grey-400 uppercase tracking-widest">Alur KP</p>

        {{-- Mendaftar KP --}}
        <a href="{{ route('eoffice.kp.mahasiswa.pendaftaran') }}"
           class="sikape-nav-item {{ isActive('mahasiswa.pendaftaran', $route) ? 'active' : '' }}">
            <span class="sikape-nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Mendaftar KP</span>
            <span class="ml-auto text-[9px] font-semibold px-2 py-0.5 rounded-full text-white shadow-sm flex-shrink-0" style="background: linear-gradient(to right, #1e293b, #1e3a8a);">Pra</span>
        </a>

        {{-- Dokumen --}}
        <a href="{{ route('eoffice.kp.mahasiswa.dokumen') }}"
           class="sikape-nav-item {{ isActive('mahasiswa.dokumen', $route) ? 'active' : '' }}">
            <span class="sikape-nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Dokumen KP</span>
            <span class="ml-auto text-[9px] font-semibold px-2 py-0.5 rounded-full text-white shadow-sm flex-shrink-0" style="background: linear-gradient(to right, #1e293b, #1e3a8a);">Saat</span>
        </a>

        {{-- Seminar --}}
        <a href="{{ route('eoffice.kp.mahasiswa.seminar') }}"
           class="sikape-nav-item {{ isActive('mahasiswa.seminar', $route) ? 'active' : '' }}">
            <span class="sikape-nav-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Seminar KP</span>
            <span class="ml-auto text-[9px] font-semibold px-2 py-0.5 rounded-full text-white shadow-sm flex-shrink-0" style="background: linear-gradient(to right, #1e293b, #1e3a8a);">Pasca</span>
        </a>

    </nav>

    <!-- Back to E-Office -->
    <div class="px-3 pb-2 flex-shrink-0" :class="sidebarCollapsed ? 'lg:px-2' : ''">
        <a href="{{ route('eoffice.dashboard') }}"
           class="sikape-back-btn flex items-center gap-2 w-full px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200"
           :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">Kembali ke E-Office</span>
        </a>
    </div>

    <!-- User Profile Bottom -->
    <div class="px-3 py-3 flex-shrink-0 sikape-user-container">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-grey-50 transition-colors cursor-pointer group sikape-user-wrapper">
            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-500 font-semibold text-sm flex-shrink-0">
                {{ strtoupper(substr($user->name ?? 'M', 0, 2)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                <p class="text-[13px] font-semibold text-grey-800 truncate leading-tight">{{ $user->name ?? 'Mahasiswa' }}</p>
                <p class="text-[11px] text-grey-400 truncate">Mahasiswa</p>
            </div>
            <form x-show="!sidebarCollapsed" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1 rounded-lg text-grey-400 hover:text-error-200 hover:bg-error-0 transition-colors" title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
/* ── SIKAPE Design Tokens ───────────────────────────────── */
:root {
    /* Primary */
    --primary-50:  #eef2ff;
    --primary-100: #e0e7ff;
    --primary-200: #c7d2fe;
    --primary-300: #a5b4fc;
    --primary-400: #818cf8;
    --primary-500: #4f46e5;

    /* Grey */
    --grey-0:   #ffffff;
    --grey-50:  #f9fafb;
    --grey-100: #f3f4f6;
    --grey-200: #e5e7eb;
    --grey-300: #d1d5db;
    --grey-400: #9ca3af;
    --grey-500: #6b7280;
    --grey-600: #4b5563;
    --grey-700: #374151;
    --grey-800: #1f2937;
    --grey-850: #111827;
    --grey-900: #030712;

    /* Sky */
    --sky-200: #bae6fd;
    --sky-300: #7dd3fc;
    --sky-400: #38bdf8;
    --sky-500: #0ea5e9;
    --sky-600: #0284c7;
    --sky-800: #075985;

    /* Success */
    --success-0:   #f0fdf4;
    --success-50:  #dcfce7;
    --success-100: #bbf7d0;
    --success-200: #4ade80;
    --success-300: #16a34a;

    /* Warning */
    --warning-0:   #fffbeb;
    --warning-50:  #fef3c7;
    --warning-100: #fde68a;
    --warning-200: #fbbf24;
    --warning-300: #d97706;

    /* Error */
    --error-0:   #fff1f2;
    --error-50:  #ffe4e6;
    --error-100: #fecdd3;
    --error-200: #f87171;
    --error-300: #dc2626;
}

/* ── Back Button ──────────────────────────────────────── */
.sikape-back-btn {
    color: var(--grey-500);
    background: transparent;
    border: 1px solid var(--grey-200);
    font-size: 13px;
}
.sikape-back-btn:hover {
    background: #293C79;
    color: #ffffff;
    border-color: #293C79;
}

/* ── Nav Items ────────────────────────────────────────── */
.sikape-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 500;
    color: var(--grey-600);
    transition: all 0.15s ease;
    cursor: pointer;
    width: 100%;
    text-align: left;
    border: none;
    background: none;
    text-decoration: none;
}
.sikape-nav-item:hover {
    background: var(--grey-50);
    color: var(--grey-800);
}
a.sikape-nav-item.active, a.sikape-sub-item.active {
    background: var(--grey-100) !important;
    color: var(--grey-900) !important;
    font-weight: 600;
    position: relative;
}
a.sikape-nav-item.active::before, a.sikape-sub-item.active::before {
    content: '';
    position: absolute;
    left: -12px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 22px;
    background-color: #293C79;
    border-radius: 9999px;
}
/* Sub items should keep the navy bar inside the padding space */
a.sikape-sub-item.active::before {
    left: -10px;
}
button.sikape-nav-item.active {
    background: transparent !important;
    color: var(--grey-900) !important;
    font-weight: 600;
}
.sikape-nav-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sikape-nav-icon svg { width: 18px; height: 18px; }

.sikape-sub-item {
    display: block;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 500;
    color: var(--grey-500);
    transition: all 0.15s ease;
    text-decoration: none;
}
.sikape-sub-item:hover { background: var(--grey-50); color: var(--grey-800); }

/* ── Collapsible Sidebar custom rules ───────────────────── */
.sikape-collapse-btn {
    display: none;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: 1px solid var(--grey-200);
    color: var(--grey-400);
    background: transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    margin-left: auto;
    flex-shrink: 0;
}
.sikape-collapse-btn:hover {
    color: var(--grey-700);
    background: var(--grey-50);
}

@media (min-width: 1024px) {
    .sikape-sidebar {
        width: 260px !important;
    }
    .sikape-sidebar.collapsed {
        width: 72px !important;
    }
    .sikape-collapse-btn {
        display: flex;
    }
    
    /* Logo container when collapsed */
    .sikape-sidebar.collapsed .sikape-logo-container {
        flex-direction: column !important;
        height: auto !important;
        padding-top: 16px !important;
        padding-bottom: 16px !important;
        gap: 12px !important;
        justify-content: center !important;
        align-items: center !important;
    }
    .sikape-sidebar.collapsed .sikape-logo-container .sikape-collapse-btn {
        margin-left: 0 !important;
        margin-top: 4px !important;
    }
    
    /* Nav element when collapsed */
    .sikape-sidebar.collapsed nav {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .sikape-sidebar.collapsed .sikape-nav-item {
        justify-content: center !important;
        padding: 8px 0 !important;
    }
    
    /* User profile when collapsed */
    .sikape-sidebar.collapsed .sikape-user-container {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .sikape-sidebar.collapsed .sikape-user-wrapper {
        justify-content: center !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
</style>
