@php
    $navigation = json_decode(file_get_contents(module_path('Capstone', 'resources/reference/navigation.json')), true);
    $roles = $actor['roles'] ?? [];
    $combined = in_array('admin', $roles) && in_array('dosen', $roles);
    $sidebarRoles = $combined ? ['admin','dosen'] : [$activeRole ?? 'mahasiswa'];
@endphp
<div x-cloak x-show="mobileSidebar" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="mobileSidebar = false" @keydown.escape.window="mobileSidebar = false"></div>
<aside data-mobile-sidebar x-trap="mobileSidebar && window.innerWidth < 768" class="sitkom-sidebar-capstone fixed inset-y-0 left-0 z-40 flex h-svh shrink-0 flex-col md:sticky md:top-0 md:bottom-auto md:h-svh md:max-h-svh md:shrink-0 md:self-start md:translate-x-0" :class="[collapsed ? 'w-16 is-collapsed' : 'w-60', mobileSidebar ? 'translate-x-0' : '-translate-x-full']">
    <div class="sb-brand">
        <a href="{{ route('capstone.blade.dashboard') }}" x-show="!collapsed" class="sb-brand-link"><img src="{{ url('/capstone/assets/logo.png') }}" alt="Logo" class="sb-brand-logo"><span class="sb-brand-text"><span class="sb-brand-name">SICATA</span><span class="sb-brand-tag">Sistem Informasi Capstone &amp; TA</span></span></a>
        <button type="button" aria-label="Alihkan sidebar" title="Alihkan Sidebar" :aria-expanded="(!collapsed).toString()" aria-controls="capstone-sidebar-nav" @click="toggleSidebar" class="sb-collapse-btn"><span :style="collapsed ? 'transform:rotate(180deg)' : ''" style="transition:transform .25s ease;display:inline-flex"><x-capstone::icon name="CaretLeft" size="16" /></span></button>
    </div>
    <nav id="capstone-sidebar-nav" aria-label="Capstone navigation" class="sb-nav">
        <div x-show="!collapsed" class="sb-section-label">Menu</div>
        @foreach($sidebarRoles as $navRole)
            @if($combined)<div x-show="!collapsed" class="sb-section-label">{{ ucfirst($navRole) }}</div>@endif
            <x-capstone::sb-link :href="\Modules\Capstone\Support\CapstoneUrl::to('/'.$navRole.'/dashboard')" icon="LayoutDashboard" label="Dashboard" :active="($pagePath ?? '') === '/'.$navRole.'/dashboard'" />
            @foreach($navigation[$navRole] ?? [] as $item)
                @php
                    $parentPath = match ($item['title']) {
                        'Progres & Dokumen' => '/mahasiswa/documents',
                        'Jadwal' => '/mahasiswa/schedule',
                        'Evaluasi' => '/mahasiswa/grades',
                        default => $item['url'] ?? $item['items'][0]['url'],
                    };
                    $itemReason = $navRole === 'mahasiswa' ? \Modules\Capstone\Support\BladeFeatureAccess::reason($parentPath, $featureAccess ?? []) : null;
                @endphp
                @if(isset($item['items']))
                    @php $expanded = collect($item['items'])->contains(fn($sub) => str_starts_with($pagePath ?? '', $sub['url'])); @endphp
                    <div x-data="{ expanded: {{ $expanded ? 'true' : 'false' }} }">
                        <button type="button" @disabled($itemReason) aria-disabled="{{ $itemReason ? 'true' : 'false' }}" class="sb-item w-full {{ $itemReason ? 'is-disabled' : '' }}" :class="collapsed ? 'is-collapsed' : ''" :aria-expanded="expanded.toString()" @click="if (collapsed) toggleSidebar(); expanded = !expanded" title="{{ $itemReason ?? $item['title'] }}"><x-capstone::icon :name="$item['icon']" size="16" /><span x-show="!collapsed" class="sb-item-label">{{ $item['title'] }}</span><x-capstone::icon name="CaretRight" size="16" class="ml-auto transition-transform" x-show="!collapsed" ::class="expanded && 'rotate-90'" /></button>
                        <div x-show="expanded && !collapsed" x-cloak x-collapse class="sb-sublist">
                            @foreach($item['items'] as $sub)
                                @php
                                    $reason = $navRole === 'mahasiswa' ? \Modules\Capstone\Support\BladeFeatureAccess::reason($sub['url'], $featureAccess ?? []) : null;
                                    $subActive = ($pagePath ?? '') === $sub['url'];
                                @endphp
                                <a @if(!$reason) href="{{ \Modules\Capstone\Support\CapstoneUrl::to($sub['url']) }}" @else aria-disabled="true" tabindex="-1" @endif title="{{ $reason ?? $sub['title'] }}" class="sb-subitem {{ $subActive ? 'is-active' : '' }} {{ $reason ? 'is-disabled' : '' }}">@if($subActive)<span class="sb-item-pill"></span>@endif{{ $sub['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-capstone::sb-link :href="\Modules\Capstone\Support\CapstoneUrl::to($item['url'])" :icon="$item['icon']" :label="$item['title']" :active="($pagePath ?? '') === $item['url']" :disabled="(bool) $itemReason" :title="$itemReason ?? $item['title']" />
                @endif
            @endforeach
        @endforeach
    </nav>
    <div class="sb-footer">
        <x-capstone::sb-link :href="url('/dashboard')" icon="House" label="Kembali ke Dashboard Utama" title="Kembali ke dashboard utama" />
        <form method="POST" action="{{ route('capstone.logout') }}" style="margin:0;">@csrf<button type="submit" class="sb-item sb-link-danger w-full text-left" :class="collapsed ? 'is-collapsed' : ''"><x-capstone::icon name="LogOut" /><span x-show="!collapsed" class="sb-item-label">Logout</span></button></form>
    </div>
</aside>
