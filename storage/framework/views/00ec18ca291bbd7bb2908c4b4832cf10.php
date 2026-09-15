<!-- Status Banner Component - untuk menampilkan periode aktif -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activePeriode): ?>
    <div class="mb-8 status-banner <?php echo e($isUploadOpen ? 'status-banner-success' : 'status-banner-warning'); ?>">
        <div class="flex gap-4 items-start">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isUploadOpen): ?>
                <i class="fas fa-calendar-check text-2xl text-green-600 mt-1"></i>
            <?php else: ?>
                <i class="fas fa-calendar-times text-2xl text-yellow-600 mt-1"></i>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div>
                <h3 class="font-bold text-slate-800 mb-1 text-lg"><?php echo e($activePeriode->judul); ?></h3>
                <p class="text-slate-600 text-sm">
                    Batas akhir pengunggahan RPS untuk Semester <strong><?php echo e($activePeriode->semester); ?> <?php echo e($activePeriode->tahun_ajaran); ?></strong> adalah <strong><?php echo e(\Carbon\Carbon::parse($activePeriode->tanggal_selesai)->locale('id')->translatedFormat('d F Y, H:i')); ?> WIB</strong>.
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isUploadOpen): ?>
                        <span class="text-red-600 font-semibold block mt-2">⚠️ Sesi unggah saat ini sedang ditutup.</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tenggatH7 && count($unsubmittedMk) > 0): ?>
        <div class="mb-8 alert alert-warning">
            <i class="fas fa-exclamation-triangle text-lg"></i>
            <div>
                <p class="text-sm">
                    <strong>
                        Waktu tersisa <?php echo e($daysLeft); ?> 
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($isHourFormat) && $isHourFormat): ?>
                            jam!
                        <?php else: ?>
                            hari!
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </strong>
                </p>
                <p class="text-sm">Anda belum mengunggah RPS untuk: <strong><?php echo e(implode(', ', $unsubmittedMk)); ?></strong></p>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="mb-8 p-6 bg-white border-l-4 border-yellow-400 rounded-lg flex items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-xmark text-yellow-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Belum Ada Jadwal Pengajuan</h3>
                <p class="text-sm text-slate-600">Tidak ada sesi pengajuan RPS yang ditambahkan saat ini</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full whitespace-nowrap flex-shrink-0">
            <i class="fas fa-exclamation-circle"></i> Belum Aktif
        </span>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\status-banner.blade.php ENDPATH**/ ?>