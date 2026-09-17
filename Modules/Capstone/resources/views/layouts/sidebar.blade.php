@php
    $navigation = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/navigation.json')), true);
    $roles = $actor['roles'] ?? [];
    $combined = in_array('admin', $roles) && in_array('dosen', $roles);
    $sidebarRoles = $combined ? ['admin','dosen'] : [$activeRole ?? 'mahasiswa'];
@endphp
<div x-cloak x-show="mobileSidebar" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="mobileSidebar = false"></div>
<aside data-mobile-sidebar class="bg-sidebar text-sidebar-foreground flex h-svh shrink-0 flex-col border-r md:relative md:translate-x-0 fixed inset-y-0 left-0 z-40 transition-all" :class="[collapsed ? 'w-16' : 'w-64', mobileSidebar ? 'translate-x-0' : '-translate-x-full']">
    <div class="p-2"><div class="flex items-center justify-between px-2 py-2">
        <a href="{{ url('/capstone/dashboard') }}" x-show="!collapsed" class="flex min-w-0 items-center gap-2"><img src="{{ url('/capstone/assets/logo.png') }}" alt="Logo" class="size-8 object-contain"><span x-show="!collapsed" class="grid flex-1 text-left text-sm leading-tight"><span class="truncate font-semibold">SICATA</span><span class="truncate text-xs">Sistem Informasi Capstone &amp; TA</span></span></a>
        <button type="button" aria-label="Toggle sidebar" @click="toggleSidebar" class="hover:bg-sidebar-accent flex h-8 w-8 items-center justify-center rounded-md shrink-0"><x-capstone::icon name="ChevronLeft" class="size-4" /></button>
    </div>
    </div>
    <nav aria-label="Capstone navigation" class="min-h-0 flex-1 overflow-y-auto p-2 space-y-1">
        <div x-show="!collapsed" class="flex h-8 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70">Menu</div>
        @foreach($sidebarRoles as $navRole)
            @if($combined)<div x-show="!collapsed" class="px-2 pt-4 pb-2 text-xs font-medium text-sidebar-foreground/70">{{ ucfirst($navRole) }}</div>@endif
            <a href="{{ url('/capstone/'.$navRole.'/dashboard') }}" class="flex h-8 items-center gap-2 rounded-md p-2 text-sm hover:bg-sidebar-accent {{ ($pagePath ?? '') === '/'.$navRole.'/dashboard' ? 'bg-sidebar-accent font-medium' : '' }}" title="Dashboard"><x-capstone::icon name="LayoutDashboard" /><span x-show="!collapsed">Dashboard</span></a>
            @foreach($navigation[$navRole] ?? [] as $item)
                @php
                    $parentPath = match ($item['title']) {
                        'Progress & Docs' => '/mahasiswa/documents',
                        'Schedules' => '/mahasiswa/schedule',
                        'Evaluations' => '/mahasiswa/grades',
                        default => $item['url'] ?? $item['items'][0]['url'],
                    };
                    $itemReason = $navRole === 'mahasiswa' ? \Modules\Capstone\Support\BladeFeatureAccess::reason($parentPath, $featureAccess ?? []) : null;
                @endphp
                @if(isset($item['items']))
                    @php $expanded = collect($item['items'])->contains(fn($sub) => str_starts_with($pagePath ?? '', $sub['url'])); @endphp
                    <div x-data="{ expanded: {{ $expanded ? 'true' : 'false' }} }">
                        <button type="button" @disabled($itemReason) aria-disabled="{{ $itemReason ? 'true' : 'false' }}" class="flex h-8 w-full items-center gap-2 rounded-md p-2 text-sm hover:bg-sidebar-accent {{ $itemReason ? 'opacity-50' : '' }}" :aria-expanded="expanded" @click="if (collapsed) toggleSidebar(); expanded = !expanded" title="{{ $itemReason ?? $item['title'] }}"><x-capstone::icon :name="$item['icon'] === 'CalendarIcon' ? 'Calendar' : $item['icon']" /><span x-show="!collapsed">{{ $item['title'] }}</span><x-capstone::icon name="ChevronRight" class="ml-auto size-4 transition-transform" x-show="!collapsed" ::class="expanded && 'rotate-90'" /></button>
                        <div x-show="expanded && !collapsed" x-cloak class="border-sidebar-border mx-3.5 flex flex-col gap-1 border-l px-2.5 py-0.5">
                            @foreach($item['items'] as $sub)
                                @php
                                    $reason = $navRole === 'mahasiswa' ? \Modules\Capstone\Support\BladeFeatureAccess::reason($sub['url'], $featureAccess ?? []) : null;
                                @endphp
                                <a @if(!$reason) href="{{ url('/capstone'.$sub['url']) }}" @else aria-disabled="true" tabindex="-1" @endif title="{{ $reason ?? $sub['title'] }}" class="flex h-7 min-w-0 items-center rounded-md px-2 text-sm hover:bg-sidebar-accent {{ $reason ? 'pointer-events-none opacity-50' : '' }} {{ ($pagePath ?? '') === $sub['url'] ? 'bg-sidebar-accent font-medium' : '' }}">{{ $sub['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a @if(!$itemReason) href="{{ url('/capstone'.$item['url']) }}" @else aria-disabled="true" tabindex="-1" @endif class="flex h-8 items-center gap-2 rounded-md p-2 text-sm hover:bg-sidebar-accent {{ $itemReason ? 'pointer-events-none opacity-50' : '' }} {{ ($pagePath ?? '') === $item['url'] ? 'bg-sidebar-accent font-medium' : '' }}" title="{{ $itemReason ?? $item['title'] }}"><x-capstone::icon :name="$item['icon'] === 'CalendarIcon' ? 'Calendar' : $item['icon']" /><span x-show="!collapsed">{{ $item['title'] }}</span></a>
                @endif
            @endforeach
        @endforeach
        <div x-show="!collapsed" class="flex h-8 items-center rounded-md px-2 text-xs font-medium text-sidebar-foreground/70">System</div>
        <a href="{{ url('/capstone/notifications') }}" class="flex h-8 items-center gap-2 rounded-md p-2 text-sm hover:bg-sidebar-accent"><x-capstone::icon name="Bell" /><span x-show="!collapsed">Notifications</span><span x-show="unread > 0 && !collapsed" x-text="unread" class="ml-auto rounded-full bg-primary px-1.5 text-xs text-white"></span></a>
    </nav>
    <div class="p-2 border-t">
        @if(in_array('admin', $roles))<x-capstone::feature-link href="/admin/settings" class="flex h-8 items-center gap-2 rounded-md p-2 text-sm hover:bg-sidebar-accent"><x-capstone::icon name="Settings" /><span x-show="!collapsed">Settings</span></x-capstone::feature-link>@endif
        <form method="POST" action="{{ route('capstone.logout') }}">@csrf<button class="flex h-8 w-full items-center gap-2 rounded-md p-2 text-sm text-destructive hover:bg-sidebar-accent"><x-capstone::icon name="LogOut" /><span x-show="!collapsed">Logout</span></button></form>
    </div>
</aside>
