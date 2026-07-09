@php
    $iconPortal = 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3v-6h6v6h3a1 1 0 001-1V10';
    $iconRiwayat = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
    $iconLogout = 'M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9';
    $iconGear = 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z';
    $iconHelp = 'M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17h.01';
@endphp

<aside x-data="{ get open() { return sidebarOpen }, set open(val) { sidebarOpen = val } }"
    :class="sidebarOpen ? 'is-open' : 'is-collapsed'"
    class="sitkom-sidebar relative z-20 font-sans">

    {{-- Brand + Collapse Button --}}
    <div class="sb-brand">
        {{-- Logo --}}
        <div class="sb-brand-logo">
            <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP"
                style="width:32px;height:32px;object-fit:contain;">
        </div>

        {{-- Brand text --}}
        <div x-show="sidebarOpen" x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="sb-brand-text">
            <div class="sb-brand-name">Portal Mahasiswa</div>
            <div class="sb-brand-tag">S1 Teknik Komputer</div>
        </div>

        {{-- Collapse button --}}
        <button @click="sidebarOpen = !sidebarOpen" class="sb-collapse-btn" title="Toggle Sidebar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round" style="transition:transform .25s ease;"
                :style="sidebarOpen ? '' : 'transform:rotate(180deg)'">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="sb-nav">
        <div x-show="sidebarOpen" class="sb-section-label">Ujian Komprehensif</div>

        <x-sidebar-link :href="route('komprehensif.mahasiswa.dashboard')" :icon="$iconPortal" label="Portal Ujian"
            :active="request()->routeIs('komprehensif.mahasiswa.dashboard')" />
            
        <x-sidebar-link :href="route('komprehensif.mahasiswa.riwayat')" :icon="$iconRiwayat" label="Riwayat Ujian"
            :active="request()->routeIs('komprehensif.mahasiswa.riwayat')" />
    </nav>

    {{-- Footer --}}
    <div class="sb-footer">
        <x-sidebar-link :href="route('profile.edit')" :icon="$iconGear" label="Settings"
            :active="request()->routeIs('profile.edit')" />
        <a class="sb-link" href="#">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="{{ $iconHelp }}" />
            </svg>
            <span x-show="sidebarOpen">Help &amp; Center</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;" data-no-loader>
            @csrf
            <button type="submit" class="sb-link sb-link-danger w-full">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="{{ $iconLogout }}" />
                </svg>
                <span x-show="sidebarOpen">Logout</span>
            </button>
        </form>
    </div>
</aside>

<style>
    /* Mengadopsi CSS Variable & Layout dari Superadmin Sidebar */
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
    }

    .sitkom-sidebar {
        background: #fff;
        border-right: 1px solid var(--c-border);
        display: flex;
        flex-direction: column;
        position: relative;
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
        font-family: 'Geist', 'Inter Tight', system-ui, sans-serif;
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

    /* Collapse button */
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
        margin-left: auto;
    }

    .sb-collapse-btn:hover {
        background: var(--c-bg);
        border-color: var(--c-border-strong);
        color: var(--c-fg);
    }

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
</style>
