<?php if (isset($component)) { $__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.koordinator','data' => ['title' => 'Edit Penilaian']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.koordinator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Penilaian']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="max-w-5xl mx-auto space-y-6 pb-20">

        <form action="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.update', $rubrik->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div class="flex justify-between items-center mb-6">
                <a href="<?php echo e(route('eoffice.kp.koordinator.master-rubrik.index')); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-[#E2E8F0] shadow-sm text-[13px] font-semibold text-[#344054] rounded-lg hover:bg-gray-50 transition-colors"
                    style="font-family: 'Inter Tight', sans-serif;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>

                <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 bg-[#2E3C5B] text-white text-[13px] font-semibold rounded-lg hover:bg-[#1D2742] transition-colors shadow-sm"
                    style="font-family: 'Inter Tight', sans-serif;">
                    Simpan Perubahan
                </button>
            </div>

            <div class="mb-8">
                <h1 class="text-[24px] font-bold text-[#0D0D12]" style="font-family: 'Inter Tight', sans-serif;">Edit
                    Penilaian</h1>
                <p class="text-[14px] text-[#666D80] font-medium mt-1" style="font-family: 'Inter Tight', sans-serif;">
                    Ubah template komponen penilaian <?php echo e($rubrik->kode); ?></p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">
                    <ul class="list-disc pl-5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><?php echo e($error); ?></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="bg-white border border-[#E2E8F0] rounded-[16px] p-6 md:p-8"
                style="box-shadow: 0 1px 2px rgba(16,24,40,0.05);">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                    
                    <div class="md:col-span-4">
                        <h2 class="text-[15px] font-bold text-[#0D0D12] mb-1.5"
                            style="font-family: 'Inter Tight', sans-serif;">Informasi Penilaian</h2>
                        <p class="text-[13px] text-[#666D80] font-medium leading-relaxed"
                            style="font-family: 'Inter Tight', sans-serif;">Tambahkan detail dari komponen penilaian
                            baru.</p>
                    </div>

                    
                    <div class="md:col-span-8 flex flex-col gap-6">

                        
                        <div>
                            <label class="block text-[13px] font-semibold text-[#344054] mb-1.5"
                                style="font-family: 'Inter Tight', sans-serif;">Kode<span
                                    class="text-[#D92D20] ml-0.5">*</span></label>
                            <input type="text" name="kode" value="<?php echo e(old('kode', $rubrik->kode)); ?>"
                                placeholder="contoh : CPMK-1" required
                                class="w-full h-[40px] px-3.5 bg-white border border-[#D0D5DD] rounded-lg text-[14px] text-[#101828] placeholder-[#98A2B3] focus:border-[#0065FF] focus:ring-1 focus:ring-[#0065FF] outline-none transition-colors shadow-sm"
                                style="font-family: 'Inter Tight', sans-serif;">
                            <p class="mt-1.5 text-[12px] text-[#667085] font-medium"
                                style="font-family: 'Inter Tight', sans-serif;">Identitas unik untuk komponen ini
                                (contoh: CPMK-1, CPL-3).</p>
                        </div>

                        
                        <div x-data="{ count: <?php echo e(strlen(old('deskripsi', $rubrik->deskripsi))); ?> }">
                            <label class="block text-[13px] font-semibold text-[#344054] mb-1.5"
                                style="font-family: 'Inter Tight', sans-serif;">Deskripsi <span
                                    class="text-[#D92D20] ml-0.5">*</span></label>
                            <div class="relative">
                                <textarea name="deskripsi" rows="3" required maxlength="200"
                                    @input="count = $event.target.value.length"
                                    placeholder="Deskripsi opsional untuk menjelaskan komponen ini"
                                    class="w-full p-3.5 pb-8 bg-white border border-[#D0D5DD] rounded-lg text-[14px] text-[#101828] placeholder-[#98A2B3] focus:border-[#0065FF] focus:ring-1 focus:ring-[#0065FF] outline-none transition-colors resize-none shadow-sm"
                                    style="font-family: 'Inter Tight', sans-serif;"><?php echo e(old('deskripsi', $rubrik->deskripsi)); ?></textarea>
                                <div class="absolute bottom-2.5 left-3.5 text-[12px] text-[#98A2B3] font-medium"
                                    style="font-family: 'Inter Tight', sans-serif;" x-text="count + '/200'"></div>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-[13px] font-semibold text-[#344054] mb-1.5"
                                style="font-family: 'Inter Tight', sans-serif;">Role Penilai<span
                                    class="text-[#D92D20] ml-0.5">*</span></label>
                            <div class="relative">
                                <select name="role_penilai" required
                                    class="w-full h-[40px] px-3.5 bg-white border border-[#D0D5DD] rounded-lg text-[14px] text-[#101828] appearance-none outline-none focus:border-[#0065FF] focus:ring-1 focus:ring-[#0065FF] transition-colors cursor-pointer pr-10 shadow-sm"
                                    style="font-family: 'Inter Tight', sans-serif;">
                                    <option value="dosen_pembimbing" <?php echo e(old('role_penilai', $rubrik->role_penilai) == 'dosen_pembimbing' ? 'selected' : ''); ?>>Dosen Pembimbing
                                    </option>
                                    <option value="koordinator" <?php echo e(old('role_penilai', $rubrik->role_penilai) == 'koordinator' ? 'selected' : ''); ?>>Koordinator</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#98A2B3]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div>
                                <label class="block text-[13px] font-semibold text-[#344054] mb-1.5"
                                    style="font-family: 'Inter Tight', sans-serif;">Bobot (%) <span
                                        class="text-[#D92D20] ml-0.5">*</span></label>
                                <input type="number" name="bobot" value="<?php echo e(old('bobot', $rubrik->bobot)); ?>" min="0"
                                    max="100" required placeholder="(0-100)"
                                    class="w-full h-[40px] px-3.5 bg-white border border-[#D0D5DD] rounded-lg text-[14px] text-[#101828] placeholder-[#98A2B3] focus:border-[#0065FF] focus:ring-1 focus:ring-[#0065FF] outline-none transition-colors shadow-sm"
                                    style="font-family: 'Inter Tight', sans-serif;">
                                <p class="mt-1.5 text-[12px] text-[#667085] font-medium"
                                    style="font-family: 'Inter Tight', sans-serif;">Persentase bobot untuk komponen ini
                                    (0–100).</p>
                            </div>

                            <div>
                                <label class="block text-[13px] font-semibold text-[#344054] mb-1.5"
                                    style="font-family: 'Inter Tight', sans-serif;">Set Aktif<span
                                        class="text-[#D92D20] ml-0.5">*</span></label>
                                <div class="mt-2.5">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $rubrik->is_active) ? 'checked' : ''); ?> class="sr-only peer">
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2E3C5B]">
                                        </div>
                                    </label>
                                </div>
                                <p class="mt-2 text-[12px] text-[#667085] font-medium pr-4"
                                    style="font-family: 'Inter Tight', sans-serif;">Template yang tidak aktif tidak akan
                                    muncul pada konfigurasi periode.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4)): ?>
<?php $attributes = $__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4; ?>
<?php unset($__attributesOriginal95cb0509b50d1cf2ddf4aa1e75407aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4)): ?>
<?php $component = $__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4; ?>
<?php unset($__componentOriginal95cb0509b50d1cf2ddf4aa1e75407aa4); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\koordinator\rubrik\edit.blade.php ENDPATH**/ ?>