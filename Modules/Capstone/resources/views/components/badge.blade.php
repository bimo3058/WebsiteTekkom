@props(['variant' => 'default'])
@php $colors = ['default'=>'border-transparent bg-primary text-primary-foreground', 'secondary'=>'border-transparent bg-secondary text-secondary-foreground', 'destructive'=>'border-transparent bg-destructive text-white', 'outline'=>'text-foreground']; @endphp
<span {{ $attributes->class(['inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 gap-1', $colors[$variant] ?? $colors['default']]) }}>{{ $slot }}</span>
