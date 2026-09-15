<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Riwayat Peminjaman']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Riwayat Peminjaman']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Riwayat Peminjaman</h1>
            <p class="mp-page-sub">Daftar arsip seluruh pengajuan peminjaman ruangan Anda yang telah selesai, ditolak,
                atau dibatalkan.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Arsip Peminjaman</h2>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayats->count() > 0): ?>
                <div class="mp-table-wrap">
                    <table class="mp-table" style="table-layout: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>RUANGAN & TUJUAN</th>
                                <th>JADWAL PEMAKAIAN</th>
                                <th>KETERANGAN</th>
                                <th>STATUS</th>
                                <th style="width: 120px; text-align: right;">LAMPIRAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $riwayat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="mp-tr">
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            <?php echo e($riwayat->ruangan->nama); ?>

                                        </div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                            title="<?php echo e($riwayat->tujuan); ?>">
                                            <?php echo e($riwayat->tujuan); ?>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            <?php echo e(\Carbon\Carbon::parse($riwayat->tanggal_pinjam)->translatedFormat('d F Y')); ?>

                                            <span class="text-gray-400 mx-1">•</span>
                                            <?php echo e(\Carbon\Carbon::parse($riwayat->jam_mulai)->format('H:i')); ?> -
                                            <?php echo e(\Carbon\Carbon::parse($riwayat->jam_selesai)->format('H:i')); ?> WIB
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Diajukan: <?php echo e($riwayat->created_at->translatedFormat('d M Y')); ?>

                                        </div>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strtolower($riwayat->status) == 'ditolak'): ?>
                                            <div class="text-[12px] font-medium text-red-600 max-w-[200px] truncate"
                                                title="<?php echo e($riwayat->alasan_penolakan ?? 'Tidak memenuhi syarat'); ?>">
                                                <?php echo e($riwayat->alasan_penolakan ?? 'Tidak memenuhi syarat'); ?>

                                            </div>
                                        <?php elseif(strtolower($riwayat->status) == 'disetujui'): ?>
                                            <div class="text-[12px] font-medium text-emerald-600">
                                                Telah terlaksana
                                            </div>
                                        <?php else: ?>
                                            <div class="text-[12px] font-medium text-gray-500">
                                                Dibatalkan
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $style = '';
                                            if (strtolower($riwayat->status) === 'disetujui')
                                                $style = 'bg-[#ECF9F7] text-[#267666]';
                                            elseif (strtolower($riwayat->status) === 'ditolak')
                                                $style = 'bg-[#FEF2F2] text-[#B91C1C]';
                                            elseif (strtolower($riwayat->status) === 'menunggu')
                                                $style = 'bg-[#FFF9ED] text-[#A77B2E]';
                                            else
                                                $style = 'bg-[#ECEFF3] text-[#0D0D12]';
                                        ?>
                                        <span
                                            class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full <?php echo e($style); ?> text-[12px] font-medium tracking-wide">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strtolower($riwayat->status) === 'disetujui'): ?> Selesai <?php else: ?>
                                            <?php echo e(ucfirst($riwayat->status)); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayat->berkas_pendukung): ?>
                                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($riwayat->berkas_pendukung)); ?>"
                                                target="_blank"
                                                class="inline-flex items-center justify-end gap-1.5 text-[12px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Proposal
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[12px] italic text-gray-400">Tanpa arsip</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-6">
                    <div class="text-center py-20 px-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                        <div
                            class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg width="32" height="32" fill="none" stroke="#9CA3AF" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Riwayat Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-0">Belum ada riwayat peminjaman yang ditolak
                            atau selesai pada akun Anda.</p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\user\riwayat\index.blade.php ENDPATH**/ ?>