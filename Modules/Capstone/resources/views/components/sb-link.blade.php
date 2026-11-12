@props(['href' => '#', 'icon' => null, 'label' => '', 'title' => null, 'active' => false, 'badge' => null, 'disabled' => false, 'target' => '_self'])
{{-- PenguinUI sidebar link: same sb-item contract (pill, badge, disabled, collapsed),
     driven by Capstone Alpine shell state (`collapsed`) + Phosphor `x-capstone::icon`.
     CSS lives in capstone::layouts.shell-styles (surface/primary tokens). --}}
<a href="{{ $disabled ? '#' : $href }}"
   target="{{ $target }}"
   class="sb-item {{ $active ? 'is-active' : '' }} {{ $disabled ? 'is-disabled' : '' }}"
   :class="collapsed ? 'is-collapsed' : ''"
   @if($disabled) aria-disabled="true" tabindex="-1" @endif
   @if($active) aria-current="page" @endif
   title="{{ $title ?? $label }}"
   {{ $attributes }}>
    @if($active)
        <span class="sb-item-pill" aria-hidden="true"></span>
    @endif
    @if($icon)
        <x-capstone::icon :name="$icon" size="16" />
    @else
        {{ $slot }}
    @endif
    <span x-show="!collapsed" class="sb-item-label">{{ $label }}</span>
    @if($badge)
        <span x-show="!collapsed" x-text="{{ $badge }}" class="sb-item-badge {{ $active ? 'is-active' : '' }}"></span>
    @endif
</a>
