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
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-[#1A1C1E] font-semibold text-[11px] tracking-tight">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="text-[#6C757D] text-[10px] font-medium">{{ $log->created_at->format('H:i:s') }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-4">
                            @if($log->user)
                                @php
                                    $user = $log->user;
                                    $isSuperadmin = $user->hasRole('superadmin');
                                    $initials = strtoupper(substr($user->name, 0, 1));
                                    $avatarColors = $isSuperadmin ? '!bg-[#F1E9FF] !text-[#5E53F4] border-[#D1BFFF]' : '!bg-[#F8F9FA] !text-[#6C757D] border-[#DEE2E6]';
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
                                    <p class="text-[12px] font-semibold text-[#1A1C1E] truncate tracking-tight">{{ $user->name }}</p>
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
                    <td class="px-5 py-4">
                        @php
                            $moduleStyle = match($log->module ?? '') {
                                'auth' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'bank_soal' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'capstone' => 'bg-sky-50 text-sky-700 border-sky-100',
                                'eoffice' => 'bg-purple-50 text-purple-700 border-purple-100',
                                'user_management' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $moduleStyle }}">
                            {{ str_replace('_', ' ', $log->module ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $actionColor = match(strtolower($log->action ?? '')) {
                                'create' => 'bg-[#E7F9F3] text-[#00C08D] border-[#B2EBD9]',
                                'update' => 'bg-[#FFF9E6] text-[#FFB800] border-[#FFEBB3]',
                                'delete' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'login' => 'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]',
                                'logout' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default => 'bg-slate-50 text-slate-500 border-slate-100'
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border {{ $actionColor }}">
                            {{ $log->action ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-[#495057] font-medium text-[12px] max-w-xs line-clamp-2 leading-relaxed tracking-tight" title="{{ $log->description ?? '-' }}">
                            {{ $log->description ?? '-' }}
                        </p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($log->user)
                            <div class="flex items-center justify-center">
                                @if($log->user->is_online)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-semibold border border-emerald-100 uppercase tracking-tighter">
                                        <span class="size-1 bg-emerald-500 rounded-full animate-pulse"></span> ONLINE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-50 text-slate-500 text-[9px] font-semibold border border-slate-100 uppercase tracking-tighter">
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