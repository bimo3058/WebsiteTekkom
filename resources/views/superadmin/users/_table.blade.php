{{-- Floating Bulk Action Bar --}}
<div id="bulkActionBar" class="hidden mb-6 p-4 bg-[#1A1C1E] rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-2 duration-300 shadow-xl border border-slate-800">
    <div class="flex items-center gap-4 ml-2">
        <div class="flex items-center justify-center w-8 h-8 bg-[#5E53F4] rounded-lg">
            {{-- check-circle --}}
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7.5 12.5L10.5 15.5L16.5 9.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z"/>
            </svg>
        </div>
        <span class="text-[13px] font-semibold text-white tracking-wide">
            <span id="selectedCount" class="text-[#D1BFFF] text-base mr-1">0</span> Users Selected
        </span>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openBulkDeleteHybrid()" class="flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-[11px] font-semibold uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95 group">
            {{-- block-circle (bulk delete) --}}
            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15.5 15.5L12 12L8.5 8.5M8.5 15.5L12 12L15.5 8.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z"/>
            </svg>
            Bulk Delete
        </button>
        <div class="w-px h-5 bg-slate-700 mx-1"></div>
        <button onclick="deselectAll()" class="text-slate-400 hover:text-white text-[11px] font-semibold uppercase tracking-widest px-4 transition-colors">
            Cancel
        </button>
    </div>
</div>

@php
    $allOnline    = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
    $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();
    $onlineUsers    = $allOnline->take(3);
    $suspendedUsers = $allSuspended->take(3);
    $hasOnline    = $allOnline->isNotEmpty();
    $hasSuspended = $allSuspended->isNotEmpty();
@endphp

{{-- Status Cards --}}
<div class="grid grid-cols-1 @if($hasOnline && $hasSuspended) md:grid-cols-2 @endif gap-4 mb-6">

    {{-- Online Users --}}
    @if($hasOnline)
    <div class="bg-white border border-[#DEE2E6] rounded-2xl p-4 shadow-sm h-full">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                <h2 class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">
                    Online • {{ $allOnline->count() }}
                </h2>
            </div>
            <a href="{{ route('superadmin.users.online') }}" class="text-[9px] font-bold text-slate-400 hover:text-[#5E53F4] uppercase tracking-widest transition-colors">View All</a>
        </div>
        <div class="space-y-1.5">
            @foreach($onlineUsers as $onlineUser)
            @php
                $isSuperadmin = $onlineUser->hasRole('superadmin');
                $initials = strtoupper(substr($onlineUser->name, 0, 1));
            @endphp
            <div class="flex items-center justify-between gap-3 p-1.5 rounded-xl hover:bg-slate-50 transition-all group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="relative shrink-0">
                        <x-ui.avatar
                            size="sm"
                            :src="$onlineUser->avatar_url"
                            :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<svg class=\'w-3.5 h-3.5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke-linejoin=\'round\'><path d=\'M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z\'/></svg>' : $initials)"
                            class="size-7 border border-slate-100 !bg-slate-50 !text-slate-500"
                        />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-700 truncate tracking-tight">{{ $onlineUser->name }}</p>
                        <p class="text-[9px] text-slate-400 font-medium uppercase">{{ $onlineUser->roles->first()->name ?? 'User' }}</p>
                    </div>
                </div>
                <button type="button"
                    onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })"
                    class="p-1 text-slate-300 hover:text-amber-500 transition-colors active:scale-90" title="Force Logout">
                    {{-- log-out-04 --}}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Suspended Users --}}
    @if($hasSuspended)
    <div class="bg-white border border-[#DEE2E6] rounded-2xl p-4 shadow-sm h-full">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                <h2 class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">
                    Suspended • {{ $allSuspended->count() }}
                </h2>
            </div>
            <a href="{{ route('superadmin.users.suspended') }}" class="text-[9px] font-bold text-slate-400 hover:text-rose-600 uppercase tracking-widest transition-colors">View All</a>
        </div>
        <div class="space-y-1.5">
            @foreach($suspendedUsers as $suspendedUser)
            @php
                $isSuperadmin = $suspendedUser->hasRole('superadmin');
                $initials = strtoupper(substr($suspendedUser->name, 0, 1));
            @endphp
            <div class="flex items-center justify-between gap-3 p-1.5 rounded-xl hover:bg-rose-50/30 transition-all group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="shrink-0 grayscale opacity-60">
                        <x-ui.avatar
                            size="sm"
                            :src="$suspendedUser->avatar_url"
                            :fallback="new \Illuminate\Support\HtmlString($initials)"
                            class="size-7 border border-slate-100 !bg-slate-50 !text-slate-400"
                        />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-slate-500 truncate tracking-tight line-through decoration-slate-300">{{ $suspendedUser->name }}</p>
                        <p class="text-[9px] text-rose-400 font-medium uppercase tracking-tighter truncate max-w-[120px]">
                            {{ $suspendedUser->suspension_reason ?? 'Violation' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('superadmin.users.unsuspend', $suspendedUser) }}" class="inline m-0">
                    @csrf
                    <button type="submit" class="p-1 text-slate-300 hover:text-emerald-500 transition-colors active:scale-90" title="Unsuspend">
                        {{-- unlocked-02 --}}
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8V9C15 9.55228 15.4477 10 16 10C16.5523 10 17 9.55228 17 9V8C17 5.23858 14.7614 3 12 3C9.23858 3 7 5.23858 7 8V11C5.34315 11 4 12.3431 4 14V18C4 19.6569 5.34315 21 7 21H17C18.6569 21 20 19.6569 20 18V14C20 12.3431 18.6569 11 17 11H16H9V8ZM6 14C6 13.4477 6.44772 13 7 13H8H16H17C17.5523 13 18 13.4477 18 14V18C18 18.5523 17.5523 19 17 19H7C6.44772 19 6 18.5523 6 18V14ZM12 14C12.8284 14 13.5 14.6716 13.5 15.5C13.5 15.9443 13.3069 16.3434 13 16.6181V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V16.6181C10.6931 16.3434 10.5 15.9443 10.5 15.5C10.5 14.6716 11.1716 14 12 14Z"/>
                        </svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- MAIN TABLE --}}
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="px-5 py-3.5 text-left w-12">
                        <input type="checkbox" id="selectAll"
                            class="size-4 rounded border-slate-300 text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                    </th>
                    <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">User Identity</th>
                    <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Access Roles</th>
                    <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Module Rights</th>
                    <th class="px-5 py-3.5 text-left text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Last Activity</th>
                    <th class="px-5 py-3.5 text-center text-[10px] font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                @php
                    $isMe        = $user->id === auth()->id();
                    $userRoles   = $user->roles;
                    $isSuperadmin = $userRoles->pluck('name')->contains('superadmin');
                    $nameParts   = explode(' ', $user->name);
                    $initials    = strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1) $initials .= strtoupper(substr(end($nameParts), 0, 1));
                    $avatarColors = match(true) {
                        $isSuperadmin      => '!bg-[#F1E9FF] !text-[#5E53F4] border-transparent',
                        $userRoles->isEmpty() => '!bg-[#FEF2F2] !text-[#EF4444] border-transparent',
                        default            => '!bg-[#F8F9FA] !text-[#6C757D] border-slate-200',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors group {{ $isMe ? 'bg-slate-50/30' : '' }} {{ $user->isSuspended() ? 'bg-rose-50/10' : '' }}">

                    {{-- Checkbox --}}
                    <td class="px-5 py-3.5">
                        @if(!$isMe)
                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}"
                            class="user-checkbox size-4 rounded border-slate-300 text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                        @else
                        {{-- locked-01 icon --}}
                        <div class="flex items-center justify-center size-4" title="Your Account">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8V11H9V8ZM17 8V11C18.6569 11 20 12.3431 20 14V18C20 19.6569 18.6569 21 17 21H7C5.34315 21 4 19.6569 4 18V14C4 12.3431 5.34315 11 7 11V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8ZM6 14C6 13.4477 6.44772 13 7 13H8H16H17C17.5523 13 18 13.4477 18 14V18C18 18.5523 17.5523 19 17 19H7C6.44772 19 6 18.5523 6 18V14ZM12 17.25C12.6904 17.25 13.25 16.6904 13.25 16C13.25 15.3096 12.6904 14.75 12 14.75C11.3096 14.75 10.75 15.3096 10.75 16C10.75 16.6904 11.3096 17.25 12 17.25Z"/>
                            </svg>
                        </div>
                        @endif
                    </td>

                    {{-- User Identity --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0 {{ $user->isSuspended() ? 'opacity-70 grayscale-[0.5]' : '' }}">
                                <div class="size-8 rounded-full flex items-center justify-center border overflow-hidden {{ $avatarColors }}">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                    @elseif($isSuperadmin)
                                        {{-- shield-02 --}}
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round">
                                            <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
                                        </svg>
                                    @else
                                        <span class="text-[10px] font-semibold uppercase">{{ $initials }}</span>
                                    @endif
                                </div>
                                @if($user->is_online)
                                    <span class="absolute -bottom-0.5 -right-0.5 size-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <p class="text-[12px] font-semibold {{ $user->isSuspended() ? 'text-rose-600 line-through decoration-rose-300' : 'text-slate-800' }} truncate">
                                        {{ $user->name }}
                                    </p>
                                    @if($isMe)
                                        <span class="text-[9px] text-[#5E53F4] font-bold bg-[#F1E9FF] px-1.5 py-0.5 rounded uppercase">YOU</span>
                                    @endif
                                    @if($user->isSuspended())
                                        <span class="text-[9px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded uppercase">Suspended</span>
                                    @endif
                                </div>
                                <p class="text-slate-500 text-[11px] truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Access Roles --}}
                    <td class="px-5 py-3.5">
                        <div class="flex gap-1.5 flex-wrap">
                            @forelse($userRoles as $role)
                            @php
                                $roleName  = strtolower($role->name);
                                $roleStyle = match(true) {
                                    $roleName === 'superadmin' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                    $roleName === 'dosen'      => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                    $roleName === 'mahasiswa'  => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                    default                    => 'bg-[#F0F5FF] text-[#5E53F4] border-[#D1DFFF]',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold border uppercase tracking-wider {{ $roleStyle }}">
                                {{ str_replace('_', ' ', $role->name) }}
                            </span>
                            @empty
                            <span class="text-slate-400 text-[10px] font-medium italic">No Role</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Module Rights --}}
                    <td class="px-5 py-3.5">
                        @if($isSuperadmin)
                            <span class="text-[#5E53F4] text-[10px] font-semibold uppercase tracking-widest flex items-center gap-1.5">
                                {{-- check-circle --}}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7.5 12.5L10.5 15.5L16.5 9.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z"/>
                                </svg>
                                Root Access
                            </span>
                        @else
                        @php
                            $perms    = $user->directPermissions->pluck('name');
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
                            <span class="text-slate-400 text-[10px]">—</span>
                        @endif
                        @endif
                    </td>

                    {{-- Last Activity --}}
                    <td class="px-5 py-3.5">
                        <p class="text-slate-800 font-medium text-[11px] mb-0.5">{{ $user->created_at->format('d M Y') }}</p>
                        <p class="text-slate-400 text-[10px]">
                            {{ $user->last_login ? $user->last_login->diffForHumans() : 'Never logged in' }}
                        </p>
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-0.5">

                            {{-- Edit — pen/write icon (pakai path sederhana) --}}
                            <button onclick="openEditInfo({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]) }})"
                                class="p-1.5 text-slate-400 hover:text-[#5E53F4] rounded-lg transition-colors" title="Edit Profile">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4C2.89543 4 2 4.89543 2 6V20C2 21.1046 2.89543 22 4 22H18C19.1046 22 20 21.1046 20 20V13M18.5 2.5C19.3284 2.5 20 3.17157 20 4V4C20.8284 4 21.5 4.67157 21.5 5.5C21.5 6.32843 20.8284 7 20 7L11 16L7 17L8 13L17 4C17 3.17157 17.6716 2.5 18.5 2.5Z"/>
                                </svg>
                            </button>

                            @if(!$isMe)
                                {{-- Force Logout — log-out-04 --}}
                                <button type="button"
                                    onclick="openForceLogoutModal({ id: '{{ $user->id }}', name: '{{ $user->name }}' })"
                                    class="p-1.5 text-slate-400 hover:text-amber-500 rounded-lg transition-colors" title="Force Logout">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238"/>
                                    </svg>
                                </button>

                                @if($user->isSuspended())
                                    {{-- Unsuspend — unlocked-02 --}}
                                    <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}" class="inline m-0">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-emerald-500 rounded-lg transition-colors" title="Unsuspend">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8V9C15 9.55228 15.4477 10 16 10C16.5523 10 17 9.55228 17 9V8C17 5.23858 14.7614 3 12 3C9.23858 3 7 5.23858 7 8V11C5.34315 11 4 12.3431 4 14V18C4 19.6569 5.34315 21 7 21H17C18.6569 21 20 19.6569 20 18V14C20 12.3431 18.6569 11 17 11H16H9V8ZM6 14C6 13.4477 6.44772 13 7 13H8H16H17C17.5523 13 18 13.4477 18 14V18C18 18.5523 17.5523 19 17 19H7C6.44772 19 6 18.5523 6 18V14ZM12 14C12.8284 14 13.5 14.6716 13.5 15.5C13.5 15.9443 13.3069 16.3434 13 16.6181V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V16.6181C10.6931 16.3434 10.5 15.9443 10.5 15.5C10.5 14.6716 11.1716 14 12 14Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @elseif(!$isSuperadmin)
                                    {{-- Suspend — block-circle --}}
                                    <button onclick="openSuspendModal({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                        class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg transition-colors" title="Suspend User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15.5 15.5L12 12L8.5 8.5M8.5 15.5L12 12L15.5 8.5M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Delete — block (X) --}}
                                <button type="button"
                                    onclick="openDeleteHybrid({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Delete User">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6H5H21M8 6V4C8 3.44772 8.44772 3 9 3H15C15.5523 3 16 3.44772 16 4V6M19 6L18.1245 19.1338C18.0544 20.1818 17.1818 21 16.1315 21H7.86852C6.81818 21 5.94558 20.1818 5.87551 19.1338L5 6H19Z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            {{-- users-01 empty state --}}
                            <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M14.8574 10.8344C15.5799 9.73304 16.0001 8.41559 16.0001 7C16.0001 5.58441 15.5799 4.26696 14.8574 3.16558C15.2195 3.05785 15.603 3 16 3C18.2092 3 20 4.79086 20 7C20 9.20914 18.2092 11 16 11C15.603 11 15.2195 10.9422 14.8574 10.8344ZM17.8741 21C17.9563 20.6804 18.0001 20.3453 18.0001 20V19C18.0001 17.1081 17.3434 15.3696 16.2455 14H17C19.7615 14 22 16.2386 22 19V20C22 20.5523 21.5523 21 21 21H17.8741Z"
                                    fill="currentColor"/>
                                <path d="M10 14H8C5.23858 14 3 16.2386 3 19V20C3 20.5523 3.44772 21 4 21H14C14.5523 21 15 20.5523 15 20V19C15 16.2386 12.7614 14 10 14Z"
                                    stroke="currentColor" stroke-width="2"/>
                                <path d="M9 11C11.2091 11 13 9.20914 13 7C13 4.79086 11.2091 3 9 3C6.79086 3 5 4.79086 5 7C5 9.20914 6.79086 11 9 11Z"
                                    stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <p class="text-slate-400 text-[11px] font-semibold uppercase tracking-widest mt-1">No users found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>