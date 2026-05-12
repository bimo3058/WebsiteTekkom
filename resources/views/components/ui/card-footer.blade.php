{{-- resources/views/components/ui/card-footer.blade.php --}}
@props([])
<div {{ $attributes->merge(['class' => 'px-6 pb-6 pt-0 flex items-center']) }}>
    {{ $slot }}
</div>
