        <header class="mp-topbar">
            <nav class="mp-breadcrumb" aria-label="Breadcrumb">
                <button type="button" class="mp-icon-button mp-mobile-menu" x-ref="mpMenuButton"
                        @click="sidebarOpen = true; $nextTick(() => $refs.mpSidebarToggle.focus())"
                        :aria-expanded="sidebarOpen" aria-controls="mp-navigation" aria-label="Buka menu praktikum">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a class="mp-breadcrumb-root" href="{{ route('eoffice.dashboard') }}">E-Office</a>
                <span class="mp-breadcrumb-root" aria-hidden="true">/</span>
                <a href="{{ route('eoffice.manprak.dashboard') }}">Praktikum</a>
                <span aria-hidden="true">/</span>
                <strong aria-current="page" title="{{ $pageTitle ?? 'Dashboard' }}">{{ $pageTitle ?? 'Dashboard' }}</strong>
            </nav>
            <div class="mp-topbar-actions">
                @if($notifCount > 0)
                    <span class="mp-notification-count" title="{{ $notifCount }} notifikasi belum dibaca" aria-label="{{ $notifCount }} notifikasi belum dibaca">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9 M10 21a2 2 0 0 0 4 0"/></svg>
                        {{ $notifCount > 99 ? '99+' : $notifCount }}
                    </span>
                @endif
                <a href="{{ route('profile.edit') }}" class="mp-account" title="Pengaturan profil" aria-label="Pengaturan profil {{ $name }}">
                    <span class="mp-account-avatar" aria-hidden="true">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="" width="34" height="34">
                        @else
                            {{ $initials }}
                        @endif
                    </span>
                    <span class="mp-account-meta"><strong>{{ $name }}</strong><span>{{ $activeRoleLabel }}</span></span>
                </a>
            </div>
        </header>
