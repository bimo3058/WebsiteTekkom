@props(['href', 'icon', 'label', 'active' => false, 'target' => '_self', 'badge' => null, 'disabled' => false])

@php
    $bgHover   = 'var(--c-bg)';
    $bgDefault = 'transparent';
    $bgVal     = $active ? 'var(--c-primary)' : $bgDefault;
    $mouseOver = (!$active && !$disabled) ? "this.style.background='" . $bgHover . "'" : '';
    $mouseOut  = (!$active && !$disabled) ? "this.style.background='" . $bgDefault . "'" : '';
@endphp

<a href="{{ $disabled ? '#' : $href }}"
   target="{{ $target }}"
   style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; cursor:{{ $disabled ? 'default' : 'pointer' }}; background:{{ $bgVal }}; opacity:{{ $disabled ? '0.45' : '1' }}; transition:background 0.12s; text-decoration:none; margin-bottom:2px;"
   @if($mouseOver) onmouseover="{{ $mouseOver }}" @endif
   @if($mouseOut)  onmouseout="{{ $mouseOut }}"   @endif
   :class="!open ? 'justify-center' : ''"
   {{ $attributes }}>

    @if($icon)
        <svg style="width:16px; height:16px; flex-shrink:0; color:{{ $active ? '#fff' : 'var(--c-fg-muted)' }};"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="{{ $icon }}"/>
        </svg>
    @else
        {{ $slot }}
    @endif

    <span x-show="open"
          style="font-size:14px; font-weight:{{ $active ? '600' : '500' }}; color:{{ $active ? '#fff' : 'var(--c-fg-sec)' }}; flex:1; letter-spacing:0.01em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
        {{ $label }}
    </span>

    @if($badge)
        <span x-show="open"
              style="background:{{ $active ? 'rgba(255,255,255,0.25)' : 'var(--c-error-subtle)' }}; color:{{ $active ? '#fff' : 'var(--c-error)' }}; font-size:11px; font-weight:600; padding:2px 7px; border-radius:9999px; flex-shrink:0;">
            {{ $badge }}
        </span>
    @endif

</a>