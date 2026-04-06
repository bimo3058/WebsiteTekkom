{{-- resources/views/components/ui/table-header.blade.php --}}
@props([])
<thead {{ $attributes->merge(['class' => '[&_tr]:border-b']) }}>
    {{ $slot }}
</thead>
