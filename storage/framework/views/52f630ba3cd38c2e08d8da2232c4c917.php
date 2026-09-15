<?php if (isset($component)) { $__componentOriginaldf0c1f9d71acfa8b3005f4638b1a29f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf0c1f9d71acfa8b3005f4638b1a29f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'pulse::components.card','data' => ['cols' => $cols,'rows' => $rows,'class' => $class]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pulse::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['cols' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cols),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($rows),'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($class)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php if (isset($component)) { $__componentOriginal7ce092db05b46b96a8ad5ab4b8902a89 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ce092db05b46b96a8ad5ab4b8902a89 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'pulse::components.card-header','data' => ['name' => 'Request Monitor','title' => 'All incoming HTTP requests','details' => 'last '.e($this->periodForHumans()).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pulse::card-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Request Monitor','title' => 'All incoming HTTP requests','details' => 'last '.e($this->periodForHumans()).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('icon', null, []); ?> 
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="w-5 h-5">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ce092db05b46b96a8ad5ab4b8902a89)): ?>
<?php $attributes = $__attributesOriginal7ce092db05b46b96a8ad5ab4b8902a89; ?>
<?php unset($__attributesOriginal7ce092db05b46b96a8ad5ab4b8902a89); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ce092db05b46b96a8ad5ab4b8902a89)): ?>
<?php $component = $__componentOriginal7ce092db05b46b96a8ad5ab4b8902a89; ?>
<?php unset($__componentOriginal7ce092db05b46b96a8ad5ab4b8902a89); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalbea25b6319928d1c693b59ced602f799 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbea25b6319928d1c693b59ced602f799 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'pulse::components.scroll','data' => ['expand' => $expand,'wire:key' => 'request-monitor-scroll']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pulse::scroll'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['expand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expand),'wire:key' => 'request-monitor-scroll']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($requests->isEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginal5fa7cfb847383b1e105a397b36250360 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5fa7cfb847383b1e105a397b36250360 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'pulse::components.no-results','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pulse::no-results'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5fa7cfb847383b1e105a397b36250360)): ?>
<?php $attributes = $__attributesOriginal5fa7cfb847383b1e105a397b36250360; ?>
<?php unset($__attributesOriginal5fa7cfb847383b1e105a397b36250360); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5fa7cfb847383b1e105a397b36250360)): ?>
<?php $component = $__componentOriginal5fa7cfb847383b1e105a397b36250360; ?>
<?php unset($__componentOriginal5fa7cfb847383b1e105a397b36250360); ?>
<?php endif; ?>
        <?php else: ?>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200/75 dark:border-gray-700/75">
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-16">Verb</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Path</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-16 text-center">Status</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-24 text-right">Duration</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-28 hidden md:table-cell">User</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-24 hidden lg:table-cell">IP</th>
                        <th class="px-3 py-2 text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 w-28 text-right">Happened</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-colors duration-150">

                        
                        <td class="px-3 py-2.5">
                            <?php
                                $methodColor = match($req->method) {
                                    'GET'    => 'bg-blue-50   text-blue-600   border border-blue-100   dark:bg-blue-500/15   dark:text-blue-300   dark:border-blue-500/25',
                                    'POST'   => 'bg-green-50  text-green-600  border border-green-100  dark:bg-green-500/15  dark:text-green-300  dark:border-green-500/25',
                                    'PUT'    => 'bg-yellow-50 text-yellow-600 border border-yellow-100 dark:bg-yellow-500/15 dark:text-yellow-300 dark:border-yellow-500/25',
                                    'PATCH'  => 'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-orange-500/15 dark:text-orange-300 dark:border-orange-500/25',
                                    'DELETE' => 'bg-red-50    text-red-600    border border-red-100    dark:bg-red-500/15    dark:text-red-300    dark:border-red-500/25',
                                    default  => 'bg-slate-50  text-slate-500  border border-slate-200  dark:bg-slate-500/15  dark:text-slate-300  dark:border-slate-500/25',
                                };
                            ?>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider <?php echo e($methodColor); ?>">
                                <?php echo e($req->method); ?>

                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 max-w-0">
                            <span class="text-xs font-mono text-gray-700 dark:text-gray-300 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors duration-150 truncate block max-w-[280px]">
                                <?php echo e($req->path); ?>

                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 text-center">
                            <?php
                                $statusColor = match(true) {
                                    $req->status >= 500 => 'bg-rose-50 text-rose-600 ring-1 ring-inset ring-rose-200/80 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20',
                                    $req->status >= 400 => 'bg-orange-50 text-orange-500 ring-1 ring-inset ring-orange-200/80 dark:bg-orange-500/10 dark:text-orange-400 dark:ring-orange-500/20',
                                    $req->status >= 300 => 'bg-sky-50 text-sky-500 ring-1 ring-inset ring-sky-200/80 dark:bg-sky-500/10 dark:text-sky-400 dark:ring-sky-500/20',
                                    default             => 'bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200/80 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                                };
                            ?>
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[10px] font-black tabular-nums <?php echo e($statusColor); ?>">
                                <?php echo e($req->status); ?>

                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 text-right">
                            <?php
                                $durationColor = match(true) {
                                    $req->duration >= 2000 => 'text-rose-500 dark:text-rose-400 font-bold',
                                    $req->duration >= 1000 => 'text-orange-500 dark:text-orange-400 font-semibold',
                                    $req->duration >= 500  => 'text-amber-500 dark:text-amber-400',
                                    default                => 'text-gray-400 dark:text-gray-500',
                                };
                            ?>
                            <span class="text-xs font-mono tabular-nums <?php echo e($durationColor); ?>">
                                <?php echo e(number_format($req->duration)); ?>ms
                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 hidden md:table-cell">
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate block max-w-[120px]" title="<?php echo e($req->user); ?>">
                                <?php echo e($req->user); ?>

                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 hidden lg:table-cell">
                            <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500 tabular-nums">
                                <?php echo e($req->ip); ?>

                            </span>
                        </td>

                        
                        <td class="px-3 py-2.5 text-right">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                <?php echo e($req->happened); ?>

                            </span>
                        </td>

                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbea25b6319928d1c693b59ced602f799)): ?>
<?php $attributes = $__attributesOriginalbea25b6319928d1c693b59ced602f799; ?>
<?php unset($__attributesOriginalbea25b6319928d1c693b59ced602f799); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbea25b6319928d1c693b59ced602f799)): ?>
<?php $component = $__componentOriginalbea25b6319928d1c693b59ced602f799; ?>
<?php unset($__componentOriginalbea25b6319928d1c693b59ced602f799); ?>
<?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf0c1f9d71acfa8b3005f4638b1a29f0)): ?>
<?php $attributes = $__attributesOriginaldf0c1f9d71acfa8b3005f4638b1a29f0; ?>
<?php unset($__attributesOriginaldf0c1f9d71acfa8b3005f4638b1a29f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf0c1f9d71acfa8b3005f4638b1a29f0)): ?>
<?php $component = $__componentOriginaldf0c1f9d71acfa8b3005f4638b1a29f0; ?>
<?php unset($__componentOriginaldf0c1f9d71acfa8b3005f4638b1a29f0); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\livewire\pulse\request-monitor.blade.php ENDPATH**/ ?>