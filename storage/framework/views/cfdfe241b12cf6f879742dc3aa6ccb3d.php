<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pendaftaran Koordinator']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pendaftaran Koordinator']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Koordinator Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Final approval setelah dosen menyetujui pendaftar · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>


<div class="mp-alert warning flex-shrink-0">
    <strong>Alur Persetujuan:</strong> Mahasiswa mendaftar → Dosen pengampu review & setujui → Admin final approval → Role <code>koor_prak</code> di-assign otomatis.
    Anda hanya bisa approve jika dosen sudah menyetujui.
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div style="padding:14px 18px;">
        <form method="GET" class="flex gap-2 flex-wrap" style="align-items: center; width: 100%;">
            <select name="praktikum_id" class="mp-input mp-select" style="flex: 1; min-width: 180px;">
                <option value="">Semua Praktikum Aktif</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($p->id); ?>" <?php echo e(request('praktikum_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->nama); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <select name="status_dosen" class="mp-input mp-select" style="flex: 1; min-width: 150px;">
                <option value="">Semua Status Dosen</option>
                <option value="menunggu"  <?php echo e(request('status_dosen')=='menunggu'  ? 'selected' : ''); ?>>Menunggu Dosen</option>
                <option value="disetujui" <?php echo e(request('status_dosen')=='disetujui' ? 'selected' : ''); ?>>Disetujui Dosen</option>
                <option value="ditolak"   <?php echo e(request('status_dosen')=='ditolak'   ? 'selected' : ''); ?>>Ditolak Dosen</option>
            </select>
            <select name="status" class="mp-input mp-select" style="flex: 1; min-width: 150px;">
                <option value="">Semua Status Admin</option>
                <option value="pending"  <?php echo e(request('status')=='pending'  ? 'selected' : ''); ?>>Menunggu Admin</option>
                <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>>Disetujui Admin</option>
                <option value="rejected" <?php echo e(request('status')=='rejected' ? 'selected' : ''); ?>>Ditolak Admin</option>
            </select>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama mahasiswa..."
                   class="mp-input" style="flex: 1; min-width: 150px;">
            <button type="submit" class="mp-btn primary sm" style="height: 38px; padding: 0 16px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','status_dosen','status','praktikum_id'])): ?>
            <a href="<?php echo e(route('eoffice.manprak.admin.pendaftaran-koor.index')); ?>" class="mp-btn secondary sm" style="height: 38px; padding: 0 16px; line-height: 36px;">Reset</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e($pendaftaran->total()); ?> pendaftar</span>
</div>

<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div class="overflow-x-auto flex-1">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Mahasiswa</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Praktikum</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">IPK</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status Dosen</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Catatan Dosen</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status Admin</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $nameParts = explode(' ', $p->user?->name ?? 'KR');
                    $initials = strtoupper(substr($nameParts[0] ?? 'K', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'R', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet'];
                    $avColor = $avColors[crc32($p->user?->email ?? '') % count($avColors)];
                ?>
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av <?php echo e($avColor); ?>"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);"><?php echo e($p->user?->name ?? '—'); ?></div>
                                <div style="font-size:11px; color:var(--c-fg-muted, #666D80);"><?php echo e($p->user?->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px; color:var(--c-fg, #0D0D12); font-size:13px;"><?php echo e($p->praktikum?->nama ?? '—'); ?></td>
                    <td style="padding:14px 16px; text-align:center;">
                        <span style="font-size:13px; font-weight:700; color:<?php echo e(($p->ipk ?? 0) >= 3.2 ? '#10B981' : '#F59E0B'); ?>;"><?php echo e(number_format($p->ipk ?? 0, 2)); ?></span>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status_dosen === 'disetujui'): ?>
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                        <?php elseif($p->status_dosen === 'ditolak'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->catatan_dosen): ?>
                        <span title="<?php echo e($p->catatan_dosen); ?>" style="font-size:12px; color:var(--c-fg-muted, #666D80); cursor:help;">
                            <?php echo e(Str::limit($p->catatan_dosen, 30, '...')); ?>

                        </span>
                        <?php else: ?>
                        <span style="font-size:12px; color:var(--c-border, #DFE1E7);">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'approved'): ?>
                        <span class="mp-badge navy sm"><span class="dot"></span>Disetujui</span>
                        <?php elseif($p->status === 'rejected'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'pending' && $p->status_dosen === 'disetujui'): ?>
                        <div class="flex gap-2 justify-center" x-data="{ alasan: '' }">
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-koor.approve', $p->id)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="mp-btn ghost sm">Terima</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-koor.reject', $p->id)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="alasan_penolakan" :value="alasan">
                                <button type="button"
                                        @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) $el.closest('form').submit()"
                                        class="mp-btn destructive sm">
                                    Tolak
                                </button>
                            </form>
                        </div>
                        <?php elseif($p->status === 'pending' && $p->status_dosen === 'menunggu'): ?>
                        <span style="font-size:11px; color:var(--c-fg-muted, #666D80);">Tunggu dosen</span>
                        <?php else: ?>
                        <span style="font-size:11px; color:var(--c-fg-muted, #666D80);">Selesai</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                        <div style="font-size:13px;color:var(--c-fg-muted, #666D80);">Tidak ada data pendaftaran koordinator.</div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendaftaran->hasPages()): ?>
    <div style="padding:12px 16px;border-top:1px solid var(--c-border, #DFE1E7);flex-shrink:0;"><?php echo e($pendaftaran->links()); ?></div>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\pendaftaran-koor.blade.php ENDPATH**/ ?>