<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Periode Pendaftaran Asprak']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Periode Pendaftaran Asprak']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran Asisten Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran Asisten Praktikum untuk praktikum yang Anda koordinir · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
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

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Praktikum yang Anda Koordinir</span>
    </div>
    <div style="padding:24px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
        <div class="mp-alert warning">
            Anda belum di-assign sebagai koordinator pada praktikum aktif manapun.
            Hubungi Dosen pengampu untuk melakukan penugasan.
        </div>
        <?php else: ?>
        <form method="GET" action="<?php echo e(route('eoffice.manprak.koordinator.periode-pendaftaran.index')); ?>" id="form-praktikum">
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                Pilih Praktikum <span style="color:#DF1C41;">*</span>
            </label>
            <select name="praktikum_id" onchange="document.getElementById('form-praktikum').submit()"
                    class="mp-input mp-select w-full">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($p->id); ?>" <?php echo e($praktikumId == $p->id ? 'selected' : ''); ?>>
                    <?php echo e($p->nama); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->matkul): ?> — [<?php echo e($p->matkul->kode); ?>] <?php echo e($p->matkul->nama); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    · Sem <?php echo e($p->semester); ?> <?php echo e($p->tahun_ajaran); ?>

                </option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih): ?>
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#ECFDF3;">
            <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($praktikumDipilih->nama); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->matkul): ?>
            <span style="font-size:11px;color:#666D80;"><?php echo e($praktikumDipilih->matkul->kode); ?> · Sem <?php echo e($praktikumDipilih->semester); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih->dosen): ?>
            <span style="font-size:11px;color:#666D80;">Dosen: <?php echo e($praktikumDipilih->dosen->name); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumDipilih): ?>


<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Periode Pendaftaran Asisten Praktikum</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Form Pembukaan Periode</span>
    </div>
    <div style="padding:24px;">
        <?php
            $periodeAktif = $periodeList->firstWhere('is_aktif', true);
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif && $periodeAktif->isSedangBuka()): ?>
        <div class="mp-alert warning" style="margin-bottom:16px;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;">Periode Asprak Sedang Dibuka</div>
            <div style="font-size:12px;color:#956321;margin-top:4px;">
                <strong><?php echo e($periodeAktif->nama); ?></strong>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif->ditutup_pada): ?>
                · Ditutup otomatis: <?php echo e($periodeAktif->ditutup_pada->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?>

                <?php else: ?>
                · Tanpa batas waktu
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $periodeAktif->id)); ?>"
                  style="margin-top:12px;" onsubmit="return confirm('Tutup periode pendaftaran asprak sekarang?')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="mp-btn error sm">🔒 Tutup Periode Sekarang</button>
            </form>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form method="POST" action="<?php echo e(route('eoffice.manprak.koordinator.periode-pendaftaran.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="praktikum_id" value="<?php echo e($praktikumDipilih->id); ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Nama Periode <span style="color:#666D80;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" class="mp-input w-full"
                           placeholder="cth. Pendaftaran Asprak Gasal 2025/2026"
                           value="<?php echo e(old('nama')); ?>">
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <div style="font-size:12px;color:#666D80;padding:8px;background:#F6F8FA;border-radius:8px;width:100%;">
                        💡 Hanya mahasiswa yang terlihat di menu Pendaftaran Asprak ketika periode ini aktif.
                        Membuka periode baru akan otomatis menutup periode aktif sebelumnya.
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
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Kosongkan untuk langsung dibuka saat ini.</p>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Ditutup Pada <span style="color:#DF1C41;">*</span>
                    </label>
                    <input type="datetime-local" name="ditutup_pada" class="mp-input w-full"
                           value="<?php echo e(old('ditutup_pada')); ?>" required>
                    <p style="font-size:11px;color:#666D80;margin-top:4px;">Wajib diisi sebagai batas waktu penutupan pendaftaran.</p>
                </div>
            </div>

            <button type="submit" class="mp-btn primary md">
                📢 Buka Periode Pendaftaran Asisten Praktikum
            </button>
        </form>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeList->isNotEmpty()): ?>
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode Pendaftaran Asprak</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Semua Periode — <?php echo e($praktikumDipilih->nama); ?></span>
        <span class="mp-badge neutral sm"><?php echo e($periodeList->count()); ?> periode</span>
    </div>
    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Nama Periode</th>
                    <th>Dibuka</th>
                    <th>Ditutup</th>
                    <th>Dibuka Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $periodeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td style="font-weight:600;"><?php echo e($p->nama); ?></td>
                    <td style="font-size:12px;">
                        <?php echo e($p->dibuka_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? '—'); ?>

                    </td>
                    <td style="font-size:12px;">
                        <?php echo e($p->ditutup_pada?->locale('id')->isoFormat('D MMM YYYY, HH:mm') ?? 'Tanpa batas'); ?>

                    </td>
                    <td style="font-size:12px;"><?php echo e($p->dibukaOleh?->name ?? '—'); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->isSedangBuka()): ?>
                            <span class="mp-badge success sm"><span class="dot"></span>Sedang Buka</span>
                        <?php elseif($p->is_aktif): ?>
                            <span class="mp-badge warning sm"><span class="dot"></span>Aktif (Belum Buka)</span>
                        <?php else: ?>
                            <span class="mp-badge neutral sm">Ditutup</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->is_aktif): ?>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.koordinator.periode-pendaftaran.tutup', $p->id)); ?>"
                                  onsubmit="return confirm('Tutup periode ini?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="mp-btn error xs">Tutup</button>
                            </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.koordinator.periode-pendaftaran.destroy', $p->id)); ?>"
                                  onsubmit="return confirm('Hapus periode ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="mp-btn neutral xs">Hapus</button>
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

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Log Pengumuman Otomatis</span>
        <span class="mp-badge neutral sm"><?php echo e($riwayatPengumuman->count()); ?> entri</span>
    </div>
    <div class="mp-table-wrap">
        <table class="mp-table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Waktu</th>
                    <th>Visibilitas</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayatPengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td style="font-size:13px;"><?php echo e($pg->judul); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->tipe_sistem === 'buka'): ?>
                            <span class="mp-badge success sm">Buka</span>
                        <?php elseif($pg->tipe_sistem === 'tutup'): ?>
                            <span class="mp-badge error sm">Tutup</span>
                        <?php else: ?>
                            <span class="mp-badge neutral sm"><?php echo e($pg->tipe_sistem); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="font-size:12px;"><?php echo e($pg->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?></td>
                    <td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->trashed()): ?>
                            <span class="mp-badge neutral sm">Disembunyikan</span>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\koordinator\periode-pendaftaran.blade.php ENDPATH**/ ?>