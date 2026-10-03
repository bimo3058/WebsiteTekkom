<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
        @include('manajemenmahasiswa::permissions._styles')
        {{-- Gaya tombol + panel "Filter" (dipakai bersama Direktori, Kegiatan, Pengaduan) --}}
        @include('manajemenmahasiswa::partials.filter-popover')
    @endpush

    @php
        $roleOptions = [
            ['name' => 'all',            'label' => 'Semua Role'],
            ['name' => 'ketua_himpunan', 'label' => 'Ketua Himpunan'],
            ['name' => 'ketua_bidang',   'label' => 'Ketua Bidang'],
            ['name' => 'ketua_unit',     'label' => 'Ketua Unit'],
            ['name' => 'staff_himpunan', 'label' => 'Staff Himpunan'],
        ];
    @endphp

    <div class="user-wrap">
        <div class="user-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="user-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="{{ $category }}">
                    Total <span style="color:var(--c-primary); font-weight:600;">{{ number_format($users->total()) }}</span> pengguna dalam kategori ini
                    <x-slot:actions>
                        <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </x-slot:actions>
                </x-manajemenmahasiswa::ui.page-header>
            </div>

            <div class="user-box-body">

                {{-- Flash --}}
                @if(session('success'))
                    <div class="mp-flash success">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mp-flash error">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- ── Filter Bar ───────────────────────────── --}}
                <form action="{{ url()->current() }}" method="GET" class="mp-filter-bar">
                    <div class="mp-filter-row">

                        {{-- Search --}}
                        <div class="mp-field mp-field-grow">
                            <label class="mp-label">Cari</label>
                            <div style="position:relative;">
                                <svg class="mp-input-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" name="search" value="{{ $search }}" class="mp-input" placeholder="Nama, NIM, atau email...">
                            </div>
                        </div>

                        {{-- Role Filter — hanya Pengurus Himpunan --}}
                        @if($category === 'Pengurus Himpunan')
                            <div class="mp-field" style="width:170px;"
                                 x-data="{
                                    open: false,
                                    selected: '{{ $roleFilter }}',
                                    roles: @js($roleOptions),
                                    get currentLabel() { return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role'; }
                                 }">
                                <label class="mp-label">Role</label>
                                <input type="hidden" name="role" :value="selected">
                                <button type="button" class="mp-select-btn" :class="open ? 'open' : ''"
                                        @click="open = !open" @click.outside="open = false">
                                    <span x-text="currentLabel" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"></span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div x-show="open" x-transition.opacity.duration.150ms class="mp-select-menu" style="display:none;">
                                    <template x-for="r in roles" :key="r.name">
                                        <button type="button" class="mp-select-option"
                                                :class="selected === r.name ? 'selected' : ''"
                                                @click="selected = r.name; open = false">
                                            <span x-text="r.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        @endif

                        {{-- Pemilih jumlah baris ada di footer tabel ("Per page"),
                             mengikuti pola tabel global SITKOM. --}}

                        {{-- Tombol Filter membuka panel angkatan; pola & gaya sama dengan
                             halaman utama Permission (partials/filter-popover). --}}
                        <div class="mp-field">
                            <div class="filter-pop" x-data="{ filterOpen: false }"
                                 @keydown.escape.window="filterOpen = false">
                                <button type="button" class="filter-pop-btn"
                                        @click="filterOpen = !filterOpen"
                                        :class="{ 'is-open': filterOpen }">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;">
                                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                                    </svg>
                                    <span style="line-height:1;">Filter</span>
                                    @if($angkatan)
                                        <span class="filter-pop-dot"></span>
                                    @endif
                                </button>

                                <div class="filter-pop-backdrop" x-show="filterOpen" x-cloak style="display:none;"
                                     @click="filterOpen = false"></div>

                                <div class="filter-pop-panel" x-show="filterOpen" x-cloak style="display:none;"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95">

                                    <p class="filter-pop-title">Advanced Filters</p>

                                    <div class="filter-pop-fields">
                                        <div>
                                            <label class="filter-pop-label" for="filterAngkatan">Angkatan</label>
                                            <x-manajemenmahasiswa::ui.select name="angkatan" id="filterAngkatan">
                                                <option value="">Semua Angkatan</option>
                                                @foreach($angkatanList as $ank)
                                                    <option value="{{ $ank }}" @selected((string) $angkatan === (string) $ank)>
                                                        Angkatan {{ $ank }}
                                                    </option>
                                                @endforeach
                                            </x-manajemenmahasiswa::ui.select>
                                        </div>

                                        <div class="filter-pop-actions">
                                            <button type="submit" class="filter-pop-submit">Terapkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isFiltered)
                            <div class="mp-field">
                                <a href="{{ url()->current() }}" class="mk-btn mk-btn--secondary mk-btn--sm" style="height:32px;padding:0 14px;">Reset</a>
                            </div>
                        @endif
                    </div>
                </form>

                {{-- ── Tabel Pengguna ───────────────────────── --}}
                <div class="mp-table-card">
                    @include('manajemenmahasiswa::permissions._table', [
                        'users'           => $users->values(),
                        'assignableRoles' => $assignableRoles,
                        'startIndex'      => ($users->currentPage() - 1) * $users->perPage(),
                        'emptyText'       => 'Tidak ada pengguna dalam kategori ini',
                    ])
                </div>
                {{-- Footer bersama: Per page + Showing X to Y of Z results + nomor halaman --}}
                @include('manajemenmahasiswa::partials.table-footer', [
                    'paginator'  => $users,
                    'standalone' => true,
                ])

            </div> <!-- end user-box-body -->
        </div> <!-- end user-box -->
    </div> <!-- end user-wrap -->

    @include('manajemenmahasiswa::permissions._scripts')
</x-manajemenmahasiswa::layouts.admin>
