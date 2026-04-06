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
            {{-- Avatar Section - Dikembalikan ke style asli dengan pembungkus div --}}
            @php
                // Logika warna background, text, dan border asli dari desainmu
                $avatarWrapperStyle = match(true) {
                    $isSuperadmin => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF] shadow-sm',
                    $hasNoRole    => 'bg-[#FEF2F2] text-[#EF4444] border-[#FEE2E2]',
                    default       => 'bg-[#F8F9FA] text-[#6C757D] border-[#DEE2E6]',
                };
            @endphp

            {{-- Div Pembungkus Asli (Mengembalikan Bulatan dan Border) --}}
            <div class="w-10 h-10 rounded-full flex items-center justify-center border {{ $avatarWrapperStyle }} flex-shrink-0 overflow-hidden">
                
                @include('components.ui.avatar', [
                    // Ambil URL foto dari user
                    'src' => $user->avatar_url, 
                    
                    // Logika Fallback Asli: Jika Superadmin tampilkan icon shield, jika tidak tampilkan inisial
                    'fallback' => $isSuperadmin 
                        ? '<span class="material-symbols-outlined !text-[16px] fill-1">admin_panel_settings</span>' 
                        : ($hasNoRole 
                            ? '<span class="material-symbols-outlined !text-[16px]">priority_high</span>'
                            : '<span class="text-[10px] font-semibold uppercase">'.substr($user->name, 0, 1).'</span>'),
                        
                    // Gunakan size 'sm' di komponen (size-9) agar pas di dalam pembungkus w-8
                    'size' => 'sm', 
                    
                    // Kosongkan class komponen agar tidak bentrok dengan pembungkus
                    'class' => '' 
                ])
            </div>

            <div class="flex-1 min-w-0">
                {{-- Name & Email Line --}}
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="font-semibold text-xs tracking-tight {{ $hasNoRole ? 'text-rose-700' : ($isSuperadmin ? 'text-purple-900' : 'text-slate-800') }}">
                        {{ $user->name }}
                    </span>
                    @if($isSuperadmin)
                        <span class="material-symbols-outlined text-purple-600" style="font-size:13px; font-variation-settings: 'FILL' 1">verified</span>
                    @endif
                    <span class="text-slate-400 text-[10px] font-medium truncate">| {{ $user->email }}</span>
                </div>

                {{-- Badges Row - Berdasarkan LuminHR Design System --}}
                <div class="flex items-center gap-2 flex-wrap mt-2">
                    {{-- Role Badges --}}
                    <div class="flex gap-1.5">
                        @forelse($user->roles as $role)
                            @php
                                $roleName = strtolower($role->name);
                                // Mapping warna berdasarkan Design System LuminHR
                                $roleStyle = match(true) {
                                    $roleName === 'superadmin'       => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]', // Primary Purple 50/100
                                    $roleName === 'dosen'            => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]', // Success/Emerald
                                    $roleName === 'mahasiswa'        => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]', // Warning/Amber
                                    $roleName === 'admin_eoffice'    => 'bg-[#EBF1FF] text-[#3377FF] border-[#B3CCFF]', // Additional/Sky
                                    str_starts_with($roleName, 'admin') => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]', // Primary Variant
                                    default                          => 'bg-[#F8F9FA] text-[#6C757D] border-[#DEE2E6]', // Greyscale
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border uppercase tracking-wide {{ $roleStyle }}">
                                {{ str_replace('_', ' ', $role->name) }}
                            </span>
                        @empty
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold border border-[#FEE2E2] bg-[#FEF2F2] text-[#EF4444] italic uppercase">
                                No Role
                            </span>
                        @endforelse
                    </div>

                    {{-- Permission Badge - LuminHR Split Style --}}
                    <div class="flex items-center">
                        @if($isSuperadmin)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#5E53F4] text-white text-[10px] font-semibold uppercase tracking-wider shadow-sm">
                                <span class="material-symbols-outlined text-[12px] fill-1">verified</span>
                                Root Access
                            </span>
                        @elseif($hasFullAccess)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#00C08D] text-white text-[10px] font-semibold uppercase tracking-wider">
                                Full Access
                            </span>
                        @elseif($permissionCount > 0)
                            <div class="inline-flex items-center border border-[#DEE2E6] rounded-full overflow-hidden bg-white shadow-sm">
                                <div class="px-2 py-0.5 bg-[#F8F9FA] text-[#495057] text-[9px] font-semibold uppercase border-r border-[#DEE2E6]">
                                    @if(!empty($adminModules))
                                        {{ implode(', ', $adminModules) }}
                                    @elseif($moduleCount <= 2)
                                        {{ implode(', ', $modules) }}
                                    @else
                                        {{ $moduleCount }} Modules
                                    @endif
                                </div>
                                <div class="px-2 py-0.5 text-[#5E53F4] text-[9px] font-semibold uppercase bg-white">
                                    {{ $permissionCount }} Perms
                                </div>
                            </div>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full border border-[#DEE2E6] text-[#ADB5BD] text-[9px] font-semibold uppercase tracking-tight bg-[#F8F9FA]">
                                Unassigned
                            </span>
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
    <div id="card-body-{{ $user->id }}" class="hidden border-t border-[#DEE2E6] bg-[#F8F9FA]/50 p-6">
        @if($isSuperadmin)
            <div class="flex items-center justify-center gap-3 py-6 text-[#5E53F4] text-[11px] font-semibold uppercase tracking-widest bg-white rounded-2xl border border-dashed border-[#D1BFFF] shadow-sm">
                <span class="material-symbols-outlined" style="font-size:20px; font-variation-settings: 'FILL' 1">verified_user</span> 
                Full System Privilege Granted
            </div>
        @else
            <form method="POST" action="{{ route('superadmin.users.update-permissions', $user->id) }}" id="perm-form-{{ $user->id }}">
                @csrf
                {{-- Section: Assign Roles --}}
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-[#6C757D]" style="font-size:18px">group_add</span>
                        <p class="text-[10px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Assign Roles</p>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($roles as $role)
                            @php $isActive = $user->roles->contains($role->id); @endphp
                            <label class="relative cursor-pointer group/role">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ $isActive ? 'checked' : '' }}
                                    class="peer sr-only role-checkbox"
                                    data-role-name="{{ strtolower($role->name) }}"
                                    data-is-academic="{{ $role->is_academic ? '1' : '0' }}">
                                <div class="flex items-center gap-2.5 px-4 py-2 rounded-full border border-[#DEE2E6] bg-white text-[#6C757D] transition-all duration-200 
                                            peer-checked:border-[#5E53F4] peer-checked:bg-[#F1E9FF] peer-checked:text-[#5E53F4] peer-checked:shadow-[0_0_0_1px_#5E53F4]
                                            hover:border-[#ADB5BD] shadow-sm">
                                    <div class="size-2 rounded-full {{ $isActive ? 'bg-[#5E53F4]' : 'bg-[#DEE2E6]' }} transition-colors peer-checked:bg-[#5E53F4]"></div>
                                    <span class="text-[11px] font-semibold uppercase tracking-tight">{{ str_replace('_', ' ', $role->name) }}</span>
                                    <span class="material-symbols-outlined hidden peer-checked:block text-[#5E53F4]" style="font-size:16px; font-variation-settings: 'FILL' 1">check_circle</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Section: Module Permissions Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($permissions as $module => $perms)
                        @php
                            $moduleSlug = strtolower($module);
                            $isAllowed = in_array($moduleSlug, $allowedModules);
                            $potentialRoles = [];
                            foreach ($roleToModuleMap as $r => $m) { if(in_array($moduleSlug, $m)) $potentialRoles[] = $r; }
                            $potentialRoles = array_merge($potentialRoles, ['superadmin', 'dosen', 'mahasiswa', 'gpm']);
                        @endphp
                        <div class="module-box bg-white p-4 rounded-2xl border transition-all duration-300 {{ $isAllowed ? 'border-[#DEE2E6] shadow-sm' : 'border-[#F8F9FA] opacity-40 grayscale pointer-events-none' }}"
                            data-module-slug="{{ $moduleSlug }}"
                            data-all-allowed-roles='@json($potentialRoles)'>
                            
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#F8F9FA]">
                                <span class="text-[11px] font-semibold text-[#1A1C1E] uppercase tracking-wider flex items-center gap-2">
                                    {{ $module }} 
                                    @if(!$isAllowed) <span class="material-symbols-outlined text-[#ADB5BD]" style="font-size:14px">lock</span> @endif
                                </span>
                                <label class="flex items-center gap-1.5 cursor-pointer group {{ !$isAllowed ? 'hidden' : '' }}">
                                    <input type="checkbox" class="module-select-all size-3.5 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-0 transition-colors" data-module-target="{{ $user->id }}-{{ $moduleSlug }}">
                                    <span class="text-[10px] font-semibold text-[#ADB5BD] uppercase group-hover:text-[#5E53F4]">All</span>
                                </label>
                            </div>

                            <div class="space-y-2">
                                @foreach($perms as $permission)
                                    @php
                                        $action = explode('.', $permission->name)[1] ?? $permission->name;
                                        $allowedActions = ['view', 'edit', 'delete'];
                                        if (!in_array($action, $allowedActions)) continue;
                                        
                                        $isView = ($action === 'view');
                                        $fromRole = $rolePerms->contains($permission->name);
                                        $isChecked = $shouldCheck($permission->name, $action, $module);
                                    @endphp
                                    <label class="flex items-center justify-between group cursor-pointer py-0.5">
                                        <div class="flex items-center gap-2.5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }}
                                                class="perm-checkbox size-4 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-0 {{ $isView ? 'master-view-cb' : 'child-perm-cb' }}"
                                                data-module-key="{{ $user->id }}-{{ $moduleSlug }}"
                                                data-is-view="{{ $isView ? '1' : '0' }}" data-perm="{{ $permission->name }}">
                                            <span class="text-[12px] font-semibold capitalize {{ $isChecked ? 'text-[#1A1C1E]' : 'text-[#6C757D]' }} group-hover:text-[#5E53F4] transition-colors">
                                                {{ str_replace('_', ' ', $action) }}
                                            </span>
                                        </div>
                                        @if($fromRole) 
                                            <div class="size-1.5 rounded-full bg-[#D1BFFF]" title="Inherited from role"></div> 
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer: Action Button --}}
                <div class="mt-8 pt-5 border-t border-[#DEE2E6] flex items-center justify-end">
                    <button type="submit" class="bg-[#1A1C1E] hover:bg-[#5E53F4] text-white text-[11px] font-semibold uppercase tracking-[0.15em] px-8 py-3 rounded-xl transition-all shadow-lg hover:shadow-[#5E53F4]/20 flex items-center gap-2.5 active:scale-95">
                        <span class="material-symbols-outlined" style="font-size:18px">save</span> Save Changes
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>