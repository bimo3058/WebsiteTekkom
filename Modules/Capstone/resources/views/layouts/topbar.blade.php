@php
    $segments = array_values(array_filter(explode('/', $pagePath ?? '/')));
    $lastSegment = end($segments) ?: '';
    if (ctype_digit($lastSegment) && count($segments)>1) $lastSegment = $segments[count($segments)-2];
    $breadcrumb = ucwords(str_replace('-', ' ', $lastSegment));
    $nameParts = preg_split('/\s+/', trim($actor['name'] ?? 'User'));
    $initials = mb_strtoupper(mb_substr($nameParts[0], 0, 1).(count($nameParts)>1 ? mb_substr(end($nameParts), 0, 1) : ''));
@endphp
<header class="sitkom-topbar-capstone">
    <button type="button" @click="mobileSidebar = !mobileSidebar" :aria-expanded="mobileSidebar.toString()" aria-controls="capstone-sidebar-nav" aria-label="Buka menu" class="md:hidden inline-flex size-9 items-center justify-center rounded-radius text-on-surface hover:bg-surface-alt"><x-capstone::icon name="Menu" size="20" /></button>
    <x-capstone::breadcrumb :current="$breadcrumb" />
    <div class="sitkom-topbar-right">
        <x-btn style="outline" size="icon" href="{{ \Modules\Capstone\Support\CapstoneUrl::to('/notifications') }}" aria-label="Notifikasi" title="Notifikasi" class="w-[34px] h-[34px] rounded-[8px] relative"><x-capstone::icon name="Bell" size="20" /><span x-show="unread > 0" x-text="unread > 99 ? '99+' : unread" class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold leading-none text-on-danger"></span></x-btn>
        <x-capstone::dropdown width="56">
            <x-slot:trigger>
                <span class="sitkom-topbar-user">
                    @if(!empty($actor['avatar_url']))
                        <x-capstone::avatar :src="$actor['avatar_url']" alt="" size="sm" />
                    @else
                        <x-capstone::avatar size="sm">{{ $initials }}</x-capstone::avatar>
                    @endif
                    <span class="sitkom-topbar-meta"><span class="sitkom-topbar-name">{{ $actor['name'] ?? 'User' }}</span><span class="sitkom-topbar-role">{{ ucfirst($activeRole ?? 'User') }}</span></span>
                </span>
            </x-slot:trigger>
            <div class="border-b border-outline px-3 py-2.5"><p class="truncate text-[12px] font-semibold text-on-surface-strong">{{ $actor['name'] ?? '' }}</p><p class="truncate text-[11px] text-on-surface/60">{{ $actor['email'] ?? '' }}</p></div>
            <a href="{{ \Modules\Capstone\Support\CapstoneUrl::to('/profile') }}" class="relative flex w-full cursor-pointer select-none items-center gap-2 rounded-radius px-2 py-1.5 text-sm outline-none transition-colors hover:bg-surface-alt hover:text-on-surface"><x-capstone::icon name="User" size="16" />Profil</a>
            @if(in_array('admin', $actor['roles'] ?? []))<x-capstone::feature-link href="/admin/settings" class="relative flex w-full cursor-pointer select-none items-center gap-2 rounded-radius px-2 py-1.5 text-sm outline-none transition-colors hover:bg-surface-alt hover:text-on-surface"><x-capstone::icon name="Settings" size="16" />Pengaturan</x-capstone::feature-link>@endif
            <x-separator class="my-1" />
            <form method="POST" action="{{ route('capstone.logout') }}">@csrf<button type="submit" class="capstone-logout-item relative flex w-full cursor-pointer select-none items-center gap-2 rounded-radius px-2 py-1.5 text-sm text-danger outline-none transition-colors hover:bg-danger/10"><x-capstone::icon name="LogOut" size="16" />Logout</button></form>
        </x-capstone::dropdown>
    </div>
</header>
