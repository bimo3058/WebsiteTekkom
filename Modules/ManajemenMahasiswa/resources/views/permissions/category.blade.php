<x-manajemenmahasiswa::layouts.admin>
    @push('styles')
        @include('manajemenmahasiswa::permissions._styles')
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
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0;">{{ $category }}</h1>
                        <p style="font-size:12px; color:var(--c-fg-muted); margin:3px 0 0;">
                            Total <span style="color:var(--c-primary); font-weight:600;">{{ number_format($users->total()) }}</span> pengguna dalam kategori ini
                        </p>
                    </div>

                    <a href="{{ route('manajemenmahasiswa.pengguna.index') }}" class="mk-btn mk-btn--secondary mk-btn--sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>
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

                        {{-- Per Page --}}
                        <div class="mp-field" style="width:120px;"
                             x-data="{ open: false, selected: '{{ $perPage }}', options: ['10','25','50','100'] }">
                            <label class="mp-label">Limit</label>
                            <input type="hidden" name="per_page" :value="selected">
                            <button type="button" class="mp-select-btn" :class="open ? 'open' : ''"
                                    @click="open = !open" @click.outside="open = false">
                                <span x-text="selected + ' baris'"></span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.150ms class="mp-select-menu" style="display:none;">
                                <template x-for="opt in options" :key="opt">
                                    <button type="button" class="mp-select-option"
                                            :class="selected === opt ? 'selected' : ''"
                                            @click="selected = opt; open = false">
                                        <span x-text="opt + ' baris'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="mp-field">
                            <button type="submit" class="mp-btn-primary mk-btn mk-btn--primary mk-btn--sm" style="height:32px;padding:0 16px;">Filter</button>
                        </div>
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

                {{-- ── Pagination ───────────────────────────── --}}
                @if($users->hasPages())
                    <div class="d-flex flex-column align-items-center gap-2 mt-3">
                        <div class="d-flex align-items-center gap-1">

                            {{-- Prev --}}
                            @if($users->onFirstPage())
                                <span class="page-btn page-btn-nav disabled">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                </span>
                            @else
                                <a href="{{ $users->withQueryString()->previousPageUrl() }}" class="page-btn page-btn-nav">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach($users->withQueryString()->links()->offsetGet('elements') as $element)
                                @if(is_string($element))
                                    <span class="page-btn page-btn-dots">…</span>
                                @endif
                                @if(is_array($element))
                                    @foreach($element as $page => $url)
                                        @if($page == $users->currentPage())
                                            <span class="page-btn page-btn-active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if($users->hasMorePages())
                                <a href="{{ $users->withQueryString()->nextPageUrl() }}" class="page-btn page-btn-nav">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            @else
                                <span class="page-btn page-btn-nav disabled">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </span>
                            @endif

                        </div>
                        <div class="mp-page-info">
                            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                        </div>
                    </div>
                @endif

            </div> <!-- end user-box-body -->
        </div> <!-- end user-box -->
    </div> <!-- end user-wrap -->

    @include('manajemenmahasiswa::permissions._scripts')
</x-manajemenmahasiswa::layouts.admin>
