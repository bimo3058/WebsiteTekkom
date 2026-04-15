@php
    $allOnline = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
    $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();
    $onlineUsers = $allOnline->take(3);
    $suspendedUsers = $allSuspended->take(3);
    $hasOnline = $allOnline->isNotEmpty();
    $hasSuspended = $allSuspended->isNotEmpty();
@endphp

<div class="grid grid-cols-1 @if($hasOnline && $hasSuspended) md:grid-cols-2 @endif gap-5 mb-6">
    @if($hasOnline)
    <div class="bg-emerald-50/30 border border-emerald-100 rounded-2xl p-4 shadow-sm h-full">
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <h2 class="text-[11px] font-bold text-emerald-800 uppercase tracking-widest">Online ({{ $allOnline->count() }})</h2>
            </div>
            <a href="{{ route('superadmin.users.online') }}" class="text-[9px] font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-widest bg-white px-2 py-1 rounded-lg border border-emerald-100 shadow-sm transition-all">View All &rarr;</a>
        </div>
        <div class="space-y-2">
            @foreach($onlineUsers as $onlineUser)
                @php 
                    $isSuperadmin = $onlineUser->hasRole('superadmin');
                    $initials = strtoupper(substr($onlineUser->name, 0, 1));
                    $avatarColors = $isSuperadmin ? '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]' : '!bg-[#F8F9FA] !text-[#6C757D] border-[#DEE2E6]';
                @endphp
                <div class="bg-white border border-emerald-100/50 rounded-xl p-2.5 flex items-center justify-between gap-3 hover:border-emerald-300 transition-all group">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative shrink-0">
                            <x-ui.avatar size="sm" :src="$onlineUser->avatar_url" :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[16px]\'>admin_panel_settings</span>' : $initials)" class="border-2 border-white shadow-sm {{ $avatarColors }}" />
                            <span class="absolute bottom-0 right-0 size-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[12px] font-semibold text-slate-800 truncate tracking-tight group-hover:text-emerald-700">{{ $onlineUser->name }}</p>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[9px] font-semibold text-emerald-600 uppercase tracking-tighter bg-emerald-50 px-1 rounded">Active Now</span>
                                <span class="text-[9px] text-slate-500 uppercase font-medium tracking-tighter">{{ $onlineUser->roles->first()->name ?? 'User' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        @if(!$isSuperadmin)
                            <button type="button" onclick="openSuspendModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"><span class="material-symbols-outlined" style="font-size:16px">block</span></button>
                        @endif
                        <button type="button" onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors"><span class="material-symbols-outlined" style="font-size:16px">logout</span></button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($hasSuspended)
    <div class="bg-rose-50/30 border border-rose-100 rounded-2xl p-4 shadow-sm h-full">
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                <h2 class="text-[11px] font-bold text-rose-800 uppercase tracking-widest">Suspended ({{ $allSuspended->count() }})</h2>
            </div>
            <a href="{{ route('superadmin.users.suspended') }}" class="text-[9px] font-bold text-rose-600 hover:text-rose-700 uppercase tracking-widest bg-white px-2 py-1 rounded-lg border border-rose-100 shadow-sm transition-all">View All &rarr;</a>
        </div>
        <div class="space-y-2">
            @foreach($suspendedUsers as $suspendedUser)
                @php 
                    $isSuperadmin = $suspendedUser->hasRole('superadmin');
                    $initials = strtoupper(substr($suspendedUser->name, 0, 1));
                    $avatarColors = $isSuperadmin ? '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]' : '!bg-rose-50 !text-rose-400 border-rose-100';
                @endphp
                <div class="bg-white border border-rose-100/50 rounded-xl p-2.5 flex items-center justify-between gap-3 hover:border-rose-300 transition-all group">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative shrink-0 opacity-60 grayscale-[0.5]">
                            <x-ui.avatar size="sm" :src="$suspendedUser->avatar_url" :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[16px]\'>admin_panel_settings</span>' : $initials)" class="border-2 border-white shadow-sm {{ $avatarColors }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[12px] font-semibold text-slate-800 truncate tracking-tight line-through decoration-rose-300 opacity-70 group-hover:text-rose-700">{{ $suspendedUser->name }}</p>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[9px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded uppercase tracking-tighter border border-rose-100">Blocked</span>
                                <span class="text-[9px] text-rose-400 font-medium truncate italic max-w-[100px]">{{ $suspendedUser->suspension_reason ?? 'Policy Violation' }}</span>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('superadmin.users.unsuspend', $suspendedUser) }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors shadow-sm bg-white border border-emerald-100 active:scale-95" title="Unsuspend"><span class="material-symbols-outlined" style="font-size:16px">lock_open</span></button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>