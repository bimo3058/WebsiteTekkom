@props(['href', 'icon', 'label', 'active' => false, 'color' => 'text-white'])

@php
    $activeClass = $active ? 'bg-slate-700/50 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-700/30';
@endphp

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'flex items-center px-2 py-2 rounded-lg transition-colors mb-1 ' . $activeClass]) }}
   :class="!sidebarOpen ? 'justify-center' : ''">
    <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'space-x-3'">
        <svg class="w-5 h-5 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
        </svg>
        <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">{{ $label }}</span>
    </div>
</a>