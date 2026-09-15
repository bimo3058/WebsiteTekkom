


<div id="bulkActionBar"
     style="display:none; align-items:center; justify-content:space-between; background:#1A1A2E; border-radius:10px; padding:10px 16px; margin-bottom:12px; box-shadow:0 4px 16px rgba(0,0,0,.2);">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:28px; height:28px; border-radius:7px; background:var(--c-primary); display:flex; align-items:center; justify-content:center;">
            <svg width="13" height="13" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <span style="font-size:13px; font-weight:600; color:#fff;">
            <span id="selectedCount" style="color:#A5B4FC; font-size:15px; font-weight:700; margin-right:2px;">0</span> user dipilih
        </span>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="openBulkDeleteHybrid()"
                style="display:inline-flex; align-items:center; gap:5px; padding:6px 14px; background:#EF4444; border:none; border-radius:7px; font-size:11px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:0.05em; transition:background .15s;"
                onmouseover="this.style.background='#DC2626'" onmouseout="this.style.background='#EF4444'">
            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.44772 8.44772 3 9 3H15C15.5523 3 16 3.44772 16 4V6M19 6L18.1245 19.1338C18.0544 20.1818 17.1818 21 16.1315 21H7.86852C6.81818 21 5.94558 20.1818 5.87551 19.1338L5 6H19Z"/></svg>
            Bulk Delete
        </button>
        <div style="width:1px; height:18px; background:rgba(255,255,255,.12);"></div>
        <button onclick="deselectAll()"
                style="font-size:11px; font-weight:600; color:rgba(255,255,255,.5); background:none; border:none; cursor:pointer; font-family:inherit; text-transform:uppercase; letter-spacing:0.05em; transition:color .15s; padding:0 4px;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.5)'">
            Batal
        </button>
    </div>
</div>


<div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

    
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
        <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">User Table</h2>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">

            
            <form method="GET" action="<?php echo e(route('superadmin.users.index')); ?>" id="searchForm"
                  style="display:flex; align-items:center; gap:8px; margin:0;">

                
                <input type="hidden" name="sort_by"  value="<?php echo e(request('sort_by', 'created_at')); ?>">
                <input type="hidden" name="sort_dir" value="<?php echo e(request('sort_dir', 'desc')); ?>">
                <input type="hidden" name="per_page" value="<?php echo e(request('per_page', 10)); ?>">
                <input type="hidden" name="role"     value="<?php echo e(request('role', 'all')); ?>">

                
                <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
                    <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;"
                         width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           placeholder="Search..."
                           style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                           onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)'"
                           onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                </div>

                
                <div class="relative inline-block" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold text-[var(--c-fg-sec)] whitespace-nowrap cursor-pointer transition-all box-border"
                            style="border-color:var(--c-border); font-family:inherit;"
                            :style="open ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                        <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                            <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
                        </svg>
                        <span class="leading-none tracking-normal">Filter</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('role') && request('role') !== 'all'): ?>
                            <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>

                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 top-[calc(100%+6px)] bg-white border rounded-xl shadow-lg min-w-[160px] z-50 overflow-hidden"
                        style="border-color:var(--c-border); display:none;">
                        <div class="p-1.5">
                            <?php $selectedRole = request('role', 'all'); ?>
                            <a href="<?php echo e(route('superadmin.users.index', array_merge(request()->except(['role','page']), ['role' => 'all']))); ?>"
                            class="block px-2.5 py-1.5 rounded-lg text-[11px] transition-colors"
                            style="font-weight:<?php echo e($selectedRole === 'all' ? '700' : '500'); ?>; color:<?php echo e($selectedRole === 'all' ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; text-decoration:none; background:<?php echo e($selectedRole === 'all' ? 'rgba(94,83,244,0.06)' : 'transparent'); ?>;"
                            onmouseover="if('<?php echo e($selectedRole); ?>' !== 'all') this.style.background='var(--c-bg)'" onmouseout="if('<?php echo e($selectedRole); ?>' !== 'all') this.style.background='transparent'">
                                Semua Role
                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('superadmin.users.index', array_merge(request()->except(['role','page']), ['role' => $r->name]))); ?>"
                            class="block px-2.5 py-1.5 rounded-lg text-[11px] transition-colors"
                            style="font-weight:<?php echo e($selectedRole === $r->name ? '700' : '500'); ?>; color:<?php echo e($selectedRole === $r->name ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; text-decoration:none; background:<?php echo e($selectedRole === $r->name ? 'rgba(94,83,244,0.06)' : 'transparent'); ?>;"
                            onmouseover="if('<?php echo e($selectedRole); ?>' !== '<?php echo e($r->name); ?>') this.style.background='var(--c-bg)'" onmouseout="if('<?php echo e($selectedRole); ?>' !== '<?php echo e($r->name); ?>') this.style.background='transparent'">
                                <?php echo e(ucfirst(str_replace('_', ' ', $r->name))); ?>

                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <?php
                    $currentSortBy  = request('sort_by', 'created_at');
                    $currentSortDir = request('sort_dir', 'desc');
                    $sortLabels = [
                        'name'       => 'Nama',
                        'role'       => 'Role',
                        'created_at' => 'Terdaftar',
                    ];
                    $activeSortLabel = match($currentSortBy) {
                        'name'       => $currentSortDir === 'asc' ? 'Nama A–Z' : 'Nama Z–A',
                        'role'       => 'Role',
                        'created_at' => $currentSortDir === 'asc' ? 'Terlama' : 'Terbaru',
                        default      => 'Terbaru',
                    };
                ?>
                <div style="position:relative; display:inline-block;"
                     x-data="{ sortOpen: false }">

                    <button type="button"
                            @click="sortOpen = !sortOpen"
                            class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border"
                            style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:inherit;"
                            :style="sortOpen ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentSortDir === 'asc'): ?>
                        <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                            <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/>
                        </svg>
                        <?php else: ?>
                        <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                            <path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/>
                        </svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="leading-none">Sort: <?php echo e($activeSortLabel); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentSortBy !== 'created_at' || $currentSortDir !== 'desc'): ?>
                            <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </button>

                    
                    <div x-show="sortOpen" x-cloak @click="sortOpen = false"
                         style="position:fixed; inset:0; z-index:48; display:none; background:transparent;"></div>

                    
                    <div x-show="sortOpen" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="position:absolute; right:0; top:calc(100% + 6px); z-index:49; min-width:200px; background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.1); padding:8px; display:none;">

                        
                        <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Nama</p>
                        <?php $nameAZ = $currentSortBy === 'name' && $currentSortDir === 'asc'; ?>
                        <?php $nameZA = $currentSortBy === 'name' && $currentSortDir === 'desc'; ?>
                        <button type="button" onclick="setSortUser('name', 'asc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($nameAZ ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($nameAZ ? '700' : '500'); ?>; color:<?php echo e($nameAZ ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($nameAZ ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($nameAZ ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/></svg>
                                <span>Nama A–Z</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nameAZ): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                        <button type="button" onclick="setSortUser('name', 'desc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($nameZA ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($nameZA ? '700' : '500'); ?>; color:<?php echo e($nameZA ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($nameZA ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($nameZA ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/></svg>
                                <span>Nama Z–A</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nameZA): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>

                        <div style="height:1px; background:var(--c-border); margin:7px 0;"></div>

                        
                        <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Role</p>
                        <?php $roleAZ = $currentSortBy === 'role' && $currentSortDir === 'asc'; ?>
                        <?php $roleZA = $currentSortBy === 'role' && $currentSortDir === 'desc'; ?>
                        <button type="button" onclick="setSortUser('role', 'asc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($roleAZ ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($roleAZ ? '700' : '500'); ?>; color:<?php echo e($roleAZ ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($roleAZ ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($roleAZ ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/></svg>
                                <span>Role A–Z</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roleAZ): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                        <button type="button" onclick="setSortUser('role', 'desc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($roleZA ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($roleZA ? '700' : '500'); ?>; color:<?php echo e($roleZA ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($roleZA ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($roleZA ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/></svg>
                                <span>Role Z–A</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roleZA): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>

                        <div style="height:1px; background:var(--c-border); margin:7px 0;"></div>

                        
                        <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Terdaftar</p>
                        <?php $regNew = $currentSortBy === 'created_at' && $currentSortDir === 'desc'; ?>
                        <?php $regOld = $currentSortBy === 'created_at' && $currentSortDir === 'asc'; ?>
                        <button type="button" onclick="setSortUser('created_at', 'desc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($regNew ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($regNew ? '700' : '500'); ?>; color:<?php echo e($regNew ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($regNew ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($regNew ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/></svg>
                                <span>Terbaru</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($regNew): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                        <button type="button" onclick="setSortUser('created_at', 'asc')"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:<?php echo e($regOld ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; font-size:12px; font-weight:<?php echo e($regOld ? '700' : '500'); ?>; color:<?php echo e($regOld ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                onmouseover="if(!<?php echo e($regOld ? 'true' : 'false'); ?>) this.style.background='var(--c-bg)'"
                                onmouseout="if(!<?php echo e($regOld ? 'true' : 'false'); ?>) this.style.background='transparent'">
                            <div style="display:flex; align-items:center; gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/></svg>
                                <span>Terlama</span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($regOld): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:780px;">
            <thead>
                <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                    <th style="padding:11px 16px; width:44px; text-align:left;">
                        <input type="checkbox" id="selectAll" style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                    </th>
                    <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:200px;">User Name</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:140px;">Access Role</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Modul</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Permissions</th>
                    <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                    <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $isMe         = $user->id === auth()->id();
                    $userRoles    = $user->roles;
                    $isSuperadmin = $userRoles->pluck('name')->contains('superadmin');
                    $isSuspended  = $user->isSuspended();

                    $rowNo = ($users->currentPage() - 1) * $users->perPage() + $index + 1;

                    $perms     = $user->permissions;
                    $permCount = $perms->count();
                    $modCount  = $isSuperadmin ? 4 : $perms->pluck('name')->map(fn($p) => explode('.', $p)[0])->unique()->count();

                    $firstRole      = $userRoles->first();
                    $extraRoleCount = max(0, $userRoles->count() - 1);
                ?>

                <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s; <?php echo e($isSuspended ? 'background:#FFF9F9;' : ''); ?>"
                    onmouseover="this.style.background='<?php echo e($isSuspended ? '#FFF5F5' : '#FAFAFA'); ?>'"
                    onmouseout="this.style.background='<?php echo e($isSuspended ? '#FFF9F9' : 'transparent'); ?>'">

                    
                    <td style="padding:14px 16px; width:44px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMe): ?>
                        <input type="checkbox" name="selected_users[]" value="<?php echo e($user->id); ?>"
                               class="user-checkbox"
                               style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                        <?php else: ?>
                        <div style="width:15px; height:15px; display:flex; align-items:center; justify-content:center;" title="Akun Anda">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24" style="color:#9CA3AF;">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 8C9 6.34315 10.3431 5 12 5C13.6569 5 15 6.34315 15 8V11H9V8ZM17 8V11C18.6569 11 20 12.3431 20 14V18C20 19.6569 18.6569 21 17 21H7C5.34315 21 4 19.6569 4 18V14C4 12.3431 5.34315 11 7 11V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8Z"/>
                            </svg>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;">
                        <?php echo e($rowNo); ?>

                    </td>

                    
                    <td style="padding:14px 16px; min-width:200px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $user,'size' => 'md','onlineDot' => $user->is_online && !$isSuspended,'suspended' => $isSuspended]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'size' => 'md','online-dot' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->is_online && !$isSuspended),'suspended' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSuspended)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2)): ?>
<?php $attributes = $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2; ?>
<?php unset($__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2252ef3298868bc9de4c534a2a83a2a2)): ?>
<?php $component = $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2; ?>
<?php unset($__componentOriginal2252ef3298868bc9de4c534a2a83a2a2); ?>
<?php endif; ?>
                            <div style="min-width:0;">
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <a href="<?php echo e(route('superadmin.users.show', $user->id)); ?>" style="text-decoration:none; outline:none;">
                                        <p style="font-size:13px; font-weight:600; color:<?php echo e($isSuspended ? '#DC2626' : 'var(--c-fg)'); ?>; <?php echo e($isSuspended ? 'text-decoration:line-through; text-decoration-color:#FCA5A5;' : ''); ?> white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; transition:color 0.2s;"
                                        onmouseover="this.style.color='var(--c-primary)'"
                                        onmouseout="this.style.color='<?php echo e($isSuspended ? '#DC2626' : 'var(--c-fg)'); ?>'">
                                            <?php echo e($user->name); ?>

                                        </p>
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMe): ?>
                                    <span style="font-size:8px; font-weight:700; color:#0B266E; background:rgba(11,38,110,0.07); padding:1px 5px; border-radius:4px; text-transform:uppercase; flex-shrink:0;">YOU</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <p style="font-size:11px; color:var(--c-fg-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; margin-top:1px;">
                                    <?php echo e($user->email); ?>

                                </p>
                            </div>
                        </div>
                    </td>

                    
                    <td style="padding:14px 16px; min-width:140px;">
                        <div style="display:flex; align-items:center; gap:4px; flex-wrap:nowrap;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstRole): ?>
                                <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $firstRole->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firstRole->name)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa)): ?>
<?php $attributes = $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa; ?>
<?php unset($__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa)): ?>
<?php $component = $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa; ?>
<?php unset($__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa); ?>
<?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($extraRoleCount > 0): ?>
                                <span style="font-size:11px; font-weight:500; color:var(--c-fg-muted); border:1px solid var(--c-border); padding:3px 9px; border-radius:9999px; white-space:nowrap;">
                                    +<?php echo e($extraRoleCount); ?>

                                </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <span style="font-size:10px; color:var(--c-fg-muted); font-style:italic;">No Role</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </td>

                    
                    <td style="padding:14px 16px;">
                        <span style="font-size:13px; font-weight:400; color:var(--c-fg);">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperadmin): ?> All <?php else: ?> <?php echo e($modCount); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> Modul
                        </span>
                    </td>

                    
                    <td style="padding:14px 16px;">
                        <span style="font-size:13px; font-weight:400; color:var(--c-fg);">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperadmin): ?> All <?php else: ?> <?php echo e($permCount); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> Permissions
                        </span>
                    </td>

                    
                    <td style="padding:12px 16px;">
                        <?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['status' => $isSuspended ? 'suspended' : 'active']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSuspended ? 'suspended' : 'active')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $attributes = $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $component = $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px; text-align:center;">
                        <div style="position:relative; display:inline-block;" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    style="width:28px; height:28px; border-radius:6px; border:1px solid var(--c-border); background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg-muted); transition:all .15s; margin:0 auto;"
                                    onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
                                    onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
                                <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 style="position:absolute; right:0; top:calc(100% + 5px); background:#fff; border:1px solid var(--c-border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.1); min-width:160px; z-index:40; overflow:hidden; display:none;">
                                <div style="padding:5px;">

                                    <a href="<?php echo e(route('superadmin.users.show', $user->id)); ?>"
                                    style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border-radius:6px; font-size:11px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:background .12s;"
                                    onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='transparent'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail Info
                                    </a>

                                    <button type="button"
                                            onclick="openEditInfo(<?php echo e(json_encode(['id' => $user->id, 'name' => $user->name, 'email' => $user->email])); ?>); open = false"
                                            style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:var(--c-fg-sec); cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                            onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='none'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"/></svg>
                                        Edit Info
                                    </button>

                                    <a href="<?php echo e(route('superadmin.permissions')); ?>"
                                       style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border-radius:6px; font-size:11px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:background .12s;"
                                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='transparent'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linejoin="round"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                        Permission
                                    </a>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMe): ?>
                                    <div style="height:1px; background:var(--c-border); margin:4px 6px;"></div>

                                    <button type="button"
                                            onclick="openForceLogoutModal({ id: '<?php echo e($user->id); ?>', name: '<?php echo e(addslashes($user->name)); ?>' }); open = false"
                                            style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#D97706; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                            onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background='none'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.93L7.87 4.06C6.39 3.66 5 5.21 5 7.27V16.73C5 18.79 6.39 20.34 7.87 19.94L11.07 19.06C12.19 18.76 13 17.42 13 15.86V15.27M11 12H19M19 12L16.5 9.5M19 12L16.5 14.5"/></svg>
                                        Force Logout
                                    </button>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuspended): ?>
                                    <form method="POST" action="<?php echo e(route('superadmin.users.unsuspend', $user)); ?>" style="display:block;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#059669; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                                onmouseover="this.style.background='#ECFDF5'" onmouseout="this.style.background='none'">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Unsuspend
                                        </button>
                                    </form>
                                    <?php elseif(!$isSuperadmin): ?>
                                    <button type="button"
                                            onclick="openSuspendModal(<?php echo e(json_encode(['id' => $user->id, 'name' => $user->name])); ?>); open = false"
                                            style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                            onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15.5 15.5L12 12M8.5 8.5L12 12M8.5 15.5L12 12M15.5 8.5L12 12M12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12C22 17.52 17.52 22 12 22Z"/></svg>
                                        Suspend
                                    </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <button type="button"
                                            onclick="openDeleteHybrid(<?php echo e(json_encode(['id' => $user->id, 'name' => $user->name])); ?>); open = false"
                                            style="width:100%; display:flex; align-items:center; gap:8px; padding:7px 10px; border:none; border-radius:6px; background:none; font-size:11px; font-weight:500; color:#DC2626; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;"
                                            onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6H5H21M8 6V4C8 3.45 8.45 3 9 3H15C15.55 3 16 3.45 16 4V6M19 6L18.12 19.13C18.05 20.18 17.18 21 16.13 21H7.87C6.82 21 5.95 20.18 5.88 19.13L5 6H19Z"/></svg>
                                        Hapus User
                                    </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="8" style="padding:60px 24px; text-align:center;">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
                            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M14.86 10.83C15.58 9.73 16 8.42 16 7C16 5.58 15.58 4.27 14.86 3.17C15.22 3.06 15.6 3 16 3C18.21 3 20 4.79 20 7C20 9.21 18.21 11 16 11C15.6 11 15.22 10.94 14.86 10.83ZM17.87 21C17.96 20.68 18 20.35 18 20V19C18 17.11 17.34 15.37 16.25 14H17C19.76 14 22 16.24 22 19V20C22 20.55 21.55 21 21 21H17.87Z"
                                    fill="currentColor"/>
                                <path d="M10 14H8C5.24 14 3 16.24 3 19V20C3 20.55 3.45 21 4 21H14C14.55 21 15 20.55 15 20V19C15 16.24 12.76 14 10 14Z"
                                    stroke="currentColor" stroke-width="1.5"/>
                                <path d="M9 11C11.21 11 13 9.21 13 7C13 4.79 11.21 3 9 3C6.79 3 5 4.79 5 7C5 9.21 6.79 11 9 11Z"
                                    stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em;">No users found</p>
                            <p style="font-size:11px; color:var(--c-fg-placeholder);">Coba ubah filter pencarian</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php echo $__env->make('superadmin.users._pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<script>
    // Sort helpers — prefixed "User" agar tidak clash dengan halaman audit logs
    // jika keduanya dimuat bersamaan
    function setSortUser(col, dir) {
        const form = document.getElementById('searchForm');
        form.querySelector('input[name="sort_by"]').value  = col;
        form.querySelector('input[name="sort_dir"]').value = dir;
        form.querySelector('input[name="page"]')?.remove();
        form.submit();
    }
</script><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\users\_table.blade.php ENDPATH**/ ?>