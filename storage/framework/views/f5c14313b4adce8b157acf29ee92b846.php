<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Mata Kuliah Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Mata Kuliah Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Mata Kuliah Praktikum</h1>
        <p class="mp-page-sub">Daftar mata kuliah yang memiliki komponen praktikum — Kurikulum 2024 S1 Teknik Komputer UNDIP</p>
    </div>
    <div class="mp-page-actions">
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="mp-btn primary md">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Matkul
        </button>
    </div>
</div>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Filter &amp; Pencarian</span>
    <span class="sec-rule"></span>
</div>


<div class="flex gap-2 flex-wrap items-center flex-shrink-0">
    <form method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari kode atau nama..."
               class="mp-input" style="width:220px;">
        <select name="semester" class="mp-input mp-select">
            <option value="">Semua Semester</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($s = 1; $s <= 8; $s++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <option value="<?php echo e($s); ?>" <?php echo e(request('semester') == $s ? 'selected' : ''); ?>>Semester <?php echo e($s); ?></option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </select>
        <button type="submit" class="mp-btn primary sm">Filter</button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['search','semester'])): ?>
        <a href="<?php echo e(route('eoffice.manprak.admin.matkul-praktikum.index')); ?>" class="mp-btn secondary sm">Reset</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>
    <div class="ml-auto" style="font-size:12px;color:#666D80;">Total: <strong><?php echo e($matkulList->total()); ?></strong> matkul</div>
</div>


<div class="sec-head flex-shrink-0">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Mata Kuliah per Semester</span>
    <span class="sec-rule"></span>
</div>


<?php
    $grouped = $matkulList->getCollection()->groupBy('semester');
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $grouped->sortKeys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<div style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column; margin-bottom: 20px; flex-shrink:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border, #DFE1E7);">
        <div class="flex items-center gap-3">
            <span class="mp-badge primary sm">Semester <?php echo e($sem); ?></span>
            <span style="font-size:13px;font-weight:700;color:var(--c-fg, #0D0D12);"><?php echo e($items->count()); ?> mata kuliah</span>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border, #DFE1E7); background:#FAFAFA;">
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:160px;">Kode MK</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap;">Nama Mata Kuliah</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:80px;">SKS</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted, #666D80); white-space:nowrap; width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;"
                    onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 16px;">
                        <span class="mp-badge primary sm" style="font-family:monospace;"><?php echo e($mk->kode); ?></span>
                    </td>
                    <td style="padding:14px 16px; font-size:13px; font-weight:600; color:var(--c-fg, #0D0D12);"><?php echo e($mk->nama); ?></td>
                    <td style="padding:14px 16px; font-size:13px; font-weight:700; color:var(--c-fg, #353849);"><?php echo e($mk->sks); ?></td>
                    <td style="padding:14px 16px;">
                        <div class="flex gap-2">
                            <button onclick="openEdit(<?php echo e($mk->id); ?>, '<?php echo e(addslashes($mk->kode)); ?>', '<?php echo e(addslashes($mk->nama)); ?>', <?php echo e($mk->sks); ?>, <?php echo e($mk->semester ?? 'null'); ?>)"
                                    class="mp-btn secondary sm">Edit</button>
                            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.matkul-praktikum.destroy', $mk->id)); ?>"
                                  onsubmit="return confirm('Hapus <?php echo e(addslashes($mk->nama)); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="mp-btn destructive sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matkulList->isEmpty()): ?>
<div class="mp-card flex-shrink-0">
    <div style="padding:64px 20px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        <div style="font-size:13px;color:#666D80;">Belum ada mata kuliah praktikum.</div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matkulList->hasPages()): ?>
<div class="flex-shrink-0" style="padding:12px 0;border-top:1px solid #DFE1E7;"><?php echo e($matkulList->links()); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex items-center justify-between">
            <div class="font-bold text-[15px] text-[#0D0D12]">Tambah Mata Kuliah Praktikum</div>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="text-[#A4ABB8] hover:text-[#353849] bg-transparent border-none cursor-pointer text-[20px] leading-none">×</button>
        </div>
        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.matkul-praktikum.store')); ?>" class="px-6 py-5 flex flex-col gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode MK <span class="text-red-500">*</span></label>
                <input type="text" name="kode" required placeholder="cth: TSK1624107" maxlength="20"
                       class="mp-input w-full" style="font-family:monospace;">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required placeholder="cth: Praktikum Pemrograman Dasar"
                       class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">SKS <span class="text-red-500">*</span></label>
                    <input type="number" name="sks" required min="1" max="6" placeholder="1"
                           class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester <span class="text-red-500">*</span></label>
                    <select name="semester" required class="mp-input mp-select w-full">
                        <option value="">— Pilih —</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($s = 1; $s <= 8; $s++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($s); ?>">Semester <?php echo e($s); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button type="submit" class="mp-btn primary md">Simpan</button>
            </div>
        </form>
    </div>
</div>


<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-[16px] w-full max-w-md mx-4 shadow-2xl">
        <div class="px-6 py-4 border-b border-[#DFE1E7] flex items-center justify-between">
            <div class="font-bold text-[15px] text-[#0D0D12]">Edit Mata Kuliah Praktikum</div>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="text-[#A4ABB8] hover:text-[#353849] bg-transparent border-none cursor-pointer text-[20px] leading-none">×</button>
        </div>
        <form id="form-edit" method="POST" action="" class="px-6 py-5 flex flex-col gap-4">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Kode MK <span class="text-red-500">*</span></label>
                <input type="text" id="edit-kode" name="kode" required maxlength="20"
                       class="mp-input w-full" style="font-family:monospace;">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Mata Kuliah <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nama" name="nama" required class="mp-input w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">SKS <span class="text-red-500">*</span></label>
                    <input type="number" id="edit-sks" name="sks" required min="1" max="6" class="mp-input w-full">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Semester <span class="text-red-500">*</span></label>
                    <select id="edit-semester" name="semester" required class="mp-input mp-select w-full">
                        <option value="">— Pilih —</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($s = 1; $s <= 8; $s++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($s); ?>">Semester <?php echo e($s); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="mp-btn secondary md">Batal</button>
                <button type="submit" class="mp-btn primary md">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, kode, nama, sks, semester) {
    const base = '<?php echo e(url("eoffice/manprak/admin/matkul-praktikum")); ?>';
    document.getElementById('form-edit').action = base + '/' + id;
    document.getElementById('edit-kode').value = kode;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-sks').value = sks;
    const sel = document.getElementById('edit-semester');
    for (let o of sel.options) o.selected = (parseInt(o.value) === semester);
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\matkul-praktikum.blade.php ENDPATH**/ ?>