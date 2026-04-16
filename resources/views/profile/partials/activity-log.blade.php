{{-- profile/partials/activity-log.blade.php --}}
@php
    $logs = \App\Models\AuditLog::where('user_id', auth()->id())
        ->latest('created_at')
        ->limit(3)
        ->get();

    $meta = [
        'LOGIN'  => ['icon' => 'login',       'bg' => 'bg-green-100',  'color' => 'text-green-600'],
        'LOGOUT' => ['icon' => 'logout',       'bg' => 'bg-slate-100',  'color' => 'text-slate-500'],
        'CREATE' => ['icon' => 'add_circle',   'bg' => 'bg-blue-100',   'color' => 'text-blue-600'],
        'UPDATE' => ['icon' => 'edit',         'bg' => 'bg-purple-100', 'color' => 'text-purple-600'],
        'DELETE' => ['icon' => 'delete',       'bg' => 'bg-red-100',    'color' => 'text-red-500'],
        'VIEW'   => ['icon' => 'visibility',   'bg' => 'bg-slate-100',  'color' => 'text-slate-400'],
    ];
@endphp

@if($logs->isEmpty())
    <div class="flex flex-col items-center py-5 text-slate-300">
        <span class="material-symbols-outlined text-[28px]">history</span>
        <p class="text-[11px] mt-1">Belum ada aktivitas.</p>
    </div>
@else
    <div class="space-y-0">
        @foreach($logs as $log)
        @php $m = $meta[$log->action] ?? ['icon' => 'info', 'bg' => 'bg-slate-100', 'color' => 'text-slate-400']; @endphp
        <div class="flex items-start gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
            <div class="w-7 h-7 rounded-lg {{ $m['bg'] }} flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined {{ $m['color'] }} text-[13px]">{{ $m['icon'] }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[12px] text-slate-700 leading-snug truncate">{{ $log->description }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
@endif