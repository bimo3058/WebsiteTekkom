@php
    $segments = array_values(array_filter(explode('/', $pagePath ?? '/')));
    $lastSegment = end($segments) ?: '';
    if (ctype_digit($lastSegment) && count($segments)>1) $lastSegment = $segments[count($segments)-2];
    $breadcrumb = ucwords(str_replace('-', ' ', $lastSegment));
    $nameParts = preg_split('/\s+/', trim($actor['name'] ?? 'User'));
    $initials = mb_strtoupper(mb_substr($nameParts[0], 0, 1).(count($nameParts)>1 ? mb_substr(end($nameParts), 0, 1) : ''));
@endphp
<header class="sticky top-0 z-30 flex items-center justify-between gap-4 px-3 sm:px-6 py-4 border-b border-grey-100 bg-white">
    <button type="button" @click="mobileSidebar = !mobileSidebar" aria-label="Open menu" class="md:hidden"><x-capstone::icon name="Menu" /></button>
    <nav aria-label="breadcrumb"><ol class="flex flex-wrap items-center gap-2 text-sm"><li class="text-grey-400">SICATA</li>@if($breadcrumb)<li class="text-grey-300">/</li><li class="text-grey-600 font-medium">{{ $breadcrumb }}</li>@endif</ol></nav>
    <div class="flex items-center gap-2">
        <button type="button" class="h-9 w-9 inline-flex items-center justify-center text-grey-600 hover:bg-grey-25 rounded-full relative bg-white border" aria-label="Search"><x-capstone::icon name="Search" class="h-5 w-5" /></button>
        <a href="{{ url('/capstone/notifications') }}" class="h-9 w-9 inline-flex items-center justify-center text-grey-600 hover:bg-grey-25 rounded-full relative bg-white border" aria-label="Notifications"><x-capstone::icon name="Bell" class="h-5 w-5" /><span x-show="unread > 0" class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-error-100"></span></a>
        <div x-data="{ open:false }" class="relative" @click.outside="open=false" @keydown.escape.window="open=false">
            <button type="button" @click="open=!open" :aria-expanded="open" class="flex items-center gap-3 h-10 px-2 hover:bg-grey-25 rounded-md">
                <span class="h-8 w-8 flex items-center justify-center rounded-full bg-grey-100 text-grey-500 text-sm font-semibold">{{ $initials }}</span>
                <span class="flex-col items-start text-left hidden sm:flex"><span class="text-sm font-semibold text-grey-600 leading-tight">{{ $actor['name'] ?? 'User' }}</span><span class="text-xs text-grey-400 leading-tight">{{ ucfirst($activeRole ?? 'User') }}</span></span>
            </button>
            <div x-show="open" x-cloak class="absolute right-0 mt-2 w-56 rounded-md border bg-popover p-1 shadow-md z-50">
                <div class="px-2 py-1.5 text-sm"><p class="font-semibold">{{ $actor['name'] ?? '' }}</p><p class="text-xs text-muted-foreground">{{ $actor['email'] ?? '' }}</p></div><hr class="my-1">
                <a href="{{ url('/capstone/profile') }}" class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent"><x-capstone::icon name="User" />Profile</a>
                @if(in_array('admin', $actor['roles'] ?? []))<x-capstone::feature-link href="/admin/settings" class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent"><x-capstone::icon name="Settings" />Settings</x-capstone::feature-link>@endif
                <hr class="my-1"><form method="POST" action="{{ route('capstone.logout') }}">@csrf<button class="flex w-full items-center gap-2 rounded-sm px-2 py-1.5 text-sm text-error-100 hover:bg-accent"><x-capstone::icon name="LogOut" />Logout</button></form>
            </div>
        </div>
    </div>
</header>
