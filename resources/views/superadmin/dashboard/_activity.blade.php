{{-- resources/views/superadmin/dashboard/_activity.blade.php --}}

<div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
    <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg); white-space:nowrap;">Aktivitas Terkini</span>
    <div style="flex:1; height:1px; background:var(--c-border);"></div>
</div>

<div class="dash-activity" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:32px;">

    {{-- ① User Online --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border);">
            <div style="display:flex; align-items:center; gap:7px;">
                <span style="width:7px; height:7px; border-radius:50%; background:var(--c-success); box-shadow:0 0 0 2px rgba(64,196,170,0.25);"></span>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted);">User Online</span>
            </div>
            <a href="{{ route('superadmin.users.online') }}"
               style="font-size:10px; font-weight:600; color:var(--c-primary); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                Detail
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="flex:1; display:flex; flex-direction:column;">
            @php
                $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
                    ->with('roles')->latest('last_login')->take(4)->get();
            @endphp
            @forelse($online_users as $onlineUser)
            <div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid #F6F8FA; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">
                <div style="position:relative; flex-shrink:0;">
                    <div style="width:28px; height:28px; border-radius:50%; background:var(--c-success-subtle); color:#287F6E; display:flex; align-items:center; justify-content:center; overflow:hidden; border:1px solid #9DE0D3; font-size:10px; font-weight:700;">
                        @if($onlineUser->avatar_url)
                            <img src="{{ $onlineUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                        @else {{ strtoupper(substr($onlineUser->name, 0, 1)) }} @endif
                    </div>
                    <span style="position:absolute; bottom:-1px; right:-1px; width:8px; height:8px; border-radius:50%; background:var(--c-success); border:1.5px solid #fff;"></span>
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:12px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $onlineUser->name }}</p>
                    <p style="font-size:10px; color:var(--c-fg-muted);">{{ ucfirst($onlineUser->roles->first()->name ?? 'User') }}</p>
                </div>
                <span style="font-size:9px; font-weight:600; color:var(--c-success); flex-shrink:0;">
                    {{ $onlineUser->last_login?->diffForHumans(null, true) ?? 'Active' }}
                </span>
            </div>
            @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px; gap:8px; color:var(--c-fg-muted);">
                <p style="font-size:11px;">Tidak ada user online</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ② User Baru --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="display:flex; align-items:center; padding:12px 16px; border-bottom:1px solid var(--c-border); gap:7px;">
            <svg width="14" height="14" fill="none" stroke="var(--c-primary)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11H18.5M16 11H18.5M18.5 11V8.5M18.5 11V13.5M8 14H12C14.7614 14 17 16.2386 17 19V20C17 20.5523 16.5523 21 16 21H4C3.44772 21 3 20.5523 3 20V19C3 16.2386 5.23858 14 8 14ZM14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"/>
            </svg>
            <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted);">User Baru</span>
        </div>
        <div style="flex:1; display:flex; flex-direction:column;">
            @forelse($new_registrations->take(4) as $newUser)
            <div style="display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid #F6F8FA; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">
                <div style="width:28px; height:28px; border-radius:50%; background:var(--c-bg); color:var(--c-fg-muted); display:flex; align-items:center; justify-content:center; overflow:hidden; border:1px solid var(--c-border); font-size:10px; font-weight:700; flex-shrink:0;">
                    @if($newUser->avatar_url)
                        <img src="{{ $newUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ strtoupper(substr($newUser->name, 0, 1)) }} @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:12px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $newUser->name }}</p>
                    <p style="font-size:10px; color:var(--c-fg-muted);">{{ $newUser->created_at->format('d M Y') }}</p>
                </div>
                <span style="font-size:9px; font-weight:600; color:var(--c-fg-muted); background:var(--c-bg); border:1px solid var(--c-border); padding:2px 7px; border-radius:9999px; flex-shrink:0;">
                    {{ ucfirst($newUser->roles->first()->name ?? 'User') }}
                </span>
            </div>
            @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px; gap:8px; color:var(--c-fg-muted);">
                <p style="font-size:11px;">Belum ada user baru</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ③ Log Aktivitas --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--c-border);">
            <div style="display:flex; align-items:center; gap:7px;">
                <svg width="14" height="14" fill="none" stroke="var(--c-primary)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                    <path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z"/>
                </svg>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--c-fg-muted);">Log Aktivitas</span>
            </div>
            <a href="{{ route('superadmin.audit-logs') }}"
               style="font-size:10px; font-weight:600; color:var(--c-primary); text-decoration:none; display:flex; align-items:center; gap:3px; transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                Semua
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="flex:1; display:flex; flex-direction:column;">
            @forelse($recent_logs->take(4) as $log)
            @php
                [$actBg, $actColor] = match(strtoupper($log->action)) {
                    'CREATE' => ['#DDF2EE', '#287F6E'],
                    'UPDATE' => ['#D1F0F9', '#0C4D6E'],
                    'DELETE' => ['var(--c-error-subtle)', 'var(--c-error)'],
                    'LOGIN'  => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
                    default  => ['var(--c-bg)', 'var(--c-fg-muted)'],
                };
            @endphp
            <div style="display:flex; align-items:flex-start; gap:10px; padding:10px 16px; border-bottom:1px solid #F6F8FA; transition:background .12s;"
                 onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">
                <div style="width:28px; height:28px; border-radius:50%; background:var(--c-bg); color:var(--c-fg-muted); display:flex; align-items:center; justify-content:center; overflow:hidden; border:1px solid var(--c-border); font-size:10px; font-weight:700; flex-shrink:0; margin-top:1px;">
                    @if($log->user?->avatar_url)
                        <img src="{{ $log->user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }} @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:11px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $log->user->name ?? 'System' }}</p>
                    <p style="font-size:10px; color:var(--c-fg-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;">{{ $log->description }}</p>
                </div>
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0;">
                    <span style="font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; padding:2px 6px; border-radius:4px; background:{{ $actBg }}; color:{{ $actColor }};">
                        {{ $log->action }}
                    </span>
                    <span style="font-size:9px; color:var(--c-fg-muted);">{{ $log->created_at->diffForHumans(null, true) }}</span>
                </div>
            </div>
            @empty
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:32px; gap:8px; color:var(--c-fg-muted);">
                <p style="font-size:11px;">Tidak ada log aktivitas</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

<style>
/* Fallback responsif untuk layar HP jika tidak cukup menampung 3 grid */
@media (max-width: 1024px) { .dash-activity { grid-template-columns: 1fr !important; } }
</style>