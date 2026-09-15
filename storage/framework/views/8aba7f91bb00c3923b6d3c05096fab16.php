<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Absensi Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Absensi Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">Catat kehadiran mahasiswa per sesi modul · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
    <div class="mp-page-actions">
        <span style="font-size:12px;color:#666D80;">Sesi:</span>
        <input type="date" id="tglFilter" value="<?php echo e(date('Y-m-d')); ?>" class="mp-input">
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-3 flex-shrink-0 flex-wrap">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modulDiampu ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <a href="<?php echo e(route('eoffice.manprak.asprak.absensi.show', $m->id)); ?>"
       class="<?php echo e(request()->route('modulId') == $m->id ? 'mp-btn primary md' : 'mp-btn secondary md'); ?>"
       style="text-decoration:none;">
        <?php echo e($m->nama); ?>

    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div style="font-size:13px;color:#808897;">Belum ada modul.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($modul) && isset($praktikan)): ?>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title"><?php echo e($modul->nama); ?></span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e($praktikan->count()); ?> mahasiswa</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header" style="flex-shrink:0;">
        <div>
            <span class="mp-card-title"><?php echo e($modul->nama); ?></span>
            <div style="font-size:12px;color:#666D80;margin-top:2px;"><?php echo e($praktikan->count()); ?> mahasiswa terdaftar</div>
        </div>
        <div class="right">
            <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.absensi.store', $modul->id)); ?>" id="absensiForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="tanggal" value="<?php echo e(date('Y-m-d')); ?>">
                <button type="submit" class="mp-btn primary md">Simpan Absensi</button>
            </form>
        </div>
    </div>

    <div style="display:flex;align-items:center;padding:8px 20px;background:#F9FAFB;border-bottom:1px solid #DFE1E7;flex-shrink:0;">
        <div class="mp-th" style="width:50px;">No</div>
        <div class="mp-th flex-1">Nama Mahasiswa</div>
        <div class="mp-th" style="width:120px;">NIM</div>
        <div class="mp-th text-center" style="width:80px;">Hadir</div>
        <div class="mp-th text-center" style="width:80px;">Izin</div>
        <div class="mp-th text-center" style="width:80px;">Tidak Hadir</div>
        <div class="mp-th" style="width:150px;">Keterangan</div>
    </div>

    <div class="overflow-y-auto flex-1">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $prak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $existing = $absensiHariIni[$prak->id] ?? null; ?>
        <div class="mp-tr" style="display:flex;align-items:center;padding:11px 20px;">
            <div style="width:50px;font-size:12px;color:#666D80;"><?php echo e($i + 1); ?></div>
            <div class="flex-1 flex items-center gap-[10px] pr-3 min-w-0">
                <div class="mp-av yellow"><?php echo e(strtoupper(substr($prak->user?->name ?? 'M', 0, 2))); ?></div>
                <span style="font-size:13px;font-weight:500;color:#0D0D12;" class="truncate"><?php echo e($prak->user?->name); ?></span>
            </div>
            <div style="width:120px;font-size:12px;color:#666D80;"><?php echo e($prak->user?->student_number ?? '—'); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['hadir','izin','tidak_hadir']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="flex justify-center" style="width:80px;">
                <input type="radio" form="absensiForm"
                       name="absensi[<?php echo e($prak->id); ?>][status]"
                       value="<?php echo e($status); ?>"
                       <?php echo e(($existing?->status ?? 'hadir') === $status ? 'checked' : ''); ?>

                       class="w-4 h-4 cursor-pointer" style="accent-color:#0B266E;">
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="width:150px;">
                <input type="text" form="absensiForm"
                       name="absensi[<?php echo e($prak->id); ?>][keterangan]"
                       value="<?php echo e($existing?->keterangan ?? ''); ?>"
                       placeholder="Opsional..."
                       class="mp-input w-full" style="font-size:12px;padding:4px 8px;">
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="mp-card flex-1" style="display:flex;align-items:center;justify-content:center;min-height:200px;">
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
            <path d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>
        </svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Pilih modul di atas untuk mulai mengisi absensi</div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\asprak\absensi.blade.php ENDPATH**/ ?>