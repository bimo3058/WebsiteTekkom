{{-- resources/views/superadmin/audit-logs/_cards.blade.php --}}
@php
    $allOnline    = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
    $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();
    $onlineUsers    = $allOnline->take(4);
    $suspendedUsers = $allSuspended->take(4);
    $hasOnline    = $allOnline->isNotEmpty();
    $hasSuspended = $allSuspended->isNotEmpty();
@endphp

@if($hasOnline || $hasSuspended)
<div style="display:grid; grid-template-columns:{{ ($hasOnline && $hasSuspended) ? '1fr 1fr' : '1fr' }}; gap:12px; margin-bottom:20px;">

    {{-- ── Online Users ───────────────────────────── --}}
    @if($hasOnline)
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border);">
            <div style="display:flex; align-items:center; gap:7px;">
                <span style="width:7px; height:7px; border-radius:50%; background:var(--c-success); box-shadow:0 0 0 2px rgba(64,196,170,.25);"></span>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted);">Online ({{ $allOnline->count() }})</span>
            </div>
            <a href="{{ route('superadmin.users.online') }}"
               style="font-size:10px; font-weight:600; color:var(--c-primary); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                View All
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="display:flex; flex-direction:column;">
            @foreach($onlineUsers as $onlineUser)
            @php
                $isSA = $onlineUser->hasRole('superadmin');
                $initials = strtoupper(substr($onlineUser->name, 0, 1));
                $sp = strpos($onlineUser->name, ' ');
                if ($sp !== false) $initials .= strtoupper(substr($onlineUser->name, $sp+1, 1));
            @endphp
            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 16px; border-bottom:1px solid #F6F8FA; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">
                <div style="display:flex; align-items:center; gap:10px; flex:1; min-width:0;">
                    <div style="position:relative; flex-shrink:0;">
                        <div style="width:28px; height:28px; border-radius:50%; background:{{ $isSA ? 'rgba(11,38,110,0.08)' : 'var(--c-success-subtle)' }}; color:{{ $isSA ? 'var(--c-primary)' : '#287F6E' }}; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:10px; font-weight:700; border:1px solid {{ $isSA ? 'var(--c-primary-border)' : '#9DE0D3' }};">
                            @if($onlineUser->avatar_url)
                                <img src="{{ $onlineUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                            @elseif($isSA)
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <span style="position:absolute; bottom:-1px; right:-1px; width:8px; height:8px; border-radius:50%; background:var(--c-success); border:1.5px solid #fff;"></span>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:12px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $onlineUser->name }}</p>
                        <p style="font-size:10px; color:var(--c-fg-muted);">{{ ucfirst($onlineUser->roles->first()->name ?? 'User') }}</p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:4px; flex-shrink:0;">
                    @if(!$isSA)
                    <button type="button" onclick="openSuspendModal({ id: '{{ $onlineUser->id }}', name: '{{ addslashes($onlineUser->name) }}' })"
                            title="Suspend"
                            style="width:26px; height:26px; border-radius:6px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-error); transition:background .12s;"
                            onmouseover="this.style.background='var(--c-error-subtle)'" onmouseout="this.style.background='transparent'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </button>
                    @endif
                    <button type="button" onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ addslashes($onlineUser->name) }}' })"
                            title="Force Logout"
                            style="width:26px; height:26px; border-radius:6px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-warning); transition:background .12s;"
                            onmouseover="this.style.background='var(--c-warning-subtle)'" onmouseout="this.style.background='transparent'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Suspended Users ─────────────────────────── --}}
    @if($hasSuspended)
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border);">
            <div style="display:flex; align-items:center; gap:7px;">
                <span style="width:7px; height:7px; border-radius:50%; background:var(--c-error);"></span>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted);">Suspended ({{ $allSuspended->count() }})</span>
            </div>
            <a href="{{ route('superadmin.users.suspended') }}"
               style="font-size:10px; font-weight:600; color:var(--c-error); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                View All
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="display:flex; flex-direction:column;">
            @foreach($suspendedUsers as $su)
            @php
                $isSA2 = $su->hasRole('superadmin');
                $initials2 = strtoupper(substr($su->name, 0, 1));
                $sp2 = strpos($su->name, ' ');
                if ($sp2 !== false) $initials2 .= strtoupper(substr($su->name, $sp2+1, 1));
            @endphp
            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 16px; border-bottom:1px solid #F6F8FA; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">
                <div style="display:flex; align-items:center; gap:10px; flex:1; min-width:0;">
                    <div style="width:28px; height:28px; border-radius:50%; background:var(--c-error-subtle); color:var(--c-error); display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:10px; font-weight:700; border:1px solid #ED8296; opacity:0.7; flex-shrink:0;">
                        @if($su->avatar_url)
                            <img src="{{ $su->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ $initials2 }}
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; text-decoration:line-through; text-decoration-color:var(--c-error-subtle);">{{ $su->name }}</p>
                        <p style="font-size:10px; color:var(--c-error); font-style:italic; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $su->suspension_reason ?? 'Policy Violation' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('superadmin.users.unsuspend', $su) }}" style="margin:0; flex-shrink:0;">
                    @csrf
                    <button type="submit" title="Unsuspend"
                            style="width:26px; height:26px; border-radius:6px; border:1px solid var(--c-success-subtle); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--c-success); transition:background .12s;"
                            onmouseover="this.style.background='var(--c-success-subtle)'" onmouseout="this.style.background='#fff'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 11V7a4 4 0 0 1 7.94-.87M19 15v4M5 11h10v9H3z"/><rect x="3" y="11" width="12" height="10" rx="2"/>
                        </svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endif