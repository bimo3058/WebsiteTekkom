@props(['title'=>null,'searchPlaceholder'=>'Cari...','columns'=>[],'emptyTitle'=>'Tidak ada data ditemukan','emptyDescription'=>'Coba ubah filter','icon'=>'heroicon-o-document-text','grouped'=>false])
@php
$emptyIconMap = [
    'FileText' => 'heroicon-o-document-text',
    'Search' => 'heroicon-o-magnifying-glass',
    'CalendarDays' => 'heroicon-o-calendar-days',
    'Building2' => 'heroicon-o-building-office-2',
    'Video' => 'heroicon-o-video-camera',
];
$emptyIcon = $emptyIconMap[$icon] ?? $icon;
@endphp
<div class="rounded-radius border border-outline bg-surface text-on-surface shadow-xs overflow-hidden">
    <div class="border-b border-outline px-6 py-4"><div class="flex flex-col gap-4 sm:flex-row sm:items-center">@if($title)<h2 class="text-lg font-semibold text-on-surface-strong flex items-center">{{ $title }}</h2>@endif<div class="flex flex-1 flex-col gap-3 sm:flex-row sm:justify-end"><div class="relative w-full sm:w-72"><span class="absolute top-1/2 left-3 -translate-y-1/2 text-on-surface/40"><x-capstone::icon name="Search" size="16" /></span><input type="text" placeholder="{{ $searchPlaceholder }}" x-model.debounce.200ms="search" @input="goToPage(1)" class="file:text-on-surface placeholder:text-on-surface/50 selection:bg-primary selection:text-on-primary border-outline h-10 w-full min-w-0 rounded-radius border bg-surface pl-9 pr-3 py-1 text-sm text-on-surface shadow-xs outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-primary/20" aria-label="{{ $searchPlaceholder }}" /></div>{{ $filters ?? '' }}</div></div></div>
    {{ $bulkActions ?? '' }}
    @include('capstone::partials.loading')
    <div x-show="!loading && !error && !filtered.length" x-cloak><x-empty :title="$emptyTitle" :description="$emptyDescription" :icon="$emptyIcon" /></div>
    <div x-show="!loading && !error && filtered.length" x-cloak class="relative w-full overflow-x-auto"><table class="w-full caption-bottom text-sm text-on-surface"><thead class="[&_tr]:border-b [&_tr]:border-outline"><tr class="border-b border-outline bg-surface-alt/50">@foreach($columns as $key=>$label)<th scope="col" class="h-10 px-4 text-left align-middle font-semibold text-on-surface whitespace-nowrap">@if($loop->first && isset($selectionHeader)){{ $selectionHeader }}@elseif(is_string($key))<button type="button" @click="sort('{{ $key }}')" class="inline-flex items-center gap-2 py-1.5 hover:text-on-surface-strong focus-visible:ring-2 focus-visible:ring-outline-strong rounded-radius">{{ $label }}<x-capstone::icon name="ArrowsDownUp" size="16" /></button>@else{{ $label }}@endif</th>@endforeach</tr></thead>@if($grouped){{ $slot }}@else<tbody class="[&_tr:last-child]:border-0 [&_tr]:border-b [&_tr]:border-outline [&_tr:hover]:bg-surface-alt/50">{{ $slot }}</tbody>@endif</table></div>
    <div x-show="!loading && !error && filtered.length" x-cloak>
        @include('capstone::partials.table-footer', ['page' => 'page', 'pageCount' => 'pageCount', 'total' => 'filtered.length', 'from' => 'showingFrom', 'to' => 'showingTo', 'pageList' => 'pageList', 'prevAction' => 'prevPage()', 'nextAction' => 'nextPage()', 'goAction' => 'goToPage(p)'])
    </div>
</div>
