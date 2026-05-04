{{-- resources/views/superadmin/audit-logs/_table.blade.php --}}
<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0px 1px 2px rgba(228,229,231,0.24);">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                    <th style="padding:12px 16px; width:40px;">
                        <input type="checkbox" id="selectAllLogs"
                               style="width:14px; height:14px; accent-color:var(--c-primary); cursor:pointer;">
                    </th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted); white-space:nowrap;">Timestamp</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted); white-space:nowrap;">User</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted); white-space:nowrap;">Modul</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted); white-space:nowrap;">Aksi</th>
                    <th style="padding:12px 16px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted);">Deskripsi</th>
                    <th style="padding:12px 16px; text-align:center; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    // Module badge token
                    [$modBg, $modColor] = match($log->module ?? '') {
                        'auth'             => ['#DDF2EE', '#287F6E'],
                        'bank_soal'        => ['#F9ECCB', '#956321'],
                        'capstone'         => ['#D1F0F9', '#0C4D6E'],
                        'eoffice'          => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
                        'user_management'  => ['#E8EDF7', 'var(--c-primary)'],
                        'manajemen_mahasiswa' => ['#DDF2EE', '#287F6E'],
                        default            => ['var(--c-bg)', 'var(--c-fg-muted)'],
                    };

                    // Action badge token
                    [$actBg, $actColor] = match(strtolower($log->action ?? '')) {
                        'create' => ['#DDF2EE', '#287F6E'],
                        'update' => ['#F9ECCB', '#956321'],
                        'delete' => ['var(--c-error-subtle)', 'var(--c-error)'],
                        'login'  => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
                        'logout' => ['var(--c-bg)', 'var(--c-fg-muted)'],
                        'view'   => ['#D1F0F9', '#0C4D6E'],
                        default  => ['var(--c-bg)', 'var(--c-fg-muted)'],
                    };

                    // Avatar
                    $user = $log->user ?? null;
                    $isSA = $user?->hasRole('superadmin');
                    $initials = $user ? strtoupper(substr($user->name, 0, 1)) : 'S';
                    $sp = $user ? strpos($user->name, ' ') : false;
                    if ($sp !== false) $initials .= strtoupper(substr($user->name, $sp+1, 1));

                    [$avBg, $avColor] = match(true) {
                        $user === null => ['var(--c-bg)', 'var(--c-fg-muted)'],
                        $isSA          => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
                        default        => ['var(--c-bg)', 'var(--c-fg-muted)'],
                    };
                @endphp
                <tr style="border-bottom:1px solid #F6F8FA; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFC'" onmouseout="this.style.background='transparent'">

                    {{-- Checkbox --}}
                    <td style="padding:12px 16px;">
                        <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}"
                               class="log-checkbox"
                               style="width:14px; height:14px; accent-color:var(--c-primary); cursor:pointer;">
                    </td>

                    {{-- Timestamp --}}
                    <td style="padding:12px 16px; white-space:nowrap;">
                        <span style="font-size:12px; font-weight:600; color:var(--c-fg); display:block;">{{ $log->created_at->format('d M Y') }}</span>
                        <span style="font-size:10px; color:var(--c-fg-muted);">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>

                    {{-- User --}}
                    <td style="padding:12px 16px;">
                        <div style="display:flex; align-items:center; gap:10px; min-width:160px;">
                            <div style="position:relative; flex-shrink:0;">
                                <div style="width:30px; height:30px; border-radius:50%; background:{{ $avBg }}; color:{{ $avColor }}; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:10px; font-weight:700; border:1px solid rgba(0,0,0,0.06);">
                                    @if($user?->avatar_url)
                                        <img src="{{ $user->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @elseif($isSA)
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                    @elseif($user)
                                        {{ $initials }}
                                    @else
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 4h.01M8 4a4 4 0 0 1 8 0v1H8V4zM6 9h12l-1 11H7L6 9z"/></svg>
                                    @endif
                                </div>
                                @if($user?->is_online)
                                    <span style="position:absolute; bottom:-1px; right:-1px; width:8px; height:8px; border-radius:50%; background:var(--c-success); border:1.5px solid #fff;"></span>
                                @endif
                            </div>
                            <div style="min-width:0;">
                                <p style="font-size:12px; font-weight:600; color:var(--c-fg); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:140px;">
                                    {{ $user?->name ?? 'System' }}
                                </p>
                                <p style="font-size:10px; color:var(--c-fg-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:140px;">
                                    {{ $user?->email ?? 'Automated Task' }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Module --}}
                    <td style="padding:12px 16px;">
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:3px 8px; border-radius:9999px; background:{{ $modBg }}; color:{{ $modColor }}; white-space:nowrap;">
                            {{ str_replace('_', ' ', $log->module ?? 'N/A') }}
                        </span>
                    </td>

                    {{-- Action --}}
                    <td style="padding:12px 16px;">
                        <span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:3px 8px; border-radius:9999px; background:{{ $actBg }}; color:{{ $actColor }}; white-space:nowrap;">
                            {{ $log->action ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- Description --}}
                    <td style="padding:12px 16px;">
                        <p style="font-size:12px; font-weight:500; color:var(--c-fg-sec); max-width:300px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5;"
                           title="{{ $log->description ?? '-' }}">
                            {{ $log->description ?? '-' }}
                        </p>
                    </td>

                    {{-- Status --}}
                    <td style="padding:12px 16px; text-align:center;">
                        @if($user)
                            @if($user->is_online)
                                <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:9999px; background:var(--c-success-subtle); color:#287F6E; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap;">
                                    <span style="width:5px; height:5px; border-radius:50%; background:#40C4AA; animation:pulse 2s infinite;"></span>
                                    Online
                                </span>
                            @else
                                <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:9999px; background:var(--c-bg); color:var(--c-fg-muted); font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; border:1px solid var(--c-border); white-space:nowrap;">
                                    Offline
                                </span>
                            @endif
                        @else
                            <span style="font-size:9px; font-weight:700; color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:0.06em;">System</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:56px 24px; text-align:center;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                            <svg width="40" height="40" fill="none" stroke="var(--c-border)" viewBox="0 0 24 24" stroke-width="1.5">
                                <path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z" stroke-linecap="round"/>
                            </svg>
                            <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--c-fg-placeholder);">Tidak ada aktivitas tercatat</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@keyframes pulse { 0%, 100% { opacity:1; } 50% { opacity:.5; } }
</style>