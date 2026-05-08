{{-- resources/views/superadmin/audit-logs/_cards.blade.php --}}
@php
    $allOnline    = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
    $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();
    $onlineUsers    = $allOnline->take(5);
    $suspendedUsers = $allSuspended->take(5);
@endphp

{{-- ── Bulk Action Bar: Online ── --}}
<div id="onlineBulkBar"
     style="display:none; align-items:center; justify-content:space-between; background:#1A1A2E; border-radius:10px; padding:10px 16px; margin-bottom:8px; box-shadow:0 4px 16px rgba(0,0,0,.2);">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:28px; height:28px; border-radius:7px; background:var(--c-primary); display:flex; align-items:center; justify-content:center;">
            <svg width="13" height="13" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <span style="font-size:13px; font-weight:600; color:#fff;">
            <span id="onlineSelectedCount" style="color:#A5B4FC; font-size:15px; font-weight:700; margin-right:2px;">0</span> user dipilih
        </span>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="openBulkForceLogoutModal()"
                style="display:inline-flex; align-items:center; gap:5px; padding:6px 14px; background:#D97706; border:none; border-radius:7px; font-size:11px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:.05em; transition:background .15s;"
                onmouseover="this.style.background='#B45309'" onmouseout="this.style.background='#D97706'">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.93L7.87 4.06C6.39 3.66 5 5.21 5 7.27V16.73C5 18.79 6.39 20.34 7.87 19.94L11.07 19.06C12.19 18.76 13 17.42 13 15.86V15.27M11 12H19M19 12L16.5 9.5M19 12L16.5 14.5"/>
            </svg>
            Force Logout
        </button>
        <div style="width:1px; height:18px; background:rgba(255,255,255,.12);"></div>
        <button onclick="deselectAllOnline()"
                style="font-size:11px; font-weight:600; color:rgba(255,255,255,.5); background:none; border:none; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:.05em; transition:color .15s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
            Batal
        </button>
    </div>
</div>

{{-- ── Bulk Action Bar: Suspended ── --}}
<div id="suspendedBulkBar"
     style="display:none; align-items:center; justify-content:space-between; background:#1A1A2E; border-radius:10px; padding:10px 16px; margin-bottom:8px; box-shadow:0 4px 16px rgba(0,0,0,.2);">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:28px; height:28px; border-radius:7px; background:#059669; display:flex; align-items:center; justify-content:center;">
            <svg width="13" height="13" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <span style="font-size:13px; font-weight:600; color:#fff;">
            <span id="suspendedSelectedCount" style="color:#6EE7B7; font-size:15px; font-weight:700; margin-right:2px;">0</span> user dipilih
        </span>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="openBulkUnsuspendModal()"
                style="display:inline-flex; align-items:center; gap:5px; padding:6px 14px; background:#059669; border:none; border-radius:7px; font-size:11px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:.05em; transition:background .15s;"
                onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
            <img src="{{ asset('images/icons/unlocked-01.svg') }}" width="12" height="12"
                 style="filter:brightness(0) invert(1);" alt="">
            Unsuspend
        </button>
        <div style="width:1px; height:18px; background:rgba(255,255,255,.12);"></div>
        <button onclick="deselectAllSuspended()"
                style="font-size:11px; font-weight:600; color:rgba(255,255,255,.5); background:none; border:none; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:.05em; transition:color .15s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
            Batal
        </button>
    </div>
</div>

{{-- ── Cards Grid ── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- ── Online Users Card ── --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">Online</h2>
            <a href="{{ route('superadmin.users.online') }}"
               style="font-size:11px; font-weight:600; color:var(--c-primary); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                View All
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:380px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                        <th style="padding:11px 16px; width:44px; text-align:left;">
                            <input type="checkbox" id="selectAllOnline"
                                   style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                        </th>
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:40px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">User Name</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlineUsers as $idx => $onlineUser)
                    @php
                        $isSA     = $onlineUser->hasRole('superadmin');
                        $initials = strtoupper(substr($onlineUser->name, 0, 1));
                        $sp       = strpos($onlineUser->name, ' ');
                        if ($sp !== false) $initials .= strtoupper(substr($onlineUser->name, $sp+1, 1));
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                        onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">

                        {{-- Checkbox --}}
                        <td style="padding:14px 16px; width:44px;">
                            @if(!$isSA)
                            <input type="checkbox"
                                   class="online-checkbox"
                                   value="{{ $onlineUser->id }}"
                                   style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                            @else
                            {{-- Superadmin tidak bisa di-bulk logout --}}
                            <div style="width:15px; height:15px; display:flex; align-items:center; justify-content:center;" title="Superadmin">
                                <svg width="12" height="12" fill="#9CA3AF" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                            </div>
                            @endif
                        </td>

                        <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:40px;">{{ $idx + 1 }}</td>

                        {{-- User Name --}}
                        <td style="padding:14px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="position:relative; flex-shrink:0;">
                                    <div style="width:32px; height:32px; border-radius:50%; background:{{ $isSA ? 'rgba(11,38,110,0.07)' : '#F3F4F6' }}; color:{{ $isSA ? '#0B266E' : '#6B7280' }}; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; font-weight:700; border:1.5px solid {{ $isSA ? 'rgba(11,38,110,0.15)' : '#E5E7EB' }};">
                                        @if($onlineUser->avatar_url)
                                            <img src="{{ $onlineUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                        @elseif($isSA)
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <span style="position:absolute; bottom:-1px; right:-1px; width:9px; height:9px; border-radius:50%; background:#22C55E; border:2px solid #fff;"></span>
                                </div>
                                <div style="min-width:0;">
                                    <p style="font-size:13px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:130px; margin:0;">{{ $onlineUser->name }}</p>
                                    <p style="font-size:11px; color:var(--c-fg-muted); margin:1px 0 0 0;">{{ ucfirst($onlineUser->roles->first()->name ?? 'User') }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#059669; border:1px solid #A7F3D0; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
                                <span style="width:6px; height:6px; border-radius:50%; background:#22C55E; flex-shrink:0;"></span>
                                Online
                            </span>
                        </td>

                        {{-- Action: 3-dot dropdown --}}
                        <td style="padding:14px 16px; text-align:center;">
                            <div style="position:relative; display:inline-block;" x-data="{ open: false }">
                                <button type="button"
                                        @click="open = !open" @click.outside="open = false"
                                        style="width:28px; height:28px; border-radius:6px; border:1px solid var(--c-border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg-muted); transition:all .15s; margin:0 auto;"
                                        onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
                                        onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                </button>

                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     style="position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid var(--c-border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.1); min-width:155px; z-index:50; overflow:hidden; display:none;">
                                    <div style="padding:5px;">
                                        {{-- Force Logout --}}
                                        <button type="button"
                                                onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ addslashes($onlineUser->name) }}' })"
                                                style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#D97706; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background='none'">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                                <path d="M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238"/>
                                            </svg>
                                            Force Logout
                                        </button>
                                        @if(!$isSA)
                                        <div style="height:1px; background:var(--c-border); margin:4px 6px;"></div>
                                        {{-- Suspend --}}
                                        <button type="button"
                                                onclick="openSuspendModal({ id: '{{ $onlineUser->id }}', name: '{{ addslashes($onlineUser->name) }}' })"
                                                style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                                <path d="M15.5 15.5L12 12L8.5 8.5M8.5 15.5L12 12L15.5 8.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z"/>
                                            </svg>
                                            Suspend User
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:32px 16px; text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#F3F4F6; display:flex; align-items:center; justify-content:center;">
                                    <svg width="16" height="16" fill="none" stroke="#9CA3AF" viewBox="0 0 24 24" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <p style="font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Tidak ada user online</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Suspended Users Card ── --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">Suspend</h2>
            <a href="{{ route('superadmin.users.suspended') }}"
               style="font-size:11px; font-weight:600; color:var(--c-error); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                View All
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:380px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                        <th style="padding:11px 16px; width:44px; text-align:left;">
                            <input type="checkbox" id="selectAllSuspended"
                                   style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                        </th>
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:40px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">User Name</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suspendedUsers as $idx => $su)
                    @php
                        $initials2 = strtoupper(substr($su->name, 0, 1));
                        $sp2       = strpos($su->name, ' ');
                        if ($sp2 !== false) $initials2 .= strtoupper(substr($su->name, $sp2+1, 1));
                    @endphp
                    <tr style="border-bottom:1px solid #F3F4F6; background:#FFF9F9; transition:background .12s;"
                        onmouseover="this.style.background='#FFF5F5'" onmouseout="this.style.background='#FFF9F9'">

                        {{-- Checkbox --}}
                        <td style="padding:14px 16px; width:44px;">
                            <input type="checkbox"
                                   class="suspended-checkbox"
                                   value="{{ $su->id }}"
                                   style="width:15px; height:15px; border-radius:4px; cursor:pointer; accent-color:var(--c-primary);">
                        </td>

                        <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:40px;">{{ $idx + 1 }}</td>

                        {{-- User Name --}}
                        <td style="padding:14px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="flex-shrink:0;">
                                    <div style="width:32px; height:32px; border-radius:50%; background:#FEF2F2; color:#DC2626; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:11px; font-weight:700; border:1.5px solid #FECACA; opacity:0.8;">
                                        @if($su->avatar_url)
                                            <img src="{{ $su->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ $initials2 }}
                                        @endif
                                    </div>
                                </div>
                                <div style="min-width:0;">
                                    <p style="font-size:13px; font-weight:600; color:#DC2626; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:130px; margin:0; text-decoration:line-through; text-decoration-color:#FECACA;">{{ $su->name }}</p>
                                    <p style="font-size:11px; color:var(--c-error); font-style:italic; margin:1px 0 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:130px;">{{ $su->suspension_reason ?? 'Policy Violation' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td style="padding:14px 16px;">
                            <span style="display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:500; color:#DC2626; border:1px solid #FECACA; padding:3px 12px; border-radius:9999px; white-space:nowrap;">
                                <span style="width:6px; height:6px; border-radius:50%; background:#DC2626; flex-shrink:0;"></span>
                                Suspend
                            </span>
                        </td>

                        {{-- Action: Unsuspend button --}}
                        <td style="padding:14px 16px; text-align:center;">
                            <form method="POST" action="{{ route('superadmin.users.unsuspend', $su) }}" style="margin:0; display:inline;">
                                @csrf
                                <button type="submit" title="Unsuspend"
                                        style="width:28px; height:28px; border-radius:6px; border:1px solid var(--c-border); background:#fff; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all .12s;"
                                        onmouseover="this.style.background='#ECFDF5'; this.style.borderColor='#6EE7B7';"
                                        onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)';">
                                    <img src="{{ asset('images/icons/unlocked-01.svg') }}" width="13" height="13"
                                         style="filter:invert(37%) sepia(71%) saturate(512%) hue-rotate(107deg) brightness(95%) contrast(92%);" alt="Unsuspend">
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:32px 16px; text-align:center;">
                            <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:#FEF2F2; display:flex; align-items:center; justify-content:center;">
                                    <img src="{{ asset('images/icons/locked-01.svg') }}" width="16" height="16"
                                         style="filter:invert(58%) sepia(10%) saturate(400%) hue-rotate(315deg) brightness(110%) contrast(90%);" alt="">
                                </div>
                                <p style="font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.06em; margin:0;">Tidak ada user tersuspend</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Modal Bulk Force Logout ── --}}
<div id="modalBulkForceLogout"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,.45); backdrop-filter:blur(2px);">
    <div style="background:#fff; border-radius:16px; padding:28px; width:100%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.2); margin:16px;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px;">
            <div style="width:40px; height:40px; border-radius:10px; background:#FFFBEB; border:1px solid #FDE68A; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#D97706" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.93L7.87 4.06C6.39 3.66 5 5.21 5 7.27V16.73C5 18.79 6.39 20.34 7.87 19.94L11.07 19.06C12.19 18.76 13 17.42 13 15.86V15.27M11 12H19M19 12L16.5 9.5M19 12L16.5 14.5"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size:15px; font-weight:700; color:var(--c-fg); margin:0 0 4px 0;">Bulk Force Logout</h3>
                <p style="font-size:12px; color:var(--c-fg-muted); margin:0;">
                    <span id="bulkForceLogoutCount" style="font-weight:700; color:#D97706;">0</span> user akan di-logout paksa dari sistem.
                </p>
            </div>
        </div>
        <form id="formBulkForceLogout" method="POST" action="{{ route('superadmin.users.bulk-force-logout') }}">
            @csrf
            <div id="bulkForceLogoutIds"></div>
            <div style="display:flex; gap:8px; margin-top:4px;">
                <button type="button" onclick="closeModal('modalBulkForceLogout')"
                        style="flex:1; padding:10px; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-muted); background:#fff; cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1; padding:10px; border:none; border-radius:8px; font-size:12px; font-weight:700; color:#fff; background:#D97706; cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='#B45309'" onmouseout="this.style.background='#D97706'">
                    Ya, Force Logout
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Bulk Unsuspend ── --}}
<div id="modalBulkUnsuspend"
     style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,.45); backdrop-filter:blur(2px);">
    <div style="background:#fff; border-radius:16px; padding:28px; width:100%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.2); margin:16px;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px;">
            <div style="width:40px; height:40px; border-radius:10px; background:#ECFDF5; border:1px solid #A7F3D0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <img src="{{ asset('images/icons/unlocked-01.svg') }}" width="18" height="18"
                     style="filter:invert(37%) sepia(71%) saturate(512%) hue-rotate(107deg) brightness(95%) contrast(92%);" alt="">
            </div>
            <div>
                <h3 style="font-size:15px; font-weight:700; color:var(--c-fg); margin:0 0 4px 0;">Bulk Unsuspend</h3>
                <p style="font-size:12px; color:var(--c-fg-muted); margin:0;">
                    <span id="bulkUnsuspendCount" style="font-weight:700; color:#059669;">0</span> user akan di-unsuspend dan bisa login kembali.
                </p>
            </div>
        </div>
        <form id="formBulkUnsuspend" method="POST" action="{{ route('superadmin.users.bulk-unsuspend') }}">
            @csrf
            <div id="bulkUnsuspendIds"></div>
            <div style="display:flex; gap:8px; margin-top:4px;">
                <button type="button" onclick="closeModal('modalBulkUnsuspend')"
                        style="flex:1; padding:10px; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-muted); background:#fff; cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1; padding:10px; border:none; border-radius:8px; font-size:12px; font-weight:700; color:#fff; background:#059669; cursor:pointer; font-family:inherit; transition:background .15s;"
                        onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    Ya, Unsuspend
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Online card bulk logic ─────────────────────────────────────────────────
function updateOnlineBulkBar() {
    const cbs  = document.querySelectorAll('.online-checkbox:checked');
    const bar  = document.getElementById('onlineBulkBar');
    const cnt  = document.getElementById('onlineSelectedCount');
    if (cnt) cnt.textContent = cbs.length;
    if (bar) bar.style.display = cbs.length > 0 ? 'flex' : 'none';
}

window.deselectAllOnline = function() {
    document.getElementById('selectAllOnline').checked = false;
    document.querySelectorAll('.online-checkbox').forEach(cb => cb.checked = false);
    updateOnlineBulkBar();
};

window.openBulkForceLogoutModal = function() {
    const ids = Array.from(document.querySelectorAll('.online-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) return;
    const container = document.getElementById('bulkForceLogoutIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('bulkForceLogoutCount').textContent = ids.length;
    openModal('modalBulkForceLogout');
};

document.getElementById('selectAllOnline')?.addEventListener('change', function() {
    document.querySelectorAll('.online-checkbox').forEach(cb => cb.checked = this.checked);
    updateOnlineBulkBar();
});
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('online-checkbox')) updateOnlineBulkBar();
});

// ── Suspended card bulk logic ──────────────────────────────────────────────
function updateSuspendedBulkBar() {
    const cbs  = document.querySelectorAll('.suspended-checkbox:checked');
    const bar  = document.getElementById('suspendedBulkBar');
    const cnt  = document.getElementById('suspendedSelectedCount');
    if (cnt) cnt.textContent = cbs.length;
    if (bar) bar.style.display = cbs.length > 0 ? 'flex' : 'none';
}

window.deselectAllSuspended = function() {
    document.getElementById('selectAllSuspended').checked = false;
    document.querySelectorAll('.suspended-checkbox').forEach(cb => cb.checked = false);
    updateSuspendedBulkBar();
};

window.openBulkUnsuspendModal = function() {
    const ids = Array.from(document.querySelectorAll('.suspended-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) return;
    const container = document.getElementById('bulkUnsuspendIds');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    document.getElementById('bulkUnsuspendCount').textContent = ids.length;
    openModal('modalBulkUnsuspend');
};

document.getElementById('selectAllSuspended')?.addEventListener('change', function() {
    document.querySelectorAll('.suspended-checkbox').forEach(cb => cb.checked = this.checked);
    updateSuspendedBulkBar();
});
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('suspended-checkbox')) updateSuspendedBulkBar();
});
</script>