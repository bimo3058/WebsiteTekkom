{{-- resources/views/superadmin/dashboard/_activity.blade.php --}}
<div class="flex items-center gap-2 mb-3">
    <span class="text-xs font-bold text-foreground tracking-tight uppercase">Aktivitas Terkini</span>
    <div class="flex-1 h-px bg-border"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4 mb-8">

    {{-- ① User Online ──────────────────────────────────────────────────── --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-border">
            <div class="flex items-center gap-2">
                {{-- sensors / online dot --}}
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">User Online</span>
            </div>
            <a href="{{ route('superadmin.users.online') }}"
               class="text-[10px] font-semibold text-primary hover:text-primary-400 transition-colors flex items-center gap-0.5">
                Detail
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 12H4M20 12L14 6M20 12L14 18"/>
                </svg>
            </a>
        </div>

        {{-- Rows --}}
        <div class="flex-1 divide-y divide-border/60">
            @php
                $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
                    ->with('roles')->latest('last_login')->take(6)->get();
            @endphp
            @forelse($online_users as $onlineUser)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-muted/30 transition-colors">
                <div class="relative shrink-0">
                    <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center overflow-hidden border border-emerald-100">
                        @if($onlineUser->avatar_url)
                            <img src="{{ $onlineUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                        @else
                            <span class="text-[10px] font-bold uppercase">{{ substr($onlineUser->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-500 border border-white"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-foreground truncate">{{ $onlineUser->name }}</p>
                    <p class="text-[10px] text-muted-foreground">{{ ucfirst($onlineUser->roles->first()->name ?? 'User') }}</p>
                </div>
                <span class="text-[9px] font-medium text-emerald-600 shrink-0">
                    {{ $onlineUser->last_login?->diffForHumans(null, true) ?? 'Active' }}
                </span>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-muted-foreground gap-2">
                {{-- eye icon --}}
                <svg class="w-6 h-6 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-[11px]">Tidak ada user online</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ② User Baru ─────────────────────────────────────────────────────── --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center px-4 py-3 border-b border-border">
            <div class="flex items-center gap-2">
                {{-- user-plus icon --}}
                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11H18.5M16 11H18.5M18.5 11V8.5M18.5 11V13.5M8 14H12C14.7614 14 17 16.2386 17 19V20C17 20.5523 16.5523 21 16 21H4C3.44772 21 3 20.5523 3 20V19C3 16.2386 5.23858 14 8 14ZM14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"/>
                </svg>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">User Baru</span>
            </div>
        </div>

        <div class="flex-1 divide-y divide-border/60">
            @forelse($new_registrations as $newUser)
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-muted/30 transition-colors">
                <div class="w-7 h-7 rounded-full bg-muted text-muted-foreground flex items-center justify-center overflow-hidden border border-border shrink-0">
                    @if($newUser->avatar_url)
                        <img src="{{ $newUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-[10px] font-bold uppercase">{{ substr($newUser->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-foreground truncate">{{ $newUser->name }}</p>
                    <p class="text-[10px] text-muted-foreground">{{ $newUser->created_at->format('d M Y') }}</p>
                </div>
                <span class="text-[9px] font-semibold text-muted-foreground bg-muted px-1.5 py-0.5 rounded shrink-0">
                    {{ ucfirst($newUser->roles->first()->name ?? 'User') }}
                </span>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-muted-foreground gap-2">
                {{-- user-plus icon --}}
                <svg class="w-6 h-6 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11H18.5M16 11H18.5M18.5 11V8.5M18.5 11V13.5M8 14H12C14.7614 14 17 16.2386 17 19V20C17 20.5523 16.5523 21 16 21H4C3.44772 21 3 20.5523 3 20V19C3 16.2386 5.23858 14 8 14ZM14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"/>
                </svg>
                <p class="text-[11px]">Belum ada user baru</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ③ Log Aktivitas ──────────────────────────────────────────────────── --}}
    <div class="bg-white border border-border rounded-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-border">
            <div class="flex items-center gap-2">
                {{-- notification-text-square icon --}}
                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                    <path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z"/>
                </svg>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Log Aktivitas</span>
            </div>
            <a href="{{ route('superadmin.audit-logs') }}"
               class="text-[10px] font-semibold text-primary hover:text-primary-400 transition-colors flex items-center gap-0.5">
                Semua
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 12H4M20 12L14 6M20 12L14 18"/>
                </svg>
            </a>
        </div>

        <div class="flex-1 divide-y divide-border/60">
            @forelse($recent_logs->take(6) as $log)
            @php
                $actionConfig = match(strtoupper($log->action)) {
                    'CREATE' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
                    'UPDATE' => ['bg' => 'bg-sky-50',     'text' => 'text-sky-700'],
                    'DELETE' => ['bg' => 'bg-rose-50',    'text' => 'text-rose-700'],
                    'LOGIN'  => ['bg' => 'bg-primary-50', 'text' => 'text-primary'],
                    default  => ['bg' => 'bg-muted',      'text' => 'text-muted-foreground'],
                };
            @endphp
            <div class="flex items-start gap-3 px-4 py-3 hover:bg-muted/30 transition-colors">
                <div class="w-7 h-7 rounded-full bg-muted text-muted-foreground flex items-center justify-center overflow-hidden border border-border shrink-0 mt-0.5">
                    @if($log->user?->avatar_url)
                        <img src="{{ $log->user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-[10px] font-bold uppercase">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-foreground truncate">{{ $log->user->name ?? 'System' }}</p>
                    <p class="text-[10px] text-muted-foreground truncate mt-0.5">{{ $log->description }}</p>
                </div>
                <div class="text-right shrink-0 flex flex-col items-end gap-1">
                    <span class="text-[8px] font-bold uppercase px-1.5 py-0.5 rounded {{ $actionConfig['bg'] }} {{ $actionConfig['text'] }}">
                        {{ $log->action }}
                    </span>
                    <span class="text-[9px] text-muted-foreground">{{ $log->created_at->diffForHumans(null, true) }}</span>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-muted-foreground gap-2">
                <svg class="w-6 h-6 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15"/>
                </svg>
                <p class="text-[11px]">Tidak ada log aktivitas</p>
            </div>
            @endforelse
        </div>
    </div>

</div>