

<?php $__env->startSection('title', 'Create Equipment Type'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create Equipment Type</h1>
        <a href="<?php echo e(route('admin.equipment.categories.index')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to Equipment Types
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <form action="<?php echo e(route('admin.equipment.categories.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-6">                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Equipment Type Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo e(old('name')); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                               required
                               placeholder="e.g., Laboratory Equipment">                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
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
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" 
                                name="description" 
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Equipment type description"><?php echo e(old('description')); ?></textarea>                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'description'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="window.history.back()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Create Equipment Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\equipment\categories\create.blade.php ENDPATH**/ ?>