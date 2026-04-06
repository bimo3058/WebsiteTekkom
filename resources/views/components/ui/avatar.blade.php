{{-- resources/views/components/ui/avatar.blade.php --}}
@props([
    'src'      => null,
    'alt'      => '',
    'fallback' => '?',
    'size'     => 'default',
])

@php
    $sizes = [
        'xs'      => 'size-7 text-[10px]',
        'sm'      => 'size-8 text-xs',
        'default' => 'size-10 text-sm',
        'lg'      => 'size-12 text-base',
        'xl'      => 'size-14 text-lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['default'];
@endphp

<div {{ $attributes->merge(['class' => "relative flex shrink-0 overflow-hidden rounded-full $sizeClass"]) }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="aspect-square h-full w-full object-cover" />
    @else
        <div class="flex h-full w-full items-center justify-center rounded-full bg-muted font-medium text-muted-foreground">
            {!! $fallback !!}
        </div>
    @endif
</div>