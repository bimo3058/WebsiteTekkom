{{--
    Partial: profile/partials/_info-row.blade.php
    Usage: @include('profile.partials._info-row', ['label' => 'NIM', 'value' => '...'])
--}}
<div class="flex items-start justify-between gap-2 py-2 border-b border-slate-100 last:border-0">
    <span class="text-[11px] text-slate-400 font-medium flex-shrink-0">{{ $label }}</span>
    <span class="text-[12px] text-slate-700 font-semibold text-right">{{ $value }}</span>
</div>