    <div class="flex items-center gap-1.5">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span
                class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        <div
            class="flex items-center rounded-md border border-slate-200 bg-white overflow-hidden text-[13px] shadow-sm font-medium">
            {{-- Pagination Elements --}}@if(empty($elements))<span class="w-8 h-8 flex items-center justify-center border-r border-slate-200 bg-[#354371] text-white">1</span>@endif
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span
                        class="w-8 h-8 flex items-center justify-center border-r border-slate-200 text-slate-400 bg-white tracking-wider">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center border-r border-slate-200 bg-[#354371] text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center border-r border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span
                class="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </div>
