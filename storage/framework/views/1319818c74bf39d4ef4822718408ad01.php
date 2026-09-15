<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Proposal Terkunci','icon' => 'Lock','xShow' => '!canCreate && flow?.reason']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proposal Terkunci','icon' => 'Lock','x-show' => '!canCreate && flow?.reason']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<span x-text="reasonText[flow?.reason] || 'Pengajuan proposal tidak tersedia untuk kondisi kelompok saat ini.'"></span> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Leader Only','icon' => 'Lock','xShow' => 'group && !isLeader']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Leader Only','icon' => 'Lock','x-show' => 'group && !isLeader']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Only the group leader can propose titles. Contact your group leader to submit proposals. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Limit Reached','icon' => 'AlertTriangle','xShow' => 'isLeader && total>=3 && !approved']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Limit Reached','icon' => 'AlertTriangle','x-show' => 'isLeader && total>=3 && !approved']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
You have used all 3 title slots (bids + proposals combined). Delete an existing bid to make room. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'No Group','xShow' => '!group']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'No Group','x-show' => '!group']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
You must <?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => '/mahasiswa/group','class' => 'underline font-medium']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/mahasiswa/group','class' => 'underline font-medium']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
create a group <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?> first before proposing a title. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Anggota Kelompok Kurang','icon' => 'AlertTriangle','variant' => 'destructive','xShow' => 'group && isLeader && !enoughMembers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Anggota Kelompok Kurang','icon' => 'AlertTriangle','variant' => 'destructive','x-show' => 'group && isLeader && !enoughMembers']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Kelompok harus memiliki minimal <span x-text="minMembers"></span> anggota untuk mengajukan judul. Saat ini kelompok Anda memiliki <span x-text="members.length"></span> anggota. Tambahkan anggota di <?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => '/mahasiswa/group','class' => 'underline font-semibold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/mahasiswa/group','class' => 'underline font-semibold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
My Group <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Active Bids','icon' => 'Lock','xShow' => 'bids.length && !group?.is_solo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Active Bids','icon' => 'Lock','x-show' => 'bids.length && !group?.is_solo']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Kelompok Anda memiliki bid aktif. Kelola bid di <?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => '/mahasiswa/bidding','class' => 'underline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/mahasiswa/bidding','class' => 'underline']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Title Bids <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?> sebelum mengajukan proposal. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Proposal Pending Review','icon' => 'Lock','xShow' => 'pending']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proposal Pending Review','icon' => 'Lock','x-show' => 'pending']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Your title proposal is awaiting supervisor review. You cannot submit another until it is resolved. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($component)) { $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.alert','data' => ['title' => 'Proposal Approved!','icon' => 'CheckCircle','xShow' => 'approved']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proposal Approved!','icon' => 'CheckCircle','x-show' => 'approved']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Your title proposal has been approved. Visit <?php if (isset($component)) { $__componentOriginal751c60d6b55ecec60fe692173fafc657 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal751c60d6b55ecec60fe692173fafc657 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'capstone::components.feature-link','data' => ['href' => '/mahasiswa/group','class' => 'underline font-medium']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('capstone::feature-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '/mahasiswa/group','class' => 'underline font-medium']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
My Group <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $attributes = $__attributesOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__attributesOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal751c60d6b55ecec60fe692173fafc657)): ?>
<?php $component = $__componentOriginal751c60d6b55ecec60fe692173fafc657; ?>
<?php unset($__componentOriginal751c60d6b55ecec60fe692173fafc657); ?>
<?php endif; ?> to see your finalized project. <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $attributes = $__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__attributesOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45)): ?>
<?php $component = $__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45; ?>
<?php unset($__componentOriginaldc7c4a2b6f7016224ae46665c09c3b45); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/mahasiswa/propose-title/constraints.blade.php ENDPATH**/ ?>