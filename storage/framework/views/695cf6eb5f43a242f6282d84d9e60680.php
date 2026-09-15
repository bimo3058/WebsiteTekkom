<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php if (isset($component)) { $__componentOriginal798d336f107c986f84961f0c7e80911e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal798d336f107c986f84961f0c7e80911e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation-assets','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation-assets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $attributes = $__attributesOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__attributesOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $component = $__componentOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__componentOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
</head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">
            
            <div class="w-full flex justify-center">
                <?php echo e($slot); ?>

            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $attributes = $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $component = $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views\layouts\guest.blade.php ENDPATH**/ ?>