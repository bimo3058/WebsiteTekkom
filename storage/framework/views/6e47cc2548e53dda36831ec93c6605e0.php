<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Periode Pendaftaran Koordinator']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Periode Pendaftaran Koordinator']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Periode Pendaftaran Koordinator</h1>
                <span class="mp-badge info sm"><span class="dot"></span>Dosen</span>
            </div>
            <p class="mp-page-sub">Buka / tutup periode pendaftaran Koordinator Praktikum untuk matkul yang Anda ampu ·
                <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="mp-alert success flex-shrink-0" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="mp-alert error flex-shrink-0" style="margin-bottom:16px;"><?php echo e(session('error')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Pilih Praktikum</span>
        <span class="sec-rule"></span>
    </div>

    <div class="mp-card" style="flex-shrink:0;">
        <div class="mp-card-header">
            <span class="mp-card-title">Praktikum yang Anda Ampu</span>
        </div>
        <div style="padding:24px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
                <div class="mp-alert warning">
                    Anda belum mengampu praktikum aktif. Hubungi Admin untuk assign praktikum ke Anda.
                </div>
            <?php else: ?>
                <form method="GET" action="<?php echo e(route('eoffice.manprak.dosen.periode-pendaftaran.index')); ?>"
                    id="form-praktikum">
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Pilih Praktikum <span style="color:#DF1C41;">*</span>
                    </label>
                    <?php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        $label .= " · {$p->semester} {$p->tahun_ajaran}";
                        $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $label];
                    }
                }
            ?>
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'praktikum_id','options' => $praktikumOptions,'selected' => (string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : ''))),'placeholder' => 'Pilih Praktikum...','onChange' => '$event.target.form.submit()','minWidth' => '240px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'praktikum_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($praktikumOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string)request('praktikum_id', (isset($praktikum) ? $praktikum?->id : (isset($praktikumId) ? $praktikumId : '')))),'placeholder' => 'Pilih Praktikum...','onChange' => '$event.target.form.submit()','minWidth' => '240px']); ?>
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
                    <div
                        style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;">
                        <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($praktikumDipilih->nama); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->matkul): ?>
                            <span style="font-size:11px;color:#666D80;"><?php echo e($praktikumDipilih->matkul->kode); ?> · Sem
                                <?php echo e($praktikumDipilih->semester); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->koordinator): ?>
                            <span style="font-size:11px;color:#666D80;">Koordinator: <?php echo e($praktikumDipilih->koordinator->name); ?></span>
                        <?php else: ?>
                            <span class="mp-badge warning sm">Belum Ada Koordinator</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih): ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->koor_id): ?>
            <div class="mp-alert info flex-shrink-0" style="margin-top:24px;">
                Praktikum ini sudah memiliki Koordinator (<?php echo e($praktikumDipilih->koordinator->name); ?>).
            </div>
        <?php else: ?>
            
            <div class="sec-head" style="margin-top:24px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Buka Periode Pendaftaran Koordinator</span>
                <span class="sec-rule"></span>
            </div>

            <div class="mp-card" style="flex-shrink:0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Form Pembukaan Periode</span>
                </div>
                <div style="padding:24px;">
                    <?php
                        $periodeAktif = $periodeList->firstWhere('is_aktif', true);
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif && $periodeAktif->isSedangBuka()): ?>
                        <div class="mp-alert warning" style="margin-bottom:16px;">
                            <div style="font-size:13px;font-weight:600;color:#7C5309;">Periode Koordinator Sedang Dibuka</div>
                            <div style="font-size:12px;color:#956321;margin-top:4px;">
                                <strong><?php echo e($periodeAktif->nama); ?></strong>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif->ditutup_pada): ?>
                                    · Ditutup otomatis: <?php echo e($periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?>

                                <?php else: ?>
                                    · Tanpa batas waktu
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <form method="POST"
                                action="<?php echo e(route('eoffice.manprak.dosen.periode-pendaftaran.tutup', $periodeAktif->id)); ?>"
                                style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran koordinator sekarang?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="mp-btn error sm"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'lock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'lock']); ?>
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
<?php endif; ?> Tutup Periode Sekarang</button>
                            </form>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <form method="POST" action="<?php echo e(route('eoffice.manprak.dosen.periode-pendaftaran.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="praktikum_id" value="<?php echo e($praktikumDipilih->id); ?>">

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                                    Nama Periode <span style="color:#666D80;font-weight:400;">(opsional)</span>
                                </label>
                                <input type="text" name="nama" class="mp-input w-full"
                                    placeholder="cth. Pendaftaran Koordinator Gasal 2025/2026" value="<?php echo e(old('nama')); ?>">
                            </div>
                            <div style="display:flex;align-items:flex-end;">
                                <div class="mp-inline-note">
                                    <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info']); ?>
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
<?php endif; ?><span>Membuka periode ini akan otomatis menutup periode koordinator aktif sebelumnya untuk praktikum ini.</span>
                                </div>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                                    Dibuka Pada
                                </label>
                                <input type="datetime-local" name="dibuka_pada" class="mp-input w-full"
                                    value="<?php echo e(old('dibuka_pada', now()->format('Y-m-d\TH:i'))); ?>">
                                <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan untuk langsung dibuka saat
                                    ini.</p>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                                    Ditutup Pada <span style="color:#DF1C41;">*</span>
                                </label>
                                <input type="datetime-local" name="ditutup_pada" class="mp-input w-full"
                                    value="<?php echo e(old('ditutup_pada')); ?>" required>
                                <p style="font-size:11px;color:#666D80;margin-top:4px;">Wajib diisi sebagai batas waktu
                                    penutupan pendaftaran.</p>
                            </div>
                        </div>

                        <button type="submit" class="mp-btn primary md">
                            <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'announcement']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'announcement']); ?>
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
<?php endif; ?> Buka Periode Pendaftaran Koordinator
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeList->isNotEmpty()): ?>
            <div class="sec-head" style="margin-top:24px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Riwayat Periode Pendaftaran Koordinator</span>
                <span class="sec-rule"></span>
            </div>

            <div class="mp-card" style="flex-shrink:0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Semua Periode — <?php echo e($praktikumDipilih->nama); ?></span>
                    <span class="mp-badge neutral sm"><?php echo e($periodeList->count()); ?> periode</span>
                </div>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:680px;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">
                                    Nama Periode</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">
                                    Dibuka</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">
                                    Ditutup</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:130px;">
                                    Dibuka Oleh</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:130px;">
                                    Status</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $periodeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:12px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);">
                                        <?php echo e($p->nama); ?></td>
                                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                                        <?php echo e($p->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—'); ?>

                                    </td>
                                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                                        <?php echo e($p->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas'); ?>

                                    </td>
                                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                                        <?php echo e($p->dibukaOleh?->name ?? '—'); ?></td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->isSedangBuka()): ?>
                                            <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                                        <?php elseif($p->is_aktif): ?>
                                            <span class="mp-badge warning sm"><span class="dot"></span>Aktif (Belum Buka)</span>
                                        <?php else: ?>
                                            <span class="mp-badge neutral sm">Ditutup</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <div style="display:flex;gap:6px;justify-content:center;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->is_aktif): ?>
                                                <form method="POST"
                                                    action="<?php echo e(route('eoffice.manprak.dosen.periode-pendaftaran.tutup', $p->id)); ?>"
                                                    onsubmit="return confirm('Tutup periode ini?')">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="mp-btn destructive sm"
                                                        style="padding:5px 10px; font-size:11px;">Tutup</button>
                                                </form>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <form method="POST"
                                                action="<?php echo e(route('eoffice.manprak.dosen.periode-pendaftaran.destroy', $p->id)); ?>"
                                                onsubmit="return confirm('Hapus periode ini? Data tidak dapat dikembalikan.')">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="mp-btn secondary sm"
                                                    style="padding:5px 10px; font-size:11px;">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayatPengumuman->isNotEmpty()): ?>
            <div class="sec-head" style="margin-top:24px;">
                <span class="sec-bar"></span>
                <span class="sec-title">Riwayat Pengumuman Sistem</span>
                <span class="sec-rule"></span>
            </div>

            <div class="mp-card" style="flex-shrink:0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Log Pengumuman Otomatis</span>
                    <span class="mp-badge neutral sm"><?php echo e($riwayatPengumuman->count()); ?> entri</span>
                </div>
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:500px;">
                        <thead>
                            <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">
                                    Judul</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">
                                    Tipe</th>
                                <th
                                    style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">
                                    Waktu</th>
                                <th
                                    style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:110px;">
                                    Visibilitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayatPengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:12px 16px; font-size:13px; color:var(--c-fg, #0D0D12);"><?php echo e($pg->judul); ?></td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->tipe_sistem === 'buka'): ?>
                                            <span class="mp-badge success sm">Buka</span>
                                        <?php elseif($pg->tipe_sistem === 'tutup'): ?>
                                            <span class="mp-badge error sm">Tutup</span>
                                        <?php else: ?>
                                            <span class="mp-badge neutral sm"><?php echo e($pg->tipe_sistem); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td style="padding:12px 16px; font-size:12px; color:var(--c-fg-muted, #666D80);">
                                        <?php echo e($pg->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?></td>
                                    <td style="padding:12px 16px; text-align:center;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->trashed()): ?>
                                            <span class="mp-badge neutral sm"
                                                title="Soft deleted — tidak tampil di user">Disembunyikan</span>
                                        <?php else: ?>
                                            <span class="mp-badge success sm">Tampil</span>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\dosen\periode-pendaftaran.blade.php ENDPATH**/ ?>