{{-- resources/views/superadmin/permission/_user_card.blade.php --}}
{{-- Used for standalone accordion view (category detail page) --}}

@php
    $cardKey      = $categoryKey . '_' . $user->id;
    $userPermCount = $user->getAllPermissions()->count();
    $userModCount  = $user->getAllPermissions()->groupBy(fn($p) => explode('.', $p->name)[0])->count();
    $isActive      = $user->is_active ?? true;

    $allModules = \App\Models\Permission::all()
        ->groupBy(fn($p) => explode('.', $p->name)[0])
        ->keys();

    $moduleIcons = [
        'SIBASO'   => ['bg' => '#FEF3C7', 'color' => '#D97706'],
        'SICATA'   => ['bg' => '#DBEAFE', 'color' => '#3B82F6'],
        'SIMENMA'  => ['bg' => '#D1FAE5', 'color' => '#10B981'],
        'SIPERKOM' => ['bg' => '#FCE7F3', 'color' => '#EC4899'],
    ];
    $defaultIcon = ['bg' => '#EDE9FE', 'color' => '#8B5CF6'];
@endphp

<div class="user-card bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" data-user-id="{{ $user->id }}">

    {{-- Card Header --}}
    <div class="flex items-center gap-4 px-5 py-4 cursor-pointer select-none"
         onclick="toggleCard('{{ $cardKey }}')">

        {{-- Avatar --}}
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 flex items-center justify-center shrink-0 overflow-hidden border-2 border-white shadow-sm">
            @if($user->profile_photo_url ?? false)
                <img src="{{ $user->profile_photo_url }}" alt="" class="w-full h-full object-cover">
            @else
                <span class="text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
            <p class="text-xs text-slate-400 font-medium truncate">{{ $user->email }}</p>
        </div>

        {{-- Roles --}}
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach($user->roles->take(3) as $role)
                <span class="role-pill inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                      style="border-color: var(--accent, #CBD5E1); color: var(--accent, #64748B); background: color-mix(in srgb, var(--accent, #CBD5E1) 12%, white)">
                    {{ Str::title(str_replace('_', ' ', $role->name)) }}
                </span>
            @endforeach
        </div>

        {{-- Stats --}}
        <div class="hidden md:flex items-center gap-4 text-xs text-slate-500 font-semibold">
            <span>{{ $userModCount }} Modul</span>
            <span class="text-slate-300">|</span>
            <span>{{ $userPermCount }} Permissions</span>
        </div>

        {{-- Status --}}
        @if($isActive)
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-500 border border-red-200 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspend
            </span>
        @endif

        {{-- Chevron --}}
        <svg class="card-chevron-{{ $cardKey }} w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </div>

    {{-- Expandable Body --}}
    <div id="card-body-{{ $cardKey }}" class="hidden border-t border-slate-100">
        <form id="form-{{ $cardKey }}" method="POST" action="{{ route('superadmin.permissions.update', $user->id) }}">
            @csrf @method('PUT')

            <div class="p-5">
                {{-- Role Assignment --}}
                <div class="mb-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Roles</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(\App\Models\Role::all() as $role)
                            @php $hasRole = $user->roles->contains('name', $role->name); @endphp
                            <label class="flex items-center gap-2 px-3 py-2 border rounded-xl cursor-pointer transition-all select-none dot-indicator-wrap"
                                   style="{{ $hasRole ? 'border-color: var(--c-primary); background: rgba(11,38,110,0.06); color: var(--c-primary)' : 'border-color: var(--c-border); background: #fff; color: var(--c-fg-muted)' }}">
                                <input type="checkbox"
                                       name="roles[]"
                                       value="{{ $role->name }}"
                                       class="role-checkbox hidden"
                                       data-role-name="{{ $role->name }}"
                                       data-is-academic="{{ in_array($role->name, ['superadmin', 'admin']) ? '1' : '0' }}"
                                       {{ $hasRole ? 'checked' : '' }}>
                                <span class="dot-indicator w-2 h-2 rounded-full shrink-0" style="background: {{ $hasRole ? 'var(--c-primary)' : 'var(--c-border)' }}"></span>
                                <span class="text-xs font-bold">{{ Str::title(str_replace('_', ' ', $role->name)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Module Permissions --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Module Permissions</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($allModules as $module)
                            @php
                                $mkey = strtoupper($module);
                                $ico  = $moduleIcons[$mkey] ?? $defaultIcon;
                                $modulePerms = \App\Models\Permission::where('name', 'like', $module . '.%')->get();
                            @endphp
                            <div class="module-box border border-slate-200 rounded-xl p-3 transition-all"
                                 data-all-allowed-roles="{{ json_encode($modulePerms->pluck('name')) }}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                             style="background:{{ $ico['bg'] }}">
                                            <div class="w-3 h-3 rounded-sm" style="background:{{ $ico['color'] }}"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-700 uppercase">{{ $mkey }}</span>
                                    </div>
                                    <label class="cursor-pointer">
                                        <input type="checkbox"
                                               class="module-select-all w-3 h-3 accent-slate-700"
                                               data-module-target="{{ $cardKey }}_{{ $module }}">
                                    </label>
                                </div>

                                <div class="flex flex-col gap-1">
                                    @foreach($modulePerms as $perm)
                                        @php
                                            $hasPerm   = $user->hasPermissionTo($perm->name);
                                            $isView    = str_contains(strtolower($perm->name), 'view') || str_contains(strtolower($perm->name), 'read') || str_contains(strtolower($perm->name), 'index');
                                            $permLabel = Str::title(explode('.', $perm->name)[1] ?? $perm->name);
                                        @endphp
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $perm->name }}"
                                                   class="perm-checkbox w-3.5 h-3.5 accent-slate-700 rounded"
                                                   data-module-key="{{ $cardKey }}_{{ $module }}"
                                                   data-perm="{{ $perm->name }}"
                                                   data-is-view="{{ $isView ? '1' : '0' }}"
                                                   {{ $hasPerm ? 'checked' : '' }}>
                                            <span class="text-[11px] text-slate-600 font-medium group-hover:text-slate-900 transition-colors">{{ $permLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-5 py-3 bg-slate-50 border-t border-slate-100">
                <button type="button" onclick="toggleCard('{{ $cardKey }}')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                    Tutup
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-bold text-white bg-[#1E293B] rounded-xl hover:bg-slate-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>