

<?php $__env->startSection('title', 'Edit Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Profile Information
            </h2>

            <form method="post" action="<?php echo e(route('profile.update')); ?>" class="mt-6 space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('patch'); ?>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">                    <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'name'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">                    <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
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

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Save
                    </button>

                    <?php if(session('status') === 'profile-updated'): ?>
                        <p class="text-sm text-gray-600">Saved.</p>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Delete Account
            </h2>

            <form method="post" action="<?php echo e(route('profile.destroy')); ?>" class="mt-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('delete'); ?>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
                        Once your account is deleted, all of its resources and data will be permanently deleted.
                    </p>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'password'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\FormError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bag' => 'userDeletion']); ?>
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

                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\profile\edit.blade.php ENDPATH**/ ?>