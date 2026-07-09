@props([
    'hasActiveFilter' => false,
    'formId' => 'filterForm',
    'applyLabel' => 'Terapkan',
    'resetRoute' => null,
])

<div class="relative" x-data="{ filterOpen: false }" @click.away="filterOpen = false">
    {{-- Tombol Filter --}}
    <button @click="filterOpen = !filterOpen" type="button"
        class="flex items-center gap-2 px-5 py-2.5 bg-white border rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm
               {{ $hasActiveFilter ? 'border-primary text-primary bg-primary/5' : 'border-slate-200' }}">
        <svg class="w-4 h-4 {{ $hasActiveFilter ? 'text-primary' : 'text-slate-400' }}" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
        </svg>
        Filter
        @if($hasActiveFilter)
            <span class="w-2 h-2 rounded-full bg-primary"></span>
        @endif
        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Panel Dropdown --}}
    <div x-show="filterOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">

        {{-- Konten filter dari parent --}}
        {{ $slot }}

        {{-- Footer: tombol Reset dan Terapkan --}}
        <div class="flex gap-2 pt-2 border-t border-slate-100">
            @if($resetRoute)
                <a href="{{ $resetRoute }}"
                   class="flex-1 py-2 text-center text-xs font-bold text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg transition-colors">
                    Reset
                </a>
            @else
                <button type="button" @click="filterOpen = false"
                    class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
                    Tutup
                </button>
            @endif

            <button type="submit" form="{{ $formId }}" @click="filterOpen = false"
                class="flex-1 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:opacity-90 shadow-md shadow-primary/20 transition-all">
                {{ $applyLabel }}
            </button>
        </div>
    </div>
</div>
