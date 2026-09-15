<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Periode Pendaftaran']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Periode Pendaftaran']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Periode Pendaftaran</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Buka / tutup periode pendaftaran koor, asprak &amp; praktikan per mata kuliah · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Periode Baru</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Buka Periode Pendaftaran Baru</span>
    </div>
    <div style="padding:24px;">

        
        <form method="GET" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.index')); ?>" id="form-matkul">
            <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                1. Pilih Mata Kuliah Praktikum <span style="color:#DF1C41;">*</span>
            </label>
            <select name="matkul_id" onchange="document.getElementById('form-matkul').submit()"
                    class="mp-input mp-select w-full">
                <option value="">— Pilih Mata Kuliah Praktikum —</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $matkulList->groupBy('semester'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <optgroup label="Semester <?php echo e($sem); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($mk->id); ?>" <?php echo e($matkulId == $mk->id ? 'selected' : ''); ?>>
                        [<?php echo e($mk->kode); ?>] <?php echo e($mk->nama); ?> (<?php echo e($mk->sks); ?> SKS)
                    </option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </optgroup>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matkulList->isEmpty()): ?>
            <p style="margin-top:4px;font-size:11px;color:#DF1C41;">
                Belum ada data matkul. Jalankan:
                <code style="background:#F6F8FA;padding:0 4px;border-radius:4px;">php artisan db:seed --class=MatkulPraktikumSeeder</code>
            </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matkulDipilih): ?>

        
        <div style="display:flex;align-items:center;gap:12px;margin-top:16px;padding:12px 16px;border-radius:10px;background:#EEF2FF;">
            <span style="font-family:monospace;font-size:12px;font-weight:700;color:#0B266E;background:white;padding:2px 8px;border-radius:4px;border:1px solid #C7D2FE;">
                <?php echo e($matkulDipilih->kode); ?>

            </span>
            <div style="flex:1;font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($matkulDipilih->nama); ?></div>
            <span style="font-size:11px;color:#666D80;">Sem <?php echo e($matkulDipilih->semester); ?> &middot; <?php echo e($matkulDipilih->sks); ?> SKS</span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumLinked->isEmpty()): ?>

        <div class="mp-alert warning flex-shrink-0" style="margin-top:16px;">
            <div style="font-size:13px;font-weight:600;color:#7C5309;margin-bottom:4px;">
                Belum ada kelas praktikum yang aktif untuk mata kuliah ini
            </div>
            <div style="font-size:12px;color:#956321;margin-bottom:12px;">
                Anda tidak dapat membuka periode pendaftaran karena belum ada kelas praktikum yang terbentuk untuk mata kuliah ini di semester berjalan. Silakan buat kelas praktikum baru terlebih dahulu.
            </div>
            <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.index')); ?>" class="mp-btn primary md" style="text-decoration:none;display:inline-flex;">
                Buat Praktikum Baru &rarr;
            </a>
        </div>

        <?php else: ?>

        
        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.store')); ?>"
              style="margin-top:20px;display:flex;flex-direction:column;gap:16px;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="matkul_id_filter" value="<?php echo e($matkulDipilih->id); ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        2. Pilih Praktikum <span style="color:#DF1C41;">*</span>
                    </label>
                    <select name="praktikum_id" required class="mp-input mp-select w-full">
                        <option value="">— Pilih Praktikum —</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumLinked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo e($praktikumId == $p->id ? 'selected' : ''); ?>>
                            <?php echo e($p->nama); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->kode): ?> [<?php echo e($p->kode); ?>] <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            &middot; <?php echo e($p->semester); ?> <?php echo e($p->tahun_ajaran); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->dosen): ?> &middot; <?php echo e($p->dosen->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        3. Jenis Pendaftaran <span style="color:#DF1C41;">*</span>
                    </label>
                    <select name="jenis" required class="mp-input mp-select w-full">
                        <option value="koor">Koordinator Praktikum</option>
                        <option value="asprak">Asisten Praktikum</option>

                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">
                        Nama Periode <span style="color:#808897;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="text" name="nama" placeholder="cth: Koor Ganjil 2025/2026" class="mp-input w-full">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Dibuka Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="dibuka_pada" required class="mp-input w-full">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:4px;">Ditutup Pada <span style="color:#DF1C41;">*</span></label>
                        <input type="datetime-local" name="ditutup_pada" required class="mp-input w-full">
                    </div>
                </div>
            </div>

            <div class="mp-alert warning flex-shrink-0">
                <strong>Info:</strong> Setelah dibuka, notifikasi dikirim ke <strong>seluruh pengguna</strong> aktif di sistem.
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="mp-btn primary md">Buka Periode &amp; Kirim Notifikasi</button>
            </div>
        </form>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

        <?php else: ?>
        
        <div style="margin-top:16px;border-radius:10px;padding:32px;text-align:center;background:#F6F8FA;border:1px dashed #DFE1E7;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <div style="font-size:13px;color:#808897;">Pilih mata kuliah praktikum di atas untuk melanjutkan.</div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Periode</span>
    <span class="sec-rule"></span>
    <form method="GET" style="display:flex;gap:8px;align-items:center;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matkulId): ?><input type="hidden" name="matkul_id" value="<?php echo e($matkulId); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select">
            <option value="">Semua Praktikum</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumLinked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <option value="<?php echo e($p->id); ?>" <?php echo e($praktikumId == $p->id ? 'selected' : ''); ?>><?php echo e($p->nama); ?></option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </select>
    </form>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat Periode Pendaftaran</span>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $periodeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php $sedangBuka = $periode->isSedangBuka(); ?>
    <div class="mp-tr" style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;"
         onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
         onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;flex-wrap:wrap;">
                <div style="font-size:14px;font-weight:700;color:#0D0D12;"><?php echo e($periode->nama); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->is_aktif && $sedangBuka): ?>
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                <?php elseif($periode->is_aktif && !$sedangBuka): ?>
                <span class="mp-badge warning sm"><span class="dot"></span>Terjadwal</span>
                <?php else: ?>
                <span class="mp-badge neutral sm">Ditutup</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->jenis === 'koor'): ?>
                <span class="mp-badge navy sm">Koor</span>
                <?php elseif($periode->jenis === 'asprak'): ?>
                <span class="mp-badge success sm">Asprak</span>
                <?php else: ?>
                <span class="mp-badge sky sm">Praktikan</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="font-size:12px;color:#666D80;">
                Praktikum: <span style="font-weight:500;color:#353849;"><?php echo e($periode->praktikum?->nama ?? '—'); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->praktikum?->matkul): ?>
                &middot; <span style="font-family:monospace;font-weight:700;color:#0B266E;"><?php echo e($periode->praktikum->matkul->kode); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->dibuka_pada): ?> &middot; Buka: <span style="font-weight:500;color:#0D0D12;"><?php echo e($periode->dibuka_pada->format('d M Y H:i')); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->ditutup_pada): ?> &middot; Tutup: <span style="font-weight:500;color:#0D0D12;"><?php echo e($periode->ditutup_pada->format('d M Y H:i')); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="font-size:11px;color:#808897;margin-top:2px;">
                Dibuka oleh: <?php echo e($periode->dibukaOleh?->name ?? '—'); ?> &middot; <?php echo e($periode->created_at?->format('d M Y')); ?>

            </div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <a href="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.edit', $periode->id)); ?>"
               class="mp-btn secondary sm" style="text-decoration:none;">Edit</a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periode->is_aktif): ?>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.tutup', $periode->id)); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="mp-btn ghost sm">Tutup</button>
            </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.periode-pendaftaran.destroy', $periode->id)); ?>"
                  onsubmit="return confirm('Hapus periode ini?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="mp-btn secondary sm" style="color:#DF1C41;border-color:#DF1C41;">Hapus</button>
            </form>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div style="padding:48px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada periode pendaftaran.</div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayatPengumuman->isNotEmpty()): ?>
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Riwayat Pengumuman Sistem</span>
    <span class="sec-rule"></span>
    <span style="font-size:12px;color:#808897;">Dibuat otomatis saat periode dibuka/ditutup</span>
</div>

<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Riwayat Pengumuman Otomatis</span>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayatPengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php $dihapus = !is_null($pg->deleted_at); ?>
    <div class="mp-tr" style="display:flex;align-items:flex-start;gap:16px;padding:16px 20px;border-bottom:1px solid #DFE1E7;<?php echo e($dihapus ? 'opacity:0.55;' : ''); ?>">
        <div class="mp-stat-icon <?php echo e($pg->tipe_sistem === 'buka' ? 'green' : 'red'); ?>" style="flex-shrink:0;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->tipe_sistem === 'buka'): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($pg->judul); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->tipe_sistem === 'buka'): ?>
                <span class="mp-badge success sm"><span class="dot"></span>Dibuka</span>
                <?php else: ?>
                <span class="mp-badge error sm"><span class="dot"></span>Ditutup</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dihapus): ?>
                <span class="mp-badge neutral sm">Disembunyikan dari publik</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div style="font-size:12px;color:#666D80;">
                Praktikum: <span style="font-weight:500;color:#353849;"><?php echo e($pg->praktikum?->nama ?? '—'); ?></span>
                &middot; Dibuat: <span style="font-weight:500;"><?php echo e($pg->created_at?->format('d M Y, H:i')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dihapus): ?>
                &middot; Disembunyikan: <span style="font-weight:500;color:#DF1C41;"><?php echo e($pg->deleted_at->format('d M Y, H:i')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\periode-pendaftaran.blade.php ENDPATH**/ ?>