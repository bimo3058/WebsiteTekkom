@props(['href' => '#'])
@php
    $resolvedHref = $href && str_starts_with($href, '/') ? \Modules\Capstone\Support\CapstoneUrl::to($href) : $href;
@endphp
<a href="{{ $resolvedHref }}" {{ $attributes->class(['text-primary underline-offset-4 hover:underline']) }}>{{ $slot }}</a>
