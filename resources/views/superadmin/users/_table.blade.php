{{-- Floating Bulk Action Bar --}}
<div id="bulkActionBar" class="hidden mb-6 p-4 bg-[#1A1C1E] rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-2 duration-300 shadow-xl border border-slate-800">
    <div class="flex items-center gap-4 ml-2">
        <div class="flex items-center justify-center w-8 h-8 bg-[#5E53F4] rounded-lg">
            <span class="material-symbols-outlined text-white" style="font-size: 18px">check_circle</span>
        </div>
        <span class="text-[13px] font-semibold text-white tracking-wide">
            <span id="selectedCount" class="text-[#D1BFFF] text-base mr-1">0</span> Users Selected
        </span>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openBulkDeleteHybrid()" class="flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-[11px] font-semibold uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95 group">
            <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-size: 18px">delete_sweep</span>
            Bulk Delete
        </button>
        <div class="w-px h-5 bg-slate-700 mx-1"></div>
        <button onclick="deselectAll()" class="text-slate-400 hover:text-white text-[11px] font-semibold uppercase tracking-widest px-4 transition-colors">
            Cancel
        </button>
    </div>
</div>

@php
    $allOnline = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
    $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();

    $onlineUsers = $allOnline->take(3);
    $suspendedUsers = $allSuspended->take(3);
    
    $hasOnline = $allOnline->isNotEmpty();
    $hasSuspended = $allSuspended->isNotEmpty();
@endphp

<div class="grid grid-cols-1 @if($hasOnline && $hasSuspended) md:grid-cols-2 @endif gap-5 mb-6">
    {{-- 1. Card Online Users --}}
    @if($hasOnline)
    <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-4 shadow-sm h-full">
        {{-- Header Tetap Sama --}}
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                <h2 class="text-[11px] font-bold text-emerald-800 uppercase tracking-widest">
                    Online ({{ $allOnline->count() }})
                </h2>
            </div>
            <a href="{{ route('superadmin.users.online') }}" class="text-[9px] font-bold text-emerald-600 hover:underline uppercase tracking-widest">View All &rarr;</a>
        </div>

        {{-- List Dibuat Vertikal & Compact (Hapus overflow-x-auto) --}}
        <div class="space-y-2">
            @foreach($onlineUsers as $onlineUser)
                @php 
                    $isSuperadmin = $onlineUser->hasRole('superadmin');
                    $initials = strtoupper(substr($onlineUser->name, 0, 1));
                @endphp
                <div class="bg-white border border-emerald-100/50 rounded-xl p-2 flex items-center justify-between gap-3 hover:border-emerald-200 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="relative shrink-0">
                            {{-- Avatar diperkecil (size xs/sm) --}}
                            <x-ui.avatar size="xs" :src="$onlineUser->avatar_url" :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[14px]\'>admin_panel_settings</span>' : $initials)" class="!w-8 !h-8 {{ $isSuperadmin ? '!bg-[#F1E9FF] !text-[#5E53F4]' : '!bg-[#F8F9FA] !text-[#6C757D]' }}" />
                            <span class="absolute bottom-0 right-0 size-2 bg-emerald-500 border border-white rounded-full"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold text-slate-800 truncate tracking-tight">{{ $onlineUser->name }}</p>
                            <p class="text-[9px] text-slate-500 uppercase tracking-tighter">{{ $onlineUser->roles->first()->name ?? 'User' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if(!$isSuperadmin)
                            <button type="button" onclick="openSuspendModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Suspend">
                                <span class="material-symbols-outlined" style="font-size:14px">block</span>
                            </button>
                        @endif
                        <button type="button" onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Force Logout">
                            <span class="material-symbols-outlined" style="font-size:14px">logout</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 2. Card Suspended Users --}}
    @if($hasSuspended)
    <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-4 shadow-sm h-full">
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                <h2 class="text-[11px] font-bold text-rose-800 uppercase tracking-widest">
                    Suspended ({{ $allSuspended->count() }})
                </h2>
            </div>
            <a href="{{ route('superadmin.users.suspended') }}" class="text-[9px] font-bold text-rose-600 hover:underline uppercase tracking-widest">View All &rarr;</a>
        </div>

        <div class="space-y-2">
            @foreach($suspendedUsers as $suspendedUser)
                @php 
                    $isSuperadmin = $suspendedUser->hasRole('superadmin');
                    $initials = strtoupper(substr($suspendedUser->name, 0, 1));
                @endphp
                <div class="bg-white border border-rose-100/50 rounded-xl p-2 flex items-center justify-between gap-3 hover:border-rose-200 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="relative shrink-0 opacity-60 grayscale">
                            <x-ui.avatar size="xs" :src="$suspendedUser->avatar_url" :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[14px]\'>admin_panel_settings</span>' : $initials)" class="!w-8 !h-8 {{ $isSuperadmin ? '!bg-[#F1E9FF] !text-[#5E53F4]' : '!bg-[#F8F9FA] !text-[#6C757D]' }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold text-slate-800 truncate tracking-tight line-through decoration-rose-200">{{ $suspendedUser->name }}</p>
                            <p class="text-[9px] text-rose-500 font-medium tracking-tighter truncate">{{ Str::limit($suspendedUser->suspension_reason ?? 'No reason', 20) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('superadmin.users.unsuspend', $suspendedUser) }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg">
                            <span class="material-symbols-outlined" style="font-size:14px">lock_open</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- MAIN TABLE --}}
<div class="bg-white border border-[#DEE2E6] rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-[#DEE2E6] bg-[#F8F9FA]">
                    <th class="px-5 py-4 text-left w-12">
                        <input type="checkbox" id="selectAll" 
                            class="size-4 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                    </th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">User Identity</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Access Roles</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Module Rights</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Last Activity</th>
                    <th class="px-5 py-4 text-center text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F8F9FA]">
                @forelse($users as $user)
                @php 
                    $isMe = $user->id === auth()->id();
                    $userRoles = $user->roles;
                    $isSuperadmin = $userRoles->pluck('name')->contains('superadmin');
                    
                    // Inisial untuk fallback
                    $nameParts = explode(' ', $user->name);
                    $initials = strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1) {
                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                    }

                    // Avatar Fallback Styles
                    $avatarColors = match(true) {
                        $isSuperadmin => '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]',
                        $userRoles->isEmpty() => '!bg-[#FEF2F2] !text-[#EF4444] border-[#FEE2E2]',
                        default => '!bg-[#F8F9FA] !text-[#6C757D] border-[#DEE2E6]',
                    };
                @endphp
                <tr class="hover:bg-[#F8F9FA]/50 transition-colors group {{ $isMe ? 'bg-slate-50/30' : '' }}">
                    <td class="px-5 py-4">
                        @if(!$isMe)
                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" 
                            class="user-checkbox size-4 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                        @else
                        <div class="flex items-center justify-center size-4 rounded bg-slate-100 border border-[#DEE2E6]" title="Your Account">
                            <span class="material-symbols-outlined text-slate-400" style="font-size: 12px">lock</span>
                        </div>
                        @endif
                    </td>

                    {{-- User Identity with Dynamic Avatar --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-4">
                            {{-- Avatar Container --}}
                            <div class="relative shrink-0">
                                <x-ui.avatar 
                                    size="sm" 
                                    :src="$user->avatar_url" 
                                    :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[18px]\'>admin_panel_settings</span>' : $initials)" 
                                    class="border-2 border-white shadow-sm {{ $avatarColors }} shrink-0" 
                                />
                                @if($user->is_online)
                                    <span class="absolute bottom-0 right-0 size-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>

                            {{-- Info Text --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <p class="text-[13px] font-semibold text-[#1A1C1E] truncate tracking-tight">
                                        {{ $user->name }}
                                    </p>
                                    @if($isMe)
                                        <span class="text-[10px] text-[#5E53F4] font-semibold bg-[#F1E9FF] px-1.5 rounded-md">YOU</span>
                                    @endif
                                </div>
                                <p class="text-[#6C757D] text-[11px] font-medium truncate leading-normal">
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Access Roles --}}
                    <td class="px-5 py-4">
                        <div class="flex gap-1.5 flex-wrap">
                            @forelse($userRoles as $role)
                                @php
                                    $roleName = strtolower($role->name);
                                    $roleStyle = match(true) {
                                        $roleName === 'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                        $roleName === 'dosen' => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                        $roleName === 'mahasiswa' => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                        default => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold border uppercase tracking-wider {{ $roleStyle }}">
                                    {{ str_replace('_', ' ', $role->name) }}
                                </span>
                            @empty
                                <span class="text-[#ADB5BD] text-[10px] font-semibold italic uppercase tracking-tighter">No Assigned Role</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Module Rights --}}
                    <td class="px-5 py-4">
                        @if($isSuperadmin)
                            <span class="text-[#5E53F4] text-[10px] font-semibold uppercase tracking-widest flex items-center gap-1.5">
                                <span class="material-symbols-outlined fill-1" style="font-size: 16px">verified</span> Root Access
                            </span>
                        @else
                            @php 
                                $perms = $user->directPermissions->pluck('name');
                                $modCount = $perms->map(fn($p) => explode('.', $p)[0])->unique()->count();
                            @endphp
                            @if($modCount > 0)
                                <div class="inline-flex items-center border border-[#DEE2E6] rounded-full overflow-hidden bg-white shadow-sm">
                                    <div class="px-2 py-0.5 bg-[#F8F9FA] text-[#495057] text-[9px] font-semibold uppercase border-r border-[#DEE2E6]">
                                        {{ $modCount }} Modules
                                    </div>
                                    <div class="px-2 py-0.5 text-[#5E53F4] text-[9px] font-semibold uppercase bg-white">
                                        {{ $perms->count() }} Perms
                                    </div>
                                </div>
                            @else
                                <span class="text-[#ADB5BD] text-[10px]">—</span>
                            @endif
                        @endif
                    </td>

                    {{-- Last Activity --}}
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-[#1A1C1E] font-semibold text-[11px]">{{ $user->created_at->format('d M Y') }}</span>
                            <span class="text-[#6C757D] text-[10px] italic">
                                Log: {{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}
                            </span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="openEditInfo({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]) }})"
                                class="p-2 text-[#5E53F4] hover:bg-[#F1E9FF] rounded-xl transition-all" title="Edit Profile">
                                <span class="material-symbols-outlined" style="font-size:18px">edit_square</span>
                            </button>

                            @if(!$isMe)
                                <button type="button" 
                                    onclick="openForceLogoutModal({ id: '{{ $user->id }}', name: '{{ $user->name }}' })"
                                    class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-all" title="Force Logout">
                                    <span class="material-symbols-outlined" style="font-size:18px">logout</span>
                                </button>

                                @if($user->isSuspended())
                                    <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-xl transition-all" title="Unsuspend">
                                            <span class="material-symbols-outlined" style="font-size:18px">lock_open</span>
                                        </button>
                                    </form>
                                @elseif(!$isSuperadmin)
                                    <button onclick="openSuspendModal({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                        class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Suspend User">
                                        <span class="material-symbols-outlined" style="font-size:18px">block</span>
                                    </button>
                                @endif

                                <button type="button" 
                                    onclick="openDeleteHybrid({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Delete User">
                                    <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-[#DEE2E6]" style="font-size: 48px">group_off</span>
                            <p class="text-[#ADB5BD] text-sm font-medium uppercase tracking-widest">No users matching your criteria</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>