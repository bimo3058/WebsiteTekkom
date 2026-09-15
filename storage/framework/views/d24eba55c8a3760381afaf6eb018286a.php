<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Detail Pendaftaran '.$label]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Detail Pendaftaran '.$label)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php
        $statusLabel = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        $statusTone = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'error'];
        $canOverride = $type === 'koor' && $registration->status_dosen !== 'disetujui'
            && ($registration->status === 'pending' || ($registration->status === 'rejected' && $registration->status_dosen === 'ditolak'));
        $canApprove = $registration->status === 'pending'
            && (($type === 'koor' && $registration->status_dosen === 'disetujui') || ($type === 'asprak' && $registration->status_koor === 'disetujui'));
    ?>
    <style>
        .mp-review-grid { display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:18px;align-items:start; }
        .mp-review-stack { display:grid;gap:18px;min-width:0; }
        .mp-review-pad { padding:20px;display:grid;gap:16px;min-width:0; }
        .mp-review-facts { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }
        .mp-review-facts dt,.mp-review-label { display:block;font-size:12px;color:var(--c-fg-sec);margin-bottom:5px; }
        .mp-review-facts dd { font-size:13px;font-weight:600;overflow-wrap:anywhere; }
        .mp-review-copy { font-size:13px;line-height:1.7;white-space:pre-line;overflow-wrap:anywhere; }
        .mp-review-doc { display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:14px;border:1px solid var(--c-border);border-radius:10px; }
        @media(max-width:1000px) { .mp-review-grid { grid-template-columns:minmax(0,1fr); } }
        @media(max-width:480px) { .mp-review-facts { grid-template-columns:minmax(0,1fr); } .mp-review-pad { padding:16px; } }
    </style>
    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pendaftaran <?php echo e($label); ?></h1>
            <p class="mp-page-sub">Periksa data, berkas, dan hasil seleksi mahasiswa.</p>
        </div>
        <a class="mp-btn secondary md" href="<?php echo e(route('eoffice.manprak.admin.pendaftaran-'.$type.'.index')); ?>">
            <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left']); ?>
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
<?php endif; ?> Kembali ke daftar
        </a>
    </div>
    <?php echo $__env->make('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => $type], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="mp-review-grid">
        <div class="mp-review-stack">
            <section class="mp-card">
                <div class="mp-card-header">
                    <h2 class="mp-card-title">Data mahasiswa</h2>
                    <span class="mp-badge <?php echo e($statusTone[$registration->status] ?? 'warning'); ?> sm"><?php echo e($statusLabel[$registration->status] ?? $registration->status); ?></span>
                </div>
                <div class="mp-review-pad">
                    <dl class="mp-review-facts">
                        <div><dt>Nama</dt><dd><?php echo e($registration->user?->name ?? 'Akun tidak tersedia'); ?></dd></div>
                        <div><dt>NIM</dt><dd><?php echo e($registration->user?->student?->student_number ?? $registration->user?->external_id ?? 'Belum tercatat'); ?></dd></div>
                        <div><dt>Email</dt><dd><?php echo e($registration->user?->email ?? 'Belum tercatat'); ?></dd></div>
                        <div><dt>Praktikum</dt><dd><?php echo e($registration->praktikum?->nama ?? 'Praktikum tidak tersedia'); ?></dd></div>
                        <div><dt>Tanggal daftar</dt><dd><?php echo e($registration->created_at?->format('d M Y, H:i') ?? 'Belum tercatat'); ?></dd></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'praktikan'): ?>
                            <div><dt>IPK</dt><dd><?php echo e($registration->ipk !== null ? number_format($registration->ipk, 2) : 'Belum tercatat'); ?></dd></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </dl>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'praktikan'): ?>
                        <div><h3 class="mp-review-label">Motivasi</h3><p class="mp-review-copy"><?php echo e($registration->motivasi ?: 'Tidak ada motivasi yang dicantumkan.'); ?></p></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'asprak' && !empty($registration->jadwal)): ?>
                        <div><h3 class="mp-review-label">Jadwal yang diajukan</h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) $registration->jadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <p class="mp-review-copy"><?php echo e(is_array($jadwal) ? implode(' · ', array_filter($jadwal, 'is_scalar')) : $jadwal); ?></p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
            <section class="mp-card">
                <div class="mp-card-header"><h2 class="mp-card-title">Dokumen pendaftaran</h2><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
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
<?php endif; ?></div>
                <div class="mp-review-pad">
                    <p class="mp-page-sub">PDF dan gambar dibuka di tab baru. Format lainnya akan diunduh.</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $documentLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="mp-review-doc">
                            <span style="display:flex;gap:10px;align-items:center;font-size:13px;font-weight:600;">
                                <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
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
<?php endif; ?> <?php echo e($documentLabel); ?>

                            </span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($registration->getAttribute($field)): ?>
                                <a class="mp-btn secondary sm" target="_blank" rel="noopener" aria-label="Buka <?php echo e($documentLabel); ?> di tab baru"
                                   href="<?php echo e(route('eoffice.manprak.admin.pendaftaran.document', ['type' => $type, 'id' => $registration->id, 'document' => $field])); ?>">
                                    Lihat dokumen <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
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
<?php endif; ?>
                                </a>
                            <?php else: ?>
                                <span class="mp-badge sm">Tidak diunggah</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </section>
        </div>
        <div class="mp-review-stack">
            <section class="mp-card">
                <div class="mp-card-header"><h2 class="mp-card-title">Hasil review <?php echo e($type === 'koor' ? 'dosen' : 'koordinator'); ?></h2></div>
                <div class="mp-review-pad">
                    <?php ($reviewStatus = $type === 'koor' ? $registration->status_dosen : ($type === 'asprak' ? $registration->status_koor : $registration->status)); ?>
                    <span class="mp-badge <?php echo e(in_array($reviewStatus, ['approved', 'disetujui']) ? 'success' : (in_array($reviewStatus, ['rejected', 'ditolak']) ? 'error' : 'warning')); ?> sm" style="justify-self:start;">
                        <?php echo e(['disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'menunggu' => 'Menunggu review'][$reviewStatus] ?? ($statusLabel[$reviewStatus] ?? 'Menunggu review')); ?>

                    </span>
                    <dl class="mp-review-facts">
                        <div><dt>Direview oleh</dt><dd><?php echo e($registration->direviewOleh?->name ?? 'Belum ada reviewer'); ?></dd></div>
                        <div><dt>Waktu review</dt><dd><?php echo e($registration->direview_pada?->format('d M Y, H:i') ?? 'Belum direview'); ?></dd></div>
                    </dl>
                    <div><h3 class="mp-review-label">Catatan reviewer</h3><p class="mp-review-copy"><?php echo e(($type === 'koor' ? $registration->catatan_dosen : $registration->catatan_koor) ?: 'Tidak ada catatan.'); ?></p></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($registration->alasan_penolakan): ?>
                        <div><h3 class="mp-review-label">Riwayat alasan penolakan</h3><p class="mp-review-copy"><?php echo e($registration->alasan_penolakan); ?></p></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overrideAudit): ?>
                        <div class="mp-alert info">
                            <strong>Persetujuan melalui override admin</strong>
                            <p class="mp-review-copy"><?php echo e($overrideAudit->new_data['override_reason'] ?? $overrideAudit->description); ?></p>
                            <p style="margin-top:8px;font-size:12px;"><?php echo e($overrideAudit->user?->name ?? 'Admin'); ?> · <?php echo e($overrideAudit->created_at?->format('d M Y, H:i')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </section>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canOverride || $canApprove || ($type !== 'praktikan' && $registration->status === 'pending')): ?>
                <section class="mp-card">
                    <div class="mp-card-header"><h2 class="mp-card-title">Keputusan admin</h2></div>
                    <div class="mp-review-pad">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canOverride): ?>
                            <div class="mp-alert warning">Admin dapat menyetujui pendaftaran meskipun dosen belum menyetujui atau sudah menolak. Keputusan dosen tetap tercatat.</div>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-koor.approve', $registration->id)); ?>" style="display:grid;gap:12px;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="override_dosen" value="1">
                                <div>
                                    <label class="mp-review-label" for="override_reason">Alasan override <span aria-hidden="true">*</span></label>
                                    <textarea class="mp-input" id="override_reason" name="override_reason" rows="4" required minlength="10" maxlength="1000" aria-describedby="override-help"><?php echo e(old('override_reason')); ?></textarea>
                                    <p id="override-help" class="mp-page-sub">Minimal 10 karakter. Alasan dan akun admin dicatat di riwayat audit.</p>
                                </div>
                                <button class="mp-btn primary md" type="submit"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check']); ?>
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
<?php endif; ?> Override &amp; Setujui</button>
                            </form>
                        <?php elseif($canApprove): ?>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-'.$type.'.approve', $registration->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="mp-btn primary md" type="submit"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'check']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check']); ?>
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
<?php endif; ?> Setujui pendaftaran</button>
                            </form>
                        <?php else: ?>
                            <p class="mp-page-sub">Menunggu persetujuan koordinator sebelum persetujuan akhir admin.</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($registration->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-'.$type.'.reject', $registration->id)); ?>" style="display:grid;gap:12px;border-top:1px solid var(--c-border);padding-top:16px;">
                                <?php echo csrf_field(); ?>
                                <div><label class="mp-review-label" for="alasan_penolakan">Alasan penolakan</label><textarea class="mp-input" id="alasan_penolakan" name="alasan_penolakan" required maxlength="1000" rows="2"><?php echo e(old('alasan_penolakan')); ?></textarea></div>
                                <button class="mp-btn secondary md" type="submit"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'close']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'close']); ?>
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
<?php endif; ?> Tolak pendaftaran</button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/admin/pendaftaran-detail.blade.php ENDPATH**/ ?>