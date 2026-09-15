<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pendaftaran Praktikan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pendaftaran Praktikan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mp-page-header">
        <div><h1 class="mp-page-title">Pendaftaran Praktikan</h1><p class="mp-page-sub">Pantau pendaftaran, verifikasi koordinator, dan dokumen IRS mahasiswa.</p></div>
        <a class="mp-btn secondary md" href="<?php echo e(route('eoffice.manprak.admin.daftar-praktikan.index')); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'book']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'book']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Praktikan terdaftar</a>
    </div>
    <?php echo $__env->make('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => 'praktikan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <section class="mp-card">
        <form method="GET" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;padding:18px;">
            <div style="flex:1;min-width:180px;">
                <label for="registration-search" style="display:block;font-size:12px;margin-bottom:6px;">Nama mahasiswa</label>
                <input class="mp-input" id="registration-search" type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari mahasiswa…" maxlength="100">
            </div>
            <div>
                <label for="registration-status" style="display:block;font-size:12px;margin-bottom:6px;">Status verifikasi</label>
                <select class="mp-input mp-select" name="status" id="registration-status">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['' => 'Semua status', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($value); ?>" <?php if(request('status', '') === $value): echo 'selected'; endif; ?>><?php echo e($text); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
            <button type="submit" class="mp-btn primary md">Terapkan</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search', 'status'])): ?>
                <a class="mp-btn secondary md" href="<?php echo e(route('eoffice.manprak.admin.pendaftaran-praktikan.index')); ?>">Reset</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
        <div class="mp-table-wrap" tabindex="0" role="region" aria-label="Daftar pendaftaran praktikan">
            <table class="mp-table">
                <thead><tr><th scope="col">Mahasiswa</th><th scope="col">Praktikum</th><th scope="col">Tanggal daftar</th><th scope="col">Status</th><th scope="col">Dokumen IRS</th><th scope="col">Aksi</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td><strong><?php echo e($p->user?->name ?? 'Akun tidak tersedia'); ?></strong><div style="font-size:12px;color:var(--c-fg-sec);"><?php echo e($p->user?->student?->student_number ?? $p->user?->external_id ?? 'NIM belum tercatat'); ?></div></td>
                            <td><?php echo e($p->praktikum?->nama ?? 'Tidak tersedia'); ?></td>
                            <td><?php echo e($p->created_at?->format('d M Y') ?? 'Belum tercatat'); ?></td>
                            <td><span class="mp-badge <?php echo e(['approved' => 'success', 'rejected' => 'error'][$p->status] ?? 'warning'); ?> sm"><?php echo e(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$p->status] ?? $p->status); ?></span></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->irs_path): ?>
                                    <a class="mp-btn secondary sm" target="_blank" rel="noopener" aria-label="Lihat IRS <?php echo e($p->user?->name); ?> di tab baru" href="<?php echo e(route('eoffice.manprak.admin.pendaftaran.document', ['type' => 'praktikan', 'id' => $p->id, 'document' => 'irs_path'])); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Lihat IRS</a>
                                <?php else: ?>
                                    <span style="color:var(--c-fg-sec);">Tidak diunggah</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><a class="mp-btn secondary sm" href="<?php echo e(route('eoffice.manprak.admin.pendaftaran.show', ['type' => 'praktikan', 'id' => $p->id])); ?>">Detail <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'arrow-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?></a></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="6" style="text-align:center;padding:40px;">Tidak ada pendaftaran yang sesuai dengan filter.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding:16px 18px;"><?php echo e($pendaftaran->links()); ?></div>
    </section>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\admin\pendaftaran-praktikan.blade.php ENDPATH**/ ?>