
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
    .perm-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
    .perm-box  { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #F2F3F5; border: 1px solid #D4D5D8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
</style>

<?php
    // 1. Mapping manual: Slug Database => Prefix Permission Database
    $slugToPerm = [
        'bank_soal'           => 'banksoal',
        'manajemen_mahasiswa' => 'kemahasiswaan',
        'capstone'            => 'capstone',
        'eoffice'             => 'eoffice'
    ];

    // 2. Mapping Style Modul
    $moduleStyles = [
        'bank_soal'           => ['bg'=>'#FDF6E9','border'=>'#EDD9A3','color'=>'#D97706','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'capstone'            => ['bg'=>'#EFF6FF','border'=>'#BFDBFE','color'=>'#3B82F6','icon'=>'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7'],
        'manajemen_mahasiswa' => ['bg'=>'#ECFDF5','border'=>'#A7F3D0','color'=>'#10B981','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        'eoffice'             => ['bg'=>'#FFF1F2','border'=>'#FECDD3','color'=>'#F43F5E','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
    $defaultStyle = ['bg'=>'#EDE9FE','border'=>'#C4B5FD','color'=>'#8B5CF6','icon'=>'M4 6h16M4 10h16M4 14h16M4 18h16'];

    // Ambil dari DB, generate otomatis
    $roleList = \App\Models\Role::orderBy('name')->get()->mapWithKeys(function ($role) {
        $colors = [
            'superadmin'          => '#0B266E',
            'admin'               => '#1E293B',
            'dosen'               => '#10B981',
            'mahasiswa'           => '#D97706',
            'gpm'                 => '#0EA5E9',
            'pengurus_himpunan'   => '#8B5CF6',
            'alumni'              => '#64748B',
            'admin_banksoal'      => '#3B82F6',
            'admin_capstone'      => '#6366F1',
            'admin_eoffice'       => '#F43F5E',
            'admin_kemahasiswaan' => '#10B981',
            'dosen_koor'          => '#F59E0B', // warna default untuk role baru
        ];

        return [$role->name => [
            'label' => \Illuminate\Support\Str::title(str_replace('_', ' ', $role->name)),
            'color' => $colors[$role->name] ?? '#94A3B8',
        ]];
    })->toArray();

    // 4. Inisialisasi Role Aktif (PENTING: Agar tidak error Undefined Variable)
    $activeRole  = request('role', 'mahasiswa');
    $activeColor = $roleList[$activeRole]['color'] ?? '#64748B';
    $activeLabel = $roleList[$activeRole]['label'] ?? Str::title(str_replace('_',' ',$activeRole));

    $dbModules = \App\Models\SystemModule::orderBy('id')->get();

    // 5. Filter & Paginate Users Berdasarkan Role & Search
    $search        = request('search','');
    $perPagePerm   = (int) request('per_page', 10);
    
    // Gunakan $activeRole yang sudah didefinisikan di atas
    $filteredUsers = $users->filter(fn($u) => $u->roles->pluck('name')->contains($activeRole))
        ->when($search, fn($c) => $c->filter(fn($u) => str_contains(strtolower($u->name), strtolower($search)) || str_contains(strtolower($u->email), strtolower($search))))
        ->values();

    $page    = \Illuminate\Pagination\Paginator::resolveCurrentPage();
    $paged   = new \Illuminate\Pagination\LengthAwarePaginator(
        $filteredUsers->forPage($page, $perPagePerm),
        $filteredUsers->count(),
        $perPagePerm,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

?>

<div class="perm-wrap">
<div class="perm-box">

    
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 24px;background:#fff;border-bottom:1px solid #D4D5D8;flex-shrink:0;">
        <a href="<?php echo e(route('superadmin.permissions')); ?>"
           style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:12px;font-weight:600;color:#475569;background:#fff;border:1px solid #D0D1D5;border-radius:6px;box-shadow:0 1px 2px rgba(0,0,0,.04);text-decoration:none;font-family:'Inter Tight',sans-serif;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
            Kembali
        </a>
        <button onclick="saveBulkPermissions()"
                style="padding:6px 18px;font-size:12px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:6px;cursor:pointer;font-family:'Inter Tight',sans-serif;box-shadow:0 1px 2px rgba(0,0,0,.1);">
            Simpan
        </button>
    </div>

    
    <div style="flex:1;overflow-y:auto;">

        
        <div style="display:flex;border-bottom:1px solid #D4D5D8;background:#fff;">

            
            <div style="width:220px;flex-shrink:0;padding:20px;border-right:1px solid #D4D5D8;background:#fff;">
                <p style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 5px 0;font-family:'Inter Tight',sans-serif;">Role &amp; Permissions</p>
                <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;font-family:'Inter Tight',sans-serif;">Manage roles and module permissions for each user.</p>
            </div>

            
            <div style="flex:1;padding:20px 24px;">

                
                <p style="font-size:9px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px 0;font-family:'Inter Tight',sans-serif;">
                    Nama Role <span style="color:#EF4444">*</span>
                </p>
                <div style="max-width:240px;margin-bottom:16px;position:relative;" x-data="{ open: false }">
                    <button type="button"
                            @click="open = !open"
                            @click.outside="open = false"
                            style="width:100%; height:38px; display:flex; align-items:center; justify-content:space-between; padding:0 14px; background:#fff; border:1px solid #D0D1D5; border-radius:8px; font-size:13px; font-weight:600; color:#334155; cursor:pointer; font-family:'Inter Tight',sans-serif; box-shadow:0 1px 2px rgba(0,0,0,.04); outline:none; box-sizing:border-box;">
                        
                        <span><?php echo e($activeLabel); ?></span>
                        
                        
                        <svg width="14" height="14" 
                            style="min-width:14px; max-width:14px; min-height:14px; max-height:14px; color:#94A3B8; flex-shrink:0; flex-grow:0; transition:transform .15s;" 
                            :style="open ? 'transform:rotate(180deg)' : ''" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M6 9l6 6 6-6"/>
                        </svg>
                    </button>

                    
                    <div x-show="open" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D0D1D5;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:999;padding:4px 0;display:none;max-height:240px;overflow-y:auto;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roleList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(url()->current()); ?>?role=<?php echo e($slug); ?>&per_page=<?php echo e($perPagePerm); ?>"
                            style="display:flex;align-items:center;gap:8px;padding:8px 14px;font-size:12px;font-weight:<?php echo e($activeRole===$slug ? '700' : '600'); ?>;color:<?php echo e($activeRole===$slug ? '#0F172A' : '#475569'); ?>;background:<?php echo e($activeRole===$slug ? '#F8FAFC' : 'transparent'); ?>;text-decoration:none;font-family:'Inter Tight',sans-serif;transition:background .1s;"
                            onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='<?php echo e($activeRole===$slug ? '#F8FAFC' : 'transparent'); ?>'">
                                <span style="width:7px;height:7px;border-radius:50%;background:<?php echo e($data['color']); ?>;flex-shrink:0;"></span>
                                <?php echo e($data['label']); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeRole===$slug): ?>
                                    <svg style="width:11px;height:11px;margin-left:auto;flex-shrink:0;" fill="none" stroke="<?php echo e($data['color']); ?>" viewBox="0 0 24 24" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dbModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $mkey    = strtolower($mod->slug);
                            $pPrefix = $slugToPerm[$mkey] ?? $mkey; // Mapping prefix permission
                            $style   = $moduleStyles[$mkey] ?? $defaultStyle;
                            $perms   = \App\Models\Permission::where('name', 'like', $pPrefix.'.%')->get();
                        ?>
                        <div class="module-box" style="border:1px solid #D4D5D8; border-radius:8px; overflow:hidden; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,.04); opacity: <?php echo e($mod->is_active ? '1' : '0.6'); ?>;">
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:9px 13px; border-bottom:1px solid #E8E9EB; background:#FAFAFA;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; background:<?php echo e($style['bg']); ?>; border:1px solid <?php echo e($style['border']); ?>;">
                                        <svg width="13" height="13" fill="none" stroke="<?php echo e($style['color']); ?>" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="<?php echo e($style['icon']); ?>"/>
                                        </svg>
                                    </div>
                                    <span style="font-size:12px; font-weight:900; color:#1E293B;"><?php echo e(strtoupper($mod->name)); ?></span>
                                </div>
                                <label style="display:flex; align-items:center; gap:5px; cursor:<?php echo e($mod->is_active ? 'pointer' : 'not-allowed'); ?>;">
                                    <input type="checkbox" class="module-select-all" data-module-target="bulk_<?php echo e($pPrefix); ?>" <?php echo e(!$mod->is_active ? 'disabled' : ''); ?>

                                        style="width:14px; height:14px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:inherit; accent-color:var(--c-primary);">
                                    <span style="font-size:9px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em;">Pilih Semua</span>
                                </label>
                            </div>
                            <div style="padding:10px 13px; display:grid; grid-template-columns:1fr 1fr; gap:6px 8px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <label style="display:flex; align-items:center; gap:7px; cursor:<?php echo e($mod->is_active ? 'pointer' : 'not-allowed'); ?>;">
                                        <input type="checkbox" class="perm-checkbox" data-module-key="bulk_<?php echo e($pPrefix); ?>" value="<?php echo e($perm->name); ?>" <?php echo e(!$mod->is_active ? 'disabled' : ''); ?>

                                            style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:inherit; accent-color:var(--c-primary);">
                                        <span style="font-size:12px; font-weight:600; color:#64748B;"><?php echo e($perm->display_name ?? Str::title(Str::after($perm->name,'.'))); ?></span>
                                    </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div style="display:flex;">

            
            <div style="width:220px;flex-shrink:0;padding:20px;border-right:1px solid #D4D5D8;background:#fff;">
                <p style="font-size:13px;font-weight:900;color:#1E293B;margin:0 0 5px 0;font-family:'Inter Tight',sans-serif;">List User</p>
                <p style="font-size:11px;font-weight:500;color:#64748B;line-height:1.6;margin:0;font-family:'Inter Tight',sans-serif;">
                    Daftar tabel user dengan role<br>
                    "<span style="font-weight:700;color:<?php echo e($activeColor); ?>"><?php echo e($activeLabel); ?></span>".
                </p>
            </div>

            
            <div style="flex:1;padding:16px 24px;background:#fff;display:flex;flex-direction:column;gap:12px;">

                
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">

                    
                    <div id="permBulkBar" style="display:none;align-items:center;gap:8px;">
                        <span style="font-size:11px;font-weight:600;color:#334155;font-family:'Inter Tight',sans-serif;">
                            <span id="permSelectedCount" style="font-weight:700;color:#1E293B;">0</span> user dipilih
                        </span>
                        <button onclick="deselectAllPerm()" style="font-size:11px;font-weight:600;color:#94A3B8;background:none;border:none;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                            Batal
                        </button>
                    </div>
                    <div></div>

                    
                    <form action="<?php echo e(url()->current()); ?>" method="GET" style="display:flex;align-items:center;gap:6px;">
                        <input type="hidden" name="role" value="<?php echo e($activeRole); ?>">

                        
                        <div style="position:relative;" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.outside="open = false"
                                    style="display:flex;align-items:center;gap:5px;height:30px;padding:0 10px;background:#fff;border:1px solid #D0D1D5;border-radius:6px;font-size:11px;font-weight:600;color:#334155;cursor:pointer;font-family:'Inter Tight',sans-serif;white-space:nowrap;">
                                <?php echo e($perPagePerm); ?> baris
                                <svg style="width:10px;height:10px;color:#94A3B8;" :style="open?'transform:rotate(180deg)':''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <input type="hidden" name="per_page" id="perPageInput" value="<?php echo e($perPagePerm); ?>">
                            <div x-show="open" x-cloak
                                 style="position:absolute;bottom:calc(100% + 4px);left:0;background:#fff;border:1px solid #D0D1D5;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.1);z-index:50;padding:3px 0;min-width:80px;display:none;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [10,25,50,100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button type="button"
                                            @click="document.getElementById('perPageInput').value = <?php echo e($opt); ?>; open = false; $el.closest('form').submit()"
                                            style="display:block;width:100%;text-align:left;padding:6px 12px;font-size:11px;font-weight:<?php echo e($perPagePerm==$opt?'700':'500'); ?>;color:<?php echo e($perPagePerm==$opt?'#1E293B':'#475569'); ?>;background:none;border:none;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                                        <?php echo e($opt); ?> baris
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>

                        
                        <div style="position:relative;">
                            <svg style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:12px;height:12px;color:#94A3B8;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Cari nama atau email..."
                                   style="padding:5px 10px 5px 28px;font-size:11px;font-weight:500;border:1px solid #D0D1D5;border-radius:6px;background:#fff;color:#334155;outline:none;width:200px;font-family:'Inter Tight',sans-serif;height:30px;"
                                   onfocus="this.style.borderColor='#3B82F6'" onblur="this.style.borderColor='#D0D1D5'">
                        </div>
                        <button type="submit"
                                style="height:30px;padding:0 14px;font-size:11px;font-weight:700;color:#fff;background:#1E293B;border:none;border-radius:6px;cursor:pointer;font-family:'Inter Tight',sans-serif;">
                            Cari
                        </button>
                    </form>
                </div>

                
                <div style="border:1px solid #E5E7EB;border-radius:10px;overflow:hidden;">
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;min-width:680px;">
                            <thead>
                                <tr style="border-bottom:1px solid #E5E7EB;background:#FAFAFA;">
                                    <th style="padding:10px 14px;width:40px;text-align:left;">
                                        <input type="checkbox" id="permSelectAll" 
                                            style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                                    </th>
                                    <th style="padding:10px 10px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;width:40px;">No</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;min-width:180px;">User Name</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;min-width:120px;">Access Role</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Modul</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Permissions</th>
                                    <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Status</th>
                                    <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;white-space:nowrap;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $paged; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $no           = ($paged->currentPage()-1)*$paged->perPage()+$i+1;
                                        $isSuspended  = $user->isSuspended();
                                        $isMe         = $user->id === auth()->id();
                                        $isSuperadmin = $user->roles->pluck('name')->contains('superadmin');
                                        $permc        = $user->permissions->count();
                                        $modc         = $isSuperadmin ? 'All' : $user->permissions->pluck('name')->map(fn($p)=>explode('.',$p)[0])->unique()->count();
                                        $firstRole    = $user->roles->first();
                                    ?>
                                    <tr style="border-bottom:1px solid #F3F4F6;transition:background .1s;<?php echo e($isSuspended ? 'background:#FFF9F9;' : ''); ?>"
                                        onmouseover="this.style.background='<?php echo e($isSuspended ? '#FFF5F5' : '#FAFAFA'); ?>'"
                                        onmouseout="this.style.background='<?php echo e($isSuspended ? '#FFF9F9' : 'transparent'); ?>'">

                                        
                                        <td style="padding:12px 14px;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMe): ?>
                                                <input type="checkbox" name="user_ids[]" value="<?php echo e($user->id); ?>" class="perm-user-checkbox"
                                                    style="width:15px; height:15px; border-radius:4px; border:1.5px solid #CBD5E1; cursor:pointer; accent-color:var(--c-primary);">
                                            <?php else: ?>
                                                
                                                <div style="width:15px;height:15px;display:flex;align-items:center;justify-content:center;" title="Akun Anda">
                                                    <svg width="12" height="12" fill="#9CA3AF" viewBox="0 0 24 24"><path d="M12 3c-2.76 0-5 2.24-5 5v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-7c0-1.1-.9-2-2-2h-1V8c0-2.76-2.24-5-5-5zm3 8H9V8c0-1.66 1.34-3 3-3s3 1.34 3 3v3z"/></svg>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>

                                        
                                        <td style="padding:12px 10px;font-size:12px;color:#94A3B8;"><?php echo e($no); ?></td>

                                        
                                        <td style="padding:12px 14px;">
                                            <div style="display:flex;align-items:center;gap:10px;">
                                                <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $user,'size' => 'sm','onlineDot' => $user->is_online && !$isSuspended,'suspended' => $isSuspended]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'size' => 'sm','online-dot' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->is_online && !$isSuspended),'suspended' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isSuspended)]); ?>
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
                                                <div>
                                                    <div style="display:flex;align-items:center;gap:6px;">
                                                        <a href="<?php echo e(route('superadmin.users.show', $user->id)); ?>"
                                                           style="font-size:13px;font-weight:700;color:#1E293B;text-decoration:none;font-family:'Inter Tight',sans-serif;"
                                                           onmouseover="this.style.color='#3B82F6'" onmouseout="this.style.color='#1E293B'">
                                                            <?php echo e($user->name); ?>

                                                        </a>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMe): ?><span style="font-size:8px;font-weight:700;color:#0B266E;background:rgba(11,38,110,0.07);padding:1px 5px;border-radius:4px;text-transform:uppercase;">YOU</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <p style="font-size:11px;color:#94A3B8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"><?php echo e($user->email); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        
                                        <td style="padding:12px 14px;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstRole): ?>
                                                <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $firstRole->name,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firstRole->name),'size' => 'xs']); ?>
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
                                            <?php else: ?>
                                                <span style="font-size:10px;color:#94A3B8;font-style:italic;">No Role</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>

                                        
                                        <td style="padding:12px 14px;font-size:13px;color:#334155;"><?php echo e($modc); ?> Modul</td>

                                        
                                        <td style="padding:12px 14px;font-size:13px;color:#334155;"><?php echo e($isSuperadmin ? 'All' : $permc); ?> Permissions</td>

                                        
                                        <td style="padding:12px 14px;">
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

                                        
                                        <td style="padding:12px 14px;text-align:center;">
                                            <div style="position:relative;display:inline-block;" x-data="{ open: false }">
                                                <button type="button" @click="open = !open" @click.outside="open = false"
                                                        style="width:28px;height:28px;border-radius:6px;border:1px solid #E2E8F0;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#94A3B8;transition:all .15s;margin:0 auto;"
                                                        onmouseover="this.style.background='#F8FAFC';this.style.borderColor='#CBD5E1'"
                                                        onmouseout="this.style.background='#fff';this.style.borderColor='#E2E8F0'">
                                                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
                                                </button>

                                                <div x-show="open"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     style="position:absolute;right:0;top:calc(100% + 5px);background:#fff;border:1px solid #E2E8F0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:160px;z-index:40;overflow:hidden;display:none;">
                                                    <div style="padding:5px;">

                                                        
                                                        <a href="<?php echo e(route('superadmin.users.show', $user->id)); ?>"
                                                           style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;font-size:11px;font-weight:500;color:#475569;text-decoration:none;transition:background .12s;"
                                                           onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            Detail Info
                                                        </a>

                                                        
                                                        <button type="button"
                                                                onclick="openEditInfo(<?php echo e(json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email])); ?>); open = false"
                                                                style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#475569;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='none'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M11 4H4C2.89 4 2 4.9 2 6V20C2 21.1 2.9 22 4 22H18C19.1 22 20 21.1 20 20V13M18.5 2.5C19.33 2.5 20 3.17 20 4V4C20.83 4 21.5 4.67 21.5 5.5C21.5 6.33 20.83 7 20 7L11 16L7 17L8 13L17 4C17 3.17 17.67 2.5 18.5 2.5Z"/></svg>
                                                            Edit Info
                                                        </button>

                                                        
                                                        <a href="<?php echo e(route('superadmin.permissions')); ?>?role=<?php echo e($activeRole); ?>"
                                                           style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;font-size:11px;font-weight:500;color:#475569;text-decoration:none;transition:background .12s;"
                                                           onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linejoin="round"><path d="M3.00059 7.59352C3.20646 13.6197 5.53308 19.0699 11.1059 20.8601C11.6866 21.0466 12.3134 21.0466 12.8941 20.8601C18.4669 19.0699 20.7935 13.6197 20.9994 7.59352C21.0169 7.08167 20.6467 6.65046 20.1578 6.55081C17.5104 6.01123 15.4106 4.85537 13.1163 3.3374C12.4363 2.88753 11.5637 2.88753 10.8837 3.3374C8.58942 4.85537 6.48962 6.01123 3.8422 6.55081C3.35327 6.65046 2.98311 7.08167 3.00059 7.59352Z"/></svg>
                                                            Permission
                                                        </a>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMe): ?>
                                                            <div style="height:1px;background:#F3F4F6;margin:4px 6px;"></div>

                                                            
                                                            <button type="button"
                                                                    onclick="openForceLogoutModal({ id: '<?php echo e($user->id); ?>', name: '<?php echo e(addslashes($user->name)); ?>' }); open = false"
                                                                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#D97706;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                    onmouseover="this.style.background='#FFFBEB'" onmouseout="this.style.background='none'">
                                                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M13 8.73V8.14C13 6.58 12.19 5.24 11.07 4.93L7.87 4.06C6.39 3.66 5 5.21 5 7.27V16.73C5 18.79 6.39 20.34 7.87 19.94L11.07 19.06C12.19 18.76 13 17.42 13 15.86V15.27M11 12H19M19 12L16.5 9.5M19 12L16.5 14.5"/></svg>
                                                                Force Logout
                                                            </button>

                                                            
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuspended): ?>
                                                                <form method="POST" action="<?php echo e(route('superadmin.users.unsuspend', $user)); ?>" style="display:block;">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit"
                                                                            style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#059669;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                            onmouseover="this.style.background='#ECFDF5'" onmouseout="this.style.background='none'">
                                                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                        Unsuspend
                                                                    </button>
                                                                </form>
                                                            <?php elseif(!$isSuperadmin): ?>
                                                                <button type="button"
                                                                        onclick="openSuspendModal(<?php echo e(json_encode(['id'=>$user->id,'name'=>$user->name])); ?>); open = false"
                                                                        style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#DC2626;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
                                                                        onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                                                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15.5 15.5L12 12M8.5 8.5L12 12M8.5 15.5L12 12M15.5 8.5L12 12M12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12C22 17.52 17.52 22 12 22Z"/></svg>
                                                                    Suspend
                                                                </button>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                            
                                                            <button type="button"
                                                                    onclick="openDeleteHybrid(<?php echo e(json_encode(['id'=>$user->id,'name'=>$user->name])); ?>); open = false"
                                                                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;border:none;border-radius:6px;background:none;font-size:11px;font-weight:500;color:#DC2626;cursor:pointer;font-family:inherit;text-align:left;transition:background .12s;"
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
                                        <td colspan="8" style="padding:40px;text-align:center;">
                                            <p style="font-size:11px;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:.06em;font-family:'Inter Tight',sans-serif;">Tidak ada user ditemukan.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paged->total() > 0): ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-top:1px solid #F3F4F6;flex-wrap:wrap;gap:8px;">
                        <span style="font-size:11px;color:#64748B;font-family:'Inter Tight',sans-serif;">
                            Showing <strong style="color:#1E293B;"><?php echo e($paged->firstItem()); ?></strong>
                            to <strong style="color:#1E293B;"><?php echo e($paged->lastItem()); ?></strong>
                            of <strong style="color:#1E293B;"><?php echo e($paged->total()); ?></strong> results
                        </span>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paged->lastPage() > 1): ?>
                        <div style="display:flex;align-items:center;gap:3px;">
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paged->currentPage() > 1): ?>
                                <a href="<?php echo e($paged->appends(request()->query())->previousPageUrl()); ?>"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </a>
                            <?php else: ?>
                                <span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #F3F4F6;border-radius:6px;background:#FAFAFA;color:#D1D5DB;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php
                                $start = max(1, $paged->currentPage()-2);
                                $end   = min($paged->lastPage(), $paged->currentPage()+2);
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 1): ?>
                                <a href="<?php echo e($paged->appends(request()->query())->url(1)); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">1</a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 2): ?><span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p=$start;$p<=$end;$p++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <a href="<?php echo e($paged->appends(request()->query())->url($p)); ?>"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:11px;font-weight:<?php echo e($p===$paged->currentPage()?'700':'500'); ?>;text-decoration:none;transition:all .15s;<?php echo e($p===$paged->currentPage() ? 'background:#1E293B;color:#fff;border:1px solid #1E293B;' : 'border:1px solid #E2E8F0;background:#fff;color:#64748B;'); ?>"
                                   <?php if($p!==$paged->currentPage()): ?> onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'" <?php endif; ?>>
                                    <?php echo e($p); ?>

                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $paged->lastPage()): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $paged->lastPage()-1): ?><span style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94A3B8;">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <a href="<?php echo e($paged->appends(request()->query())->url($paged->lastPage())); ?>" style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;font-size:11px;font-weight:500;color:#64748B;text-decoration:none;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'"><?php echo e($paged->lastPage()); ?></a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paged->currentPage() < $paged->lastPage()): ?>
                                <a href="<?php echo e($paged->appends(request()->query())->nextPageUrl()); ?>"
                                   style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid #E2E8F0;border-radius:6px;background:#fff;color:#64748B;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
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
</div>
</div>


<form id="bulk-save-form" method="POST" action="<?php echo e(route('superadmin.permissions.bulk-update')); ?>" style="display:none">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="role" value="<?php echo e($activeRole); ?>">
    <div id="bulk-perm-inputs"></div>
    <div id="bulk-user-inputs"></div>
</form>


<?php echo $__env->make('superadmin.permission._modal_confirm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('superadmin.users._modal_suspend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('superadmin.users._modal_delete_hybrid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('superadmin.users._modal_force_logout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('superadmin.permission._scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\permission\permissions.blade.php ENDPATH**/ ?>