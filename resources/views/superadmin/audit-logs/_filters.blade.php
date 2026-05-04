{{-- resources/views/superadmin/audit-logs/_filters.blade.php --}}
<div class="bg-white border rounded-[14px] p-5 mb-5 shadow-sm" style="border-color:#E4E7EC;">
    <form method="GET" action="{{ route('superadmin.audit-logs') }}" id="auditFilterForm">

        {{-- Row 1: dropdowns + dates --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-3">

            {{-- Module --}}
            <div x-data="{
                open: false,
                selected: '{{ request('module', '') }}',
                opts: { '': 'Semua Modul', @foreach($modules as $mod)'{{ $mod }}': '{{ strtoupper(str_replace('_', ' ', $mod)) }}',@endforeach }
            }">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Modul</label>
                <div class="relative">
                    <input type="hidden" name="module" :value="selected">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                            style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit; cursor:pointer;"
                            :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                        <span class="truncate" x-text="opts[selected]"></span>
                        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:var(--c-fg-placeholder);">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1 w-full z-50 rounded-lg overflow-hidden py-1"
                         style="display:none; background:#fff; border:1px solid var(--c-border); box-shadow:0 8px 20px rgba(0,0,0,.08);">
                        <template x-for="(label, value) in opts" :key="value">
                            <button type="button"
                                    class="w-full text-left px-3 py-2 text-xs transition-colors hover:bg-[var(--c-bg)]"
                                    :class="selected === value ? 'font-semibold' : 'font-normal'"
                                    :style="selected === value ? 'color:var(--c-primary); background:rgba(11,38,110,0.04)' : 'color:var(--c-fg-sec)'"
                                    @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())"
                                    style="font-family:inherit; border:none; cursor:pointer; background:none;">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Tipe Aksi --}}
            <div x-data="{
                open: false,
                selected: '{{ request('action', '') }}',
                opts: { '': 'Semua Aksi', @foreach($actions as $act)'{{ $act }}': '{{ strtoupper($act) }}',@endforeach }
            }">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Tipe Aksi</label>
                <div class="relative">
                    <input type="hidden" name="action" :value="selected">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                            style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit; cursor:pointer;"
                            :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                        <span class="truncate" x-text="opts[selected]"></span>
                        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:var(--c-fg-placeholder);">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1 w-full z-50 rounded-lg overflow-hidden py-1"
                         style="display:none; background:#fff; border:1px solid var(--c-border); box-shadow:0 8px 20px rgba(0,0,0,.08);">
                        <template x-for="(label, value) in opts" :key="value">
                            <button type="button"
                                    class="w-full text-left px-3 py-2 text-xs transition-colors hover:bg-[var(--c-bg)]"
                                    :class="selected === value ? 'font-semibold' : 'font-normal'"
                                    :style="selected === value ? 'color:var(--c-primary); background:rgba(11,38,110,0.04)' : 'color:var(--c-fg-sec)'"
                                    @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())"
                                    style="font-family:inherit; border:none; cursor:pointer; background:none;">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Pelaku (User) --}}
            <div x-data="{
                open: false,
                selected: '{{ request('user_id', '') }}',
                opts: { '': 'Semua User', @foreach($users as $u)'{{ $u->id }}': '{{ addslashes($u->name) }}',@endforeach }
            }">
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Pelaku</label>
                <div class="relative">
                    <input type="hidden" name="user_id" :value="selected">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                            style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit; cursor:pointer;"
                            :style="open ? 'border-color:var(--c-primary); box-shadow:0 0 0 3px var(--c-primary-subtle)' : ''">
                        <span class="truncate" x-text="opts[selected]"></span>
                        <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                             width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color:var(--c-fg-placeholder);">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1 w-full z-50 rounded-lg py-1 overflow-y-auto"
                         style="display:none; max-height:200px; min-width:100%; width:max-content; max-width:260px; background:#fff; border:1px solid var(--c-border); box-shadow:0 8px 20px rgba(0,0,0,.08);">
                        <template x-for="(label, value) in opts" :key="value">
                            <button type="button"
                                    class="w-full text-left px-3 py-2 text-xs transition-colors hover:bg-[var(--c-bg)]"
                                    :class="selected === value ? 'font-semibold' : 'font-normal'"
                                    :style="selected === value ? 'color:var(--c-primary); background:rgba(11,38,110,0.04)' : 'color:var(--c-fg-sec)'"
                                    @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())"
                                    style="font-family:inherit; border:none; cursor:pointer; background:none; white-space:nowrap;">
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Dari Tanggal --}}
            <div>
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 rounded-lg text-xs outline-none transition-all"
                       style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>

            {{-- Sampai Tanggal --}}
            <div>
                <label class="block mb-1.5" style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted);">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 rounded-lg text-xs outline-none transition-all"
                       style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>

        </div>

        {{-- Row 2: search + buttons --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                     class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                     style="color:var(--c-fg-placeholder);">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kata kunci aktivitas..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg text-xs outline-none transition-all"
                       style="background:#fff; border:1px solid #D0D5DD; color:var(--c-fg); font-family:inherit;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>

            <button type="submit"
                    class="px-4 py-2 rounded-lg text-xs font-bold tracking-wider text-white transition-colors whitespace-nowrap"
                    style="background:var(--c-primary); border:none; font-family:inherit; cursor:pointer; letter-spacing:.06em;"
                    onmouseover="this.style.background='var(--c-primary-hover)'"
                    onmouseout="this.style.background='var(--c-primary)'">
                Filter
            </button>

            @if(request()->hasAny(['module','action','user_id','date_from','date_to','search']))
            <a href="{{ route('superadmin.audit-logs') }}"
               class="px-4 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-colors"
               style="background:#fff; border:1px solid var(--c-border); color:var(--c-fg-muted); text-decoration:none;"
               onmouseover="this.style.background='var(--c-bg)'"
               onmouseout="this.style.background='#fff'">
                Reset
            </a>
            @endif
        </div>

    </form>
</div>