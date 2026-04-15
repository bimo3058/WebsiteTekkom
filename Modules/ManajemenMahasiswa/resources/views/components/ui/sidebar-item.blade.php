@props(['route' => '#', 'icon' => '', 'label' => '', 'routeName' => ''])

@php
    $isActive = $routeName && request()->routeIs($routeName . '*');
@endphp

<a href="{{ $route }}"
   class="nav-link-item {{ $isActive ? 'active' : '' }}">
    <span class="nav-icon">{{ $icon }}</span>
    {{ $label }}
</a>
