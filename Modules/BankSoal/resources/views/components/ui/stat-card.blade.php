@props([
    'label',
    'value' => 0,
    'icon' => 'fa-chart-bar',
    'tone' => 'blue',
])

@php
    $tones = [
        'blue'  => 'bg-blue-100 text-blue-600',
        'green' => 'bg-emerald-100 text-emerald-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'red'   => 'bg-rose-100 text-rose-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
    $toneKey     = is_string($tone) ? $tone : 'blue';
    $toneClasses = $tones[$toneKey] ?? $tones['blue'];
@endphp

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-3">
    <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs {{ $toneClasses }} shrink-0">
            <i class="fas {{ $icon }}"></i>
        </div>
        <div>
            <p class="text-lg font-bold text-slate-900 leading-none">{{ $value }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">{{ $label }}</p>
        </div>
    </div>
</div>