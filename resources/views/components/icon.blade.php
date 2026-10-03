@props(['name', 'size' => 16, 'class' => ''])
@php
    $iconPath = public_path("images/icons/{$name}.svg");
    $svg = file_exists($iconPath) ? file_get_contents($iconPath) : '';
    // Replace known hardcoded colors with currentColor
    $svg = str_replace(
        ['#0D0D12', 'stroke="black"', 'fill="black"', 'stroke="#000"', 'fill="#000"'],
        ['currentColor', 'stroke="currentColor"', 'fill="currentColor"', 'stroke="currentColor"', 'fill="currentColor"'],
        $svg
    );
    // Force width/height to 100% so it fills the container
    $svg = preg_replace('/\bwidth="24"/', 'width="100%"', $svg);
    $svg = preg_replace('/\bheight="24"/', 'height="100%"', $svg);
@endphp
<span style="display:inline-flex;align-items:center;justify-content:center;width:{{ $size }}px;height:{{ $size }}px;flex-shrink:0;vertical-align:middle;line-height:0;" @if($class) class="{{ $class }}" @endif>{!! $svg !!}</span>
