<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pendaftaran Asisten Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pendaftaran Asisten Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Asisten Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Review dan proses permohonan calon asisten praktikum</p>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter Status</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:14px 18px;">
    <form method="GET" class="flex gap-2 flex-wrap" style="align-items: center; width: 100%;">
        <select name="praktikum_id" class="mp-input mp-select" style="flex: 1; min-width: 180px;">
            <option value="">Semua Praktikum Aktif</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <option value="<?php echo e($p->id); ?>" <?php echo e(request('praktikum_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->nama); ?></option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </select>
        <select name="status_koor" class="mp-input mp-select" style="flex: 1; min-width: 150px;">
            <option value="">Semua Status Koor</option>
            <option value="menunggu"  <?php echo e(request('status_koor')=='menunggu'  ? 'selected' : ''); ?>>Menunggu Koor</option>
            <option value="disetujui" <?php echo e(request('status_koor')=='disetujui' ? 'selected' : ''); ?>>Disetujui Koor</option>
            <option value="ditolak"   <?php echo e(request('status_koor')=='ditolak'   ? 'selected' : ''); ?>>Ditolak Koor</option>
        </select>
        <select name="status" class="mp-input mp-select" style="flex: 1; min-width: 150px;">
            <option value="">Semua Status Admin</option>
            <option value="pending"  <?php echo e(request('status')=='pending'  ? 'selected' : ''); ?>>Menunggu Admin</option>
            <option value="approved" <?php echo e(request('status')=='approved' ? 'selected' : ''); ?>>Disetujui Admin</option>
            <option value="rejected" <?php echo e(request('status')=='rejected' ? 'selected' : ''); ?>>Ditolak Admin</option>
        </select>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama / NIM..."
               class="mp-input" style="flex: 1; min-width: 150px;">
        <button type="submit" class="mp-btn primary sm" style="height: 38px; padding: 0 16px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Filter
        </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','status_koor','status','praktikum_id'])): ?>
        <a href="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.index')); ?>" class="mp-btn secondary sm" style="height: 38px; padding: 0 16px; line-height: 36px;">Reset</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pendaftar Asisten Praktikum</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e(count($pendaftaran ?? [])); ?> pendaftar</span>
</div>

<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div style="overflow-x:auto; flex:1;">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama / NIM</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Praktikum</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">IPK</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Motivasi</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Terdaftar</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status Koor</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Status Admin</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $nameParts = explode(' ', $pend->user?->name ?? 'AS');
                    $initials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
                    $avColors = ['sky','navy','green','yellow','violet'];
                    $avColor = $avColors[crc32($pend->user?->email ?? '') % count($avColors)];
                ?>
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;">
                        <div class="flex items-center gap-3">
                            <div class="mp-av <?php echo e($avColor); ?>"><?php echo e($initials); ?></div>
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);"><?php echo e($pend->user?->name ?? '—'); ?></div>
                                <div style="font-size:11px; color:var(--c-fg-muted, #666D80);"><?php echo e($pend->user?->student_number ?? '—'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px; color:var(--c-fg, #0D0D12); font-size:13px;"><?php echo e($pend->praktikum?->nama ?? '—'); ?></td>
                    <td style="padding:14px 16px; text-align:center;">
                        <span style="font-size:13px; font-weight:700; color:<?php echo e(($pend->ipk ?? 0) >= 3.0 ? '#10B981' : '#EF4444'); ?>;"><?php echo e(number_format($pend->ipk ?? 0, 2)); ?></span>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->motivasi): ?>
                        <span class="mp-badge success sm"><span class="dot"></span>Ada</span>
                        <?php else: ?>
                        <span class="mp-badge neutral sm">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center; font-size:12px; color:var(--c-fg-muted, #666D80);">
                        <?php echo e($pend->created_at?->format('d M Y') ?? '—'); ?>

                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status_koor === 'disetujui'): ?>
                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                        <?php elseif($pend->status_koor === 'ditolak'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status === 'approved'): ?>
                        <span class="mp-badge navy sm"><span class="dot"></span>Disetujui</span>
                        <?php elseif($pend->status === 'rejected'): ?>
                        <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                        <?php else: ?>
                        <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status === 'pending' && $pend->status_koor === 'disetujui'): ?>
                        <div class="flex gap-2 justify-center">
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="mp-btn ghost sm">Terima</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id)); ?>"
                                  onsubmit="return confirm('Tolak pendaftaran ini?')" style="display:inline;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="mp-btn destructive sm">Tolak</button>
                            </form>
                        </div>
                        <?php elseif($pend->status === 'pending' && $pend->status_koor === 'menunggu'): ?>
                        <span style="font-size:11px; color:var(--c-fg-muted, #666D80);">Tunggu Koor</span>
                        <?php else: ?>
                        <span style="font-size:11px;color:var(--c-fg-muted, #666D80);">Selesai</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-fg-muted, #A4ABB8)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
                        </svg>
                        <div style="font-size:13px;color:var(--c-fg-muted, #666D80);">Tidak ada data pendaftaran asisten praktikum.</div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pendaftaran) && method_exists($pendaftaran, 'links')): ?>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\pendaftaran-asprak.blade.php ENDPATH**/ ?>