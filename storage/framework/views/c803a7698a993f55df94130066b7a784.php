<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Periode Pendaftaran Praktikan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Periode Pendaftaran Praktikan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran Praktikan</h1>
            <span class="mp-badge info sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Buka atau tutup pendaftaran mahasiswa untuk setiap mata kuliah praktikum.</p>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
<div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;"><?php echo e(session('error')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
<div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;">
    <ul style="margin:0;padding-left:18px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <li><?php echo e($error); ?></li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </ul>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum Aktif</span>
    </div>
    <div style="padding:24px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
        <div class="mp-alert warning">Belum ada praktikum aktif. Buat atau aktifkan praktikum terlebih dahulu.</div>
        <?php else: ?>
        <form method="GET" action="<?php echo e(route('eoffice.manprak.admin.periode-praktikan.index')); ?>">
            <?php
                $praktikumOptions = $praktikumList->map(function ($p) {
                    $matkul = $p->matkul
                        ? trim(($p->matkul->kode ? $p->matkul->kode . ' · ' : '') . $p->matkul->nama)
                        : $p->nama;
                    return [
                        'value' => (string) $p->id,
                        'label' => $matkul . " · {$p->semester} {$p->tahun_ajaran}",
                    ];
                })->values()->all();
            ?>
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Mata Kuliah / Praktikum</label>
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'praktikum_id','options' => $praktikumOptions,'selected' => (string) $praktikumId,'placeholder' => 'Pilih Praktikum...','onChange' => '$event.target.form.submit()','minWidth' => '280px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'praktikum_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($praktikumOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) $praktikumId),'placeholder' => 'Pilih Praktikum...','onChange' => '$event.target.form.submit()','minWidth' => '280px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $attributes = $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $component = $__componentOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih): ?>
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;flex-wrap:wrap;">
            <div style="flex:1;min-width:220px;font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($praktikumDipilih->nama); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->matkul): ?>
            <span style="font-size:11px;color:#666D80;"><?php echo e($praktikumDipilih->matkul->kode); ?> · <?php echo e($praktikumDipilih->matkul->nama); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span style="font-size:11px;color:#666D80;"><?php echo e($praktikumDipilih->semester); ?> · <?php echo e($praktikumDipilih->tahun_ajaran); ?></span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih): ?>
<?php $periodeAktif = $periodeList->firstWhere('is_aktif', true); ?>

<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Pendaftaran Praktikan</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title"><?php echo e($praktikumDipilih->nama); ?></span>
    </div>
    <div style="padding:24px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif && $periodeAktif->isSedangBuka()): ?>
        <div class="mp-alert success" style="margin-bottom:16px;">
            <div style="font-weight:600;">Pendaftaran sedang dibuka</div>
            <div style="font-size:12px;margin-top:4px;">
                <?php echo e($periodeAktif->nama); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif->ditutup_pada): ?>
                · ditutup <?php echo e($periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-praktikan.tutup', $periodeAktif->id)); ?>" style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran praktikan sekarang?')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="mp-btn error sm">Tutup Periode</button>
            </form>
        </div>
        <?php elseif($periodeAktif): ?>
        <div class="mp-alert warning" style="margin-bottom:16px;">Periode sudah dijadwalkan dan akan dibuka pada <?php echo e($periodeAktif->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'sekarang'); ?>.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-praktikan.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="praktikum_id" value="<?php echo e($praktikumDipilih->id); ?>">
            <div class="mp-form-grid" style="margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Nama Periode <span style="font-weight:400;color:#666D80;">(opsional)</span></label>
                    <input type="text" name="nama" class="mp-input w-full" value="<?php echo e(old('nama')); ?>" placeholder="Pendaftaran Praktikan Semester Ganjil">
                </div>
                <div style="font-size:12px;color:#666D80;padding:10px;background:#F6F8FA;border-radius:8px;align-self:end;">
                    Membuka periode baru otomatis menutup periode praktikan sebelumnya untuk mata kuliah ini.
                </div>
            </div>
            <div class="mp-form-grid" style="margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Dibuka Pada</label>
                    <input type="datetime-local" name="dibuka_pada" class="mp-input w-full" value="<?php echo e(old('dibuka_pada', now()->format('Y-m-d\TH:i'))); ?>">
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan untuk langsung dibuka.</p>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                    <input type="datetime-local" name="ditutup_pada" class="mp-input w-full" value="<?php echo e(old('ditutup_pada')); ?>" required>
                </div>
            </div>
            <button type="submit" class="mp-btn primary md">Buka Pendaftaran Praktikan</button>
        </form>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeList->isNotEmpty()): ?>
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="overflow-x-auto">
        <table class="mp-table">
            <thead><tr><th>Nama</th><th>Dibuka</th><th>Ditutup</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $periodeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e($periode->nama); ?></td>
                    <td style="font-size:12px;"><?php echo e($periode->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Sekarang'); ?></td>
                    <td style="font-size:12px;"><?php echo e($periode->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas'); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->isSedangBuka()): ?>
                        <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                        <?php elseif($periode->is_aktif): ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Terjadwal</span>
                        <?php else: ?>
                        <span class="mp-badge neutral sm">Ditutup</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->is_aktif): ?>
                        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-praktikan.tutup', $periode->id)); ?>" onsubmit="return confirm('Tutup periode ini?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="mp-btn error xs">Tutup</button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-praktikan.destroy', $periode->id)); ?>" onsubmit="return confirm('Hapus riwayat periode ini?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="mp-btn neutral xs">Hapus</button>
                        </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\admin\periode-praktikan.blade.php ENDPATH**/ ?>