{{-- Floating Bulk Action Bar --}}
<div id="bulkActionBar" class="hidden mb-4 p-3 bg-slate-900 rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-2 duration-300 shadow-lg border border-slate-800">
    <div class="flex items-center gap-4 ml-2">
        <div class="flex items-center justify-center w-6 h-6 bg-blue-500 rounded-lg">
            <span class="material-symbols-outlined text-white" style="font-size: 14px">check_circle</span>
        </div>
        <span class="text-[11px] font-black text-white uppercase tracking-widest">
            <span id="selectedCount" class="text-blue-400 text-sm mr-1">0</span> User Terpilih
        </span>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openBulkDeleteHybrid()" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-[10px] font-black uppercase px-4 py-2 rounded-xl transition-all shadow-sm group">
            <span class="material-symbols-outlined group-hover:rotate-12 transition-transform" style="font-size: 16px">delete_sweep</span>
            Hapus Massal
        </button>
        <div class="w-px h-4 bg-slate-700 mx-1"></div>
        <button onclick="deselectAll()" class="text-slate-400 hover:text-white text-[10px] font-bold uppercase px-3 transition-colors">
            Batal
        </button>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/50">
                    {{-- Checkbox Header --}}
                    <th class="px-4 py-3 text-left w-10">
                        <input type="checkbox" id="selectAll" 
                            class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-all cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">User Info</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Roles & Access</th>
                    <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">Timestamps</th>
                    <th class="px-4 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                @php $isMe = $user->id === auth()->id(); @endphp
                <tr class="hover:bg-slate-50/80 transition-colors user-row {{ $isMe ? 'opacity-70' : '' }}">
                    {{-- Row Checkbox --}}
                    <td class="px-4 py-3">
                        @if(!$isMe)
                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" 
                            class="user-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-all cursor-pointer">
                        @else
                        <div class="w-4 h-4 rounded bg-slate-100 border border-slate-200 cursor-not-allowed flex items-center justify-center" title="Akun Anda">
                            <span class="material-symbols-outlined text-slate-400" style="font-size: 10px">lock</span>
                        </div>
                        @endif
                    </td>

                    {{-- User Info --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-slate-800 font-bold text-xs truncate">{{ $user->name }} @if($isMe)<span class="text-[9px] text-blue-500 font-normal ml-1">(Anda)</span>@endif</p>
                                    @if($user->isSuspended())
                                        <span class="flex-shrink-0 w-2 h-2 rounded-full bg-red-500 animate-pulse" title="Suspended"></span>
                                    @endif
                                </div>
                                <p class="text-slate-400 text-[10px] truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Roles --}}
                    <td class="px-4 py-3">
                        <div class="flex gap-1 flex-wrap">
                            @forelse($user->roles as $role)
                                @php
                                    $roleColors = match(strtolower($role->name)) {
                                        'superadmin' => 'bg-purple-50 text-purple-600 border-purple-100',
                                        'dosen' => 'bg-green-50 text-green-600 border-green-100',
                                        'mahasiswa' => 'bg-orange-50 text-orange-600 border-orange-100',
                                        'admin_banksoal' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                        'admin_capstone' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                        default => 'bg-blue-50 text-blue-600 border-blue-100',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black border uppercase {{ $roleColors }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-slate-300 text-[10px] italic underline decoration-dotted">No Role</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Timestamps --}}
                    <td class="px-4 py-3">
                        <div class="space-y-0.5">
                            <p class="text-slate-500 text-[10px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-slate-300" style="font-size:12px">calendar_today</span>
                                {{ $user->created_at->format('d/m/y') }}
                            </p>
                            <p class="text-slate-400 text-[10px] italic">
                                Log: {{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditInfo({{ json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'is_superadmin' => $user->hasRole('superadmin')]) }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition border border-transparent hover:border-blue-100" title="Edit Info">
                                <span class="material-symbols-outlined" style="font-size:16px">edit</span>
                            </button>

                            @if(!$isMe)
                                <form method="POST" action="{{ route('superadmin.users.force-logout', $user) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Force logout user ini?')"
                                        class="p-1.5 text-orange-500 hover:bg-orange-50 rounded-md transition border border-transparent hover:border-orange-100" title="Force Logout">
                                        <span class="material-symbols-outlined" style="font-size:16px">logout</span>
                                    </button>
                                </form>

                                @if($user->isSuspended())
                                    <form method="POST" action="{{ route('superadmin.users.unsuspend', $user) }}">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-md transition border border-transparent hover:border-emerald-100" title="Unsuspend">
                                            <span class="material-symbols-outlined" style="font-size:16px">lock_open</span>
                                        </button>
                                    </form>
                                @elseif(!$user->hasRole('superadmin'))
                                    <button onclick="openSuspendModal({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-md transition border border-transparent hover:border-red-100" title="Suspend">
                                        <span class="material-symbols-outlined" style="font-size:16px">block</span>
                                    </button>
                                @endif

                                <button type="button" 
                                    onclick="openDeleteHybrid({{ json_encode(['id' => $user->id, 'name' => $user->name]) }})"
                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition border border-transparent hover:border-red-100" 
                                    title="Hapus User">
                                    <span class="material-symbols-outlined" style="font-size:16px">delete</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-xs italic">Data user tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>