@props(['name', 'size' => 16])
@php
    static $icons;
    $icons ??= json_decode(file_get_contents(module_path('Capstone', 'resources/reference/icons.json')), true);
    $nodes = $icons[$name] ?? $icons['Circle'] ?? [];
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes }}>
    @foreach($nodes as [$tag, $properties])
        @php unset($properties['key']); @endphp
        <{{ $tag }} @foreach($properties as $key => $value) {{ $key }}="{{ $value }}" @endforeach />
    @endforeach
</svg>
