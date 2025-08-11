<?php if($shouldShow()): ?>
<div class="border-l-4 p-4 <?php echo e($dismissible ? 'relative' : ''); ?> <?php echo e($getAlertClasses()); ?> <?php echo e($attributes->get('class', 'mb-4')); ?>" 
     role="alert" 
     <?php echo e($attributes->except('class')); ?>>
    
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="<?php echo e($getDefaultIcon()); ?> <?php echo e($type === 'success' ? 'text-green-500' : ($type === 'error' || $type === 'danger' ? 'text-red-500' : ($type === 'warning' ? 'text-yellow-500' : 'text-blue-500'))); ?>"></i>
        </div>
        
        <div class="ml-3 flex-1">
            <?php if($title): ?>
                <h3 class="text-sm font-medium <?php echo e($type === 'success' ? 'text-green-800' : ($type === 'error' || $type === 'danger' ? 'text-red-800' : ($type === 'warning' ? 'text-yellow-800' : 'text-blue-800'))); ?>">
                    <?php echo e($title); ?>

                </h3>
            <?php endif; ?>
            
            <div class="text-sm <?php echo e($title ? 'mt-1' : ''); ?>">
                <?php if($slot->isNotEmpty()): ?>
                    <?php echo e($slot); ?>

                <?php else: ?>
                    <?php echo e($getSessionMessage()); ?>

                <?php endif; ?>
            </div>
        </div>
        
        <?php if($dismissible): ?>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" 
                            class="inline-flex rounded-md p-1.5 <?php echo e($type === 'success' ? 'text-green-500 hover:bg-green-100' : ($type === 'error' || $type === 'danger' ? 'text-red-500 hover:bg-red-100' : ($type === 'warning' ? 'text-yellow-500 hover:bg-yellow-100' : 'text-blue-500 hover:bg-blue-100'))); ?> focus:outline-none focus:ring-2 focus:ring-offset-2 <?php echo e($type === 'success' ? 'focus:ring-green-600' : ($type === 'error' || $type === 'danger' ? 'focus:ring-red-600' : ($type === 'warning' ? 'focus:ring-yellow-600' : 'focus:ring-blue-600'))); ?>"
                            onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/components/alert.blade.php ENDPATH**/ ?>