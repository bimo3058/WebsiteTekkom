{{-- resources/views/components/ui/dropdown.blade.php --}}
{{-- Requires Alpine.js (sudah termasuk di Laravel Breeze/Jetstream) --}}
@props([
    'align' => 'right',
    'width' => '48',
])

@php
    $alignClasses = match($align) {
        'left'   => 'left-0 origin-top-left',
        'right'  => 'right-0 origin-top-right',
        'center' => 'left-1/2 -translate-x-1/2 origin-top',
        default  => 'right-0 origin-top-right',
    };
    $widthClass = match($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    {{-- Trigger --}}
    <div @click="open = !open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    {{-- Menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $widthClass }} {{ $alignClasses }} rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md"
         style="display: none;">
        {{ $slot }}
    </div>
</div>
