@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-4',
])

<div {{ $attributes->class('bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden') }}>
    @if($title || $subtitle || isset($actions))
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between gap-4">
            <div>
                @if($title)
                    <h2 class="text-sm font-semibold text-slate-900">{{ $title }}</h2>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>