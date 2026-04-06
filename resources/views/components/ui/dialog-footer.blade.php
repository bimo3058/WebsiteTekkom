{{-- resources/views/components/ui/dialog-footer.blade.php --}}
@props([])
<div {{ $attributes->merge(['class' => 'flex flex-col-reverse sm:flex-row sm:justify-end gap-2 mt-6']) }}>
    {{ $slot }}
</div>
