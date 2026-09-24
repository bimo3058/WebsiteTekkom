@php
    $segments = array_values(array_filter(explode('/', $pagePath ?? '/')));
    $lastSegment = end($segments) ?: '';
    if (ctype_digit($lastSegment) && count($segments)>1) $lastSegment = $segments[count($segments)-2];
    $breadcrumb = ucwords(str_replace('-', ' ', $lastSegment));
    $nameParts = preg_split('/\s+/', trim($actor['name'] ?? 'User'));
    $initials = mb_strtoupper(mb_substr($nameParts[0], 0, 1).(count($nameParts)>1 ? mb_substr(end($nameParts), 0, 1) : ''));
@endphp
<header class="sitkom-topbar-capstone">
    <button type="button" @click="mobileSidebar = !mobileSidebar" aria-label="Open menu" class="md:hidden"><x-capstone::icon name="Menu" /></button>
    <nav aria-label="breadcrumb"><ol class="sitkom-crumb"><li>SICATA</li>@if($breadcrumb)<li class="sitkom-crumb-sep">/</li><li><b>{{ $breadcrumb }}</b></li>@endif</ol></nav>
    <div class="sitkom-topbar-right">
        <x-ui.button variant="outline" size="icon" type="button" aria-label="Search" title="Search" style="width:34px;height:34px;border-radius:8px;"><x-capstone::icon name="Search" class="h-5 w-5" /></x-ui.button>
        <x-ui.button variant="outline" size="icon" as="a" href="{{ url('/capstone/notifications') }}" aria-label="Notifications" title="Notifications" style="width:34px;height:34px;border-radius:8px;position:relative;"><x-capstone::icon name="Bell" class="h-5 w-5" /><span x-show="unread > 0" class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-[#DF1C41]"></span></x-ui.button>
        <x-ui.dropdown width="56">
            <x-slot:trigger>
                <span class="sitkom-topbar-user">
                    @if(!empty($actor['avatar_url']))
                        <x-ui.avatar :src="$actor['avatar_url']" alt="" size="sm" />
                    @else
                        <span class="sitkom-topbar-avatar">{{ $initials }}</span>
                    @endif
                    <span class="sitkom-topbar-meta"><span class="sitkom-topbar-name">{{ $actor['name'] ?? 'User' }}</span><span class="sitkom-topbar-role">{{ ucfirst($activeRole ?? 'User') }}</span></span>
                </span>
            </x-slot:trigger>
            <div class="border-b border-[#F0F1F4] px-3 py-2.5"><p class="truncate text-[12px] font-semibold text-[#0D0D12]">{{ $actor['name'] ?? '' }}</p><p class="truncate text-[11px] text-[#808897]">{{ $actor['email'] ?? '' }}</p></div>
            <x-ui.dropdown-item as="a" href="{{ url('/capstone/profile') }}"><x-capstone::icon name="User" />Profile</x-ui.dropdown-item>
            @if(in_array('admin', $actor['roles'] ?? []))<x-capstone::feature-link href="/admin/settings" class="relative flex w-full cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0"><x-capstone::icon name="Settings" />Settings</x-capstone::feature-link>@endif
            <x-ui.separator class="my-1" />
            <form method="POST" action="{{ route('capstone.logout') }}">@csrf<x-ui.dropdown-item type="submit" class="capstone-logout-item text-[#DF1C41]"><x-capstone::icon name="LogOut" />Logout</x-ui.dropdown-item></form>
        </x-ui.dropdown>
    </div>
</header>
