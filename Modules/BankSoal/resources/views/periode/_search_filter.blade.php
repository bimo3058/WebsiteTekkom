@php
    $statusFilter = $statusFilter ?? request('status', 'all');
@endphp
<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
    <form method="GET" action="{{ route('banksoal.periode.setup') }}" id="searchForm" style="display:flex; align-items:center; gap:8px; margin:0;">
        <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
        <input type="hidden" name="status" value="{{ $statusFilter }}">

        {{-- Search --}}
        <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
            <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Periode..."
                   style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)'"
                   onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
        </div>

        {{-- Filter (Status) dropdown --}}
        <div class="relative inline-block" x-data="{ open: false }">
            <button type="button" @click="open = !open" @click.outside="open = false"
                    class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold text-[var(--c-fg-sec)] whitespace-nowrap cursor-pointer transition-all box-border"
                    style="border-color:var(--c-border); font-family:inherit;"
                    :style="open ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                    <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                </svg>
                <span class="leading-none tracking-normal">Filter</span>
                @if($statusFilter !== 'all')
                    <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                @endif
            </button>

            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute right-0 top-[calc(100%+6px)] bg-white border rounded-xl shadow-lg min-w-[160px] z-50 overflow-hidden"
                style="border-color:var(--c-border); display:none;">
                <div class="p-1.5">
                    @php 
                        $statuses = [
                            'all'     => 'Semua Status',
                            'aktif'   => 'Aktif',
                            'draft'   => 'Draft',
                            'selesai' => 'Selesai',
                        ]; 
                    @endphp
                    @foreach($statuses as $val => $label)
                    <a href="{{ route('banksoal.periode.setup', array_merge(request()->except(['status','page']), ['status' => $val])) }}"
                    class="block px-2.5 py-1.5 rounded-lg text-[11px] transition-colors"
                    style="font-weight:{{ $statusFilter === $val ? '700' : '500' }}; color:{{ $statusFilter === $val ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; text-decoration:none; background:{{ $statusFilter === $val ? 'rgba(94,83,244,0.06)' : 'transparent' }};"
                    onmouseover="if('{{ $statusFilter }}' !== '{{ $val }}') this.style.background='var(--c-bg)'" onmouseout="if('{{ $statusFilter }}' !== '{{ $val }}') this.style.background='transparent'">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>
