<aside data-mobile-sidebar id="mp-navigation" class="mp-sidebar" :class="{ 'is-collapsed': !sidebarOpen }"
       :inert="isMobile &amp;&amp; !sidebarOpen" aria-label="Navigasi praktikum">
    <div class="mp-sidebar-brand">
        <a href="{{ route('eoffice.dashboard') }}" class="mp-brand" title="Dashboard E-Office" aria-label="Dashboard E-Office">
            <img src="{{ asset('images/UNDIPOfficial.png') }}" width="32" height="32" alt="">
            <span x-show="sidebarOpen"><strong>SIPERKOM</strong><small>Manajemen Praktikum</small></span>
        </a>
        <button type="button" x-ref="mpSidebarToggle" class="mp-icon-button mp-sidebar-toggle"
                @click="sidebarOpen = !sidebarOpen; if (isMobile) $refs.mpMenuButton.focus()"
                :aria-expanded="sidebarOpen" aria-controls="mp-navigation"
                :aria-label="sidebarOpen ? 'Lipat menu praktikum' : 'Perluas menu praktikum'"
                :title="sidebarOpen ? 'Lipat menu' : 'Perluas menu'">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        </button>
    </div>
    <nav class="mp-sidebar-nav" aria-label="Menu manajemen praktikum">
        @foreach($sections as $section)
            @php $sectionActive = str_contains($currentRoute, $section['match']); @endphp
            <section class="mp-nav-section" x-data="{ openSection: {{ ($sectionActive || !$multiRole) ? 'true' : 'false' }} }" aria-label="Menu {{ $section['label'] }}">
                @if($multiRole)
                    <button type="button" class="mp-role-toggle" :aria-expanded="openSection" aria-controls="mp-role-{{ $loop->index }}"
                            title="Menu {{ $section['label'] }}" aria-label="Menu {{ $section['label'] }}"
                            @click="if (!sidebarOpen) { sidebarOpen = true; openSection = true; } else { openSection = !openSection; }">
                        <span class="mp-role-initial" aria-hidden="true">{{ mb_substr($section['label'], 0, 1) }}</span>
                        <span class="mp-role-name" x-show="sidebarOpen">{{ $section['label'] }}</span>
                        <svg x-show="sidebarOpen" :class="{ 'is-open': openSection }" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                @endif
                <div id="mp-role-{{ $loop->index }}" x-show="openSection || !sidebarOpen">
                    @foreach($section['groups'] as $groupLabel => $items)
                        <div class="mp-nav-group">
                            <div class="mp-nav-label" x-show="sidebarOpen">{{ $groupLabel }}</div>
                            @foreach($items as $item)
                                @php $active = $sectionActive && \Illuminate\Support\Str::contains($currentRoute, $item['match']); @endphp
                                <a href="{{ $item['href'] }}" class="mp-nav-link {{ $active ? 'is-active' : '' }}"
                                   title="{{ $item['label'] }}" aria-label="{{ $item['label'] }} — {{ $section['label'] }}"
                                   @if($active) aria-current="page" @endif
                                   @click="if (isMobile) sidebarOpen = false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg>
                                    <span x-show="sidebarOpen">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </nav>
    <div class="mp-sidebar-footer">
        <a href="{{ route('eoffice.dashboard') }}" class="mp-nav-link" title="Kembali ke E-Office" aria-label="Kembali ke E-Office">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $iBack }}"/></svg>
            <span x-show="sidebarOpen">Kembali ke E-Office</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="mp-nav-link" title="Pengaturan profil" aria-label="Pengaturan profil">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $iGear }}"/></svg>
            <span x-show="sidebarOpen">Pengaturan Profil</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" data-no-loader>
            @csrf
            <button type="submit" class="mp-nav-link mp-nav-logout" title="Keluar" aria-label="Keluar dari akun">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $iLogout }}"/></svg>
                <span x-show="sidebarOpen">Keluar</span>
            </button>
        </form>
    </div>
</aside>
