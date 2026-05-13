{{-- resources/views/superadmin/dashboard/_activity.blade.php --}}

<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Aktivitas Terkini</span>
</div>

@php
    if (isset($new_registrations)) $new_registrations->loadMissing('roles');
    if (isset($recent_logs))       $recent_logs->loadMissing('user.roles');

    $avatarGradients = [
        'linear-gradient(135deg,#D39C3D,#956321)',
        'linear-gradient(135deg,#5C78B8,#0B266E)',
        'linear-gradient(135deg,#40C4AA,#287F6E)',
        'linear-gradient(135deg,#ED8296,#95122B)',
    ];

    $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
        ->with('roles')->latest('last_login')->take(5)->get();
@endphp

<div class="dash-activity" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">

    {{-- ① Monitoring Online --}}
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Monitoring Online</div>
            <a href="{{ route('superadmin.users.online') }}" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Lihat Semua
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:380px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Last Online</th>
                </tr>
            </thead>
            <tbody>
            @forelse($online_users as $i => $onlineUser)
                @php
                    $role = $onlineUser->roles->first()->name ?? 'user';
                    $grad = $avatarGradients[$onlineUser->id % count($avatarGradients)];
                @endphp
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);font-size:13px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <x-ui.user-avatar :user="$onlineUser" size="sm" :gradient="$grad" />
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;">{{ $onlineUser->name }}</div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $onlineUser->nim ?? $onlineUser->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <x-ui.role-badge :role="$role" size="xs" />
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <x-ui.status-badge status="online" />
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Tidak ada user online</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- ② User Baru Terdaftar --}}
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">User Baru Terdaftar</div>
            <a href="{{ route('superadmin.users.index') }}" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Detail
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:380px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
            @forelse($new_registrations->take(5) as $newUser)
                @php
                    $role = $newUser->roles->first()->name ?? 'user';
                    $grad = $avatarGradients[$newUser->id % count($avatarGradients)];
                @endphp
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <x-ui.user-avatar :user="$newUser" size="sm" :gradient="$grad" />
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;">{{ $newUser->name }}</div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $newUser->nim ?? $newUser->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <x-ui.role-badge :role="$role" size="xs" />
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);font-size:12px;color:var(--c-fg-sec);">
                        {{ $newUser->created_at->translatedFormat('F d, Y') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Belum ada user baru</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- ③ Log Aktivitas Terbaru (full width) --}}
    <div style="grid-column:1/-1;background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Log Aktivitas Terbaru</div>
            <a href="{{ route('superadmin.audit-logs') }}" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Lihat Semua
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:480px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:32%;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:18%;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:18%;">Log</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Value</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recent_logs->take(8) as $log)
                @php
                    $role         = $log->user?->roles->first()->name ?? 'system';
                    $uid          = $log->user?->id ?? 0;
                    $grad         = $avatarGradients[$uid % count($avatarGradients)];
                    $name         = $log->user?->name ?? 'System';
                    $actionVariant = match(strtoupper($log->action)) {
                        'CREATE' => 'action-create',
                        'UPDATE' => 'action-update',
                        'DELETE' => 'action-delete',
                        'LOGIN'  => 'action-login',
                        'LOGOUT' => 'action-logout',
                        default  => 'action-default',
                    };
                @endphp
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <x-ui.user-avatar :user="$log->user" size="sm" :gradient="$grad" />
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;">{{ $name }}</div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;">{{ $log->user?->nim ?? $uid }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <x-ui.role-badge :role="$role" size="xs" />
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <x-ui.badge :variant="$actionVariant" size="xs">{{ $log->action }}</x-ui.badge>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--c-fg-muted);">
                            <span style="width:5px;height:5px;border-radius:50%;background:var(--c-fg-placeholder);"></span>
                            {{ $log->created_at->diffForHumans(null, true) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Tidak ada log aktivitas</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

</div>

<style>
@media (max-width: 1024px) { .dash-activity { grid-template-columns: 1fr !important; } }
</style>