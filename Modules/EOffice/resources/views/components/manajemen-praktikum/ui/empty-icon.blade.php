@props(['name'])
<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
    stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;" aria-hidden="true" focusable="false">
    @switch($name)
        @case('task')
            <path d="M19 8V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-5M6 6h9M6 9h9M6 12h9" />
            <path d="m15 16 6-9 2 1-6 9-3 3 1-4Zm0 0 2 1" />
            @break
        @case('medal')
            <circle cx="12" cy="9" r="7" />
            <path d="m12 5 1.2 2.5 2.8.4-2 2 .5 2.8L12 11.4l-2.5 1.3.5-2.8-2-2 2.8-.4L12 5ZM6.3 13l-3.3 8 4-1 2 3 3-7 3 7 2-3 4 1-3.3-8" />
            @break
        @case('book')
            <path d="M12 6C10 4 7 3 4 3v15c3 0 6 1 8 3m0-15c2-2 5-3 8-3v15c-3 0-6 1-8 3V6M4 5H2v16h20V5h-2" />
            @break
        @case('bell')
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
            @break
    @endswitch
</svg>
