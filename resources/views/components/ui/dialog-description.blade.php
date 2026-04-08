{{-- resources/views/components/ui/dialog-description.blade.php --}}
@props([])
<p {{ $attributes->merge(['class' => 'text-sm text-muted-foreground mt-1.5']) }}>
    {{ $slot }}
</p>
