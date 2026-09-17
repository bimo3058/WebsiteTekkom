{{-- resources/views/superadmin/notifications/index.blade.php --}}
<x-app-layout>
<x-sidebar :user="auth()->user()">

    <style>
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .notif-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 12px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
        .notif-box { display: flex; flex-direction: column; flex: 1; background: #fff; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
    </style>

    <div class="notif-wrap">
        <div class="notif-box">
            {{-- ── Header ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:#fff;border-bottom:1px solid #D4D5D8;flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <a href="{{ route('superadmin.dashboard') }}"
                       style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;color:#475569;background:#fff;border:1px solid #D0D1D5;border-radius:8px;box-shadow:0 1px 2px rgba(0,0,0,.05);text-decoration:none;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h1 style="font-size:14px; font-weight:800; color:#1E293B; margin:0; text-transform:uppercase; letter-spacing:0.02em;">Pusat Notifikasi</h1>
                </div>
                <a href="{{ route('profile.edit', ['tab' => 'notifikasi']) }}"
                   style="padding:6px 12px;font-size:11px;font-weight:700;color:#fff;background:#0B266E;border:none;border-radius:8px;text-decoration:none; transition:background .15s;"
                   onmouseover="this.style.background='#091958'" onmouseout="this.style.background='#0B266E'">Pengaturan Notifikasi</a>
            </div>

            {{-- ── List Notifikasi ── --}}
            <div style="flex:1; overflow-y:auto; padding:20px;">
                @if($notifications->isEmpty())
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; opacity:0.5; text-align:center;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#CBD5E1; margin-bottom:12px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341c-1.151.12-2.146.61-2.93 1.422L5 11h10z" />
                        </svg>
                        <p style="font-size:14px; font-weight:600; color:#64748B;">Tidak ada notifikasi terbaru</p>
                        <p style="font-size:12px; color:#94A3B8;">Semua aktivitas sistem terpantau dengan baik.</p>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($notifications as $notif)
                            <a href="{{ $notif['url'] }}"
                               style="display:flex; align-items:flex-start; gap:14px; padding:14px; border:1px solid var(--c-border); border-radius:12px; text-decoration:none; transition:all .15s; background:#fff;"
                               onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)'"
                               onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">

                                <div style="width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:{{ str_contains($notif['id'], 'import') ? '#EFF6FF' : '#F1F5F9' }}; flex-shrink:0;">
                                    <svg width="20" height="20" fill="none" stroke="{{ str_contains($notif['id'], 'import') ? '#3B82F6' : '#64748B' }}" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        @if(str_contains($notif['id'], 'import'))
                                            <path d="M12 5v14M5 12h14" />
                                        @else
                                            <path d="M10.29 9.718l.002.001c.22.22.48.42.76.6l1.28 1.28a.998.998 0 001.414 0l1.28-1.28a.998.998 0 00-.282-1.414L12 8.464l-1.71//-1.71//.586-.586a.998.998 0 00-1.414 0L8.586 8.28a.998.998 0 00-.282 1.414z" />
                                            <path d="M12 12v6M10 15h4" />
                                        @endif
                                    </svg>
                                </div>

                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                                        <p style="font-size:13px; font-weight:700; color:#1E293B; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $notif['title'] }}</p>
                                        <span style="font-size:10px; font-weight:600; color:#94A3B8; white-space:nowrap;">{{ $notif['time'] }}</span>
                                    </div>
                                    <p style="font-size:12px; color:#64748B; margin:4px 0 0 0; line-height:1.5; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $notif['description'] }}
                                    </p>
                                </div>
                                <div style="display:flex; align-items:center; padding-left:10px;">
                                    <svg width="16" height="16" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-sidebar>
</x-app-layout>
