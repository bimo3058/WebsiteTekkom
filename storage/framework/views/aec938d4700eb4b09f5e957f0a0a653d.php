<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pendaftaran Asisten Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pendaftaran Asisten Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Praktikum[] $praktikumList */
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Modules\EOffice\Models\PendaftaranAsprak[] $pendaftaran */
?>
<div class="flex flex-col gap-5 mb-2">
    
    <div class="mp-page-header" style="margin-bottom: 0;">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pendaftaran</h1>
            </div>
            <p class="mp-page-sub">Kelola seleksi pendaftaran Koordinator, Asisten Praktikum, dan Praktikan · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
        </div>
    </div>

    <?php echo $__env->make('eoffice::manajemen-praktikum.admin.partials.registration-tabs', ['activeRegistrationTab' => 'asprak'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<div id="daftar-pendaftaran" class="mp-card">
    
    <div class="p-4 border-b border-[#DFE1E7] bg-white flex items-center justify-between">
        <form id="filter_pendaftaran_asprak_form" method="GET" action="<?php echo e(url()->current()); ?>#daftar-pendaftaran" class="flex w-full gap-3 items-center">
            <?php
                $praktikumOptions = [['value' => '', 'label' => 'Semua Praktikum']];
                foreach($praktikumList as $p) {
                    $praktikumOptions[] = ['value' => (string)$p->id, 'label' => $p->nama];
                }
            ?>
            
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'praktikum_id','options' => $praktikumOptions,'selected' => request('praktikum_id', ''),'placeholder' => 'Semua Praktikum','onChange' => '$event.target.form.submit()','minWidth' => '160px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'praktikum_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($praktikumOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('praktikum_id', '')),'placeholder' => 'Semua Praktikum','onChange' => '$event.target.form.submit()','minWidth' => '160px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $attributes = $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $component = $__componentOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'status_koor','options' => [
                    ['value' => '', 'label' => 'Status Koordinator'],
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak']
                ],'selected' => request('status_koor', ''),'placeholder' => 'Status Koordinator','onChange' => '$event.target.form.submit()','minWidth' => '140px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status_koor','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['value' => '', 'label' => 'Status Koordinator'],
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak']
                ]),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('status_koor', '')),'placeholder' => 'Status Koordinator','onChange' => '$event.target.form.submit()','minWidth' => '140px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $attributes = $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $component = $__componentOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'status','options' => [
                    ['value' => '', 'label' => 'Status Admin'],
                    ['value' => 'pending', 'label' => 'Menunggu'],
                    ['value' => 'approved', 'label' => 'Disetujui'],
                    ['value' => 'rejected', 'label' => 'Ditolak']
                ],'selected' => request('status', ''),'placeholder' => 'Status Admin','onChange' => '$event.target.form.submit()','minWidth' => '140px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                    ['value' => '', 'label' => 'Status Admin'],
                    ['value' => 'pending', 'label' => 'Menunggu'],
                    ['value' => 'approved', 'label' => 'Disetujui'],
                    ['value' => 'rejected', 'label' => 'Ditolak']
                ]),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('status', '')),'placeholder' => 'Status Admin','onChange' => '$event.target.form.submit()','minWidth' => '140px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $attributes = $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $component = $__componentOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
            <div class="relative flex-1 min-w-[150px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Cari nama / NIM..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] border border-[#DFE1E7] rounded-[8px] focus:outline-none focus:border-[#0B266E]"
                       onchange="this.form.submit()">
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->anyFilled(['search','status_koor','status','praktikum_id'])): ?>
            <a href="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.index')); ?>"
               class="mp-btn secondary md px-4" style="height:35px; text-decoration:none; flex-shrink: 0;">Reset</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>

    <div id="table_and_pagination">
    <div class="mp-card-body p-0 overflow-x-auto">
        <div style="min-width: max-content;">
        
        <div class="grid gap-3 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
             style="grid-template-columns: 40px minmax(200px, auto) minmax(220px, auto) 130px minmax(250px, auto) 50px 70px 190px 100px 190px;">
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">No</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Nama Mahasiswa</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Email</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">NIM</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase whitespace-nowrap">Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">IPK</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Motivasi</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Koordinator Praktikum</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Status Admin</div>
            <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center whitespace-nowrap">Aksi</div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pendaftaran ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php
            $nameParts = explode(' ', $pend->user?->name ?? 'AS');
            $initials = strtoupper(substr($nameParts[0] ?? 'A', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'S', 0, 1));
            $avColors = ['sky','navy','green','yellow','violet'];
            $avColor = $avColors[crc32($pend->user?->email ?? '') % count($avColors)];
        ?>
        <div class="grid gap-3 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
             style="grid-template-columns: 40px minmax(200px, auto) minmax(220px, auto) 130px minmax(250px, auto) 50px 70px 190px 100px 190px;">
            
            <div class="text-[12px] font-semibold text-[#666D80]">
                <?php echo e($loop->iteration + ($pendaftaran->firstItem() ?? 1) - 1); ?>

            </div>

            
            <div class="flex items-center gap-[10px] pr-3">
                <div class="mp-av <?php echo e($avColor); ?> flex-shrink-0"><?php echo e($initials); ?></div>
                <div>
                    <div class="text-[13px] font-semibold text-[#0D0D12] whitespace-nowrap"><?php echo e($pend->user?->name ?? '—'); ?></div>
                </div>
            </div>

            
            <div class="text-[12px] text-[#666D80] whitespace-nowrap"><?php echo e($pend->user?->email ?? '—'); ?></div>

            
            <div class="text-[13px] text-[#0D0D12] whitespace-nowrap" style="font-family: 'Inter', sans-serif;">
                <?php echo e($pend->user?->student?->student_number ?? $pend->user?->external_id ?? '—'); ?>

            </div>

            
            <div class="text-[13px] text-[#0D0D12] whitespace-nowrap"><?php echo e($pend->praktikum?->nama ?? '—'); ?></div>
            <div class="text-center">
                <span class="text-[13px] text-[#0D0D12]">
                    <?php echo e(number_format($pend->ipk ?? 0, 2)); ?>

                </span>
            </div>
            <div class="text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->motivasi): ?>
                <span class="mp-badge success sm"><span class="dot"></span>Ada</span>
                <?php else: ?>
                <span class="mp-badge neutral sm">—</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status_koor === 'disetujui'): ?>
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                <?php elseif($pend->status_koor === 'ditolak'): ?>
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                <?php else: ?>
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status === 'approved'): ?>
                <span class="mp-badge success sm"><span class="dot"></span>Disetujui</span>
                <?php elseif($pend->status === 'rejected'): ?>
                <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                <?php else: ?>
                <span class="mp-badge warning sm"><span class="dot"></span>Menunggu</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="text-center flex flex-col items-center justify-center gap-2">
                <a class="mp-btn secondary sm" href="<?php echo e(route('eoffice.manprak.admin.pendaftaran.show', ['type' => 'asprak', 'id' => $pend->id])); ?>">
                    <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Detail &amp; Dokumen
                </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pend->status === 'pending' && $pend->status_koor === 'disetujui'): ?>
                <div class="flex gap-2 justify-center" x-data="{ alasan: '' }">
                    <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.approve', $pend->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="mp-btn" style="padding:6px 8px; background:rgba(16, 185, 129, 0.15); color:#10B981;" title="Terima">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.pendaftaran-asprak.reject', $pend->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="alasan_penolakan" :value="alasan">
                        <button type="button" @click="let r = prompt('Alasan Penolakan:'); if(r!==null){ alasan=r; $el.closest('form').submit(); }" class="mp-btn" style="padding:6px 8px; background:rgba(223, 28, 65, 0.15); color:#DF1C41;" title="Tolak">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                </div>
                <?php elseif($pend->status === 'pending' && $pend->status_koor === 'menunggu'): ?>
                <span class="text-[11px] text-[#666D80]">Tunggu Koordinator</span>
                <?php else: ?>
                <span class="text-[11px] text-[#666D80]">Selesai</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="py-12 flex flex-col items-center justify-center">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mb-3">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 11l-3.5 3.5-1.5-1.5"/>
            </svg>
            <div class="text-[13px] text-[#666D80]">Tidak ada data pendaftaran asisten praktikum.</div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0)): ?>
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '<?php echo e(request('per_page', 5)); ?>', options: [5, 10, 20] }" 
                 class="relative"
                 @click.away="open = false">
                <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                     :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                     @click="open = !open">
                    <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per halaman</span>
                    <div class="flex items-center gap-1 font-semibold text-[12px]">
                        <span x-text="selected"></span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                             :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" 
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>
                <div x-show="open" @click.away="open = false" style="display: none;" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full left-0 mt-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                    <template x-for="option in options" :key="option">
                        <label class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0"
                               :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                            <input type="radio" :value="option" name="per_page" form="filter_pendaftaran_asprak_form" x-model="selected" @change="document.getElementById('filter_pendaftaran_asprak_form').submit()" class="hidden">
                            <span x-text="option"></span>
                            <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan <?php echo e($pendaftaran->firstItem() ?? 0); ?> sampai <?php echo e($pendaftaran->lastItem() ?? 0); ?> dari <?php echo e($pendaftaran->total()); ?> data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendaftaran->onFirstPage()): ?>
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            <?php else: ?>
                <a href="<?php echo e($pendaftaran->previousPageUrl()); ?>#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php
                $current = $pendaftaran->currentPage();
                $last = $pendaftaran->lastPage();
                if ($current % 3 == 1) {
                    $start = $current;
                } else {
                    $start = $current - 1;
                }
                if ($start + 2 > $last) {
                    $start = max(1, $last - 2);
                }
                $end = min($start + 2, $last);
            ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 1): ?>
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = $start; $i <= $end; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i == $current): ?>
                    <span style="width:32px; height:32px; background:var(--c-primary); color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                        <?php echo e($i); ?>

                    </span>
                <?php else: ?>
                    <a href="<?php echo e($pendaftaran->url($i)); ?>#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none;">
                        <?php echo e($i); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $last): ?>
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendaftaran->hasMorePages()): ?>
                <a href="<?php echo e($pendaftaran->nextPageUrl()); ?>#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            <?php else: ?>
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\admin\pendaftaran-asprak.blade.php ENDPATH**/ ?>