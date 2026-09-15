<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Kelola Role — Manajemen Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Kelola Role — Manajemen Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header" style="flex-shrink:0;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Role Praktikum</h1>
            <span class="mp-badge error sm"><span class="dot"></span>Admin</span>
        </div>
        <p class="mp-page-sub">Assign atau cabut role asprak & koordinator per praktikum · <?php echo e(now()->locale('id')->isoFormat('D MMMM YYYY')); ?></p>
    </div>
    <div class="mp-page-actions">
        <form method="GET" class="flex gap-2 items-center">
            <select name="praktikum_id" onchange="this.form.submit()" class="mp-input mp-select" style="min-width:240px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($prak->id); ?>" <?php echo e($prak->id == $praktikumId ? 'selected' : ''); ?>>
                    <?php echo e($prak->nama); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prak->kode): ?> [<?php echo e($prak->kode); ?>] <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    · <?php echo e($prak->semester); ?> <?php echo e($prak->tahun_ajaran); ?>

                </option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </form>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="mp-alert success flex-shrink-0">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
<div class="mp-alert warning flex-shrink-0"><?php echo e(session('error')); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>

<div class="mp-card flex-shrink-0">
    <div style="padding:56px;text-align:center;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 21V13H5C3.895 13 3 13.895 3 15v4C3 20.105 3.895 21 5 21H9ZM9 21H15M9 21V10C9 8.895 9.895 8 11 8H15V21M15 21H19C20.105 21 21 20.105 21 19V5C21 3.895 20.105 3 19 3h-2C15.895 3 15 3.895 15 5V21Z"/></svg>
        <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada praktikum yang tersedia.</div>
    </div>
</div>

<?php else: ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum): ?>
<div style="background:#fff;border:1px solid #DFE1E7;border-radius:11px;padding:12px 16px;display:flex;align-items:center;gap:16px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);">
    <div style="flex:1;min-width:0;">
        <div style="font-size:13px;font-weight:700;color:#0D0D12;"><?php echo e($praktikum->nama); ?></div>
        <div style="font-size:11px;color:#A4ABB8;margin-top:1px;"><?php echo e($praktikum->semester); ?> <?php echo e($praktikum->tahun_ajaran); ?></div>
    </div>
    <div style="display:flex;gap:16px;flex-shrink:0;">
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Dosen</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;"><?php echo e($praktikum->dosen?->name ?? '—'); ?></div>
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Koordinator</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->koordinator): ?>
            <div style="font-size:12px;font-weight:600;color:#10B981;"><?php echo e($praktikum->koordinator->name); ?></div>
            <?php else: ?>
            <div style="font-size:12px;font-weight:600;color:#EF4444;">Belum ditunjuk</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div style="width:1px;background:#F0F1F4;"></div>
        <div style="text-align:center;">
            <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Anggota</div>
            <div style="font-size:12px;font-weight:600;color:#0D0D12;"><?php echo e($anggota->count()); ?> orang</div>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div style="display:flex;gap:14px;flex:1;min-height:0;">

    
    <div style="flex:1;min-width:0;display:flex;flex-direction:column;overflow:hidden;">

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:12px 16px;margin-bottom:10px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:13px;font-weight:700;color:#0D0D12;">Anggota Terdaftar</div>
                <div style="font-size:11px;color:#A4ABB8;margin-top:1px;">
                    <?php
                        $asprakCount = $anggota->where('role','asprak')->count();
                        $koorCount   = $anggota->where('role','koor')->count();
                    ?>
                    <?php echo e($asprakCount); ?> asprak · <?php echo e($koorCount); ?> koordinator
                </div>
            </div>
            <div style="display:flex;gap:6px;">
                <span class="mp-badge success sm"><span class="dot"></span><?php echo e($asprakCount); ?> Asprak</span>
                <span class="mp-badge navy sm"><span class="dot"></span><?php echo e($koorCount); ?> Koor</span>
            </div>
        </div>

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;flex-direction:column;flex:1;min-height:0;">
            <div style="overflow-y:auto;flex:1;">
                <table style="width:100%;border-collapse:collapse;min-width:420px;">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr style="border-bottom:1px solid #DFE1E7;background:#FAFAFA;">
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">#</th>
                            <th style="padding:10px 16px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">Pengguna</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:110px;">Role</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Bergabung</th>
                            <th style="padding:10px 16px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $np  = explode(' ', $a->user?->name ?? 'U');
                            $ini = strtoupper(substr($np[0]??'U',0,1).substr($np[1]??$np[0]??'U',0,1));
                            $avc = $a->role === 'koor' ? 'violet' : 'green';
                        ?>
                        <tr style="border-bottom:1px solid #F8F9FB;transition:background .1s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:13px 16px;font-size:11px;color:#C8CAD4;text-align:center;"><?php echo e($i + 1); ?></td>
                            <td style="padding:13px 16px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="mp-av <?php echo e($avc); ?>" style="width:30px;height:30px;font-size:11px;flex-shrink:0;"><?php echo e($ini); ?></div>
                                    <div style="min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;"><?php echo e($a->user?->name ?? '—'); ?></div>
                                        <div style="font-size:11px;color:#A4ABB8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;"><?php echo e($a->user?->email ?? '—'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->role === 'koor'): ?>
                                <span class="mp-badge navy sm"><span class="dot"></span>Koor</span>
                                <?php else: ?>
                                <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:13px 16px;text-align:center;font-size:11px;color:#A4ABB8;white-space:nowrap;">
                                <?php echo e($a->created_at?->locale('id')->isoFormat('D MMM YY') ?? '—'); ?>

                            </td>
                            <td style="padding:13px 16px;text-align:center;">
                                <form method="POST"
                                      action="<?php echo e(route('eoffice.manprak.admin.kelola-role.revoke', $a->id)); ?>"
                                      onsubmit="return confirm('Cabut role <?php echo e($a->role); ?> dari <?php echo e(addslashes($a->user?->name ?? '')); ?>?\n\nJika user tidak terdaftar di praktikum lain, role Spatie akan ikut dicabut.')"
                                      style="display:inline;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit"
                                            style="font-size:11px;font-weight:600;padding:5px 10px;border-radius:6px;border:1px solid #FADAE1;background:#FFF5F6;color:#DF1C41;cursor:pointer;transition:all .12s;white-space:nowrap;"
                                            onmouseover="this.style.background='#FADAE1'" onmouseout="this.style.background='#FFF5F6'">
                                        Cabut
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="5" style="padding:56px;text-align:center;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada asprak atau koordinator di praktikum ini.</div>
                                <div style="font-size:12px;color:#A4ABB8;margin-top:4px;">Gunakan form Assign Role di sebelah kanan untuk menambahkan.</div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    

    
    <div style="width:272px;flex-shrink:0;display:flex;flex-direction:column;gap:12px;">

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="padding:12px 16px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;">
                <div style="font-size:12px;font-weight:700;color:#0D0D12;text-transform:uppercase;letter-spacing:.06em;">Assign Role Baru</div>
            </div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.kelola-role.assign')); ?>"
                  style="padding:16px;display:flex;flex-direction:column;gap:14px;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="praktikum_id" value="<?php echo e($praktikumId); ?>">

                
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Pilih User</label>
                    <div x-data="{
                        open: false,
                        search: '',
                        selected: '<?php echo e(old('user_id')); ?>',
                        options: <?php echo e(json_encode($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]))); ?>,
                        get filteredOptions() {
                            if (this.search === '') return this.options.slice(0, 50);
                            const lowerSearch = this.search.toLowerCase();
                            return this.options.filter(o => o.name.toLowerCase().includes(lowerSearch)).slice(0, 50);
                        },
                        selectOption(opt) {
                            this.selected = opt.id;
                            this.search = opt.name;
                            this.open = false;
                        },
                        init() {
                            if (this.selected) {
                                const opt = this.options.find(o => o.id == this.selected);
                                if (opt) this.search = opt.name;
                            }
                        }
                    }" class="relative">
                        <input type="hidden" name="user_id" :value="selected">
                        <div class="relative">
                            <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                   placeholder="Ketik nama mahasiswa..." class="mp-input" autocomplete="off"
                                   @input="selected = ''" style="width:100%; padding-right: 30px;">
                            <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#A4ABB8] pointer-events-none" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="margin-top:-7px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </div>
                        
                        <div x-show="open" style="display:none; position:absolute; z-index:50; width:100%; background:#fff; border:1px solid #DFE1E7; border-radius:6px; margin-top:4px; max-height:192px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                            <template x-for="opt in filteredOptions" :key="opt.id">
                                <div @click="selectOption(opt)"
                                     style="padding:8px 12px; cursor:pointer; font-size:12px; color:#0D0D12; border-bottom:1px solid #F3F4F6;"
                                     onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='transparent'"
                                     x-text="opt.name">
                                </div>
                            </template>
                            <div x-show="filteredOptions.length === 0" style="padding:12px; text-align:center; font-size:11px; color:#A4ABB8;">
                                Tidak ada nama yang ditampilkan
                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="font-size:11px;color:#DF1C41;margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">Role</label>
                    <div style="display:flex;gap:8px;">
                        <label style="flex:1;display:flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #DFE1E7;border-radius:8px;cursor:pointer;transition:all .15s;"
                               id="lbl-asprak"
                               onmouseover="this.style.borderColor='#0B266E'" onmouseout="if(!document.getElementById('r-asprak').checked)this.style.borderColor='#DFE1E7'">
                            <input type="radio" name="role" value="asprak" id="r-asprak"
                                   <?php echo e(old('role','asprak') === 'asprak' ? 'checked' : ''); ?>

                                   style="accent-color:#0B266E;"
                                   onchange="syncRoleLabels()">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:#0D0D12;">Asprak</div>
                                <div style="font-size:10px;color:#A4ABB8;">Asisten Praktikum</div>
                            </div>
                        </label>
                        <label style="flex:1;display:flex;align-items:center;gap:7px;padding:9px 12px;border:1px solid #DFE1E7;border-radius:8px;cursor:pointer;transition:all .15s;"
                               id="lbl-koor"
                               onmouseover="this.style.borderColor='#0B266E'" onmouseout="if(!document.getElementById('r-koor').checked)this.style.borderColor='#DFE1E7'">
                            <input type="radio" name="role" value="koor" id="r-koor"
                                   <?php echo e(old('role') === 'koor' ? 'checked' : ''); ?>

                                   style="accent-color:#0B266E;"
                                   onchange="syncRoleLabels()">
                            <div>
                                <div style="font-size:12px;font-weight:600;color:#0D0D12;">Koor</div>
                                <div style="font-size:10px;color:#A4ABB8;">Koordinator</div>
                            </div>
                        </label>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div style="font-size:11px;color:#DF1C41;margin-top:4px;"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button type="submit" class="mp-btn primary md" style="width:100%;justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Assign Role
                </button>
            </form>
        </div>

        
        <div style="background:#FFFBF0;border:1px solid #FDE68A;border-radius:13px;padding:14px 16px;">
            <div style="font-size:11px;font-weight:700;color:#92400E;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
                ⚠️ Catatan Penting
            </div>
            <div style="font-size:12px;color:#78350F;line-height:1.6;display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span><strong>Cabut</strong> akan menghapus user dari daftar praktikum ini.</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span>Jika user tidak terdaftar di praktikum lain, <strong>role Spatie</strong> (<code style="background:#FEF3C7;padding:0 3px;border-radius:3px;font-size:10px;">asprak</code> / <code style="background:#FEF3C7;padding:0 3px;border-radius:3px;font-size:10px;">koor_prak</code>) ikut dicabut dari sistem.</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <span style="flex-shrink:0;margin-top:1px;">•</span>
                    <span>Jika masih terdaftar di praktikum lain, role <strong>tidak</strong> dicabut.</span>
                </div>
            </div>
        </div>

    </div>
    

</div>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script>
function syncRoleLabels() {
    const asprak = document.getElementById('r-asprak');
    const koor   = document.getElementById('r-koor');
    const lblA   = document.getElementById('lbl-asprak');
    const lblK   = document.getElementById('lbl-koor');
    lblA.style.borderColor = asprak.checked ? '#0B266E' : '#DFE1E7';
    lblA.style.background  = asprak.checked ? 'rgba(11,38,110,0.04)' : '';
    lblK.style.borderColor = koor.checked   ? '#0B266E' : '#DFE1E7';
    lblK.style.background  = koor.checked   ? 'rgba(11,38,110,0.04)' : '';
}
document.addEventListener('DOMContentLoaded', syncRoleLabels);
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\kelola-role.blade.php ENDPATH**/ ?>