        @php
            // Resolve navigation from the current route, never from role priority.
            $breadcrumbRole = str_replace('manprak.', '', $activeSection['match'] ?? 'manprak.mahasiswa');
            $praktikumListRoute = "eoffice.manprak.{$breadcrumbRole}.praktikum.index";
            $praktikumListHref = route($praktikumListRoute);
            $praktikumHref = null;
            if ($breadcrumbPraktikum) {
                $praktikumHref = match ($breadcrumbRole) {
                    'admin' => route('eoffice.manprak.admin.praktikum.detail', $breadcrumbPraktikum->id),
                    'mahasiswa' => route('eoffice.manprak.mahasiswa.pengumuman.index', ['praktikum_id' => $breadcrumbPraktikum->id]),
                    default => route("eoffice.manprak.{$breadcrumbRole}.praktikum.show", $breadcrumbPraktikum->id),
                };
            }
            $breadcrumbTitle = $pageTitle ?? 'Dashboard';
            if ($breadcrumbPraktikum && str_starts_with($breadcrumbTitle, $breadcrumbPraktikum->nama . ' / ')) {
                $breadcrumbTitle = substr($breadcrumbTitle, strlen($breadcrumbPraktikum->nama . ' / '));
            }
            $breadcrumbParentLabel = null;
            $breadcrumbParentHref = null;
            if ($breadcrumbPraktikum && str_contains($currentRoute, '.tugas-akhir.')) {
                $taSection = request()->route('taSection') ?? 'modul';
                $taParent = $taSection === 'nilai' ? ($breadcrumbRole === 'asprak' ? 'absensi' : 'nilai') : $taSection;
                $breadcrumbParentLabel = match($taSection) { 'nilai' => 'Absensi & Nilai', 'tugas' => 'Tugas', default => $breadcrumbRole === 'koor' ? 'Kelola Modul' : 'Modul' };
                $breadcrumbParentHref = $breadcrumbRole === 'dosen' && $taSection !== 'tugas'
                    ? route("eoffice.manprak.dosen.{$taParent}.index", $breadcrumbPraktikum->id)
                    : route("eoffice.manprak.{$breadcrumbRole}.{$taParent}.index", ['praktikum_id' => $breadcrumbPraktikum->id]);
            } elseif ($breadcrumbPraktikum && str_contains($currentRoute, '.tugas.') && !str_ends_with($currentRoute, '.tugas.index')) {
                $breadcrumbParentLabel = 'Tugas';
                $breadcrumbParentHref = route("eoffice.manprak.{$breadcrumbRole}.tugas.index", ['praktikum_id' => $breadcrumbPraktikum->id]);
                $breadcrumbTitle = str_contains($breadcrumbTitle, ' / ') ? substr($breadcrumbTitle, strrpos($breadcrumbTitle, ' / ') + 3) : $breadcrumbTitle;
            } elseif ($breadcrumbPraktikum && str_contains($currentRoute, '.modul.') && !str_ends_with($currentRoute, '.modul.index')) {
                $breadcrumbParentLabel = 'Modul';
                $breadcrumbParentHref = route("eoffice.manprak.{$breadcrumbRole}.modul.index", ['praktikum_id' => $breadcrumbPraktikum->id]);
            }
        @endphp
        <header class="mp-topbar">
            <nav class="mp-breadcrumb" aria-label="Breadcrumb">
                <button type="button" class="mp-icon-button mp-mobile-menu" x-ref="mpMenuButton"
                        @click="sidebarOpen = true; $nextTick(() => $refs.mpSidebarToggle.focus())"
                        :aria-expanded="sidebarOpen" aria-controls="mp-navigation" aria-label="Buka menu praktikum">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a class="mp-breadcrumb-root" href="{{ route('eoffice.dashboard') }}">E-Office</a>
                <span class="mp-breadcrumb-root" aria-hidden="true">/</span>
                <a href="{{ $praktikumListHref }}">Praktikum</a>
                <span aria-hidden="true">/</span>
                @if($breadcrumbPraktikum)
                    <a class="mp-breadcrumb-praktikum" href="{{ $praktikumHref }}" title="{{ $breadcrumbPraktikum->nama }}">{{ $breadcrumbPraktikum->nama }}</a>
                    <span aria-hidden="true">/</span>
                @endif
                @if($breadcrumbParentHref)
                    <a href="{{ $breadcrumbParentHref }}">{{ $breadcrumbParentLabel }}</a>
                    <span aria-hidden="true">/</span>
                @endif
                <strong aria-current="page" title="{{ $breadcrumbTitle }}">{{ $breadcrumbTitle }}</strong>
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
