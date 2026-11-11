@props(['user','showEmail'=>false,'wrap'=>false])
@php
    $parts=explode(' ',$user->name ?? 'U');
    $initials=strtoupper(substr($parts[0]??'U',0,1).substr($parts[1]??$parts[0]??'U',0,1));
    $colors=['sky','navy','green','yellow','violet'];
    $color=$colors[crc32($user->email??'')%count($colors)];
@endphp
<div class="flex items-center justify-start gap-2 pr-3 w-full" style="min-width:0">
    <div class="mp-av {{ $color }} flex-shrink-0" style="min-width:28px;min-height:28px;max-width:28px;max-height:28px;font-size:10px;overflow:hidden;display:flex;align-items:center;justify-content:center">
        @if($user->avatar_url ?? null)<img src="{{ $user->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%">@else{{ $initials }}@endif
    </div>
    <div style="min-width:0"><div class="text-[13px] font-medium text-[#353849]" style="{{ $wrap?'white-space:normal;overflow-wrap:anywhere':'white-space:nowrap' }}" title="{{ $user->name }}">{{ $user->name }}</div>@if($showEmail)<div class="mp-settings-email" style="margin-top:3px">{{ $user->email }}</div>@endif</div>
</div>
