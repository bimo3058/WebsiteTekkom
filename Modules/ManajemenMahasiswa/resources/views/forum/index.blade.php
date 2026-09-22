<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            /* ── Main Wrapper Override (match Pengumuman) ─────────────────── */
            /* Halaman ini menggambar kotak kontennya sendiri (.dash-wrap/.dash-box),
               jadi kotak bawaan .main-wrapper dari layout dimatikan. */
            .main-wrapper {
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid #DFE1E7; flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: #C1C7CF; border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; padding: 0; }
                .dash-box { flex: none !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Cards ───────────────────────────────────────────────────────── */
            .dashboard-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #DFE1E7;
                padding: 22px 26px;
                margin-bottom: 16px;
                box-shadow: 0 1px 3px rgba(22, 22, 43, 0.06), 0 1px 2px rgba(22, 22, 43, 0.04);
            }

            .forum-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #DFE1E7;
                padding: 14px 16px;
                margin-bottom: 10px;
                transition: border-color 0.15s, box-shadow 0.15s;
                box-shadow: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .forum-card:hover {
                border-color: #5C78B8;
                box-shadow: 0 4px 14px rgba(11, 38, 110, 0.07);
            }

            .avatar-placeholder {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(11, 38, 110, 0.08);
                color: #0B266E;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 12px;
                flex-shrink: 0;
            }

            .btn-join {
                background-color: #0B266E;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 4px 16px;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn-join:hover {
                background-color: #091958;
                transform: translateY(-1px);
            }

            .btn-post {
                background-color: #0B266E;
                color: white;
                border: 1px solid #0B266E;
                border-radius: 8px;
                padding: 7px 14px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                white-space: nowrap;
                transition: all 0.15s;
                text-decoration: none;
                box-shadow: 0 2px 6px rgba(11, 38, 110, 0.3);
                flex-shrink: 0;
            }

            .btn-post:hover {
                background-color: #091958;
                border-color: #091958;
                color: white;
                box-shadow: 0 4px 12px rgba(11, 38, 110, 0.4);
            }

            .post-actions .vote-pill {
                background: #f1f5f9;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                padding: 1px;
                margin-right: 8px;
                border: 1px solid #e2e8f0;
            }

            .post-actions .vote-pill button {
                background: transparent;
                border: none;
                padding: 5px 10px;
                border-radius: 20px;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .post-actions .vote-pill button:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            .post-actions .vote-pill button.vote-active-up {
                color: #ff4500;
            }

            .post-actions .vote-pill button.vote-active-up:hover {
                background: rgba(255, 69, 0, 0.1);
            }

            .post-actions .vote-pill button.vote-active-down {
                color: #7193ff;
            }

            .post-actions .vote-pill button.vote-active-down:hover {
                background: rgba(113, 147, 255, 0.1);
            }

            .post-actions .vote-pill span {
                font-weight: 700;
                font-size: 13px;
                padding: 0 4px;
                text-align: center;
                color: #1e293b;
            }

            .post-actions .vote-pill .v-separator {
                width: 1px;
                height: 16px;
                background-color: #cbd5e1;
                margin: 0 2px;
            }

            .post-actions .action-btn {
                background: #f1f5f9;
                border: none;
                padding: 6px 14px;
                border-radius: 20px;
                color: #4b5563;
                font-size: 13px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-right: 8px;
                transition: background 0.15s;
            }

            .post-actions .action-btn:hover {
                background: #e2e8f0;
            }

            .search-input {
                border: 1px solid #DFE1E7;
                border-radius: 8px;
                padding: 8px 14px 8px 36px;
                font-size: 12px;
                font-weight: 500;
                color: #353849;
                outline: none;
                background: #fff;
                transition: border-color 0.15s, box-shadow 0.15s;
                width: 100%;
            }

            .search-input::placeholder { color: #808897; }

            .search-input:focus {
                border-color: #0B266E;
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }

            .search-wrapper {
                position: relative;
                flex-grow: 1;
            }

            .search-icon {
                position: absolute;
                left: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
                width: 18px;
                height: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .search-icon svg {
                width: 18px;
                height: 18px;
            }

            /* Force SVGs inside btn-post to inherit size */
            .btn-post svg {
                width: 16px;
                height: 16px;
                flex-shrink: 0;
            }

            /* ── Sort Tabs ── */
            .sort-tab {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 5px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                border: 1px solid #DFE1E7;
                background: #fff;
                color: #666D80;
                cursor: pointer;
                transition: all 0.15s;
            }

            .sort-tab:hover {
                border-color: #0B266E;
                color: #0B266E;
                background: rgba(11,38,110,0.04);
            }

            .sort-tab.active {
                background: #0B266E;
                color: #fff;
                border-color: #0B266E;
            }

            .sort-tab svg { width: 13px; height: 13px; }

            .tag-label {
                font-size: 11px;
                font-weight: 700;
                padding: 4px 9px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                letter-spacing: 0.02em;
            }

            .tag-green  { background: #DDF2EE; color: #287F6E; }
            .tag-red    { background: #FADAE1; color: #DF1C41; }
            .tag-gray   { background: #F6F8FA; color: #666D80; }
            .tag-blue   { background: #D1F0F9; color: #0C4D6E; }
            .tag-purple { background: rgba(11,38,110,0.08); color: #0B266E; }

            .pinned-badge {
                background: #F9ECCB;
                color: #956321;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                letter-spacing: 0.02em;
            }

            .empty-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 64px 20px;
                text-align: center;
            }

            .empty-state .empty-icon {
                width: 64px;
                height: 64px;
                border-radius: 50%;
                background: rgba(11, 38, 110, 0.08);
                color: #0B266E;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 14px;
            }

            .empty-state h5 {
                font-size: 14px;
                font-weight: 700;
                color: #0D0D12;
                margin-bottom: 4px;
            }

            .empty-state p {
                font-size: 12px;
                color: #666D80;
                margin: 0 0 16px;
            }

            .edited-badge {
                font-size: 11px;
                color: #808897;
                font-style: italic;
            }

            .personal-pin-badge {
                background: rgba(11,38,110,0.08);
                color: #0B266E;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                letter-spacing: 0.02em;
            }

            /* Pagination Custom Layout */
            .pagination-container nav>.d-sm-flex {
                flex-direction: column-reverse;
                align-items: center !important;
                gap: 0.75rem;
            }

            .pagination-container nav>.d-sm-flex>div:last-child {
                margin-bottom: 0.25rem;
            }

            .pagination-container .pagination {
                margin-bottom: 0;
            }

        </style>
    @endpush

    <div class="dash-wrap">
    <div class="dash-box">
    <div class="dash-box-header">
        <x-manajemenmahasiswa::ui.page-header
            title="Forum Diskusi"
            subtitle="Wadah komunikasi mahasiswa & alumni" />
    </div>
    <div class="dash-box-body">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- User Stats Banner --}}
    @php
        $progressPct = $userStats['xp_needed'] > 0
            ? min(100, round(($userStats['xp_current'] / $userStats['xp_needed']) * 100))
            : 100;
    @endphp
    <div style="background: linear-gradient(135deg, #0B266E 0%, #091958 55%, #1a3a8a 100%); border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 0; flex-wrap: wrap; position: relative; overflow: hidden; box-shadow: 0px 2px 8px rgba(11,38,110,0.18);">

        {{-- Decorative circles --}}
        <div style="position:absolute; top:-40px; right:-20px; width:120px; height:120px; background:rgba(255,255,255,0.05); border-radius:50%; pointer-events:none;"></div>
        <div style="position:absolute; bottom:-50px; right:80px; width:90px; height:90px; background:rgba(255,255,255,0.04); border-radius:50%; pointer-events:none;"></div>

        {{-- Streak --}}
        <div style="display:flex; align-items:center; gap:10px; padding-right:20px; border-right:1px solid rgba(255,255,255,0.15); flex-shrink:0;">
            <div style="background:linear-gradient(135deg,#f97316,#dc2626); border-radius:10px; width:38px; height:38px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 8px rgba(249,115,22,0.4);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:9px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:0.06em; line-height:1;">Streak</div>
                <div style="font-size:20px; font-weight:900; color:#fff; line-height:1.15; letter-spacing:-0.02em; margin-top:1px;">
                    {{ $userStats['current_streak'] }}<span style="font-size:11px; font-weight:600; opacity:0.7; margin-left:2px;">hr</span>
                </div>
            </div>
        </div>

        {{-- Rank --}}
        <div style="padding:0 20px; border-right:1px solid rgba(255,255,255,0.15); text-align:center; flex-shrink:0;">
            <div style="font-size:9px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1px;">Rank</div>
            <div style="font-size:22px; font-weight:900; color:#fbbf24; line-height:1.1; letter-spacing:-0.03em;">#{{ $userStats['rank'] }}</div>
        </div>

        {{-- Level --}}
        <div style="padding:0 20px; border-right:1px solid rgba(255,255,255,0.15); text-align:center; flex-shrink:0;">
            <div style="font-size:9px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1px;">Level</div>
            <div style="font-size:18px; font-weight:800; color:#fff; line-height:1.1;">{!! $userStats['tier_icon'] !!} {{ $userStats['level'] }}</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.55); font-weight:500; margin-top:1px;">{{ $userStats['tier_name'] }}</div>
        </div>

        {{-- XP Progress --}}
        <div style="flex:1; min-width:160px; padding:0 20px; {{ $userStats['badges']->isNotEmpty() ? 'border-right:1px solid rgba(255,255,255,0.15);' : '' }}">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                <span style="font-size:9px; font-weight:700; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.06em;">XP</span>
                <span style="font-size:11px; font-weight:700; color:#fbbf24;">{{ number_format($userStats['total_xp']) }} / {{ number_format($userStats['xp_for_next']) }}</span>
            </div>
            <div style="height:5px; background:rgba(255,255,255,0.15); border-radius:99px; overflow:hidden;">
                <div style="height:100%; width:{{ $progressPct }}%; background:linear-gradient(90deg,#fbbf24,#f97316); border-radius:99px; transition:width .5s ease;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:4px;">
                <span style="font-size:9px; color:rgba(255,255,255,0.4);">Lv.{{ $userStats['level'] }}</span>
                <span style="font-size:9px; color:rgba(255,255,255,0.4);">Lv.{{ $userStats['level'] + 1 }}</span>
            </div>
        </div>

        {{-- Badges --}}
        @if($userStats['badges']->isNotEmpty())
            <div style="padding:0 0 0 20px; flex-shrink:0;">
                <div style="font-size:9px; color:rgba(255,255,255,0.6); font-weight:600; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:5px;">Badges</div>
                <div style="display:flex; gap:4px; flex-wrap:wrap; align-items:center;">
                    @foreach($userStats['badges']->take(4) as $badge)
                        @if($badge->image)
                            <img src="{{ asset($badge->image) }}" title="{{ $badge->name }}"
                                style="width:22px; height:22px; object-fit:contain;"
                                onerror="this.style.display='none';">
                        @else
                            <span title="{{ $badge->name }}" style="font-size:18px; line-height:1;">{{ $badge->icon }}</span>
                        @endif
                    @endforeach
                    @if($userStats['badges']->count() > 4)
                        <span style="font-size:10px; color:rgba(255,255,255,0.5); font-weight:600;">+{{ $userStats['badges']->count() - 4 }}</span>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- Search & Filter Area -->
    <form method="GET" action="{{ route('manajemenmahasiswa.forum.index') }}" id="forumFilterForm">
        <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'terbaru') }}">
        @php $currentSort = request('sort', 'terbaru'); @endphp

        {{-- Row 1: Search + Buat Post --}}
        <div class="d-flex gap-2 mb-2 flex-wrap align-items-center">
            <div class="search-wrapper" style="flex: 1; min-width: 180px;">
                <span class="search-icon">
                    <x-manajemenmahasiswa::ui.icon name="search-01" size="16" />
                </span>
                <input type="text" name="search" class="search-input" placeholder="Cari thread..."
                    value="{{ request('search') }}">
            </div>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="mk-btn mk-btn--primary flex-shrink-0">
                <x-manajemenmahasiswa::ui.icon name="plus" size="16" />
                Buat Post
            </a>
        </div>

        {{-- Row 2: Category + Sort Tabs --}}
        <div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
            <x-manajemenmahasiswa::ui.select name="kategori" size="md" :block="false" min-width="148"
                class="flex-shrink-0"
                onchange="document.getElementById('forumFilterForm').submit()">
                <option value="semua" {{ request('kategori') == 'semua' || !request('kategori') ? 'selected' : '' }}>
                    Semua Kategori</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </x-manajemenmahasiswa::ui.select>
            <button type="button" class="sort-tab {{ $currentSort === 'terbaru' ? 'active' : '' }}"
                onclick="document.getElementById('sortInput').value='terbaru'; document.getElementById('forumFilterForm').submit();">
                <x-manajemenmahasiswa::ui.icon name="clock-02" size="13" /> Terbaru
            </button>
            <button type="button" class="sort-tab {{ $currentSort === 'hot' ? 'active' : '' }}"
                onclick="document.getElementById('sortInput').value='hot'; document.getElementById('forumFilterForm').submit();">
                <x-manajemenmahasiswa::ui.icon name="flash" size="13" /> Hot
            </button>
            <button type="button" class="sort-tab {{ $currentSort === 'top' ? 'active' : '' }}"
                onclick="document.getElementById('sortInput').value='top'; document.getElementById('forumFilterForm').submit();">
                <x-manajemenmahasiswa::ui.icon name="chevron-up" size="13" /> Top
            </button>
        </div>
    </form>

    <!-- Forum Posts -->
    <div class="forum-cards-container">
        @forelse($threads as $thread)
            <div class="forum-card" data-thread-id="{{ $thread->id }}" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span style="font-size:16px; font-weight:700; color:#0D0D12;">{{ $thread->author->name ?? 'Unknown' }}</span>
                                @include('manajemenmahasiswa::forum.partials.role-badge', ['roleUser' => $thread->author])
                                @if(isset($authorTiers[$thread->user_id]))
                                    <span style="background:rgba(11,38,110,0.08); color:#0B266E; font-size:10px; font-weight:700; padding:3px 8px; border-radius:8px; letter-spacing:0.02em;"
                                        title="{{ $authorTiers[$thread->user_id]['tier_name'] }}">
                                        {!! $authorTiers[$thread->user_id]['tier_icon'] !!}
                                        Lv.{{ $authorTiers[$thread->user_id]['level'] }}
                                    </span>
                                @endif
                                <span style="font-size:11px; color:#666D80; font-weight:500;">• {{ $thread->created_at->diffForHumans() }}</span>
                                @if($thread->isEdited())
                                    <span class="edited-badge">(diedit)</span>
                                @endif
                                @if($thread->is_pinned)
                                    <span class="pinned-badge">
                                        <x-manajemenmahasiswa::ui.icon name="bookmark" size="12" />
                                        Pinned
                                    </span>
                                @endif
                                <span class="personal-pin-badge" data-personal-pin="{{ $thread->id }}"
                                    style="display: {{ $thread->is_personal_pinned ? 'inline-flex' : 'none' }};">
                                    <x-manajemenmahasiswa::ui.icon name="bookmark-check" size="10" /> Pin Pribadi
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- Aksi thread: tombol "..." + panel .mk-menu milik modul, sama dengan
                         kolom Aksi Direktori & Pengumuman. Sebelumnya memakai dropdown
                         Bootstrap; diseragamkan supaya semua menu titik tiga sebentuk. --}}
                    <div style="position: relative;" x-data="{ open: false }">
                        <button type="button" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm"
                            @click="open = !open" @click.outside="open = false"
                            :aria-expanded="open" aria-haspopup="menu" title="Aksi lainnya">
                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                            </svg>
                        </button>

                        <div class="mk-menu" role="menu" x-show="open" x-cloak style="display: none;"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100">

                            {{-- Edit (owner only) --}}
                            @if($thread->user_id === $user->id)
                                <a href="{{ route('manajemenmahasiswa.forum.edit', $thread->id) }}"
                                    class="mk-menu-item" role="menuitem">
                                    <x-manajemenmahasiswa::ui.icon name="file-01" size="14" /> Edit
                                </a>
                            @endif

                            {{-- Lock / Unlock (admin only) --}}
                            @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.lock', $thread->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" role="menuitem"
                                        class="mk-menu-item {{ $thread->is_locked ? 'is-active' : '' }}">
                                        @if($thread->is_locked)
                                            <x-manajemenmahasiswa::ui.icon name="unlocked-01" size="14" /> Unlock Thread
                                        @else
                                            <x-manajemenmahasiswa::ui.icon name="locked-01" size="14" /> Kunci Thread
                                        @endif
                                    </button>
                                </form>
                            @endif

                            {{-- Pin Global (admin only) --}}
                            @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.pin', $thread->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" role="menuitem"
                                        class="mk-menu-item {{ $thread->is_pinned ? 'is-active' : '' }}">
                                        @if($thread->is_pinned)
                                            <x-manajemenmahasiswa::ui.icon name="bookmark" size="14" /> Unpin Global
                                        @else
                                            <x-manajemenmahasiswa::ui.icon name="bookmark" size="14" /> Pin Global
                                        @endif
                                    </button>
                                </form>
                            @endif

                            {{-- Pin Pribadi (semua role) --}}
                            <form method="POST"
                                action="{{ route('manajemenmahasiswa.forum.personal_pin', $thread->id) }}">
                                @csrf
                                <button type="submit" role="menuitem"
                                    class="mk-menu-item {{ $thread->is_personal_pinned ? 'is-active' : '' }}">
                                    <x-manajemenmahasiswa::ui.icon name="bookmark" size="14" />
                                    @if($thread->is_personal_pinned) Unpin Pribadi @else Pin Pribadi @endif
                                </button>
                            </form>

                            <div class="mk-menu-sep"></div>

                            {{-- Delete --}}
                            @if($thread->user_id === $user->id)
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                    onsubmit="return mkConfirmSubmit(this, 'Yakin ingin menghapus thread ini?', { title: 'Hapus Thread', confirmText: 'Ya, Hapus' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mk-menu-item" role="menuitem">
                                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" /> Hapus
                                    </button>
                                </form>
                            @elseif($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                    onsubmit="return mkConfirmSubmit(this, 'Yakin ingin menghapus thread ini (sebagai admin)?', { title: 'Hapus Thread (Admin)', confirmText: 'Ya, Hapus' })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="mk-menu-item" role="menuitem">
                                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" /> Hapus (Admin)
                                    </button>
                                </form>
                            @endif

                            @if($thread->user_id !== $user->id && !$user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                {{-- Modal laporan masih milik Bootstrap, jadi menunya ditutup manual. --}}
                                <button type="button" class="mk-menu-item" role="menuitem" @click="open = false"
                                    data-bs-toggle="modal" data-bs-target="#reportModal" data-thread-id="{{ $thread->id }}"
                                    data-thread-title="{{ $thread->judul }}">
                                    <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" /> Laporkan Thread
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 style="font-size:16px; font-weight:700; color:#0D0D12; margin-bottom:6px; line-height:1.3;">{{ $thread->judul }}</h6>
                    <p style="font-size:14px; color:#666D80; margin:0; line-height:1.6;">
                        {{ Str::limit($thread->getTextContent() ?: strip_tags($thread->konten), 200) }}
                    </p>
                </div>

                @if($thread->getFirstImageUrl())
                    <div class="mt-2 mb-3"
                        style="width: 100%; max-height: 300px; overflow: hidden; border-radius: 10px; border: 1px solid #DFE1E7; background: #f8fafc; display: flex; justify-content: center; align-items: center;">
                        <img src="{{ $thread->getFirstImageUrl() }}" alt="Thumbnail"
                            style="width: 100%; max-height: 300px; object-fit: contain;">
                    </div>
                @endif

                {{-- Poll preview --}}
                @if($thread->poll)
                    @php
                        $poll = $thread->poll;
                        $threadId = $thread->id;
                    @endphp
                    @include('manajemenmahasiswa::forum._poll', ['poll' => $poll, 'threadId' => $threadId, 'threadOwnerId' => $thread->user_id])
                @endif

                <!-- Labels -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @foreach($thread->getKategoriLabels() as $idx => $lbl)
                        @php $colorClass = $thread->getKategoriColors()[$idx] ?? 'tag-gray'; @endphp
                        <span class="tag-label {{ $colorClass }}">{{ $lbl }}</span>
                    @endforeach
                    @if($thread->is_locked)
                        <span class="tag-label tag-red"><x-manajemenmahasiswa::ui.icon name="locked-01" size="12" /> Dikunci</span>
                    @endif
                </div>

                <!-- Actions -->
                @php
                    $threadVoteKey = \Modules\ManajemenMahasiswa\Models\Thread::class . '_' . $thread->id;
                    $threadUserVote = $userVotes[$threadVoteKey] ?? null;
                @endphp
                <div class="post-actions d-flex align-items-center mt-2">
                    <div class="vote-pill shadow-sm">
                        <button
                            class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === 1 ? 'vote-active-up' : '' }}"
                            data-thread-id="{{ $thread->id }}" data-value="1">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                fill="{{ $threadUserVote && $threadUserVote->value === 1 ? 'currentColor' : 'none' }}"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="19" x2="12" y2="5"></line>
                                <polyline points="5 12 12 5 19 12"></polyline>
                            </svg>
                        </button>
                        <span class="thread-vote-count-{{ $thread->id }}">{{ $thread->vote_count }}</span>
                        <div class="v-separator"></div>
                        <button
                            class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === -1 ? 'vote-active-down' : '' }}"
                            data-thread-id="{{ $thread->id }}" data-value="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24"
                                fill="{{ $threadUserVote && $threadUserVote->value === -1 ? 'currentColor' : 'none' }}"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <polyline points="19 12 12 19 5 12"></polyline>
                            </svg>
                        </button>
                    </div>
                    <button class="action-btn ms-2"
                        onclick="window.location.href='{{ route('manajemenmahasiswa.forum.show', $thread->id) }}'">
                        <x-manajemenmahasiswa::ui.icon name="message-dots-circle" size="18" />
                        {{ $thread->comments_count ?? $thread->comment_count }}
                    </button>
                    <button class="action-btn share-btn ms-1"
                        data-url="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}">
                        <x-manajemenmahasiswa::ui.icon name="link-01" size="18" />
                        Bagikan
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <x-manajemenmahasiswa::ui.icon name="message-dots-circle" size="28" />
                </div>
                <h5>Belum ada diskusi</h5>
                <p>Jadilah yang pertama memulai diskusi!</p>
                <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="mk-btn mk-btn--primary">
                    <x-manajemenmahasiswa::ui.icon name="plus" size="14" /> Buat Post Pertama
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($threads->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-4 pagination-container">
            {{ $threads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    @if($errors->has('alasan'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert"
            style="border-radius: 10px; border: none; font-weight: 600;">
            {{ $errors->first('alasan') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <form id="reportForm" method="POST" action="">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="18" />
                            Laporkan Thread
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 14px;">Apakah thread <strong
                                id="reportThreadTitle"></strong> melanggar panduan komunitas?</p>

                        <div class="mb-3">
                            <label for="alasan" class="form-label fw-bold" style="font-size: 14px;">Alasan Pelaporan
                                <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alasan" id="alasan" rows="4"
                                placeholder="Tulis alasan spesifik (misal: SARA, Spam, Hoax)..." required
                                minlength="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="mk-btn mk-btn--secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="mk-btn mk-btn--primary">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rules Modal Overlay -->
    @if(isset($showRulesOverlay) && $showRulesOverlay)
    <div class="modal fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="rulesModalLabel">
                        <x-manajemenmahasiswa::ui.icon name="info-circle" size="20" />
                        Peraturan Forum Diskusi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="font-size: 14px; line-height: 1.6; color: #374151;">
                    <p>Selamat datang di Forum Diskusi! Untuk menjaga kenyamanan bersama, mohon patuhi peraturan berikut:</p>
                    <ol class="ps-3 mb-0">
                        <li class="mb-2"><strong>Gunakan bahasa yang sopan:</strong> Dilarang menggunakan kata-kata kasar, umpatan, atau konten SARA.</li>
                        <li class="mb-2"><strong>Dilarang Spam:</strong> Terdapat batasan dalam pembuatan thread dan komentar untuk mencegah spam. Promosi ilegal (seperti judi online) akan diblokir otomatis.</li>
                        <li class="mb-2"><strong>Hargai sesama pengguna:</strong> Jangan menyebarkan ujaran kebencian, ancaman, atau konten diskriminatif.</li>
                        <li class="mb-2"><strong>Sesuai Topik (On-Topic):</strong> Pilih kategori thread yang sesuai dengan isi pembahasan agar forum terorganisir.</li>
                        <li><strong>Laporkan pelanggaran:</strong> Gunakan fitur <i>"Laporkan Thread"</i> jika menemukan konten yang melanggar.</li>
                    </ol>
                </div>
                <div class="modal-footer border-0 pt-0 mt-3">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; background-color: #0B266E; border-color: #0B266E;">Saya Mengerti & Setuju</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

    @push('scripts')

        <script>
            const csrfToken = '{{ csrf_token() }}';

            // ---- Rules Modal Overlay ----
            @if(isset($showRulesOverlay) && $showRulesOverlay)
            document.addEventListener('DOMContentLoaded', function() {
                const rulesModalEl = document.getElementById('rulesModal');
                if (rulesModalEl && typeof bootstrap !== 'undefined') {
                    const rulesModal = new bootstrap.Modal(rulesModalEl);
                    rulesModal.show();
                }
            });
            @endif

            // ---- Vote Thread (AJAX) ----
            document.querySelectorAll('.vote-thread-btn').forEach(btn => {
                btn.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const threadId = this.dataset.threadId;
                    const value = parseInt(this.dataset.value);
                    try {
                        const res = await fetch(`{{ url('manajemen-mahasiswa/forum') }}/${threadId}/vote`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ value })
                        });
                        const data = await res.json();
                        document.querySelectorAll(`.thread-vote-count-${threadId}`).forEach(el => el.textContent = data.vote_count);
                        const parent = this.closest('.post-actions');
                        if (parent) {
                            parent.querySelectorAll('.vote-thread-btn').forEach(b => b.classList.remove('vote-active-up', 'vote-active-down'));
                            const countEl = parent.querySelector(`.thread-vote-count-${threadId}`);
                            countEl.textContent = data.vote_count;

                            if (data.user_vote === 1) {
                                parent.querySelector('.vote-thread-btn[data-value="1"]').classList.add('vote-active-up');
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            } else if (data.user_vote === -1) {
                                parent.querySelector('.vote-thread-btn[data-value="-1"]').classList.add('vote-active-down');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'none');
                            } else {
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'none');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            }
                        }
                    } catch (err) { console.error('Vote error:', err); }
                });
            });

            // ---- Share (Copy Link) ----
            document.querySelectorAll('.share-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigator.clipboard.writeText(this.dataset.url).then(() => {
                        const orig = this.innerHTML;
                        this.innerHTML = '<span style="font-size:14px">✅</span>';
                        setTimeout(() => this.innerHTML = orig, 2000);
                    });
                });
            });

            // ---- Report Modal ----
            const reportModal = document.getElementById('reportModal');
            if (reportModal) {
                reportModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    reportModal.querySelector('#reportThreadTitle').textContent = `"${btn.dataset.threadTitle}"`;
                    reportModal.querySelector('#reportForm').action = `{{ url('manajemen-mahasiswa/forum') }}/${btn.dataset.threadId}/report`;
                });
            }
            // ---- Forum Card Click Handler ----
            document.querySelectorAll('.forum-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    if (e.target.closest('.dropdown') || e.target.closest('.post-actions') || e.target.closest('.poll-container')) {
                        return;
                    }
                    const threadId = this.dataset.threadId;
                    window.location.href = `{{ url('manajemen-mahasiswa/forum') }}/${threadId}`;
                });
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>
