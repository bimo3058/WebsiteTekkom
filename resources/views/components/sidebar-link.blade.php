@props(['href', 'icon', 'label', 'active' => false, 'target' => '_self', 'badge' => null, 'disabled' => false])

<a href="{{ $disabled ? '#' : $href }}"
   target="{{ $target }}"
   class="sb-item {{ $active ? 'is-active' : '' }} {{ $disabled ? 'is-disabled' : '' }}"
   :class="!open ? 'is-collapsed' : ''"
   {{ $attributes }}>

    {{-- Left pill indicator --}}
    @if($active)
        <span class="sb-item-pill"></span>
    @endif

    @if($icon)
        <svg viewBox="0 0 24 24"
             fill="none"
             stroke="{{ $active ? 'var(--c-primary)' : 'currentColor' }}"
             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $icon }}"/>
        </svg>
    @else
        {{ $slot }}
    @endif

    <span x-show="open" class="sb-item-label">{{ $label }}</span>

    @if($badge)
        <span x-show="open" class="sb-item-badge {{ $active ? 'is-active' : '' }}">{{ $badge }}</span>
    @endif
</a>

<style>
.sb-item {
  position: relative;
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 7px 10px 7px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: var(--c-fg-sec);
  cursor: pointer;
  text-decoration: none;
  transition: background .12s, color .12s;
}
.sb-item svg {
  width: 16px;
  height: 16px;
  color: var(--c-fg-muted);
  flex-shrink: 0;
}
.sb-item:hover { background: var(--c-bg); }

/* Active state — subtle highlight + navy text */
.sb-item.is-active {
  background: var(--c-primary-subtle);
  color: var(--c-primary);
  font-weight: 600;
}
.sb-item.is-active svg {
  color: var(--c-primary);
}

/* Left pill indicator */
.sb-item-pill {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 3px;
  height: 20px;
  background: var(--c-primary);
  border-radius: 0 3px 3px 0;
  flex-shrink: 0;
}

.sb-item.is-disabled { opacity: .45; cursor: default; pointer-events: none; }
.sb-item.is-collapsed { justify-content: center; padding-left: 0; padding-right: 0; }
.sb-item.is-collapsed .sb-item-pill { display: none; }

.sb-item-label {
  flex: 1;
  letter-spacing: .01em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.sb-item-badge {
  background: var(--c-error-subtle);
  color: var(--c-error);
  font-size: 11px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 9999px;
  flex-shrink: 0;
}
.sb-item-badge.is-active {
  background: var(--c-primary-subtle);
  color: var(--c-primary);
}
</style>