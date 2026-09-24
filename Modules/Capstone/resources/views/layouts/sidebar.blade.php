@php
    $navigation = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/navigation.json')), true);
    $roles = $actor['roles'] ?? [];
    $combined = in_array('admin', $roles) && in_array('dosen', $roles);
    $sidebarRoles = $combined ? ['admin','dosen'] : [$activeRole ?? 'mahasiswa'];
@endphp
<div x-cloak x-show="mobileSidebar" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="mobileSidebar = false"></div>
<aside data-mobile-sidebar class="sitkom-sidebar-capstone fixed inset-y-0 left-0 z-40 flex h-svh shrink-0 flex-col md:relative md:translate-x-0" :class="[collapsed ? 'w-16 is-collapsed' : 'w-[240px]', mobileSidebar ? 'translate-x-0' : '-translate-x-full']">
    <div class="sb-brand">
        <a href="{{ url('/capstone/dashboard') }}" x-show="!collapsed" class="sb-brand-link"><img src="{{ url('/capstone/assets/logo.png') }}" alt="Logo" class="sb-brand-logo"><span class="sb-brand-text"><span class="sb-brand-name">SICATA</span><span class="sb-brand-tag">Sistem Informasi Capstone &amp; TA</span></span></a>
        <button type="button" aria-label="Toggle sidebar" title="Toggle Sidebar" @click="toggleSidebar" class="sb-collapse-btn"><span :style="collapsed ? 'transform:rotate(180deg)' : ''" style="transition:transform .25s ease;display:inline-flex"><x-capstone::icon name="ChevronLeft" /></span></button>
    </div>
    <nav aria-label="Capstone navigation" class="sb-nav">
        <div x-show="!collapsed" class="sb-section-label">Menu</div>
        @foreach($sidebarRoles as $navRole)
            @if($combined)<div x-show="!collapsed" class="sb-section-label">{{ ucfirst($navRole) }}</div>@endif
            <x-capstone::sb-link :href="url('/capstone/'.$navRole.'/dashboard')" icon="LayoutDashboard" label="Dashboard" :active="($pagePath ?? '') === '/'.$navRole.'/dashboard'" />
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
                        <button type="button" @disabled($itemReason) aria-disabled="{{ $itemReason ? 'true' : 'false' }}" class="sb-item w-full {{ $expanded ? 'is-active' : '' }} {{ $itemReason ? 'is-disabled' : '' }}" :class="collapsed ? 'is-collapsed' : ''" :aria-expanded="expanded" @click="if (collapsed) toggleSidebar(); expanded = !expanded" title="{{ $itemReason ?? $item['title'] }}">@if($expanded)<span class="sb-item-pill"></span>@endif<x-capstone::icon :name="$item['icon'] === 'CalendarIcon' ? 'Calendar' : $item['icon']" /><span x-show="!collapsed" class="sb-item-label">{{ $item['title'] }}</span><x-capstone::icon name="ChevronRight" class="ml-auto size-4 transition-transform" x-show="!collapsed" ::class="expanded && 'rotate-90'" /></button>
                        <div x-show="expanded && !collapsed" x-cloak class="sb-sublist">
                            @foreach($item['items'] as $sub)
                                @php
                                    $reason = $navRole === 'mahasiswa' ? \Modules\Capstone\Support\BladeFeatureAccess::reason($sub['url'], $featureAccess ?? []) : null;
                                    $subActive = ($pagePath ?? '') === $sub['url'];
                                @endphp
                                <a @if(!$reason) href="{{ url('/capstone'.$sub['url']) }}" @else aria-disabled="true" tabindex="-1" @endif title="{{ $reason ?? $sub['title'] }}" class="sb-subitem {{ $subActive ? 'is-active' : '' }} {{ $reason ? 'is-disabled' : '' }}">@if($subActive)<span class="sb-item-pill"></span>@endif{{ $sub['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-capstone::sb-link :href="url('/capstone'.$item['url'])" :icon="$item['icon'] === 'CalendarIcon' ? 'Calendar' : $item['icon']" :label="$item['title']" :active="($pagePath ?? '') === $item['url']" :disabled="(bool) $itemReason" :title="$itemReason ?? $item['title']" />
                @endif
            @endforeach
        @endforeach
    </nav>
    <div class="sb-footer">
        <x-capstone::sb-link :href="url('/dashboard')" icon="House" label="Back to Main Dashboard" title="Back to main dashboard" />
        <form method="POST" action="{{ route('capstone.logout') }}" style="margin:0;">@csrf<button type="submit" class="sb-item sb-link-danger w-full text-left" :class="collapsed ? 'is-collapsed' : ''"><x-capstone::icon name="LogOut" /><span x-show="!collapsed" class="sb-item-label">Logout</span></button></form>
    </div>
</aside>
