@props(['title' => null, 'variant' => 'default', 'icon' => null, 'role' => 'alert'])
@php
    $variantClasses = [
        'default' => '',
        'warning' => 'border-amber-500 bg-amber-50 text-amber-700 [&_h3]:text-amber-800',
        'success' => 'border-emerald-500 bg-emerald-50 text-emerald-700 [&_h3]:text-emerald-800',
        'info' => 'border-sky-500 bg-sky-50 text-sky-700 [&_h3]:text-sky-800',
        'destructive' => 'text-destructive border-destructive/50',
    ];
    $defaultIcons = ['warning' => 'TriangleAlert', 'success' => 'CircleCheck', 'info' => 'Info', 'destructive' => 'Info'];
    $iconColors = ['warning' => 'text-amber-600', 'success' => 'text-emerald-600', 'info' => 'text-sky-600', 'destructive' => 'text-destructive'];
    $iconName = $icon ?? $defaultIcons[$variant] ?? 'Info';
    $iconColor = $iconColors[$variant] ?? '';
@endphp
<div role="{{ $role }}" {{ $attributes->class(['relative w-full rounded-lg border px-4 py-3 text-sm grid grid-cols-[auto_1fr] items-start gap-x-3', $variantClasses[$variant] ?? $variantClasses['default']]) }}><x-capstone::icon :name="$iconName" class="h-4 w-4 mt-0.5 {{ $iconColor }}" /><div>@if($title)<h3 class="font-medium leading-none tracking-tight mb-1">{{ $title }}</h3>@endif<div class="text-sm leading-relaxed">{{ $slot }}</div></div></div>
