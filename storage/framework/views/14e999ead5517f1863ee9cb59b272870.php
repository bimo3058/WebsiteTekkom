
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => ['user' => auth()->user()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<style>
    .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

    .rp-wrap {
        display: flex; flex-direction: column;
        height: calc(100vh - 60px);
        padding: 10px; box-sizing: border-box;
        font-family: 'Inter Tight', sans-serif;
    }

    .rp-box {
        display: flex; flex-direction: column; flex: 1; min-height: 0;
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .rp-box-header {
        background: #fff;
        border-bottom: 1px solid var(--c-border);
        flex-shrink: 0;
        padding: 16px 24px;
        box-sizing: border-box;
    }

    .rp-box-body {
        flex: 1; overflow-y: auto;
        padding: 20px 24px;
        display: flex; flex-direction: column; gap: 16px;
    }

    .rp-tr:hover td { background: #FAFBFC !important; }

    /* ── Mobile: scroll natively ── */
    @media (max-width: 767px) {
        .sitkom-content {
            padding: 8px 8px 80px !important;
            display: block !important;
            overflow: visible !important;
        }
        .rp-wrap {
            height: auto !important;
            min-height: 0 !important;
            padding: 0;
        }
        .rp-box {
            flex: none !important;
            min-height: 0 !important;
            overflow: visible !important;
            border-radius: 10px;
        }
        .rp-box-header {
            padding: 12px 14px;
            position: sticky;
            top: 52px;
            z-index: 10;
        }
        .rp-box-body {
            overflow-y: visible !important;
            flex: none !important;
            padding: 12px 14px;
        }
    }

</style>

<?php
    $sortBy  = request('sort_by', 'name');
    $sortDir = request('sort_dir', 'asc');
    $search  = request('search', '');
    $perPage = (int) request('per_page', 10);

    $rolesQuery = \App\Models\Role::withCount('users')->with('permissions');
    if ($search) $rolesQuery->where('name', 'like', "%{$search}%");

    $allowedSort = ['name', 'users_count'];
    $sortBy  = in_array($sortBy, $allowedSort) ? $sortBy : 'name';
    $sortDir = in_array($sortDir, ['asc','desc']) ? $sortDir : 'asc';

    $roles = $rolesQuery->orderBy($sortBy, $sortDir)->paginate($perPage)->withQueryString();

    $dbModules = \App\Models\SystemModule::all();
?>

<div class="rp-wrap">
<div class="rp-box">

    
    <div class="rp-box-header">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div>
                <h1 style="font-size:15px; font-weight:800; color:var(--c-fg); margin:0 0 2px 0; letter-spacing:-.02em;">Role &amp; Permission</h1>
                <p style="font-size:11px; color:var(--c-fg-muted); margin:0;">Kelola role dan izin akses modul untuk setiap pengguna</p>
            </div>
        </div>
    </div>

    
    <div class="rp-box-body">

        
        <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

            
            <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:12px; flex-wrap:wrap;">
                <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">Role Table</h2>

                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <form method="GET" action="<?php echo e(route('superadmin.permissions')); ?>" id="rpForm"
                          style="display:flex; align-items:center; gap:8px;">
                        <input type="hidden" name="sort_by"  value="<?php echo e($sortBy); ?>">
                        <input type="hidden" name="sort_dir" value="<?php echo e($sortDir); ?>">

                        
                        <div style="position:relative; width:200px;">
                            <svg style="position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none; color:var(--c-fg-placeholder);"
                                 width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" name="search" value="<?php echo e($search); ?>"
                                   placeholder="Search role..."
                                   style="width:100%; height:34px; padding:0 10px 0 30px; border:1px solid var(--c-border); border-radius:8px; font-size:12px; color:var(--c-fg); font-family:'Inter Tight',sans-serif; outline:none; box-sizing:border-box; background:#fff;"
                                   onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)'"
                                   onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                        </div>

                        
                        <div class="relative" x-data="{ open:false, cur:<?php echo e($perPage); ?>, opts:[10,25,50,100] }">
                            <button type="button" @click="open=!open" @click.outside="open=false"
                                    class="flex items-center gap-1.5 h-[34px] px-3 rounded-lg text-[12px] font-semibold border bg-white cursor-pointer"
                                    style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:'Inter Tight',sans-serif;"
                                    :style="open?'border-color:var(--c-primary);':''">
                                <span x-text="cur+' baris'"></span>
                                <svg :class="open?'rotate-180':''" class="transition-transform" width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <input type="hidden" name="per_page" x-ref="pp" value="<?php echo e($perPage); ?>">
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 top-[calc(100%+4px)] bg-white border rounded-xl py-1 shadow-lg z-50 min-w-[110px]"
                                 style="border-color:var(--c-border); display:none;">
                                <template x-for="o in opts" :key="o">
                                    <button type="button"
                                            @click="cur=o; $refs.pp.value=o; open=false; $el.closest('form').submit()"
                                            class="w-full text-left px-3 py-1.5 text-[11px] transition-colors"
                                            :class="cur==o?'font-bold':'font-normal'"
                                            :style="cur==o?'color:var(--c-primary);background:rgba(94,83,244,0.05)':'color:var(--c-fg-sec)'"
                                            style="border:none;cursor:pointer;background:none;font-family:'Inter Tight',sans-serif;">
                                        <span x-text="o+' baris'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        
                        <div class="relative" x-data="{ open:false }">
                            <button type="button" @click="open=!open" @click.outside="open=false"
                                    class="flex items-center gap-1.5 h-[34px] px-3 rounded-lg text-[12px] font-semibold border bg-white cursor-pointer"
                                    style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:'Inter Tight',sans-serif;"
                                    :style="open?'border-color:var(--c-primary);':''">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                                Sort by
                            </button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 top-[calc(100%+4px)] bg-white border rounded-xl py-1 shadow-lg z-50 min-w-[160px]"
                                 style="border-color:var(--c-border); display:none;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                                    ['col'=>'name','dir'=>'asc','label'=>'Nama A–Z'],
                                    ['col'=>'name','dir'=>'desc','label'=>'Nama Z–A'],
                                    ['col'=>'users_count','dir'=>'desc','label'=>'User Terbanyak'],
                                    ['col'=>'users_count','dir'=>'asc','label'=>'User Tersedikit'],
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $active = ($sortBy===$opt['col'] && $sortDir===$opt['dir']); ?>
                                <a href="<?php echo e(route('superadmin.permissions', array_merge(request()->except(['sort_by','sort_dir','page']),['sort_by'=>$opt['col'],'sort_dir'=>$opt['dir']]))); ?>"
                                   @click="open=false"
                                   style="display:flex;align-items:center;justify-content:space-between;padding:6px 12px;text-decoration:none;font-size:11px;font-family:'Inter Tight',sans-serif;
                                          font-weight:<?php echo e($active?'700':'500'); ?>;
                                          color:<?php echo e($active?'var(--c-primary)':'var(--c-fg-sec)'); ?>;
                                          background:<?php echo e($active?'rgba(94,83,244,0.05)':'transparent'); ?>;"
                                   onmouseover="this.style.background='<?php echo e($active?'rgba(94,83,244,0.05)':'var(--c-bg)'); ?>'"
                                   onmouseout="this.style.background='<?php echo e($active?'rgba(94,83,244,0.05)':'transparent'); ?>'">
                                    <?php echo e($opt['label']); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($active): ?><svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" style="color:var(--c-primary);"><path d="M20 6L9 17l-5-5"/></svg><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-family:'Inter Tight',sans-serif;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--c-border); background:#FAFBFC;">
                            <th style="width:40px; padding:10px 12px 10px 16px;">
                                <input type="checkbox" id="rpSelectAll" style="width:14px;height:14px;cursor:pointer;accent-color:var(--c-primary);">
                            </th>
                            <th style="padding:10px 12px;text-align:left;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;width:40px;">No</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;">Nama Role</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;">Modul</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;">Permissions</th>
                            <th style="padding:10px 12px;text-align:left;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;">Status</th>
                            <th style="padding:10px 12px;text-align:center;font-size:10.5px;font-weight:700;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $permCount = $role->permissions->count();
                            $modCount  = $dbModules->count();
                            $rowNum    = ($roles->currentPage()-1) * $roles->perPage() + $i + 1;
                            $isActive  = $role->users_count > 0;
                        ?>
                        <tr class="rp-tr" style="border-bottom:1px solid #F3F4F6;">
                            <td style="padding:10px 12px 10px 16px;">
                                <input type="checkbox" class="rp-cb" value="<?php echo e($role->id); ?>"
                                       style="width:14px;height:14px;cursor:pointer;accent-color:var(--c-primary);">
                            </td>
                            <td style="padding:10px 12px;font-size:12px;font-weight:600;color:var(--c-fg-muted);"><?php echo e($rowNum); ?></td>

                            
                            <td style="padding:10px 12px;">
                                <a href="<?php echo e(route('superadmin.permissions.show') . '?role=' . $role->name); ?>" style="text-decoration:none;">
                                    <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $role->name,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role->name),'size' => 'xs']); ?>
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
                                </a>
                            </td>

                            <td style="padding:10px 12px;font-size:12px;font-weight:600;color:var(--c-fg-sec);"><?php echo e($modCount); ?> Modul</td>
                            <td style="padding:10px 12px;font-size:12px;font-weight:600;color:var(--c-fg-sec);"><?php echo e($permCount); ?> Permissions</td>

                            <td style="padding:10px 12px;">
                                <?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['status' => $isActive ? 'active' : 'nonaktif']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isActive ? 'active' : 'nonaktif')]); ?>
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

                            
                            <td style="padding:10px 12px;text-align:center;">
                                <div class="relative" x-data="{ open:false }">
                                    <button type="button" @click="open=!open" @click.outside="open=false"
                                            style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--c-border);border-radius:7px;background:#fff;cursor:pointer;transition:background .15s;"
                                            onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:var(--c-fg-muted);">
                                            <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute right-0 top-[calc(100%+5px)] bg-white rounded-xl py-1 z-50 min-w-[160px]"
                                         style="border:1px solid var(--c-border);box-shadow:0 8px 24px rgba(0,0,0,.1);display:none;">
                                        <a href="<?php echo e(route('superadmin.permissions.show') . '?role=' . $role->name); ?>"
                                           @click="open=false"
                                           style="display:flex;align-items:center;gap:8px;padding:7px 12px;font-size:11px;font-weight:500;color:var(--c-fg-sec);text-decoration:none;transition:background .12s;"
                                           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='transparent'">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round">
                                                <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/>
                                            </svg>
                                            Edit Permissions
                                        </a>
                                        <a href="<?php echo e(route('superadmin.users.index', ['role' => $role->name])); ?>"
                                           @click="open=false"
                                           style="display:flex;align-items:center;gap:8px;padding:7px 12px;font-size:11px;font-weight:500;color:var(--c-fg-sec);text-decoration:none;transition:background .12s;"
                                           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='transparent'">
                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                            </svg>
                                            Lihat User
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" style="padding:60px 24px;text-align:center;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;">
                                        <path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"
                                              stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                    <p style="font-size:12px;font-weight:600;color:var(--c-fg-muted);text-transform:uppercase;letter-spacing:.06em;margin:0;">Tidak ada role ditemukan</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->total() > 0): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid #F3F4F6;flex-wrap:wrap;gap:8px;">
                <span style="font-size:11px;color:#64748B;font-family:'Inter Tight',sans-serif;">
                    Showing <strong style="color:#1E293B;"><?php echo e($roles->firstItem()); ?></strong>
                    to <strong style="color:#1E293B;"><?php echo e($roles->lastItem()); ?></strong>
                    of <strong style="color:#1E293B;"><?php echo e($roles->total()); ?></strong> results
                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->lastPage() > 1): ?>
                <div style="display:flex;align-items:center;gap:3px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->currentPage() > 1): ?>
                        <a href="<?php echo e($roles->previousPageUrl()); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                        </a>
                    <?php else: ?>
                        <span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #F3F4F6;border-radius:6px;background:#FAFAFA;color:#D1D5DB;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php $s=max(1,$roles->currentPage()-2); $e=min($roles->lastPage(),$roles->currentPage()+2); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s>1): ?>
                        <a href="<?php echo e($roles->url(1)); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">1</a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s>2): ?><span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p=$s;$p<=$e;$p++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e($roles->url($p)); ?>"
                           style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:11px;font-weight:<?php echo e($p===$roles->currentPage()?'700':'500'); ?>;text-decoration:none;<?php echo e($p===$roles->currentPage()?'background:#1E293B;color:#fff;border:1px solid #1E293B;':'border:1px solid #E2E8F0;background:#fff;color:#64748B;'); ?>"
                           <?php if($p!==$roles->currentPage()): ?> onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'" <?php endif; ?>><?php echo e($p); ?></a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e<$roles->lastPage()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($e<$roles->lastPage()-1): ?><span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <a href="<?php echo e($roles->url($roles->lastPage())); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'"><?php echo e($roles->lastPage()); ?></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roles->currentPage() < $roles->lastPage()): ?>
                        <a href="<?php echo e($roles->nextPageUrl()); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    <?php else: ?>
                        <span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #F3F4F6;border-radius:6px;background:#FAFAFA;color:#D1D5DB;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>
</div>
</div>

<script>
    document.getElementById('rpSelectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.rp-cb').forEach(cb => cb.checked = this.checked);
    });
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\permission\index.blade.php ENDPATH**/ ?>