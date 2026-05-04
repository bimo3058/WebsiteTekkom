{{-- resources/views/superadmin/permission/_user_card.blade.php --}}
@php
    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
    $userRoles    = $user->roles;
    $roleNames    = $userRoles->pluck('name')->map(fn($r) => strtolower($r))->toArray();

    $userPerms           = $user->directPermissions->pluck('name');
    $rolePerms           = $user->roles->flatMap->permissions->pluck('name')->unique();
    $hasAcademicRole     = $userRoles->contains('is_academic', true);
    $hasNoRole           = $userRoles->isEmpty();
    $hasExistingPermissions = $userPerms->isNotEmpty();

    $permissionCount = $userPerms->count();
    $modules = [];
    foreach ($userPerms as $perm) {
        $mod = explode('.', $perm)[0];
        if (!in_array($mod, $modules)) $modules[] = $mod;
    }
    $moduleCount = count($modules);

    $allModules    = ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'];
    $hasFullAccess = $moduleCount === 4 && $permissionCount === 12;

    $adminModules = [];
    foreach ($roleNames as $role) {
        if (str_starts_with($role, 'admin_')) {
            $moduleMap = [
                'admin_banksoal'      => 'banksoal',
                'admin_capstone'      => 'capstone',
                'admin_eoffice'       => 'eoffice',
                'admin_kemahasiswaan' => 'kemahasiswaan',
            ];
            if (isset($moduleMap[$role])) $adminModules[] = $moduleMap[$role];
        }
    }

    $roleToModuleMap = [
        'admin_banksoal'      => ['banksoal'],
        'admin_capstone'      => ['capstone'],
        'admin_eoffice'       => ['eoffice'],
        'admin_kemahasiswaan' => ['kemahasiswaan'],
    ];
    $allowedModules = [];
    foreach ($roleNames as $name) {
        if ($hasAcademicRole) { $allowedModules = $allModules; break; }
        if (isset($roleToModuleMap[$name])) $allowedModules = array_merge($allowedModules, $roleToModuleMap[$name]);
    }
    $allowedModules = array_unique($allowedModules);

    $shouldCheck = function(string $permName, string $action, string $module)
        use ($userPerms, $roleNames, $hasAcademicRole, $allowedModules, $hasExistingPermissions): bool {
        if ($hasExistingPermissions) return $userPerms->contains($permName);
        if (!in_array(strtolower($module), $allowedModules)) return false;
        if (collect($roleNames)->contains(fn($r) => str_starts_with($r, 'admin_')))
            return in_array($action, ['view','index','read','edit','update']);
        if ($hasAcademicRole) return true;
        return false;
    };

    // ── Role accent — subtle left border indicator ──────────────────────────
    // Navy left stripe = superadmin, amber = no role, default = transparent
    $accentColor = match(true) {
        $isSuperadmin => 'var(--c-primary)',
        $hasNoRole    => 'var(--c-error)',
        in_array('dosen', $roleNames)               => '#287F6E',
        in_array('mahasiswa', $roleNames)            => '#956321',
        in_array('gpm', $roleNames)                  => '#0C4D6E',
        in_array('pengurus_himpunan', $roleNames)    => '#7B3FA0',
        in_array('alumni', $roleNames)               => '#5C6B73',
        default => 'var(--c-border)',
    };

    // ── Avatar bg/color per role ────────────────────────────────────────────
    [$avBg, $avColor] = match(true) {
        $isSuperadmin => ['rgba(11,38,110,0.08)', 'var(--c-primary)'],
        $hasNoRole    => ['var(--c-error-subtle)', 'var(--c-error)'],
        in_array('dosen', $roleNames)               => ['#DDF2EE', '#287F6E'],
        in_array('mahasiswa', $roleNames)            => ['#F9ECCB', '#956321'],
        in_array('gpm', $roleNames)                  => ['#D1F0F9', '#0C4D6E'],
        in_array('pengurus_himpunan', $roleNames)    => ['#F3E8FA', '#7B3FA0'],
        in_array('alumni', $roleNames)               => ['#EAECEE', '#5C6B73'],
        default => ['var(--c-bg)', 'var(--c-fg-muted)'],
    };

    // ── Role pill style ─────────────────────────────────────────────────────
    $roleStyle = function(string $roleName) {
        return match(true) {
            $roleName === 'superadmin'               => 'background:rgba(11,38,110,0.08); color:var(--c-primary); border:1px solid var(--c-primary-border);',
            $roleName === 'dosen'                    => 'background:#DDF2EE; color:#287F6E; border:1px solid #9DE0D3;',
            $roleName === 'mahasiswa'                => 'background:#F9ECCB; color:#956321; border:1px solid #FBD982;',
            $roleName === 'gpm'                      => 'background:#D1F0F9; color:#0C4D6E; border:1px solid #7EDCF1;',
            $roleName === 'pengurus_himpunan'        => 'background:#F3E8FA; color:#7B3FA0; border:1px solid #D4A8E8;',
            $roleName === 'alumni'                   => 'background:#EAECEE; color:#5C6B73; border:1px solid #C2C8CC;',
            str_starts_with($roleName, 'admin_')    => 'background:#E8EDF7; color:var(--c-primary); border:1px solid #C2CEEA;',
            default                                  => 'background:var(--c-bg); color:var(--c-fg-muted); border:1px solid var(--c-border);',
        };
    };

    // ── Initials ────────────────────────────────────────────────────────────
    $initials = strtoupper(substr($user->name, 0, 1));
    $sp = strpos($user->name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($user->name, $sp + 1, 1));

    // ── Unique card key per user per category (fix: user muncul di 2 kategori) ──
    $cardKey = $user->id . '-' . ($categoryKey ?? 'default');
@endphp

{{-- Card --}}
<div class="user-card"
     data-user-id="{{ $user->id }}"
     data-card-key="{{ $cardKey }}"
     data-name="{{ strtolower($user->name) }}"
     data-email="{{ strtolower($user->email) }}"
     style="
         background: #fff;
         border: 1px solid var(--c-border);
         border-left: 3px solid {{ $accentColor }};
         border-radius: 12px;
         overflow: hidden;
         margin-bottom: 8px;
         box-shadow: 0px 1px 2px rgba(228,229,231,0.24);
         transition: box-shadow .15s, border-color .15s;
     "
     onmouseover="this.style.boxShadow='0 4px 12px rgba(11,38,110,0.07)'"
     onmouseout="this.style.boxShadow='0px 1px 2px rgba(228,229,231,0.24)'">

    {{-- ── Header / Toggle row ─────────────────────────────────────────── --}}
    <button type="button" onclick="toggleCard('{{ $cardKey }}')"
            style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:transparent; border:none; cursor:pointer; text-align:left; transition:background .12s;"
            onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='transparent'">

        <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">

            {{-- Avatar --}}
            <div style="width:36px; height:36px; border-radius:50%; background:{{ $avBg }}; color:{{ $avColor }}; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:1px solid rgba(0,0,0,0.06); font-size:11px; font-weight:700;">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;">
                @elseif($isSuperadmin)
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
                    </svg>
                @elseif($hasNoRole)
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    </svg>
                @else
                    {{ $initials }}
                @endif
            </div>

            {{-- Info --}}
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                    <span style="font-size:13px; font-weight:600; color:var(--c-fg); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:220px;">
                        {{ $user->name }}
                    </span>
                    <span style="font-size:11px; color:var(--c-fg-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $user->email }}
                    </span>
                </div>

                {{-- Badges row --}}
                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    {{-- Role pills --}}
                    @forelse($user->roles as $role)
                        <span style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:9999px; {{ $roleStyle(strtolower($role->name)) }}">
                            {{ str_replace('_', ' ', $role->name) }}
                        </span>
                    @empty
                        <span style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:9999px; background:var(--c-error-subtle); color:var(--c-error); border:1px solid #ED8296;">
                            No Role
                        </span>
                    @endforelse

                    {{-- Permission summary pill --}}
                    @if($isSuperadmin)
                        <span style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:9999px; background:var(--c-primary); color:#fff;">
                            Root Access
                        </span>
                    @elseif($hasFullAccess)
                        <span style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:9999px; background:#DDF2EE; color:#287F6E; border:1px solid #9DE0D3;">
                            Full Access
                        </span>
                    @elseif($permissionCount > 0)
                        <span style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; padding:2px 8px; border-radius:9999px; background:var(--c-bg); color:var(--c-fg-muted); border:1px solid var(--c-border);">
                            @if(!empty($adminModules))
                                {{ implode(', ', $adminModules) }} · {{ $permissionCount }}p
                            @elseif($moduleCount <= 2)
                                {{ implode(', ', $modules) }} · {{ $permissionCount }}p
                            @else
                                {{ $moduleCount }} modules · {{ $permissionCount }}p
                            @endif
                        </span>
                    @else
                        <span style="font-size:9px; font-weight:500; padding:2px 8px; border-radius:9999px; background:var(--c-bg); color:var(--c-fg-placeholder); border:1px solid var(--c-border);">
                            No permissions
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chevron --}}
        <svg class="card-chevron-{{ $cardKey }}"
             width="16" height="16" fill="none" stroke="var(--c-fg-muted)" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"
             style="flex-shrink:0; margin-left:8px; transition:transform .25s ease;">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    {{-- ── Body / Permission editor ────────────────────────────────────── --}}
    <div id="card-body-{{ $cardKey }}" class="hidden"
         style="border-top:1px solid var(--c-border); background:var(--c-bg); padding:24px;">

        @if($isSuperadmin)
            {{-- Superadmin: read-only notice --}}
            <div style="display:flex; align-items:center; justify-content:center; gap:10px; padding:20px; background:#fff; border:1px dashed var(--c-primary-border); border-radius:12px; color:var(--c-primary);">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
                </svg>
                <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em;">Full System Privilege — Cannot be modified</span>
            </div>
        @else
            <form method="POST" action="{{ route('superadmin.users.update-permissions', $user->id) }}" id="perm-form-{{ $user->id }}">
                @csrf

                {{-- ── Assign Roles ────────────────────────────────────── --}}
                <div style="margin-bottom:24px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <svg width="14" height="14" fill="none" stroke="var(--c-fg-muted)" viewBox="0 0 24 24" stroke-width="1.8">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M17 21v-2a4 4 0 0 0-3-3.87M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75M9 7a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                        </svg>
                        <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:var(--c-fg-muted);">Assign Roles</span>
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @foreach($roles as $role)
                            @php $isActive = $user->roles->contains($role->id); @endphp
                            <label style="position:relative; cursor:pointer;">
                                <input type="checkbox"
                                       name="roles[]"
                                       value="{{ $role->id }}"
                                       {{ $isActive ? 'checked' : '' }}
                                       class="peer sr-only role-checkbox"
                                       data-role-name="{{ strtolower($role->name) }}"
                                       data-is-academic="{{ $role->is_academic ? '1' : '0' }}">
                                <div class="dot-indicator-wrap"
                                     style="display:flex; align-items:center; gap:8px; padding:6px 14px; border-radius:9999px; border:1px solid var(--c-border); background:#fff; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; color:var(--c-fg-muted); transition:all .15s; cursor:pointer; {{ $isActive ? 'border-color:var(--c-primary); background:rgba(11,38,110,0.06); color:var(--c-primary);' : '' }}">
                                    <div class="dot-indicator" style="width:6px; height:6px; border-radius:50%; transition:background .15s; background:{{ $isActive ? 'var(--c-primary)' : 'var(--c-border)' }};"></div>
                                    {{ str_replace('_', ' ', $role->name) }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ── Module Permissions ──────────────────────────────── --}}
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:24px;">
                    @foreach($permissions as $module => $perms)
                        @php
                            $moduleSlug   = strtolower($module);
                            $isAllowed    = in_array($moduleSlug, $allowedModules);
                            $potentialRoles = [];
                            foreach ($roleToModuleMap as $r => $m) { if(in_array($moduleSlug, $m)) $potentialRoles[] = $r; }
                            $potentialRoles = array_merge($potentialRoles, ['superadmin', 'dosen', 'mahasiswa', 'gpm']);

                            // Module accent color
                            $modColor = match($moduleSlug) {
                                'banksoal'      => ['bg' => '#F9ECCB', 'color' => '#956321'],
                                'capstone'      => ['bg' => '#D1F0F9', 'color' => '#0C4D6E'],
                                'eoffice'       => ['bg' => 'rgba(11,38,110,0.08)', 'color' => 'var(--c-primary)'],
                                'kemahasiswaan' => ['bg' => '#DDF2EE', 'color' => '#287F6E'],
                                default         => ['bg' => 'var(--c-bg)', 'color' => 'var(--c-fg-muted)'],
                            };
                        @endphp
                        <div class="module-box"
                             data-module-slug="{{ $moduleSlug }}"
                             data-all-allowed-roles='@json($potentialRoles)'
                             style="background:#fff; border:1px solid var(--c-border); border-radius:10px; padding:14px; transition:opacity .2s; {{ !$isAllowed ? 'opacity:0.35; filter:grayscale(1); pointer-events:none;' : '' }}">

                            {{-- Module header --}}
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid var(--c-border);">
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <div style="width:22px; height:22px; border-radius:6px; background:{{ $modColor['bg'] }}; display:flex; align-items:center; justify-content:center;">
                                        <svg width="11" height="11" fill="none" stroke="{{ $modColor['color'] }}" viewBox="0 0 24 24" stroke-width="2">
                                            <path d="M4 4h16v16H4z M9 9h6v6H9z" style="display:none"/>
                                            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                        </svg>
                                    </div>
                                    <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--c-fg);">
                                        {{ $module }}
                                    </span>
                                    @if(!$isAllowed)
                                        <svg width="11" height="11" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    @endif
                                </div>
                                @if($isAllowed)
                                <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                    <input type="checkbox" class="module-select-all"
                                           data-module-target="{{ $user->id }}-{{ $moduleSlug }}"
                                           style="width:13px; height:13px; accent-color:var(--c-primary); cursor:pointer;">
                                    <span style="font-size:10px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.04em;">All</span>
                                </label>
                                @endif
                            </div>

                            {{-- Permission rows --}}
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                @foreach($perms as $permission)
                                    @php
                                        $action = explode('.', $permission->name)[1] ?? $permission->name;
                                        if (!in_array($action, ['view','edit','delete'])) continue;
                                        $isView    = ($action === 'view');
                                        $fromRole  = $rolePerms->contains($permission->name);
                                        $isChecked = $shouldCheck($permission->name, $action, $module);

                                        $actionIcon = match($action) {
                                            'view'   => 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                                            'edit'   => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                            'delete' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                                            default  => 'M9 12l2 2 4-4',
                                        };
                                    @endphp
                                    <label style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding:4px 0; gap:8px;"
                                           onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->name }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   class="perm-checkbox {{ $isView ? 'master-view-cb' : 'child-perm-cb' }}"
                                                   data-module-key="{{ $user->id }}-{{ $moduleSlug }}"
                                                   data-is-view="{{ $isView ? '1' : '0' }}"
                                                   data-perm="{{ $permission->name }}"
                                                   style="width:14px; height:14px; accent-color:var(--c-primary); cursor:pointer; flex-shrink:0;">
                                            <div style="display:flex; align-items:center; gap:5px;">
                                                <svg width="12" height="12" fill="none" stroke="{{ $isChecked ? 'var(--c-fg-sec)' : 'var(--c-fg-placeholder)' }}" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="{{ $actionIcon }}"/>
                                                </svg>
                                                <span style="font-size:12px; font-weight:500; color:{{ $isChecked ? 'var(--c-fg-sec)' : 'var(--c-fg-muted)' }}; text-transform:capitalize;">
                                                    {{ str_replace('_', ' ', $action) }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($fromRole)
                                            <div title="Inherited from role"
                                                 style="width:5px; height:5px; border-radius:50%; background:var(--c-primary-border); flex-shrink:0;"></div>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── Save button ──────────────────────────────────────── --}}
                <div style="padding-top:16px; border-top:1px solid var(--c-border); display:flex; justify-content:flex-end;">
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:7px; padding:9px 20px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; cursor:pointer; font-family:inherit; transition:background .15s, transform .1s;"
                            onmouseover="this.style.background='var(--c-primary-hover)'"
                            onmouseout="this.style.background='var(--c-primary)'"
                            onmousedown="this.style.transform='translateY(1px)'"
                            onmouseup="this.style.transform='none'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z M17 21v-8H7v8 M7 3v5h8"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<style>
/* Peer-checked role pill via JS (karena peer tidak bisa ke nested child) */
.peer:checked ~ .dot-indicator-wrap {
    border-color: var(--c-primary) !important;
    background: rgba(11,38,110,0.06) !important;
    color: var(--c-primary) !important;
}
.peer:checked ~ .dot-indicator-wrap .dot-indicator {
    background: var(--c-primary) !important;
}
@media (min-width: 1024px) {
    .module-grid-4 { grid-template-columns: repeat(4, 1fr) !important; }
}
</style>