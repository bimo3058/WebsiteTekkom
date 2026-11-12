@props(['root' => 'SICATA', 'current' => null])
<nav aria-label="breadcrumb" {{ $attributes->class(['text-xs text-on-surface/60 flex items-center gap-1.5']) }}>
    <span>{{ $root }}</span>
    @if($current)<span class="text-outline-strong" aria-hidden="true">/</span><span class="font-semibold text-on-surface" aria-current="page">{{ $current }}</span>@endif
    {{ $slot }}
</nav>
