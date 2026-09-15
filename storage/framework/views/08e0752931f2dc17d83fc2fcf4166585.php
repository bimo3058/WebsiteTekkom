<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Pengaturan Sistem']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pengaturan Sistem']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Pengaturan Sistem & Operasional</h1>
            <p class="mp-page-sub">Atur jam buka pelayanan peminjaman dan blokir tanggal merah (Hari Libur).</p>
        </div>
    </div>

    <div style="display: flex; gap: 24px; margin-top: 24px; align-items: flex-start; flex-wrap: wrap;">

        
        <div class="mp-card" style="flex: 1; min-width: 300px; max-width: 400px; border-radius: 12px;">
            <div class="mp-card-header" style="background: #FAFBFC;">
                <h3 class="mp-card-title" style="font-size: 14px;">Jam Operasional</h3>
            </div>
            <form method="POST" action="<?php echo e(route('eoffice.peminjaman.admin.pengaturan.operasional')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mp-card-body" style="padding: 24px;">
                    <div style="margin-bottom: 20px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Jam
                            Buka Peminjaman</label>
                        <input type="time" name="jam_buka" class="mp-input" value="<?php echo e($jamBuka ?? '08:00'); ?>"
                            style="padding: 10px 14px; font-size: 14px;" required>
                    </div>
                    <div style="margin-bottom: 24px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Jam
                            Tutup (Batas Akhir)</label>
                        <input type="time" name="jam_tutup" class="mp-input" value="<?php echo e($jamTutup ?? '16:00'); ?>"
                            style="padding: 10px 14px; font-size: 14px;" required>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Batas
                            Minimal H- Booking (Hari)</label>
                        <input type="number" name="batas_h_min_booking" min="0" max="30" class="mp-input"
                            value="<?php echo e($batasHMinBooking ?? 0); ?>" style="padding: 10px 14px; font-size: 14px;" required>
                        <p style="font-size: 11px; color: #6B7280; margin-top: 6px;">Contoh: Isi <strong>2</strong> jika
                            booking minimal harus 2 hari sebelum pemakaian. Isi <strong>0</strong> untuk mengizinkan
                            booking di hari yang sama.</p>
                    </div>

                    <div style="background: #F8F9FB; padding: 16px; border-radius: 10px; border: 1px solid #E5E7EB;">
                        <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer;">
                            <input type="checkbox" name="buka_akhir_pekan" value="1" <?php echo e($bukaAkhirPekan ? 'checked' : ''); ?> style="margin-top: 2px; width: 16px; height: 16px; accent-color: #0B266E;">
                            <div>
                                <span style="display:block; font-size:13px; font-weight:600; color:#111827;">Buka di
                                    Akhir Pekan</span>
                                <span style="display:block; font-size:12px; color:#6B7280; margin-top:2px;">Jika aktif,
                                    pengguna bisa pinjam ruangan di hari Sabtu & Minggu.</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top:1px solid #E5E7EB; background:#fff; text-align:right;">
                    <button type="submit" class="mp-btn primary"
                        style="padding: 10px 24px; font-size: 13px; border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        
        <div class="mp-card" style="flex: 2; min-width: 400px; border-radius: 12px;">
            <div class="mp-card-header" style="background: #FAFBFC;">
                <h3 class="mp-card-title" style="font-size: 14px;">Tanggal Merah (Blackout Dates)</h3>
            </div>
            <div class="mp-card-body" style="padding: 24px;">
                <form method="POST" action="<?php echo e(route('eoffice.peminjaman.admin.pengaturan.libur')); ?>"
                    style="display:flex; gap:16px; align-items: flex-end; margin-bottom: 24px; background: #FAFBFC; padding: 20px; border-radius: 10px; border: 1px solid #E5E7EB;">
                    <?php echo csrf_field(); ?>
                    <div style="flex: 1.2;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Pilih
                            Tanggal</label>
                        <input type="date" name="tanggal" class="mp-input"
                            style="padding: 10px 14px; font-size: 14px; width: 100%; box-sizing: border-box;" required>
                    </div>
                    <div style="flex: 2;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:8px; color: #4B5563;">Keterangan
                            / Nama Libur</label>
                        <input type="text" name="keterangan" class="mp-input"
                            style="padding: 10px 14px; font-size: 14px; width: 100%; box-sizing: border-box;"
                            placeholder="Misal: Libur Idul Fitri" required>
                    </div>
                    <div style="padding-bottom: 0;">
                        <button type="submit" class="mp-btn primary"
                            style="padding: 10px 20px; font-size: 13px; border-radius: 8px; white-space: nowrap;">+
                            Tambah Libur</button>
                    </div>
                </form>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div
                        style="background: #FEF2F2; color: #991B1B; padding: 10px 14px; border-radius: 8px; margin-top:-14px; margin-bottom: 16px; font-size:13px; border: 1px solid #FCA5A5;">
                        <?php echo e($message); ?>

                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mp-table-wrap" style="border:1px solid #E5E7EB; border-radius: 8px;">
                    <table class="mp-table" style="margin: 0;">
                        <thead>
                            <tr>
                                <th style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px;">TANGGAL LIBUR
                                </th>
                                <th style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px;">KETERANGAN
                                </th>
                                <th
                                    style="background: #F8F9FB; padding-top: 12px; padding-bottom: 12px; text-align:right;">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tanggalLibur; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $libur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="mp-tr">
                                    <td style="font-weight:600; color: #111827;">
                                        <?php echo e(\Carbon\Carbon::parse($libur->tanggal)->translatedFormat('d F Y')); ?>

                                    </td>
                                    <td style="color: #4B5563;"><?php echo e($libur->keterangan); ?></td>
                                    <td style="text-align:right;">
                                        <form method="POST"
                                            action="<?php echo e(route('eoffice.peminjaman.admin.pengaturan.libur.destroy', $libur->id)); ?>"
                                            onsubmit="return confirm('Hapus tanggal libur ini?');" style="margin:0;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="mp-btn secondary sm"
                                                style="padding:8px 8px; color: #DC2626; border-color: #FCA5A5; background: #FEF2F2; display: inline-flex;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <path
                                                        d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="3"
                                        style="text-align:center; padding: 30px; color:#9CA3AF; font-size:13px; font-style: italic;">
                                        Belum ada hari libur / blackout date yang ditambahkan.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\setting\index.blade.php ENDPATH**/ ?>