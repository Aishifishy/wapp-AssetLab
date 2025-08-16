<?php if($shouldShow()): ?>
<div class="border rounded-lg p-4 <?php echo e($getBannerClasses()); ?> <?php echo e($attributes->get('class', 'mb-4')); ?>" 
     role="alert" 
     <?php echo e($attributes->except('class')); ?>>
    
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 <?php echo e($getIconClasses()); ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="<?php echo e($getDefaultIcon()); ?>" />
            </svg>
        </div>
        
        <div class="ml-3 flex-1">
            <?php if($title): ?>
                <h3 class="text-sm font-medium <?php echo e($getTextClasses()); ?>">
                    <?php echo e($title); ?>

                </h3>
            <?php endif; ?>
            
            <div class="text-sm <?php echo e($title ? 'mt-2' : ''); ?> <?php echo e($getTextClasses()); ?>">
                <?php if($slot->isNotEmpty()): ?>
                    <?php echo e($slot); ?>

                <?php else: ?>
                    <?php echo e($getSessionMessage()); ?>

                <?php endif; ?>
            </div>

            <?php if(!empty($actions)): ?>
                <div class="mt-4">
                    <div class="-mx-2 -my-1.5 flex">
                        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" 
                                    class="px-2 py-1.5 rounded-md text-sm font-medium <?php echo e($type === 'success' ? 'bg-green-100 text-green-800 hover:bg-green-200' : ($type === 'error' || $type === 'danger' ? 'bg-red-100 text-red-800 hover:bg-red-200' : ($type === 'warning' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200'))); ?>"
                                    <?php if(isset($action['onclick'])): ?> onclick="<?php echo e($action['onclick']); ?>" <?php endif; ?>
                                    <?php if(isset($action['href'])): ?> onclick="window.location.href='<?php echo e($action['href']); ?>'" <?php endif; ?>>
                                <?php echo e($action['text']); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if($dismissible): ?>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" 
                            class="inline-flex rounded-md p-1.5 <?php echo e($getIconClasses()); ?> <?php echo e($type === 'success' ? 'hover:bg-green-100' : ($type === 'error' || $type === 'danger' ? 'hover:bg-red-100' : ($type === 'warning' ? 'hover:bg-yellow-100' : 'hover:bg-blue-100'))); ?> focus:outline-none focus:ring-2 focus:ring-offset-2 <?php echo e($type === 'success' ? 'focus:ring-green-600' : ($type === 'error' || $type === 'danger' ? 'focus:ring-red-600' : ($type === 'warning' ? 'focus:ring-yellow-600' : 'focus:ring-blue-600'))); ?>"
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
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\components\notification-banner.blade.php ENDPATH**/ ?>