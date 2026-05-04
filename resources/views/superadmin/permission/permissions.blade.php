{{-- resources/views/superadmin/permission/permissions.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
        <div style="max-width:100%; padding:2px 24px 56px;">

            {{-- ── Header ──────────────────────────────────────────────── --}}
            <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
                <a href="{{ route('superadmin.dashboard') }}" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                <span style="color:var(--c-fg); font-weight:500;">Permission</span>
            </nav>

            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:28px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">Permission</h1>
                    <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">Manage roles and module permissions for each user</p>
                </div>

                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    {{-- Search --}}
                    <form action="{{ url()->current() }}" method="GET" style="display:flex; align-items:center; gap:8px;">
                        <div style="position:relative;">
                            <svg width="14" height="14" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user..."
                                   style="padding:7px 12px 7px 32px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; width:220px; transition:border-color .15s;"
                                   onmouseover="this.style.borderColor='var(--c-border-strong)'"
                                   onmouseout="this.style.borderColor='var(--c-border)'"
                                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                                   onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                        </div>
                        <button type="submit"
                                style="padding:7px 14px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s;"
                                onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                            Cari
                        </button>
                    </form>

                    <div style="width:1px; height:20px; background:var(--c-border);"></div>

                    {{-- Back --}}
                    <a href="{{ route('superadmin.users.index') }}"
                       style="display:inline-flex; align-items:center; gap:6px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                        Back
                    </a>
                </div>
            </div>

            {{-- ── Category sections ───────────────────────────────────── --}}
            @php
                $categories = [
                    'Admins'              => ['superadmin', 'admin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                    'Unassigned'          => [],
                    'Dosen'               => ['dosen'],
                    'GPM'                 => ['gpm'],
                    'Mahasiswa'           => ['mahasiswa'],
                    'Pengurus Himpunan'   => ['pengurus_himpunan'],
                    'Alumni'              => ['alumni'],
                ];

                // Category accent colors (subtle left indicator on section title)
                $categoryAccent = [
                    'Admins'              => 'var(--c-primary)',
                    'Unassigned'          => 'var(--c-error)',
                    'Dosen'               => '#287F6E',
                    'GPM'                 => '#0C4D6E',
                    'Mahasiswa'           => '#956321',
                    'Pengurus Himpunan'   => '#7B3FA0',
                    'Alumni'              => '#5C6B73',
                ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:40px;">
                @foreach($categories as $title => $slugs)
                    @php
                        $filteredUsers = $slugs === []
                            ? $users->filter(fn($u) => $u->roles->isEmpty())
                            : $users->filter(fn($u) => $u->roles->pluck('name')->intersect($slugs)->isNotEmpty());
                        $sortedUsers = $filteredUsers->sortByDesc(fn($u) => $u->roles->pluck('name')->contains('superadmin'))->take(5);
                        $accent = $categoryAccent[$title] ?? 'var(--c-border)';
                        $catKey = strtolower(str_replace(' ', '_', $title));
                    @endphp

                    @if($title === 'Unassigned' && $filteredUsers->isEmpty())
                        @continue
                    @endif

                    <section>
                        {{-- Section heading --}}
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
                            <div style="width:3px; height:14px; border-radius:2px; background:{{ $accent }}; flex-shrink:0;"></div>
                            <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--c-fg); white-space:nowrap;">
                                {{ $title }}
                            </span>
                            <span style="font-size:11px; color:var(--c-fg-placeholder);">({{ $filteredUsers->count() }})</span>
                            <div style="flex:1; height:1px; background:var(--c-border);"></div>
                            <a href="{{ route('superadmin.permissions.category', $title) }}"
                               style="font-size:11px; font-weight:600; color:{{ $accent }}; text-decoration:none; display:flex; align-items:center; gap:3px; white-space:nowrap; transition:opacity .15s;"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                View All
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
                            </a>
                        </div>

                        {{-- Cards --}}
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            @forelse($sortedUsers as $user)
                                @include('superadmin.permission._user_card', ['user' => $user, 'categoryKey' => $catKey])
                            @empty
                                <div style="background:#fff; border:1px dashed var(--c-border); border-radius:12px; padding:32px; text-align:center;">
                                    <p style="font-size:12px; color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:0.08em; font-weight:500;">No users found</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>

        </div>
    </div>

    @include('superadmin.permission._modal_confirm')
    @include('superadmin.permission._scripts')

</x-sidebar>
</x-app-layout>