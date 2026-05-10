{{-- resources/views/superadmin/audit-logs/_table.blade.php --}}

{{-- Main Table Card --}}
<div style="background:#fff; border:1px solid var(--c-border); border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

    {{-- Table Toolbar --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:12px; flex-wrap:wrap; background:#FAFAFA;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">Audit Log Table</h2>

        <form method="GET" action="{{ route('superadmin.audit-logs') }}" id="auditFilterForm"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">

            {{-- Preserve sort & per_page state across filter submissions --}}
            <input type="hidden" name="sort_by"  value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

            {{-- Search --}}
            <div style="position:relative; width:220px;">
                <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;"
                     width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search..."
                       style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>

            {{-- ── FILTER DROPDOWN ──
                 FIX: Hapus @click.outside karena itu yang menyebabkan dropdown nutup
                 saat klik elemen di dalam (select, date input).
                 Ganti dengan backdrop invisible di belakang dropdown. --}}
            <div style="position:relative; display:inline-block;"
                 x-data="{ filterOpen: false }">

                <button type="button"
                        @click="filterOpen = !filterOpen"
                        class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border"
                        style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:inherit;"
                        :style="filterOpen ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                    <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                        <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                    </svg>
                    <span class="leading-none">Filter</span>
                    @if(request()->hasAny(['module','action','user_id','date_from','date_to']))
                        <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                    @endif
                </button>

                {{-- Backdrop: klik di luar dropdown area akan nutup --}}
                <div x-show="filterOpen"
                     x-cloak
                     @click="filterOpen = false"
                     style="position:fixed; inset:0; z-index:48; display:none; background:transparent;"></div>

                {{-- Dropdown panel - z-index lebih tinggi dari backdrop --}}
                <div x-show="filterOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="position:absolute; right:0; top:calc(100% + 6px); z-index:49; min-width:280px; background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.1); padding:14px; display:none;">

                    <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-muted); margin:0 0 10px 0;">Advanced Filters</p>

                    <div style="display:flex; flex-direction:column; gap:10px;">

                        {{-- Module --}}
                        <div>
                            <label style="display:block; font-size:10px; font-weight:600; color:var(--c-fg-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em;">Modul</label>
                            <select name="module"
                                    style="width:100%; height:32px; padding:0 10px; border:1px solid var(--c-border); border-radius:7px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; background:#fff; cursor:pointer;">
                                <option value="">Semua Modul</option>
                                @foreach($modules as $mod)
                                <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>
                                    {{ strtoupper(str_replace('_', ' ', $mod)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action --}}
                        <div>
                            <label style="display:block; font-size:10px; font-weight:600; color:var(--c-fg-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em;">Tipe Aksi</label>
                            <select name="action"
                                    style="width:100%; height:32px; padding:0 10px; border:1px solid var(--c-border); border-radius:7px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; background:#fff; cursor:pointer;">
                                <option value="">Semua Aksi</option>
                                @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                    {{ strtoupper($act) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pelaku --}}
                        <div>
                            <label style="display:block; font-size:10px; font-weight:600; color:var(--c-fg-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em;">Pelaku</label>
                            <select name="user_id"
                                    style="width:100%; height:32px; padding:0 10px; border:1px solid var(--c-border); border-radius:7px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; background:#fff; cursor:pointer;">
                                <option value="">Semua User</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date range --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div>
                                <label style="display:block; font-size:10px; font-weight:600; color:var(--c-fg-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em;">Dari</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                       style="width:100%; height:32px; padding:0 8px; border:1px solid var(--c-border); border-radius:7px; font-size:11px; font-family:inherit; color:var(--c-fg); outline:none; box-sizing:border-box; background:#fff; cursor:pointer;">
                            </div>
                            <div>
                                <label style="display:block; font-size:10px; font-weight:600; color:var(--c-fg-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em;">Sampai</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                       style="width:100%; height:32px; padding:0 8px; border:1px solid var(--c-border); border-radius:7px; font-size:11px; font-family:inherit; color:var(--c-fg); outline:none; box-sizing:border-box; background:#fff; cursor:pointer;">
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div style="display:flex; gap:6px; padding-top:2px;">
                            <button type="submit"
                                    style="flex:1; height:32px; background:var(--c-primary); border:none; border-radius:7px; font-size:12px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; transition:background .15s;"
                                    onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                                Terapkan
                            </button>
                            @if(request()->hasAny(['module','action','user_id','date_from','date_to','search']))
                            <a href="{{ route('superadmin.audit-logs') }}"
                               style="flex:1; height:32px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:7px; font-size:12px; font-weight:500; color:var(--c-fg-muted); text-decoration:none; background:#fff; transition:background .15s;"
                               onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                                Reset
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SORT BY DROPDOWN ── --}}
            @php
                $currentSortBy  = request('sort_by', 'created_at');
                $currentSortDir = request('sort_dir', 'desc');
                $sortLabels = [
                    'created_at' => 'Timestamp',
                    'module'     => 'Modul',
                    'action'     => 'Aksi',
                ];
            @endphp
            <div style="position:relative; display:inline-block;"
                 x-data="{ sortOpen: false }">

                <button type="button"
                        @click="sortOpen = !sortOpen"
                        class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border"
                        style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:inherit;"
                        :style="sortOpen ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                    @if($currentSortDir === 'asc')
                    <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                        <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/>
                    </svg>
                    @else
                    <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                        <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/>
                    </svg>
                    @endif
                    <span class="leading-none">Sort: {{ $sortLabels[$currentSortBy] ?? 'Timestamp' }}</span>
                    @if($currentSortBy !== 'created_at' || $currentSortDir !== 'desc')
                        <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                    @endif
                </button>

                {{-- Backdrop --}}
                <div x-show="sortOpen" x-cloak @click="sortOpen = false"
                     style="position:fixed; inset:0; z-index:48; display:none; background:transparent;"></div>

                <div x-show="sortOpen" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="position:absolute; right:0; top:calc(100% + 6px); z-index:49; min-width:200px; background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.1); padding:8px; display:none;">

                    {{-- Sort by column --}}
                    <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Kolom</p>
                    @foreach($sortLabels as $col => $label)
                    <button type="button"
                            onclick="setSortBy('{{ $col }}')"
                            style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $currentSortBy === $col ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $currentSortBy === $col ? '700' : '500' }}; color:{{ $currentSortBy === $col ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                            onmouseover="if('{{ $currentSortBy }}' !== '{{ $col }}') this.style.background='var(--c-bg)'"
                            onmouseout="if('{{ $currentSortBy }}' !== '{{ $col }}') this.style.background='transparent'">
                        <span>{{ $label }}</span>
                        @if($currentSortBy === $col)
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                        @endif
                    </button>
                    @endforeach

                    <div style="height:1px; background:var(--c-border); margin:7px 0;"></div>

                    {{-- Sort direction --}}
                    <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Arah</p>

                    <button type="button" onclick="setSortDir('desc')"
                            style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $currentSortDir === 'desc' ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $currentSortDir === 'desc' ? '700' : '500' }}; color:{{ $currentSortDir === 'desc' ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                            onmouseover="if('{{ $currentSortDir }}' !== 'desc') this.style.background='var(--c-bg)'"
                            onmouseout="if('{{ $currentSortDir }}' !== 'desc') this.style.background='transparent'">
                        <div style="display:flex; align-items:center; gap:7px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/>
                            </svg>
                            <span>Terbaru (Desc)</span>
                        </div>
                        @if($currentSortDir === 'desc')
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                        @endif
                    </button>

                    <button type="button" onclick="setSortDir('asc')"
                            style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $currentSortDir === 'asc' ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $currentSortDir === 'asc' ? '700' : '500' }}; color:{{ $currentSortDir === 'asc' ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                            onmouseover="if('{{ $currentSortDir }}' !== 'asc') this.style.background='var(--c-bg)'"
                            onmouseout="if('{{ $currentSortDir }}' !== 'asc') this.style.background='transparent'">
                        <div style="display:flex; align-items:center; gap:7px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/>
                            </svg>
                            <span>Terlama (Asc)</span>
                        </div>
                        @if($currentSortDir === 'asc')
                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
                        @endif
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:740px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                    <th style="padding:11px 16px; width:44px; text-align:left;">
                        <input type="checkbox" id="selectAllLogs"
                               style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                    </th>
                    <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Timestamp</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">User Name</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Modul</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Aksi</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted);">Deskripsi</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                @php
                    [$modBg, $modColor, $modBorder] = match($log->module ?? '') {
                        'auth'               => ['#ECFDF5', '#059669', '#A7F3D0'],
                        'bank_soal'          => ['#FFFBEB', '#D97706', '#FDE68A'],
                        'capstone'           => ['#EFF6FF', '#3B82F6', '#BFDBFE'],
                        'eoffice'            => ['rgba(11,38,110,0.06)', 'var(--c-primary)', 'rgba(11,38,110,0.18)'],
                        'user_management'    => ['rgba(11,38,110,0.06)', 'var(--c-primary)', 'rgba(11,38,110,0.18)'],
                        'manajemen_mahasiswa'=> ['#ECFDF5', '#059669', '#A7F3D0'],
                        'modul_setting'      => ['#FFF1F2', '#F43F5E', '#FECDD3'],
                        default              => ['#F9FAFB', '#6B7280', '#E5E7EB'],
                    };
                    [$actBg, $actColor, $actBorder] = match(strtolower($log->action ?? '')) {
                        'create' => ['#ECFDF5', '#059669', '#A7F3D0'],
                        'update' => ['#FFFBEB', '#D97706', '#FDE68A'],
                        'delete' => ['#FEF2F2', '#DC2626', '#FECACA'],
                        'login'  => ['rgba(11,38,110,0.06)', 'var(--c-primary)', 'rgba(11,38,110,0.18)'],
                        'logout' => ['#F9FAFB', '#6B7280', '#E5E7EB'],
                        'view'   => ['#EFF6FF', '#3B82F6', '#BFDBFE'],
                        default  => ['#F9FAFB', '#6B7280', '#E5E7EB'],
                    };

                    $user        = $log->user ?? null;
                    $isSA        = $user?->hasRole('superadmin');
                    $initials    = $user ? strtoupper(substr($user->name, 0, 1)) : 'S';
                    $sp          = $user ? strpos($user->name, ' ') : false;
                    if ($sp !== false) $initials .= strtoupper(substr($user->name, $sp+1, 1));
                    $isSuspended = $user?->isSuspended();
                    $rowNo       = ($logs->currentPage() - 1) * (int) request('per_page', 10) + $index + 1;
                @endphp
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">

                    {{-- Checkbox --}}
                    <td style="padding:14px 16px; width:44px;">
                        <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}"
                               class="log-checkbox"
                               style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                    </td>

                    {{-- No --}}
                    <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;">{{ $rowNo }}</td>

                    {{-- Timestamp --}}
                    <td style="padding:14px 16px; white-space:nowrap;">
                        <span style="font-size:12px; font-weight:600; color:var(--c-fg); display:block;">{{ $log->created_at->format('d M Y') }}</span>
                        <span style="font-size:11px; color:var(--c-fg-muted);">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>

                    {{-- User Name --}}
                    <td style="padding:14px 16px; min-width:180px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="position:relative; flex-shrink:0;">
                                <div style="width:34px; height:34px; border-radius:50%; background:{{ $isSA ? 'rgba(11,38,110,0.07)' : '#F3F4F6' }}; color:{{ $isSA ? '#0B266E' : '#6B7280' }}; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; font-weight:700; border:1.5px solid {{ $isSA ? 'rgba(11,38,110,0.15)' : '#E5E7EB' }};">
                                    @if($user?->avatar_url)
                                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @elseif($isSA)
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                    @elseif($user)
                                        {{ $initials }}
                                    @else
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    @endif
                                </div>
                                @if($user?->is_online && !$isSuspended)
                                    <span style="position:absolute; bottom:-1px; right:-1px; width:9px; height:9px; border-radius:50%; background:#22C55E; border:2px solid #fff;"></span>
                                @endif
                            </div>
                            <div style="min-width:0;">
                                <p style="font-size:13px; font-weight:600; color:{{ $isSuspended ? '#DC2626' : 'var(--c-fg)' }}; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:140px; margin:0; {{ $isSuspended ? 'text-decoration:line-through; text-decoration-color:#FECACA;' : '' }}">
                                    {{ $user?->name ?? 'System' }}
                                </p>
                                <p style="font-size:11px; color:var(--c-fg-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:140px; margin:1px 0 0 0;">
                                    {{ $user?->email ?? 'Automated Task' }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Module --}}
                    <td style="padding:14px 16px;">
                        <span style="display:inline-flex; padding:3px 10px; font-size:11px; font-weight:500; border-radius:9999px; white-space:nowrap; color:{{ $modColor }}; border:1px solid {{ $modBorder }}; background:{{ $modBg }};">
                            {{ strtoupper(str_replace('_', ' ', $log->module ?? 'N/A')) }}
                        </span>
                    </td>

                    {{-- Action badge --}}
                    <td style="padding:14px 16px;">
                        <span style="display:inline-flex; padding:3px 10px; font-size:11px; font-weight:500; border-radius:9999px; white-space:nowrap; color:{{ $actColor }}; border:1px solid {{ $actBorder }}; background:{{ $actBg }};">
                            {{ strtoupper($log->action ?? 'N/A') }}
                        </span>
                    </td>

                    {{-- Description --}}
                    <td style="padding:14px 16px;">
                        <p style="font-size:12px; font-weight:400; color:var(--c-fg-sec); max-width:300px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; margin:0;"
                           title="{{ $log->description ?? '-' }}">
                            {{ $log->description ?? '-' }}
                        </p>
                    </td>

                    {{-- Status --}}
                    <td style="padding:14px 16px; text-align:center;">
                        @if($user)
                            @if($isSuspended)
                                <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#DC2626; border:1px solid #FECACA; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#DC2626; flex-shrink:0;"></span>Suspend
                                </span>
                            @elseif($user->is_online)
                                <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#059669; border:1px solid #A7F3D0; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#22C55E; flex-shrink:0; animation:pulse-dot 2s infinite;"></span>Online
                                </span>
                            @else
                                <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#6B7280; border:1px solid #E5E7EB; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#9CA3AF; flex-shrink:0;"></span>Offline
                                </span>
                            @endif
                        @else
                            <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#6B7280; border:1px solid #E5E7EB; padding:3px 12px; border-radius:9999px; white-space:nowrap;">System</span>
                        @endif
                    </td>


                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:60px 24px; text-align:center;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 4H6C4.34 4 3 5.34 3 7V18C3 19.66 4.34 21 6 21H17C18.66 21 20 19.66 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88 19.88 8 18.5 8C17.12 8 16 6.88 16 5.5C16 4.12 17.12 3 18.5 3C19.88 3 21 4.12 21 5.5Z" stroke-linecap="round"/>
                            </svg>
                            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada aktivitas tercatat</p>
                            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:0;">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Pagination ── --}}
    @php
        $perPageOptions  = [5, 10, 25, 50];
        $currentPerPage  = (int) request('per_page', 10);
        $from  = $logs->firstItem() ?? 0;
        $to    = $logs->lastItem()  ?? 0;
        $total = $logs->total();
        $currentPage = $logs->currentPage();
        $lastPage    = $logs->lastPage();
    @endphp
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid var(--c-border); flex-wrap:wrap; gap:10px;">

        <div style="display:flex; align-items:center; gap:10px;" x-data="{ open: false }">
            <div style="display:flex; align-items:center; gap:6px;">
                <span style="font-size:12px; font-weight:500; color:var(--c-fg-muted);">Per page</span>
                <div style="position:relative;">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex flex-row items-center justify-center gap-1.5 h-[28px] px-2 bg-white border rounded-md text-xs font-semibold whitespace-nowrap cursor-pointer box-border"
                            style="border-color:var(--c-border); color:var(--c-fg); font-family:inherit;"
                            :style="open ? 'border-color:var(--c-primary);' : ''">
                        <span>{{ $currentPerPage }}</span>
                        <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" style="color:var(--c-fg-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute bottom-[calc(100%+5px)] left-0 bg-white border rounded-lg shadow-lg min-w-[80px] z-50 overflow-hidden"
                         style="border-color:var(--c-border); display:none;">
                        @foreach($perPageOptions as $opt)
                        <button type="button" onclick="setPerPage({{ $opt }})"
                                style="display:block; width:100%; padding:7px 14px; font-size:12px; font-weight:{{ $currentPerPage == $opt ? '700' : '500' }}; color:{{ $currentPerPage == $opt ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; background:{{ $currentPerPage == $opt ? 'rgba(11,38,110,0.05)' : 'transparent' }}; border:none; text-align:left; cursor:pointer; font-family:inherit; transition:background .12s;"
                                onmouseover="if({{ $currentPerPage }} !== {{ $opt }}) this.style.background='var(--c-bg)'" onmouseout="if({{ $currentPerPage }} !== {{ $opt }}) this.style.background='transparent'">
                            {{ $opt }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="width:1px; height:14px; background:var(--c-border);"></div>
            <span style="font-size:12px; color:var(--c-fg-sec);">
                Showing <strong style="color:var(--c-fg); font-weight:700;">{{ $from }}</strong>
                to <strong style="color:var(--c-fg); font-weight:700;">{{ $to }}</strong>
                of <strong style="color:var(--c-fg); font-weight:700;">{{ number_format($total) }}</strong> results
            </span>
        </div>

        @if($lastPage > 1)
        <div style="display:flex; align-items:center; gap:4px;">
            @if($currentPage > 1)
            <a href="#" onclick="goToPage({{ $currentPage - 1 }}); return false;" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            @else
            <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg></span>
            @endif

            @php $range = 2; $start = max(1, $currentPage - $range); $end = min($lastPage, $currentPage + $range); @endphp
            @if($start > 1)
                <a href="#" onclick="goToPage(1); return false;" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
                @if($start > 2)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
            @endif
            @for($p = $start; $p <= $end; $p++)
            <a href="#" onclick="goToPage({{ $p }}); return false;" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:{{ $p === $currentPage ? '700' : '500' }}; text-decoration:none; transition:all .15s; {{ $p === $currentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);' }}">{{ $p }}</a>
            @endfor
            @if($end < $lastPage)
                @if($end < $lastPage - 1)<span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>@endif
                <a href="#" onclick="goToPage({{ $lastPage }}); return false;" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">{{ $lastPage }}</a>
            @endif

            @if($currentPage < $lastPage)
            <a href="#" onclick="goToPage({{ $currentPage + 1 }}); return false;" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
            </a>
            @else
            <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg></span>
            @endif
        </div>
        @endif
    </div>

</div>

<style>
@keyframes pulse-dot { 0%, 100% { opacity:1; } 50% { opacity:.4; } }
</style>