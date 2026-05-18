{{-- resources/views/components/ui/action-menu.blade.php --}}
{{-- Three-dots action menu component for table rows --}}
{{-- Usage:
    <x-ui.action-menu>
        <x-ui.dropdown-item href="/edit/{{ $id }}">Edit</x-ui.dropdown-item>
        <x-ui.dropdown-item href="/view/{{ $id }}">View</x-ui.dropdown-item>
        <x-ui.dropdown-separator />
        <x-ui.dropdown-item class="text-destructive">Delete</x-ui.dropdown-item>
    </x-ui.action-menu>
--}}

@props([
    'align' => 'right',
])

@php
    $alignClasses = match($align) {
        'left'   => 'left-0 origin-top-left',
        'right'  => 'right-0 origin-top-right',
        'center' => 'left-1/2 -translate-x-1/2 origin-top',
        default  => 'right-0 origin-top-right',
    };
@endphp

<div class="inline-block" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    {{-- Three-dots trigger button --}}
    <button
        @click="open = !open"
        type="button"
        class="inline-flex items-center justify-center p-2 text-gray-500 rounded-md hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        title="More options"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM14 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-1 w-48 {{ $alignClasses }} rounded-lg border border-border bg-popover shadow-lg"
         style="display: none;">
        <div class="py-1">
            {{ $slot }}
        </div>
    </div>
</div>
