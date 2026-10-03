@php
    $topbarUser = auth()->user();
    $topbarName = $topbarUser?->name ?? 'GPM';
    $topbarInitials = collect(preg_split('/\s+/u', trim($topbarName), -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<header class="bs-topbar bs-dosen-topbar">
    <div class="bs-dosen-crumb" aria-label="Breadcrumb">
        <span class="bs-dosen-crumb-brand">SIBASO</span>
        @hasSection('breadcrumbs')
            <span class="bs-dosen-crumb-separator" aria-hidden="true">/</span>
            <div class="bs-dosen-crumb-page">@yield('breadcrumbs')</div>
        @endif
    </div>

    <div class="bs-dosen-topbar-right">
        <x-banksoal::ui.gpm-notification-bell />
        <div class="bs-dosen-topbar-user" title="{{ $topbarName }} — GPM">
            <div class="bs-dosen-topbar-avatar">
                @if($topbarUser?->avatar_url)
                    <img src="{{ $topbarUser->avatar_url }}" alt="{{ $topbarName }}">
                @else
                    <span>{{ $topbarInitials }}</span>
                @endif
            </div>
            <div class="bs-dosen-topbar-meta">
                <div class="bs-dosen-topbar-name">{{ $topbarName }}</div>
                <div class="bs-dosen-topbar-role">Modules GPM</div>
            </div>
        </div>
    </div>
</header>
