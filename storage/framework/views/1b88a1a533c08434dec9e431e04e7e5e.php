<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'ResourEase')); ?><?php echo $__env->yieldContent('title'); ?></title>

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        <!-- Alpine.js -->
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gradient-custom flex flex-col">
            <?php echo $__env->make('layouts.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Page Content -->
            <main class="flex-grow">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
            
            <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/layouts/app.blade.php ENDPATH**/ ?>