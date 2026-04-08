<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F8F9FA] p-8">
        <div class="max-w-full mx-auto">

            {{-- Header --}}
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-[#1A1C1E] tracking-tight">Audit Log System</h1>
                    <p class="text-[#6C757D] text-[12px] mt-1 font-semibold">
                        Total <span class="text-[#5E53F4] font-medium">{{ number_format($logs->total()) }}</span> aktivitas tercatat dalam sistem
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openBulkDeleteModal()" 
                        class="inline-flex items-center gap-2 bg-white hover:bg-rose-50 text-rose-600 font-medium px-5 py-2.5 rounded-xl transition-all text-[11px] border border-rose-100 shadow-sm uppercase tracking-widest active:scale-95">
                        <span class="material-symbols-outlined" style="font-size:18px">delete_sweep</span>
                        Hapus Massal
                    </button>
                </div>
            </div>

            {{-- Floating Bulk Action Bar --}}
            <div id="bulkActionBar" class="hidden mb-6 p-4 bg-[#1A1C1E] rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-2 duration-300 shadow-xl border border-slate-800">
                <div class="flex items-center gap-4 ml-2">
                    <div class="flex items-center justify-center w-8 h-8 bg-[#5E53F4] rounded-lg shadow-lg shadow-[#5E53F4]/20">
                        <span class="material-symbols-outlined text-white" style="font-size: 18px">check_circle</span>
                    </div>
                    <span class="text-[13px] font-medium text-white tracking-wide">
                        <span id="selectedCount" class="text-[#D1BFFF] text-base mr-1">0</span> Logs Selected
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openBulkDeleteModal()" class="flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-[11px] font-medium uppercase tracking-widest px-5 py-2.5 rounded-xl transition-all shadow-sm active:scale-95 group">
                        <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-size: 18px">delete_sweep</span>
                        Hapus Terpilih
                    </button>
                    <div class="w-px h-5 bg-slate-700 mx-1"></div>
                    <button onclick="deselectAll()" class="text-slate-400 hover:text-white text-[11px] font-medium uppercase tracking-widest px-4 transition-colors">
                        Batal
                    </button>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white border border-[#DEE2E6] rounded-2xl p-5 mb-6 shadow-sm">
                <form method="GET" action="{{ route('superadmin.audit-logs') }}" id="auditFilterForm">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        
                        {{-- Module Filter (Alpine.js) --}}
                        <div class="md:col-span-3" x-data="{ 
                            open: false, 
                            selected: '{{ request('module', '') }}',
                            modules: { '': 'Semua Modul', @foreach($modules as $mod) '{{ $mod }}': '{{ strtoupper(str_replace('_', ' ', $mod)) }}', @endforeach }
                        }">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Modul System</label>
                            <div class="relative">
                                <input type="hidden" name="module" :value="selected">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                                    <span x-text="modules[selected]"></span>
                                    <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                                </button>
                                <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                                    <template x-for="(label, value) in modules" :key="value">
                                        <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                            class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                            :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                            <span x-text="label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Action Filter (Alpine.js) --}}
                        <div class="md:col-span-2" x-data="{ 
                            open: false, 
                            selected: '{{ request('action', '') }}',
                            actions: { '': 'Semua Action', @foreach($actions as $act) '{{ $act }}': '{{ strtoupper($act) }}', @endforeach }
                        }">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Tipe Aksi</label>
                            <div class="relative">
                                <input type="hidden" name="action" :value="selected">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                                    <span x-text="actions[selected]"></span>
                                    <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                                </button>
                                <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1">
                                    <template x-for="(label, value) in actions" :key="value">
                                        <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                            class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                            :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                            <span x-text="label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- User Filter (Alpine.js) --}}
                        <div class="md:col-span-3" x-data="{ 
                            open: false, 
                            selected: '{{ request('user_id', '') }}',
                            users: { '': 'Semua User', @foreach($users as $u) '{{ $u->id }}': '{{ $u->name }}', @endforeach }
                        }">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Pelaku (User)</label>
                            <div class="relative">
                                <input type="hidden" name="user_id" :value="selected">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full flex items-center justify-between bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-xs text-[#1A1C1E] focus:border-[#5E53F4] transition-all outline-none font-medium">
                                    <span x-text="users[selected]" class="truncate mr-2"></span>
                                    <span class="material-symbols-outlined text-[#ADB5BD] transition-transform" :class="open ? 'rotate-180' : ''" style="font-size:18px">expand_more</span>
                                </button>
                                <div x-show="open" x-transition class="absolute left-0 top-full mt-2 w-full bg-white border border-[#DEE2E6] rounded-xl shadow-2xl z-[50] overflow-hidden py-1 max-h-60 overflow-y-auto scrollbar-thin">
                                    <template x-for="(label, value) in users" :key="value">
                                        <button type="button" @click="selected = value; open = false; $nextTick(() => $el.closest('form').submit())" 
                                            class="w-full text-left px-4 py-2 text-xs transition-colors hover:bg-slate-50"
                                            :class="selected === value ? 'text-[#5E53F4] font-medium bg-[#5E53F4]/5' : 'text-[#495057]'">
                                            <span x-text="label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Date From --}}
                        <div class="md:col-span-2">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-[#1A1C1E] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs font-medium">
                        </div>

                        {{-- Date To --}}
                        <div class="md:col-span-2">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl px-4 py-2.5 text-[#1A1C1E] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs font-medium">
                        </div>

                        {{-- Search --}}
                        <div class="md:col-span-9">
                            <label class="block text-[#6C757D] text-[10px] font-medium uppercase tracking-tight mb-2 ml-1">Pencarian Deskripsi</label>
                            <div class="relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#ADB5BD] group-focus-within:text-[#5E53F4] transition-colors" style="font-size:18px">search</span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari kata kunci aktivitas..."
                                    class="w-full bg-[#F8F9FA] border border-[#DEE2E6] rounded-xl pl-11 pr-4 py-2.5 text-[#1A1C1E] placeholder-[#ADB5BD] focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] outline-none transition-all text-xs">
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="md:col-span-3 flex items-end gap-2 pb-0.5">
                            <button type="submit" class="flex-1 bg-[#5E53F4] hover:bg-[#4e44e0] active:scale-[0.98] text-white font-medium py-2.5 px-4 rounded-xl transition-all text-xs shadow-sm shadow-[#5E53F4]/20 uppercase tracking-widest">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @php
                // Mengambil data real-time untuk dashboard log
                $allOnline = \App\Models\User::with('roles')->where('is_online', \Illuminate\Support\Facades\DB::raw('true'))->get();
                $allSuspended = \App\Models\User::with('roles')->whereNotNull('suspended_at')->get();

                // Limit 3 data agar tetap compact seperti di Manajemen User
                $onlineUsers = $allOnline->take(3);
                $suspendedUsers = $allSuspended->take(3);
                
                $hasOnline = $allOnline->isNotEmpty();
                $hasSuspended = $allSuspended->isNotEmpty();
            @endphp

            <div class="grid grid-cols-1 @if($hasOnline && $hasSuspended) md:grid-cols-2 @endif gap-5 mb-6">
                {{-- 1. Card Online Users --}}
                @if($hasOnline)
                <div class="bg-emerald-50/30 border border-emerald-100 rounded-2xl p-4 shadow-sm h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <h2 class="text-[11px] font-bold text-emerald-800 uppercase tracking-widest">
                                Online ({{ $allOnline->count() }})
                            </h2>
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
                            <div class="bg-white border border-emerald-100/50 rounded-xl p-2.5 flex items-center justify-between gap-3 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/5 transition-all group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0">
                                        <x-ui.avatar 
                                            size="sm" 
                                            :src="$onlineUser->avatar_url" 
                                            :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[16px]\'>admin_panel_settings</span>' : $initials)" 
                                            class="border-2 border-white shadow-sm {{ $avatarColors }}" 
                                        />
                                        <span class="absolute bottom-0 right-0 size-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[12px] font-bold text-slate-800 truncate tracking-tight group-hover:text-emerald-700 transition-colors">{{ $onlineUser->name }}</p>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-tighter bg-emerald-50 px-1 rounded">Active Now</span>
                                            <span class="text-[10px] text-slate-400 font-medium">•</span>
                                            <span class="text-[9px] text-slate-500 uppercase font-semibold tracking-tighter">{{ $onlineUser->roles->first()->name ?? 'User' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if(!$isSuperadmin)
                                        <button type="button" onclick="openSuspendModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined" style="font-size:16px">block</span>
                                        </button>
                                    @endif
                                    <button type="button" onclick="openForceLogoutModal({ id: '{{ $onlineUser->id }}', name: '{{ $onlineUser->name }}' })" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined" style="font-size:16px">logout</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- 2. Card Suspended Users --}}
                @if($hasSuspended)
                <div class="bg-rose-50/30 border border-rose-100 rounded-2xl p-4 shadow-sm h-full">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                            <h2 class="text-[11px] font-bold text-rose-800 uppercase tracking-widest">
                                Suspended ({{ $allSuspended->count() }})
                            </h2>
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
                            <div class="bg-white border border-rose-100/50 rounded-xl p-2.5 flex items-center justify-between gap-3 hover:border-rose-300 hover:shadow-md hover:shadow-rose-500/5 transition-all group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0 opacity-60 grayscale-[0.5]">
                                        <x-ui.avatar 
                                            size="sm" 
                                            :src="$suspendedUser->avatar_url" 
                                            :fallback="new \Illuminate\Support\HtmlString($isSuperadmin ? '<span class=\'material-symbols-outlined !text-[16px]\'>admin_panel_settings</span>' : $initials)" 
                                            class="border-2 border-white shadow-sm {{ $avatarColors }}" 
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[12px] font-bold text-slate-800 truncate tracking-tight line-through decoration-rose-300 opacity-70 group-hover:text-rose-700 transition-colors">
                                            {{ $suspendedUser->name }}
                                        </p>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[9px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded uppercase tracking-tighter border border-rose-100">Blocked</span>
                                            <span class="text-[9px] text-rose-400 font-medium truncate italic max-w-[100px]">
                                                {{ $suspendedUser->suspension_reason ?? 'Policy Violation' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('superadmin.users.unsuspend', $suspendedUser) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors shadow-sm bg-white border border-emerald-100 active:scale-95" title="Unsuspend">
                                        <span class="material-symbols-outlined" style="font-size:16px">lock_open</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Table Audit Logs --}}
            <div class="bg-white border border-[#DEE2E6] rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-[#DEE2E6] bg-[#F8F9FA]">
                                <th class="px-5 py-4 text-left w-12">
                                    <input type="checkbox" id="selectAllLogs" 
                                        class="size-4 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                                </th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Timestamp</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">User Identity</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">System Module</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Action Type</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Activity Description</th>
                                <th class="px-5 py-4 text-center text-[11px] font-semibold text-[#6C757D] uppercase tracking-[0.15em]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F8F9FA]">
                            @forelse($logs as $log)
                            <tr class="hover:bg-[#F8F9FA]/50 transition-colors group">
                                <td class="px-5 py-4">
                                    <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" 
                                        class="log-checkbox size-4 rounded border-[#DEE2E6] text-[#5E53F4] focus:ring-[#5E53F4]/20 transition-all cursor-pointer">
                                </td>
                                
                                {{-- Timestamp --}}
                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[#1A1C1E] font-semibold text-[11px] tracking-tight">{{ $log->created_at->format('d M Y') }}</span>
                                        <span class="text-[#6C757D] text-[10px] font-medium">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>

                                {{-- Initiator (User Identity) --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-4">
                                        @if($log->user)
                                            @php
                                                $user = $log->user;
                                                $isSuperadmin = $user->hasRole('superadmin');
                                                $initials = strtoupper(substr($user->name, 0, 1));
                                                
                                                $avatarColors = $isSuperadmin 
                                                    ? '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]' 
                                                    : '!bg-[#F8F9FA] !text-[#6C757D] border-[#DEE2E6]';
                                            @endphp

                                            <div class="relative shrink-0">
                                                <div class="size-9 rounded-full flex items-center justify-center border-2 border-white shadow-sm overflow-hidden {{ $avatarColors }}">
                                                    @if($user->avatar_url)
                                                        <img src="{{ $user->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                                                    @elseif($isSuperadmin)
                                                        <span class="material-symbols-outlined !text-[18px]">admin_panel_settings</span>
                                                    @else
                                                        <span class="text-[11px] font-semibold uppercase">{{ $initials }}</span>
                                                    @endif
                                                </div>
                                                @if($user->is_online)
                                                    <span class="absolute bottom-0 right-0 size-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <p class="text-[12px] font-bold text-[#1A1C1E] truncate tracking-tight">{{ $user->name }}</p>
                                                <p class="text-[#6C757D] text-[10px] font-medium truncate">{{ $user->email }}</p>
                                            </div>
                                        @else
                                            <div class="size-9 rounded-full bg-slate-50 border-2 border-white shadow-sm flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-slate-400 text-[18px]">settings_suggest</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[#1A1C1E] text-[12px] font-bold tracking-tight">System</p>
                                                <p class="text-[#6C757D] text-[10px] font-medium italic">Automated Task</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Module Badge --}}
                                <td class="px-5 py-4">
                                    @php
                                        $moduleStyle = match($log->module ?? '') {
                                            'auth'                 => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'bank_soal'            => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'capstone'             => 'bg-sky-50 text-sky-700 border-sky-100',
                                            'eoffice'              => 'bg-purple-50 text-purple-700 border-purple-100',
                                            'user_management'      => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                            default                => 'bg-slate-50 text-slate-600 border-slate-100',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $moduleStyle }}">
                                        {{ str_replace('_', ' ', $log->module ?? 'N/A') }}
                                    </span>
                                </td>

                                {{-- Action Badge --}}
                                <td class="px-5 py-4">
                                    @php
                                        $actionColor = match(strtolower($log->action ?? '')) {
                                            'create' => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                            'update' => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                            'delete' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            'login'  => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                            'logout' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            default  => 'bg-slate-50 text-slate-500 border-slate-100'
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $actionColor }}">
                                        {{ $log->action ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Description --}}
                                <td class="px-5 py-4">
                                    <p class="text-[#495057] font-medium text-[12px] max-w-xs line-clamp-2 leading-relaxed tracking-tight" title="{{ $log->description ?? '-' }}">
                                        {{ $log->description ?? '-' }}
                                    </p>
                                </td>

                                {{-- User Status --}}
                                <td class="px-5 py-4 text-center">
                                    @if($log->user)
                                        <div class="flex items-center justify-center">
                                            @if($log->user->is_online)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-100 uppercase tracking-tighter">
                                                    <span class="size-1 bg-emerald-500 rounded-full animate-pulse"></span> ONLINE
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 text-slate-500 text-[9px] font-bold border border-slate-100 uppercase tracking-tighter">
                                                    OFFLINE
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">SYSTEM</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="material-symbols-outlined text-[#DEE2E6]" style="font-size: 56px">history</span>
                                        <p class="text-[#ADB5BD] text-[11px] font-bold uppercase tracking-[0.2em]">Tidak ada aktivitas tercatat</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div id="paginationWrapper" class="mt-8 w-full border-t border-[#DEE2E6] pt-5">
                {{ $logs->links() }}
            </div>

        </div>

        {{-- Modals from User Management (Reused here for Active User Actions) --}}
        @include('superadmin.users._modal_force_logout')
        @include('superadmin.users._modal_suspend')

        {{-- Modal Bulk Delete --}}
        <div id="modalBulkDeleteAudit" class="hidden fixed inset-0 bg-[#1A1C1E]/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full animate-in fade-in zoom-in-95 duration-300">
                <div class="px-6 py-5 border-b border-[#DEE2E6] flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center border border-rose-100">
                        <span class="material-symbols-outlined text-rose-600" style="font-size:20px">warning</span>
                    </div>
                    <h3 class="text-lg font-medium text-[#1A1C1E]">Hapus Audit Logs?</h3>
                </div>

                <div class="px-6 py-5">
                    <p class="text-[#1A1C1E] text-sm font-medium mb-1">Anda akan menghapus <span id="selectedCountText" class="font-medium text-rose-600">0</span> log.</p>
                    <p class="text-[#6C757D] text-xs leading-relaxed">Tindakan ini tidak dapat dibatalkan. Pastikan Anda yakin sebelum melanjutkan penghapusan permanen.</p>
                </div>

                <div class="px-6 py-4 bg-[#F8F9FA] rounded-b-2xl flex gap-3 border-t border-[#DEE2E6]">
                    <button onclick="closeModal('modalBulkDeleteAudit')" 
                        class="flex-1 bg-white hover:bg-slate-50 border border-[#DEE2E6] text-[#6C757D] font-medium py-2.5 px-4 rounded-xl transition-all text-[11px] uppercase tracking-widest shadow-sm">
                        Batal
                    </button>
                    <form id="formBulkDeleteAudit" method="POST" action="{{ route('superadmin.audit-logs.bulk-delete') }}" class="flex-1">
                        @csrf
                        <div id="selectedIdsContainer"></div>
                        <button type="submit" 
                            class="w-full bg-rose-500 hover:bg-rose-600 text-white font-medium py-2.5 px-4 rounded-xl transition-all text-[11px] uppercase tracking-widest shadow-sm shadow-rose-500/20 active:scale-95">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    const selectedLogs = new Set();

    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.log-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionBar');
        const selectedCountText = document.getElementById('selectedCount');
        
        selectedLogs.clear();
        checkboxes.forEach(cb => selectedLogs.add(cb.value));
        
        if (selectedCountText) selectedCountText.textContent = selectedLogs.size;
        
        if (selectedLogs.size > 0) {
            bulkBar?.classList.replace('hidden', 'flex');
        } else {
            bulkBar?.classList.replace('flex', 'hidden');
        }
    }
    
    function deselectAll() {
        const selectAll = document.getElementById('selectAllLogs');
        if (selectAll) selectAll.checked = false;
        
        document.querySelectorAll('.log-checkbox').forEach(cb => {
            cb.checked = false;
        });
        
        updateBulkBar();
    }
    
    function openBulkDeleteModal() {
        const selectedIds = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(cb => cb.value);
        const container = document.getElementById('selectedIdsContainer');
        
        container.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        
        const selectedCountSpan = document.getElementById('selectedCountText');
        if (selectedCountSpan) selectedCountSpan.textContent = selectedIds.length;
        
        openModal('modalBulkDeleteAudit');
    }

    // ============================================
    // MODAL CONTROL & ACTIVE USERS ACTION 
    // ============================================
    function openModal(id) { 
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.remove('hidden'); 
            document.body.style.overflow = 'hidden'; 
        }
    }
    
    function closeModal(id) { 
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.add('hidden'); 
            document.body.style.overflow = ''; 
        }
    }

    function openForceLogoutModal(data) {
        document.getElementById('formForceLogout').action = `/superadmin/users/${data.id}/force-logout`;
        document.getElementById('logoutTargetName').textContent = data.name;
        openModal('modalForceLogout');
    }

    function openSuspendModal(data) {
        const form = document.getElementById('formSuspend') || document.querySelector('form[action*="suspend"]');
        if(form) form.action = `/superadmin/users/${data.id}/suspend`;
        const nameEl = document.getElementById('suspendTargetName');
        if(nameEl) nameEl.textContent = data.name;
        openModal('modalSuspend');
    }
    
    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selectAllLogs')?.addEventListener('change', function() {
            document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('log-checkbox')) updateBulkBar();
        });
        
        ['select[name="module"]', 'select[name="action"]', 'select[name="user_id"]'].forEach(selector => {
            document.querySelector(selector)?.addEventListener('change', function () {
                this.form.submit();
            });
        });
        
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#paginationWrapper a');
            if (!link) return;
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page') ?? 1;
            const form = document.getElementById('auditFilterForm');
            
            let pageInput = form.querySelector('input[name="page"]');
            if (!pageInput) {
                pageInput = document.createElement('input');
                pageInput.type = 'hidden';
                pageInput.name = 'page';
                form.appendChild(pageInput);
            }
            pageInput.value = page;
            form.submit();
        });
        
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') {
                ['modalBulkDeleteAudit', 'modalForceLogout', 'modalSuspend'].forEach(closeModal);
            }
        });
    });
    </script>
</x-sidebar>
</x-app-layout>