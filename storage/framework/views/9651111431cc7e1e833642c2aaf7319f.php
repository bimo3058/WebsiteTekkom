<?php if (isset($component)) { $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.gpm-master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.gpm-master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('banksoal.soal.gpm.parameter.index')); ?>" class="text-slate-500 hover:text-primary transition-colors">Manajemen Parameter</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah Parameter</span>
    <?php $__env->stopSection(); ?>

    <?php if (isset($component)) { $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.notification.alerts','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::notification.alerts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $attributes = $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $component = $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>

    <div class="max-w-3xl mx-auto py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Tambah Parameter Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Definisikan aspek penilaian baru untuk validasi RPS atau Bank Soal.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <form action="<?php echo e(route('banksoal.soal.gpm.parameter.store')); ?>" method="POST" class="p-8 space-y-6" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
                <?php echo csrf_field(); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="jenis" class="text-sm font-bold text-slate-700">Jenis Penilaian <span class="text-rose-500">*</span></label>
                        <select name="jenis" id="jenis" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none <?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="rps" <?php echo e(old('jenis') == 'rps' ? 'selected' : ''); ?>>Validasi RPS</option>
                            <option value="soal" <?php echo e(old('jenis') == 'soal' ? 'selected' : ''); ?>>Validasi Bank Soal</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jenis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label for="bobot" class="text-sm font-bold text-slate-700">Bobot (Poin) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="bobot" id="bobot" value="<?php echo e(old('bobot', 10)); ?>" min="1" max="100" class="w-full pl-4 pr-12 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none <?php $__errorArgs = ['bobot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Poin</span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bobot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="aspek" class="text-sm font-bold text-slate-700">Aspek Parameter <span class="text-rose-500">*</span></label>
                    <textarea name="aspek" id="aspek" rows="3" placeholder="Contoh: Kesesuaian CPL dengan Materi Pembelajaran" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none <?php $__errorArgs = ['aspek'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><?php echo e(old('aspek')); ?></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['aspek'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="<?php echo e(route('banksoal.soal.gpm.parameter.index')); ?>" class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-2.5 rounded-xl bg-[#0B266E] text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all">
                        Simpan Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $attributes = $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $component = $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\gpm\parameter\create.blade.php ENDPATH**/ ?>