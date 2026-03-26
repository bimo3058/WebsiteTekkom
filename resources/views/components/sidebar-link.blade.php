@props(['href', 'icon', 'label', 'active' => false])

@php
    $activeClass = $active
        ? 'bg-blue-800/60 text-white font-semibold'
        : 'text-blue-100 hover:text-white hover:bg-blue-600/50';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'flex items-center px-2 py-2 rounded-lg transition-colors mb-0.5 ' . $activeClass]) }}
   :class="!sidebarOpen ? 'justify-center' : ''">
    <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'gap-2.5'">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
        </svg>
        <span x-show="sidebarOpen" class="text-sm whitespace-nowrap">{{ $label }}</span>
    </div>
</a>