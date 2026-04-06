{{-- resources/views/components/ui/card-description.blade.php --}}
@props([])
<p {{ $attributes->merge(['class' => 'text-sm text-muted-foreground']) }}>
    {{ $slot }}
</p>
