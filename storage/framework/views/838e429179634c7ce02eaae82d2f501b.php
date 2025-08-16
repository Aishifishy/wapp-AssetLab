<?php $__env->startSection('title', ' - Welcome'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-[80vh] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl w-full">
        <!-- Hero Section -->
        <div class="text-center mb-12 animate-float">
            <h2 class="hero-text text-5xl lg:text-6xl">Welcome to ResourEase</h2>
            <h4 class="text-xl text-gray-600 max-w-2xl mx-auto mt-6">
                Request IT Resources with Ease
            </p>
        </div>

        <!-- Auth Buttons with Enhanced Styling -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-16">
            <a href="<?php echo e(route('login')); ?>" 
               class="btn-secondary text-lg px-8 py-4 w-full sm:w-auto flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                User Login
            </a>
            <a href="<?php echo e(route('register')); ?>" 
               class="btn-primary text-lg px-8 py-4 w-full sm:w-auto flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Register
            </a>
        </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\welcome.blade.php ENDPATH**/ ?>