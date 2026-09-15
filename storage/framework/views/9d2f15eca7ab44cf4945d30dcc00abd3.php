<p class="panel-title">Notifikasi</p>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasRole('superadmin')): ?>
    <p class="panel-sub">Pilih aktivitas yang ditampilkan melalui lonceng notifikasi superadmin. Preferensi ini berlaku untuk akun Anda.</p>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') === 'notifications-updated'): ?>
        <p role="status" style="padding:12px;border:1px solid #bbf7d0;border-radius:8px;background:#f0fdf4;color:#166534;margin-bottom:16px;">Pengaturan notifikasi berhasil disimpan.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->has('notifications') || $errors->has('notifications.*')): ?>
        <p role="alert" style="color:#b91c1c;margin-bottom:16px;">Pengaturan belum tersimpan. Pastikan semua pilihan bernilai aktif atau nonaktif.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php
        $notificationPreferences = app(\App\Services\SuperAdminNotifications::class)->preferences($user);
    ?>
    <form id="form-notifikasi" method="POST" action="<?php echo e(route('profile.notifications.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        <input type="hidden" name="_settings_tab" value="notifikasi">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Services\SuperAdminNotifications::OPTIONS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <label class="notification-option" for="notification-<?php echo e($key); ?>">
                <span>
                    <span class="notification-option-title"><?php echo e($option['label']); ?></span>
                    <span class="notification-option-description" id="notification-<?php echo e($key); ?>-description"><?php echo e($option['description']); ?></span>
                </span>
                <span class="notification-switch">
                    <input type="hidden" name="notifications[<?php echo e($key); ?>]" value="0">
                    <input type="checkbox" id="notification-<?php echo e($key); ?>" name="notifications[<?php echo e($key); ?>]" value="1"
                           aria-describedby="notification-<?php echo e($key); ?>-description"
                           <?php if(old('notifications.'.$key, $notificationPreferences[$key])): echo 'checked'; endif; ?>>
                    <span class="notification-switch-track" aria-hidden="true"></span>
                </span>
            </label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <p style="font-size:12px;color:#64748b;line-height:1.6;margin-top:18px;">Lonceng menampilkan maksimal 10 aktivitas terbaru dalam 7 hari terakhir. Menonaktifkan pilihan hanya menyembunyikan notifikasinya; riwayat audit dan hasil impor tetap tersimpan.</p>
    </form>
<?php else: ?>
    <p class="panel-sub">Notifikasi akademik dikelola di masing-masing aplikasi. Buka menu notifikasi pada aplikasi yang Anda gunakan.</p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views/profile/partials/settings-panel-notifikasi.blade.php ENDPATH**/ ?>