@props(['width' => '56', 'align' => 'right'])
@php $widthClass = 'w-'.$width; @endphp
<div x-data="{ open: false, highlightedIndex: -1 }" @keydown.escape.window="open = false; highlightedIndex = -1" {{ $attributes->class(['relative inline-block']) }}>
    <div @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" class="cursor-pointer">{{ $trigger ?? '' }}</div>
    <div x-show="open" x-cloak x-transition x-trap="open" @click.outside="open = false"
        @keydown.arrow-down.prevent="highlightedIndex = Math.min(highlightedIndex + 1, $el.querySelectorAll('[role=menuitem],[role=option]').length - 1)"
        @keydown.arrow-up.prevent="highlightedIndex = Math.max(highlightedIndex - 1, 0)"
        @keydown.home.prevent="highlightedIndex = 0" @keydown.end.prevent="highlightedIndex = $el.querySelectorAll('[role=menuitem],[role=option]').length - 1"
        role="menu" aria-label="Menu"
        class="absolute {{ $align === 'left' ? 'left-0' : 'right-0' }} mt-2 {{ $widthClass }} rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg z-[70] text-on-surface">
        @isset($heading)<p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $heading }}</p>@endisset
        {{ $slot }}
    </div>
</div>
