<p class="panel-title">Notifikasi</p>
@if($user->hasRole('superadmin'))
    <p class="panel-sub">Pilih aktivitas yang ditampilkan melalui lonceng notifikasi superadmin. Preferensi ini berlaku untuk akun Anda.</p>
    @if(session('status') === 'notifications-updated')
        <p role="status" style="padding:12px;border:1px solid #bbf7d0;border-radius:8px;background:#f0fdf4;color:#166534;margin-bottom:16px;">Pengaturan notifikasi berhasil disimpan.</p>
    @endif
    @if($errors->has('notifications') || $errors->has('notifications.*'))
        <p role="alert" style="color:#b91c1c;margin-bottom:16px;">Pengaturan belum tersimpan. Pastikan semua pilihan bernilai aktif atau nonaktif.</p>
    @endif
    @php
        $notificationPreferences = app(\App\Services\SuperAdminNotifications::class)->preferences($user);
    @endphp
    <form id="form-notifikasi" method="POST" action="{{ route('profile.notifications.update') }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="_settings_tab" value="notifikasi">
        @foreach(\App\Services\SuperAdminNotifications::OPTIONS as $key => $option)
            <label class="notification-option" for="notification-{{ $key }}">
                <span>
                    <span class="notification-option-title">{{ $option['label'] }}</span>
                    <span class="notification-option-description" id="notification-{{ $key }}-description">{{ $option['description'] }}</span>
                </span>
                <span class="notification-switch">
                    <input type="hidden" name="notifications[{{ $key }}]" value="0">
                    <input type="checkbox" id="notification-{{ $key }}" name="notifications[{{ $key }}]" value="1"
                           aria-describedby="notification-{{ $key }}-description"
                           @checked(old('notifications.'.$key, $notificationPreferences[$key]))>
                    <span class="notification-switch-track" aria-hidden="true"></span>
                </span>
            </label>
        @endforeach
        <p style="font-size:12px;color:#64748b;line-height:1.6;margin-top:18px;">Lonceng menampilkan maksimal 10 aktivitas terbaru dalam 7 hari terakhir. Menonaktifkan pilihan hanya menyembunyikan notifikasinya; riwayat audit dan hasil impor tetap tersimpan.</p>
    </form>
@else
    <p class="panel-sub">Notifikasi akademik dikelola di masing-masing aplikasi. Buka menu notifikasi pada aplikasi yang Anda gunakan.</p>
@endif
<style>
    .notification-option { display:flex; align-items:center; justify-content:space-between; gap:24px; padding:16px 0; border-bottom:1px solid #f1f5f9; cursor:pointer; }
    .notification-option-title { display:block; font-size:13px; font-weight:600; color:#0f172a; margin-bottom:3px; }
    .notification-option-description { display:block; font-size:12px; color:#64748b; line-height:1.6; }
    .notification-switch { position:relative; flex-shrink:0; width:40px; height:22px; }
    .notification-switch input[type=checkbox] { position:absolute; inset:0; width:100%; height:100%; opacity:0; z-index:1; cursor:pointer; margin:0; }
    .notification-switch-track { display:block; width:40px; height:22px; border-radius:99px; background:#cbd5e1; transition:background .15s; }
    .notification-switch-track::after { content:''; position:absolute; left:2px; top:2px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 3px #0002; transition:transform .15s; }
    .notification-switch input:checked + .notification-switch-track { background:#1e1b4b; }
    .notification-switch input:checked + .notification-switch-track::after { transform:translateX(18px); }
    .notification-switch input:focus-visible + .notification-switch-track { outline:2px solid #475569; outline-offset:3px; }
</style>
