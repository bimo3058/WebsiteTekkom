@props(['src' => null, 'alt' => '', 'size' => 'md', 'initials' => null])
@php
    $sizes = ['sm' => 'size-8 text-xs', 'md' => 'size-10 text-sm', 'lg' => 'size-12 text-base'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp
@if($src)
<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->class(['rounded-full object-cover shrink-0', $sizeClass]) }} />
@else
<span {{ $attributes->class(['flex items-center justify-center rounded-full font-bold shrink-0 text-white', $sizeClass]) }} style="background:linear-gradient(135deg,#5C78B8,#0B266E)" aria-hidden="true">{{ $initials ?? $slot }}</span>
@endif
