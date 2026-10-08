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

<div class="inline-block relative" x-data="{ open: false }" x-init="window.addEventListener('scroll', () => { if(open) open = false; }, true)" @click.outside="open = false" @close.stop="open = false" @resize.window="open = false">
    {{-- Three-dots trigger button --}}
    <button
        @click="open = !open; if(open) { const rect = $event.currentTarget.getBoundingClientRect(); $refs.dropdown.style.top = (rect.bottom + 4) + 'px'; $refs.dropdown.style.right = (window.innerWidth - rect.right) + 'px'; }"
        type="button"
        class="inline-flex items-center justify-center w-8 h-8 text-slate-500 bg-white border border-slate-200 rounded-lg hover:text-primary hover:bg-slate-50 hover:border-primary transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
        title="More options"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM8.5 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM14 10a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <template x-teleport="body">
        <div x-show="open"
             x-ref="dropdown"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed z-[9999] w-48 rounded-lg border border-slate-200 bg-white shadow-lg overflow-hidden"
             style="display: none;">
            <div class="py-1">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
