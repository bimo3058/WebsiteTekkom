@props([
    'align' => 'right',
])
@php
    $alignClasses = match ($align) {
        'left' => 'left-0 origin-top-left',
        'right' => 'right-0 origin-top-right',
        'center' => 'left-1/2 -translate-x-1/2 origin-top',
        default => 'right-0 origin-top-right',
    };
@endphp
<div class="relative inline-block" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" @keydown.escape.window="open = false" @resize.window="open = false">
    <div @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" class="cursor-pointer">{{ $trigger ?? '' }}</div>
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" role="menu" aria-label="Actions" class="absolute z-[70] mt-2 w-48 {{ $alignClasses }} rounded-xl border border-outline bg-surface p-1.5 text-on-surface shadow-lg" style="display:none;">{{ $slot }}</div>
</div>
