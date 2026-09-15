<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Detail Praktikum — '.e($praktikum->nama).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Detail Praktikum — '.e($praktikum->nama).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header" style="flex-shrink:0;">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;min-width:0;">
        <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.index')); ?>"
           style="display:flex;align-items:center;gap:4px;font-size:12px;color:#A4ABB8;text-decoration:none;white-space:nowrap;flex-shrink:0;transition:color .15s;"
           onmouseover="this.style.color='#0B266E'" onmouseout="this.style.color='#A4ABB8'">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Daftar Praktikum
        </a>
        <span style="color:#DFE1E7;font-size:14px;">›</span>
        <div style="display:flex;align-items:center;gap:8px;min-width:0;">
            <h1 class="mp-page-title" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($praktikum->nama); ?></h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->kode): ?>
            <span style="font-size:11px;font-family:monospace;font-weight:700;color:#0B266E;background:rgba(11,38,110,0.08);padding:2px 8px;border-radius:5px;white-space:nowrap;flex-shrink:0;"><?php echo e($praktikum->kode); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="mp-badge <?php echo e($praktikum->status === 'aktif' ? 'success' : 'neutral'); ?> sm" style="flex-shrink:0;">
                <span class="dot"></span><?php echo e(ucfirst($praktikum->status)); ?>

            </span>
        </div>
    </div>
    <div class="mp-page-actions" style="flex-shrink:0;">
        <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.edit', $praktikum->id)); ?>" class="mp-btn secondary md" style="text-decoration:none;white-space:nowrap;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Praktikum
        </a>
    </div>
</div>


<div style="display:flex;gap:16px;flex:1;min-height:0;align-items:stretch;padding-bottom:2px;">

    
    <div style="width:300px;flex-shrink:0;overflow-y:auto;display:flex;flex-direction:column;gap:10px;padding-bottom:4px;"
         class="[scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="padding:11px 14px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;">
                <div style="font-size:11px;font-weight:700;color:#0D0D12;text-transform:uppercase;letter-spacing:.07em;">Info Praktikum</div>
            </div>
            <div style="padding:14px;display:flex;flex-direction:column;gap:11px;">
                <div>
                    <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Status</div>
                    <span class="mp-badge <?php echo e($praktikum->status === 'aktif' ? 'success' : 'neutral'); ?> sm"><span class="dot"></span><?php echo e(ucfirst($praktikum->status)); ?></span>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Periode</div>
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($praktikum->semester); ?> <?php echo e($praktikum->tahun_ajaran); ?></div>
                    <div style="font-size:11px;color:#A4ABB8;">T.A. <?php echo e($praktikum->tahun_ajaran); ?></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->matkul): ?>
                <div>
                    <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Mata Kuliah</div>
                    <div style="font-size:12px;font-weight:600;color:#0D0D12;line-height:1.4;"><?php echo e($praktikum->matkul->nama); ?></div>
                    <span style="font-size:10px;font-family:monospace;font-weight:700;color:#0B266E;background:rgba(11,38,110,0.08);padding:1px 6px;border-radius:4px;display:inline-block;margin-top:3px;"><?php echo e($praktikum->matkul->kode); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div style="height:1px;background:#F3F4F6;margin:0 -2px;"></div>
                <div>
                    <div style="font-size:10px;font-weight:600;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;">Dibuat</div>
                    <div style="font-size:11px;color:#666D80;"><?php echo e($praktikum->created_at?->locale('id')->isoFormat('D MMMM YYYY')); ?></div>
                </div>
            </div>
        </div>

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="padding:11px 14px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:11px;font-weight:700;color:#0D0D12;text-transform:uppercase;letter-spacing:.07em;">Dosen Pengampu</div>
                <div style="width:7px;height:7px;border-radius:50%;background:<?php echo e($praktikum->dosen ? '#10B981' : '#EF4444'); ?>;flex-shrink:0;"></div>
            </div>
            <div style="padding:14px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->dosen): ?>
                <?php
                    $d  = $praktikum->dosen;
                    $dp = explode(' ', $d->name);
                    $di = strtoupper(substr($dp[0],0,1).substr($dp[1]??$dp[0],0,1));
                ?>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="mp-av sky" style="width:34px;height:34px;font-size:12px;flex-shrink:0;"><?php echo e($di); ?></div>
                    <div style="min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($d->name); ?></div>
                        <div style="font-size:10px;color:#A4ABB8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($d->email); ?></div>
                    </div>
                </div>
                <?php else: ?>
                <div style="display:flex;align-items:center;gap:7px;padding:7px 9px;background:#FFF5F5;border-radius:7px;border:1px dashed #FCA5A5;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="font-size:11px;font-weight:600;color:#EF4444;">Belum ditunjuk</span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <div style="padding:11px 14px;border-bottom:1px solid #F3F4F6;background:#FAFAFA;display:flex;align-items:center;justify-content:space-between;">
                <div style="font-size:11px;font-weight:700;color:#0D0D12;text-transform:uppercase;letter-spacing:.07em;">Koordinator</div>
                <div style="width:7px;height:7px;border-radius:50%;background:<?php echo e($praktikum->koordinator ? '#10B981' : '#EF4444'); ?>;flex-shrink:0;"></div>
            </div>
            <div style="padding:14px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum->koordinator): ?>
                <?php
                    $k  = $praktikum->koordinator;
                    $kp = explode(' ', $k->name);
                    $ki = strtoupper(substr($kp[0],0,1).substr($kp[1]??$kp[0],0,1));
                ?>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="mp-av green" style="width:34px;height:34px;font-size:12px;flex-shrink:0;"><?php echo e($ki); ?></div>
                    <div style="min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:#0D0D12;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($k->name); ?></div>
                        <div style="font-size:10px;color:#A4ABB8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($k->email); ?></div>
                        <span class="mp-badge success sm" style="margin-top:5px;display:inline-flex;font-size:9px;"><span class="dot"></span>Koordinator</span>
                    </div>
                </div>
                <?php else: ?>
                <div style="display:flex;align-items:center;gap:7px;padding:7px 9px;background:#FFF5F5;border-radius:7px;border:1px dashed #FCA5A5;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="font-size:11px;font-weight:600;color:#EF4444;">Belum ditunjuk</span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>



    </div>
    

    
    <div style="flex:1;min-width:0;display:flex;flex-direction:column;height:100%;min-height:0;"
         x-data="{ tab: '<?php echo e(request()->has('tab') ? request('tab') : 'praktikan'); ?>' }">

        
        <div style="display:flex;align-items:center;gap:0;background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:5px;margin-bottom:12px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);">
            <button @click="tab='praktikan'"
                    :class="tab==='praktikan' ? 'bg-[#0B266E] text-white shadow-sm' : 'text-[#666D80] hover:bg-[#F6F8FA]'"
                    style="flex:1;padding:7px 0;border-radius:9px;border:none;cursor:pointer;font-size:13px;font-weight:600;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Praktikan
                <span x-bind:class="tab==='praktikan' ? 'bg-white/20 text-white' : 'bg-[#F0F1F4] text-[#666D80]'"
                      style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;"><?php echo e($praktikans->total()); ?></span>
            </button>
            <button @click="tab='asprak'"
                    :class="tab==='asprak' ? 'bg-[#0B266E] text-white shadow-sm' : 'text-[#666D80] hover:bg-[#F6F8FA]'"
                    style="flex:1;padding:7px 0;border-radius:9px;border:none;cursor:pointer;font-size:13px;font-weight:600;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Asisten Praktikum
                <span x-bind:class="tab==='asprak' ? 'bg-white/20 text-white' : 'bg-[#F0F1F4] text-[#666D80]'"
                      style="font-size:10px;font-weight:700;padding:1px 6px;border-radius:99px;"><?php echo e($aspraks->count()); ?></span>
            </button>
        </div>

        
        <div x-show="tab==='praktikan'" style="display:flex;flex-direction:column;flex:1;min-height:0;height:100%;">

            
            <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:13px 16px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                <form method="GET" style="flex:1;display:flex;gap:8px;" action="<?php echo e(route('eoffice.manprak.admin.praktikum.detail', $praktikum->id)); ?>">
                    <input type="hidden" name="tab" value="praktikan">
                    <div style="flex:1;position:relative;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round"
                             style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="<?php echo e($search ?? ''); ?>"
                               placeholder="Cari nama atau email praktikan..."
                               class="mp-input" style="width:100%;padding-left:32px;">
                    </div>
                    <button type="submit" class="mp-btn secondary sm">Cari</button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($search)): ?>
                    <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.detail', $praktikum->id)); ?>" class="mp-btn secondary sm" style="text-decoration:none;">Reset</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>
                <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                        class="mp-btn primary sm" style="display:flex;align-items:center;gap:5px;flex-shrink:0;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    Import
                </button>
            </div>

            
            <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;flex-direction:column;flex:1;min-height:0;margin-bottom:2px;">
                <div style="overflow-y:auto;flex:1;">
                    <table style="width:100%;border-collapse:collapse;min-width:460px;">
                        <thead style="position:sticky;top:0;z-index:1;">
                            <tr style="border-bottom:1px solid #DFE1E7;background:#FAFAFA;">
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:36px;">#</th>
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">Nama Praktikan</th>
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:140px;">NIM</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Angkatan</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Status</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:64px;">Hapus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $np  = explode(' ', $p->user?->name ?? 'PR');
                                $ini = strtoupper(substr($np[0]??'P',0,1).substr($np[1]??$np[0]??'R',0,1));
                                $avc = ['sky','navy','green','yellow','violet'];
                                $av  = $avc[crc32($p->user?->email??'')%count($avc)];
                                $st  = $p->user ? \App\Models\Student::where('user_id',$p->user->id)->first() : null;
                                $nim = $st?->student_number ?? '—';
                                $ang = $st?->cohort_year ?? '—';
                            ?>
                            <tr style="border-bottom:1px solid #F8F9FB;transition:background .1s;"
                                onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                <td style="padding:11px 14px;font-size:11px;color:#C8CAD4;text-align:center;"><?php echo e($praktikans->firstItem() + $i); ?></td>
                                <td style="padding:11px 14px;">
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="mp-av <?php echo e($av); ?>" style="width:28px;height:28px;font-size:10px;flex-shrink:0;"><?php echo e($ini); ?></div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;"><?php echo e($p->user?->name ?? '—'); ?></div>
                                            <div style="font-size:10px;color:#A4ABB8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;"><?php echo e($p->user?->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:11px 14px;font-size:12px;font-family:monospace;color:#353849;"><?php echo e($nim); ?></td>
                                <td style="padding:11px 14px;text-align:center;font-size:12px;font-weight:600;color:#353849;"><?php echo e($ang); ?></td>
                                <td style="padding:11px 14px;text-align:center;">
                                    <span class="mp-badge success sm" style="font-size:10px;"><span class="dot"></span>Aktif</span>
                                </td>
                                <td style="padding:11px 14px;text-align:center;">
                                    <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.daftar-praktikan.destroy', $p->id)); ?>"
                                          onsubmit="return confirm('Hapus praktikan ini?')" style="display:inline;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                style="width:26px;height:26px;border-radius:6px;border:1px solid #FADAE1;background:#FFF5F6;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .12s;"
                                                onmouseover="this.style.background='#FADAE1'" onmouseout="this.style.background='#FFF5F6'">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" style="padding:56px;text-align:center;">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8"/></svg>
                                    <div style="font-size:13px;font-weight:600;color:#666D80;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($search)): ?> Tidak ditemukan hasil untuk "<?php echo e($search); ?>"
                                        <?php else: ?> Belum ada praktikan terdaftar <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikans->hasPages()): ?>
                <div style="padding:10px 14px;border-top:1px solid #DFE1E7;flex-shrink:0;background:#FAFAFA;"><?php echo e($praktikans->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        </div>
        

        
        <div x-show="tab==='asprak'" style="display:flex;flex-direction:column;flex:1;min-height:0;height:100%;">

            
            <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;padding:13px 16px;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div>
                    <div style="font-size:13px;font-weight:700;color:#0D0D12;">Daftar Asisten Praktikum</div>
                    <div style="font-size:11px;color:#A4ABB8;margin-top:1px;"><?php echo e($aspraks->count()); ?> asprak terdaftar di praktikum ini</div>
                </div>

            </div>

            
            <div style="background:#fff;border:1px solid #DFE1E7;border-radius:13px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.04);display:flex;flex-direction:column;flex:1;min-height:0;margin-bottom:2px;">
                <div style="overflow-y:auto;flex:1;">
                    <table style="width:100%;border-collapse:collapse;min-width:580px;">
                        <thead style="position:sticky;top:0;z-index:1;">
                            <tr style="border-bottom:1px solid #DFE1E7;background:#FAFAFA;">
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:36px;">#</th>
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;">Nama Asprak</th>
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:140px;">NIM</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:80px;">Angkatan</th>
                                <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:200px;">Modul Ditugaskan</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:90px;">Status</th>
                                <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#A4ABB8;text-transform:uppercase;letter-spacing:.05em;width:90px;">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $aspraks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $asprak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $user      = $asprak->user;
                                $np        = explode(' ', $user?->name ?? 'AS');
                                $ini       = strtoupper(substr($np[0]??'A',0,1).substr($np[1]??$np[0]??'S',0,1));
                                $avc       = ['sky','navy','green','yellow','violet'];
                                $av        = $avc[crc32($user?->email??'')%count($avc)];
                                $st        = $user ? \App\Models\Student::where('user_id',$user->id)->first() : null;
                                $nim       = $st?->student_number ?? '—';
                                $ang       = $st?->cohort_year ?? '—';
                                $mods      = $asprak->modulAsprak?->pluck('modul.nama')->filter()->values() ?? collect();
                                $isKoor    = \Modules\EOffice\Models\Praktikum::where('koor_id',$asprak->user_id)->where('id',$asprak->praktikum_id)->exists();
                            ?>
                            <tr style="border-bottom:1px solid #F8F9FB;transition:background .1s;"
                                onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                <td style="padding:12px 14px;font-size:11px;color:#C8CAD4;text-align:center;"><?php echo e($i + 1); ?></td>
                                <td style="padding:12px 14px;">
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="mp-av <?php echo e($av); ?>" style="width:28px;height:28px;font-size:10px;flex-shrink:0;"><?php echo e($ini); ?></div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px;"><?php echo e($user?->name ?? '—'); ?></div>
                                            <div style="font-size:10px;color:#A4ABB8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px;"><?php echo e($user?->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:12px 14px;font-size:12px;font-family:monospace;color:#353849;"><?php echo e($nim); ?></td>
                                <td style="padding:12px 14px;text-align:center;font-size:12px;font-weight:600;color:#353849;"><?php echo e($ang); ?></td>
                                <td style="padding:12px 14px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mods->isNotEmpty()): ?>
                                    <div style="display:flex;flex-wrap:wrap;gap:3px;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mods->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <span style="font-size:10px;font-weight:600;padding:2px 6px;border-radius:4px;background:rgba(11,38,110,0.08);color:#0B266E;white-space:nowrap;"><?php echo e($mn); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mods->count() > 3): ?>
                                        <span style="font-size:10px;font-weight:600;padding:2px 6px;border-radius:4px;background:#F0F1F4;color:#666D80;">+<?php echo e($mods->count()-3); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <span style="font-size:11px;color:#C8CAD4;font-style:italic;">Belum ditugaskan</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="padding:12px 14px;text-align:center;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isKoor): ?>
                                    <span class="mp-badge warning sm" style="font-size:10px;"><span class="dot"></span>Koor</span>
                                    <?php else: ?>
                                    <span class="mp-badge neutral sm" style="font-size:10px;"><span class="dot"></span>Asprak</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td style="padding:12px 14px;text-align:center;font-size:11px;color:#A4ABB8;white-space:nowrap;">
                                    <?php echo e($asprak->created_at?->locale('id')->isoFormat('D MMM YY') ?? '—'); ?>

                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="7" style="padding:56px;text-align:center;">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;display:block;"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada asisten praktikum terdaftar.</div>

                                </td>
                            </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        

    </div>
    

</div>



<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.45);">
    <div style="background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.2);width:100%;max-width:420px;margin:0 16px;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #DFE1E7;">
            <div style="font-size:15px;font-weight:700;color:#0D0D12;">Import Praktikan</div>
            <button onclick="document.getElementById('modalImport').classList.add('hidden')"
                    style="width:28px;height:28px;border-radius:50%;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#A4ABB8;font-size:18px;line-height:1;transition:background .12s;"
                    onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='transparent'">×</button>
        </div>
        <form method="POST" action="<?php echo e(route('eoffice.manprak.admin.daftar-praktikan.store')); ?>" enctype="multipart/form-data"
              style="padding:20px;display:flex;flex-direction:column;gap:14px;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="praktikum_id" value="<?php echo e($praktikum->id); ?>">
            <div style="background:#F6F8FA;border-radius:9px;padding:11px 13px;font-size:12px;color:#666D80;line-height:1.6;">
                Upload <strong>CSV</strong> atau <strong>XLSX</strong>. Sistem dapat mendeteksi format mentah <strong>SSO Kampus</strong> secara otomatis, atau daftar satu kolom dengan header
                <code style="background:#fff;border:1px solid #DFE1E7;padding:1px 5px;border-radius:4px;font-size:11px;">email</code> atau
                <code style="background:#fff;border:1px solid #DFE1E7;padding:1px 5px;border-radius:4px;font-size:11px;">nim</code>.
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:5px;">Pilih File</label>
                <input type="file" name="file" class="mp-input" accept=".csv,.xlsx,.xls" required style="width:100%;">
            </div>
            <div style="display:flex;gap:8px;margin-top:4px;">
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')"
                        class="mp-btn secondary md" style="flex:1;">Batal</button>
                <button type="submit" class="mp-btn primary md" style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    Import
                </button>
            </div>
        </form>
    </div>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\admin\praktikum-detail.blade.php ENDPATH**/ ?>