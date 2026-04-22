{{-- resources/views/superadmin/dashboard/_activity.blade.php --}}
<div class="flex items-center gap-3 mb-4">
    <div class="w-1 h-5 rounded bg-primary shrink-0"></div>
    <span class="text-sm sm:text-base font-bold text-grey-800 tracking-tight whitespace-nowrap">Aktivitas Terkini</span>
    <x-ui.separator class="flex-1" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5 mb-7 items-stretch">

    {{-- ① User Online --}}
    <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
        <x-ui.card-header class="!flex !flex-row !items-center !justify-between !px-4 sm:!px-5 !pt-3 sm:!pt-4 !pb-2 sm:!pb-3 border-b border-border bg-muted/30">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-[16px] sm:text-[18px]">sensors</span>
                <x-ui.card-title class="!text-[10px] sm:!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">Monitoring Online</x-ui.card-title>
            </div>
            <a href="{{ route('superadmin.users.online') }}" class="text-[9px] sm:text-[10px] font-semibold text-primary hover:text-primary-400 flex items-center gap-0.5 transition-colors">
                Detail <span class="material-symbols-outlined text-[11px] sm:text-[13px]">arrow_forward</span>
            </a>
        </x-ui.card-header>
        <div class="flex-1">
            <x-ui.table>
                <x-ui.table-body>
                    @php
                        $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
                            ->with('roles')->latest('last_login')->take(6)->get();
                    @endphp
                    @forelse($online_users as $onlineUser)
                    <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                        <x-ui.table-cell class="!py-2 sm:!py-3 !px-3 sm:!px-5">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="relative shrink-0">
                                    <div class="size-7 sm:size-8 rounded-full flex items-center justify-center border border-emerald-100 bg-emerald-50 text-emerald-600 overflow-hidden">
                                        @if($onlineUser->avatar_url)
                                            <img src="{{ $onlineUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[9px] sm:text-[10px] font-semibold uppercase">{{ substr($onlineUser->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="absolute bottom-0 right-0 block h-1.5 sm:h-2 w-1.5 sm:w-2 rounded-full bg-emerald-500 border border-white"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] sm:text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $onlineUser->name }}</p>
                                    @php
                                        $rn = strtolower($onlineUser->roles->first()->name ?? '');
                                        $badgeVariant = match($rn) {
                                            'superadmin' => 'role-superadmin',
                                            'dosen'      => 'role-dosen',
                                            'mahasiswa'  => 'role-mahasiswa',
                                            default      => 'role-default',
                                        };
                                    @endphp
                                    <x-ui.badge :variant="$badgeVariant" class="!text-[8px] sm:!text-[9px] !px-1.5 sm:!px-2 !py-0">
                                        {{ $onlineUser->roles->first()->name ?? 'User' }}
                                    </x-ui.badge>
                                </div>
                                <span class="text-[9px] sm:text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1 sm:px-1.5 py-0.5 rounded border border-emerald-100 shrink-0">
                                    {{ $onlineUser->last_login ? $onlineUser->last_login->diffForHumans(null, true) : 'Active' }}
                                </span>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @empty
                    <x-ui.table-row>
                        <x-ui.table-cell class="text-center py-8 sm:py-10 text-[10px] sm:text-[11px] text-muted-foreground italic">Tidak ada user online.</x-ui.table-cell>
                    </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
        </div>
    </x-ui.card>

    {{-- ② User Baru --}}
    <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
        <x-ui.card-header class="!flex !flex-row !items-center !px-4 sm:!px-5 !pt-3 sm:!pt-4 !pb-2 sm:!pb-3 border-b border-border bg-muted/30">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-success-500 text-[16px] sm:text-[18px]">person_add</span>
                <x-ui.card-title class="!text-[10px] sm:!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">User Baru Terdaftar</x-ui.card-title>
            </div>
        </x-ui.card-header>
        <div class="flex-1">
            <x-ui.table>
                <x-ui.table-body>
                    @forelse($new_registrations as $newUser)
                    <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                        <x-ui.table-cell class="!py-2 sm:!py-3 !px-3 sm:!px-5">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="size-7 sm:size-8 rounded-full flex items-center justify-center border border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D] flex-shrink-0 overflow-hidden">
                                    @if($newUser->avatar_url)
                                        <img src="{{ $newUser->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[9px] sm:text-[10px] font-semibold uppercase">{{ substr($newUser->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] sm:text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $newUser->name }}</p>
                                    <p class="text-[9px] sm:text-[10px] text-muted-foreground mt-0.5 font-medium">Joined {{ $newUser->created_at->format('d M Y') }}</p>
                                </div>
                                @php
                                    $rn = strtolower($newUser->roles->first()->name ?? '');
                                    $badgeVariant = match($rn) {
                                        'superadmin' => 'role-superadmin',
                                        'dosen'      => 'role-dosen',
                                        'mahasiswa'  => 'role-mahasiswa',
                                        default      => 'role-default',
                                    };
                                @endphp
                                <x-ui.badge :variant="$badgeVariant" class="!text-[8px] sm:!text-[9px] !px-1.5 sm:!px-2 !py-0 shrink-0">
                                    {{ $newUser->roles->first()->name ?? 'USER' }}
                                </x-ui.badge>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @empty
                    <x-ui.table-row>
                        <x-ui.table-cell class="text-center py-8 sm:py-10 text-[10px] sm:text-[11px] text-muted-foreground italic">Belum ada user baru.</x-ui.table-cell>
                    </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
        </div>
    </x-ui.card>

    {{-- ③ Log Aktivitas --}}
    <x-ui.card class="shadow-sm border-border/60 overflow-hidden flex flex-col">
        <x-ui.card-header class="!flex !flex-row !items-center !justify-between !px-4 sm:!px-5 !pt-3 sm:!pt-4 !pb-2 sm:!pb-3 border-b border-border bg-muted/30">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-500 text-[16px] sm:text-[18px]">history</span>
                <x-ui.card-title class="!text-[10px] sm:!text-[11px] !font-bold uppercase tracking-widest text-muted-foreground !m-0">Log Aktivitas Terbaru</x-ui.card-title>
            </div>
            <a href="{{ route('superadmin.audit-logs') }}" class="text-[9px] sm:text-[10px] font-semibold text-primary hover:text-primary-400 flex items-center gap-0.5 transition-colors">
                Lihat semua <span class="material-symbols-outlined text-[11px] sm:text-[13px]">arrow_forward</span>
            </a>
        </x-ui.card-header>
        <div class="flex-1 overflow-x-auto scrollbar-none">
            <x-ui.table>
                <x-ui.table-body>
                    @forelse($recent_logs->take(6) as $log)
                    @php
                        $actionVariant = match(strtoupper($log->action)) {
                            'CREATE' => 'success', 'UPDATE' => 'sky',
                            'DELETE' => 'destructive', 'LOGIN' => 'purple',
                            default  => 'secondary',
                        };
                    @endphp
                    <x-ui.table-row class="hover:bg-muted/20 transition-colors border-b last:border-0 border-border/40">
                        <x-ui.table-cell class="!py-2 sm:!py-3 !px-3 sm:!px-5">
                            <div class="flex items-center gap-2 sm:gap-3">
                                @if($log->user)
                                    @php
                                        $logSa = $log->user->hasRole('superadmin');
                                        $logAv = $logSa ? '!bg-[#F1E9FF] border-[#D1BFFF] text-[#5E53F4]' : 'border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D]';
                                    @endphp
                                    <div class="size-7 sm:size-8 rounded-full flex items-center justify-center border flex-shrink-0 overflow-hidden {{ $logAv }}">
                                        @if($log->user->avatar_url)
                                            <img src="{{ $log->user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[9px] sm:text-[10px] font-semibold uppercase">{{ substr($log->user->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="size-7 sm:size-8 rounded-full flex items-center justify-center border border-[#DEE2E6] bg-[#F8F9FA] text-[#6C757D] flex-shrink-0">
                                        <span class="material-symbols-outlined text-[14px] sm:text-[16px]">settings_suggest</span>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] sm:text-[12px] font-semibold text-grey-800 truncate leading-tight">{{ $log->user->name ?? 'System' }}</p>
                                    <p class="text-[9px] sm:text-[10px] text-muted-foreground mt-0.5 truncate">{{ $log->description }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <x-ui.badge :variant="$actionVariant" class="!text-[7px] sm:!text-[8px] !px-1 sm:!px-1.5 !py-0 font-black leading-none block mb-0.5 sm:mb-1">
                                        {{ $log->action }}
                                    </x-ui.badge>
                                    <span class="text-[8px] sm:text-[9px] text-muted-foreground font-medium">{{ $log->created_at->diffForHumans(null, true) }}</span>
                                </div>
                            </div>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                    @empty
                    <x-ui.table-row>
                        <x-ui.table-cell class="text-center py-8 sm:py-10 text-[10px] sm:text-[11px] text-muted-foreground italic">Tidak ada log aktivitas.</x-ui.table-cell>
                    </x-ui.table-row>
                    @endforelse
                </x-ui.table-body>
            </x-ui.table>
        </div>
    </x-ui.card>
</div>