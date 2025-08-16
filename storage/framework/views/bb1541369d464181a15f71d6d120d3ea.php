

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Term</h1>
        <a href="<?php echo e(route('admin.academic.index')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Calendar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Term Details for <?php echo e($academicYear->name); ?></h3>
            </div>
        </div>
        <div class="p-6">
            <form action="<?php echo e(route('admin.academic.terms.update', [$academicYear, $term])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Term Name</label>
                        <input type="text" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="name" name="name" value="<?php echo e(old('name', $term->name)); ?>" required>
                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
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
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="start_date" name="start_date" 
                            value="<?php echo e(old('start_date', $term->start_date->format('Y-m-d'))); ?>" 
                            min="<?php echo e($academicYear->start_date->format('Y-m-d')); ?>"
                            max="<?php echo e($academicYear->end_date->format('Y-m-d')); ?>"
                            required>
                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'start_date'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 focus:border-red-500 focus:ring-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="end_date" name="end_date" 
                            value="<?php echo e(old('end_date', $term->end_date->format('Y-m-d'))); ?>"
                            min="<?php echo e($academicYear->start_date->format('Y-m-d')); ?>"
                            max="<?php echo e($academicYear->end_date->format('Y-m-d')); ?>"
                            required>
                        <?php if (isset($component)) { $__componentOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc93285135aa759ebaf0b3dc38aeeeb0d = $attributes; } ?>
<?php $component = App\View\Components\FormError::resolve(['field' => 'end_date'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Note:</strong> Term dates must be within the academic year period
                                (<?php echo e($academicYear->start_date->format('M d, Y')); ?> - <?php echo e($academicYear->end_date->format('M d, Y')); ?>).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="<?php echo e(route('admin.academic.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                        <i class="fas fa-save mr-2"></i> Update Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Initialize date validation for edit form
    document.addEventListener('DOMContentLoaded', function() {
        if (window.dateValidationManager) {
            // Set up basic date validation
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && endDateInput) {
                window.dateValidationManager.initEndDateValidation(startDateInput, endDateInput);
                
                // Add start date validation
                startDateInput.addEventListener('change', function() {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    
                    if (endDate && startDate >= endDate) {
                        window.dateValidationManager.showValidationError('Start date must be before end date');
                        startDateInput.value = '';
                    }
                });
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\admin\academic\terms\edit.blade.php ENDPATH**/ ?>