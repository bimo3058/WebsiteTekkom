@props([
    'title',
    'subtitle' => null,
    'backLink' => null,
])

<div class="mb-8">
    @if($backLink)
        <a href="{{ $backLink }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/80 font-medium text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span>Kembali</span>
        </a>
    @endif
    
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-900 tracking-tight">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-2 text-sm text-slate-600">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
