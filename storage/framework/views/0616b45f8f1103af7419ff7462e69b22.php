<?php if (isset($component)) { $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.card','data' => ['xShow' => 'showForm && (editing ? canEdit(editing) : canCreate)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'showForm && (editing ? canEdit(editing) : canCreate)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<form @submit.prevent="save()" class="space-y-6"><div class="px-6 space-y-2"><h2 class="font-semibold leading-none" x-text="formTitle"></h2><p class="text-sm text-muted-foreground" x-text="editing ? 'Edit your proposal and submit for review.' : 'Fill in the details for your proposed title and select a supervisor.'"></p></div><div class="px-6 grid gap-4">
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Error','variant' => 'destructive','xShow' => 'errors.root']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Error','variant' => 'destructive','x-show' => 'errors.root']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<span x-text="errors.root"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<label class="grid gap-2 text-sm font-medium" for="proposal-title">Title<?php if (isset($component)) { $__componentOriginal2f30e5e2854777f60031a186edf35415 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2f30e5e2854777f60031a186edf35415 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.input','data' => ['id' => 'proposal-title','xModel' => 'form.title','required' => true,'minlength' => '5','maxlength' => '100','placeholder' => 'Enter your proposed title']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'proposal-title','x-model' => 'form.title','required' => true,'minlength' => '5','maxlength' => '100','placeholder' => 'Enter your proposed title']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2f30e5e2854777f60031a186edf35415)): ?>
<?php $attributes = $__attributesOriginal2f30e5e2854777f60031a186edf35415; ?>
<?php unset($__attributesOriginal2f30e5e2854777f60031a186edf35415); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2f30e5e2854777f60031a186edf35415)): ?>
<?php $component = $__componentOriginal2f30e5e2854777f60031a186edf35415; ?>
<?php unset($__componentOriginal2f30e5e2854777f60031a186edf35415); ?>
<?php endif; ?><span class="text-destructive" x-text="errors.title?.[0]"></span></label>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['description'=>['Description','Describe your project in detail','Provide a clear description of what your project will accomplish.'],'problem_statement'=>['Problem Statement','What problem does your project solve?','Describe the specific problem or gap that your project addresses.'],'scope'=>['Scope','Define the boundaries and scope of your project','Define what is included and excluded in your project scope.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<label class="grid gap-2 text-sm font-medium" for="proposal-<?php echo e($key); ?>"><?php echo e($field[0]); ?><textarea id="proposal-<?php echo e($key); ?>" x-model="form.<?php echo e($key); ?>" required minlength="<?php echo e($key==='description'?20:10); ?>" maxlength="<?php echo e($key==='description'?500:300); ?>" rows="3" placeholder="<?php echo e($field[1]); ?>" class="border-input placeholder:text-muted-foreground w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs"></textarea><span class="text-muted-foreground font-normal"><?php echo e($field[2]); ?></span><span class="text-destructive" x-text="errors.<?php echo e($key); ?>?.[0]"></span></label>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<fieldset class="space-y-3"><legend class="text-sm font-medium">Specializations</legend><p class="text-sm text-muted-foreground">Select all specializations that apply to your project.</p><div class="grid gap-3"><template x-for="spec in specs" :key="spec"><label class="flex items-center gap-3 text-sm"><input type="checkbox" :value="spec" x-model="form.specializations" class="size-4 rounded border-input accent-primary"><span x-text="spec"></span></label></template></div><p class="text-sm text-destructive" x-text="errors.specializations?.[0]"></p></fieldset>
<label class="grid gap-2 text-sm font-medium" for="proposal-supervisor">Supervisor (Dosen Pembimbing)<span class="text-muted-foreground font-normal">Select a lecturer who will supervise your project.</span><select id="proposal-supervisor" x-model="form.proposed_supervisor_id" required class="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs"><option value="">Select a supervisor</option><template x-for="lecturer in lecturers" :key="lecturer.id"><option :value="String(lecturer.id)" x-text="person(lecturer)+' ('+email(lecturer)+')'"></option></template></select><span class="text-destructive" x-text="errors.proposed_supervisor_id?.[0]"></span></label>
</div><div class="px-6 flex justify-end gap-2"><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['variant' => 'outline','@click' => 'showForm=false; editing=null',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','@click' => 'showForm=false; editing=null',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?><?php if (isset($component)) { $__componentOriginalb35a908784863ee4a31335a0ede028f1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb35a908784863ee4a31335a0ede028f1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.button','data' => ['type' => 'submit',':disabled' => 'saving']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit',':disabled' => 'saving']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php if (isset($component)) { $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.icon','data' => ['name' => 'Send']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'Send']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a)): ?>
<?php $attributes = $__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a; ?>
<?php unset($__attributesOriginal0ebc33cc959e9b0fde2e3c32f43abd3a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a)): ?>
<?php $component = $__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a; ?>
<?php unset($__componentOriginal0ebc33cc959e9b0fde2e3c32f43abd3a); ?>
<?php endif; ?><span x-text="editing ? (editing.supervisor_approval_status==='PENDING'?'Update Proposal':'Resubmit Proposal') : 'Submit Proposal'"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $attributes = $__attributesOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__attributesOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb35a908784863ee4a31335a0ede028f1)): ?>
<?php $component = $__componentOriginalb35a908784863ee4a31335a0ede028f1; ?>
<?php unset($__componentOriginalb35a908784863ee4a31335a0ede028f1); ?>
<?php endif; ?></div></form> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $attributes = $__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__attributesOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582)): ?>
<?php $component = $__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582; ?>
<?php unset($__componentOriginal5ac34a7ad891af7fa808b2dbb0e60582); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/mahasiswa/propose-title/form.blade.php ENDPATH**/ ?>