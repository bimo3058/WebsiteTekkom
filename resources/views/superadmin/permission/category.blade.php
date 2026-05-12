{{-- resources/views/superadmin/permission/category.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
        <div style="max-width:100%; padding:24px 24px 56px;">

            {{-- ── Breadcrumb ───────────────────────────────────────────── --}}
            <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
                <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                <a href="{{ route('superadmin.permissions') }}" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Access Control</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                <span style="color:var(--c-fg); font-weight:500;">{{ $category }}</span>
            </nav>

            {{-- ── Header + Filter row ─────────────────────────────────── --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">
                        {{ $category }}
                        <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); margin-left:6px;">
                            ({{ $users->total() }} users)
                        </span>
                    </h1>
                    <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">Menampilkan semua pengguna dalam grup ini</p>
                </div>

                <form action="{{ url()->current() }}" method="GET" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

                    {{-- Per page --}}
                    <div x-data="{
                            open: false,
                            selected: '{{ request('per_page') ?: (session('cat_per_page') ?? '10') }}',
                            options: ['10','25','50','100'],
                        }"
                         style="position:relative; width:110px;">
                        <input type="hidden" name="per_page" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                                onmouseover="this.style.borderColor='var(--c-border-strong)'" onmouseout="this.style.borderColor='var(--c-border)'">
                            <span x-text="selected + ' baris'" style="font-weight:500;"></span>
                            <svg :style="open ? 'transform:rotate(180deg)' : ''" width="12" height="12" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; flex-shrink:0;"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms
                             style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.08); z-index:50; overflow:hidden; padding:4px 0;">
                            <template x-for="opt in options" :key="opt">
                                <button type="button" @click="selected = opt; open = false"
                                        :style="selected == opt ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600;' : 'color:var(--c-fg-sec);'"
                                        style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; background:none; border:none; cursor:pointer; transition:background .12s;"
                                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="if(this.getAttribute('data-active')!='1') this.style.background='none'">
                                    <span x-text="opt + ' baris'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Role filter (Admins only) --}}
                    @if($category === 'Admins')
                    <div x-data="{
                            open: false,
                            selected: '{{ request('role', 'all') }}',
                            roles: [
                                { name: 'all', label: 'Semua Role' },
                                { name: 'superadmin', label: 'Superadmin' },
                                { name: 'admin_banksoal', label: 'Admin Bank Soal' },
                                { name: 'admin_capstone', label: 'Admin Capstone' },
                                { name: 'admin_eoffice', label: 'Admin E-Office' },
                                { name: 'admin_kemahasiswaan', label: 'Admin Kemahasiswaan' },
                            ],
                            get currentLabel() { return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role'; }
                        }"
                         style="position:relative; width:160px;">
                        <input type="hidden" name="role" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                                onmouseover="this.style.borderColor='var(--c-border-strong)'" onmouseout="this.style.borderColor='var(--c-border)'">
                            <span x-text="currentLabel" style="font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                            <svg :style="open ? 'transform:rotate(180deg)' : ''" width="12" height="12" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; flex-shrink:0; margin-left:4px;"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms
                             style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.08); z-index:50; padding:4px 0;">
                            <template x-for="r in roles" :key="r.name">
                                <button type="button" @click="selected = r.name; open = false"
                                        :style="selected === r.name ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600;' : 'color:var(--c-fg-sec);'"
                                        style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; background:none; border:none; cursor:pointer; transition:background .12s;"
                                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='none'">
                                    <span x-text="r.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @endif

                    {{-- Search --}}
                    <div style="position:relative;">
                        <svg width="14" height="14" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                               style="padding:7px 12px 7px 32px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; width:220px; transition:border-color .15s, box-shadow .15s;"
                               onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                               onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- Filter submit --}}
                    <button type="submit"
                            style="padding:7px 14px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s;"
                            onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                        Filter
                    </button>

                    {{-- Back --}}
                    <a href="{{ route('superadmin.permissions') }}"
                       style="display:inline-flex; align-items:center; gap:5px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                        Kembali
                    </a>
                </form>
            </div>

            {{-- ── User cards ───────────────────────────────────────────── --}}
            <div style="display:flex; flex-direction:column; gap:6px;">
                @forelse($users as $user)
                    @include('superadmin.permission._user_card', ['user' => $user, 'categoryKey' => strtolower(str_replace(' ', '_', $category))])
                @empty
                    <div style="background:#fff; border:1px dashed var(--c-border); border-radius:12px; padding:48px; text-align:center;">
                        <p style="font-size:12px; color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:0.08em; font-weight:500;">
                            User tidak ditemukan dalam kategori ini
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- ── Pagination ───────────────────────────────────────────── --}}
            @if($users->hasPages())
            <div style="margin-top:24px;">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif

        </div>
    </div>

    @include('superadmin.permission._scripts')

</x-sidebar>
</x-app-layout>