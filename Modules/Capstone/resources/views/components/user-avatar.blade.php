@props([
    'name' => null,
    'src' => null,
    'size' => 'sm',
    'online' => false,
    'suspended' => false,
])
@php
    $sizes = [
        'xs' => ['px' => 28, 'font' => '10px', 'dot' => '7px', 'border' => '1.5px'],
        'sm' => ['px' => 32, 'font' => '11px', 'dot' => '8px', 'border' => '1.5px'],
        'md' => ['px' => 36, 'font' => '12px', 'dot' => '9px', 'border' => '1.5px'],
        'lg' => ['px' => 40, 'font' => '13px', 'dot' => '10px', 'border' => '2px'],
        'xl' => ['px' => 84, 'font' => '28px', 'dot' => '12px', 'border' => '1.5px'],
    ];
    $s = $sizes[$size] ?? $sizes['sm'];
    $initials = '';
    if ($name) {
        $parts = preg_split('/\s+/', trim($name));
        $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));
    }
    $wrapStyle = 'background:#F3F4F6;border:' . $s['border'] . ' solid #E5E7EB;';
    $filterStyle = $suspended ? 'filter:grayscale(0.5);opacity:0.7;' : '';
    $showDot = $online && ! $suspended;
@endphp
<div style="position:relative;flex-shrink:0;">
    <div style="width:{{ $s['px'] }}px;height:{{ $s['px'] }}px;border-radius:50%;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:{{ $s['font'] }};font-weight:700;{{ $wrapStyle }}{{ $filterStyle }}">
        @if ($src)
            <img src="{{ $src }}" alt="" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover;">
        @elseif ($name)
            <span style="color:#6B7280;">{{ $initials ?: '?' }}</span>
        @else
            <x-capstone::icon name="User" size="16" />
        @endif
    </div>
    @if ($showDot)
        <span aria-hidden="true" style="position:absolute;bottom:-1px;right:-1px;width:{{ $s['dot'] }};height:{{ $s['dot'] }};border-radius:50%;background:#22C55E;border:{{ $s['border'] }} solid #fff;"></span>
    @endif
</div>
