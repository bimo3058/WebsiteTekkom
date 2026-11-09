@props(['href' => '#', 'icon' => null, 'label' => '', 'title' => null, 'active' => false, 'badge' => null, 'disabled' => false, 'target' => '_self'])
{{-- Capstone mirror of resources/views/components/sidebar-link.blade.php.
     Same sb-item contract (pill, badge, disabled, collapsed), but driven by
     the Capstone Alpine shell state (`collapsed`) and Lucide `x-capstone::icon`
     instead of inline stroke-path strings. CSS lives in capstone::layouts.shell-styles. --}}
<a href="{{ $disabled ? '#' : $href }}"
   target="{{ $target }}"
   class="sb-item {{ $active ? 'is-active' : '' }} {{ $disabled ? 'is-disabled' : '' }}"
   :class="collapsed ? 'is-collapsed' : ''"
   @if($disabled) aria-disabled="true" tabindex="-1" @endif
   title="{{ $title ?? $label }}"
   {{ $attributes }}>
    @if($active)
        <span class="sb-item-pill"></span>
    @endif
    @if($icon)
        <x-capstone::icon :name="$icon" />
    @else
        {{ $slot }}
    @endif
    <span x-show="!collapsed" class="sb-item-label">{{ $label }}</span>
    @if($badge)
        <span x-show="!collapsed" x-text="{{ $badge }}" class="sb-item-badge {{ $active ? 'is-active' : '' }}"></span>
    @endif
</a>
