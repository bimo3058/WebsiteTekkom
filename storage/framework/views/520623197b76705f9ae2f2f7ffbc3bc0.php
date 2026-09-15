<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Peminjaman Saya']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Peminjaman Saya']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Peminjaman Saya</h1>
            <p class="mp-page-sub">Pantau status pengajuan yang sedang diproses dan kelola jadwal pemakaian ruangan Anda
                yang akan datang.</p>
        </div>
        <div>
            <a href="<?php echo e(route('eoffice.peminjaman.user.booking')); ?>"
                class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[13px] font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm hover:shadow">
                Pinjam Ruang
            </a>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 24px;">
        <div class="mp-card-body">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-[12px]">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Daftar Peminjaman Anda</h2>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peminjamans->count() > 0): ?>
                <div class="mp-table-wrap">
                    <table class="mp-table" style="table-layout: auto; width: 100%;">
                        <thead>
                            <tr>
                                <th>RUANGAN & TUJUAN</th>
                                <th>JADWAL PEMAKAIAN</th>
                                <th>LAMPIRAN</th>
                                <th>STATUS</th>
                                <th style="width: 100px; text-align: right;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="mp-tr">
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            <?php echo e($booking->ruangan->nama); ?>

                                        </div>
                                        <div class="text-[11px] text-gray-500 max-w-[200px] truncate mt-0.5"
                                            title="<?php echo e($booking->tujuan); ?>">
                                            <?php echo e($booking->tujuan); ?>

                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-[13px] font-medium text-[#111827]">
                                            <?php echo e(\Carbon\Carbon::parse($booking->tanggal_pinjam)->translatedFormat('d F Y')); ?>

                                            <span class="text-gray-400 mx-1">•</span>
                                            <?php echo e(\Carbon\Carbon::parse($booking->jam_mulai)->format('H:i')); ?> -
                                            <?php echo e(\Carbon\Carbon::parse($booking->jam_selesai)->format('H:i')); ?> WIB
                                        </div>
                                    </td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($booking->berkas_pendukung): ?>
                                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($booking->berkas_pendukung)); ?>"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 text-[12px] font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                Lihat Dokumen
                                            </a>
                                        <?php else: ?>
                                            <span class="text-[12px] italic text-gray-400">Tidak ada</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $style = '';
                                            if (strtolower($booking->status) === 'disetujui')
                                                $style = 'bg-[#E8F8F2] text-[#166534]';
                                            elseif (strtolower($booking->status) === 'ditolak')
                                                $style = 'bg-[#FDF2F2] text-[#991B1B]';
                                            elseif (strtolower($booking->status) === 'menunggu')
                                                $style = 'bg-[#FFF9ED] text-[#A77B2E]';
                                            else
                                                $style = 'bg-[#F1F5F9] text-[#1E293B]';
                                        ?>
                                        <span
                                            class="inline-flex items-center justify-center px-[12px] py-[4px] rounded-full <?php echo e($style); ?> text-[12px] font-medium tracking-wide">
                                            <?php echo e(ucfirst($booking->status)); ?>

                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="flex items-center justify-end">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array(strtolower($booking->status), ['dibatalkan', 'ditolak', 'selesai'])): ?>
                                                <form method="POST"
                                                    action="<?php echo e(route('eoffice.peminjaman.user.saya.batal', $booking->id)); ?>"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin secara sukarela membatalkan pengajuan ini?');"
                                                    class="m-0">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit"
                                                        class="h-8 px-3 rounded-md bg-white border border-red-200 text-[12px] font-medium text-red-600 hover:bg-red-50 hover:border-red-300 shadow-sm transition-all focus:ring-2 focus:ring-offset-1 focus:ring-red-100">
                                                        Batal
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-[12px] italic text-gray-400">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
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
                                <path
                                    d="M9 12h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Peminjaman Kosong</h3>
                        <p class="text-[13px] text-gray-500 max-w-sm mx-auto mb-6">Anda tidak memiliki pengajuan peminjaman
                            ruangan yang sedang berjalan atau aktif saat ini.</p>
                        <a href="<?php echo e(route('eoffice.peminjaman.user.booking')); ?>"
                            class="inline-flex items-center justify-center bg-[#0B266E] hover:bg-[#071946] text-white text-[14px] font-semibold px-6 py-[11px] rounded-full transition-colors shadow-md shadow-blue-900/10">Mulai
                            Ajukan Peminjaman</a>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\user\peminjaman\index.blade.php ENDPATH**/ ?>