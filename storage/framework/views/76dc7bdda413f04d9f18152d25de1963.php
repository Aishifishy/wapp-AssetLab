

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 space-y-3 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-900">Currently Borrowed Equipment</h1>
        <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center">
            Borrow More Equipment
        </a>
    </div>

    <?php if($borrowedRequests->count() > 0): ?>
        <div class="grid gap-6">
            <?php $__currentLoopData = $borrowedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-4 lg:space-y-0">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo e($request->equipment->name); ?></h3>
                            <p class="text-gray-600 mt-1"><?php echo e($request->equipment->description); ?></p>
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Borrowed From:</span>
                                    <p class="text-gray-600"><?php echo e($request->requested_from->format('M d, Y g:i A')); ?></p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Return By:</span>
                                    <p class="text-gray-600"><?php echo e($request->requested_until->format('M d, Y g:i A')); ?></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="font-medium text-gray-700">Purpose:</span>
                                    <p class="text-gray-600"><?php echo e($request->purpose); ?></p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        <?php echo e(ucfirst($request->status)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4">
                            <?php if(!$request->return_requested_at): ?>
                                <form action="<?php echo e(route('ruser.equipment.return', $request)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm"
                                            onclick="return confirm('Are you sure you want to request return of this equipment?')">
                                        Request Return
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                    Return Requested
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <?php echo e($borrowedRequests->links()); ?>

        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Equipment Currently Borrowed</h3>
            <p class="text-gray-600 mb-4">You don't have any equipment currently borrowed.</p>
            <a href="<?php echo e(route('ruser.equipment.borrow')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Browse Equipment
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views\ruser\equipment\borrowed.blade.php ENDPATH**/ ?>