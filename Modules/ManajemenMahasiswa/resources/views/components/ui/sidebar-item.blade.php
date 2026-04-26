@props(['route' => '#', 'icon' => '', 'label' => '', 'routeName' => ''])

@php
    $isActive = $routeName && request()->routeIs($routeName . '*');
@endphp

<a href="{{ $route }}"
   class="nav-link-item {{ $isActive ? 'active' : '' }}"
   :class="{ 'justify-content-center': !sidebarOpen }">
    <span class="nav-icon d-inline-flex">{!! $iconSlot ?? $icon !!}</span>
    <span class="nav-label" x-show="sidebarOpen" style="flex-grow:1;">{{ $slot->isNotEmpty() ? $slot : $label }}</span>
    @if(isset($badge))
        <span x-show="sidebarOpen">{!! $badge !!}</span>
    @endif
</a>
