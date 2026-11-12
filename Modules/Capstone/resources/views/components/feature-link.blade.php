@props(['href'])
@php
    $path = str_starts_with($href, '/capstone/') ? substr($href, strlen('/capstone')) : $href;
    $state = request()->attributes->get('capstone_feature_access', []);
    $reason = $state ? \Modules\Capstone\Support\BladeFeatureAccess::reason($path, $state) : null;
    $resolvedHref = $reason ? null : \Modules\Capstone\Support\CapstoneUrl::to($path);
@endphp
<a @if($reason) role="link" aria-disabled="true" tabindex="-1" title="{{ $reason }}" @else href="{{ $resolvedHref }}" @endif {{ ($reason ? $attributes->except(['href', ':href', '@click']) : $attributes)->class(['opacity-50 cursor-not-allowed' => (bool) $reason]) }}>{{ $slot }}</a>
