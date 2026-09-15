<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Seleksi Koordinator']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Seleksi Koordinator']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
        <p class="mp-page-sub">Review pendaftar koordinator sesuai praktikum yang Anda ampu</p>
    </div>
</div>


<div class="mp-alert info flex-shrink-0">
    <strong>Alur:</strong> Anda mereview data mahasiswa (IPK, motivasi) → Setujui/Tolak → Jika disetujui, masuk ke antrian Admin untuk final approval.
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter Pendaftaran</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Pencarian & Filter</span>
    </div>
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama mahasiswa..."
                   class="mp-input" style="width:200px;">
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'sort','options' => [
                    ['value' => 'terbaru', 'label' => 'Terbaru'],
                    ['value' => 'terlama', 'label' => 'Terlama'],
                    ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
                    ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
                ],'selected' => request('sort', 'terbaru'),'placeholder' => 'Urutkan...','onChange' => '$event.target.form.submit()','minWidth' => '140px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'sort','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['value' => 'terbaru', 'label' => 'Terbaru'],
                    ['value' => 'terlama', 'label' => 'Terlama'],
                    ['value' => 'nama_asc', 'label' => 'Nama (A-Z)'],
                    ['value' => 'nama_desc', 'label' => 'Nama (Z-A)']
                ]),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('sort', 'terbaru')),'placeholder' => 'Urutkan...','onChange' => '$event.target.form.submit()','minWidth' => '140px']); ?>
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
            <?php
                $praktikumOptions = [];
                if(isset($praktikumList)) {
                    foreach($praktikumList as $p) {
                        $label = $p->nama;
                        if(isset($p->kode) && $p->kode) $label .= " [{$p->kode}]";
                        if(isset($p->semester)) $label .= " · {$p->semester} {$p->tahun_ajaran}";
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
            <select name="status_dosen" class="mp-input mp-select">
                <option value="">Semua Status</option>
                <option value="menunggu"  <?php echo e(request('status_dosen')=='menunggu'  ? 'selected' : ''); ?>>Menunggu Review</option>
                <option value="disetujui" <?php echo e(request('status_dosen')=='disetujui' ? 'selected' : ''); ?>>Sudah Disetujui</option>
                <option value="ditolak"   <?php echo e(request('status_dosen')=='ditolak'   ? 'selected' : ''); ?>>Ditolak</option>
            </select>
            <button type="submit" class="mp-btn primary sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
        </form>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm"><?php echo e($pendaftaran->total()); ?> pendaftar</span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="overflow-x-auto flex-1">
        <table class="w-full" style="font-size:13px;">
            <thead style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                <tr>
                    <th class="mp-th text-left" style="padding:10px 20px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">IPK</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Motivasi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Berkas</th>
                    <th class="mp-th text-center" style="padding:10px 16px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $namaParts = explode(' ', $p->user?->name ?? 'UN');
                    $initials = strtoupper(substr($namaParts[0] ?? 'U', 0, 1) . substr($namaParts[1] ?? $namaParts[0] ?? 'N', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet','red'];
                    $avColor = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                    $ipkOk = ($p->ipk ?? 0) >= 3.0;
                ?>
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 20px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av <?php echo e($avColor); ?>"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;"><?php echo e($p->user?->name ?? '—'); ?></div>
                                <div style="font-size:11px;color:#666D80;"><?php echo e($p->user?->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#353849;"><?php echo e($p->praktikum?->nama ?? '—'); ?></td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="font-weight:700;color:<?php echo e($ipkOk ? '#40C4AA' : '#DF1C41'); ?>;"><?php echo e(number_format($p->ipk ?? 0, 2)); ?></span>
                    </td>
                    <td style="padding:12px 16px;color:#666D80;max-width:200px;">
                        <div class="line-clamp-2" style="font-size:12px;"><?php echo e($p->motivasi ?? '—'); ?></div>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->transkrip_path): ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->transkrip_path, 'eoffice')); ?>" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;" class="hover:underline">Transkrip</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->berkas_cerc_path): ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, 'eoffice')); ?>" target="_blank"
                           style="font-size:11px;font-weight:600;color:#0B266E;text-decoration:none;display:block;margin-top:2px;" class="hover:underline">CERC</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$p->transkrip_path && !$p->berkas_cerc_path): ?>
                        <span style="font-size:11px;color:#808897;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status_dosen === 'disetujui'): ?>
                        <div>
                            <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                            <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
                        </div>
                        <?php elseif($p->status_dosen === 'ditolak'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status_dosen === 'menunggu'): ?>
                        <div class="flex gap-2" x-data="{ catatan: '' }">
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="catatan_dosen" value="">
                                <button type="submit" class="mp-btn ghost sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id)); ?>" x-data="{ alasan: '' }">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
                        </svg>
                        <div style="font-size:13px;color:#666D80;">Tidak ada pendaftaran untuk praktikum Anda.</div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendaftaran->hasPages()): ?>
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;"><?php echo e($pendaftaran->links()); ?></div>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup_kode\dosen\pendaftaran-koor.blade.php ENDPATH**/ ?>