<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Daftar Dosen Pengampu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Daftar Dosen Pengampu']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Dosen Pengampu</h1>
        <p class="mp-page-sub">Data dosen diambil dari tabel lecturers</p>
    </div>
</div>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Pencarian Dosen</span>
    <span class="sec-rule"></span>
</div>


<form method="GET" class="flex gap-2 flex-shrink-0">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama / email / NIP..."
           class="mp-input flex-1">
    <button type="submit" class="mp-btn primary sm">Cari</button>
    <a href="<?php echo e(route('eoffice.manprak.admin.dosen.index')); ?>" class="mp-btn secondary sm">Reset</a>
</form>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Data Dosen</span>
    <span class="sec-rule"></span>
</div>


<div class="mp-card flex-1 min-h-0">
    <div style="flex-shrink:0;">
        <div style="display:flex;align-items:center;padding:10px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
            <div class="mp-th flex-1">Nama Dosen</div>
            <div class="mp-th" style="width:180px;">Email</div>
            <div class="mp-th" style="width:140px;">No. Pegawai</div>
            <div class="mp-th" style="width:110px;">Praktikum Diampu</div>
            <div class="mp-th" style="width:80px;">Bergabung</div>
        </div>
    </div>

    <div class="overflow-y-auto flex-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dosens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecturer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $user = $lecturer->user; ?>
        <div class="mp-tr" style="display:flex;align-items:center;padding:12px 20px;">
            <div class="flex-1 flex items-center gap-[10px] min-w-0 pr-3">
                <div class="mp-av violet flex-shrink-0">
                    <?php echo e(strtoupper(substr($user?->name ?? 'D', 0, 2))); ?>

                </div>
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate"><?php echo e($user?->name ?? '—'); ?></div>
                </div>
            </div>
            <div style="width:180px;font-size:12px;color:#666D80;" class="truncate"><?php echo e($user?->email ?? '—'); ?></div>
            <div style="width:140px;font-size:12px;font-family:monospace;color:#353849;"><?php echo e($lecturer->employee_number); ?></div>
            <div style="width:110px;text-align:center;">
                <?php $jumlah = \Modules\EOffice\Models\Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $lecturer->user_id))->count(); ?>
                <span class="mp-badge primary sm"><?php echo e($jumlah); ?> praktikum</span>
            </div>
            <div style="width:80px;font-size:11px;color:#666D80;">
                <?php echo e($lecturer->created_at?->format('M Y') ?? '—'); ?>

            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div style="padding:64px 20px;text-align:center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <div style="font-size:13px;color:#666D80;">Belum ada dosen terdaftar di tabel lecturers.</div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dosens->hasPages()): ?>
    <div style="padding:12px 20px;border-top:1px solid #DFE1E7;flex-shrink:0;"><?php echo e($dosens->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\dosen.blade.php ENDPATH**/ ?>