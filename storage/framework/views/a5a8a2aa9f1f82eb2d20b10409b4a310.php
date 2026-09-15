

<div style="width: 100%; min-width: 0; background:#fff; border:1px solid var(--c-border); border-radius:14px; margin-bottom:20px; box-shadow:var(--shadow-card); overflow:hidden;">

    
    <div class="chart-header" style="display:flex; align-items:flex-start; justify-content:space-between; padding:16px 20px 14px; border-bottom:1px solid var(--c-border); flex-wrap:wrap; gap:12px;">
        <div>
            <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:2px;">Analisis Capaian Pembelajaran Lulusan (CPL)</h3>
            <p style="font-size:11px; color:var(--c-fg-muted);">
                Persentase jawaban benar per indikator CPL
            </p>
        </div>

        
        <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
            <form method="GET" action="<?php echo e(route('banksoal.dashboard')); ?>"
                  x-data="{
                      open: false,
                      search: '',
                      periode: <?php echo \Illuminate\Support\Js::from($semuaPeriode)->toHtml() ?>,
                      selectedIds: <?php echo \Illuminate\Support\Js::from($periodeIdFilters)->toHtml() ?>,
                      get filtered() {
                          if (!this.search) return this.periode;
                          return this.periode.filter(p => p.nama_periode.toLowerCase().includes(this.search.toLowerCase()));
                      }
                  }"
                  x-on:keydown.escape="open = false"
                  style="position:relative; display:flex; align-items:center; gap:12px;">

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($periodeIdFilters) > 0): ?>
                    <span style="font-size:11px; font-weight:600; color:var(--c-primary); background:rgba(94,83,244,0.09); border:1px solid rgba(94,83,244,0.18); padding:4px 10px; border-radius:8px;">
                        <?php echo e(count($periodeIdFilters) === 1 ? $semuaPeriode->firstWhere('id', $periodeIdFilters[0])?->nama_periode : count($periodeIdFilters) . ' Periode'); ?>

                    </span>
                    <a href="<?php echo e(route('banksoal.dashboard')); ?>" style="font-size:11px; color:var(--c-error); text-decoration:none;">Reset</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <button type="button" @click="open = !open"
                        style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); cursor:pointer;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    <span>Filter Periode</span>
                </button>

                
                <div x-show="open" @click.outside="open = false" style="display:none; position:absolute; right:0; top:calc(100% + 8px); width:300px; background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.1); z-index:50; flex-direction:column; overflow:hidden;">
                    <div style="padding:12px; border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                        <input type="text" x-model="search" placeholder="Cari nama periode..." style="width:100%; padding:8px 12px; font-size:12px; border:1px solid var(--c-border); border-radius:6px; outline:none;">
                    </div>
                    <div style="max-height:200px; overflow-y:auto; padding:8px 0;">
                        <label style="display:flex; align-items:center; gap:12px; padding:8px 16px; cursor:pointer; border-bottom:1px solid var(--c-bg);">
                            <input type="checkbox" @change="selectedIds = $event.target.checked ? periode.map(p => p.id) : []" :checked="selectedIds.length === periode.length && periode.length > 0">
                            <span style="font-size:12px; font-weight:600;">Pilih Semua</span>
                        </label>
                        <template x-for="p in filtered" :key="p.id">
                            <label style="display:flex; align-items:center; gap:12px; padding:6px 16px; cursor:pointer;">
                                <input type="checkbox" name="periode_id[]" :value="p.id" x-model="selectedIds">
                                <span style="font-size:12px; color:var(--c-fg);" x-text="p.nama_periode"></span>
                            </label>
                        </template>
                    </div>
                    <div style="padding:12px; border-top:1px solid var(--c-border); background:var(--c-bg); display:flex; justify-content:flex-end; gap:8px;">
                        <button type="button" @click="selectedIds = []" style="padding:6px 12px; font-size:11px; background:none; border:none; cursor:pointer;">Clear</button>
                        <button type="submit" style="padding:6px 12px; font-size:11px; font-weight:600; background:var(--c-primary); color:#fff; border:none; border-radius:6px; cursor:pointer;">Terapkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:0;">
        
        <div style="padding:20px; border-right:1px solid var(--c-border); max-height:350px; overflow-y:auto;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cplStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $barColor = $stat['persentase'] >= 80 ? 'var(--c-success)'
                              : ($stat['persentase'] >= 60 ? 'var(--c-warning)' : 'var(--c-error)');
                ?>
                <div style="margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                        <div>
                            <span style="font-size:12px; font-weight:700; color:var(--c-fg);"><?php echo e($stat['cpl_kode']); ?></span>
                            <p style="font-size:11px; color:var(--c-fg-muted); margin-top:4px; line-height: 1.45; padding-right: 12px;"><?php echo e($stat['deskripsi']); ?></p>
                        </div>
                        <span style="font-size:13px; font-weight:800; color:var(--c-fg);"><?php echo e($stat['persentase']); ?>%</span>
                    </div>
                    <div style="width:100%; background:var(--c-bg); border-radius:99px; height:6px; overflow:hidden;">
                        <div style="background:<?php echo e($barColor); ?>; height:100%; border-radius:99px; width:<?php echo e($stat['persentase']); ?>%;"></div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div style="text-align:center; padding:40px 0; color:var(--c-fg-muted); font-size:12px;">Data CPL belum tersedia.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div style="padding:20px; display:flex; flex-direction:column;">
            <h4 style="font-size:12px; font-weight:600; color:var(--c-fg); margin-bottom:16px; text-align:center;">Peta Kompetensi</h4>
            <div style="flex:1; min-height:250px; display:flex; align-items:center; justify-content:center;" id="chartCPL"></div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cplLabels = <?php echo json_encode($cplStats->pluck('cpl_kode')); ?>;
        const cplData = <?php echo json_encode($cplStats->pluck('persentase')); ?>;
        
        if (cplData.length > 0) {
            new ApexCharts(document.querySelector("#chartCPL"), {
                series: [{ name: 'Persentase', data: cplData }],
                chart: { type: 'radar', height: 320, toolbar: { show: false } },
                labels: cplLabels,
                stroke: { width: 2, colors: ['#5E53F4'] },
                fill: { opacity: 0.2, colors: ['#5E53F4'] },
                markers: { size: 3, colors: ['#fff'], strokeColors: '#5E53F4', strokeWidth: 2, hover: { size: 5 } },
                yaxis: { show: false, min: 0, max: 100 },
                xaxis: { labels: { style: { colors: '#666D80', fontSize: '11px', fontFamily: 'Inter Tight, sans-serif', fontWeight: 600 } } },
                dataLabels: { enabled: false }
            }).render();
        } else {
            document.querySelector("#chartCPL").innerHTML = '<span style="font-size:12px; color:var(--c-fg-muted);">Belum ada data kompetensi</span>';
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/BankSoal\resources/views/dashboard/partials/_chart.blade.php ENDPATH**/ ?>