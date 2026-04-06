{{-- resources/views/components/ui/dialog.blade.php --}}
{{-- Requires Alpine.js --}}
@props([
    'name'      => 'dialog',
    'maxWidth'  => 'lg',
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        default => 'sm:max-w-lg',
    };
@endphp

<div x-data="{ show: false }"
     x-on:open-{{ $name }}.window="show = true"
     x-on:close-{{ $name }}.window="show = false"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    {{-- Overlay --}}
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50"
         @click="show = false">
    </div>

    {{-- Panel --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="show"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full {{ $maxWidthClass }} rounded-lg border border-border bg-background p-6 shadow-lg"
             @click.outside="show = false">
            {{ $slot }}
        </div>
    </div>
</div>
