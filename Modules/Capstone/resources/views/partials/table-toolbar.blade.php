@props([
    'title' => null,
    'searchPlaceholder' => 'Cari...',
    'searchLabel' => null,
    'filterLabel' => 'Filter',
    'sortLabel' => 'Urutkan',
])
{{-- Canonical Image 6 toolbar: Cari [Filter] [Urutkan]. Parent x-data must provide: search, page. Filter/sort content via slots. --}}
<div class="flex flex-wrap items-center justify-between gap-2.5 border-b border-slate-100 p-3">
    @if($title)<h2 class="text-sm font-bold text-foreground">{{ $title }}</h2>@endif
    <div class="flex items-center gap-2">
        <label class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-[13px] text-slate-500">
            <x-capstone::icon name="Search" size="15" />
            @isset($search)
                {{ $search }}
            @else
                <input x-model.debounce.300ms="search" @input="page = 1" type="search" placeholder="{{ $searchPlaceholder }}" aria-label="{{ $searchLabel ?? $searchPlaceholder }}" class="w-28 bg-transparent outline-none placeholder:text-slate-400 sm:w-44" />
            @endisset
        </label>
        @isset($filters)
            <x-capstone::dropdown>
                <x-slot:trigger>
                    <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 text-[13px] text-slate-500 hover:bg-slate-50" aria-label="{{ $filterLabel }}">
                        <x-capstone::icon name="ListFilter" size="15" /> {{ $filterLabel }}
                    </button>
                </x-slot:trigger>
                {{ $filters }}
            </x-capstone::dropdown>
        @endisset
        @isset($sort)
            <label class="inline-flex h-10 items-center gap-1.5 rounded-xl border border-slate-200 px-2.5 py-1.5 text-[13px] text-slate-500" title="{{ $sortLabel }}">
                <x-capstone::icon name="ArrowUpDown" size="15" />
                {{ $sort }}
            </label>
        @endisset
        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
</div>
