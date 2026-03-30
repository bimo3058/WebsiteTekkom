@php
    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
    $userRoles    = $user->roles; 
    $roleNames    = $userRoles->pluck('name')->map(fn($r) => strtolower($r))->toArray();
    
    $userPerms    = $user->directPermissions->pluck('name'); 
    $rolePerms    = $user->roles->flatMap->permissions->pluck('name')->unique();
    
    $hasAcademicRole = $userRoles->contains('is_academic', true);

    // ── KUNCI PERBAIKAN ──────────────────────────────────────────
    // Cek apakah user ini sudah PERNAH disimpan permissionnya.
    // Jika sudah ada record di user_permissions, gunakan DB sebagai sumber kebenaran.
    // Jika belum ada sama sekali (user baru), baru pakai default role.
    $hasExistingPermissions = $userPerms->isNotEmpty();
    // ─────────────────────────────────────────────────────────────

    $roleToModuleMap = [
        'admin_banksoal'      => ['banksoal'],
        'admin_capstone'      => ['capstone'],
        'admin_eoffice'       => ['eoffice'],
        'admin_kemahasiswaan' => ['kemahasiswaan'],
    ];

    $allowedModules = [];
    foreach ($roleNames as $name) {
        if ($hasAcademicRole) {
            $allowedModules = ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'];
            break;
        }
        if (isset($roleToModuleMap[$name])) {
            $allowedModules = array_merge($allowedModules, $roleToModuleMap[$name]);
        }
    }
    $allowedModules = array_unique($allowedModules);

    $shouldCheck = function(string $permName, string $action, string $module) use ($userPerms, $roleNames, $hasAcademicRole, $allowedModules, $hasExistingPermissions): bool {
        
        // ── JIKA SUDAH PERNAH DISIMPAN: pakai DB saja, tidak ada fallback ──
        if ($hasExistingPermissions) {
            return $userPerms->contains($permName);
        }

        // ── JIKA BELUM ADA RECORD (user baru): pakai default dari role ──
        if (!in_array(strtolower($module), $allowedModules)) return false;

        if (collect($roleNames)->contains(fn($r) => str_starts_with($r, 'admin_'))) {
            return in_array($action, ['view','index','read','edit','update']);
        }
        
        if ($hasAcademicRole) return true;

        return false;
    };
@endphp

<div class="user-card bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-blue-200 transition-all shadow-sm mb-3"
     data-user-id="{{ $user->id }}"
     data-name="{{ strtolower($user->name) }}"
     data-email="{{ strtolower($user->email) }}">

    {{-- Header --}}
    <button type="button" onclick="toggleCard({{ $user->id }})" class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50/80 transition-all text-left">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs border border-slate-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-800 font-semibold text-xs">{{ $user->name }}</span>
                    <span class="text-slate-400 text-[10px] font-medium">• {{ $user->email }}</span>
                </div>
                <div class="flex gap-1 mt-1 flex-wrap">
                    @foreach($user->roles as $role)
                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 text-[9px] font-bold border border-blue-100 uppercase">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <span class="material-symbols-outlined text-slate-300 transition-transform duration-200 card-chevron-{{ $user->id }}" style="font-size:20px">expand_more</span>
    </button>

    {{-- Body --}}
    <div id="card-body-{{ $user->id }}" class="hidden border-t border-slate-100 bg-slate-50/30 p-5">
        @if($isSuperadmin)
            <div class="flex items-center justify-center gap-2 py-4 text-slate-400 text-[11px] italic bg-white/50 rounded-lg border border-dashed border-slate-200">
                <span class="material-symbols-outlined" style="font-size:16px">verified_user</span> Superadmin bypasses all checks.
            </div>
        @else
            <form method="POST" action="{{ route('superadmin.users.update-permissions', $user->id) }}" id="perm-form-{{ $user->id }}">
                @csrf
                    <div class="mb-5">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-3 tracking-widest flex items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size:14px">group_add</span> Assign Roles
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($roles as $role)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                        class="peer sr-only role-checkbox"
                                        data-user="{{ $user->id }}"
                                        data-role-name="{{ strtolower($role->name) }}"
                                        data-is-academic="{{ $role->is_academic ? '1' : '0' }}">
                                    
                                    {{-- Desain Tombol Role --}}
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 transition-all duration-200 
                                                peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:shadow-[0_0_0_1px_rgba(37,99,235,1)]
                                                hover:border-slate-300 hover:bg-slate-50 shadow-sm">
                                        
                                        {{-- Status Indicator (Dot) --}}
                                        <div class="w-1.5 h-1.5 rounded-full {{ $user->roles->contains($role->id) ? 'bg-blue-600' : 'bg-slate-300' }} transition-colors peer-checked:bg-blue-600 group-hover:scale-110"></div>
                                        
                                        <span class="text-[11px] font-bold uppercase tracking-tight">{{ $role->name }}</span>

                                        {{-- Icon Check (Muncul saat aktif) --}}
                                        <span class="material-symbols-outlined hidden peer-checked:block text-blue-600" style="font-size:14px; font-variation-settings: 'FILL' 1">check_circle</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($permissions as $module => $perms)
                        @php
                            $moduleSlug = strtolower($module);
                            $isAllowed = in_array($moduleSlug, $allowedModules);
                            $potentialRoles = [];
                            foreach ($roleToModuleMap as $r => $m) { if(in_array($moduleSlug, $m)) $potentialRoles[] = $r; }
                            $potentialRoles = array_merge($potentialRoles, ['superadmin', 'dosen', 'mahasiswa', 'gpm']);
                        @endphp
                        <div class="module-box bg-white p-3 rounded-xl border transition-all duration-200 {{ $isAllowed ? 'border-slate-200 shadow-sm' : 'border-slate-100 opacity-40 grayscale pointer-events-none' }}"
                             data-module-slug="{{ $moduleSlug }}"
                             data-all-allowed-roles='@json($potentialRoles)'>
                            <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-slate-100">
                                <span class="text-[10px] font-black text-slate-700 uppercase flex items-center gap-1">
                                    {{ $module }} <span class="material-symbols-outlined lock-icon {{ $isAllowed ? 'hidden' : '' }}" style="font-size:12px">lock</span>
                                </span>
                                <label class="flex items-center gap-1 cursor-pointer group {{ !$isAllowed ? 'hidden' : '' }} select-all-container">
                                    <input type="checkbox" class="module-select-all w-3 h-3 rounded border-slate-300 text-blue-600" data-module-target="{{ $user->id }}-{{ $moduleSlug }}">
                                    <span class="text-[9px] text-slate-400 group-hover:text-slate-600">all</span>
                                </label>
                            </div>
                            <div class="space-y-1.5">
                                @foreach($perms as $permission)
                                    @php
                                        $action = explode('.', $permission->name)[1] ?? $permission->name;
                                        $isView = in_array($action, ['view','index','read']);
                                        $fromRole = $rolePerms->contains($permission->name);
                                        $isChecked = $shouldCheck($permission->name, $action, $module);
                                    @endphp
                                    <label class="flex items-center justify-between group cursor-pointer perm-label">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}
                                                   class="perm-checkbox w-3.5 h-3.5 rounded border-slate-300 text-blue-600 {{ $isView ? 'master-view-cb' : 'child-perm-cb' }}"
                                                   data-module-key="{{ $user->id }}-{{ $moduleSlug }}"
                                                   data-is-view="{{ $isView ? '1' : '0' }}" data-perm="{{ $permission->name }}">
                                            <span class="text-[11px] capitalize {{ $fromRole ? 'text-blue-600 font-bold' : 'text-slate-600' }}">{{ str_replace('_', ' ', $action) }}</span>
                                        </div>
                                        @if($fromRole)<div class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></div>@endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-[11px] font-bold px-5 py-2 rounded-lg transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:15px">save</span> Simpan Akses
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>