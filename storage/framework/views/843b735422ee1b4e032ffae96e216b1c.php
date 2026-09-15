<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Daftar Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Daftar Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Daftar Praktikum</h1>
        <p class="mp-page-sub">Kelola semua praktikum terdaftar</p>
    </div>
    <div class="mp-page-actions">
        <button onclick="document.getElementById('modalCreate').classList.remove('hidden')" class="mp-btn primary md">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Praktikum
        </button>
    </div>
</div>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter &amp; Pencarian</span>
    <span class="sec-rule"></span>
</div>


<div class="flex gap-3 flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama / kode..."
               class="mp-input flex-1" style="min-width: 150px;">
        <select name="status" class="mp-input mp-select" style="width: 140px;">
            <option value="">Semua Status</option>
            <option value="aktif" <?php echo e(request('status')==='aktif'?'selected':''); ?>>Aktif</option>
            <option value="nonaktif" <?php echo e(request('status')==='nonaktif'?'selected':''); ?>>Nonaktif</option>
        </select>
        <select name="semester" class="mp-input mp-select" style="width: 140px;">
            <option value="">Semua Semester</option>
            <option value="Ganjil" <?php echo e(request('semester')==='Ganjil'?'selected':''); ?>>Ganjil</option>
            <option value="Genap"  <?php echo e(request('semester')==='Genap'?'selected':''); ?>>Genap</option>
        </select>
        <input type="text" name="tahun_ajaran" value="<?php echo e(request('tahun_ajaran')); ?>" placeholder="Tahun Ajaran..."
               class="mp-input" style="width: 140px;">
        <button type="submit" class="mp-btn primary sm">Filter</button>
        <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.index')); ?>" class="mp-btn secondary sm">Reset</a>
    </form>
</div>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Praktikum</span>
    <span class="sec-rule"></span>
</div>


<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; flex:1; min-height:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0;">Daftar Praktikum</h2>
    </div>
    <div class="overflow-x-auto flex-1">
        <table style="width:100%; border-collapse:collapse; min-width:850px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">

                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Praktikum</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Dosen Pengampu</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:140px;">Koordinator</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">Praktikan</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:90px;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikums ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s; cursor:pointer;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'"
                    onclick="window.location='<?php echo e(route('eoffice.manprak.admin.praktikum.detail', $p->id)); ?>'">

                    <td style="padding:14px 16px;">
                        <div style="font-size:13px; font-weight:600; color:#0B266E;" class="truncate"><?php echo e($p->nama); ?></div>
                        <div style="font-size:11px; color:var(--c-fg-muted, #666D80);"><?php echo e($p->tahun_ajaran); ?> / Sem. <?php echo e($p->semester); ?></div>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-muted, #666D80);" class="truncate">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $p->dosens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div style="margin-bottom:2px;"><?php echo e($dosen->name); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            —
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-muted, #666D80);" class="truncate">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->koordinator): ?>
                            <?php echo e($p->koordinator->name); ?>

                        <?php else: ?>
                            <span style="color:#EF4444; font-size:11px; font-weight:600;">Belum ditunjuk</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12); text-align:center;">
                        <?php echo e($p->daftar_praktikan_count ?? 0); ?>

                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'aktif'): ?>
                            <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                        <?php else: ?>
                            <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="padding:14px 16px; text-align:center;">
                        <div class="flex gap-1 justify-center">
                            <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.edit', $p->id)); ?>"
                               class="mp-btn secondary sm" style="padding:5px 8px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.praktikum.destroy', $p->id)); ?>"
                                  onsubmit="return confirm('Hapus praktikum <?php echo e($p->nama); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="mp-btn destructive sm" style="padding:5px 8px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="6" style="padding:64px 20px; text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border, #DFE1E7)" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        <div style="font-size:13px;color:var(--c-fg-muted, #666D80);">Belum ada data praktikum.</div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikums) && method_exists($praktikums, 'links')): ?>
    <div style="padding:12px 20px; border-top:1px solid var(--c-border, #DFE1E7); flex-shrink:0;">
        <?php echo e($praktikums->links()); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div id="modalCreate" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.4);">
    <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DFE1E7]">
            <div class="font-bold text-[16px] text-[#0D0D12]">Tambah Praktikum Baru</div>
            <button onclick="document.getElementById('modalCreate').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#F6F8FA] border-none bg-transparent cursor-pointer text-[#666D80] text-lg">×</button>
        </div>
        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.praktikum.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="mb-4 p-3 bg-[#FFF5F5] border border-[#FCA5A5] rounded-[9px]">
                <div class="text-[#EF4444] text-[12px] font-bold mb-1">Gagal menyimpan data:</div>
                <ul class="list-disc pl-5 text-[11px] text-[#EF4444] m-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($err); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Praktikum <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: Praktikum Algoritma dan Pemrograman"
                           value="<?php echo e(old('nama')); ?>" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Mata Kuliah Praktikum <span class="text-red-500">*</span></label>
                    <select name="matkul_id" required class="mp-input mp-select w-full">
                        <option value="">— Pilih Mata Kuliah —</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($matkulList ?? collect())->groupBy('semester'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <optgroup label="Semester <?php echo e($sem); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($mk->id); ?>" <?php echo e(old('matkul_id') == $mk->id ? 'selected' : ''); ?>>
                                [<?php echo e($mk->kode); ?>] <?php echo e($mk->nama); ?>

                            </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </optgroup>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <input type="text" name="tahun_ajaran" required placeholder="2025/2026" value="<?php echo e(old('tahun_ajaran')); ?>"
                               class="mp-input w-full">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester <span class="text-red-500">*</span></label>
                        <select name="semester" required class="mp-input mp-select w-full">
                            <option value="Ganjil" <?php echo e(old('semester')=='Ganjil'?'selected':''); ?>>Ganjil</option>
                            <option value="Genap"  <?php echo e(old('semester','Genap')=='Genap'?'selected':''); ?>>Genap</option>
                        </select>
                    </div>
                </div>
                <?php
                    $oldDosenIds = old('dosen_ids', ['']);
                    if (!is_array($oldDosenIds) || empty($oldDosenIds)) {
                        $oldDosenIds = [''];
                    }
                ?>
                <div x-data="{ dosens: <?php echo e(json_encode($oldDosenIds)); ?> }">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Dosen Pengampu <span class="text-red-500">*</span></label>
                    <template x-for="(dosen, index) in dosens" :key="index">
                        <div class="flex gap-2 mb-2">
                            <select :name="'dosen_ids[' + index + ']'" x-model="dosens[index]" required class="mp-input mp-select w-full">
                                <option value="">— Pilih Dosen —</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dosenList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <button type="button" x-show="dosens.length > 1" @click="dosens.splice(index, 1)" class="mp-btn destructive sm px-3" style="padding:0 12px;">✕</button>
                        </div>
                    </template>
                    <button type="button" x-show="dosens.length < 3" @click="dosens.push('')" class="mp-btn secondary sm w-full mt-1" style="border:1px dashed #A4ABB8;">+ Tambah Dosen Pengampu</button>
                    <p class="text-[11px] text-[#666D80] mt-1 text-right">Maks. 3 Dosen</p>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="mp-input mp-select w-full">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="mp-btn secondary md flex-1">Batal</button>
                <button type="submit" class="mp-btn primary md flex-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('modalCreate').classList.remove('hidden'))</script>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\praktikum.blade.php ENDPATH**/ ?>