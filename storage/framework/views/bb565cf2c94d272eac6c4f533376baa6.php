

<?php $__env->startSection('title', 'Login'); ?>
<?php $__env->startSection('subtitle', 'Sign in to your account'); ?>

<?php $__env->startSection('content'); ?>
    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="<?php echo e(old('email')); ?>">
            </div>
            <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'email'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FormError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d)): ?>
<?php $attributes = $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d; ?>
<?php unset($__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d)): ?>
<?php $component = $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d; ?>
<?php unset($__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d); ?>
<?php endif; ?>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'password'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FormError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d)): ?>
<?php $attributes = $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d; ?>
<?php unset($__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d)): ?>
<?php $component = $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d; ?>
<?php unset($__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d); ?>
<?php endif; ?>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Sign in
            </button>
        </div>

        <div class="text-sm text-center">
            <p class="text-gray-600">Don't have an account?
                <a href="<?php echo e(route('register')); ?>" class="font-medium text-blue-600 hover:text-blue-500">Register here</a>
            </p>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\auth\unified\login.blade.php ENDPATH**/ ?>