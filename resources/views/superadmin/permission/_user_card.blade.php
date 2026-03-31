@php
    $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
    $userRoles    = $user->roles; 
    $roleNames    = $userRoles->pluck('name')->map(fn($r) => strtolower($r))->toArray();
    
    $userPerms    = $user->directPermissions->pluck('name'); 
    $rolePerms    = $user->roles->flatMap->permissions->pluck('name')->unique();
    
    $hasAcademicRole = $userRoles->contains('is_academic', true);
    $hasNoRole = $userRoles->isEmpty();
    $hasExistingPermissions = $userPerms->isNotEmpty();

    // ============================================
    // HITUNG PERMISSION YANG SEBENARNYA DIMILIKI
    // ============================================
    $permissionCount = $userPerms->count();
    
    // Hitung module yang sebenarnya diakses (berdasarkan permission yang ada)
    $modules = [];
    foreach ($userPerms as $perm) {
        $module = explode('.', $perm)[0];
        if (!in_array($module, $modules)) $modules[] = $module;
    }
    $moduleCount = count($modules);
    
    // Cek apakah user memiliki FULL ACCESS ke semua module (4 module: banksoal, capstone, eoffice, kemahasiswaan)
    $allModules = ['banksoal', 'capstone', 'eoffice', 'kemahasiswaan'];
    $hasFullAccess = $moduleCount === 4 && $permissionCount === 12; // 4 module x 3 action = 12 permission
    
    // Cek apakah user memiliki akses ke module tertentu (untuk admin per module)
    $adminModules = [];
    foreach ($roleNames as $role) {
        if (str_starts_with($role, 'admin_')) {
            $moduleMap = [
                'admin_banksoal' => 'banksoal',
                'admin_capstone' => 'capstone',
                'admin_eoffice' => 'eoffice',
                'admin_kemahasiswaan' => 'kemahasiswaan',
            ];
            if (isset($moduleMap[$role])) {
                $adminModules[] = $moduleMap[$role];
            }
        }
    }

    $cardStyle = match(true) {
        $hasNoRole => 'border-l-4 border-l-rose-500 bg-rose-50/10',
        $isSuperadmin => 'border-l-4 border-l-purple-600 bg-purple-50/20',
        default => 'border-l border-l-slate-200',
    };

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

    $shouldCheck = function(string $permName, string $action, string $module) 
        use ($userPerms, $roleNames, $hasAcademicRole, $allowedModules, $hasExistingPermissions): bool {

        if ($hasExistingPermissions) {
            return $userPerms->contains($permName);
        }

        if (!in_array(strtolower($module), $allowedModules)) return false;

        if (collect($roleNames)->contains(fn($r) => str_starts_with($r, 'admin_'))) {
            return in_array($action, ['view','index','read','edit','update']);
        }

        if ($hasAcademicRole) return true;

        return false;
    };
@endphp

<div class="user-card bg-white border border-slate-200 rounded-lg overflow-hidden transition-all shadow-sm mb-2 group/card {{ $cardStyle }}"
     data-user-id="{{ $user->id }}"
     data-name="{{ strtolower($user->name) }}"
     data-email="{{ strtolower($user->email) }}">

    {{-- Header --}}
    <button type="button" onclick="toggleCard({{ $user->id }})" 
        class="w-full flex items-center justify-between px-3 py-3 hover:bg-slate-50/80 transition-all text-left">

        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- Avatar Section - Resized to 8x8 --}}
            @php
                $avatarStyle = match(true) {
                    $isSuperadmin => 'bg-purple-600 text-white border-purple-700 shadow-sm',
                    $hasNoRole => 'bg-rose-100 text-rose-600 border-rose-200',
                    default => 'bg-slate-100 text-slate-500 border-slate-200',
                };
            @endphp

            <div class="w-8 h-8 rounded-md flex items-center justify-center border {{ $avatarStyle }} flex-shrink-0">
                @if($isSuperadmin)
                    <span class="material-symbols-outlined" style="font-size: 16px; font-variation-settings: 'FILL' 1;">
                        admin_panel_settings
                    </span>
                @elseif($hasNoRole)
                    <span class="material-symbols-outlined" style="font-size: 16px;">priority_high</span>
                @else
                    <span class="text-[10px] font-black uppercase">{{ substr($user->name, 0, 1) }}</span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                {{-- Name & Email Line --}}
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="font-bold text-xs tracking-tight {{ $hasNoRole ? 'text-rose-700' : ($isSuperadmin ? 'text-purple-900' : 'text-slate-800') }}">
                        {{ $user->name }}
                    </span>
                    @if($isSuperadmin)
                        <span class="material-symbols-outlined text-purple-600" style="font-size:13px; font-variation-settings: 'FILL' 1">verified</span>
                    @endif
                    <span class="text-slate-400 text-[10px] font-medium truncate">| {{ $user->email }}</span>
                </div>

                {{-- Badges Row --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    {{-- Role Badges - Smaller Padding --}}
                    <div class="flex gap-1">
                        @forelse($user->roles as $role)
                            @php
                                $roleColors = match(strtolower($role->name)) {
                                    'superadmin' => 'bg-purple-600 text-white border-purple-700',
                                    'dosen' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'mahasiswa' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'admin_banksoal' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                                    'admin_capstone' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                                    'admin_eoffice' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-black border uppercase tracking-tighter {{ $roleColors }}">
                                {{ $role->name }}
                            </span>
                        @empty
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold border uppercase bg-rose-50 text-rose-500 border-rose-100 italic">
                                No Role
                            </span>
                        @endforelse
                    </div>

                    {{-- Permission Badge - Compact Split Style --}}
                    <div class="flex items-center">
                        @if($isSuperadmin)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 text-[9px] font-black border border-purple-100 uppercase tracking-tighter">
                                Root Access
                            </span>
                        @elseif($hasFullAccess)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-black border border-emerald-100 uppercase tracking-tighter">
                                Full Access
                            </span>
                        @elseif($permissionCount > 0)
                            <div class="inline-flex items-center border border-blue-200 rounded overflow-hidden shadow-sm">
                                <div class="px-1.5 py-0.5 bg-blue-600 text-white text-[9px] font-black uppercase tracking-tighter">
                                    @if(!empty($adminModules))
                                        {{ implode(', ', $adminModules) }}
                                    @elseif($moduleCount <= 2)
                                        {{ implode(', ', $modules) }}
                                    @else
                                        {{ $moduleCount }} MOD
                                    @endif
                                </div>
                                <div class="px-1.5 py-0.5 bg-white text-blue-700 text-[9px] font-black tracking-tighter">
                                    {{ $permissionCount }}P
                                </div>
                            </div>
                        @else
                            <span class="text-slate-300 text-[9px] font-bold uppercase tracking-tighter">No Perms</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center ml-2">
            <span class="material-symbols-outlined text-slate-300 transition-transform duration-300 card-chevron-{{ $user->id }}" style="font-size: 18px">
                expand_more
            </span>
        </div>
    </button>

    {{-- Body - Detail Permissions --}}
    <div id="card-body-{{ $user->id }}" class="hidden border-t border-slate-100 bg-slate-50/30 p-5">
        @if($isSuperadmin)
            <div class="flex items-center justify-center gap-2 py-4 text-purple-600 text-[10px] font-black uppercase tracking-widest bg-white rounded-lg border border-dashed border-purple-200 shadow-sm">
                <span class="material-symbols-outlined" style="font-size:16px">verified_user</span> 
                Full System Privilege Granted
            </div>
        @else
            <form method="POST" action="{{ route('superadmin.users.update-permissions', $user->id) }}" id="perm-form-{{ $user->id }}">
                @csrf
                <div class="mb-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-3 tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:14px">group_add</span> Assign Roles
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($roles as $role)
                            @php $isActive = $user->roles->contains($role->id); @endphp
                            <label class="relative cursor-pointer group/role">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ $isActive ? 'checked' : '' }}
                                    class="peer sr-only role-checkbox"
                                    data-role-name="{{ strtolower($role->name) }}"
                                    data-is-academic="{{ $role->is_academic ? '1' : '0' }}">
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-500 transition-all duration-200 
                                            peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:shadow-[0_0_0_1px_rgba(37,99,235,1)]
                                            hover:border-slate-300 shadow-sm">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-blue-600' : 'bg-slate-300' }} transition-colors peer-checked:bg-blue-600"></div>
                                    <span class="text-[10px] font-black uppercase tracking-tight">{{ $role->name }}</span>
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
                        <div class="module-box bg-white p-3 rounded-lg border transition-all duration-200 {{ $isAllowed ? 'border-slate-200 shadow-sm' : 'border-slate-100 opacity-40 grayscale pointer-events-none' }}"
                             data-module-slug="{{ $moduleSlug }}"
                             data-all-allowed-roles='@json($potentialRoles)'>
                            
                            <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-slate-100">
                                <span class="text-[10px] font-black text-slate-700 uppercase flex items-center gap-1.5">
                                    {{ $module }} 
                                    @if(!$isAllowed) <span class="material-symbols-outlined text-slate-300" style="font-size:12px">lock</span> @endif
                                </span>
                                <label class="flex items-center gap-1 cursor-pointer group {{ !$isAllowed ? 'hidden' : '' }} select-all-container">
                                    <input type="checkbox" class="module-select-all w-3 h-3 rounded border-slate-300 text-blue-600 focus:ring-0" data-module-target="{{ $user->id }}-{{ $moduleSlug }}">
                                    <span class="text-[9px] font-black text-slate-400 uppercase group-hover:text-blue-600">All</span>
                                </label>
                            </div>

                            <div class="space-y-1.5">
                                @foreach($perms as $permission)
                                    @php
                                        $action = explode('.', $permission->name)[1] ?? $permission->name;
                                        $allowedActions = ['view', 'edit', 'delete'];
                                        
                                        if (!in_array($action, $allowedActions)) {
                                            continue;
                                        }
                                        
                                        $isView = ($action === 'view');
                                        $fromRole = $rolePerms->contains($permission->name);
                                        $isChecked = $shouldCheck($permission->name, $action, $module);
                                    @endphp
                                    <label class="flex items-center justify-between group cursor-pointer">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}
                                                class="perm-checkbox w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-0 {{ $isView ? 'master-view-cb' : 'child-perm-cb' }}"
                                                data-module-key="{{ $user->id }}-{{ $moduleSlug }}"
                                                data-is-view="{{ $isView ? '1' : '0' }}" data-perm="{{ $permission->name }}">
                                            <span class="text-[10px] font-bold capitalize {{ $fromRole ? 'text-blue-600' : 'text-slate-500' }}">
                                                {{ str_replace('_', ' ', $action) }}
                                            </span>
                                        </div>
                                        @if($fromRole) <div class="w-1 h-1 rounded-full bg-blue-400"></div> @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest px-6 py-2 rounded-lg transition-all shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px">save</span> Save Permissions
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>