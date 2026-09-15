<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Praktikum[] $praktikums */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
    /** @var \Illuminate\Support\Collection|array $dosenTerbaru */
    /** @var \Illuminate\Support\Collection|array $matkulTerbaru */
    $name      = auth()->user()->name;
    $firstName = explode(' ', $name)[0];
    $semesterLabel = $semesterLabel ?? 'Semester Genap 2025/2026';
?>

 <?php $__env->slot('header', null, []); ?> 
<div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">Dashboard</h1>
            <span style="font-size:10px; font-weight:600; color:var(--c-primary); background:rgba(94,83,244,0.09); border:1px solid rgba(94,83,244,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Admin</span>
        </div>
        <p style="font-size:12px; color:var(--c-fg-muted);">
            Selamat datang, <span style="color:var(--c-fg); font-weight:600;"><?php echo e($firstName); ?></span>
            <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
            <span style="margin-left:4px; color:var(--c-fg-muted);"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> · <?php echo e($semesterLabel); ?></span>
        </p>
    </div>
</div>
 <?php $__env->endSlot(); ?>

<div class="mp-stats-grid cols-3" style="flex-shrink:0;">

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Praktikum Aktif</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e($totalPraktikumAktif ?? 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Saat Ini</div>
    </div>

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon sky">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Total Dosen</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e($totalDosen ?? 0); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Terdaftar di Sistem</div>
    </div>

    
    <div class="mp-stat" style="border:1px solid var(--c-border); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div class="mp-stat-icon yellow">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg);">Pendaftaran</div>
        </div>
        <div class="mp-stat-value" style="font-size:32px;"><?php echo e(($totalAsprakPending ?? 0) + ($totalKoorPending ?? 0)); ?></div>
        <div class="mp-stat-sub" style="font-size:13px;">Perlu Peninjauan</div>
    </div>

</div>


<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; flex-shrink:0;">

    
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Praktikum</div>
            <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.index')); ?>"
               style="font-size:12px; font-weight:600; color:var(--c-primary); text-decoration:none;">Lihat Semua &rarr;</a>
        </div>
        <div style="display:grid; grid-template-columns:1fr 100px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Status</div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikums ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div style="display:grid; grid-template-columns:1fr 100px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
            <div style="font-size:13px; font-weight:600; color:var(--c-fg);"><?php echo e($p->nama); ?></div>
            <div style="text-align:right;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'aktif'): ?>
                <span class="mp-badge success sm">Aktif</span>
                <?php else: ?>
                <span class="mp-badge neutral sm">Tutup</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Daftar Praktikum Aktif belum tersedia</span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
            <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Pendaftaran</div>
        </div>
        <div style="display:grid; grid-template-columns:1fr auto; gap:16px; padding:12px 20px; border-bottom:1px solid var(--c-border); background:#FAFAFA;">
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec);">Nama Praktikum</div>
            <div style="font-size:12px; font-weight:600; color:var(--c-fg-sec); text-align:right;">Jenis Pendaftaran</div>
        </div>
        <?php
            $listPendaftaran = [];
            if(isset($periodeBuka)) {
                foreach($periodeBuka as $periode) {
                    $isKoor = $periode->jenis === 'koor';
                    $listPendaftaran[] = [
                        'nama' => $periode->praktikum?->nama ?? 'Praktikum', 
                        'jenis' => $isKoor ? 'Koordinator Praktikum' : 'Asisten Praktikum',
                        'bg' => $isKoor ? 'rgba(94,83,244,0.1)' : '#DDF2EE',
                        'text' => $isKoor ? 'var(--c-primary)' : '#287F6E',
                        'border' => $isKoor ? 'rgba(94,83,244,0.2)' : '#40C4AA'
                    ];
                }
            }
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = array_slice($listPendaftaran, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div style="display:grid; grid-template-columns:1fr auto; gap:16px; padding:14px 20px; border-bottom:1px solid var(--c-border); align-items:center;">
            <div style="font-size:13px; font-weight:600; color:var(--c-fg);"><?php echo e($pend['nama']); ?></div>
            <div style="text-align:right;">
                <span style="display:inline-block; font-size:11px; font-weight:600; background:<?php echo e($pend['bg']); ?>; color:<?php echo e($pend['text']); ?>; border:1px solid <?php echo e($pend['border']); ?>; padding:2px 8px; border-radius:6px;"><?php echo e($pend['jenis']); ?></span>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div style="padding:40px 24px; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                <path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"/>
            </svg>
            <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); max-width:280px; line-height:1.5;">Pendaftaran Koordinator Praktikum dan Asisten Praktikum belum tersedia</span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>


<div id="daftar-dosen" style="margin-top:24px; background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); flex-shrink:0;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--c-border);">
        <div style="font-size:15px; font-weight:700; color:var(--c-fg);">Daftar Dosen</div>
        
        <form id="dosen_filter_form" method="GET" action="<?php echo e(url()->current()); ?>#daftar-dosen" style="display:flex; gap:10px; margin:0;">
            <div class="relative flex items-center">
                <svg class="absolute left-3 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search_dosen" value="<?php echo e(request('search_dosen')); ?>" placeholder="Search dosen" onchange="this.form.submit()" style="padding:6px 12px 6px 32px; border:1px solid var(--c-border); border-radius:8px; font-size:13px; outline:none; min-width:200px;">
            </div>
        </form>
    </div>
    
    <div class="grid gap-4 px-5 py-3 bg-[#FAFAFA] border-b border-[#DFE1E7]"
         style="grid-template-columns: 50px 2fr 1.5fr 150px 130px;">
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">No</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Nama Dosen</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">Email</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase">NIP</div>
        <div class="text-[11px] font-semibold text-[#666D80] tracking-[0.06em] uppercase text-center">Praktikum Diampu</div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dosenTerbaru ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $parts   = explode(' ', $d['name'] ?? 'D');
        $ini     = strtoupper(substr($parts[0] ?? 'D', 0, 1) . substr($parts[1] ?? $parts[0], 0, 1));
    ?>
    <div class="grid gap-4 px-5 py-4 border-b border-[#F6F8FA] hover:bg-[#FAFAFA] transition-colors items-center"
         style="grid-template-columns: 50px 2fr 1.5fr 150px 130px;">
        
        <div class="text-[12px] font-semibold text-[#666D80]">
            <?php echo e($dosenPaginator->firstItem() + $i); ?>

        </div>

        
        <div class="flex items-center gap-[10px] min-w-0 pr-3">
            <div class="mp-av sky flex-shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d['avatar_url'] ?? null): ?>
                <img src="<?php echo e($d['avatar_url']); ?>" alt="" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                <?php else: ?>
                <?php echo e($ini); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="min-w-0">
                <div class="text-[13px] font-semibold text-[#0D0D12] truncate"><?php echo e($d['name']); ?></div>
            </div>
        </div>

        
        <div class="text-[12px] text-[#666D80] truncate"><?php echo e($d['email']); ?></div>
        
        
        <div class="text-[12px] font-medium text-[#353849]"><?php echo e($d['employee_number']); ?></div>
        
        
        <div class="text-center">
            <span class="text-[13px] font-medium text-[#353849]"><?php echo e($d['jumlah_praktikum'] ?? 0); ?></span>
            <span class="text-[12px] font-medium text-[#353849]"> praktikum</span>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div style="padding:32px; text-align:center; font-size:13px; color:var(--c-fg-muted);">Belum ada data dosen.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dosenPaginator->hasPages() || $dosenPaginator->total() > 0): ?>
    <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div x-data="{ open: false, selected: '<?php echo e(request('per_page', 10)); ?>', options: [5, 10, 20] }" 
                 class="relative flex items-center gap-2 border border-[#DFE1E7] rounded-[8px] px-2 py-1 cursor-pointer bg-white hover:bg-[#F6F8FA] transition-colors"
                 @click="open = !open">
                <span class="text-[12px] text-[#666D80]">Per halaman</span>
                <div class="flex items-center gap-1 font-semibold text-[12px] text-[#0D0D12]">
                    <span x-text="selected"></span>
                    <svg class="w-3 h-3 text-[#666D80] transition-transform duration-200" :class="{'rotate-180': open}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <div x-show="open" @click.away="open = false" style="display: none;" 
                     class="absolute bottom-full left-0 mb-1 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-lg shadow-[0_4px_12px_rgba(0,0,0,0.08)] py-1.5 overflow-hidden">
                    <template x-for="option in options" :key="option">
                        <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-[#F6F8FA] cursor-pointer text-[12px] font-medium text-[#353849]">
                            <input type="radio" :value="option" name="per_page" form="dosen_filter_form" x-model="selected" @change="document.getElementById('dosen_filter_form').submit()" class="hidden">
                            <div class="w-3 h-3 rounded-full border flex items-center justify-center transition-colors flex-shrink-0" :class="selected == option ? 'border-[#0B266E]' : 'border-[#DFE1E7]'">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0B266E]" x-show="selected == option" style="display: none;"></div>
                            </div>
                            <span x-text="option"></span>
                        </label>
                    </template>
                </div>
            </div>
            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan <?php echo e($dosenPaginator->firstItem() ?? 0); ?> sampai <?php echo e($dosenPaginator->lastItem() ?? 0); ?> dari <?php echo e($dosenPaginator->total()); ?> data</div>
        </div>
        
        <div style="display:flex; gap:4px;">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dosenPaginator->onFirstPage()): ?>
                <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            <?php else: ?>
                <a href="<?php echo e($dosenPaginator->previousPageUrl()); ?>" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php
                $current = $dosenPaginator->currentPage();
                $last = $dosenPaginator->lastPage();
                
                if ($current % 3 == 1) {
                    $start = $current;
                } else {
                    $start = $current - 1;
                }

                // Jika sudah mentok di halaman terakhir
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
                    <span style="width:32px; height:32px; border:none; background:var(--c-primary); color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;"><?php echo e($i); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($dosenPaginator->url($i)); ?>" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:13px; font-weight:600; color:var(--c-fg); text-decoration:none;"><?php echo e($i); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $last): ?>
                <span style="width:32px; height:32px; display:flex; align-items:center; justify-content:center; font-size:13px; color:var(--c-fg-sec);">...</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dosenPaginator->hasMorePages()): ?>
                <a href="<?php echo e($dosenPaginator->nextPageUrl()); ?>" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\manajemen-praktikum\admin\dashboard.blade.php ENDPATH**/ ?>