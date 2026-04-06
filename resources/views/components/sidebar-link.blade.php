@props(['href', 'icon', 'label', 'active' => false])

@php
    // Warna sesuai Design System: Primary 50 (#F1E9FF) & Primary 500 (#5E53F4)
    $activeClass = $active
        ? 'bg-[#F1E9FF] text-[#5E53F4] font-semibold shadow-sm'
        : 'text-[#6C757D] hover:text-[#1A1C1E] hover:bg-[#F8F9FA]';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group mb-1 ' . $activeClass]) }}
   :class="!sidebarOpen ? 'justify-center px-2' : ''">
    
    <div class="flex items-center" :class="!sidebarOpen ? 'justify-center w-full' : 'gap-3'">
        {{-- Render SVG icon ATAU slot content, tapi tidak keduanya --}}
        @if($icon)
            <svg class="w-5 h-5 flex-shrink-0 transition-all group-hover:scale-110 {{ $active ? 'text-[#5E53F4]' : 'text-[#ADB5BD] group-hover:text-[#1A1C1E]' }}" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24"
                 stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
        @else
            {{-- Jika tidak ada SVG icon prop, render slot content (untuk Material Symbols, etc) --}}
            {{ $slot }}
        @endif
        
        <span x-show="sidebarOpen" 
              class="text-sm tracking-tight whitespace-nowrap overflow-hidden font-medium">
            {{ $label }}
        </span>
    </div>

    {{-- Indikator titik aktif di kanan sesuai design modern --}}
    @if($active)
        <div x-show="sidebarOpen" class="ml-auto size-1.5 rounded-full bg-[#5E53F4] animate-in fade-in zoom-in duration-300"></div>
    @endif
</a>