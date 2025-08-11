<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'ResourEase')); ?> - <?php echo $__env->yieldContent('title'); ?></title>

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900">
                    <a href="/">ResourEase</a>
                </h1>
                <h2 class="mt-2 text-gray-600"><?php echo $__env->yieldContent('subtitle'); ?></h2>
            </div>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</body>
</html> <?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/layouts/auth.blade.php ENDPATH**/ ?>