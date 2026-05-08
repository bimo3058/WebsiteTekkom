@props(['href', 'icon', 'label', 'active' => false, 'target' => '_self', 'badge' => null, 'disabled' => false])

<a href="{{ $disabled ? '#' : $href }}"
   target="{{ $target }}"
   class="sb-item {{ $active ? 'is-active' : '' }} {{ $disabled ? 'is-disabled' : '' }}"
   :class="!open ? 'is-collapsed' : ''"
   {{ $attributes }}>

    @if($icon)
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
.sb-item{
  display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;
  font-size:13px;font-weight:500;color:var(--c-fg-sec);cursor:pointer;text-decoration:none;
  transition:background .12s,color .12s;
}
.sb-item svg{width:18px;height:18px;color:var(--c-fg-muted);flex-shrink:0;}
.sb-item:hover{background:var(--c-bg);}
.sb-item.is-active{background:var(--c-primary);color:#fff;font-weight:600;}
.sb-item.is-active svg{color:#fff;}
.sb-item.is-disabled{opacity:.45;cursor:default;pointer-events:none;}
.sb-item.is-collapsed{justify-content:center;padding-left:0;padding-right:0;}
.sb-item-label{flex:1;letter-spacing:.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.sb-item-badge{
  background:var(--c-error-subtle);color:var(--c-error);font-size:11px;font-weight:600;
  padding:2px 7px;border-radius:9999px;flex-shrink:0;
}
.sb-item-badge.is-active{background:rgba(255,255,255,.25);color:#fff;}
</style>
