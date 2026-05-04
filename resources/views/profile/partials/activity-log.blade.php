{{-- resources/views/profile/partials/activity-log.blade.php --}}
@php
    $logs = \App\Models\AuditLog::where('user_id', auth()->id())
        ->latest('created_at')
        ->limit(3)
        ->get();

    $meta = [
        'LOGIN'  => ['icon' => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>',      'bg' => 'bg-[#DDF2EE]', 'color' => '#287F6E'],
        'LOGOUT' => ['icon' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',     'bg' => 'bg-[#F0F1F4]', 'color' => '#808897'],
        'CREATE' => ['icon' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>',                        'bg' => 'bg-[#E8EDF7]', 'color' => '#0B266E'],
        'UPDATE' => ['icon' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>', 'bg' => 'bg-[#F9ECCB]', 'color' => '#956321'],
        'DELETE' => ['icon' => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>',  'bg' => 'bg-[#FADAE1]', 'color' => '#DF1C41'],
        'VIEW'   => ['icon' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',                                               'bg' => 'bg-[#F0F1F4]', 'color' => '#A4ABB8'],
    ];
@endphp

@if($logs->isEmpty())
    <div class="flex flex-col items-center py-5 text-[#C1C7CF]">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        <p class="text-[11px] mt-2 text-[#A4ABB8]">Belum ada aktivitas.</p>
    </div>
@else
    <div>
        @foreach($logs as $log)
        @php $m = $meta[$log->action] ?? $meta['VIEW']; @endphp
        <div class="flex items-start gap-3 py-2.5 {{ !$loop->last ? 'border-b border-[#F0F1F4]' : '' }}">
            <div class="w-7 h-7 rounded-lg {{ $m['bg'] }} flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $m['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    {!! $m['icon'] !!}
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[12px] text-[#353849] leading-snug truncate font-medium">{{ $log->description }}</p>
                <p class="text-[11px] text-[#A4ABB8] mt-0.5">
                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
@endif
